<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Receipt #{{ $return->return_no }} — {{ $distributor->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }

        .receipt-card {
            max-width: 380px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .receipt-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .receipt-subtitle {
            font-size: 0.82rem;
            color: #333;
        }

        .receipt-table {
            width: 100%;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }

        .receipt-table th {
            border-bottom: 1px dashed #000;
            padding: 4px 2px;
        }

        .receipt-table td {
            padding: 4px 2px;
            vertical-align: top;
        }

        .receipt-footer {
            border-top: 1px dashed #000;
            padding-top: 10px;
            text-align: center;
            font-size: 0.8rem;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                width: 100%;
                border-radius: 0;
                padding: 10px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container py-3">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-2 mb-3 no-print">
        <a href="{{ route('distributor.returns.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Returns Register
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm rounded-pill px-3">
            <i class="fas fa-print me-1"></i> Print Return Receipt
        </button>
        <a href="{{ route('distributor.returns.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="fas fa-plus me-1"></i> New Return
        </a>
    </div>

    <!-- Thermal Receipt Card -->
    <div class="receipt-card">
        <div class="receipt-header">
            <h4 class="receipt-title">{{ $distributor->name }}</h4>
            <div class="receipt-subtitle">{{ $distributor->area }} &bull; {{ $distributor->city }}</div>
            <div class="receipt-subtitle">Phone: {{ $distributor->phone }}</div>
            <div class="badge bg-danger text-white mt-1 px-2 py-0.5" style="font-size: 0.75rem;">CUSTOMER SALE RETURN</div>
        </div>

        <div style="font-size: 0.82rem; margin-bottom: 10px;">
            <div class="d-flex justify-content-between">
                <span><b>Return #:</b> {{ $return->return_no }}</span>
                <span><b>Date:</b> {{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}</span>
            </div>
            @if($return->sale)
            <div><b>Original Inv:</b> {{ $return->sale->invoice_no }}</div>
            @endif
            <div><b>Customer:</b> {{ $return->customer->shop_name ?? 'N/A' }}</div>
            <div><b>Owner/Phone:</b> {{ $return->customer->owner_name ?? '' }} ({{ $return->customer->phone ?? '' }})</div>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Rate</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->items as $item)
                <tr>
                    <td>
                        {{ $item->product->item_name ?? 'Item' }}
                        @if($item->variant)
                        <div style="font-size: 0.72rem; color: #555;">({{ $item->variant->size_label ?: $item->variant->variant_name }})</div>
                        @endif
                        @if($item->item_condition === 'damaged')
                        <div style="font-size: 0.70rem; color: red;">[Damaged/Expired]</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ (float)$item->qty }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="border-top: 1px dashed #000; padding-top: 6px; font-size: 0.85rem;">
            <div class="d-flex justify-content-between fw-bold">
                <span>Total Return Value:</span>
                <span>Rs. {{ number_format($return->net_return_amount, 2) }}</span>
            </div>
            @if($return->credit_adjusted > 0)
            <div class="d-flex justify-content-between" style="color: green;">
                <span>Deducted from Udhaar:</span>
                <span>- Rs. {{ number_format($return->credit_adjusted, 2) }}</span>
            </div>
            @endif
            @if($return->refund_amount > 0)
            <div class="d-flex justify-content-between text-danger fw-bold">
                <span>Cash Refunded:</span>
                <span>Rs. {{ number_format($return->refund_amount, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between pt-1 border-top mt-1" style="font-size: 0.88rem;">
                <span class="fw-bold">Current Balance:</span>
                <span class="fw-bold">Rs. {{ number_format($return->customer->current_balance ?? 0, 2) }}</span>
            </div>
        </div>

        @if($return->reason)
        <div style="font-size: 0.78rem; margin-top: 8px; color: #444;">
            <b>Reason:</b> {{ $return->reason }}
        </div>
        @endif

        <div class="receipt-footer mt-3">
            <p class="mb-0">*** Customer Sale Return Acknowledged ***</p>
            <p class="text-muted mb-0" style="font-size: 0.7rem;">Software By: {{ sys_setting('software_name', 'Memon Nimko') }}</p>
        </div>
    </div>
</div>

</body>
</html>
