@extends('backend.layouts.app')

@section('content')
    <div class="container rounded bg-white p-4 shadow">
        <!-- Shipment Title with Name and ID -->
        <h3 class="text-center mb-4 mt-4 text-sm font-weight-bold text-dark">
            Shipment: {{ $shipment->name }} (ID: {{ $shipment->id }})
        </h3>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="bg-warning text-white p-3 rounded shadow-sm">
                    <strong class="font-weight-bold">Total Price (BDT):</strong> {{ number_format($totalPrice, 2) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-warning text-white p-3 rounded shadow-sm">
                    <strong class="font-weight-bold">Total Purchase Price (BDT):</strong>
                    {{ number_format($totalPurchasePrice, 2) }}
                </div>
            </div>

            @php
                $profitLoss = $totalPrice - $totalPurchasePrice;
            @endphp

            <div class="col-md-4">
                <div class="p-3 rounded shadow-sm
                    @if ($profitLoss > 0) bg-success
                    @elseif($profitLoss < 0) bg-danger
                    @else bg-secondary @endif text-white">
                    <strong class="font-weight-bold">
                        @if ($profitLoss > 0)
                            Profit (BDT):
                        @elseif($profitLoss < 0)
                            Loss (BDT):
                        @else
                            No Profit/Loss
                        @endif
                    </strong>
                    {{ number_format(abs($profitLoss), 2) }}
                </div>
            </div>
        </div>

        <form action="{{ route('backend.shipment.store', ['id' => $shipment->id]) }}" method="POST" class="bg-light p-4 rounded shadow-sm">

            @csrf
            <div class="form-row mb-3">
                <div class="col">
                    <label for="purchase_amount" class="font-weight-bold">Purchase Amount:</label>
                    <input type="number" name="purchase_amount" value="{{ $totalPurchasePrice }}" readonly class="form-control">

                    <input type="hidden" name="purchase_amount" value="{{ $totalPurchasePrice }}">
                </div>

                <div class="col">
                    <label for="shipping_cost_inr" class="font-weight-bold">Shipping (INR):</label>
                    <input type="number" id="shipping_cost_inr" name="shipping_cost_inr" value="{{ old('shipping_cost_inr', $existingDetails->shipping_cost_inr ?? '') }}" required class="form-control">
                </div>

                <div class="col">
                    <label for="conversion_rate" class="font-weight-bold">Conversion Rate:</label>
                    <input type="number" step="0.01" id="conversion_rate" name="conversion_rate" value="{{ old('conversion_rate', $existingDetails->conversion_rate ?? '') }}" required class="form-control">
                </div>
            </div>

            <div class="form-row mb-3">
                <div class="col">
                    <label for="shipping_cost_bdt" class="font-weight-bold">Shipping (BDT):</label>
                    <input type="number" id="shipping_cost_bdt" name="shipping_cost_bdt" value="{{ old('shipping_cost_bdt', $existingDetails->shipping_cost_bdt ?? '') }}" required class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-warning mt-3">Submit</button>
        </form>


        <div class="mt-4">
            <a href="" class="btn btn-primary">Export to Excel</a>
        </div>

        @if ($groupedReqOrderItems->isEmpty())
            <div class="alert alert-warning text-center mt-4">
                <strong>No items attached to this shipment.</strong>
            </div>
        @else
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="bg-warning text-white">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Total</th>
                        <th>Order Status</th>
                        <th>Items</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($reqOrders as $index => $reqOrder)
                        @php
                            $items = $groupedReqOrderItems[$reqOrder->id] ?? [];
                        @endphp
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-light' }}">
                            <td class="text-center font-weight-bold text-dark">{{ $reqOrder->order_no }}</td>
                            <td class="text-center font-weight-bold text-dark">
                                {{ number_format($items->sum(fn($item) => $item->quantity * $item->price_bdt), 2) }}
                            </td>
                            <td class="text-center font-weight-bold text-dark">{{ $reqOrder->status ?? 'Pending' }}</td>
                            <td>
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Purchase Price</th>
                                        <th>Quantity</th>
                                        <th>Price (BDT)</th>
                                        <th>Total (BDT)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($items as $item)
                                        <tr class="bg-light">
                                            <td class="text-center">
                                                @if($item->image)
                                                    <img src="{{ asset($item->image) }}" width="50" height="50" class="rounded shadow">
                                                @else
                                                    No Image
                                                @endif
                                            </td>
                                            <td class="text-dark">{{ $item->product_name }}</td>
                                            <td class="text-center text-dark">{{ number_format($item->purchase_amount, 2) }}</td>
                                            <td class="text-center text-dark">{{ $item->quantity }}</td>
                                            <td class="text-center text-dark">{{ number_format($item->price_bdt, 2) }}</td>
                                            <td class="text-center font-weight-bold text-dark">
                                                {{ number_format($item->quantity * $item->price_bdt, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $reqOrders->links() }}
            </div>
        @endif
    </div>
@endsection
