@extends('admin_panel.layout.app')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --cb-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --cb-bg: #f8fafc;
        --cb-surface: #ffffff;
        --cb-border: #e2e8f0;
        --cb-navy: #0f172a;
        --cb-navy-light: #1e293b;
        --cb-emerald: #059669;
        --cb-rose: #e11d48;
        --cb-amber: #d97706;
        --cb-indigo: #4f46e5;
    }

    body, html {
        overflow-x: hidden !important;
        max-width: 100vw;
    }

    .cb-page-wrap * {
        font-family: var(--cb-font);
    }

    .cashbook-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        margin: 16px 0;
        border: 1px solid var(--cb-border);
        width: 100%;
        max-width: 100%;
    }

    /* ═══════ HEADER & FILTERS ═══════ */
    .cashbook-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e293b 100%);
        color: #fff;
        padding: 14px 20px;
    }
    .cb-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cb-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #38bdf8;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .cb-header-title h4 {
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
        color: #ffffff;
    }

    .cb-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-end;
        width: 100%;
    }
    .cb-fgroup {
        display: flex;
        flex-direction: column;
        gap: 3px;
        flex: 1 1 130px;
        min-width: 110px;
    }
    .cb-fgroup label {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
    }
    .cb-input {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12.5px;
        font-weight: 600;
        color-scheme: dark;
        outline: none;
        width: 100%;
        transition: all 0.2s ease;
        height: 36px;
    }
    .cb-input:focus {
        border-color: rgba(255, 255, 255, 0.6);
        background: rgba(255, 255, 255, 0.2);
    }
    select.cb-input option {
        background: #1e293b;
        color: #ffffff;
    }

    .cashbook-body {
        padding: 16px 20px;
        background: var(--cb-bg);
    }

    /* ═══════ COMPACT BALANCE HERO CARDS ═══════ */
    .cb-balance-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    .cb-balance-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 12px 16px;
        border: 1px solid var(--cb-border);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .cb-balance-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .cb-balance-card.opening::before {
        background: linear-gradient(180deg, #3b82f6, #60a5fa);
    }
    .cb-balance-card.closing::before {
        background: linear-gradient(180deg, #059669, #34d399);
    }
    .cb-balance-card.closing.negative::before {
        background: linear-gradient(180deg, #e11d48, #f87171);
    }
    .cb-balance-info .lbl {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .cb-balance-info .val {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .cb-balance-info .val.positive { color: #059669; }
    .cb-balance-info .val.negative { color: #e11d48; }
    .cb-balance-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .cb-balance-card.opening .cb-balance-icon {
        background: #eff6ff;
        color: #2563eb;
    }
    .cb-balance-card.closing .cb-balance-icon {
        background: #ecfdf5;
        color: #059669;
    }
    .cb-balance-card.closing.negative .cb-balance-icon {
        background: #fff1f2;
        color: #e11d48;
    }

    /* ═══════ SMALL BEAUTIFUL ATTRACTIVE STAT CARDS ═══════ */
    .cb-section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #475569;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cb-mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 12px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .cb-mini-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .cb-mini-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .cb-mini-card-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .cb-mini-card-icon {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .cb-mini-card-val {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.15;
    }
    .cb-mini-card-sub {
        font-size: 10.5px;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 2px;
    }

    .cb-mini-card.cash { border-left: 3.5px solid #10b981; }
    .cb-mini-card.cash .cb-mini-card-icon { background: #ecfdf5; color: #059669; }
    .cb-mini-card.card { border-left: 3.5px solid #0ea5e9; }
    .cb-mini-card.card .cb-mini-card-icon { background: #f0f9ff; color: #0284c7; }
    .cb-mini-card.change { border-left: 3.5px solid #f59e0b; }
    .cb-mini-card.change .cb-mini-card-icon { background: #fffbeb; color: #d97706; }
    .cb-mini-card.sale { border-left: 3.5px solid #8b5cf6; }
    .cb-mini-card.sale .cb-mini-card-icon { background: #f5f3ff; color: #7c3aed; }
    .cb-mini-card.recovery { border-left: 3.5px solid #059669; }
    .cb-mini-card.recovery .cb-mini-card-icon { background: #ecfdf5; color: #059669; }
    .cb-mini-card.vendor { border-left: 3.5px solid #e11d48; }
    .cb-mini-card.vendor .cb-mini-card-icon { background: #fff1f2; color: #e11d48; }

    /* Method pills */
    .method-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .method-badge.cash { background: #dcfce7; color: #15803d; }
    .method-badge.card { background: #e0f2fe; color: #0369a1; }
    .method-badge.account { background: #fef3c7; color: #b45309; }
    .method-badge.credit { background: #fee2e2; color: #b91c1c; }
    .method-badge.bank { background: #f3e8ff; color: #7e22ce; }

    .method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 6px;
        margin-top: 6px;
    }
    .method-grid-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 8px;
        padding: 4px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .method-grid-item .amt {
        font-weight: 800;
        font-size: 12px;
        color: #0f172a;
    }

    /* ═══════ DESKTOP TRANSACTIONS TABLE ═══════ */
    .cashbook-card .table-responsive {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        overflow-x: hidden;
    }
    .cash-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }
    .cash-table thead th {
        background: #0f172a;
        color: #ffffff;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 9px 12px;
        border: none;
    }
    .cash-table thead th.th-rec {
        background: linear-gradient(90deg, #064e3b, #047857);
    }
    .cash-table thead th.th-pay {
        background: linear-gradient(90deg, #881337, #be123c);
    }
    .cash-table thead th.sep-col,
    .cash-table tbody td.sep-col {
        width: 3px;
        background: #e2e8f0;
        padding: 0 !important;
        border: none !important;
    }
    .cash-table tbody td {
        padding: 7px 12px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        word-break: normal;
        overflow-wrap: anywhere;
    }
    .cash-table tbody tr:hover td {
        background: #f8fafc;
    }
    .entry-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 12.5px;
    }
    .entry-ref {
        font-size: 11px;
        color: #64748b;
        display: block;
    }
    .entry-amount {
        font-weight: 800;
        text-align: right;
        font-size: 13.5px;
        color: #059669;
    }
    .entry-amount.credit {
        color: #e11d48;
    }
    .total-row td {
        background: #f8fafc !important;
        font-weight: 800;
        font-size: 13.5px;
        border-top: 2px solid #cbd5e1 !important;
        padding: 10px 12px;
    }

    /* ═══════ MOBILE CARDS (TOUCH-FRIENDLY & NO OVERFLOW) ═══════ */
    .cb-cards {
        display: none;
        width: 100%;
        max-width: 100%;
    }
    .cb-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        margin-bottom: 8px;
    }
    .cb-row {
        display: flex;
    }
    .cb-col {
        flex: 1 1 50%;
        min-width: 0;
        padding: 8px 10px;
    }
    .cb-col + .cb-col {
        border-left: 1px dashed #e2e8f0;
    }
    .cb-col-hd {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding-bottom: 4px;
        margin-bottom: 4px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cb-col-rec .cb-col-hd { color: #059669; }
    .cb-col-pay .cb-col-hd { color: #e11d48; }
    .cb-col-hd .cb-amt {
        margin-left: auto;
        font-size: 12px;
        font-weight: 800;
    }
    .cb-col-rec .cb-amt { color: #059669; }
    .cb-col-pay .cb-amt { color: #e11d48; }
    .cb-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }
    .cb-ref {
        font-size: 10.5px;
        color: #64748b;
        margin-top: 1px;
        overflow-wrap: anywhere;
    }
    .cb-empty {
        font-size: 10.5px;
        color: #cbd5e1;
        font-style: italic;
    }

    .cb-totals {
        display: flex;
        border-radius: 12px;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        color: #fff;
        overflow: hidden;
        margin-top: 10px;
    }
    .cb-total {
        flex: 1 1 50%;
        min-width: 0;
        padding: 10px 12px;
    }
    .cb-total + .cb-total {
        border-left: 1px solid rgba(255, 255, 255, 0.15);
    }
    .cb-total span {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.75;
        margin-bottom: 2px;
    }
    .cb-total b {
        font-size: 14px;
        font-weight: 800;
    }
    .cb-total-rec b { color: #34d399; }
    .cb-total-pay b { color: #f87171; }

    /* ═══════ RESPONSIVE BREAKPOINTS ═══════ */
    @media (max-width: 991.98px) {
        .cashbook-card .table-responsive { display: none; }
        .cb-cards { display: block; }
    }

    @media (max-width: 767.98px) {
        .cashbook-card {
            margin: 8px 0;
            border-radius: 14px;
        }
        .cashbook-header {
            padding: 12px 14px;
        }
        .cashbook-body {
            padding: 12px 14px;
        }
        .cb-balance-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .cb-balance-card {
            padding: 10px 12px;
        }
        .cb-balance-info .val {
            font-size: 18px;
        }
        .cb-balance-icon {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
        .cb-mini-card-val {
            font-size: 15px;
        }
        .method-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .cashbook-header { padding: 10px 12px; }
        .cb-header-title h4 { font-size: 1rem; }
        .cb-fgroup { flex-basis: calc(50% - 4px); min-width: 0; }
        .cb-fgroup.branch-col { flex-basis: 100%; }
        .cb-row { flex-direction: column; }
        .cb-col + .cb-col { border-left: none; border-top: 1px dashed #e2e8f0; }
    }
</style>

<div class="main-content cb-page-wrap">
    <div class="main-content-inner">
        <div class="container-fluid px-2 px-md-3">
            <div class="cashbook-card">
                
                {{-- ═══════ HEADER & LIVE FILTERS ═══════ --}}
                <div class="cashbook-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="cb-header-title">
                            <div class="cb-header-icon">
                                <i class="fa fa-book-open"></i>
                            </div>
                            <div>
                                <h4>Daily Cash Book</h4>
                            </div>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('cashbook') }}" id="dateFilterForm" class="cb-filters">
                        <div class="cb-fgroup branch-col">
                            <label><i class="fa fa-building me-1"></i>Branch</label>
                            <select name="branch_id" class="cb-input" onchange="document.getElementById('dateFilterForm').submit()">
                                <option value="all" {{ ($selectedBranchId ?? 'all') === 'all' ? 'selected' : '' }}>All Branches</option>
                                @if(isset($branches))
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ (string)($selectedBranchId ?? '') === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="cb-fgroup">
                            <label><i class="fa fa-calendar-alt me-1"></i>From Date</label>
                            <input type="date" name="start_date" class="cb-input"
                                   value="{{ $startDate ?? now()->subDays(30)->format('Y-m-d') }}"
                                   onchange="document.getElementById('dateFilterForm').submit()">
                        </div>
                        <div class="cb-fgroup">
                            <label><i class="fa fa-calendar-day me-1"></i>Target Date</label>
                            <input type="date" name="date" class="cb-input"
                                   value="{{ $selectedDate ?? date('Y-m-d') }}"
                                   onchange="document.getElementById('dateFilterForm').submit()">
                        </div>
                        <div class="cb-fgroup">
                            <label><i class="fa fa-clock me-1"></i>Time From</label>
                            <input type="time" name="start_time" class="cb-input"
                                   value="{{ $startTime ?? '00:00' }}"
                                   onchange="document.getElementById('dateFilterForm').submit()">
                        </div>
                        <div class="cb-fgroup">
                            <label><i class="fa fa-clock me-1"></i>Time To</label>
                            <input type="time" name="end_time" class="cb-input"
                                   value="{{ $endTime ?? '23:59' }}"
                                   onchange="document.getElementById('dateFilterForm').submit()">
                        </div>
                    </form>
                </div>

                <div class="cashbook-body">
                    
                    {{-- ═══════ DUAL COMPACT BALANCE CARDS ═══════ --}}
                    <div class="cb-balance-grid">
                        <div class="cb-balance-card opening">
                            <div class="cb-balance-info">
                                <div class="lbl"><i class="fa fa-door-open text-primary"></i> Opening Balance</div>
                                <div class="val {{ $openingBalance >= 0 ? 'positive' : 'negative' }}">
                                    Rs {{ number_format($openingBalance, 0) }}
                                </div>
                            </div>
                            <div class="cb-balance-icon">
                                <i class="fa fa-coins"></i>
                            </div>
                        </div>

                        <div class="cb-balance-card closing {{ $closingBalance >= 0 ? '' : 'negative' }}">
                            <div class="cb-balance-info">
                                <div class="lbl"><i class="fa fa-wallet {{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}"></i> Closing Balance</div>
                                <div class="val {{ $closingBalance >= 0 ? 'positive' : 'negative' }}">
                                    Rs {{ number_format($closingBalance, 0) }}
                                </div>
                            </div>
                            <div class="cb-balance-icon">
                                <i class="fa fa-vault"></i>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════ SALES BREAKDOWN (SMALL ATTRACTIVE CARDS) ═══════ --}}
                    <div class="cb-section-title">
                        <i class="fa fa-chart-pie text-primary"></i> Sales Breakdown
                    </div>
                    <div class="row g-2 g-md-3 mb-3 row-cols-2 row-cols-md-4">
                        <div class="col">
                            <div class="cb-mini-card cash">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Cash Sales</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-money-bill-wave"></i></div>
                                </div>
                                <div class="cb-mini-card-val">Rs {{ number_format($totalSaleCash, 0) }}</div>
                                <div class="cb-mini-card-sub">{{ $saleCount }} invoice(s)</div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="cb-mini-card card">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Card Sales</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-credit-card"></i></div>
                                </div>
                                <div class="cb-mini-card-val">Rs {{ number_format($totalSaleCard, 0) }}</div>
                                <div class="cb-mini-card-sub">via card terminal</div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="cb-mini-card change">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Change Returned</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-hand-holding-usd"></i></div>
                                </div>
                                <div class="cb-mini-card-val">Rs {{ number_format($totalChange, 0) }}</div>
                                <div class="cb-mini-card-sub">deducted from cash</div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="cb-mini-card sale">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Net Sales</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-chart-line"></i></div>
                                </div>
                                <div class="cb-mini-card-val">Rs {{ number_format($totalSaleNet, 0) }}</div>
                                <div class="cb-mini-card-sub">Cash + Card - Change</div>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════ RECOVERIES & VENDOR PAYMENTS (COMPACT CARDS) ═══════ --}}
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-md-6">
                            <div class="cb-mini-card recovery h-100">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Customer Recoveries</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-arrow-down"></i></div>
                                </div>
                                <div class="cb-mini-card-val text-success">Rs {{ number_format($totalRecoveries, 0) }}</div>
                                @if(count($recoveryByMethod))
                                    <div class="method-grid">
                                        @foreach($recoveryByMethod as $method => $amt)
                                            <div class="method-grid-item">
                                                <span class="method-badge {{ strtolower($method) }}">{{ $method }}</span>
                                                <span class="amt">{{ number_format($amt, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="cb-mini-card-sub">No customer recoveries for this filter</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="cb-mini-card vendor h-100">
                                <div class="cb-mini-card-head">
                                    <span class="cb-mini-card-label">Vendor Payments</span>
                                    <div class="cb-mini-card-icon"><i class="fa fa-arrow-up"></i></div>
                                </div>
                                <div class="cb-mini-card-val text-danger">Rs {{ number_format($totalVendorPayments, 0) }}</div>
                                @if(count($vendorPayByMethod))
                                    <div class="method-grid">
                                        @foreach($vendorPayByMethod as $method => $amt)
                                            <div class="method-grid-item">
                                                <span class="method-badge {{ strtolower($method) }}">{{ $method }}</span>
                                                <span class="amt">{{ number_format($amt, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="cb-mini-card-sub">No vendor payments for this filter</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ═══════ TRANSACTIONS ═══════ --}}
                    <div class="cb-section-title mt-3">
                        <i class="fa fa-list-alt text-secondary"></i> Detailed Cash Transactions
                    </div>

                    {{-- Desktop Table (>= 992px) --}}
                    <div class="table-responsive">
                        <table class="cash-table">
                            <thead>
                                <tr>
                                    <th width="38%" class="th-rec"><i class="fa fa-arrow-down me-1"></i> Receipts</th>
                                    <th width="12%" class="th-rec text-end">Amount</th>
                                    <th class="sep-col"></th>
                                    <th width="38%" class="th-pay"><i class="fa fa-arrow-up me-1"></i> Payments</th>
                                    <th width="12%" class="th-pay text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i < $maxRows; $i++)
                                    <tr>
                                        <td>
                                            @if(isset($receipts[$i]))
                                                <span class="entry-title">{{ $receipts[$i]['title'] }}</span>
                                                <span class="entry-ref">{{ $receipts[$i]['ref'] }}</span>
                                            @endif
                                        </td>
                                        <td class="entry-amount">{{ isset($receipts[$i]) ? number_format($receipts[$i]['amount'],0) : '' }}</td>
                                        <td class="sep-col"></td>
                                        <td>
                                            @if(isset($payments[$i]))
                                                <span class="entry-title">{{ $payments[$i]['title'] }}</span>
                                                <span class="entry-ref">{{ $payments[$i]['ref'] }}</span>
                                            @endif
                                        </td>
                                        <td class="entry-amount credit">{{ isset($payments[$i]) ? number_format($payments[$i]['amount'],0) : '' }}</td>
                                    </tr>
                                @endfor

                                {{-- Totals --}}
                                <tr class="total-row">
                                    <td><span class="text-success fw-bold"><i class="fa fa-check-circle me-1"></i> Total Receipts</span></td>
                                    <td class="text-end text-success fw-bold">{{ number_format($totalReceipts, 0) }}</td>
                                    <td class="sep-col"></td>
                                    <td><span class="text-danger fw-bold"><i class="fa fa-arrow-circle-up me-1"></i> Total Payments</span></td>
                                    <td class="text-end text-danger fw-bold">{{ number_format($totalPayments, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile / Tablet Cards (< 992px) --}}
                    <div class="cb-cards">
                        @for($i = 0; $i < $maxRows; $i++)
                        <div class="cb-card">
                            <div class="cb-row">
                                <div class="cb-col cb-col-rec">
                                    <div class="cb-col-hd"><i class="fa fa-arrow-down"></i> Receipt
                                        <span class="cb-amt">{{ isset($receipts[$i]) ? number_format($receipts[$i]['amount'],0) : '' }}</span>
                                    </div>
                                    @if(isset($receipts[$i]))
                                        <div class="cb-title">{{ $receipts[$i]['title'] }}</div>
                                        <div class="cb-ref">{{ $receipts[$i]['ref'] }}</div>
                                    @else
                                        <div class="cb-empty">No receipt</div>
                                    @endif
                                </div>
                                <div class="cb-col cb-col-pay">
                                    <div class="cb-col-hd"><i class="fa fa-arrow-up"></i> Payment
                                        <span class="cb-amt">{{ isset($payments[$i]) ? number_format($payments[$i]['amount'],0) : '' }}</span>
                                    </div>
                                    @if(isset($payments[$i]))
                                        <div class="cb-title">{{ $payments[$i]['title'] }}</div>
                                        <div class="cb-ref">{{ $payments[$i]['ref'] }}</div>
                                    @else
                                        <div class="cb-empty">No payment</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endfor

                        <div class="cb-totals">
                            <div class="cb-total cb-total-rec">
                                <span>Total Receipts</span>
                                <b>Rs {{ number_format($totalReceipts, 0) }}</b>
                            </div>
                            <div class="cb-total cb-total-pay">
                                <span>Total Payments</span>
                                <b>Rs {{ number_format($totalPayments, 0) }}</b>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection