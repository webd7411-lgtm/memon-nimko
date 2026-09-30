@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --rvf-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --rvf-bg: #f8fafc;
    --rvf-surface: #ffffff;
    --rvf-border: #e2e8f0;
    --rvf-primary: #059669;
    --rvf-primary-dark: #047857;
    --rvf-primary-light: #ecfdf5;
    --rvf-text-main: #0f172a;
    --rvf-text-muted: #64748b;
    --rvf-radius: 16px;
    --rvf-radius-sm: 10px;
    --rvf-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    --rvf-shadow-lg: 0 12px 32px -4px rgba(15, 23, 42, 0.12);
}

.rvf-page * {
    font-family: var(--rvf-font);
}

.rvf-page {
    background-color: var(--rvf-bg);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ══════════ HERO BANNER ══════════ */
.rvf-hero {
    position: relative;
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f766e 100%);
    border-radius: var(--rvf-radius);
    padding: 1.5rem 1.85rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 35px -10px rgba(5, 150, 105, 0.35);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    overflow: hidden;
}

.rvf-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 15% 90%, rgba(52, 211, 153, 0.3) 0%, transparent 60%),
        radial-gradient(circle at 85% 15%, rgba(56, 189, 248, 0.25) 0%, transparent 55%);
    pointer-events: none;
}

.rvf-hero > * {
    position: relative;
    z-index: 1;
}

.rvf-hero-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rvf-hero-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.5rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.rvf-hero-title h2 {
    font-size: 1.55rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.02em;
}

.rvf-hero-badge {
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

.rvf-hero-actions {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.rvf-btn-glass {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    padding: 0.55rem 1.15rem;
    border-radius: var(--rvf-radius-sm);
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s;
    text-decoration: none;
    backdrop-filter: blur(6px);
}
.rvf-btn-glass:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-1px);
}

/* ══════════ FORM CARD ══════════ */
.rvf-card {
    background: var(--rvf-surface);
    border: 1px solid var(--rvf-border);
    border-radius: var(--rvf-radius);
    box-shadow: var(--rvf-shadow);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}

.rvf-section-title {
    font-size: 0.92rem;
    font-weight: 800;
    color: #064e3b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.65rem;
    border-bottom: 1.5px solid #ecfdf5;
}

.rvf-label {
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    margin-bottom: 0.35rem;
    display: block;
}

.rvf-input {
    border: 1.5px solid #e2e8f0;
    border-radius: var(--rvf-radius-sm);
    padding: 0.55rem 0.85rem;
    font-size: 0.88rem;
    color: var(--rvf-text-main);
    background: #ffffff;
    transition: all 0.2s ease;
    width: 100%;
}
.rvf-input:focus {
    border-color: var(--rvf-primary);
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    outline: none;
}
.rvf-input[readonly] {
    background: #f8fafc;
    color: #334155;
    cursor: not-allowed;
}

/* ══════════ FIX INPUT GROUP ALIGNMENT ══════════ */
.input-group {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    width: 100% !important;
}
.input-group > .input-group-text {
    border: 1.5px solid #e2e8f0 !important;
    border-right: none !important;
    border-radius: var(--rvf-radius-sm) 0 0 var(--rvf-radius-sm) !important;
    background: #f8fafc;
    color: #64748b;
    padding: 0.55rem 0.85rem;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.input-group > .rvf-input {
    flex: 1 1 auto !important;
    width: 1% !important;
    min-width: 0 !important;
    border-radius: 0 var(--rvf-radius-sm) var(--rvf-radius-sm) 0 !important;
}
.input-group > .btn {
    border: 1.5px solid #e2e8f0 !important;
    border-left: none !important;
    border-radius: 0 var(--rvf-radius-sm) var(--rvf-radius-sm) 0 !important;
    padding: 0.4rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ══════════ TABLE STYLING ══════════ */
.rvf-table-container {
    border: 1px solid var(--rvf-border);
    border-radius: var(--rvf-radius-sm);
    overflow: hidden;
    margin-bottom: 1.5rem;
    background: #ffffff;
}

table.rvf-table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
}

table.rvf-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.85rem 0.95rem;
    border-bottom: 1.5px solid #e2e8f0;
    border-top: none;
}

table.rvf-table tbody td {
    padding: 0.65rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

table.rvf-table tfoot th {
    background: #f8fafc;
    padding: 0.9rem 1rem;
    border-top: 2px solid #e2e8f0;
    font-size: 0.88rem;
}

.rvf-row-btn-del {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #fee2e2;
    color: #dc2626;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
}
.rvf-row-btn-del:hover {
    background: #dc2626;
    color: #ffffff;
    transform: scale(1.05);
}

.rvf-btn-add-row {
    background: #ecfdf5;
    border: 1.5px dashed #10b981;
    color: #047857;
    font-size: 0.84rem;
    font-weight: 700;
    padding: 0.6rem 1.25rem;
    border-radius: var(--rvf-radius-sm);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.2s;
}
.rvf-btn-add-row:hover {
    background: #d1fae5;
    border-color: #059669;
    color: #065f46;
    transform: translateY(-1px);
}

/* ══════════ TOTAL DISPLAY ══════════ */
.rvf-total-box {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1.5px solid #a7f3d0;
    border-radius: var(--rvf-radius-sm);
    padding: 0.75rem 1.25rem;
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
}
.rvf-total-box .total-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: #065f46;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.rvf-total-box .total-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: #047857;
    font-family: 'SF Mono', Consolas, Monaco, monospace;
}

/* ══════════ ACTION BUTTONS ══════════ */
.rvf-submit-btn {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 0.75rem 2rem;
    border-radius: var(--rvf-radius-sm);
    border: none;
    box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.rvf-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(5, 150, 105, 0.45);
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
}

.rvf-cancel-btn {
    background: #ffffff;
    color: #475569;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: var(--rvf-radius-sm);
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
    transition: all 0.2s;
}
.rvf-cancel-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

/* ══════════ RESPONSIVE & MOBILE POLISH ══════════ */
.rvf-table-container {
    border: 1px solid var(--rvf-border);
    border-radius: var(--rvf-radius-sm);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1.5rem;
    background: #ffffff;
}

@media (min-width: 992px) {
    table.rvf-table {
        min-width: 860px;
    }
}

@media (max-width: 991.98px) {
    .rvf-table-container {
        border: none !important;
        background: transparent !important;
        overflow: visible !important;
        margin-bottom: 1.25rem !important;
    }
    table.rvf-table {
        min-width: 0 !important;
        width: 100% !important;
        display: block !important;
        border: none !important;
    }
    table.rvf-table thead {
        display: none !important;
    }
    table.rvf-table tbody {
        display: flex !important;
        flex-direction: column !important;
        gap: 1.15rem !important;
        width: 100% !important;
    }
    table.rvf-table tbody tr {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.75rem 0.85rem !important;
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-left: 4.5px solid var(--rvf-primary) !important;
        border-radius: 14px !important;
        padding: 1.1rem !important;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06) !important;
        position: relative !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    table.rvf-table tbody tr:hover {
        background: #ffffff !important;
    }
    table.rvf-table tbody td {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 0 !important;
        border: none !important;
        min-width: 0 !important;
    }
    /* Narration spans full width */
    table.rvf-table tbody td:nth-child(1) {
        grid-column: 1 / -1 !important;
    }
    /* Reference spans full width or 1 col */
    table.rvf-table tbody td:nth-child(2) {
        grid-column: 1 / -1 !important;
    }
    /* Account Head & Destination Account */
    table.rvf-table tbody td:nth-child(3) {
        grid-column: 1 / 2 !important;
    }
    table.rvf-table tbody td:nth-child(4) {
        grid-column: 2 / 3 !important;
    }
    /* Discount & Amount */
    table.rvf-table tbody td:nth-child(5) {
        grid-column: 1 / 2 !important;
    }
    table.rvf-table tbody td:nth-child(6) {
        grid-column: 2 / 3 !important;
    }
    /* Delete button full width */
    table.rvf-table tbody td:nth-child(7) {
        grid-column: 1 / -1 !important;
        padding-top: 0.5rem !important;
        margin-top: 0.35rem !important;
        border-top: 1px dashed #e2e8f0 !important;
    }
    /* Labels on mobile */
    table.rvf-table tbody td::before {
        content: attr(data-label);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
        margin-bottom: 0.25rem;
        display: block;
    }
    table.rvf-table tbody td:nth-child(7)::before {
        display: none !important;
    }
    table.rvf-table tbody td:nth-child(7) .removeRow {
        width: 100% !important;
        border-radius: 9px !important;
        padding: 0.5rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.4rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
        transition: all 0.2s ease !important;
    }
    table.rvf-table tbody td:nth-child(7) .removeRow::after {
        content: " Delete Transaction Row";
    }

    .rvf-hero {
        padding: 1.25rem 1.15rem;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    .rvf-hero-left {
        gap: 0.75rem;
    }
    .rvf-hero-icon {
        width: 44px;
        height: 44px;
        font-size: 1.3rem;
        border-radius: 12px;
    }
    .rvf-hero-title h2 {
        font-size: 1.35rem;
    }
    .rvf-hero-actions {
        width: 100%;
        display: flex;
        gap: 0.5rem;
    }
    .rvf-btn-glass {
        flex: 1;
        justify-content: center;
        text-align: center;
        padding: 0.55rem 0.75rem;
        font-size: 0.8rem;
    }
    .rvf-card {
        padding: 1.2rem;
        border-radius: 14px;
    }
}

@media (max-width: 575.98px) {
    table.rvf-table tbody tr {
        grid-template-columns: 1fr 1fr !important;
        padding: 0.85rem !important;
    }
    table.rvf-table tbody td:nth-child(3),
    table.rvf-table tbody td:nth-child(4) {
        grid-column: 1 / -1 !important;
    }
}

@media (max-width: 767.98px) {
    .rvf-input {
        min-height: 42px;
        font-size: 0.95rem;
    }
    .rvf-card {
        padding: 1rem;
    }
    .rvf-bottom-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1.25rem !important;
    }
    .rvf-bottom-bar-left {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.5rem !important;
    }
    .rvf-bottom-bar-left .rvf-btn-add-row {
        width: 100%;
        justify-content: center;
        min-height: 44px;
    }
    .rvf-bottom-bar-right {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.75rem !important;
        width: 100%;
    }
    .rvf-total-box {
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0.85rem 1.15rem;
    }
    .rvf-submit-btn {
        width: 100%;
        justify-content: center;
        min-height: 46px;
        font-size: 1rem;
    }
    .rvf-cancel-btn {
        width: 100%;
        justify-content: center;
        min-height: 44px;
    }
}
</style>

<div class="main-content rvf-page">
    <div class="container-fluid px-3 px-md-4 py-3">

        {{-- ══════════ HERO BANNER ══════════ --}}
        <div class="rvf-hero">
            <div class="rvf-hero-left">
                <div class="rvf-hero-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="rvf-hero-title">
                    <h2>New Receipt Voucher</h2>
                    <span class="rvf-hero-badge">
                        <i class="bi bi-arrow-down-left-circle-fill"></i> Inward Cash & Bank Entry
                    </span>
                </div>
            </div>

            <div class="rvf-hero-actions">
                <a href="{{ route('all-recepit-vochers') }}" class="rvf-btn-glass">
                    <i class="bi bi-list-ul"></i> All Receipt Vouchers
                </a>
                <button type="button" class="rvf-btn-glass" onclick="location.reload();">
                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                </button>
            </div>
        </div>

        {{-- ══════════ ALERTS ══════════ --}}
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

        {{-- ══════════ MAIN FORM CARD ══════════ --}}
        <div class="rvf-card">
            <form action="{{ route('recepit.vochers.store') }}" method="POST" id="receiptVoucherForm">
                @csrf

                {{-- Header Details --}}
                <div class="rvf-section-title">
                    <i class="bi bi-info-circle-fill text-success"></i> Voucher Primary Details
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="rvf-label">RVID No</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                            <input type="text" class="rvf-input fw-bold text-success border-start-0 font-monospace" name="rvid" value="{{ $nextRvid }}" readonly>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="rvf-label">Receipt Date</label>
                        <input type="date" name="receipt_date" class="rvf-input" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="rvf-label">Entry Date</label>
                        <input type="date" name="entry_date" class="rvf-input" value="{{ now()->toDateString() }}">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="rvf-label">Receipt Type</label>
                        <select name="vendor_type" class="rvf-input" required>
                            <option value="">Select Receipt Type</option>
                            @foreach($AccountHeads as $head)
                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                            @endforeach
                            <option value="vendor">Vendor</option>
                            <option value="customer">Customer</option>
                            <option value="walkin">Walk-in Customer</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="rvf-label">Party / Account Title</label>
                        <select name="vendor_id" class="rvf-input" required>
                            <option disabled selected>Select Type First</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="rvf-label">Phone / Account Code</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="tel" id="tel" class="rvf-input border-start-0" readonly placeholder="Auto-populated">
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-9">
                        <label class="rvf-label">General Remarks / Notes</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-chat-left-text"></i></span>
                            <input type="text" name="remarks" class="rvf-input border-start-0" id="remarks" placeholder="Enter general remarks for this voucher...">
                        </div>
                    </div>
                </div>

                {{-- Line Items Table --}}
                <div class="rvf-section-title d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-list-check text-success"></i> Transaction Accounts & Amounts
                    </div>
                    <button type="button" class="rvf-btn-add-row" id="btnAddRow">
                        <i class="bi bi-plus-circle-fill"></i> Add Line Row
                    </button>
                </div>

                <div class="d-md-none text-muted small mb-2 d-flex align-items-center gap-1">
                    <i class="bi bi-card-checklist text-success fw-bold"></i>
                    <span>Mobile View: Transactions are stacked in clean responsive cards</span>
                </div>

                <div class="rvf-table-container">
                    <table class="table rvf-table align-middle" id="voucherTable">
                        <thead>
                            <tr>
                                <th style="width:25%;">Narration / Detail</th>
                                <th style="width:14%;">Reference#</th>
                                <th style="width:18%;">Account Head</th>
                                <th style="width:20%;">Destination Account</th>
                                <th style="width:10%;">Discount</th>
                                <th style="width:13%;" class="text-end">Amount (PKR)</th>
                                <th style="width:50px;" class="text-center"><i class="bi bi-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Narration / Detail">
                                    <div class="narration-wrapper">
                                        <input type="hidden" name="narration_text[]" class="narrationTextHidden">
                                        <select name="narration_id[]" class="rvf-input narrationSelect">
                                            <option value="">-- Select Detail --</option>
                                            <option value="new" style="font-weight: 700; color: #059669;">➕ Type New Detail / Manual</option>
                                            @foreach($narrations as $id => $name)
                                             <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="narrationInputWrap mt-1" style="display:none;">
                                            <div class="input-group">
                                                <input type="text" class="rvf-input narrationInput" placeholder="Type new detail...">
                                                <button type="button" class="btn btn-sm btn-outline-success btnCancelNewNarr" title="Back to dropdown"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Reference #">
                                    <input name="reference_no[]" type="text" class="rvf-input" placeholder="Ref#">
                                </td>
                                <td data-label="Account Head">
                                    <select name="row_account_head[]" class="rvf-input rowAccountHead" required>
                                        <option value="">Select Head</option>
                                        @foreach($AccountHeads as $head)
                                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td data-label="Destination Account">
                                    <select name="row_account_id[]" class="rvf-input rowAccountSub" required>
                                        <option value="">Select Account</option>
                                    </select>
                                </td>
                                <td data-label="Discount">
                                    <input name="discount_value[]" type="number" step="0.01" class="rvf-input text-end discountValue" value="0">
                                </td>
                                <td data-label="Amount (PKR)">
                                    <input name="amount[]" type="number" step="0.01" class="rvf-input text-end fw-bold text-success amount" placeholder="0.00" required>
                                </td>
                                <td class="text-center" data-label="Action">
                                    <button type="button" class="rvf-row-btn-del removeRow" title="Delete Row">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Bottom Summary & Submit Bar --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2 rvf-bottom-bar">
                    <div class="d-flex align-items-center gap-2 rvf-bottom-bar-left">
                        <button type="button" class="rvf-btn-add-row" id="btnAddRowBottom">
                            <i class="bi bi-plus-lg"></i> Add Another Row
                        </button>
                        <span class="text-muted small ps-2 d-none d-sm-inline">Tip: Press <kbd class="bg-light text-dark border">Enter</kbd> in amount to quickly add rows</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 rvf-bottom-bar-right">
                        <div class="rvf-total-box">
                            <span class="total-label">Grand Total:</span>
                            <span class="total-val" id="totalDisplay">Rs. 0.00</span>
                            <input type="hidden" name="total_amount" id="totalAmount" value="0">
                        </div>

                        <button type="submit" class="rvf-submit-btn">
                            <i class="bi bi-check2-circle fs-5"></i> Save Receipt Voucher
                        </button>

                        <a href="{{ route('all-recepit-vochers') }}" class="rvf-cancel-btn">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    // Narration toggle for custom text
    $(document).on('change', '.narrationSelect', function() {
        let $cell = $(this).closest('td');
        let $wrap = $cell.find('.narrationInputWrap');
        let $input = $wrap.find('.narrationInput');
        let $hidden = $cell.find('.narrationTextHidden');

        if ($(this).val() === 'new') {
            $wrap.show();
            $input.focus();
        } else {
            $wrap.hide();
            $input.val('');
            $hidden.val('');
        }
    });

    $(document).on('input', '.narrationInput', function() {
        $(this).closest('td').find('.narrationTextHidden').val($(this).val());
    });

    $(document).on('click', '.btnCancelNewNarr', function() {
        let $cell = $(this).closest('td');
        $cell.find('.narrationSelect').val('').trigger('change');
    });

    // Party Type change -> fetch parties
    $(document).on('change', 'select[name="vendor_type"]', function() {
        let type = $(this).val();
        let $vendorSelect = $('select[name="vendor_id"]');

        $('input[name="tel"]').val('');
        $('#remarks').val('');

        $vendorSelect.empty().append('<option disabled selected>Loading...</option>');

        if (type === 'vendor' || type === 'customer' || type === 'walkin') {
            $.get('{{ route("party.list") }}?type=' + type, function(data) {
                $vendorSelect.empty().append('<option disabled selected>Select Party</option>');
                data.forEach(function(item) {
                    $vendorSelect.append('<option value="' + item.id + '">' + item.text + '</option>');
                });
            });
        } else if (type) {
            let headId = type;
            $.get('{{ url("get-accounts-by-head") }}/' + headId, function(data) {
                $vendorSelect.empty().append('<option disabled selected>Select Account</option>');
                data.forEach(function(acc) {
                    $vendorSelect.append(
                        '<option value="' + acc.id + '" data-code="' + (acc.account_code || '') + '">' +
                        acc.title + (acc.account_code ? ' (' + acc.account_code + ')' : '') +
                        '</option>'
                    );
                });
            });
        }
    });

    $(document).on('change', 'select[name="vendor_id"]', function() {
        let $selected = $(this).find(':selected');
        let id = $selected.val();
        let type = $('select[name="vendor_type"]').val().toLowerCase();

        if (!id) return;

        let accountCode = $selected.data('code');
        if (accountCode) {
            $('input[name="tel"]').val(accountCode);
            return;
        }

        $.get('{{ route("customers.show", ["id" => "__ID__"]) }}'.replace('__ID__', id) + '?type=' + type, function(d) {
            $('input[name="tel"]').val(d.mobile || '');
            if (d.remarks && !$('#remarks').val()) {
                $('#remarks').val(d.remarks);
            }
        });
    });

    // Row Account Head -> Sub Accounts
    $(document).on('change', '.rowAccountHead', function() {
        let headId = $(this).val();
        let $subSelect = $(this).closest('tr').find('.rowAccountSub');

        if (!headId) {
            $subSelect.html('<option value="">Select Account</option>');
            return;
        }

        $subSelect.html('<option value="">Loading accounts...</option>');
        $.get('{{ url("get-accounts-by-head") }}/' + headId, function(res) {
            let html = '<option value="">Select Account</option>';
            res.forEach(acc => {
                html += `<option value="${acc.id}">${acc.title}</option>`;
            });
            $subSelect.html(html);
        });
    });

    // Total Calculation
    function calculateTotals() {
        let total = 0;
        $('#voucherTable tbody tr').each(function() {
            let amt = parseFloat($(this).find('.amount').val()) || 0;
            total += amt;
        });
        $('#totalAmount').val(total.toFixed(2));
        $('#totalDisplay').text('Rs. ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $(document).on('input', '.amount', function() {
        calculateTotals();
    });

    // Add Row Helper
    function appendNewRow() {
        let newRow = `
        <tr>
            <td data-label="Narration / Detail">
                <div class="narration-wrapper">
                    <input type="hidden" name="narration_text[]" class="narrationTextHidden">
                    <select name="narration_id[]" class="rvf-input narrationSelect">
                        <option value="">-- Select Detail --</option>
                        <option value="new" style="font-weight: 700; color: #059669;">➕ Type New Detail / Manual</option>
                        @foreach($narrations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="narrationInputWrap mt-1" style="display:none;">
                        <div class="input-group">
                            <input type="text" class="rvf-input narrationInput" placeholder="Type new detail...">
                            <button type="button" class="btn btn-sm btn-outline-success btnCancelNewNarr" title="Back to dropdown"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                </div>
            </td>
            <td data-label="Reference #"><input name="reference_no[]" type="text" class="rvf-input" placeholder="Ref#"></td>
            <td data-label="Account Head">
                <select name="row_account_head[]" class="rvf-input rowAccountHead" required>
                    <option value="">Select Head</option>
                    @foreach($AccountHeads as $head)
                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                    @endforeach
                </select>
            </td>
            <td data-label="Destination Account">
                <select name="row_account_id[]" class="rvf-input rowAccountSub" required>
                    <option value="">Select Account</option>
                </select>
            </td>
            <td data-label="Discount"><input name="discount_value[]" type="number" step="0.01" class="rvf-input text-end discountValue" value="0"></td>
            <td data-label="Amount (PKR)"><input name="amount[]" type="number" step="0.01" class="rvf-input text-end fw-bold text-success amount" placeholder="0.00" required></td>
            <td class="text-center" data-label="Action">
                <button type="button" class="rvf-row-btn-del removeRow" title="Delete Row"><i class="bi bi-trash3-fill"></i></button>
            </td>
        </tr>`;
        $('#voucherTable tbody').append(newRow);
        $('#voucherTable tbody tr:last .amount').focus();
    }

    $('#btnAddRow, #btnAddRowBottom').on('click', function(e) {
        e.preventDefault();
        appendNewRow();
    });

    // Enter in amount adds new row
    $(document).on('keypress', '.amount', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            appendNewRow();
        }
    });

    // Delete row
    $(document).on('click', '.removeRow', function() {
        if ($('#voucherTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        } else {
            alert('At least one transaction line is required.');
        }
    });
</script>
@endsection