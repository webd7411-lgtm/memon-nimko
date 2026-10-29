<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Slip - {{ $booking->booking_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            margin: 0;
            padding: 15px;
            color: #000;
            background: #fff;
            font-size: 13px;
        }
        .ticket {
            max-width: 380px;
            margin: 0 auto;
            border: 1px dashed #444;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .info-row span:first-child {
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            padding: 5px 2px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 5px 2px;
            font-size: 12px;
        }
        .totals-table {
            width: 100%;
            margin-top: 8px;
            border-top: 1px dashed #000;
            padding-top: 6px;
        }
        .totals-table tr td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            font-size: 15px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 8px;
        }
        .btn-print {
            display: block;
            width: 100%;
            max-width: 380px;
            margin: 10px auto;
            padding: 10px;
            background: #0f766e;
            color: #fff;
            border: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border-radius: 6px;
        }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
            .ticket { border: none; padding: 0; }
        }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">🖨️ Print Booking Slip</button>

    <div class="ticket">
        <div class="header">
            <h2>{{ sys_setting('software_name', 'Memon Nimko') }}</h2>
            <p><strong>DISTRIBUTION DEMAND / BOOKING SLIP</strong></p>
            <p>{{ $distributor->name ?? 'Distributor Agency' }} ({{ $distributor->code }})</p>
            <p>Phone: {{ $distributor->phone ?? '-' }}</p>
        </div>

        <div class="info-row">
            <span>Booking #:</span>
            <strong>{{ $booking->booking_no }}</strong>
        </div>
        <div class="info-row">
            <span>Date:</span>
            <strong>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-M-Y') }}</strong>
        </div>
        <div class="info-row">
            <span>Booker:</span>
            <strong>{{ $booking->booker->name ?? 'Staff' }}</strong>
        </div>
        <div class="info-row">
            <span>Shop Name:</span>
            <strong>{{ $booking->customer->shop_name ?? 'Retail Shop' }}</strong>
        </div>
        <div class="info-row">
            <span>Owner / Phone:</span>
            <span>{{ $booking->customer->owner_name ?? '' }} ({{ $booking->customer->phone ?? '-' }})</span>
        </div>
        <div class="info-row">
            <span>Area / Addr:</span>
            <span>{{ $booking->customer->area ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span>Shop Balance:</span>
            <strong style="color: {{ ($booking->customer->current_balance ?? 0) > 0 ? '#b91c1c' : '#15803d' }};">
                Rs {{ number_format($booking->customer->current_balance ?? 0, 2) }}
            </strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Rate</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->items as $it)
                    <tr>
                        <td>
                            {{ $it->product->item_name ?? 'Item' }}
                            @if($it->variant)
                                <br><small>({{ $it->variant->size_label ?: $it->variant->variant_name }})</small>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ number_format($it->qty, 1) }}</td>
                        <td style="text-align: right;">{{ number_format($it->unit_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($it->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Total Items / Units:</td>
                <td>{{ $booking->total_items }} items / {{ number_format($booking->total_qty, 1) }} qty</td>
            </tr>
            <tr>
                <td>Subtotal:</td>
                <td>Rs {{ number_format($booking->subtotal, 2) }}</td>
            </tr>
            @if($booking->discount > 0)
            <tr>
                <td>Discount:</td>
                <td>- Rs {{ number_format($booking->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td><strong>NET ORDER TOTAL:</strong></td>
                <td>Rs {{ number_format($booking->grand_total, 2) }}</td>
            </tr>
        </table>

        @if(!empty($booking->notes))
        <div style="margin-top: 10px; font-size: 11px; background: #eee; padding: 6px; border-radius: 4px;">
            <strong>Notes:</strong> {{ $booking->notes }}
        </div>
        @endif

        <div class="footer">
            <p>*** DEMAND BOOKING SLIP (NOT A TAX INVOICE) ***</p>
            <p>Goods will be dispatched upon distributor review and delivery approval.</p>
            <p>Generated: {{ now()->format('d-M-Y h:i A') }}</p>
        </div>
    </div>

</body>
</html>
