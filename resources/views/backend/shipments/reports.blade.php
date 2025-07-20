@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center text-primary mb-4">Shipment & Order Reports</h2>

        <!-- Search Form -->
        <form action="{{ route('backend.shipments.report') }}" method="GET" class="mb-4 p-3 bg-light shadow-sm rounded">
            <div class="row">
                <!-- Shipment Dropdown -->
                <div class="col-md-5">
                    <label for="shipment_id" class="font-weight-bold">Select Shipment:</label>
                    <select id="shipment_id" name="shipment_id" class="form-control">
                        <option value="">All Shipments</option>
                        @foreach ($shipments as $shipment)
                            <option value="{{ $shipment->id }}" {{ request('shipment_id') == $shipment->id ? 'selected' : '' }}>
                                {{ $shipment->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Dropdown -->
                <div class="col-md-5">
                    <label for="customer_id" class="font-weight-bold">Select Customer:</label>
                    <select id="customer_id" name="customer_id" class="form-control">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Search</button>
                </div>
            </div>
        </form>

        <!-- Display Orders -->
        <div class="card">
            <div class="card-body">
                @if ($orders && !$orders->isEmpty())
                    @php
                        // Group orders by shipment_id
                        $groupedOrders = $orders->groupBy(function ($order) {
                            return optional($order->items->first())->shipment->id ?? 'unknown';
                        });
                    @endphp

                    @foreach ($groupedOrders as $shipmentId => $shipmentOrders)
                        @php
                            $shipment = $shipments->firstWhere('id', $shipmentId);
                        @endphp

                        <div class="mb-4 p-3 border border-primary rounded">
                            <h4 class="text-primary font-weight-bold">
                                Shipment: {{ $shipment->name ?? 'Unknown Shipment' }}
                            </h4>

                            @php
                                $customersGrouped = $shipmentOrders->groupBy('user_id');
                            @endphp

                            @foreach ($customersGrouped as $customerId => $customerOrders)
                                @php
                                    $customer = $customers->firstWhere('id', $customerId);
                                @endphp

                                <div class="ml-4 p-3 border border-secondary rounded">
                                    <h5 class="text-secondary font-weight-bold">
                                        Customer: {{ $customer->name ?? 'Unknown Customer' }}
                                    </h5>
                                    @if ($shipment && $shipment->id && $customer && $customer->id)
                                        <a href="{{ route('backend.shipments.invoice', [
        'shipment' => $shipment->id,
        'customer' => $customer->id
    ]) }}" class="btn btn-sm btn-info mt-2">
                                            Generate Invoice
                                        </a>
                                    @else
                                        <p class="text-danger">Invalid shipment or customer data.</p>
                                    @endif



                                    <table class="table table-bo@extends('backend.layouts.app')

                                    @section('content')
                                        <div class="container mt-4">
                                    <h2 class="text-center text-primary mb-4">Shipment & Order Reports</h2>

                                    <!-- Search Form -->
                                    <form action="{{ route('backend.shipments.report') }}" method="GET" class="mb-4 p-3 bg-light shadow-sm rounded">
                                        <div class="row">
                                            <!-- Shipment Dropdown -->
                                            <div class="col-md-5">
                                                <label for="shipment_id" class="font-weight-bold">Select Shipment:</label>
                                                <select id="shipment_id" name="shipment_id" class="form-control">
                                                    <option value="">All Shipments</option>
                                                    @foreach ($shipments as $shipment)
                                                        <option value="{{ $shipment->id }}" {{ request('shipment_id') == $shipment->id ? 'selected' : '' }}>
                                                            {{ $shipment->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Customer Dropdown -->
                                            <div class="col-md-5">
                                                <label for="customer_id" class="font-weight-bold">Select Customer:</label>
                                                <select id="customer_id" name="customer_id" class="form-control">
                                                    <option value="">All Customers</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                            {{ $customer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Search Button -->
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Display Orders -->
                                    <div class="card">
                                        <div class="card-body">
                                            @if ($orders && !$orders->isEmpty())
                                                @php
                                                    // Group orders by shipment_id
                                                    $groupedOrders = $orders->groupBy(function ($order) {
                                                        return optional($order->items->first())->shipment->id ?? 'unknown';
                                                    });
                                                @endphp

                                                @foreach ($groupedOrders as $shipmentId => $shipmentOrders)
                                                    @php
                                                        $shipment = $shipments->firstWhere('id', $shipmentId) ?? (object) ['id' => null, 'name' => 'Unknown Shipment'];
                                                    @endphp


                                                    <div class="mb-4 p-3 border border-primary rounded">
                                                        <h4 class="text-primary font-weight-bold">
                                                            Shipment: {{ $shipment->name ?? 'Unknown Shipment' }}
                                                        </h4>

                                                        @php
                                                            $customersGrouped = $shipmentOrders->groupBy('user_id');
                                                        @endphp

                                                        @foreach ($customersGrouped as $customerId => $customerOrders)
                                                            @php
                                                                $customer = $customers->firstWhere('id', $customerId) ?? (object) ['id' => null, 'name' => 'Unknown Customer'];
                                                            @endphp


                                                            <div class="ml-4 p-3 border border-secondary rounded">
                                                                <h5 class="text-secondary font-weight-bold">
                                                                    Customer: {{ $customer->name ?? 'Unknown Customer' }}
                                                                </h5>
                                                                @if($shipment->id && $customer->id)
                                                                    <a href="{{ route('backend.shipments.invoice', ['shipment' => $shipment->id, 'customer' => $customer->id]) }}"
                                                                       class="btn btn-sm btn-info mt-2">
                                                                        Generate Invoice
                                                                    </a>
                                                                @endif



                                                                <table class="table table-bordered mt-3">
                                                                    <thead class="thead-dark">
                                                                    <tr>
                                                                        <th>Order No</th>
                                                                        <th>Product Name</th>
                                                                        <th>Quantity</th>
                                                                        <th>Price (BDT)</th>
                                                                        <th>Total (BDT)</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach ($customerOrders as $order)
                                                                        @foreach ($order->items as $item)
                                                                            <tr>
                                                                                @if ($loop->first)
                                                                                    <td rowspan="{{ $order->items->count() }}" class="align-middle font-weight-bold">
                                                                                        {{ $order->order_no }}
                                                                                    </td>
                                                                                @endif
                                                                                <td>{{ $item->product_name }}</td>
                                                                                <td>{{ $item->quantity }}</td>
                                                                                <td>৳{{ number_format($item->price_bdt, 2) }}</td>
                                                                                <td>৳{{ number_format($item->quantity * $item->price_bdt, 2) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-center text-danger">No orders found for the selected filters.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endsection
                                rdered mt-3">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th>Order No</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price (BDT)</th>
                                            <th>Total (BDT)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($customerOrders as $order)
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    @if ($loop->first)
                                                        <td rowspan="{{ $order->items->count() }}" class="align-middle font-weight-bold">
                                                            {{ $order->order_no }}
                                                        </td>
                                                    @endif
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>৳{{ number_format($item->price_bdt, 2) }}</td>
                                                    <td>৳{{ number_format($item->quantity * $item->price_bdt, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-danger">No orders found for the selected filters.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
