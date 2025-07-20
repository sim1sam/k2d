<?php

namespace App\Http\Controllers;

use App\Models\ReqOrder;
use App\Models\ReqOrderItem;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;
use App\Models\Address;
class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $sort_search = $request->input('search');

        // If a search query exists, filter the shipments by name or shipment note
        if ($sort_search) {
            $shipments = Shipment::where('name', 'like', '%' . $sort_search . '%')
                                 ->orWhere('shipment_note', 'like', '%' . $sort_search . '%')
                                 ->orderBy('created_at', 'desc')  // Ordering by creation date in descending order
                                 ->paginate(10);
        } else {
            // Otherwise, fetch all shipments ordered by creation date in descending order
            $shipments = Shipment::orderBy('created_at', 'desc')
                                 ->paginate(10);
        }

        return view('backend.shipments.index', compact('shipments', 'sort_search'));
    }





    public function create()
    {
        return view('backend.shipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shipment_note' => 'nullable|string|max:1000',
        ]);

        Shipment::create($request->all());

        flash(translate('Shipment created successfully!'))->success();
        return redirect()->route('backend.shipments.index');
    }

    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('backend.shipments.edit', compact('shipment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shipment_note' => 'nullable|string|max:1000',
        ]);

        $shipment = Shipment::findOrFail($id);
        $shipment->update($request->all());

        flash(translate('Shipment updated successfully!'))->success();
        return redirect()->route('backend.shipments.index');
    }

    public function show($id)
{
    $shipment = Shipment::findOrFail($id);
    return view('backend.shipments.show', compact('shipment'));
}


    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        flash(translate('Shipment deleted successfully!'))->success();
        return redirect()->route('backend.shipments.index');
    }

    public function attach($id)
    {
        // Find the shipment along with related reqOrderItems
        $shipment = Shipment::findOrFail($id);

        // Attach shipment_id to all related reqorder_items
        ReqOrderItem::where('shipment_id', null)->update(['shipment_id' => $id]);

        // Fetch the updated reqorder_items related to this shipment
        $reqOrderItems = ReqOrderItem::where('shipment_id', $id)->get();

        // Group reqorder_items by reqorder_id
        $groupedReqOrderItems = $reqOrderItems->groupBy('reqorder_id');

        // Fetch only relevant reqorders and include `order_no`
        $reqOrders = ReqOrder::whereIn('id', $reqOrderItems->pluck('reqorder_id')->unique())
            ->select('id', 'order_no') // Fetch both `id` and `order_no`
            ->paginate(10);

        // Initialize total amounts
        $totalPrice = 0;
        $totalPurchasePrice = 0;
        $totalProfitLoss = 0;

        // Calculate total price, total purchase price, and total profit or loss
        foreach ($reqOrderItems as $item) {
            $totalPrice += $item->price_bdt * $item->quantity;
            $totalPurchasePrice += $item->purchase_amount * $item->quantity;
            $totalProfitLoss += ($item->price_bdt - $item->purchase_amount) * $item->quantity;
        }

        // Return the view with necessary data
        return view('backend.shipments.attach', compact('shipment', 'groupedReqOrderItems', 'reqOrders', 'totalPrice', 'totalPurchasePrice', 'totalProfitLoss'));
    }

    // In ShipmentController.php

    public function reportindex(Request $request)
    {
        $shipments = Shipment::all();
        $customers = User::where('user_type', 'customer')->get();

        $shipmentId = $request->shipment_id;
        $customerId = $request->customer_id;

        $orders = collect(); // Empty collection for initial page load

        if ($shipmentId || $customerId) {
            $query = ReqOrder::query()->with(['items.shipment', 'customer']);

            if ($customerId) {
                $query->where('user_id', $customerId);
            }

            if ($shipmentId) {
                $query->whereHas('items', function ($q) use ($shipmentId) {
                    $q->where('shipment_id', $shipmentId);
                });
            }

            $orders = $query->get();
        } else {
            // Show all orders grouped by shipment
            $orders = ReqOrder::with(['items.shipment', 'customer'])->get();
        }

        return view('backend.shipments.reports', compact('orders', 'shipments', 'customers', 'shipmentId', 'customerId'));
    }


    public function generateInvoice($shipmentId, $customerId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $customer = User::findOrFail($customerId);
    
        // Fetch orders related to this shipment
        $orders = ReqOrder::where('user_id', $customerId)
            ->whereHas('items', function ($query) use ($shipmentId) {
                $query->where('shipment_id', $shipmentId);
            })
            ->with('items')
            ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No orders found for this customer in this shipment.');
        }
    
        // Get address from the first order
        $firstOrder = $orders->first();
        $address = $firstOrder->address;
        $city = is_object($firstOrder->city) ? $firstOrder->city->name : $firstOrder->city;
        $state = is_object($firstOrder->state) ? $firstOrder->state->name : $firstOrder->state;
        $country = is_object($firstOrder->country) ? $firstOrder->country->name : $firstOrder->country;
        $postalCode = $firstOrder->postal_code;
        $phone = $firstOrder->phone;
    
        // If no address is found in reqorders, fetch the default address from addresses table
        if (!$address) {
            $defaultAddress = Address::where('user_id', $customerId)->where('set_default', 1)->first();
            if ($defaultAddress) {
                $address = $defaultAddress->address;
                $city = is_object($defaultAddress->city) ? $defaultAddress->city->name : $defaultAddress->city;
                $state = is_object($defaultAddress->state) ? $defaultAddress->state->name : $defaultAddress->state;
                $country = is_object($defaultAddress->country) ? $defaultAddress->country->name : $defaultAddress->country;
                $postalCode = $defaultAddress->postal_code;
                $phone = $defaultAddress->phone;
            }
        }
    
        // Ensure phone is not null
        $phone = $phone ?? $customer->phone ?? 'N/A';
    
        // Generate PDF
        $pdf = Pdf::loadView('backend.shipments.invoice', compact(
            'shipment', 'customer', 'orders', 'address', 'city', 'state', 'country', 'postalCode', 'phone'
        ));
    
        return $pdf->download('invoice_' . $shipment->id . '_for_' . $customer->id . '.pdf');
    }
    
    


}
