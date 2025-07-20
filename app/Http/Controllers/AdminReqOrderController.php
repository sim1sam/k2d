<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\Brand;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Customer;
use App\Models\ReqOrder;
use App\Models\ReqOrderItem;
use App\Models\ReqOrderItemFile;
use App\Models\Seller;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Session;
use PDF;
use Config;
use App\Models\Currency;
use App\Models\Language;

class AdminReqOrderController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the search and filter inputs
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $orderStatus = $request->input('order_status');
        $paymentStatus = $request->input('payment_status');
        $customerId = $request->input('customer_id');

        // Query Orders
        $orders = ReqOrder::when($search, function ($query, $search) {
            return $query->where('order_no', 'like', '%' . $search . '%');
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($orderStatus, function ($query, $orderStatus) {
                return $query->where('status', $orderStatus);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                return $query->where('pay_status', $paymentStatus);
            })
            ->when($customerId, function ($query, $customerId) {
                return $query->where('user_id', $customerId);
            })
            ->orderBy('created_at', 'desc') // Show latest orders first
            ->paginate(6);

        // Fetch customers where user_type is customer
        $customers = User::where('user_type', 'customer')->get();
        $banks = Bank::all();
        // Pass data to view
        return view('backend.req_order.index', compact('orders', 'customers', 'banks'));
    }
    public function create()
    {
        // Retrieve the order with related items and their files


        // Fetch all customers (users with user_type = 'customer')
        $customers = User::where('user_type', 'customer')->get();

        // Fetch all vendors (users with user_type = 'seller')
        $vendors = User::where('user_type', 'seller')->get();

        // Fetch all brands
        $brands = Brand::all();
        $banks = Bank::all();
        $shipments = Shipment::all();
        // Pass data to the view
        return view('backend.req_order.create', compact('customers',  'vendors', 'brands', 'banks', 'shipments'));
    }
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id', // Changed from user_id to customer_id
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.product_link' => 'nullable|url',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_bdt' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
            'items.*.files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:10240',
        ]);

        // Get customer details
        $customer = User::find($request->customer_id);

        // Generate unique order_no (K2DYYYYMMDD-HHMMSSXX)
        $timestamp = Carbon::now()->format('Ymd-His');
        $randomDigits = mt_rand(10, 99);
        $orderNo = 'K2D' . $timestamp . $randomDigits;

        // Calculate total order amount
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += $item['quantity'] * $item['price_bdt'];
        }

        // Get authenticated user
        $authUser = auth()->user();

        try {
            DB::beginTransaction();

            // Store the order
            $order = ReqOrder::create([
                'user_id' => $customer->id,
                'order_no' => $orderNo,
                'total' => $totalAmount,
                'status' => 'pending',
                'is_admin' => 1,
                'created_by_name' => $authUser->name,
                'created_by_email' => $authUser->email,
                'name' => $customer->name,      // Customer's name
                'email' => $customer->email,    // Customer's email
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Loop through each item and store the data
            foreach ($request->items as $item) {
                // Calculate item-wise total, due, and paid amount
                $totalPrice = $item['quantity'] * $item['price_bdt'];
                $paidAmount = 0;
                $dueAmount = $totalPrice - $paidAmount;

                $orderItem = $order->items()->create([
                    'order_no' => $order->order_no,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price_bdt' => $item['price_bdt'],
                    'size' => $item['size'] ?? null,
                    'product_link' => $item['product_link'] ?? null,
                    'note' => $item['note'] ?? null,
                    'total' => $totalPrice,
                    'paid_amount' => $paidAmount,
                    'due' => $dueAmount,
                    'name' => $customer->name,      // Customer's name
                    'email' => $customer->email,    // Customer's email
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Handle file upload (image) if any
                if (isset($item['files'])) {
                    foreach ($item['files'] as $file) {
                        $fileName = $orderNo . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $filePath = 'k2d/uploads/reqorder/' . $fileName;
                        $file->move(public_path('k2d/uploads/reqorder'), $fileName);

                        // Save the file path in the database
                        DB::table('reqorder_item_files')->insert([
                            'reqorder_item_id' => $orderItem->id,
                            'order_no' => $orderNo,
                            'file_path' => $filePath,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            flash(translate('Order Created successfully!'))->success();
            return redirect()->route('backend.req.order.address.edit', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            flash(translate('Error creating order: ' . $e->getMessage()))->error();
            return redirect()->back()->withInput();
        }
    }



    public function edit($id)
    {
        // Retrieve the order with related items and their files
        $order = ReqOrder::with('items.files')->findOrFail($id);

        // Fetch all customers (users with user_type = 'customer')
        $customers = User::where('user_type', 'customer')->get();

        // Fetch all vendors (users with user_type = 'seller')
        $vendors = User::where('user_type', 'seller')->get();

        // Fetch all brands
        $brands = Brand::all();
        $banks = Bank::all();
        $shipments = Shipment::all();
        // Pass data to the view
        return view('backend.req_order.edit', compact('customers', 'order', 'vendors', 'brands', 'banks', 'shipments'));
    }


    public function update(Request $request, $id)
    {


        // Validate the input
        $request->validate([
            'delivery_charge' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1', // Ensure items array exists
            'items.*.id' => 'required|exists:reqorder_items,id', // Ensure item exists in DB
            'items.*.product_name' => 'required|string',
            'items.*.product_link' => 'nullable|url',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price_bdt' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
            'items.*.purchase_amount' => 'nullable|numeric|min:0',
            'items.*.user_id' => 'nullable|exists:user,id',
            'items.*.brand_id' => 'nullable|exists:brands,id',
            'items.*.shipment_id' => 'nullable|exists:shipments,id',
            'items.*.bank_id' => 'nullable|exists:banks,id', // Fixed bank validation
            'items.*.files' => 'nullable|array',
            'items.*.files.*' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);



        // Find the order to be updated
        $order = ReqOrder::findOrFail($id);

        // Store the current delivery charge and other important details
        $previousDeliveryCharge = $order->delivery_charge;
        $currentPaidAmount = $order->paid_amount;
        $currentDueAmount = $order->due;

        // Update order delivery charge
        $order->delivery_charge = $request->input('delivery_charge');

        $totalAmount = 0; // Initialize total amount

        // Update order items and calculate total amount
        foreach ($request->input('items') as $index => $item) {
            $orderItem = $order->items[$index];
            $orderItem->product_name = $item['product_name'];
            $orderItem->product_link = $item['product_link'];
            $orderItem->size = $item['size'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price_bdt = $item['price_bdt'];
            $orderItem->note = $item['note'];
            $orderItem->purchase_amount = $item['purchase_amount'];
            $orderItem->bank_id = $item['bank_id'];
            $orderItem->vendor_id = $item['vendor_id'];
            $orderItem->brand_id = $item['brand_id'];
            $orderItem->shipment_id = $item['shipment_id'];
            $orderItem->save();

            // Calculate the item total and add to totalAmount
            $totalAmount += round($item['quantity'] * $item['price_bdt']); // Round item total amount
        }

        foreach ($request->input('items') as $index => $item) {
            if (!empty($item['bank_id']) && !empty($item['purchase_amount'])) {
                $bank = Bank::find($item['bank_id']);
                if ($bank) {
                    $totalPurchaseAmount = round($item['purchase_amount'] * $item['quantity']);
                    $bank->decrement('current_balance', $totalPurchaseAmount);

                    // Create CashOut record
                    CashOut::create([
                        'bank_id' => $item['bank_id'],
                        'order_id' => $order->id,
                        'amount' => $totalPurchaseAmount,
                        'recipient' => 'Vendor Payment',
                        'payment_mode' => 'Bank Transfer',
                        'category' => 'Purchase',
                        'notes' => 'Purchase payment for Order #' . $order->order_no . ' - ' . $item['product_name'],
                        'date' => now(),
                    ]);

                    // Log transaction in BankTransaction
                    BankTransaction::create([
                        'bank_id' => $item['bank_id'],
                        'order_id' => $order->id,
                        'amount' => $totalPurchaseAmount,
                        'source' => 'Vendor Payment',
                        'payment_mode' => 'Bank Transfer',
                        'category' => 'Purchase',
                        'notes' => 'Purchase payment for Order #' . $order->order_no . ' - ' . $item['product_name'],
                        'date' => now(),
                        'transaction_type' => 'cash_out',
                    ]);
                }
            }
        }

        // Update total amount and due amount
        $newTotalAmount = round($totalAmount) + round($order->delivery_charge); // Round the total including delivery charge

        // If the order has already been paid or partially paid, handle delivery charge difference
        if ($order->pay_status == 'paid' || $order->pay_status == 'partial') {
            // Show the previous delivery charge in the frontend (you can display $previousDeliveryCharge wherever necessary)

            // Calculate the difference in delivery charge
            $deliveryChargeDifference = abs(round($order->delivery_charge) - round($previousDeliveryCharge)); // Round the difference

            // If the delivery charge has increased, add the difference to the due amount
            if ($order->delivery_charge > $previousDeliveryCharge) {
                $order->due += $deliveryChargeDifference; // Round due amount after change
            }

            // Set the total and due amounts as they were before the update
            $order->total = $order->total; // Keep the total amount unchanged
            $order->due = round($currentDueAmount + $deliveryChargeDifference); // Round the due amount
        } else {
            // If the order is not paid, update total and due normally
            $order->total = round($newTotalAmount); // Round total amount
            $order->due = round($newTotalAmount); // Round the due amount
        }

        // Save the updated order
        $order->save();

        flash(translate('Request Order has been updated successfully!'))->success();

        // Redirect back to the order show page
        return redirect()->route('backend.req.order.show', $order->id);
    }




    public function show($id)
    {
        $order = ReqOrder::with([
            'customer',
            'items.vendor',
            'items.brand',
            'items.bank', // Now correctly mapped to bank_id
            'items.shipment',
            'items.vendor',
            'items.files'
        ])->findOrFail($id);

        return view('backend.req_order.show', compact('order'));
    }


    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipfromkol,shipfromdhk,completed,canceled,transit',
        ]);

        $order = ReqOrder::findOrFail($id);
        $order->status = $request->order_status;
        $order->save();

        flash(translate(key: 'Order status updated successfully!'))->success();


    return back();
}

    public function updateOrderItemStatus(Request $request, $id)
    {
        $request->validate([
            'item_status' => 'required|in:pending,processing,shipfromkol,shipfromdhk,completed,canceled,transit',
        ]);

        $item = ReqOrderItem::findOrFail($id);
        $item->status = $request->item_status;
        $item->save();

        flash(translate(key: 'Order item status updated successfully!'))->success();


    return back();
}


    public function destroyItem($id)
    {

        $item = ReqOrderItem::with('order')->findOrFail($id);

        // Ensure the item belongs to an order before proceeding
        if (!$item->order) {
            flash(translate('This item does not belong to any order.'))->error();
            return back();
        }

        // Check if the order has more than one item
        if ($item->order->items()->count() <= 1) {
            flash(translate('An order must have at least one item!'))->error();
            return back();
        }

        $item->delete();
        flash(translate('Order item removed successfully!'))->success();
        return back();
    }


    /*public function adminMakePayment(Request $request)
    {
        // Validate the input
        $request->validate([
            'order_id' => 'required|exists:reqorders,id', // Order ID
            'amount' => 'required|numeric|min:0', // Payment amount
            'payment_method' => 'required|exists:banks,id', // Payment method (bank ID)
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the order
            $order = ReqOrder::findOrFail($request->order_id);

            // Ensure the payment amount does not exceed the due amount
            if ($request->amount > $order->due) {
                flash(translate('Payment amount cannot exceed the due amount!'))->error();
                return redirect()->back();
            }

            // Update paid amount and due amount
            $order->paid_amount += $request->amount;
            $order->due = max(0, $order->total - $order->paid_amount); // Ensure due amount doesn't go negative

            // Check if the order is fully paid
            if ($order->paid_amount >= $order->total) {
                $order->pay_status = 'paid'; // Update payment status if fully paid
            } else {
                $order->pay_status = 'partial'; // Update to partial if not fully paid
            }

            // Save the payment method (bank_id)
            $order->payment_method = $request->payment_method;

            // Save the order
            $order->save();

            // Process CashIn (incoming payment to the bank)
            $paymentMethod = Bank::findOrFail($request->payment_method); // Find the bank

            // Increment the bank balance (since this is a customer payment)
            $paymentMethod->increment('current_balance', $request->amount);

            // Create a CashIn transaction record
            CashIn::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Customer Payment for Order ID: ' . $order->order_no,
                'payment_mode' => 'Bank',
                'category' => 'Order Payment',
                'notes' => 'Payment for Order ID: ' . $order->order_no,
                'date' => now(),
            ]);

            // Log the bank transaction details before saving
            \Log::info('Creating bank transaction:', [
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Customer Payment for Order ID: ' . $order->order_no,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order ID: ' . $order->order_no,
                'date' => now(),
            ]);

            // Create a BankTransaction record
            $bankTransaction = BankTransaction::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Customer Payment for Order ID: ' . $order->order_no,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order ID: ' . $order->order_no,
                'date' => now(),
            ]);

            // Check if the bank transaction was created successfully
            if ($bankTransaction) {
                \Log::info('Bank transaction saved successfully');
            } else {
                \Log::error('Failed to save bank transaction');
            }

            // Commit the transaction
            DB::commit();

            flash(translate('Payment has been successfully processed and recorded as Cash In!'))->success();

            // Redirect back to the admin order history or the appropriate route
            return redirect()->route('backend.req.order.index');
        } catch (\Exception $e) {
            // Log error message for debugging
            \Log::error('Error processing payment: ' . $e->getMessage());
            DB::rollBack();

            flash(translate('There was an error processing the payment.'))->error();
            return redirect()->back();
        }
    }*/
    public function adminMakePayment(Request $request)
    {
        // Validate the input
        $request->validate([
            'item_id' => 'required|exists:reqorder_items,id', // Item ID
            'amount' => 'required|numeric|min:0', // Payment amount
            'payment_method' => 'required|exists:banks,id', // Payment method (bank ID)
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the specific order item
            $orderItem = ReqOrderItem::findOrFail($request->item_id);

            // Ensure the payment amount does not exceed the due amount for the item
            if ($request->amount > $orderItem->due) {
                flash(translate('Payment amount cannot exceed the due amount for this item!'))->error();
                return redirect()->back();
            }

            // Update paid amount and due amount for the item
            $orderItem->paid_amount += $request->amount;
            $orderItem->due = max(0, $orderItem->total - $orderItem->paid_amount); // Ensure due amount doesn't go negative

            // Check if the item is fully paid
            if ($orderItem->paid_amount >= $orderItem->total) {
                $orderItem->payment_status = 'paid'; // Update payment status to 'paid' if fully paid
            } else {
                $orderItem->payment_status = 'partial'; // Update payment status to 'partial' if not fully paid
            }

            // Save the order item
            $orderItem->save();

            // Update the related order status
            $order = $orderItem->order; // Get the order associated with the item
            $order->pay_status = ($order->items->sum('paid_amount') >= $order->total) ? 'paid' : 'partial';
            $order->save();

            // Save the payment method (bank_id)
            $orderItem->payment_method = $request->payment_method;

            // Process CashIn (incoming payment to the bank)
            $paymentMethod = Bank::findOrFail($request->payment_method); // Find the bank

            // Increment the bank balance (since this is a customer payment)
            $paymentMethod->increment('current_balance', $request->amount);

            // Create a CashIn transaction record
            CashIn::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'payment_mode' => 'Bank',
                'category' => 'Order Payment',
                'notes' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'date' => now(),
            ]);

            // Log the bank transaction details before saving
            \Log::info('Creating bank transaction:', [
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'date' => now(),
            ]);

            // Create a BankTransaction record
            BankTransaction::create([
                'bank_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'source' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'transaction_type' => 'cash_in',
                'category' => 'Order Payment',
                'payment_mode' => 'Bank',
                'notes' => 'Payment for Order No: ' . $order->order_no . ', Item ID: ' . $orderItem->id,
                'date' => now(),
            ]);

            // Commit the transaction
            DB::commit();

            flash(translate('Payment has been successfully processed and recorded as Cash In!'))->success();

            // Redirect back to the admin order history or the appropriate route
            return redirect()->route('backend.req.order.index');
        } catch (\Exception $e) {
            // Log error message for debugging
            \Log::error('Error processing payment: ' . $e->getMessage());
            DB::rollBack();

            flash(translate('There was an error processing the payment.'))->error();
            return redirect()->back();
        }
    }



    public function updateAddress($id)
    {
        // Retrieve the order with related items and their files
        $order = ReqOrder::with('items.files')->findOrFail($id);


        // Pass data to the view
        return view('backend.req_order.address', compact('order'));
    }
    public function uupdateAddress(Request $request, $orderId)
    {
        // Validate input
        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country'=> 'required|string|max:20',
        ]);

        // Find the order
        $order = ReqOrder::findOrFail($orderId);

        // Update fields
        $order->address = $request->address;
        $order->phone = $request->phone;
        $order->state = $request->state;
        $order->city = $request->city;
        $order->postal_code = $request->postal_code;
        $order->country = $request->country;
        // Save changes
        $order->save();
        flash(translate('Address  successfully update'))->success();
        // Redirect with success message
        return redirect()->route('backend.req.order.index');
    }

    public function downloadInvoice($order_id, $item_id = null)
    {

        // Get the order by ID
        $reqorder = ReqOrder::findOrFail($order_id);

        // Get the currency code from session or default setting
        $currency_code = Session::has('currency_code') ? Session::get('currency_code') : Currency::findOrFail(get_setting('system_default_currency'))->code;

        // Get the language code from session or default to the app's locale
        $language_code = Session::get('locale', Config::get('app.locale'));

        // Determine the direction and text alignment based on language settings
        $language = Language::where('code', $language_code)->first();
        if ($language && $language->rtl == 1) {
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        } else {
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        // Set the font family based on currency code or language code
        switch ($currency_code) {
            case 'BDT':
            case 'bd':
                $font_family = "'Hind Siliguri','freeserif'";
                break;
            case 'KHR':
            case 'kh':
                $font_family = "'Hanuman','sans-serif'";
                break;
            case 'AMD':
                $font_family = "'arnamu','sans-serif'";
                break;
            case 'AED':
            case 'EGP':
            case 'sa':
            case 'IQD':
            case 'ir':
            case 'om':
            case 'ROM':
            case 'SDG':
            case 'ILS':
            case 'jo':
                $font_family = "xbriyaz";
                break;
            case 'THB':
                $font_family = "'Kanit','sans-serif'";
                break;
            case 'CNY':
            case 'zh':
                $font_family = "'sun-exta','gb'";
                break;
            case 'MMK':
            case 'mm':
                $font_family = 'tharlon';
                break;
            case 'THB':
            case 'th':
                $font_family = "'zawgyi-one','sans-serif'";
                break;
            case 'USD':
                $font_family = "'Roboto','sans-serif'";
                break;
            default:
                $font_family = "freeserif";
                break;
        }

        // Check if the user has permission to access this order
        if (in_array(auth()->user()->user_type, ['admin', 'staff']) || in_array(auth()->id(), [$reqorder->user_id, $reqorder->seller_id])) {

            // If item_id is provided, get the specific item
            if ($item_id) {
                $orderDetail = $reqorder->items()->findOrFail($item_id);

                // Generate PDF for a single item
                return PDF::loadView('backend.req_order.itinvoice_pdf', [
                    'orderDetail' => $orderDetail,
                    'reqorder' => $reqorder,
                    'font_family' => $font_family,
                    'direction' => $direction,
                    'text_align' => $text_align,
                    'not_text_align' => $not_text_align
                ])->download('reqorder-item-' . $orderDetail->product_name . '.pdf');
            }

            // Generate PDF for the entire order (all items in the order)
            return PDF::loadView('backend.req_order.invoice_pdf', [
                'reqorder' => $reqorder,
                'font_family' => $font_family,
                'direction' => $direction,
                'text_align' => $text_align,
                'not_text_align' => $not_text_align
            ])->download('reqorder-' . $reqorder->order_no . '.pdf');
        }

        flash(translate("You do not have the right permission to access this invoice."))->error();
        return redirect()->route('home');
    }

    public function applyDiscount(Request $request, $itemId)
    {
        // Validate the request
        $validated = $request->validate([
            'discount' => 'required|numeric|min:0', // Ensure the discount is a number and greater than 0
        ]);

        // Retrieve the item by its ID
        $item = ReqOrderItem::findOrFail($itemId);

        // Calculate the item total (quantity * price)
        $itemTotal = $item->quantity * $item->price_bdt;

        // Apply the discount to the individual item
        $discountAmount = $validated['discount'];

        // Calculate the item's due: item total minus the discount amount
        $itemDue = $itemTotal - $discountAmount;

        // Update the item's coupon discount and due amount
        $item->coupon_discount = $discountAmount;
        $item->due = $itemDue;
        $item->save();

        // Calculate the total discount for the ReqOrder (sum of all item discounts)
        $totalItemDiscount = ReqOrderItem::where('reqorder_id', $item->reqorder_id)
            ->sum('coupon_discount'); // Sum of all item discounts

        // Retrieve the ReqOrder and calculate its original total amount (before discounts)
        $reqOrder = ReqOrder::find($item->reqorder_id);

        // Original total due (before applying discounts) is stored in the `due` field
        $reqOrderOriginalTotal = $reqOrder->total; // This is the original total amount before any discounts

        // Calculate the updated due amount for the ReqOrder after subtracting the total discount
        // This should subtract the total item discount from the original total amount of the ReqOrder
        $reqOrderDue = $reqOrderOriginalTotal - $totalItemDiscount;

        // Ensure the `due` is never negative. If it's negative, keep it at 0
        $reqOrderDue = max($reqOrderDue, 0);  // Ensure the due is never negative

        // Update the ReqOrder's discount and due after applying the total discount
        $reqOrder->discount = $totalItemDiscount; // Total discount from all items
        $reqOrder->due = $reqOrderDue; // Updated due amount after discount
        $reqOrder->save();

        // Flash success message using the translate helper
        flash(translate('Discount applied successfully!'))->success();

        // Redirect back to the order details page
        return redirect()->route('backend.req.order.index');
    }


    public function filterCreate(Request $request)
    {
        // Retrieve the search and filter inputs
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerId = $request->input('customer_id');
        $brand = $request->input('brand');  // Filter for brand
        $vendor = $request->input('vendor'); // Filter for vendor

        // Query Orders
        $orders = ReqOrder::when($search, function ($query, $search) {
            return $query->where('order_no', 'like', '%' . $search . '%');
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($customerId, function ($query, $customerId) {
                return $query->where('user_id', $customerId);
            })
            ->when($brand, function ($query, $brand) {
                return $query->whereHas('items', function ($q) use ($brand) {
                    $q->where('brand_id', $brand); // Ensure you are using the correct field name
                });
            })
            ->when($vendor, function ($query, $vendor) {
                return $query->whereHas('items', function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor); // Vendor filter works, so ensure this is correct for brand
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        // Fetch customers where user_type is customer
        $customers = User::where('user_type', 'customer')->get();
        $banks = Bank::all();

        // Fetch all unique brands and vendors for the filters
        $brands = Brand::all();
        $vendors = User::where('user_type', 'seller')->pluck('name', 'id');

        // Pass data to view
        return view('backend.req_order.filter_index', compact('orders', 'customers', 'banks', 'brands', 'vendors'));
    }

}
