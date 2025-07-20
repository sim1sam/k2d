@extends('backend.layouts.app')

@section('content')
    <div class="card shadow-none rounded-0 border">
        <div class="row">
            <div class="col-md-6 col-12 card-header border-bottom-0">
                <h3 class="mb-2 fs-20 fw-700 text-dark">{{ translate('Request Order History') }}</h3>
            </div>
        </div>

        <div class="card-body pb-0">
            <form method="GET" action="{{ route('report-reqorder.create') }}">
                <div class="row align-items-center g-2">

                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <select class="form-control" name="customer_id">
                            <option value="">{{ translate('All Customers') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <select class="form-control form-control-sm" name="brand">
                            <option value="">{{ translate('All Brands') }}</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <select class="form-control" name="vendor">
                            <option value="">{{ translate('All Vendors') }}</option>
                            @foreach ($vendors as $vendorId => $vendorName)
                                <option value="{{ $vendorId }}" {{ request('vendor') == $vendorId ? 'selected' : '' }}>{{ $vendorName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="las la-search"></i> {{ translate('Filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @foreach ($orders as $order)
                <div class="order-block mb-3">
                    <div class="card shadow-none rounded-0 border mb-3">
                        <div class="card-body p-3 bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h5 class="fs-16 fw-700 text-dark">{{ translate('Order Code') }}: {{ $order->order_no }} <br> {{ $order->customer->name }}</h5>
                                </div>
                                <div class="col-md-2 col-12">
                                    <p class="fs-12 mb-0">{{ translate('Date') }}: {{ $order->created_at->format('d M, Y') }}</p>
                                </div>


                            </div>

                            <div class="order-items mt-3">
                                <h6 class="fs-16 fw-bold text-dark">{{ translate('Order Items') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered w-100">
                                        <thead>
                                        <tr>
                                            <th>{{ translate('Product Name') }}</th>
                                            <th>{{ translate('Image') }}</th>
                                            <th>{{ translate('Size') }}</th>
                                            <th>{{ translate('Quantity') }}</th>
                                            <th>{{ translate('Price') }}</th>
                                            <th>{{ translate('Total') }}</th>
                                            <th>{{ translate('Brand') }}</th>
                                            <th>{{ translate('Vendor') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product_name }}</td>
                                                <td>
                                                    @php
                                                        $firstFile = isset($item->files[0]) ? $item->files[0] : null;
                                                    @endphp
                                                    @if ($firstFile)
                                                        <a href="{{ asset('public/k2d/uploads/reqorder/' . basename($firstFile->file_path)) }}" target="_blank">
                                                            <img src="{{ asset('public/k2d/uploads/reqorder/' . basename($firstFile->file_path)) }}" alt="{{ $item->product_name }}" class="img-thumbnail" width="80">
                                                        </a>
                                                    @else
                                                        <img src="{{ asset('public/assets/img/placeholder.jpg') }}" class="img-thumbnail" width="80" alt="No image available">
                                                    @endif
                                                </td>
                                                <td>{{ $item->size ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->price_bdt }}</td>
                                                <td>{{ $item->quantity * $item->price_bdt }}</td>
                                                <td>{{ $item->brand ? $item->brand->name : '-' }}</td>
                                                <td>{{ $item->vendor ? $item->vendor->name : '-' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="aiz-pagination mt-2">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
