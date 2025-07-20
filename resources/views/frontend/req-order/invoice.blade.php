<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style media="all">
        @page {
            margin: 0;
            padding: 0;
        }
        body{
            font-size: 0.875rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: normal;
            direction: ltr;
            text-align: center;
            padding: 0;
            margin: 0;
        }
        .gry-color *,
        .gry-color{
            color: #000;
        }
        table{
            width: 100%;
        }
        table th{
            font-weight: normal;
        }
        table.padding th,
        table.padding td {
            padding: .25rem .7rem;
        }
        table.sm-padding td{
            padding: .1rem .7rem;
        }
        .border-bottom td,
        .border-bottom th{
            border-bottom: 1px solid #eceff4;
        }
        .text-left{
            text-align: left;
        }
        .text-right{
            text-align: right;
        }
        .strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div>

    @php
        $logo = get_setting('header_logo');
    @endphp

    <div style="background: #eceff4; padding: 1rem;">
        <table>
            <tr>
                <td>
                    @if($logo != null)
                        <img src="{{ uploaded_asset($logo) }}" height="30" style="display:inline-block;">
                    @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" height="30" style="display:inline-block;">
                    @endif
                </td>
                <td style="font-size: 1.5rem;" class="text-right strong">{{ translate('Invoice') }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="font-size: 1rem;" class="strong">{{ get_setting('site_name') }}</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td class="gry-color small">{{ get_setting('contact_address') }}</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('Email') }}: {{ get_setting('contact_email') }}</td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Order ID') }}:</span> <span class="strong">{{ $reqorder->order_no }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Order Date') }}:</span> <span class="strong">{{ date('d-m-Y', strtotime($reqorder->created_at)) }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small">
                    <span class="gry-color small">{{ translate('Payment method') }}:</span>
                    @php
                        $paymentMethod = $reqorder->payment_method ?? 'cod'; // Fallback to 'cod' if payment_method is null
                    @endphp
                    <span class="strong">
                        {{ translate(ucfirst(str_replace('_', ' ', $paymentMethod))) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Payment Status') }}:</span> <span class="strong">{{ $reqorder->pay_status }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Due') }}:</span> <span class="strong">BDT {{ ($reqorder->items->sum('due')) }}</span></td>
            </tr>
        </table>
    </div>

    <div style="padding: 1rem; padding-bottom: 0;">
        <table>
            <tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>
            <tr><td class="strong">{{ $reqorder->customer->name }}</td></tr>
            <tr><td class="gry-color small">
                    {{ $reqorder->address }}, {{ $reqorder->city }},
                    @if(isset($reqorder->state)) {{ $reqorder->state }}, @endif
                    {{ $reqorder->country }}
                </td></tr>
            <tr><td class="gry-color small">{{ translate('Email') }}: {{ $reqorder->customer->email }}</td></tr>
            <tr><td class="gry-color small">{{ translate('Phone') }}: {{ $reqorder->phone }}</td></tr>
        </table>
    </div>

    <div style="padding: 1rem;">
        <table class="padding text-left small border-bottom">
            <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="35%" class="text-left">{{ translate('Product Name') }}</th>
                <th width="10%" class="text-left">{{ translate('Qty') }}</th>
                <th width="15%" class="text-left">{{ translate('Unit Price') }}</th>
                <th width="15%" class="text-right">{{ translate('Total') }}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($reqorder->items as $key => $orderDetail)
                <tr>
                    <td>{{ $orderDetail->product_name }}</td>
                    <td>{{ $orderDetail->quantity }}</td>
                    <td class="currency">BDT {{ number_format($orderDetail->price_bdt, 2) }}</td>
                    <td class="text-right currency">BDT {{ number_format($orderDetail->price_bdt * $orderDetail->quantity, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="padding: 0 1.5rem;">
        <table class="text-right sm-padding small strong">
            <thead>
            <tr>
                <th width="60%"></th>
                <th width="40%"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-left">
                    @php
                        $qrCodeText = $reqorder->order_no ?? 'fallback_value'; // Provide a fallback if it's null
                    @endphp
                    {!! str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(100)->generate($qrCodeText)) !!}
                </td>
                <td>
                    <table class="text-right sm-padding small strong">
                        <tbody>
                        <tr>
                            <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
                            <td class="currency">BDT {{ number_format($reqorder->total, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="gry-color text-left">{{ translate('Shipping Cost') }}</th>
                            <td class="currency">BDT {{ number_format($reqorder->delivery_charge, 2) }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <th class="gry-color text-left">{{ translate('Coupon Discount') }}</th>
                            <td class="currency">BDT {{ number_format($reqorder->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="text-left strong">{{ translate('Grand Total') }}</th>
                            <td class="currency">BDT {{ number_format($reqorder->total - $reqorder->discount, 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
