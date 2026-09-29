@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --rv-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --rv-bg: #f8fafc;
    --rv-surface: #ffffff;
    --rv-border: #e2e8f0;
    --rv-primary: #059669;
    --rv-primary-dark: #047857;
    --rv-primary-light: #ecfdf5;
    --rv-accent: #0284c7;
    --rv-text-main: #0f172a;
    --rv-text-muted: #64748b;
    --rv-radius: 16px;
    --rv-radius-sm: 10px;
    --rv-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    --rv-shadow-lg: 0 12px 32px -4px rgba(15, 23, 42, 0.12);
}

.rv-page * {
    font-family: var(--rv-font);
}

.rv-page {
    background-color: var(--rv-bg);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ══════════ HERO HEADER ══════════ */
.rv-hero {
    position: relative;
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f766e 100%);
    border-radius: var(--rv-radius);
    padding: 1.6rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 35px -10px rgba(5, 150, 105, 0.35);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    overflow: hidden;
}

.rv-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 15% 90%, rgba(52, 211, 153, 0.3) 0%, transparent 60%),
        radial-gradient(circle at 85% 15%, rgba(56, 189, 248, 0.25) 0%, transparent 55%);
    pointer-events: none;
}

.rv-hero > * {
    position: relative;
    z-index: 1;
}

.rv-hero-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rv-hero-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.6rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.rv-hero-title h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.02em;
}

.rv-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 30px;
    padding: 0.25rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #a7f3d0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.rv-hero-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.rv-btn-create {
    background: #ffffff;
    color: #065f46 !important;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 0.65rem 1.35rem;
    border-radius: var(--rv-radius-sm);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}
.rv-btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
    background: #f0fdf4;
    color: #047857 !important;
}

/* ══════════ KPI METRIC CARDS ══════════ */
.rv-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.rv-kpi-card {
    background: var(--rv-surface);
    border: 1px solid var(--rv-border);
    border-radius: var(--rv-radius);
    padding: 1.25rem 1.35rem;
    box-shadow: var(--rv-shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.rv-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--rv-shadow-lg);
    border-color: #cbd5e1;
}

.rv-kpi-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}
.rv-kpi-card.emerald::after { background: #059669; }
.rv-kpi-card.sky::after { background: #0284c7; }
.rv-kpi-card.amber::after { background: #d97706; }
.rv-kpi-card.indigo::after { background: #6366f1; }

.rv-kpi-info h6 {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--rv-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.35rem;
}
.rv-kpi-info .rv-kpi-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--rv-text-main);
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.rv-kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    flex-shrink: 0;
}
.rv-kpi-card.emerald .rv-kpi-icon { background: #d1fae5; color: #059669; }
.rv-kpi-card.sky .rv-kpi-icon { background: #e0f2fe; color: #0284c7; }
.rv-kpi-card.amber .rv-kpi-icon { background: #fef3c7; color: #d97706; }
.rv-kpi-card.indigo .rv-kpi-icon { background: #e0e7ff; color: #6366f1; }

/* ══════════ FILTER & TABLE CONTAINER ══════════ */
.rv-card {
    background: var(--rv-surface);
    border: 1px solid var(--rv-border);
    border-radius: var(--rv-radius);
    box-shadow: var(--rv-shadow);
    overflow: hidden;
}

.rv-filter-bar {
    padding: 1.15rem 1.35rem;
    background: #ffffff;
    border-bottom: 1px solid var(--rv-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.rv-filter-group {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.rv-filter-control {
    border: 1.5px solid #e2e8f0;
    border-radius: var(--rv-radius-sm);
    padding: 0.45rem 0.85rem;
    font-size: 0.85rem;
    color: var(--rv-text-main);
    background: #f8fafc;
    transition: all 0.2s;
    min-height: 38px;
}
.rv-filter-control:focus {
    background: #ffffff;
    border-color: var(--rv-primary);
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    outline: none;
}

/* ══════════ TABLE STYLING ══════════ */
.rv-table-wrap {
    padding: 0;
}

table#voucherTable {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 !important;
}

table#voucherTable thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.95rem 1rem;
    border-bottom: 1.5px solid #e2e8f0;
    border-top: none;
    white-space: nowrap;
}

table#voucherTable tbody td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
    color: #1e293b;
}

table#voucherTable tbody tr:hover td {
    background-color: #f8fafc;
}

/* ══════════ BADGES & ELEMENTS ══════════ */
.rv-id-badge {
    font-family: 'SF Mono', Consolas, Monaco, monospace;
    font-size: 0.8rem;
    font-weight: 700;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 0.28rem 0.65rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.rv-type-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.rv-type-customer { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.rv-type-vendor { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.rv-type-account { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.rv-type-walkin { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
.rv-type-other { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.rv-party-name {
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.rv-amount {
    font-weight: 800;
    color: #059669;
    font-size: 0.95rem;
    text-align: right;
    white-space: nowrap;
}

.rv-date-text {
    font-size: 0.82rem;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.rv-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
}
.rv-action-print {
    background: #fee2e2;
    color: #dc2626;
}
.rv-action-print:hover {
    background: #dc2626;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
}

.rv-action-view {
    background: #e0f2fe;
    color: #0284c7;
}
.rv-action-view:hover {
    background: #0284c7;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
}

/* ══════════ DATA TABLES OVERRIDES ══════════ */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #059669 !important;
    border-color: #059669 !important;
    color: #ffffff !important;
    border-radius: 8px !important;
}
.dataTables_wrapper .dataTables_info, 
.dataTables_wrapper .dataTables_paginate {
    padding: 1rem 1.25rem;
    font-size: 0.85rem;
}

/* ══════════ MOBILE CARD VIEW & RESPONSIVENESS ══════════ */
@media (max-width: 991.98px) {
    .rv-page {
        padding-bottom: 2rem;
    }
    .rv-hero {
        padding: 1.25rem 1.15rem;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    .rv-hero-left {
        gap: 0.75rem;
    }
    .rv-hero-icon {
        width: 44px;
        height: 44px;
        font-size: 1.3rem;
        border-radius: 12px;
    }
    .rv-hero-title h2 {
        font-size: 1.3rem;
    }
    .rv-hero-actions {
        width: 100%;
    }
    .rv-btn-create {
        width: 100%;
        justify-content: center;
        padding: 0.75rem;
    }
    .rv-kpi-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .rv-kpi-card {
        padding: 1rem;
    }
    .rv-kpi-info h6 {
        font-size: 0.7rem;
    }
    .rv-kpi-info .rv-kpi-val {
        font-size: 1.15rem;
    }
    .rv-kpi-icon {
        width: 38px;
        height: 38px;
        font-size: 1.15rem;
    }
    .rv-filter-bar {
        padding: 1rem;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .rv-filter-group {
        flex-direction: column;
        align-items: stretch;
        gap: 0.65rem;
    }
    .rv-filter-group > div {
        width: 100%;
    }
    .rv-filter-control {
        width: 100%;
        min-height: 42px;
    }

    /* Transform Table to Clean Responsive Cards on Mobile */
    .rv-table-wrap {
        padding: 0.75rem !important;
        overflow: visible !important;
    }
    table#voucherTable {
        display: block !important;
        width: 100% !important;
        border: none !important;
    }
    table#voucherTable thead {
        display: none !important;
    }
    table#voucherTable tbody {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.85rem !important;
    }
    table#voucherTable tbody tr {
        display: block !important;
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        padding: 1rem !important;
        position: relative !important;
        overflow: hidden !important;
    }
    table#voucherTable tbody tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: #059669;
    }
    table#voucherTable tbody td {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 0.4rem 0 !important;
        border-bottom: 1px dashed #f1f5f9 !important;
        font-size: 0.84rem !important;
    }
    table#voucherTable tbody td:last-child {
        border-bottom: none !important;
        padding-top: 0.65rem !important;
    }
    table#voucherTable tbody td::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    table#voucherTable tbody td[data-label="ID"] {
        display: none !important;
    }
    table#voucherTable tbody td[data-label="Total Amount"] {
        background: #ecfdf5;
        margin: 0.4rem -1rem 0;
        padding: 0.65rem 1rem !important;
        border-radius: 0;
        border-bottom: none !important;
    }
    table#voucherTable tbody td[data-label="Total Amount"]::before {
        color: #065f46;
        font-weight: 800;
    }
    table#voucherTable tbody td[data-label="Total Amount"] .rv-amount {
        font-size: 1.15rem;
    }
    table#voucherTable tbody td[data-label="Action"] {
        justify-content: stretch !important;
    }
    table#voucherTable tbody td[data-label="Action"]::before {
        display: none !important;
    }
    table#voucherTable tbody td[data-label="Action"] .rv-action-print {
        width: 100%;
        min-height: 40px;
        border-radius: 10px;
        font-weight: 700;
        gap: 0.5rem;
    }
    table#voucherTable tbody td[data-label="Action"] .rv-action-print::after {
        content: ' Print Voucher';
        font-size: 0.85rem;
    }
}

@media (max-width: 575.98px) {
    .rv-kpi-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="main-content rv-page">
    <div class="container-fluid px-3 px-md-4 py-3">

        {{-- ══════════ HERO BANNER ══════════ --}}
        <div class="rv-hero">
            <div class="rv-hero-left">
                <div class="rv-hero-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="rv-hero-title">
                    <h2>Receipts Vouchers</h2>
                    <span class="rv-hero-badge">
                        <i class="bi bi-shield-check"></i> Inward Financial Receipts
                    </span>
                </div>
            </div>

            <div class="rv-hero-actions">
                <a href="{{ route('recepit-vochers') }}" class="rv-btn-create">
                    <i class="bi bi-plus-circle-fill"></i> Add Receipt Voucher
                </a>
            </div>
        </div>

        {{-- ══════════ KPI METRIC CARDS ══════════ --}}
        @php
            $totalCount = $receipts->count();
            $totalSum = $receipts->sum(function($r) {
                return (float)$r->total_amount;
            });
            $todayReceipts = $receipts->filter(function($r) {
                return substr($r->receipt_date ?? $r->entry_date ?? '', 0, 10) === date('Y-m-d');
            });
            $todayCount = $todayReceipts->count();
            $todaySum = $todayReceipts->sum(function($r) {
                return (float)$r->total_amount;
            });
        @endphp

        <div class="rv-kpi-grid">
            <div class="rv-kpi-card emerald">
                <div class="rv-kpi-info">
                    <h6>Total Receipts</h6>
                    <div class="rv-kpi-val">{{ number_format($totalCount) }}</div>
                </div>
                <div class="rv-kpi-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>

            <div class="rv-kpi-card sky">
                <div class="rv-kpi-info">
                    <h6>Total Received</h6>
                    <div class="rv-kpi-val">Rs. {{ number_format($totalSum, 2) }}</div>
                </div>
                <div class="rv-kpi-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>

            <div class="rv-kpi-card amber">
                <div class="rv-kpi-info">
                    <h6>Today's Receipts</h6>
                    <div class="rv-kpi-val">{{ number_format($todayCount) }} <span style="font-size:0.8rem; font-weight:600; color:#64748b;">vouchers</span></div>
                </div>
                <div class="rv-kpi-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>

            <div class="rv-kpi-card indigo">
                <div class="rv-kpi-info">
                    <h6>Today's Received Amount</h6>
                    <div class="rv-kpi-val">Rs. {{ number_format($todaySum, 2) }}</div>
                </div>
                <div class="rv-kpi-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>

        {{-- ══════════ MAIN CARD WITH DATATABLE ══════════ --}}
        <div class="rv-card">
            {{-- Quick Filter Toolbar --}}
            <div class="rv-filter-bar">
                <div class="rv-filter-group">
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold text-uppercase" style="letter-spacing:0.04em;">From:</label>
                        <input type="date" id="filterFromDate" class="rv-filter-control">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold text-uppercase" style="letter-spacing:0.04em;">To:</label>
                        <input type="date" id="filterToDate" class="rv-filter-control">
                    </div>
                    <select id="filterType" class="rv-filter-control">
                        <option value="">All Types</option>
                        <option value="Customer">Customer</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Walk-in">Walk-in</option>
                        <option value="Account">Account</option>
                    </select>
                    <button id="resetFilters" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="height:38px; border-radius:10px;">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>

                <div class="text-muted small">
                    Showing <strong id="visibleCount">{{ $totalCount }}</strong> entries
                </div>
            </div>

            {{-- Table Container --}}
            <div class="table-responsive rv-table-wrap">
                <table id="voucherTable" class="table">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th style="width:130px;">Voucher No</th>
                            <th style="width:120px;">Receipt Date</th>
                            <th style="width:110px;">Type</th>
                            <th>Party / Beneficiary</th>
                            <th>Reference No</th>
                            <th>Remarks</th>
                            <th class="text-end" style="width:140px;">Total Amount</th>
                            <th style="width:140px;">Created At</th>
                            <th class="text-center" style="width:90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $item)
                        @php
                            $amounts = json_decode($item->amount, true);
                            $amount = is_array($amounts) ? (float)($amounts[0] ?? 0) : (float)$item->amount;

                            $refs = json_decode($item->reference_no, true);
                            $reference = is_array($refs) ? implode(', ', array_filter($refs)) : $item->reference_no;

                            $type = strtolower($item->type_label ?? '');
                            $typeClass = 'rv-type-other';
                            if (str_contains($type, 'customer')) $typeClass = 'rv-type-customer';
                            elseif (str_contains($type, 'vendor')) $typeClass = 'rv-type-vendor';
                            elseif (str_contains($type, 'walk')) $typeClass = 'rv-type-walkin';
                            elseif (str_contains($type, 'account') || !empty($item->type_label)) $typeClass = 'rv-type-account';
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold" data-label="ID">{{ $item->id }}</td>
                            <td data-label="Voucher No">
                                <span class="rv-id-badge">
                                    <i class="bi bi-receipt"></i> {{ $item->rvid }}
                                </span>
                            </td>
                            <td data-label="Receipt Date">
                                <span class="rv-date-text">
                                    <i class="bi bi-calendar3 text-muted"></i> {{ $item->receipt_date ?? $item->entry_date }}
                                </span>
                            </td>
                            <td data-label="Type">
                                <span class="rv-type-badge {{ $typeClass }}">
                                    {{ $item->type_label }}
                                </span>
                            </td>
                            <td data-label="Party">
                                <div class="rv-party-name">
                                    <i class="bi bi-person-circle text-muted"></i>
                                    <span>{{ $item->party_name ?: '—' }}</span>
                                </div>
                            </td>
                            <td data-label="Reference">
                                <span class="text-muted">{{ $reference ?: '—' }}</span>
                            </td>
                            <td data-label="Remarks">
                                <span class="text-muted text-truncate d-inline-block" style="max-width:180px;" title="{{ $item->remarks }}">
                                    {{ $item->remarks ?: '—' }}
                                </span>
                            </td>
                            <td class="text-end" data-label="Total Amount">
                                <span class="rv-amount">Rs. {{ number_format((float)$item->total_amount, 2) }}</span>
                            </td>
                            <td data-label="Created At">
                                <span class="small text-muted">{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </td>
                            <td class="text-center" data-label="Action">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('receiptVoucher.print', $item->id) }}"
                                       target="_blank"
                                       class="rv-action-btn rv-action-print"
                                       title="Print Voucher">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#voucherTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search receipts...",
            lengthMenu: "Show _MENU_ entries"
        },
        dom: "<'row px-3 pt-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row px-3 pb-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Custom filtering function for date range and type
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const fromDate = $('#filterFromDate').val();
        const toDate = $('#filterToDate').val();
        const typeFilter = $('#filterType').val().toLowerCase();

        const rowDate = data[2] ? data[2].trim() : '';
        const rowType = data[3] ? data[3].trim().toLowerCase() : '';

        // Date check
        if (fromDate && rowDate < fromDate) return false;
        if (toDate && rowDate > toDate) return false;

        // Type check
        if (typeFilter && !rowType.includes(typeFilter)) return false;

        return true;
    });

    $('#filterFromDate, #filterToDate, #filterType').on('change', function() {
        table.draw();
        updateCount();
    });

    $('#resetFilters').on('click', function() {
        $('#filterFromDate').val('');
        $('#filterToDate').val('');
        $('#filterType').val('');
        table.search('').draw();
        updateCount();
    });

    function updateCount() {
        const info = table.page.info();
        $('#visibleCount').text(info.recordsDisplay);
    }
    table.on('draw', updateCount);
});
</script>
@endsection