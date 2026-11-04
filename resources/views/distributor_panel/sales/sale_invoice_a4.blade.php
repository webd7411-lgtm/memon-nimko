<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $sale->invoice_no }} — {{ $distributor->name }}</title>
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
    $bookerDisplayName = null;
    if ($sale->booker) {
        $bookerDisplayName = $sale->booker->name;
    } elseif ($sale->notes && str_contains($sale->notes, 'Booked by:')) {
        $bookerDisplayName = trim(rtrim(\Illuminate\Support\Str::after($sale->notes, 'Booked by: '), ')'));
    }

    $distAddressParts = array_filter([$distributor->area ?? null, $distributor->city ?? null]);
    $distAddress = !empty($distAddressParts) ? implode(', ', $distAddressParts) : 'Territory / Area';
    $distPhone = $distributor->phone ?: '-';

    $cust = $sale->customer;
    $custShopName = $cust?->shop_name ?: ($cust?->name ?: 'Walk-in Customer');
    $custOwnerName = $cust?->owner_name ?: ($cust?->contact_person ?: null);
    $custPhone = $cust?->phone ?: ($cust?->mobile ?: '-');
    $custAddressParts = array_filter([$cust?->address ?? null, $cust?->area ?? null, $cust?->city ?? null]);
    $custAddress = !empty($custAddressParts) ? implode(', ', $custAddressParts) : '-';

    $created = $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->timezone(config('app.timezone', 'Asia/Karachi')) : now();
    $invoiceDateTime = $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->setTime($created->hour, $created->minute, $created->second) : $created;

    // Line totals & discount computations
    $computedGross = 0;
    $computedItemDiscount = 0;
    $computedNet = 0;
    $totalQtySum = 0;
    $totalTOSum = 0;

    foreach ($sale->items as $it) {
        $qty = (float)($it->qty ?? 0);
        $to = (float)($it->trade_offer ?? 0);
        $price = (float)($it->unit_price ?? 0);
        $disc = (float)($it->discount ?? 0);
        $totalQtySum += $qty;
        $totalTOSum += $to;
        $lGross = ($qty * $price);
        $computedGross += $lGross;
        $computedItemDiscount += $disc;
        $computedNet += max(0, $lGross - $disc);
    }

    // Determine clean total discount (handles legacy double-counted discounts)
    $rawDiscount = (float)($sale->discount ?? 0);
    if ($computedItemDiscount > 0 && abs($rawDiscount - (2 * $computedItemDiscount)) < 0.05) {
        $totalDiscount = $computedItemDiscount;
    } elseif ($rawDiscount > 0) {
        $totalDiscount = $rawDiscount;
    } else {
        $totalDiscount = $computedItemDiscount;
    }

    $grossTotal = $computedGross > 0 ? $computedGross : (float)($sale->subtotal ?? 0);
    $netAmount = $computedNet > 0 ? $computedNet : max(0, $grossTotal - $totalDiscount);

    // Number to words converter
    $numberToWords = function($number) {
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $rupees = floor($number);
        $paisa = round(($number - $rupees) * 100);
        $words = ucwords($f->format($rupees)) . ' Rupees';
        if ($paisa > 0) {
            $words .= ' and ' . ucwords($f->format($paisa)) . ' Paisa';
        }
        return $words . ' Only';
    };

    $amountWords = '';
    try {
        if (class_exists('\NumberFormatter')) {
            $amountWords = $numberToWords($netAmount);
        }
    } catch (\Throwable $e) {}
@endphp

<!-- Top Action Bar (Screen Only) -->
<div class="container no-print py-3 text-center">
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 me-2 shadow-sm fw-bold">
        <i class="fas fa-print me-1"></i> Print Invoice (A4)
    </button>
    <a href="{{ route('distributor.sales.receipt', $sale->id) }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
        <i class="fas fa-receipt me-1"></i> Thermal Receipt
    </a>
    <a href="{{ route('distributor.sales.create') }}" class="btn btn-success rounded-pill px-4 me-2 shadow-sm">
        <i class="fas fa-plus me-1"></i> New Sale
    </a>
    <a href="{{ route('distributor.sales.index') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
        <i class="fas fa-list me-1"></i> Sales List
    </a>
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
                <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $distributor->name }}</h3>
                <div class="text-muted small fw-semibold">Authorized Distributor — {{ sys_setting('software_name', 'Memon Nimko') }}</div>
                <div class="text-muted small">{{ $distAddress }}</div>
                <div class="text-muted small"><strong>Phone:</strong> {{ $distPhone }}</div>
            </div>
        </div>

        <div class="text-end">
            <span class="badge text-white px-3 py-1.5 rounded-pill mb-1" style="background: #0f766e; font-size: 0.78rem; letter-spacing: 0.5px;">
                DISTRIBUTOR COMMERCIAL INVOICE
            </span>
            <h4 class="fw-bold font-monospace text-dark mb-0 mt-1">{{ $sale->invoice_no }}</h4>
            <div class="text-muted small"><strong>Date:</strong> {{ $invoiceDateTime->format('d M, Y - h:i A') }}</div>
            <div class="text-muted small"><strong>Billed By:</strong> {{ $bookerDisplayName ?: ($distributor->name ?? 'Distributor') }}</div>
        </div>
    </div>

    <!-- Customer / Dispatch Meta Box -->
    <div class="row g-3 mb-3">
        <div class="col-7">
            <div class="info-box h-100">
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; color: #0f766e !important;">
                    <i class="fas fa-store me-1"></i> Billed To (Retail Customer / Shop):
                </span>
                <h5 class="fw-bold text-dark mt-1 mb-1">
                    {{ $custShopName }}
                </h5>
                @if($custOwnerName)
                <div class="small text-muted"><strong>Proprietor:</strong> {{ $custOwnerName }}</div>
                @endif
                <div class="small text-muted"><strong>Contact Phone:</strong> {{ $custPhone }}</div>
                <div class="small text-muted"><strong>Address:</strong> {{ $custAddress }}</div>
            </div>
        </div>

        <div class="col-5">
            <div class="info-box h-100">
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; color: #0f766e !important;">
                    <i class="fas fa-receipt me-1"></i> Dispatch & Account Meta:
                </span>
                <div class="mt-1 small text-muted">
                    <strong>Distributor:</strong> {{ $distributor->name }}
                </div>
                @if($bookerDisplayName)
                <div class="small text-muted">
                    <strong>Order Booker:</strong> <span class="fw-bold" style="color: #0f766e;">{{ $bookerDisplayName }}</span>
                </div>
                @endif
                <div class="small text-muted">
                    <strong>Payment Mode:</strong> 
                    <span class="fw-semibold">{{ ucfirst($sale->payment_type ?: ($sale->paid_amount >= $netAmount ? 'Cash Paid' : ($sale->paid_amount > 0 ? 'Partial' : 'Credit Account'))) }}</span>
                </div>
                <div class="small mt-1 pt-1 border-top">
                    <strong>Customer Ledger Balance:</strong> 
                    <span class="fw-bold text-danger">Rs. {{ number_format($cust?->current_balance ?? 0, 2) }}</span>
                </div>
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
                @foreach($sale->items as $index => $item)
                @php
                    $qty = (float)($item->qty ?? 0);
                    $to = (float)($item->trade_offer ?? 0);
                    $price = (float)($item->unit_price ?? 0);
                    $disc = (float)($item->discount ?? 0);
                    $lineGross = $qty * $price;
                    $lineNet = max(0, ((float)$item->subtotal > 0 ? (float)$item->subtotal : $lineGross) - $disc);
                    $prodCode = $item->product->item_code ?? ($item->product->code ?? '-');
                    $unitName = $item->product->unit->name ?? ($item->product->unit ?? 'PC');

                    // Per unit discount & rate calculation
                    $unitDisc = ($qty > 0 && $disc > 0) ? ($disc / $qty) : 0;
                    $discPct = ($lineGross > 0 && $disc > 0) ? (($disc / $lineGross) * 100) : 0;
                    $isRoundPct = ($discPct > 0 && abs($discPct - round($discPct)) < 0.01);
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold text-dark">{{ $item->product->item_name ?? 'Product' }}</span>
                        @if($item->variant)
                        <div class="small text-muted">{{ $item->variant->size_label ?: $item->variant->variant_name }}</div>
                        @endif
                    </td>
                    <td class="text-center font-monospace small text-muted">
                        {{ $prodCode }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">{{ strtoupper($unitName) }}</span>
                    </td>
                    <td class="text-center fw-bold fs-6 text-dark">
                        {{ (float)$qty }}
                    </td>
                    <td class="text-end">
                        Rs. {{ number_format($price, 2) }}
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
                    <td class="text-end text-danger fw-semibold">
                        {{ $disc > 0 ? ('- ' . number_format($disc, 2)) : '-' }}
                    </td>
                    <td class="text-end fw-bold text-dark">
                        Rs. {{ number_format($lineNet, 2) }}
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
                    <td class="text-center text-uppercase" style="font-size: 0.78rem;">Total:</td>
                    <td class="text-end text-danger fw-bold">{{ $totalDiscount > 0 ? ('- ' . number_format($totalDiscount, 2)) : '-' }}</td>
                    <td class="text-end text-dark fs-6 fw-bold">Rs. {{ number_format($netAmount, 2) }}</td>
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
                    <strong class="text-dark" style="font-size: 0.88rem;">{{ $amountWords ?: 'Rupees ' . number_format($netAmount, 2) . ' Only' }}</strong>
                </div>

                @if($totalDiscount > 0)
                <div class="small text-danger mb-1 fw-semibold">
                    <i class="fas fa-tag me-1"></i> Special Extra Discount Applied: Rs. {{ number_format($totalDiscount, 2) }}
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
                        <td class="text-end fw-bold">Rs. {{ number_format($grossTotal, 2) }}</td>
                    </tr>
                    @if($totalDiscount > 0)
                    <tr>
                        <td class="text-danger">Total Discount:</td>
                        <td class="text-end fw-bold text-danger">- Rs. {{ number_format($totalDiscount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top border-bottom">
                        <td class="py-2 fw-bold text-dark fs-6" style="color: #0f766e !important;">Net Amount:</td>
                        <td class="py-2 text-end fw-bold fs-6" style="color: #0f766e !important;">Rs. {{ number_format($netAmount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pt-2">Cash Received:</td>
                        <td class="text-end pt-2 fw-semibold text-success">Rs. {{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    @if($sale->due_amount > 0)
                    <tr>
                        <td class="text-danger">Remaining Due:</td>
                        <td class="text-end fw-bold text-danger">Rs. {{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td class="text-muted">Balance / Change:</td>
                        <td class="text-end fw-semibold text-muted">Rs. 0.00</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row pt-4 mt-2 text-center text-muted" style="font-size: 0.8rem;">
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Prepared By (Cashier / Booker)</strong>
                <div class="small">{{ $bookerDisplayName ?: ($distributor->name ?? 'Admin') }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Checked & Dispatched By</strong>
                <div class="small">Distributor / Warehouse Incharge</div>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">
                <strong>Retailer Signature & Stamp</strong>
                <div class="small">{{ $custShopName }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1' || urlParams.get('print') === 'true') {
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 350);
        };
    }
</script>
</body>
</html>
