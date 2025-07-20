@extends('backend.layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-custom-orange text-white">
                <h5 class="mb-0">Order Details - {{ $order->order_no }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Customer Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Customer Name:</label>
                            <p>{{ $order->customer->name }}</p>
                        </div>
                    </div>

                    <!-- Delivery Charge -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Delivery Charge (BDT):</label>
                            <p>{{ single_price($order->delivery_charge) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div id="orderItems">
                    @php
                        $totalAmount = 0;
                    @endphp
                    @foreach ($order->items as $index => $item)
                        <div class="order-item border p-3 mb-3 rounded">
                            <h6 class="text-custom-orange font-weight-bold">Item {{ $index + 1 }}</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Product Name:</label>
                                        <p>{{ $item->product_name }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Product Link:</label>
                                        <p><a href="{{ $item->product_link }}" target="_blank">{{ $item->product_link }}</a></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Size:</label>
                                        <p>{{ $item->size }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Quantity:</label>
                                        <p>{{ $item->quantity }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Price (BDT):</label>
                                        <p>{{ single_price($item->price_bdt) }}</p>
                                    </div>
                                </div>
                            </div>
                            <!--total-->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Due (BDT):</label>
                                        <p>{{ $item->due }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Paid (BDT):</label>
                                        <p>{{ $item->paid_amount }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Total (BDT)</label>
                                        <p>{{ single_price($item->price_bdt*$item->quantity) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Discount (BDT):</label>
                                        <p>{{ $item->coupon_discount ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Purchase Bank:</label>
                                        <p>{{ $item->bank ? $item->bank->name : '-' }}</p>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Purchase Price (BDT):</label>
                                        <p>{{ single_price($item->purchase_amount) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Note:</label>
                                        <p>{{ $item->note ?? '-' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Vendor:</label>
                                        <p>{{ $item->vendor ? $item->vendor->name : '-' }}</p>
                                    </div>
                                </div>
                                <!-- Brand -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Brand:</label>
                                        <p>{{ $item->brand ? $item->brand->name : '-' }}</p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <!-- Shipment -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Shipment:</label>
                                        <p>{{ $item->shipment ? $item->shipment->name : '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Uploaded Images:</label>
                                        <div>
                                            @forelse ($item->files as $file)

                                          <a href="{{ asset('public/k2d/uploads/reqorder/' . basename($file->file_path)) }}" target="_blank">      <img src="{{ asset('public/k2d/uploads/reqorder/' . basename($file->file_path)) }}" alt="{{ $item->product_name }}" class="img-thumbnail" width="40">

                                          </a>

                                            @empty
                                                <img src="{{ asset('public/assets/img/placeholder.jpg') }}" class="img-thumbnail" width="80" alt="No image available">
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Download Invoice</label>
                                        <form action="" method="POST" >
                                            @csrf
                                            <button type="submit" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                                <i class="las la-download mr-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>


                            </div>

                            @php
                                $itemTotal = $item->quantity * $item->price_bdt;

       // Subtract the coupon discount from the item total
       $discountedAmount = $itemTotal - $item->coupon_discount;

       // Add the discounted amount to the total amount
       $totalAmount += $discountedAmount;
                            @endphp
                        </div>
                    @endforeach
                </div>

                <!-- Total Amount & Delivery Address -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Total Amount (BDT):</label>
                            <p class="text-success font-weight-bold">{{ single_price($order->total) }}</p>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Total Discount Amount (BDT):</label>
                            @php
                                // Calculate the total discount amount by summing up the coupon_discount of each item
                                $totalDiscountAmount = $order->items->sum('coupon_discount');
                            @endphp
                            <p class="text-success font-weight-bold">{{ single_price($totalDiscountAmount) }}</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Total Amount After Discount (BDT):</label>
                            <p class="text-success font-weight-bold">{{ single_price($totalAmount) }}</p>
                        </div>


                    </div>

                    <div class="col-md-6">
                        <div class="form-group border p-3 rounded">
                            <label class="font-weight-bold">Delivery Address:</label>
                            <p><strong>Address:</strong> {{ $order->address }}
                            <strong>State:</strong> {{ $order->state }}
                           <strong>City:</strong> {{ $order->city }}
                           <strong>Postal Code:</strong> {{ $order->postal_code }}
                            <strong>Country:</strong> {{ $order->country }}</p>
                            <p><strong>Phone:</strong> {{ $order->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('backend.req.order.index') }}" class="btn btn-custom-orange">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .bg-custom-orange {
            background-color: #FF5722 !important;
        }
        .text-custom-orange {
            color: #FF5722 !important;
        }
        .btn-custom-orange {
            background-color: #FF5722 !important;
            color: white !important;
            border: none;
        }
        .btn-custom-orange:hover {
            background-color: #E64A19 !important;
        }
        .border {
            border: 1px solid #ddd;
        }
    </style>
@endsection
