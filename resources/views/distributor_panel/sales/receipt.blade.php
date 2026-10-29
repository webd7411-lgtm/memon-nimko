<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $sale->invoice_no }} — {{ sys_setting('software_name', 'Memon Nimko') }}</title>
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
            padding: 4px 0;
            text-transform: uppercase;
        }

        .receipt-table td {
            padding: 5px 0;
        }

        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .receipt-footer {
            text-align: center;
            font-size: 0.75rem;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

<div class="container no-print py-3 text-center">
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 me-2">
        <i class="fas fa-print me-1"></i> Print Receipt
    </button>
    <a href="{{ route('distributor.sales.create') }}" class="btn btn-success rounded-pill px-4 me-2">
        <i class="fas fa-plus me-1"></i> New Sale
    </a>
    <a href="{{ route('distributor.sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
        Back to List
    </a>
</div>

<div class="receipt-card">
    <div class="receipt-header">
        <h4 class="receipt-title">{{ sys_setting('software_name', 'Memon Nimko') }}</h4>
        <div class="receipt-subtitle">Distributor: {{ $distributor->name }}</div>
        <div class="receipt-subtitle">Mobile: {{ $distributor->phone }}</div>
        <div class="receipt-subtitle font-monospace mt-1">Invoice: <strong>{{ $sale->invoice_no }}</strong></div>
        <div class="receipt-subtitle">Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y h:i A') }}</div>
    </div>

    <!-- Customer Details -->
    <div class="mb-2" style="font-size: 0.85rem;">
        <div><strong>Customer:</strong> {{ $sale->customer->shop_name }}</div>
        @if($sale->customer->owner_name)
        <div><strong>Proprietor:</strong> {{ $sale->customer->owner_name }}</div>
        @endif
        @if($sale->customer->phone)
        <div><strong>Phone:</strong> {{ $sale->customer->phone }}</div>
        @endif
    </div>

    <table class="receipt-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item</th>
                <th class="text-center" style="width: 20%;">Qty</th>
                <th class="text-end" style="width: 30%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>
                    {{ $item->product->item_name }}
                    @if($item->variant)
                    <div style="font-size: 0.72rem; color: #555;">{{ $item->variant->size_label ?: $item->variant->variant_name }}</div>
                    @endif
                </td>
                <td class="text-center">{{ (float)$item->qty }}</td>
                <td class="text-end">Rs {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="receipt-divider"></div>

    <div style="font-size: 0.88rem;">
        <div class="d-flex justify-content-between py-1">
            <span>Subtotal:</span>
            <span>Rs {{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="d-flex justify-content-between py-1 text-danger">
            <span>Discount:</span>
            <span>- Rs {{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        <div class="d-flex justify-content-between py-1 fw-bold fs-6 border-top border-bottom my-1">
            <span>Net Total:</span>
            <span>Rs {{ number_format($sale->grand_total, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between py-1 text-success fw-bold">
            <span>Cash Paid:</span>
            <span>Rs {{ number_format($sale->paid_amount, 2) }}</span>
        </div>
        @if($sale->due_amount > 0)
        <div class="d-flex justify-content-between py-1 text-danger fw-bold">
            <span>Remaining Due:</span>
            <span>Rs {{ number_format($sale->due_amount, 2) }}</span>
        </div>
        @endif
        <div class="d-flex justify-content-between py-1 text-muted small border-top mt-1">
            <span>Customer Current Balance:</span>
            <span class="fw-bold">Rs {{ number_format($sale->customer->current_balance, 2) }}</span>
        </div>
    </div>

    <div class="receipt-footer">
        <div>Thank you for your business!</div>
        <div class="text-muted" style="font-size: 0.65rem;">Software by Memon Nimko ERP</div>
    </div>
</div>

<script>
    // Auto trigger print if requested
    if (window.location.search.includes('print=true')) {
        window.print();
    }
</script>
</body>
</html>
