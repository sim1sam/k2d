@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card shadow-none rounded-0 border">
        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-20 fw-700 text-dark">{{ translate('Request Order History') }}</h5>

            <!-- Order Number Search Form -->
            <form method="GET" action="{{ route('frontend.order.index') }}" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ translate('Search Order No') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table aiz-table mb-0">
                    <thead class="text-gray fs-12">
                    <tr>
                        <th class="pl-0">{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Date') }}</th>
                        <th>{{ translate('Total Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment') }}</th>
                        <th class="text-right pr-0">{{ translate('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="fs-14">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="pl-0">
                                    <a href="{{ route('frontend.order.show', encrypt($order->id)) }}">
                                        {{ $order->order_no }}
                                    </a>
                                </td>
                                <td class="text-secondary">{{ date('d-m-Y', strtotime($order->created_at)) }}</td>


                            <td class="fw-700">
                                {{ single_price($order->calculated_total) }}
                            </td>

                                <td class="fw-700">
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->status))) }}
                                    @if ($order->status_viewed == 0)
                                        <span class="ml-2 text-success"><strong>*</strong></span>
                                    @endif
                                </td>
                                <td class="fw-700">
                                    <span class="{{ $order->pay_status == 'paid' ? 'text-success' : 'text-danger' }}">
                                        {{ translate(ucfirst($order->pay_status)) }}
                                    </span>
                                 </td>


                                <td class="text-right pr-0">


                                    <a href="{{ route('frontend.order.show', encrypt($order->id)) }}"
                                       class="btn btn-soft-info btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0"
                                       title="{{ translate('View Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <!-- Pagination -->
            <div class="aiz-pagination mt-2">
                {{ $orders->links() }}
            </div>
        </div>
    </div>



@endsection
