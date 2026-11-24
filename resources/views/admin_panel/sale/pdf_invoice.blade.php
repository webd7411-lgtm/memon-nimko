<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #128c7e;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #128c7e;
            margin: 0;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-align: right;
            margin: 0;
        }
        .invoice-details {
            font-size: 10px;
            color: #555;
            text-align: right;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .meta-title {
            font-weight: bold;
            color: #128c7e;
            font-size: 11px;
            margin-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #128c7e;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #128c7e;
            text-align: left;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) {
            background-color: #fbfcfd;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }

        .summary-wrapper {
            width: 100%;
            margin-top: 10px;
        }
        .summary-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .total-row {
            background-color: #e6f4f1;
            font-weight: bold;
            font-size: 13px !important;
            color: #075e54;
        }
        .total-row td {
            border-top: 2px solid #128c7e;
            border-bottom: 2px solid #128c7e;
        }
        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <h1 class="company-title">{{ $branch && $branch->name && !in_array(strtolower($branch->name), ['default', 'main branch']) ? $branch->name : shop_name() }}</h1>
                <div class="company-info">{{ $branch->address ?? 'Main Market, Pakistan' }}</div>
                <div class="company-info">Phone: {{ $branch->phone ?? '0300-0000000' }}</div>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <h2 class="invoice-title">SALE INVOICE</h2>
                <div class="invoice-details"><b>Invoice #:</b> {{ $sale->invoice_no }}</div>
                <div class="invoice-details"><b>Date:</b> {{ $sale->created_at ? $sale->created_at->format('d-M-Y h:i A') : date('d-M-Y h:i A') }}</div>
                <div class="invoice-details"><b>Cashier:</b> {{ $sale->user->name ?? 'Admin' }}</div>
            </td>
        </tr>
    </table>

    {{-- CUSTOMER DETAILS --}}
    <table class="meta-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="meta-title">BILL TO (CUSTOMER)</div>
                <div><b>Name:</b> {{ $sale->customer_display_name ?: 'Walk-in Customer' }}</div>
                @if($sale->customer_relation)
                    <div><b>Phone:</b> {{ $sale->customer_relation->mobile ?: ($sale->customer_relation->phone ?: '-') }}</div>
                    @if($sale->customer_relation->address)
                        <div><b>Address:</b> {{ $sale->customer_relation->address }}</div>
                    @endif
                @endif
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="meta-title">ORDER INFORMATION</div>
                <div><b>Payment Method:</b> 
                    {{ ((float)$sale->card > 0 && (float)$sale->cash > 0) ? 'Split (Cash + Card)' : (((float)$sale->card > 0) ? 'Bank Card' : 'Cash') }}
                </div>
                <div><b>Status:</b> Completed</div>
            </td>
        </tr>
    </table>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 45%;">Item Description</th>
                <th style="width: 10%;" class="text-center">Unit</th>
                <th style="width: 12%;" class="text-right">Price</th>
                <th style="width: 8%;" class="text-center">Qty</th>
                <th style="width: 10%;" class="text-right">Disc</th>
                <th style="width: 12%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <b>{{ $item['name'] }}</b>
                        @if(!empty($item['variant']))
                            <span style="font-size: 9px; color: #666;">({{ $item['variant'] }})</span>
                        @endif
                        @if(!empty($item['code']))
                            <div style="font-size: 8px; color: #888;">Code: {{ $item['code'] }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['unit'] ?: '-' }}</td>
                    <td class="text-right">Rs. {{ number_format((float)$item['price'], 2) }}</td>
                    <td class="text-center">{{ $item['qty'] }}</td>
                    <td class="text-right">{{ (float)$item['discount'] > 0 ? 'Rs. ' . number_format((float)$item['discount'], 2) : '-' }}</td>
                    <td class="text-right fw-bold">Rs. {{ number_format((float)$item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS SUMMARY --}}
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td class="text-left">Gross Total:</td>
                <td class="text-right">Rs. {{ number_format((float)($sale->total_bill_amount ?? $sale->total_net ?? 0), 2) }}</td>
            </tr>
            @if((float)($sale->total_extradiscount ?? 0) > 0)
                <tr>
                    <td class="text-left">Extra Discount:</td>
                    <td class="text-right" style="color: #c00;">- Rs. {{ number_format((float)$sale->total_extradiscount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td class="text-left">Net Amount:</td>
                <td class="text-right">Rs. {{ number_format((float)($sale->total_net ?? $sale->total_bill_amount ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="text-left">Paid Amount:</td>
                <td class="text-right">Rs. {{ number_format((float)($sale->cash ?? 0) + (float)($sale->card ?? 0), 2) }}</td>
            </tr>
            @if((float)($sale->change ?? 0) > 0)
                <tr>
                    <td class="text-left">Change / Return:</td>
                    <td class="text-right">Rs. {{ number_format((float)$sale->change, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div>Thank you for choosing <b>{{ $branch && $branch->name && !in_array(strtolower($branch->name), ['default', 'main branch']) ? $branch->name : shop_name() }}</b>!</div>
        <div>Software Developed by ProWave Technologies</div>
        <div><i>This is a computer-generated invoice and requires no physical signature.</i></div>
    </div>

</body>
</html>
