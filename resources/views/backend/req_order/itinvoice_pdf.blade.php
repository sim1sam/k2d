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
            font-family: '{{ $font_family }}';
            font-weight: normal;
            direction: {{ $direction }};
            text-align: {{ $text_align }};
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
            text-align: {{ $text_align }};
        }
        .text-right{
            text-align: {{ $not_text_align }};
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
                <td class="text-right small"><span class="gry-color small">{{ translate('Order Date') }}:</span> <span class="strong">{{ date('d-m-Y', $reqorder->date) }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small">
                    <span class="gry-color small">{{ translate('Payment Due') }}:</span>
                    <span class="strong">{{ $orderDetail->due }}</span>
                </td>
            </tr>

            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small">
                    <span class="gry-color small">{{ translate('Payment Status') }}:</span>
                    <span class="strong">
        {{ $orderDetail->payment_status ?? 'Unpaid' }}
    </span>
                </td>
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

    <!-- Displaying items conditionally: -->
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
            @if(isset($orderDetail)) <!-- If item-specific invoice, show just one item -->
            <tr>
                <td>{{ $orderDetail->product_name }}</td>
                <td class="">{{ $orderDetail->quantity }}</td>
                <td class="currency">BDT {{ ($orderDetail->price_bdt ) }}</td>
                <td class="text-right currency">BDT {{ ($orderDetail->price_bdt * $orderDetail->quantity) }}</td>
            </tr>
            @else <!-- If order-wise invoice, show all items -->
            @foreach ($reqorder->items as $key => $orderDetail)
                <tr>
                    <td>{{ $orderDetail->product_name }}</td>
                    <td class="">{{ $orderDetail->quantity }}</td>
                    <td class="currency">BDT {{ ($orderDetail->price_bdt ) }}</td>
                    <td class="text-right currency">BDT {{ ($orderDetail->price_bdt * $orderDetail->quantity) }}</td>
                </tr>
            @endforeach
            @endif
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
                            <td class="currency">{{ ($orderDetail->price_bdt * $orderDetail->quantity) }}</td> <!-- Total sum of all item subtotals -->

                        </tr>
                        <tr class="border-bottom">
                            <th class="gry-color text-left">{{ translate('Coupon Discount') }}</th>
                            <td class="currency">{{ number_format($orderDetail->coupon_discount, 2) }}</td>
                        </tr>


                            <tr>
                                <th class="text-left strong">{{ translate('Grand Total') }}</th>
                                <td class="currency">BDT {{ number_format($orderDetail->price_bdt * $orderDetail->quantity - ($orderDetail->coupon_discount ?? 0), 2) }}</td>
                            </tr>
                        
                        <!-- Payment Breakdown Section -->
                        <tr>
                            <th colspan="2" class="text-left strong" style="padding-top: 10px; border-bottom: 1px solid #ccc;">{{ translate('Payment Breakdown') }}</th>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0;">
                                <table class="text-left small" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">{{ translate('Date') }}</th>
                                            <th style="width: 25%;">{{ translate('Payment Method') }}</th>
                                            <th style="width: 25%;">{{ translate('Status') }}</th>
                                            <th style="width: 25%; text-align: right;">{{ translate('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($orderDetail->paid_amount > 0)
                                            <tr>
                                                <td>{{ $orderDetail->updated_at->format('d M Y, h:i A') }}</td>
                                                <td>{{ $orderDetail->payment_method ?? translate('N/A') }}</td>
                                                <td>{{ $orderDetail->payment_status ?? translate('N/A') }}</td>
                                                <td class="text-right">BDT {{ number_format($orderDetail->paid_amount, 2) }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">{{ translate('No payment records found') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr style="border-top: 1px solid #ccc;">
                                            <th colspan="3" class="text-left">{{ translate('Total Paid') }}</th>
                                            <td class="text-right">BDT {{ number_format($orderDetail->paid_amount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-left">{{ translate('Item Discount') }}</th>
                                            <td class="text-right">BDT {{ number_format($orderDetail->coupon_discount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-left strong">{{ translate('Due Amount') }}</th>
                                            <td class="text-right strong">BDT {{ number_format($orderDetail->due ?? ($orderDetail->price_bdt * $orderDetail->quantity - ($orderDetail->coupon_discount ?? 0) - ($orderDetail->paid_amount ?? 0)), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Total Amount in Words -->
                        <tr>
                            <td colspan="2" style="padding-top: 15px; border-top: 1px dashed #ccc;">
                                @php
                                    function convertNumberToWord($num = false)
                                    {
                                        $num = str_replace(array(',', ' '), '', trim($num));
                                        if (!$num) {
                                            return false;
                                        }
                                        
                                        $num = (int) $num;
                                        $words = array();
                                        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
                                            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
                                        );
                                        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
                                        $list3 = array('', 'thousand', 'million', 'billion', 'trillion');
                                        
                                        $num_length = strlen($num);
                                        $levels = (int) (($num_length + 2) / 3);
                                        $max_length = $levels * 3;
                                        $num = substr('00' . $num, -$max_length);
                                        $num_levels = str_split($num, 3);
                                        
                                        for ($i = 0; $i < count($num_levels); $i++) {
                                            $levels--;
                                            $hundreds = (int) ($num_levels[$i] / 100);
                                            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
                                            $tens = (int) ($num_levels[$i] % 100);
                                            $singles = '';
                                            
                                            if ($tens < 20) {
                                                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                                            } else {
                                                $tens = (int) ($tens / 10);
                                                $tens = ' ' . $list2[$tens] . ' ';
                                                $singles = (int) ($num_levels[$i] % 10);
                                                $singles = ' ' . $list1[$singles] . ' ';
                                            }
                                            $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
                                        }
                                        
                                        $commas = count($words);
                                        if ($commas > 1) {
                                            $commas = $commas - 1;
                                        }
                                        
                                        $words = implode(' ', $words);
                                        $words = preg_replace('/\s+/', ' ', $words);
                                        return ucwords(trim($words));
                                    }
                                    
                                    $totalAmount = $orderDetail->price_bdt * $orderDetail->quantity - ($orderDetail->coupon_discount ?? 0);
                                    $amountInWords = convertNumberToWord($totalAmount);
                                @endphp
                                <p style="margin: 0; font-weight: bold;">{{ translate('In Words') }}: {{ $amountInWords }} {{ translate('Taka Only') }}</p>
                            </td>
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
