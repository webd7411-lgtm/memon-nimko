@extends('admin_panel.layout.app')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-stk { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2.5rem; }
    .rp-stk * { font-family: inherit; }

    .rp-hdr {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #164e63 0%, #0891b2 100%);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(22, 78, 99, .2);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #67e8f9; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .72); font-size: .85rem; font-weight: 500; }
    .rp-hdr .rp-btn-ghost { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); color: #fff; }
    .rp-hdr .rp-btn-ghost:hover { background: rgba(255,255,255,.18); color: #fff; }

    .rp-card {
        background: #fff;
        border: 1px solid #e9edf2;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05);
        margin-bottom: 1.2rem;
        overflow: hidden;
    }
    .rp-card-body { padding: 1.4rem; }

    .rp-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; margin-bottom: .35rem; display: block; }
    .rp-input, .rp-select {
        border: 1.5px solid #e9edf2;
        border-radius: 10px;
        padding: .5rem .8rem;
        font-size: .85rem;
        font-weight: 600;
        color: #0b1a33;
        background: #fff;
        width: 100%;
        outline: none;
        transition: all .2s ease;
        height: auto;
    }
    .rp-input:focus, .rp-select:focus { border-color: #0891b2; box-shadow: 0 0 0 3px rgba(8, 145, 178, .1); }

    .rp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border: none;
        border-radius: 10px;
        padding: .55rem 1.1rem;
        font-size: .83rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .22s ease;
        min-height: 42px;
        text-decoration: none;
    }
    .rp-btn-primary { background: linear-gradient(135deg, #0891b2, #0e7490); color: #fff; box-shadow: 0 6px 18px rgba(8, 145, 178, .28); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(8, 145, 178, .34); color: #fff; }
    .rp-btn-dark { background: #0f172a; color: #fff; }
    .rp-btn-dark:hover { transform: translateY(-1px); color: #fff; }
    .rp-btn-ghost { background: #fff; border: 1.5px solid #e9edf2; color: #54657e; }
    .rp-btn-ghost:hover { border-color: #0891b2; color: #0891b2; }

    /* Summary cards */
    .rp-stat { border-radius: 14px; padding: 1.1rem 1.3rem; position: relative; overflow: hidden; }
    .rp-stat::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .rp-stat .val { font-size: 1.6rem; font-weight: 900; line-height: 1.1; }
    .rp-stat .lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-stat.s-inc { background: #f0fdf4; border: 1px solid #bbf7d0; } .rp-stat.s-inc::after { background: linear-gradient(180deg,#22c55e,#4ade80); } .rp-stat.s-inc .val { color: #15803d; }
    .rp-stat.s-dec { background: #fef2f2; border: 1px solid #fecaca; } .rp-stat.s-dec::after { background: linear-gradient(180deg,#ef4444,#f87171); } .rp-stat.s-dec .val { color: #b91c1c; }
    .rp-stat.s-tot { background: #eff6ff; border: 1px solid #bfdbfe; } .rp-stat.s-tot::after { background: linear-gradient(180deg,#2563eb,#60a5fa); } .rp-stat.s-tot .val { color: #1d4ed8; }
    .rp-stat.s-range { background: #fafafa; border: 1px solid #e9edf2; } .rp-stat.s-range::after { background: linear-gradient(180deg,#64748b,#94a3b8); } .rp-stat.s-range .val { font-size: .95rem; color: #334155; }

    /* Table */
    .rp-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
    .rp-table thead th {
        background: #0f172a;
        font-size: .66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        padding: .65rem .7rem;
        border-bottom: 2px solid #0f172a;
        text-align: left;
        white-space: nowrap;
    }
    .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-table tbody td { padding: .6rem .7rem; border-bottom: 1px solid #f1f4f9; vertical-align: middle; color: #1e293b; }
    .rp-table tbody tr:hover td { background: #fafbfc; }
    .rp-table tbody tr:last-child td { border-bottom: none; }
    .rp-table .rp-badge { display: inline-flex; align-items: center; gap: 3px; font-size: .68rem; font-weight: 700; letter-spacing: .3px; padding: .2rem .6rem; border-radius: 6px; }
    .rp-badge-inc { background: #dcfce7; color: #15803d; }
    .rp-badge-dec { background: #fee2e2; color: #b91c1c; }

    /* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
    .rp-tbl-wrap { overflow-x: hidden; }
    .rp-table { table-layout: fixed; width: 100% !important; }
    .rp-table thead th { white-space: normal !important; overflow-wrap: break-word; }
    .rp-table tbody td { white-space: normal !important; overflow-wrap: break-word; word-break: normal; }

    /* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
    .sak-cards { display: none; }

    .sak-card {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05); margin-bottom: .85rem; overflow: hidden;
    }
    .sak-head {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        padding: .8rem .9rem .7rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e9edf2;
    }
    .sak-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
    .sak-ref { font-size: .95rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: normal; overflow-wrap: break-word; line-height: 1.25; }
    .sak-ref i { color: #0891b2; font-size: .85rem; margin-right: 5px; }
    .sak-sub { display: flex; align-items: center; gap: 5px; font-size: .73rem; color: #64748b; font-weight: 600; word-break: normal; overflow-wrap: break-word; }
    .sak-sub i { color: #94a3b8; font-size: .8rem; }
    .sak-badge {
        flex: 0 0 auto; display: inline-flex; align-items: center; gap: 4px;
        border-radius: 20px; padding: .28rem .7rem; font-size: .68rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
    }
    .sak-badge.increase { background: #e7f9f0; color: #0f7a4d; border: 1px solid #b9ecd2; }
    .sak-badge.decrease { background: #fef2f2; color: #a72d2d; border: 1px solid #f3c9c9; }

    .sak-product { padding: .7rem .9rem .35rem; }
    .sak-product-row { display: flex; align-items: center; gap: .45rem; flex-wrap: wrap; }
    .sak-product-nm { flex: 1 1 auto; min-width: 0; font-size: .9rem; font-weight: 700; color: #0f172a; word-break: normal; overflow-wrap: break-word; line-height: 1.35; }
    .sak-product-v { font-size: .66rem; font-weight: 700; color: #0e7490; background: #ecfeff; border: 1px solid #cffafe; border-radius: 7px; padding: .12rem .45rem; white-space: nowrap; }
    .sak-product-code { display: inline-block; margin-top: 4px; font-size: .68rem; font-weight: 700; color: #64748b; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 6px; padding: .12rem .5rem; }

    .sak-meta { display: flex; flex-wrap: wrap; gap: .4rem; padding: .4rem .9rem .55rem; }
    .sak-meta span {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .72rem; color: #475569; font-weight: 600;
        background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px;
        padding: .28rem .6rem; word-break: normal; overflow-wrap: break-word; line-height: 1.3;
    }
    .sak-meta span i { color: #0891b2; font-size: .76rem; }

    .sak-qty-row { display: flex; gap: .6rem; margin: .1rem .9rem .7rem; }
    .sak-qty { flex: 1 1 0; min-width: 0; padding: .55rem .7rem; border-radius: 12px; border: 1px solid #e9edf2; background: #f8fafc; }
    .sak-qty span { display: block; font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-bottom: .15rem; }
    .sak-qty b { font-size: .95rem; font-weight: 900; color: #0f172a; word-break: normal; overflow-wrap: break-word; }
    .sak-qty.sak-up { border-color: #bbf7d0; background: #f0fdf4; }
    .sak-qty.sak-up b { color: #15803d; }
    .sak-qty.sak-down { border-color: #fecaca; background: #fef2f2; }
    .sak-qty.sak-down b { color: #b91c1c; }

    .sak-note {
        margin: 0 .9rem .7rem; padding: .5rem .7rem;
        background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
        font-size: .74rem; color: #92400e; font-weight: 600;
        display: flex; align-items: flex-start; gap: 6px;
        word-break: normal; overflow-wrap: break-word;
    }
    .sak-note i { color: #f59e0b; margin-top: 2px; }

    .sak-empty {
        text-align: center; padding: 2.5rem 1rem; color: #64748b;
        display: flex; flex-direction: column; align-items: center; gap: .4rem;
    }
    .sak-empty i { font-size: 2.2rem; color: #cbd5e1; }

    @media (max-width: 991.98px) {
        .rp-stk { overflow-x: hidden; }
        .rp-stk, .rp-stk .container-fluid { max-width: 100%; }

        /* Filters stack full width, no overflow */
        .rp-card-body form.row > div { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
        .rp-card-body form .col-auto { padding-left: 0; padding-right: 0; }
        .rp-card-body form .d-flex { flex-wrap: wrap; width: 100%; }
        .rp-btn { flex: 1 1 auto; justify-content: center; min-height: 44px; }

        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: stretch; gap: .7rem; }
        .rp-hdr h2 { font-size: 1.12rem; }
        .rp-hdr p { font-size: .8rem; }

        .rp-card-body { padding: 1rem; }
        .rp-stat { padding: .9rem 1rem; }
        .rp-stat .val { font-size: 1.3rem; word-break: break-word; }

        /* Hide table on mobile, render premium cards instead */
        #adjReportTable { display: none !important; }
        .sak-cards { display: block; padding: .5rem .6rem 1rem; }
        .rp-tbl-wrap { overflow: visible !important; padding: 0; }
    }

    @media (max-width: 575.98px) {
        .rp-card-body { padding: 1rem; }
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: stretch; }
        .rp-hdr h2 { font-size: 1.12rem; }
        .rp-hdr p { font-size: .78rem; }
        .rp-btn { width: 100%; }
        .rp-stat { padding: .9rem 1rem; }
        .rp-stat .val { font-size: 1.3rem; }
        .rp-stat .val { word-break: break-word; font-size: .95rem; }
    }

    .print-title { display: none; }

    @media print {
        @page { size: A4; margin: 10mm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; background: #fff; }
        .no-print, .rp-hdr, .rp-card:first-child, .rp-page > .container-fluid > .row { display: none !important; }
        .print-title { display: block !important; }
        .pt-header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #000; margin-bottom: 12px; }
        .pt-header .pt-logo { max-height: 55px; margin-bottom: 4px; }
        .pt-header h1 { font-size: 16px; font-weight: 800; margin: 0 0 2px; }
        .pt-header p { font-size: 10px; color: #000; margin: 0; }
        .pt-info { display: flex; justify-content: space-between; font-size: 9px; font-weight: 600; margin-bottom: 8px; padding: 4px 8px; border: 1px solid #000; background: #fff; }
        .rp-page { background: #fff !important; padding: 0 !important; margin: 0 !important; min-height: auto !important; }
        .rp-page > .container-fluid { padding: 0 !important; margin: 0 !important; }
        .rp-card { box-shadow: none !important; border: none !important; margin: 0 !important; }
        .rp-card-body.p-0 { padding: 0 !important; }
        .rp-table { width: 100% !important; border-collapse: collapse !important; font-size: 9px !important; }
        .rp-table thead th { background: #000 !important; color: #fff !important; padding: 4px 6px !important; font-size: 8px !important; text-transform: uppercase !important; border: 1px solid #000 !important; text-align: center !important; }
        .rp-table tbody td { padding: 4px 6px !important; border: 1px solid #000 !important; vertical-align: middle !important; text-align: center !important; }
        .rp-table tbody tr:nth-child(even) td { background: #fff !important; }
        .rp-table tbody tr:last-child td { border-bottom: 1px solid #000 !important; }
        .rp-badge { display: inline-block; padding: 1px 4px; border-radius: 2px; font-weight: 600; font-size: 7px; background: #000 !important; color: #fff !important; }
        .text-success { color: #000 !important; font-weight: bold; }
        .text-danger { color: #000 !important; font-weight: bold; }
        .text-muted { color: #000 !important; }
        .fw-bold { font-weight: bold !important; }
        .text-center { text-align: center !important; }
        .table-dark thead th { background: #000 !important; }
        .table-responsive { overflow: visible !important; }
        .pt-footer { text-align: center; font-size: 8px; color: #000; margin-top: 10px; padding-top: 4px; border-top: 1px solid #000; }
        .py-4 { padding: 12px !important; }
        small { font-size: 9px !important; display: block; color: #000 !important; }
    }
</style>

<div class="rp-page rp-stk">
    <div class="container-fluid px-3 px-md-4 py-3">
        <div class="print-title" style="display:none;">
            <div class="pt-header">
                <img src="{{ str_replace('\\', '/', public_path('assets/images/logo.png')) }}" alt="Logo" class="pt-logo">
                <h1>STOCK ADJUSTMENT REPORT</h1>
                <p>Period: {{ $from }} to {{ $to }} &nbsp;|&nbsp; Generated: {{ now()->format('d-M-Y h:i A') }}</p>
            </div>
            <div class="pt-info">
                <span><strong>Type:</strong> {{ $type ? ucfirst($type) : 'All' }}</span>
                <span><strong>Total Entries:</strong> {{ $rows->count() }}</span>
                <span><strong>Increase:</strong> {{ $rows->where('type','increase')->count() }}</span>
                <span><strong>Decrease:</strong> {{ $rows->where('type','decrease')->count() }}</span>
            </div>
        </div>
        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-sliders"></i> Stock Adjustment Report</h2>
                <p>Review stock increase / decrease adjustments for a period</p>
            </div>
            <a href="{{ route('stock-adjustment.index') }}" class="rp-btn rp-btn-ghost no-print"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        {{-- FILTERS --}}
        <div class="rp-card no-print">
            <div class="rp-card-body py-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="rp-label">From Date</label>
                        <input type="date" name="from_date" value="{{ $from }}" class="rp-input">
                    </div>
                    <div class="col-auto">
                        <label class="rp-label">To Date</label>
                        <input type="date" name="to_date" value="{{ $to }}" class="rp-input">
                    </div>
                    <div class="col-auto">
                        <label class="rp-label">Type</label>
                        <select name="type" class="rp-select">
                            <option value="">All</option>
                            <option value="increase" {{ $type=='increase'?'selected':'' }}>➕ Increase</option>
                            <option value="decrease" {{ $type=='decrease'?'selected':'' }}>➖ Decrease</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2 flex-wrap">
                        <button type="submit" class="rp-btn rp-btn-primary"><i class="bi bi-search"></i> Search</button>
                        <button type="button" onclick="window.print()" class="rp-btn rp-btn-dark no-print"><i class="bi bi-printer"></i> Print</button>
                        <a href="{{ route('stock-adjustment.report') }}" class="rp-btn rp-btn-ghost no-print"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $totalInc = $rows->where('type','increase')->sum('qty');
            $totalDec = $rows->where('type','decrease')->sum('qty');
            $totalRows = $rows->count();
        @endphp
        <div class="row g-3 mb-4 no-print">
            <div class="col-6 col-md-3">
                <div class="rp-stat s-inc">
                    <div class="val">{{ $rows->where('type','increase')->count() }}</div>
                    <div class="lbl">Increase Entries</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-dec">
                    <div class="val">{{ $rows->where('type','decrease')->count() }}</div>
                    <div class="lbl">Decrease Entries</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-tot">
                    <div class="val">{{ $totalRows }}</div>
                    <div class="lbl">Total Line Items</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-range">
                    <div class="val">{{ $from }} → {{ $to }}</div>
                    <div class="lbl">Date Range</div>
                </div>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive rp-tbl-wrap">
                    <table class="rp-table mb-0" id="adjReportTable">
                        <colgroup>
                            <col style="width:3%"><col style="width:8%"><col style="width:7%"><col style="width:6%"><col style="width:12%"><col style="width:20%"><col style="width:7%"><col style="width:9%"><col style="width:10%"><col style="width:9%"><col style="width:9%">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Ref #</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Product</th>
                                <th>Code</th>
                                <th>Qty Entered</th>
                                <th>Stock Change</th>
                                <th>By</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $i => $row)
                            @php
                                $isKg = $row->unit_type === 'kg';
                                $qty = (float)$row->qty;
                                if ($isKg) {
                                    $kg = floor($qty); $gm = round(($qty - $kg) * 1000);
                                    $qtyFmt = ($kg > 0 ? $kg.'kg ' : '') . ($gm > 0 ? $gm.'g' : ($kg > 0 ? '' : '0g'));
                                } else {
                                    $qtyFmt = number_format($qty, 0) . ' ' . $row->unit;
                                }
                                $sFmt = $isKg
                                    ? number_format($row->qty_stock, 0) . 'g'
                                    : number_format($row->qty_stock, 0) . ' ' . $row->unit;
                            @endphp
                            <tr>
                                <td class="rp-num">{{ $i + 1 }}</td>
                                <td data-label="Ref #"><strong>{{ $row->ref_no }}</strong></td>
                                <td data-label="Date">{{ \Carbon\Carbon::parse($row->adjustment_date)->format('d-M-Y') }}</td>
                                <td data-label="Type">
                                    @if($row->type === 'increase')
                                        <span class="rp-badge rp-badge-inc">➕ Inc</span>
                                    @else
                                        <span class="rp-badge rp-badge-dec">➖ Dec</span>
                                    @endif
                                </td>
                                <td data-label="Reason">{{ $row->reason }}</td>
                                <td data-label="Product">
                                    <strong>{{ $row->item_name }}</strong>
                                    @if($row->size_label || $row->variant_name)
                                        <br><small class="text-muted">({{ $row->size_label ?: $row->variant_name }})</small>
                                    @endif
                                </td>
                                <td data-label="Code">{{ $row->item_code }}</td>
                                <td data-label="Qty Entered"><strong>{{ $qtyFmt }}</strong></td>
                                <td data-label="Stock Change">
                                    @if($row->type === 'increase')
                                        <span class="text-success fw-bold">+{{ $sFmt }}</span>
                                    @else
                                        <span class="text-danger fw-bold">-{{ $sFmt }}</span>
                                    @endif
                                </td>
                                <td data-label="By">{{ $row->user_name ?? '-' }}</td>
                                <td data-label="Note">{{ $row->item_note ?? $row->adj_note ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr class="rp-tbl-empty">
                                <td colspan="11" class="text-center py-4 text-muted">No records found for selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ --}}
                <div class="sak-cards">
                    @foreach($rows as $i => $row)
                    @php
                        $isKg = $row->unit_type === 'kg';
                        $qty = (float)$row->qty;
                        if ($isKg) {
                            $kg = floor($qty); $gm = round(($qty - $kg) * 1000);
                            $qtyFmt = ($kg > 0 ? $kg.'kg ' : '') . ($gm > 0 ? $gm.'g' : ($kg > 0 ? '' : '0g'));
                        } else {
                            $qtyFmt = number_format($qty, 0) . ' ' . $row->unit;
                        }
                        $sFmt = $isKg
                            ? number_format($row->qty_stock, 0) . 'g'
                            : number_format($row->qty_stock, 0) . ' ' . $row->unit;
                    @endphp
                    <div class="sak-card">
                        <div class="sak-head">
                            <div class="sak-head-l">
                                <span class="sak-ref"><i class="bi bi-upc-scan"></i>{{ $row->ref_no }}</span>
                                <span class="sak-sub"><i class="bi bi-calendar3"></i>{{ \Carbon\Carbon::parse($row->adjustment_date)->format('d-M-Y') }}</span>
                            </div>
                            <span class="sak-badge {{ $row->type === 'increase' ? 'increase' : 'decrease' }}"><i class="bi bi-{{ $row->type === 'increase' ? 'plus-circle' : 'dash-circle' }}"></i>{{ $row->type === 'increase' ? 'Inc' : 'Dec' }}</span>
                        </div>
                        <div class="sak-product">
                            <div class="sak-product-row">
                                <span class="sak-product-nm">{{ $row->item_name }}</span>
                                @if($row->size_label || $row->variant_name)
                                <span class="sak-product-v">{{ $row->size_label ?: $row->variant_name }}</span>
                                @endif
                            </div>
                            <span class="sak-product-code">{{ $row->item_code }}</span>
                        </div>
                        <div class="sak-meta">
                            <span><i class="bi bi-person"></i>{{ $row->user_name ?? '-' }}</span>
                            @if($row->reason)
                            <span><i class="bi bi-chat-left-text"></i>{{ $row->reason }}</span>
                            @endif
                        </div>
                        <div class="sak-qty-row">
                            <div class="sak-qty">
                                <span>Qty Entered</span>
                                <b>{{ $qtyFmt }}</b>
                            </div>
                            <div class="sak-qty {{ $row->type === 'increase' ? 'sak-up' : 'sak-down' }}">
                                <span>Stock Change</span>
                                <b>{{ $row->type === 'increase' ? '+' : '-' }}{{ $sFmt }}</b>
                            </div>
                        </div>
                        @if($row->item_note || $row->adj_note)
                        <div class="sak-note"><i class="bi bi-journal-text"></i> {{ $row->item_note ?? $row->adj_note }}</div>
                        @endif
                    </div>
                    @endforeach
                    @if($rows->isEmpty())
                        <div class="sak-empty"><i class="bi bi-inbox"></i><span>No records found for selected period.</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-footer" style="display:none;">Memon Nimko — Stock Adjustment Report</div>

    </div>
</div>
@endsection
