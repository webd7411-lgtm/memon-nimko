<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $payment->reference_no ?? ('REC-' . $payment->id) }} — {{ $distributor->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }

        .receipt-card {
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            padding: 24px;
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
            margin-bottom: 12px;
            font-size: 0.85rem;
        }

        .receipt-table td {
            padding: 4px 0;
        }

        .receipt-footer {
            border-top: 1px dashed #000;
            padding-top: 12px;
            text-align: center;
            font-size: 0.8rem;
        }

        @media print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0 auto;
                padding: 10px;
                width: 100%;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-card">
        <!-- Actions (Hidden during print) -->
        <div class="d-flex justify-content-between mb-3 no-print">
            <a href="{{ route('distributor.customer_payments.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <div>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Receipt
                </button>
            </div>
        </div>

        <!-- Receipt Header -->
        <div class="receipt-header">
            <h5 class="receipt-title">{{ $distributor->business_name ?? $distributor->name }}</h5>
            <div class="receipt-subtitle">{{ $distributor->address ?? 'Distribution Center' }}</div>
            <div class="receipt-subtitle">Phone: {{ $distributor->phone ?? '-' }}</div>
            <div class="badge bg-dark text-white mt-2 px-3 py-1">CUSTOMER PAYMENT RECEIPT</div>
        </div>

        <!-- Info Table -->
        <table class="receipt-table">
            <tr>
                <td><strong>Receipt No:</strong></td>
                <td class="text-end">{{ $payment->reference_no ?: ('REC-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT)) }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td class="text-end">{{ \Carbon\Carbon::parse($payment->date)->format('d M, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Customer / Shop:</strong></td>
                <td class="text-end"><strong>{{ $payment->customer->shop_name ?? 'N/A' }}</strong></td>
            </tr>
            @if($payment->customer && $payment->customer->owner_name)
            <tr>
                <td><strong>Proprietor:</strong></td>
                <td class="text-end">{{ $payment->customer->owner_name }}</td>
            </tr>
            @endif
            @if($payment->customer && $payment->customer->phone)
            <tr>
                <td><strong>Contact:</strong></td>
                <td class="text-end">{{ $payment->customer->phone }}</td>
            </tr>
            @endif
            @if($payment->account)
            <tr>
                <td><strong>Account:</strong></td>
                <td class="text-end">{{ $payment->account->title }}</td>
            </tr>
            @endif
        </table>

        <!-- Amount Box -->
        <div style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 12px 0; margin: 12px 0;">
            <div class="d-flex justify-content-between align-items-center">
                <span style="font-size: 1rem; font-weight: bold;">AMOUNT RECEIVED:</span>
                <span style="font-size: 1.35rem; font-weight: bold;">Rs. {{ number_format($payment->credit, 2) }}</span>
            </div>
        </div>

        <!-- Balance Info -->
        <table class="receipt-table">
            <tr>
                <td><strong>Remaining Balance Due:</strong></td>
                <td class="text-end"><strong>Rs. {{ number_format($payment->balance, 2) }}</strong></td>
            </tr>
            @if($payment->description)
            <tr>
                <td colspan="2" class="pt-2">
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 6px 8px; border-radius: 4px; font-size: 0.8rem;">
                        <strong>Note:</strong> {{ $payment->description }}
                    </div>
                </td>
            </tr>
            @endif
        </table>

        <!-- Signatures & Footer -->
        <div class="d-flex justify-content-between mt-5 pt-3" style="font-size: 0.8rem; border-top: 1px dotted #ccc;">
            <div class="text-center" style="width: 45%;">
                <div style="border-top: 1px solid #000; margin-top: 30px;">Customer Signature</div>
            </div>
            <div class="text-center" style="width: 45%;">
                <div style="border-top: 1px solid #000; margin-top: 30px;">Authorized Signature</div>
            </div>
        </div>

        <div class="receipt-footer mt-4">
            <div>Thank you for your business!</div>
            <div class="text-muted" style="font-size: 0.72rem;">Generated on {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>

<script>
    // Auto-open print dialog if requested
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.onload = function() { window.print(); };
    }
</script>
</body>
</html>
