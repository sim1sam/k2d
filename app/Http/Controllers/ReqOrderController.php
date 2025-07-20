<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\CashIn;
use App\Models\Currency;
use App\Models\Language;
use App\Models\ReqOrder;
use App\Models\Address;
use App\Models\ReqOrderItemFile;
use App\Models\ReqOrderItem;  // Add this import to use ReqOrderItem
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;
use Config;

class ReqOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReqOrder::where('user_id', Auth::id());

        // If there is a search request, filter by order number
        if ($request->has('search')) {
            $query->where('order_no', 'LIKE', '%' . $request->search . '%');
        }

        // Fetch the orders in descending order and paginate results
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $banks  = Bank::all();

        // Calculate total amount dynamically if order->total is 0
        foreach ($orders as $order) {
            $calculatedTotal = 0;

            foreach ($order->items as $item) {
                $calculatedTotal += $item->quantity * $item->price_bdt;
            }

            // Add a new attribute to store the correct total
            $order->calculated_total = ($order->total == 0) ? $calculatedTotal : $order->total;
        }

        return view('frontend.req-order.index', compact('orders', 'banks'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // If user is authenticated, return the order creation page
            return view('frontend.req-order.create-order');
        } else {
            // If user is not authenticated, redirect to login page
            return Redirect::route('login')->with('message', 'Please log in to create an order.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         // Validate request data
         $request->validate([
             'items' => 'required|array',
             'items.*.product_name' => 'required|string',
             'items.*.quantity' => 'required|integer',
             'items.*.price_bdt' => 'required|numeric',
             'items.*.size' => 'nullable|string',
             'items.*.product_link' => 'nullable|url',
             'items.*.note' => 'nullable|string',
             'items.*.files.*' => 'nullable|file|mimes:jpg,png,pdf,webp|max:20048',
         ]);

         // Generate unique order number
         $timestamp = now()->format('Ymd-His');
         $randomDigits = mt_rand(10, 99);
         $orderNo = 'K2D' . $timestamp . $randomDigits;

         $totalAmount = 0;
         $processedItems = [];

         foreach ($request->items as $item) {
             $totalPrice = $item['quantity'] * $item['price_bdt'];
             $totalAmount += $totalPrice;

             $files = [];
             if (isset($item['files'])) {
                 foreach ($item['files'] as $file) {
                     $uniqueId = uniqid();
                     $fileName = $orderNo . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
                     $file->move(public_path('k2d/uploads/reqorder'), $fileName);
                     $files[] = 'k2d/uploads/reqorder/' . $fileName;
                 }
             }

             $processedItems[] = [
                 'product_name' => $item['product_name'],
                 'quantity' => $item['quantity'],
                 'price_bdt' => $item['price_bdt'],
                 'size' => $item['size'] ?? null,
                 'product_link' => $item['product_link'] ?? null,
                 'note' => $item['note'] ?? null,
                 'total' => $totalPrice,
                 'paid_amount' => 0,
                 'due' => $totalPrice,
                 'files' => $files,
             ];
         }

         // Store order details in session (instead of database)
         session()->put('pending_order', [
             'order_no' => $orderNo,
             'user_id' => Auth::id(),
             'total' => $totalAmount,
             'items' => $processedItems,
         ]);

         return redirect()->route('frontend.req.checkout')->with('success', 'Order added to session. Proceed to checkout.');
     }




     public function checkout()
     {
         $order = session('pending_order');

         if (!$order) {
             return redirect()->route('frontend.order.index')->with('error', 'No order found.');
         }

         $user = Auth::user();

         // Get the default address
         $defaultAddress = Address::where('user_id', $user->id)
             ->where('set_default', 1)
             ->first();

         // Get all addresses for the user
         $addresses = Address::where('user_id', $user->id)->get();



         return view('frontend.req-order.req_checkout', compact('order', 'defaultAddress', 'addresses'));
     }

     public function confirmOrder(Request $request)
     {
         $request->validate([
             'address' => 'required|string',
             'phone' => 'required|string',
             'state' => 'required|string',
             'city' => 'required|string',
             'postal_code' => 'required|string',
             'country' => 'required|string',
             'name' => 'required|string',
             'email' => 'required|string',
         ]);

         // Retrieve session data (pending order)
         $orderData = session('pending_order');

         if (!$orderData) {
             return redirect()->route('frontend.order.index')->with('error', 'Order session expired.');
         }

         DB::beginTransaction();

         try {
             // Save order in database
             $orderId = DB::table('reqorders')->insertGetId([
                 'order_no' => $orderData['order_no'],
                 'user_id' => Auth::id(),
                 'total' => $orderData['total'],
                 'address' => $request->address,
                 'phone' => $request->phone,
                 'state' => $request->state,
                 'city' => $request->city,
                 'postal_code' => $request->postal_code,
                 'country' => $request->country,
                 'name' => $request->name,
                 'email' => $request->email,
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);

             // Save order items
             foreach ($orderData['items'] as $item) {
                 $itemId = DB::table('reqorder_items')->insertGetId([
                     'reqorder_id' => $orderId,
                     'order_no' => $orderData['order_no'],
                     'product_name' => $item['product_name'],
                     'quantity' => $item['quantity'],
                     'price_bdt' => $item['price_bdt'],
                     'size' => $item['size'] ?? null,
                     'product_link' => $item['product_link'] ?? null,
                     'note' => $item['note'] ?? null,
                     'total' => $item['total'],
                     'paid_amount' => $item['paid_amount'],
                     'due' => $item['due'],
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);

                 // Save files
                 foreach ($item['files'] as $filePath) {
                     DB::table('reqorder_item_files')->insert([
                         'reqorder_item_id' => $itemId,
                         'order_no' => $orderData['order_no'],
                         'file_path' => $filePath,
                         'created_at' => now(),
                         'updated_at' => now(),
                     ]);
                 }
             }

             DB::commit();

             // Clear session
             session()->forget('pending_order');

             // ✅ Add flash message before redirect
             flash(translate('Order Created Successfully'))->success();

             return redirect()->route('frontend.order.index');
         } catch (\Exception $e) {
             DB::rollBack();
             return redirect()->route('frontend.order.index')->with('error', 'Error while confirming order.');
         }
     }




    public function updateAddress(Request $request)
    {
        $addressId = $request->input('address_id');
        $user = Auth::user();

        // Ensure the address exists and belongs to the logged-in user
        $address = Address::where('user_id', $user->id)->find($addressId);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found or not associated with your account.']);
        }

        // Optionally, store the address in the session
        session(['selected_address' => $address]);

        // Return address data so we can update the front end dynamically
        return response()->json([
            'success' => true,
            'address' => [
                'address' => $address->address,
                'city' => $address->city->name ?? '',
                'state' => $address->state->name ?? '',
                'country' => $address->country->name ?? '',
                'postal_code' => $address->postal_code,
                'phone' => $address->phone,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ]
        ]);
    }





    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Decrypt the ID first
        $orderId = decrypt($id);

        // Fetch the order details, ensuring it belongs to the authenticated user
        $reqOrder = ReqOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Fetch items from reqorder_items using the correct column name
        $orderItems = ReqOrderItem::with('files')
            ->where('reqorder_id', $orderId)
            ->get();



        // Return the view with the fetched data
        return view('frontend.req-order.show', compact('reqOrder', 'orderItems'));
    }



    public function downloadInvoice($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $reqorder = ReqOrder::findOrFail($id);
        } catch (\Exception $e) {
            flash(translate('Invalid invoice or permission.'))->error();
            return redirect()->route('home');
        }

        if (!auth()->check()) {
            flash(translate("You must be logged in to access this invoice."))->error();
            return redirect()->route('home');
        }

        if (auth()->user()->user_type === 'customer' && auth()->id() == $reqorder->user_id) {
            return PDF::loadView('frontend.req-order.invoice', [
                'reqorder' => $reqorder
            ])->download('reqorder-' . $reqorder->order_no . '.pdf');
        }

        flash(translate("You do not have the right permission to access this invoice."))->error();
        return redirect()->route('home');
    }




    private function getLanguageDirection($language_code)
    {
        $language = Language::where('code', $language_code)->first();
        if ($language && $language->rtl == 1) {
            // For RTL languages
            return ['rtl', 'right', 'left'];
        }
        // For LTR languages
        return ['ltr', 'left', 'right'];
    }

    private function getFontFamily($currency_code, $language_code)
    {
        // Return font family based on currency and language
        if ($currency_code == 'BDT' || $language_code == 'bd') {
            return "'Hind Siliguri','freeserif'";
        } elseif ($currency_code == 'KHR' || $language_code == 'kh') {
            return "'Hanuman','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            return "'arnamu','sans-serif'";
        } elseif (
            in_array($currency_code, ['AED', 'EGP', 'IQD', 'SA', 'OM', 'SDG', 'ILS']) ||
            in_array($language_code, ['sa', 'jo'])
        ) {
            return "xbriyaz";
        } elseif ($currency_code == 'THB') {
            return "'Kanit','sans-serif'";
        } elseif ($currency_code == 'CNY' || $language_code == 'zh') {
            return "'sun-exta','gb'";
        } elseif ($currency_code == 'MMK' || $language_code == 'mm') {
            return 'tharlon';
        } elseif ($currency_code == 'USD') {
            return "'Roboto','sans-serif'";
        }
        return "freeserif"; // Default font
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReqOrder $reqOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReqOrder $reqOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReqOrder $reqOrder)
    {
        //
    }

    // In ReqOrderController.php
    public function showPaymentForm($encryptedItemId)
    {
        try {
            $itemId = Crypt::decrypt($encryptedItemId);
            $item = ReqOrderItem::findOrFail($itemId);

            // Check if the authenticated user is the owner of the order
            if ($item->order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to make a payment for this order.');
            }

            $banks = Bank::all(); // Fetch all available banks

            return view('frontend.req-order.payment_form', compact('item', 'banks'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid item ID.');
        }
    }

    /*public function makePayment(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:reqorder_items,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $item = ReqOrderItem::findOrFail($request->item_id);

            // Check if the authenticated user is the owner of the order
            if ($item->order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to make a payment for this order.');
            }

            if ($item->due <= 0) {
                return redirect()->back()->with('error', 'This item is already fully paid.');
            }

            $paymentToApply = min($request->amount, $item->due);
            $item->paid_amount += $paymentToApply;
            $item->due -= $paymentToApply;
            $item->payment_status = ($item->due <= 0) ? 'paid' : 'partial';
            $item->payment_method = $request->payment_method;

            // Ensure bank_id is updated only if it's a valid numeric ID
            if ($request->payment_method != 'ssl' && is_numeric($request->payment_method)) {
                $item->bank_id = $request->payment_method;
            }

            $item->save();

            // Fetch and update the orderxc
            $order = ReqOrder::with('items')->find($item->reqorder_id);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            $order->due = $order->items->sum('due');
            $order->paid_amount = $order->items->sum('paid_amount');
            $order->payment_method = $request->payment_method;
            $order->pay_status = ($order->due <= 0) ? 'paid' : 'partial';
            $order->save();

            DB::commit();

            flash(translate('Payment has been successfully processed!'))->success();
            return redirect()->route('frontend.order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }*/


    public function makePayment(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:reqorder_items,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $item = ReqOrderItem::findOrFail($request->item_id);

            // Check if the authenticated user is the owner of the order
            if ($item->order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to make a payment for this order.');
            }

            if ($item->due <= 0) {
                return redirect()->back()->with('error', 'This item is already fully paid.');
            }

            $paymentToApply = min($request->amount, $item->due);
            $item->paid_amount += $paymentToApply;
            $item->due -= $paymentToApply;
            $item->payment_status = ($item->due <= 0) ? 'paid' : 'partial';
            $item->payment_method = $request->payment_method;

            // Ensure bank_id is updated only if it's a valid numeric ID
            if ($request->payment_method != 'ssl' && is_numeric($request->payment_method)) {
                $item->bank_id = $request->payment_method;
            }

            $item->save();

            // Fetch and update the order
            $order = ReqOrder::with('items')->find($item->reqorder_id);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            $order->due = $order->items->sum('due');
            $order->paid_amount = $order->items->sum('paid_amount');
            $order->payment_method = $request->payment_method;
            $order->pay_status = ($order->due <= 0) ? 'paid' : 'partial';
            $order->save();

            // Handle bank transaction and cash-in logic
            $paymentMethod = Bank::findOrFail($request->payment_method); // Find the bank

            // Increment the bank balance (since this is a customer payment)
            $paymentMethod->increment('current_balance', $request->amount);

            // Create a CashIn transaction record
            CashIn::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Item ID: ' . $item->id,
                'payment_mode' => 'Bank',
                'category' => 'Order Payment',
                'notes' => 'Payment for Item ID: ' . $item->id,
                'date' => now(),
            ]);

            // Log the bank transaction details before saving
            \Log::info('Creating bank transaction:', [
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Item ID: ' . $item->id,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order No: ' . $item->order->order_no . ', Item ID: ' . $item->id,
                'date' => now(),
            ]);

            // Create a BankTransaction record
            BankTransaction::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Item ID: ' . $item->id,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order No: ' . $item->order->order_no . ', Item ID: ' . $item->id,
                'date' => now(),
            ]);

            DB::commit();

            flash(translate('Payment has been successfully processed and recorded as Cash In!'))->success();
            return redirect()->route('frontend.order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }





}
