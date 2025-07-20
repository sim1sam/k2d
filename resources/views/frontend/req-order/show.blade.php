@extends('frontend.layouts.user_panel')

@section('panel_content')

    <!-- Order ID Header -->
    <div class="aiz-titlebar mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fs-20 fw-700 text-dark">{{ translate('Order ID') }}: {{ $reqOrder->order_no }}</h1>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="card rounded-0 shadow-none border mb-4">
        <div class="card-header border-bottom-0">
            <h5 class="fs-16 fw-700 text-dark mb-0">{{ translate('Order Summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table-borderless table">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Code') }}:</td>
                            <td>{{ $reqOrder->order_no }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Customer') }}:</td>
                            <td>{{ $reqOrder->customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Mobile Number') }}:</td>
                            <td>{{ $reqOrder->phone }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Delivery Address') }}:</td>
                            <td>{{ $reqOrder->address }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Discount') }}:</td>
                            <td>{{ single_price($reqOrder->discount) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table-borderless table">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Date') }}:</td>
                            <td>{{ date('d-m-Y H:i A', strtotime($reqOrder->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Status') }}:</td>
                            <td>{{ ucfirst($reqOrder->status) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment Status') }}:</td>
                            <td>{{ ucfirst($reqOrder->pay_status) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total Order Amount') }}:</td>
                            <td>{{ single_price($reqOrder->total) }}</td>
                        </tr>

                        <tr>
                            <td class="w-50 fw-600">{{ translate('Due Amount') }}:</td>
                            <td> {{ single_price($reqOrder->items->where('pay_status', '!=', 'paid')->sum('due')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Button for the entire Order -->
    <div class="text-right mb-4">
        <a href="{{ route('invoices.download', encrypt($reqOrder->id)) }}" class="btn btn-sm btn-secondary">
            {{ translate('Download Invoice') }}
        </a>
    </div>

    <!-- Order Details (Table for Desktop) -->
    <div class="d-none d-md-block">
        <div class="card rounded-0 shadow-none border mt-2 mb-4">
            <div class="card-header border-bottom-0">
                <h5 class="fs-16 fw-700 text-dark mb-0">{{ translate('Order Details') }}</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table aiz-table">
                    <thead class="text-gray fs-12">
                    <tr>
                        <th>#</th>
                        <th width="20%">{{ translate('Product') }}</th>
                        <th>{{ translate('Size') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('Unit Price') }}</th>
                        <th>{{ translate('Total Price') }}</th>
                        <th>{{ translate('Paid Amount') }}</th>
                        <th>{{ translate('Due Amount') }}</th>
                        <th>{{ translate('Images') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody class="fs-14">
                    @foreach ($reqOrder->items as $key => $item)
                        @php $itemTotal = $item->quantity * $item->price_bdt; @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->size }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ single_price($item->price_bdt) }}</td>
                            <td>{{ single_price($itemTotal) }}</td>
                            <td>{{ single_price($item->paid_amount) }}</td>
                            <td>{{ single_price($item->due) }}</td>
                            <td>
                                @if($item->files->count() > 0)
                                    <div class="item-images">
                                        @foreach($item->files as $file)
                                            @php
                                                $fileExtension = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $fullPath = $file->file_path;
                                            @endphp
                                            
                                            <!-- Debug info -->
                                            @php
                                                // Use the same approach as admin panel
                                                $adminStylePath = asset('public/k2d/uploads/reqorder/' . basename($file->file_path));
                                            @endphp
                                            
                                            @if($isImage)
                                                <a href="{{ $adminStylePath }}" target="_blank">
                                                    <img src="{{ $adminStylePath }}" alt="Order Item Image" class="img-fluid mb-1" style="max-height: 50px; max-width: 50px; margin-right: 5px;">
                                                </a>
                                            @else
                                                <a href="{{ $adminStylePath }}" target="_blank" class="btn btn-sm btn-secondary mb-1">
                                                    <i class="las la-file-pdf"></i> {{ translate('View File') }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">{{ translate('No files') }}</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>
                                @if($item->due > 0 && $item->pay_status != 'paid')
                                    <a href="{{ route('order.payment.form', encrypt($item->id)) }}" class="btn btn-sm btn-primary">
                                        {{ translate('Pay Now') }}
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-success" disabled>{{ translate('Paid') }}</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile View (Accordion) -->
    <div class="d-block d-md-none">
        @foreach($reqOrder->items as $key => $item)
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between" onclick="toggleAccordion({{ $key }})">
                    <span>{{ $item->product_name }} | {{ ucfirst($item->status) }}</span>
                    <span id="icon-{{ $key }}">+</span>
                </div>
                <div class="card-body d-none" id="details-{{ $key }}">
                    <p><strong>Size:</strong> {{ $item->size }}</p>
                    <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
                    <p><strong>Unit Price:</strong> {{ single_price($item->price_bdt) }}</p>
                    <p><strong>Total Price:</strong> {{ single_price($item->quantity * $item->price_bdt) }}</p>
                    <p><strong>Paid Amount:</strong> {{ single_price($item->paid_amount) }}</p>
                    <p><strong>Due Amount:</strong> {{ single_price($item->due) }}</p>
                    
                    @if($item->files->count() > 0)
                        <p><strong>{{ translate('Images') }}:</strong></p>
                        <div class="item-images mb-3">
                            @foreach($item->files as $file)
                                @php
                                    $fileExtension = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $fullPath = $file->file_path;
                                @endphp
                                
                                <!-- Debug info -->
                                @php
                                    // Use the same approach as admin panel
                                    $adminStylePath = asset('public/k2d/uploads/reqorder/' . basename($file->file_path));
                                @endphp
                                
                                @if($isImage)
                                    <a href="{{ $adminStylePath }}" target="_blank">
                                        <img src="{{ $adminStylePath }}" alt="Order Item Image" class="img-fluid mb-2" style="max-height: 100px; max-width: 100px; margin-right: 5px;">
                                    </a>
                                @else
                                    <a href="{{ $adminStylePath }}" target="_blank" class="btn btn-sm btn-secondary mb-2">
                                        <i class="las la-file-pdf"></i> {{ translate('View File') }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                    @if($item->due > 0 && $item->pay_status != 'paid')
                        <a href="{{ route('order.payment.form', encrypt($item->id)) }}" class="btn btn-sm btn-primary">
                            {{ translate('Pay Now') }}
                        </a>
                    @else
                        <button class="btn btn-sm btn-success" disabled>{{ translate('Paid') }}</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function toggleAccordion(key) {
            let details = document.getElementById(`details-${key}`);
            let icon = document.getElementById(`icon-${key}`);
            details.classList.toggle('d-none');
            icon.textContent = icon.textContent === '+' ? '-' : '+';
        }
    </script>
    
    <style>
        .item-images {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .item-images img:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
    </style>
@endsection
