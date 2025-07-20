@if ($orders->isEmpty())
    <p>No orders found for the selected filters.</p>
@else
    <div class="overflow-x-auto">
        @php
            $customers = $orders->groupBy('customer_id'); // Group orders by customer
        @endphp

        @foreach ($customers as $customerId => $customerOrders)
            <div class="flex justify-between items-center my-4">
                <h2 class="text-lg font-semibold text-gray-700">
                    Orders for {{ $customerOrders->first()->customer->name ?? 'N/A' }}
                </h2>

                <!-- Invoice Button (Top-Right) -->
                <a href="{{ route('generate-invoice-customer', ['customer_id' => $customerId]) }}"
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow">
                    Generate Invoice for {{ $customerOrders->first()->customer->name ?? 'N/A' }}
                </a>
            </div>

            <table class="w-full border-collapse border border-gray-300 text-sm mb-6">
                <thead class="bg-gray-100 text-gray-700 uppercase text-left">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Order No</th>
                    <th class="border border-gray-300 px-4 py-2">Product Name</th>
                    <th class="border border-gray-300 px-4 py-2">Price (BDT)</th>
                    <th class="border border-gray-300 px-4 py-2">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2">Total Amount (BDT)</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($customerOrders as $order)
                    @foreach ($order->items as $item)
                        <tr class="hover:bg-gray-50">
                            @if ($loop->first)
                                <td class="border border-gray-300 px-4 py-2 font-semibold text-blue-600" rowspan="{{ $order->items->count() }}">
                                    {{ $order->order_no ?? 'N/A' }}
                                </td>
                            @endif
                            <td class="border border-gray-300 px-4 py-2">{{ $item->product_name ?? 'N/A' }}</td>
                            <td class="border border-gray-300 px-4 py-2">৳{{ number_format($item->price_bdt ?? 0, 2) }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->quantity ?? 0 }}</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">
                                ৳{{ number_format(($item->price_bdt ?? 0) * ($item->quantity ?? 0), 2) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Total Row for Order -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-right">Total Order Amount:</td>
                        <td class="border border-gray-300 px-4 py-2">
                            ৳{{ number_format($order->items->sum(fn($item) => ($item->price_bdt ?? 0) * ($item->quantity ?? 0)), 2) }}
                        </td>
                    </tr>

                    <!-- Delivery Charge Row -->
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-right">Delivery Charge:</td>
                        <td class="border border-gray-300 px-4 py-2">৳{{ number_format($order->delivery_charge ?? 0, 2) }}</td>
                    </tr>

                    <!-- Grand Total Row -->
                    <tr class="bg-green-200 font-bold">
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-right">Total Payable (Including Delivery):</td>
                        <td class="border border-gray-300 px-4 py-2 text-green-700">
                            ৳{{ number_format(($order->items->sum(fn($item) => ($item->price_bdt ?? 0) * ($item->quantity ?? 0))) + ($order->delivery_charge ?? 0), 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endif
