<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt - {{ $sale->invoice_no }}</title>
<style>
    * {
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        box-sizing: border-box;
    }
    body {
        font-family: 'Courier New', 'Lucida Console', monospace, sans-serif !important;
        font-size: 12px;
        color: #000000 !important;
        background: #ffffff;
        margin: 0;
        padding: 0;
        font-weight: 600;
        -webkit-font-smoothing: antialiased;
    }
    .receipt-container {
        width: 100%;
        max-width: 340px;
        margin: auto;
        padding: 0 10px 10px;
        padding-top: 0 !important;
    }
    .center {
        text-align: center;
    }
    .bold, th, h2, .bold-text {
        font-weight: 900 !important;
    }
    .line {
        border-top: 1.5px dashed #000000 !important;
        margin: 4px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 2px 0;
        color: #000000 !important;
    }
    th {
        text-align: left;
        font-size: 11px;
        font-weight: 900 !important;
    }
    td {
        font-size: 11px;
        font-weight: 600;
    }
    td:last-child, th:last-child {
        text-align: right;
    }
    .footer {
        text-align: center;
        font-size: 11px;
        margin-top: 6px;
        border-top: 1.5px dashed #000000 !important;
        padding-top: 4px;
        color: #000000 !important;
    }
    .footer p {
        margin: 2px 0;
        color: #000000 !important;
    }
    @media print {
        * {
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @page {
            margin: 0;
            size: 80mm auto;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: auto;
            color: #000000 !important;
            background: #ffffff !important;
        }
        .receipt-container {
            width: 74mm !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 2mm 2mm !important;
            padding-top: 0 !important;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
            color: #000000 !important;
        }
        table {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
        }
        th, td {
            white-space: normal !important;
            padding: 1px !important;
            font-size: 10px !important;
            color: #000000 !important;
        }
        body {
            font-size: 10px !important;
            color: #000000 !important;
        }
    }
    .page-break {
        page-break-after: always;
    }
</style>
</head>
<body>

<div class="receipt-container">

    <!-- Header -->
    <div class="center" style="margin-top: 0; padding-top: 0;">
        @php
            $receiptLogo = receipt_logo_url();
        @endphp
        @if(!empty($receiptLogo))
            <img src="{{ $receiptLogo }}" alt="" style="max-height: 55px; margin: 0 auto 4px; display: block; filter: contrast(180%); image-rendering: -webkit-optimize-contrast;">
        @endif

        <h2 style="margin:0;font-size:15px;" class="bold">{{ shop_name() }}</h2>
        @if(!empty(shop_tagline()))
            <p style="margin:0;font-size:11px;">{{ shop_tagline() }}</p>
        @endif

        @php
            $currentBranch = $sale->branch ?? \App\Models\Branch::find(active_branch_id());
            
            // Short Unit Helper
            $fnUnitShort = function($unit) {
                $u = trim(strtolower($unit));
                if (in_array($u, ['gram', 'grams', 'gm', 'g'])) return 'Gm';
                if (in_array($u, ['piece', 'pieces', 'pc', 'pcs'])) return 'Pc';
                if (in_array($u, ['pound', 'pounds', 'pnd', 'lb', 'lbs'])) return 'Pnd';
                if (in_array($u, ['kilogram', 'kilograms', 'kg'])) return 'Kg';
                if (in_array($u, ['packet', 'packets', 'pack', 'pkt'])) return 'Pkt';
                if (in_array($u, ['box', 'boxes', 'bx'])) return 'Bx';
                return !empty($unit) ? ucfirst(strtolower($unit)) : '';
            };

            $returnItems = [];
            $newItems = [];
            $returnTotalCost = 0;
            $newTotalCost = 0;

            foreach($saleItems as $item) {
                $q = (float)($item['qty'] ?? 0);
                if ($q < 0) {
                    $returnItems[] = $item;
                    $returnTotalCost += abs((float)($item['total'] ?? 0));
                } else {
                    $newItems[] = $item;
                    $newTotalCost += (float)($item['total'] ?? 0);
                }
            }

            $isExchange = count($returnItems) > 0 || ($sale->sale_status == 2);

            $currentBranch = $currentBranch 
                ?? $sale->branch 
                ?? ($sale->branch_id ? \App\Models\Branch::find($sale->branch_id) : null)
                ?? (auth()->check() && auth()->user()->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : null)
                ?? \App\Models\Branch::find(active_branch_id())
                ?? \App\Models\Branch::first();

            $bAddress = !empty($currentBranch?->address) ? $currentBranch->address : sys_setting('address', '');
            $rawPhone = !empty($currentBranch?->number) ? $currentBranch->number : (!empty($currentBranch?->phone) ? $currentBranch->phone : sys_setting('phone', ''));
            $bPhone = preg_replace('/^Phone:\s*/i', '', $rawPhone);
        @endphp

        @if(!empty($bAddress))
            <p style="margin:2px 0 0 0;font-size:11px;">{{ $bAddress }}</p>
        @endif
        @if(!empty($bPhone))
            <p style="margin:0;font-size:11px;">Phone: {{ $bPhone }}</p>
        @endif
    </div>

    <div class="line"></div>
    <div class="center bold" style="font-size: 13px; text-transform: uppercase;">
        {{ $isExchange ? 'SALE EXCHANGE RECEIPT' : 'SALE RECEIPT' }}
    </div>
    <div class="line"></div>

    <!-- Details -->
    <table>
        <tr><th>Invoice #:</th><td>{{ $sale->invoice_no }}</td></tr>
        <tr><th>Operator Name:</th><td>{{ auth()->user()->name ?? 'Admin' }}</td></tr>
        <tr><th>Order Type:</th><td>{{ $sale->order_type ?? 'Walk-in' }}</td></tr>
        @if(isset($sale->order_type) && $sale->order_type == 'Dine-in' && $sale->table_id)
        <tr><th>Table No:</th><td>{{ $sale->table->table_name ?? 'N/A' }}</td></tr>
        @endif
        <tr><th>Invoice Date:</th><td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y H:i:s') }}</td></tr>
    </table>

    <div class="line"></div>

    <!-- Items -->
    @if($isExchange)
        {{-- RETURN ITEMS SECTION --}}
        @if(count($returnItems) > 0)
            <div style="font-weight: bold; text-align: center; margin: 4px 0; font-size: 11px; text-transform:uppercase;">--- RETURN ITEMS ---</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:40%;">Item Name</th>
                        <th style="width:20%;">Price</th>
                        <th style="width:20%;">Qty/Wt</th>
                        <th style="width:20%; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returnItems as $item)
                    @php
                        $rQty = abs((float)($item['qty'] ?? 0));
                        $isKg = strtolower($item['unit'] ?? '') === 'kg';
                        if ($isKg && $rQty < 1) {
                            $displayQty = round($rQty * 1000);
                            $displayUnit = 'Gm';
                        } else {
                            $displayQty = (float)number_format($rQty, 3);
                            $displayUnit = $fnUnitShort($item['unit'] ?? '');
                        }
                    @endphp
                    <tr style="font-weight: bold;">
                        <td>{{ $item['item_name'] }}</td>
                        <td>{{ number_format($item['price'], 0) }}</td>
                        <td>-{{ $displayQty }} {{ $displayUnit }}</td>
                        <td style="text-align:right;">-{{ number_format(abs($item['total']), 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="text-align: right; font-weight: bold; margin: 3px 0;">
                Return Total: -Rs {{ number_format($returnTotalCost, 0) }}
            </div>
            <div class="line"></div>
        @endif

        {{-- NEW PURCHASED ITEMS SECTION --}}
        @if(count($newItems) > 0)
            <div style="font-weight: bold; text-align: center; margin: 4px 0; font-size: 11px; text-transform:uppercase;">--- NEW ITEMS ---</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:40%;">Item Name</th>
                        <th style="width:20%;">Price</th>
                        <th style="width:20%;">Qty/Wt</th>
                        <th style="width:20%; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newItems as $item)
                    @php
                        $nQty = (float)($item['qty'] ?? 0);
                        $isKg = strtolower($item['unit'] ?? '') === 'kg';
                        if ($isKg && $nQty < 1) {
                            $displayQty = round($nQty * 1000);
                            $displayUnit = 'Gm';
                        } else {
                            $displayQty = (float)number_format($nQty, 3);
                            $displayUnit = $fnUnitShort($item['unit'] ?? '');
                        }
                    @endphp
                    <tr>
                        <td>{{ $item['item_name'] }}</td>
                        <td>{{ number_format($item['price'], 0) }}</td>
                        <td>{{ $displayQty }} {{ $displayUnit }}@if(!empty($item['trade_offer']) && $item['trade_offer'] > 0)<br><small style="color:#0f766e;font-size:10px;font-weight:bold;">(+{{ $item['trade_offer'] }} Free)</small>@endif</td>
                        <td style="text-align:right;">{{ number_format($item['total'], 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="text-align: right; font-weight: bold; margin: 3px 0;">
                New Items Cost: Rs {{ number_format($newTotalCost, 0) }}
            </div>
            <div class="line"></div>
        @endif
    @else
        {{-- STANDARD SALE ITEMS --}}
        <table>
            <thead>
                <tr>
                    <th style="width:40%;">Item Name</th>
                    <th style="width:20%;">Price</th>
                    <th style="width:20%;">Qty/Wt</th>
                    <th style="width:20%; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saleItems as $item)
                @php
                    $qty = (float)($item['qty'] ?? 0);
                    $isKg = strtolower($item['unit'] ?? '') === 'kg';
                    if ($isKg && $qty < 1) {
                        $displayQty = round($qty * 1000);
                        $displayUnit = 'Gm';
                    } else {
                        $displayQty = (float)number_format($qty, 3);
                        $displayUnit = $fnUnitShort($item['unit'] ?? '');
                    }
                @endphp
                <tr>
                    <td>
                        {{ $item['item_name'] }}
                        @if(isset($item['discount']) && $item['discount'] > 0)
                            <br><small style="font-weight:normal;">Disc: {{ number_format($item['discount'], 0) }}</small>
                        @endif
                    </td>
                    <td>{{ number_format($item['price'], 0) }}</td>
                    <td>{{ $displayQty }} {{ $displayUnit }}@if(!empty($item['trade_offer']) && $item['trade_offer'] > 0)<br><small style="color:#0f766e;font-size:10px;font-weight:bold;">(+{{ $item['trade_offer'] }} Free)</small>@endif</td>
                    <td style="text-align:right;">{{ number_format($item['total'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="line"></div>
    @endif

    <!-- Totals -->
    <table>
        @if($isExchange)
            @if($returnTotalCost > 0)
            <tr><th>Return Total:</th><td>-{{ number_format($returnTotalCost, 0) }}</td></tr>
            @endif
            @if($newTotalCost > 0)
            <tr><th>New Items Cost:</th><td>{{ number_format($newTotalCost, 0) }}</td></tr>
            @endif
            <tr><td colspan="2"><div class="line"></div></td></tr>
        @endif
        <tr><th>Total Item:</th><td>{{ $sale->total_items }}</td></tr>
        <tr><th>Gross Amount:</th><td>{{ number_format($sale->total_bill_amount ?? 0, 0) }}</td></tr>
        @if(!empty($sale->total_extradiscount) && $sale->total_extradiscount > 0)
        @php 
            $discPercent = ($sale->total_bill_amount > 0) ? ($sale->total_extradiscount / $sale->total_bill_amount * 100) : 0;
        @endphp
        <tr><th>Discount ({{ number_format($discPercent, 2) }}%):</th><td>{{ number_format($sale->total_extradiscount, 0) }}</td></tr>
        @endif
        <tr class="bold" style="font-size:12px;"><th>Net Amount:</th><td>{{ number_format($sale->total_net, 0) }}</td></tr>
        <tr><th>Cash Paid:</th><td>{{ number_format($sale->cash, 0) }}</td></tr>
        <tr><th>Cash Back:</th><td>{{ number_format($sale->change, 0) }}</td></tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        @if(!empty(sys_setting('receipt_note_1')))
            <p>{{ sys_setting('receipt_note_1') }}</p>
        @endif
        @if(!empty(sys_setting('receipt_note_2')))
            <p>{{ sys_setting('receipt_note_2') }}</p>
        @endif
        @if(!empty(sys_setting('invoice_footer_note')))
            <p>*** {{ sys_setting('invoice_footer_note') }} ***</p>
        @endif
        <div class="line" style="margin: 4px 0 3px;"></div>
        <p style="margin: 2px 0 1px; font-size: 10px;">Develop By: <strong>ProWave Technologies</strong></p>
        <p style="margin: 1px 0 0; font-size: 9.5px; font-weight: 700;">+92 317 3836223 | +92 325 9385085</p>
    </div>
</div>

<script>
    window.onload = function () {
        window.print();
        setTimeout(function () {
            window.close(); // Close the tab after printing dialog is closed
        }, 800);
    };
</script>
</body>
</html>
