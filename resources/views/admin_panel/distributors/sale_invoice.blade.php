<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $order->order_no }} — {{ sys_setting('software_name', 'Memon Nimko') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .invoice-card {
            max-width: 800px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .invoice-header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .table-invoice th {
            background: #f1f5f9;
            text-transform: uppercase;
            font-size: 0.78rem;
            color: #475569;
            padding: 8px 12px;
        }

        .table-invoice td {
            padding: 8px 12px;
            font-size: 0.88rem;
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
            .invoice-card {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container no-print py-3 text-center">
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 me-2">
        <i class="fas fa-print me-1"></i> Print Invoice / Gatepass
    </button>
    <a href="{{ route('admin.distributors.direct_sale') }}" class="btn btn-success rounded-pill px-4 me-2">
        <i class="fas fa-plus me-1"></i> New Sale
    </a>
    <a href="{{ route('admin.distributors.sales') }}" class="btn btn-outline-secondary rounded-pill px-3">
        Back to Sales List
    </a>
</div>

<div class="invoice-card">
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0 text-dark">{{ sys_setting('software_name', 'Memon Nimko') }}</h3>
            <span class="text-muted small">Head Office & Factory Stock Dispatch Invoice</span>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 font-monospace" style="color: #0f766e;">{{ $order->order_no }}</h5>
            <span class="text-muted small">Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</span>
        </div>
    </div>

    <!-- Distributor Info Box -->
    <div class="row g-3 mb-4 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
        <div class="col-6">
            <span class="text-muted small text-uppercase fw-semibold">Billed / Dispatched To:</span>
            <h5 class="fw-bold text-dark mb-1">{{ $order->distributor->name }}</h5>
            <div class="small text-muted"><strong>Code:</strong> {{ $order->distributor->code }}</div>
            <div class="small text-muted"><strong>Phone:</strong> {{ $order->distributor->phone }}</div>
            <div class="small text-muted"><strong>Area:</strong> {{ $order->distributor->area ?: 'Karachi' }}</div>
        </div>
        <div class="col-6 text-end">
            <span class="text-muted small text-uppercase fw-semibold">Dispatched From:</span>
            <h6 class="fw-bold text-dark mb-1">{{ $order->warehouse->name ?? 'Main Factory / Shop Stock' }}</h6>
            <div class="small text-muted"><strong>Status:</strong> Dispatched & Delivered</div>
            <div class="small text-muted"><strong>Current Ledger Balance:</strong> Rs. {{ number_format($order->distributor->current_balance, 2) }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="table table-bordered table-invoice mb-4">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Item Description</th>
                <th class="text-center" style="width: 15%;">Quantity</th>
                <th class="text-end" style="width: 15%;">Unit Rate</th>
                <th class="text-end" style="width: 20%;">Total (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="fw-bold text-dark">{{ $item->product->item_name }}</div>
                    @if($item->variant)
                    <div class="text-muted small">{{ $item->variant->size_label ?: $item->variant->variant_name }}</div>
                    @endif
                </td>
                <td class="text-center fw-bold">{{ (float)$item->approved_qty }} {{ $item->product->unit->name ?? 'Pcs' }}</td>
                <td class="text-end">Rs. {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-end fw-bold">Rs. {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc;">
                <td colspan="4" class="text-end fw-bold">Total Invoice Value:</td>
                <td class="text-end fw-bold text-success fs-6">Rs. {{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->admin_remarks)
    <div class="p-2.5 rounded-3 bg-light border small text-muted mb-4">
        <strong>Remarks:</strong> {{ $order->admin_remarks }}
    </div>
    @endif

    <div class="row pt-4 mt-4 border-top text-center text-muted small">
        <div class="col-4">
            <div class="border-top pt-2">Prepared By</div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">Dispatched / Driver</div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">Receiver / Distributor</div>
        </div>
    </div>
</div>

</body>
</html>
