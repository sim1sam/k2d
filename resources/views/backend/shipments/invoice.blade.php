<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $shipment->name }} - {{ $customer->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #ff6c00;
            padding-bottom: 15px;
        }
        .header img {
            max-width: 80px; /* Reduced logo size */
            margin-bottom: 10px;
        }
        .header h2 {
            color: #ff6c00;
            font-size: 28px;
            font-weight: bold;
            margin: 5px 0;
        }
        .invoice-details {
            padding: 20px 0;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }
        .invoice-details div {
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }
        .invoice-details strong {
            color: #ff6c00;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 15px;
        }
        table th {
            background: #ff6c00;
            color: #ffffff;
            text-transform: uppercase;
        }
        .total-section {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            padding-top: 15px;
            color: #333;
        }
        .total-section p {
            margin: 5px 0;
        }
        .grand-total {
            color: #ff6c00;
            font-size: 22px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #555;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('public/uploads/all/logo.png') }}" alt="Company Logo">
        <h2>Invoice</h2>
        <p>{{ $shipment->name }} - {{ $customer->name }}</p>
    </div>

    <div class="invoice-details">
        <div><strong>Invoice Date:</strong> {{ \Carbon\Carbon::now()->format('d M, Y') }}</div>
        <div><strong>Customer Name:</strong> {{ $customer->name }}</div>
        <div><strong>Address:</strong> {{ $address }}, {{ $city }}, {{ $state }} - {{ $postalCode }}, {{ $country }}</div>
        <div><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</div>
        <div><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</div>

    </div>

    <table>
        <thead>
        <tr>
            <th>Order No</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Due</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalAmountBeforeDiscount = 0;
            $totalDiscount = 0;
            $totalDue = 0;
        @endphp
        @foreach ($orders as $order)
            @foreach ($order->items as $item)
                <tr>
                    @if ($loop->first)
                        <td rowspan="{{ $order->items->count() }}">{{ $order->order_no }}</td>
                    @endif
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>BDT {{ number_format($item->price_bdt, 2) }}</td>
                    <td>BDT {{ number_format($item->coupon_discount, 2) }}</td>
                    <td>BDT {{ number_format(($item->quantity * $item->price_bdt) - $item->coupon_discount, 2) }}</td>
                    <td>BDT {{ number_format($item->due, 2) }}</td>
                </tr>
                @php
                    $totalAmountBeforeDiscount += $item->quantity * $item->price_bdt;
                    $totalDiscount += $item->coupon_discount;
                    $totalDue += $item->due;
                @endphp
            @endforeach
        @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p><strong>Total Amount:</strong> BDT {{ number_format($totalAmountBeforeDiscount, 2) }}</p>
        <p><strong>Discount:</strong> BDT {{ number_format($totalDiscount, 2) }}</p>
        <p class="grand-total"><strong>Grand Total:</strong> BDT {{ number_format($totalAmountBeforeDiscount - $totalDiscount, 2) }}</p>
        <p><strong>Total Due:</strong> BDT {{ number_format($totalDue, 2) }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions, contact us at <strong>support@company.com</strong></p>
        <p>Company Address | Phone: +123 456 7890</p>
    </div>
</div>
</body>
</html>
