@extends('backend.layouts.app')

@section('content')

    <div class="card shadow-none rounded-0 border">
        <div class="row">
            <div class="col-md-4 col-12 card-header border-bottom-0">
                @can('add_req_orders')
                <a href="{{ route('backend.req.order.create') }}" class="btn btn-outline-success mb-2 fs-20 fw-700 text-dark">
                    {{ translate('Create Order') }}
                </a>
                @endcan
            </div>
            <div class="col-md-6 col-12 card-header border-bottom-0">
                <h3 class="mb-2 fs-20 fw-700 text-dark">{{ translate('Request Order History') }}</h3>
            </div>
        </div>

        <div class="card-body pb-0">
            <form method="GET" action="{{ route('backend.req.order.index') }}">
                <div class="row align-items-center g-2">
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ translate('Search Order No') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <select class="form-control form-control-sm" name="order_status">
                            <option value="">{{ translate('All Orders') }}</option>
                            <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipfromkol" {{ request('order_status') == 'shipfromkol' ? 'selected' : '' }}>Kolkata Warehouse</option>
                            <option value="shipfromdhk" {{ request('order_status') == 'shipfromdhk' ? 'selected' : '' }}>Dhaka Warehouse</option>
                            <option value="transit" {{ request('order_status') == 'transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="canceled" {{ request('order_status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
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

                                <div class="col-md-3 col-12 text-right">
                                    <a href="{{ route('backend.req.order.address.edit', $order->id) }}" class="btn btn-soft-warning btn-icon btn-circle btn-sm">
                                        <i class="las la-address-book"></i>
                                    </a>
                                    <a href="{{ route('backend.req.order.edit', $order->id) }}" class="btn btn-soft-warning btn-icon btn-circle btn-sm">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="{{ route('backend.req.order.show', $order->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.req-orders.invoice.download', $order->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                        <i class="las la-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 col-12">
                                    <form method="POST" action="{{ route('backend.req.order.update_status', $order->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="order_status" class="form-control form-control-sm">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="shipfromkol" {{ $order->status == 'shipfromkol' ? 'selected' : '' }}>Kolkata Warehouse</option>
                                            <option value="shipfromdhk" {{ $order->status == 'shipfromdhk' ? 'selected' : '' }}>Dhaka Warehouse</option>
                                            <option value="transit" {{ $order->status == 'transit' ? 'selected' : '' }}>In Transit</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update</button>
                                    </form>
                                </div>
                                <div class="col-md-3 col-12">
                                    <h6>Payment Status:
                                        @if(strtolower($order->pay_status) == 'paid')
                                            <span class="text-success font-weight-bold">{{ strtoupper($order->pay_status) }}</span>
                                        @else
                                            <span class="text-warning">{{ ucfirst($order->pay_status) }}</span>
                                        @endif
                                    </h6>
                                </div>

                                <div class="col-md-3 col-12">
                                    <h6 class="fw-bold">
                                        {{ $order->paid_amount > 0 ? translate('Due Amount:') : translate('Total Amount:') }}
                                    </h6>
                                    @php
                                        $totalAfterDiscount = $order->items->sum(function($item) {
                                            return ($item->quantity * $item->price_bdt) - $item->coupon_discount;
                                        });
                                    @endphp
                                    <p class="fs-16 fw-bold">
                                        {{ number_format($totalAfterDiscount) }} BDT
                                    </p>
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
                                            <th>{{ translate('Discount') }}</th>
                                            <th>{{ translate('Paid') }}</th>
                                            <th>{{ translate('Due') }}</th>
                                            <th>{{ translate('Brand') }}</th>
                                            <th>{{ translate('Status') }}</th>
                                            <th class="text-center">{{ translate('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($order->items as $item)

                                            <tr>
                                                <td>{{ $item->product_name }}</td>
                                                <td>
                                                    @php
                                                        // Ensure that $item->files is an array or object and handle if no files are present
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
                                                <td>{{ $item->quantity*$item->price_bdt }}</td>
                                                <td>{{ $item->coupon_discount }}</td>
                                                <td>{{ $item->paid_amount }}</td>
                                                <td>{{ $item->due }}</td>
                                                <td>{{ $item->brand ? $item->brand->name : '-' }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('backend.req.order_item.update_status', $item->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="item_status" class="form-control form-control-sm">
                                                           <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="processing" {{ $item->status == 'processing' ? 'selected' : '' }}>Confirmed</option>
                                                            <option value="shipfromkol" {{ $item->status == 'shipfromkol' ? 'selected' : '' }}>Kolkata Warehouse</option>
                                                            <option value="shipfromdhk" {{ $item->status == 'shipfromdhk' ? 'selected' : '' }}>Dhaka Warehouse</option>
                                                            <option value="transit" {{ $item->status == 'transit' ? 'selected' : '' }}>In Transit</option>
                                                            <option value="completed" {{ $item->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="canceled" {{ $item->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-primary btn-sm mt-1">Update</button>
                                                    </form>
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('backend.req.order_item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item? ');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                                            <i class="la la-trash mr-2"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Discount Button -->
                                                    <button type="button" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="showDiscountModal({{ $item->id }})">
                                                        <i class="la la-percent mr-2"></i>
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="discountModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="discountModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="discountModalLabel">Apply Discount</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('backend.req.order_item.discount', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to discount this item?');">
                                                                        @csrf
                                                                        <input type="number" name="discount" class="form-control" placeholder="Enter discount amount" required>
                                                                        <button type="submit" class="btn btn-success btn-icon btn-circle btn-sm mt-3">
                                                                            <i class="la la-percent mr-2"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>




                                                @if($item->due > 0)
                                                    <button class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                                            onclick="openAdminPaymentModal({{ $item->id }}, {{ $item->due }}, {{ $item->id }})">
                                                        <i class="la la-credit-card"></i>
                                                    </button>
                                                    @endif


                                                    {{-- @if($item->due > 0)
                                                         <a href="{{ route('adminorder.payment.form', encrypt($item->id)) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                                             <i class="la la-credit-card mr-2"></i>
                                                         </a>
                                                     @endif--}}
                                                    <a href="{{ route('admin.req-orders.invoice.download', [$order->id, $item->id]) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                                        <i class="las la-download"></i>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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

    <!-- Payment Modal for Admin -->
    <div class="modal fade" id="adminPaymentModal" tabindex="-1" role="dialog" aria-labelledby="adminPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Make Payment (Admin)') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="adminPaymentForm" action="{{ route('admin.order.payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" id="admin_order_id">
                        <input type="hidden" name="item_id" id="admin_item_id">

                        <div class="form-group">
                            <label for="admin_amount">{{ translate('Amount') }}</label>
                            <input type="number" name="amount" id="admin_amount" class="form-control" required>

                        </div>

                        <div class="form-group">
                            <label for="admin_payment_method">{{ translate('Select Payment Method') }}</label>
                            <select name="payment_method" id="admin_payment_method" class="form-control" required>
                                <option value="">{{ translate('Choose Bank or SSL') }}</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                                <option value="ssl">{{ translate('SSL Payment Gateway') }}</option>
                            </select>
                        </div>

                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ translate('Confirm Payment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@section('script')
    <script>
        function openAdminPaymentModal(orderId, dueAmount,itemId) {
            $('#admin_order_id').val(orderId);
            $('#admin_item_id').val(itemId);// Set the order ID
            $('#admin_amount').val(dueAmount); // Set the due amount
            $('#adminPaymentModal').modal('show');
        }
    </script>

            <script>
                function showDiscountModal(itemId) {
                    $('#discountModal-' + itemId).modal('show');
                }
            </script>


@endsection

@endsection
