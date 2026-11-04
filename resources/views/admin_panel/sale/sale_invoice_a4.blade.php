<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $sale->invoice_no }} — {{ sys_setting('software_name', 'Memon Nimko') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .invoice-card {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 32px 36px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .invoice-header {
            border-bottom: 2.5px solid #0f766e;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .table-invoice {
            border-color: #cbd5e1;
        }

        .table-invoice th {
            background: #f1f5f9;
            text-transform: uppercase;
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            padding: 7px 10px;
            border-bottom: 1.5px solid #cbd5e1;
        }

        .table-invoice td {
            padding: 7px 10px;
            font-size: 0.85rem;
            vertical-align: middle;
            border-color: #e2e8f0;
        }

        .table-invoice tbody tr:nth-child(even) {
            background-color: #fbfcfe;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }

        .totals-table td {
            padding: 4px 8px;
            font-size: 0.86rem;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm 12mm;
            }
            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 11pt;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .table-invoice th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .info-box {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

@php
    $receiptBranch = $receiptBranch 
        ?? $sale->branch 
        ?? ($sale->branch_id ? \App\Models\Branch::find($sale->branch_id) : null)
        ?? \App\Models\Branch::find(active_branch_id())
        ?? \App\Models\Branch::first();

    $branchAddress = !empty($receiptBranch?->address) ? $receiptBranch->address : 'Hyderabad, Sindh, Pakistan';
    $rawPhone = !empty($receiptBranch?->number) ? $receiptBranch->number : (!empty($receiptBranch?->phone) ? $receiptBranch->phone : '0334 2615888');
    $branchPhone = preg_replace('/^Phone:\s*/i', '', $rawPhone);
    $returnTo = request('return_to', route('sale.screen'));
@endphp

<!-- Top Action Bar (Screen Only) -->
<div class="container no-print py-3 text-center">
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 me-2 shadow-sm fw-bold">
        <i class="fas fa-print me-1"></i> Print Invoice (A4)
    </button>
    <a href="{{ $returnTo }}" class="btn btn-outline-primary rounded-pill px-4 me-2 shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Sale Screen
    </a>
    <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
        Close
    </button>
</div>

<div class="invoice-card">
    <!-- Header -->
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            @if(file_exists(public_path('assets/images/logo_print.png')))
                <img src="{{ asset('assets/images/logo_print.png') }}" alt="Logo" style="max-height: 70px; max-width: 150px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/images/logo.png')))
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 70px; max-width: 150px; object-fit: contain;">
            @endif
            <div>
                <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ shop_name() ?: sys_setting('software_name', 'Memon Nimko') }}</h3>
                <div class="text-muted small fw-semibold">{{ shop_tagline() }}</div>
                <div class="text-muted small">{{ $branchAddress }}</div>
                <div class="text-muted small"><strong>Phone:</strong> {{ $branchPhone }}</div>
            </div>
        </div>

        <div class="text-end">
            <span class="badge text-white px-3 py-1.5 rounded-pill mb-1" style="background: #0f766e; font-size: 0.78rem; letter-spacing: 0.5px;">
                DISTRIBUTOR COMMERCIAL INVOICE
            </span>
            <h4 class="fw-bold font-monospace text-dark mb-0 mt-1">{{ $sale->invoice_no }}</h4>
            <div class="text-muted small"><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d M, Y - h:i A') }}</div>
            <div class="text-muted small"><strong>Billed By:</strong> {{ $sale->user->name ?? 'Head Office' }}</div>
        </div>
    </div>

    <!-- Distributor / Customer Info Box -->
    <div class="row g-3 mb-3">
        <div class="col-7">
            <div class="info-box h-100">
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; color: #0f766e !important;">
                    <i class="fas fa-truck me-1"></i> Billed To (Distributor Custody / Order):
                </span>
                <h5 class="fw-bold text-dark mt-1 mb-1">
                    {{ $distributor ? $distributor->name : $sale->customer_display_name }}
                </h5>
                @if($distributor)
                <div class="small text-muted"><strong>Distributor Code:</strong> <span class="badge bg-light text-dark border font-monospace">{{ $distributor->code }}</span></div>
                <div class="small text-muted"><strong>Contact Phone:</strong> {{ $distributor->phone ?: '-' }}</div>
                <div class="small text-muted"><strong>Territory / Area:</strong> {{ $distributor->address ?: ($distributor->area ?: 'Main Territory') }}</div>
                @else
                <div class="small text-muted"><strong>Contact:</strong> {{ $sale->customer_relation->phone ?? '-' }}</div>
                <div class="small text-muted"><strong>Address:</strong> {{ $sale->customer_relation->address ?? '-' }}</div>
                @endif
            </div>
        </div>

        <div class="col-5">
            <div class="info-box h-100">
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; color: #0f766e !important;">
                    <i class="fas fa-receipt me-1"></i> Dispatch & Account Meta:
                </span>
                <div class="mt-1 small text-muted">
                    <strong>Dispatch Branch:</strong> {{ $receiptBranch->name ?? 'Main Branch / Factory' }}
                </div>
                <div class="small text-muted">
                    <strong>Reference / M.Bill:</strong> {{ $sale->reference ?: '-' }}
                </div>
                <div class="small text-muted">
                    <strong>Payment Mode:</strong> {{ $sale->cash > 0 ? 'Cash Paid' : ($sale->card > 0 ? 'Bank Transfer' : 'Credit Account') }}
                </div>
                @if($distributor)
                <div class="small mt-1 pt-1 border-top">
                    <strong>Distributor Ledger Balance:</strong> 
                    <span class="fw-bold text-danger">Rs. {{ number_format($distributor->current_balance ?? 0, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-invoice align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 3%;" class="text-center">#</th>
                    <th style="width: 27%;">Item Description & Variant</th>
                    <th style="width: 10%;" class="text-center">Item Code</th>
                    <th style="width: 7%;" class="text-center">Unit</th>
                    <th style="width: 7%;" class="text-center">Quantity</th>
                    <th style="width: 10%;" class="text-end">Unit Price</th>
                    <th style="width: 8%;" class="text-center">Scheme / T.O</th>
                    <th style="width: 9%;" class="text-center">Disc. / Unit</th>
                    <th style="width: 9%;" class="text-end">Discount</th>
                    <th style="width: 10%;" class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalQtySum = 0; 
                    $totalTOSum = 0;
                @endphp
                @foreach($saleItems as $index => $item)
                @php
                    $qty = (float)($item['qty'] ?? 0);
                    $price = (float)($item['price'] ?? 0);
                    $itemDiscount = (float)($item['discount'] ?? 0);
                    $to = (float)($item['trade_offer'] ?? 0);
                    $totalQtySum += $qty;
                    $totalTOSum += $to;

                    // Per unit discount & rate calculation
                    $unitDisc = ($qty > 0 && $itemDiscount > 0) ? ($itemDiscount / $qty) : 0;
                    $lineGross = $price * $qty;
                    $discPct = ($lineGross > 0 && $itemDiscount > 0) ? (($itemDiscount / $lineGross) * 100) : 0;
                    $isRoundPct = ($discPct > 0 && abs($discPct - round($discPct)) < 0.01);
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold text-dark">{{ $item['item_name'] }}</span>
                    </td>
                    <td class="text-center font-monospace small text-muted">
                        {{ $item['item_code'] ?: '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">{{ strtoupper($item['unit'] ?: 'PC') }}</span>
                    </td>
                    <td class="text-center fw-bold fs-6 text-dark">
                        {{ (float)$qty }}
                    </td>
                    <td class="text-end">
                        Rs. {{ number_format($item['price'], 2) }}
                    </td>
                    <td class="text-center">
                        @if($to > 0)
                        <span class="badge bg-success-subtle text-success border border-success fw-bold" style="font-size: 0.75rem;">
                            +{{ (float)$to }} Free
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($unitDisc > 0)
                            @if($isRoundPct)
                                <span class="fw-bold text-dark">{{ round($discPct) }}%</span>
                                <div class="text-muted" style="font-size: 0.70rem;">Rs. {{ number_format($unitDisc, 2) }}/pc</div>
                            @elseif($discPct > 0 && $discPct < 100)
                                <span class="fw-bold text-dark">{{ round($discPct, 1) }}%</span>
                                <div class="text-muted" style="font-size: 0.70rem;">Rs. {{ number_format($unitDisc, 2) }}/pc</div>
                            @else
                                <span class="fw-bold text-dark">Rs. {{ number_format($unitDisc, 2) }}</span>
                                <div class="text-muted" style="font-size: 0.70rem;">per pc</div>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end text-danger">
                        {{ $itemDiscount > 0 ? ('- ' . number_format($itemDiscount, 2)) : '-' }}
                    </td>
                    <td class="text-end fw-bold text-dark">
                        Rs. {{ number_format($item['total'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background: #f8fafc; font-weight: 700;">
                <tr>
                    <td colspan="4" class="text-end text-uppercase" style="font-size: 0.78rem;">Total Quantity:</td>
                    <td class="text-center text-dark fs-6">{{ (float)$totalQtySum }}</td>
                    <td class="text-center text-uppercase" style="font-size: 0.78rem;">Total T.O:</td>
                    <td class="text-center text-success">{{ $totalTOSum > 0 ? ('+' . (float)$totalTOSum . ' Free') : '-' }}</td>
                    <td colspan="2" class="text-end text-uppercase" style="font-size: 0.78rem;">Subtotal:</td>
                    <td class="text-end text-dark">Rs. {{ number_format($sale->total_bill_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Summary & Financial Settlement -->
    <div class="row g-3 mb-4">
        <!-- Left: In words & Terms -->
        <div class="col-7">
            <div class="p-3 rounded-3 h-100" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <div class="mb-2">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Amount in Words:</span>
                    <strong class="text-dark" style="font-size: 0.88rem;">{{ $sale->total_amount_Words ?: 'Three Thousand Seven Hundred Eighty Rupees Only' }}</strong>
                </div>

                @if(!empty($sale->total_extradiscount) && $sale->total_extradiscount > 0)
                <div class="small text-danger mb-1">
                    <i class="fas fa-tag me-1"></i> Special Extra Discount Applied: Rs. {{ number_format($sale->total_extradiscount, 2) }}
                </div>
                @endif

                <div class="mt-2 pt-2 border-top text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                    <strong>Terms & Conditions:</strong><br>
                    1. Stock received in good condition. Claims or shortages must be reported within 24 hours of delivery.<br>
                    2. Goods are supplied subject to distributor custody terms and commercial ledger agreement.
                </div>
            </div>
        </div>

        <!-- Right: Totals Box -->
        <div class="col-5">
            <div class="info-box p-3">
                <table class="w-100 totals-table">
                    <tr>
                        <td class="text-muted">Gross Total:</td>
                        <td class="text-end fw-bold">Rs. {{ number_format($sale->total_bill_amount, 2) }}</td>
                    </tr>
                    @if(!empty($sale->total_extradiscount) && $sale->total_extradiscount > 0)
                    <tr>
                        <td class="text-danger">Extra Discount:</td>
                        <td class="text-end fw-bold text-danger">- Rs. {{ number_format($sale->total_extradiscount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top border-bottom">
                        <td class="py-2 fw-bold text-dark fs-6" style="color: #0f766e !important;">Net Amount:</td>
                        <td class="py-2 text-end fw-bold fs-6" style="color: #0f766e !important;">Rs. {{ number_format($sale->total_net, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pt-2">Cash Received:</td>
                        <td class="text-end pt-2 fw-semibold">Rs. {{ number_format($sale->cash, 2) }}</td>
                    </tr>
                    @if($sale->card > 0)
                    <tr>
                        <td class="text-muted">Bank / Card Paid:</td>
                        <td class="text-end fw-semibold">Rs. {{ number_format($sale->card, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Balance / Change:</td>
                        <td class="text-end fw-semibold text-muted">Rs. {{ number_format($sale->change, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row pt-4 mt-2 text-center text-muted" style="font-size: 0.8rem;">
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Prepared By (Cashier)</strong>
                <div class="small">{{ $sale->user->name ?? 'Admin' }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Checked & Dispatched By</strong>
                <div class="small">Store / Warehouse Incharge</div>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Distributor Signature & Stamp</strong>
                <div class="small">{{ $distributor ? $distributor->name : 'Receiver' }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto print if ?autoprint=1 is in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 350);
        };
    }
</script>
</body>
</html>
