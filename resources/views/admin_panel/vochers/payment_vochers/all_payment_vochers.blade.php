@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --pv-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --pv-bg: #f8fafc;
    --pv-surface: #ffffff;
    --pv-border: #e2e8f0;
    --pv-primary: #dc2626;
    --pv-primary-dark: #b91c1c;
    --pv-primary-light: #fef2f2;
    --pv-accent: #f59e0b;
    --pv-text-main: #0f172a;
    --pv-text-muted: #64748b;
    --pv-radius: 16px;
    --pv-radius-sm: 10px;
    --pv-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    --pv-shadow-lg: 0 12px 32px -4px rgba(15, 23, 42, 0.12);
}

.pv-page * {
    font-family: var(--pv-font);
}

.pv-page {
    background-color: var(--pv-bg);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ══════════ HERO HEADER ══════════ */
.pv-hero {
    position: relative;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #881337 100%);
    border-radius: var(--pv-radius);
    padding: 1.6rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 35px -10px rgba(136, 19, 55, 0.35);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    overflow: hidden;
}

.pv-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 15% 90%, rgba(244, 63, 94, 0.3) 0%, transparent 60%),
        radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.25) 0%, transparent 55%);
    pointer-events: none;
}

.pv-hero > * {
    position: relative;
    z-index: 1;
}

.pv-hero-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pv-hero-icon {
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

.pv-hero-title h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.02em;
}

.pv-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 30px;
    padding: 0.25rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fecdd3;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.pv-hero-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.pv-btn-create {
    background: #ffffff;
    color: #881337 !important;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 0.65rem 1.35rem;
    border-radius: var(--pv-radius-sm);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}
.pv-btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
    background: #fff1f2;
    color: #9f1239 !important;
}

/* ══════════ KPI METRIC CARDS ══════════ */
.pv-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.pv-kpi-card {
    background: var(--pv-surface);
    border: 1px solid var(--pv-border);
    border-radius: var(--pv-radius);
    padding: 1.25rem 1.35rem;
    box-shadow: var(--pv-shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.pv-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--pv-shadow-lg);
    border-color: #cbd5e1;
}

.pv-kpi-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}
.pv-kpi-card.rose::after { background: #e11d48; }
.pv-kpi-card.amber::after { background: #d97706; }
.pv-kpi-card.violet::after { background: #7c3aed; }
.pv-kpi-card.slate::after { background: #475569; }

.pv-kpi-info h6 {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--pv-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.35rem;
}
.pv-kpi-info .pv-kpi-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--pv-text-main);
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.pv-kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    flex-shrink: 0;
}
.pv-kpi-card.rose .pv-kpi-icon { background: #ffe4e6; color: #e11d48; }
.pv-kpi-card.amber .pv-kpi-icon { background: #fef3c7; color: #d97706; }
.pv-kpi-card.violet .pv-kpi-icon { background: #ede9fe; color: #7c3aed; }
.pv-kpi-card.slate .pv-kpi-icon { background: #f1f5f9; color: #475569; }

/* ══════════ FILTER & TABLE CONTAINER ══════════ */
.pv-card {
    background: var(--pv-surface);
    border: 1px solid var(--pv-border);
    border-radius: var(--pv-radius);
    box-shadow: var(--pv-shadow);
    overflow: hidden;
}

.pv-filter-bar {
    padding: 1.15rem 1.35rem;
    background: #ffffff;
    border-bottom: 1px solid var(--pv-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.pv-filter-group {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.pv-filter-control {
    border: 1.5px solid #e2e8f0;
    border-radius: var(--pv-radius-sm);
    padding: 0.45rem 0.85rem;
    font-size: 0.85rem;
    color: var(--pv-text-main);
    background: #f8fafc;
    transition: all 0.2s;
    min-height: 38px;
}
.pv-filter-control:focus {
    background: #ffffff;
    border-color: #e11d48;
    box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.12);
    outline: none;
}

/* ══════════ TABLE STYLING ══════════ */
.pv-table-wrap {
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
.pv-id-badge {
    font-family: 'SF Mono', Consolas, Monaco, monospace;
    font-size: 0.8rem;
    font-weight: 700;
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    padding: 0.28rem 0.65rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.pv-type-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.pv-type-vendor { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.pv-type-customer { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.pv-type-account { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.pv-type-walkin { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
.pv-type-other { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.pv-party-name {
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.pv-amount {
    font-weight: 800;
    color: #be123c;
    font-size: 0.95rem;
    text-align: right;
    white-space: nowrap;
}

.pv-date-text {
    font-size: 0.82rem;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.pv-action-btn {
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
.pv-action-print {
    background: #fee2e2;
    color: #dc2626;
}
.pv-action-print:hover {
    background: #dc2626;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
}

/* ══════════ DATA TABLES OVERRIDES ══════════ */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #be123c !important;
    border-color: #be123c !important;
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
    .pv-page {
        padding-bottom: 2rem;
    }
    .pv-hero {
        padding: 1.25rem 1.15rem;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    .pv-hero-left {
        gap: 0.75rem;
    }
    .pv-hero-icon {
        width: 44px;
        height: 44px;
        font-size: 1.3rem;
        border-radius: 12px;
    }
    .pv-hero-title h2 {
        font-size: 1.3rem;
    }
    .pv-hero-actions {
        width: 100%;
    }
    .pv-btn-create {
        width: 100%;
        justify-content: center;
        padding: 0.75rem;
    }
    .pv-kpi-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .pv-kpi-card {
        padding: 1rem;
    }
    .pv-kpi-info h6 {
        font-size: 0.7rem;
    }
    .pv-kpi-info .pv-kpi-val {
        font-size: 1.15rem;
    }
    .pv-kpi-icon {
        width: 38px;
        height: 38px;
        font-size: 1.15rem;
    }
    .pv-filter-bar {
        padding: 1rem;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .pv-filter-group {
        flex-direction: column;
        align-items: stretch;
        gap: 0.65rem;
    }
    .pv-filter-group > div {
        width: 100%;
    }
    .pv-filter-control {
        width: 100%;
        min-height: 42px;
    }

    /* Transform Table to Clean Responsive Cards on Mobile */
    .pv-table-wrap {
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
        background: #e11d48;
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
        background: #fff1f2;
        margin: 0.4rem -1rem 0;
        padding: 0.65rem 1rem !important;
        border-radius: 0;
        border-bottom: none !important;
    }
    table#voucherTable tbody td[data-label="Total Amount"]::before {
        color: #9f1239;
        font-weight: 800;
    }
    table#voucherTable tbody td[data-label="Total Amount"] .pv-amount {
        font-size: 1.15rem;
    }
    table#voucherTable tbody td[data-label="Action"] {
        justify-content: stretch !important;
    }
    table#voucherTable tbody td[data-label="Action"]::before {
        display: none !important;
    }
    table#voucherTable tbody td[data-label="Action"] .pv-action-print {
        width: 100%;
        min-height: 40px;
        border-radius: 10px;
        font-weight: 700;
        gap: 0.5rem;
    }
    table#voucherTable tbody td[data-label="Action"] .pv-action-print::after {
        content: ' Print Voucher';
        font-size: 0.85rem;
    }
}

@media (max-width: 575.98px) {
    .pv-kpi-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="main-content pv-page">
    <div class="container-fluid px-3 px-md-4 py-3">

        {{-- ══════════ HERO BANNER ══════════ --}}
        <div class="pv-hero">
            <div class="pv-hero-left">
                <div class="pv-hero-icon">
                    <i class="bi bi-credit-card-2-back-fill"></i>
                </div>
                <div class="pv-hero-title">
                    <h2>Payment Vouchers</h2>
                    <span class="pv-hero-badge">
                        <i class="bi bi-shield-check"></i> Outward Financial Disbursements
                    </span>
                </div>
            </div>

            <div class="pv-hero-actions">
                <a href="{{ route('Payment-vochers') }}" class="pv-btn-create">
                    <i class="bi bi-plus-circle-fill"></i> Add Payment Voucher
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        {{-- ══════════ KPI METRIC CARDS ══════════ --}}
        @php
            $totalCount = $receipts->count();
            $totalSum = $receipts->sum(function($r) {
                return (float)$r->total_amount;
            });
            $todayPayments = $receipts->filter(function($r) {
                return substr($r->receipt_date ?? $r->entry_date ?? '', 0, 10) === date('Y-m-d');
            });
            $todayCount = $todayPayments->count();
            $todaySum = $todayPayments->sum(function($r) {
                return (float)$r->total_amount;
            });
        @endphp

        <div class="pv-kpi-grid">
            <div class="pv-kpi-card rose">
                <div class="pv-kpi-info">
                    <h6>Total Payments</h6>
                    <div class="pv-kpi-val">{{ number_format($totalCount) }}</div>
                </div>
                <div class="pv-kpi-icon">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>

            <div class="pv-kpi-card amber">
                <div class="pv-kpi-info">
                    <h6>Total Disbursed</h6>
                    <div class="pv-kpi-val">Rs. {{ number_format($totalSum, 2) }}</div>
                </div>
                <div class="pv-kpi-icon">
                    <i class="bi bi-cash"></i>
                </div>
            </div>

            <div class="pv-kpi-card violet">
                <div class="pv-kpi-info">
                    <h6>Today's Vouchers</h6>
                    <div class="pv-kpi-val">{{ number_format($todayCount) }} <span style="font-size:0.8rem; font-weight:600; color:#64748b;">vouchers</span></div>
                </div>
                <div class="pv-kpi-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>

            <div class="pv-kpi-card slate">
                <div class="pv-kpi-info">
                    <h6>Today's Paid Amount</h6>
                    <div class="pv-kpi-val">Rs. {{ number_format($todaySum, 2) }}</div>
                </div>
                <div class="pv-kpi-icon">
                    <i class="bi bi-send-check"></i>
                </div>
            </div>
        </div>

        {{-- ══════════ MAIN CARD WITH DATATABLE ══════════ --}}
        <div class="pv-card">
            {{-- Quick Filter Toolbar --}}
            <div class="pv-filter-bar">
                <div class="pv-filter-group">
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold text-uppercase" style="letter-spacing:0.04em;">From:</label>
                        <input type="date" id="filterFromDate" class="pv-filter-control">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold text-uppercase" style="letter-spacing:0.04em;">To:</label>
                        <input type="date" id="filterToDate" class="pv-filter-control">
                    </div>
                    <select id="filterType" class="pv-filter-control">
                        <option value="">All Types</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Customer">Customer</option>
                        <option value="Account">Account</option>
                        <option value="Walk-in">Walk-in</option>
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
            <div class="table-responsive pv-table-wrap">
                <table id="voucherTable" class="table">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th style="width:130px;">Voucher No</th>
                            <th style="width:120px;">Payment Date</th>
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

                            $type = strtolower($item->type_label ?? $item->type ?? '');
                            $typeClass = 'pv-type-other';
                            if (str_contains($type, 'vendor')) $typeClass = 'pv-type-vendor';
                            elseif (str_contains($type, 'customer')) $typeClass = 'pv-type-customer';
                            elseif (str_contains($type, 'walk')) $typeClass = 'pv-type-walkin';
                            elseif (str_contains($type, 'account') || !empty($item->type_label)) $typeClass = 'pv-type-account';
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold" data-label="ID">{{ $item->id }}</td>
                            <td data-label="Voucher No">
                                <span class="pv-id-badge">
                                    <i class="bi bi-credit-card"></i> {{ $item->pvid }}
                                </span>
                            </td>
                            <td data-label="Payment Date">
                                <span class="pv-date-text">
                                    <i class="bi bi-calendar3 text-muted"></i> {{ $item->receipt_date ?? $item->entry_date }}
                                </span>
                            </td>
                            <td data-label="Type">
                                <span class="pv-type-badge {{ $typeClass }}">
                                    {{ $item->type_label ?? (ucfirst($item->type) ?: '—') }}
                                </span>
                            </td>
                            <td data-label="Party">
                                <div class="pv-party-name">
                                    <i class="bi bi-building text-muted"></i>
                                    <span>{{ $item->party_name ?: ($item->party_id ?: '—') }}</span>
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
                                <span class="pv-amount">Rs. {{ number_format((float)$item->total_amount, 2) }}</span>
                            </td>
                            <td data-label="Created At">
                                <span class="small text-muted">{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </td>
                            <td class="text-center" data-label="Action">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('PaymentVoucher.print', $item->id) }}"
                                       target="_blank"
                                       class="pv-action-btn pv-action-print"
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
            searchPlaceholder: "Search payments...",
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