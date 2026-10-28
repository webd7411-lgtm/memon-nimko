@extends('admin_panel.layout.app')

@section('content')
<style>
/* ==========================================================================
   SALE RETURN SCREEN — IDENTICAL UI TO POS SALE SCREEN
   Full Excel Spreadsheet 2-Column Split Layout with POS Sidebar
   ========================================================================== */
:root {
    --ss-primary: #0f766e;
    --ss-primary-hover: #0d9488;
    --ss-primary-light: #f0fdfa;
    --ss-accent: #2563eb;
    --ss-success: #16a34a;
    --ss-danger: #ef4444;
    --ss-warning: #f59e0b;
    --ss-border: #cbd5e1;
    --ss-border-light: #e2e8f0;
    --ss-bg: #f8fafc;
    --ss-card-bg: #ffffff;
    --ss-text-main: #0f172a;
    --ss-text-muted: #64748b;
    --ss-radius: 8px;
    --ss-radius-sm: 4px;
    --ss-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
}

.ss-wrapper {
    font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--ss-text-main);
    padding: 0 4px 14px 4px !important;
    margin: 0 !important;
    width: 100% !important;
    position: relative;
}

/* TOP CONTROLS BAR */
.ss-topbar-card {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    padding: 8px 14px;
    box-shadow: var(--ss-shadow-sm);
    margin-bottom: 8px;
}

.ss-field-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.ss-field-group label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.ss-input-control {
    border: 1.5px solid var(--ss-border);
    border-radius: var(--ss-radius-sm);
    padding: 4px 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ss-text-main);
    background-color: #ffffff;
    outline: none;
    transition: all 0.15s ease;
    height: 34px;
}
.ss-input-control:focus {
    border-color: var(--ss-primary);
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.15);
}

.ss-top-btn {
    height: 34px;
    font-size: 12px;
    font-weight: 700;
    border-radius: var(--ss-radius-sm);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.15s ease;
}
.btn-save-sale {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
}
.btn-save-sale:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    color: #ffffff;
}

/* SEARCH & ACTION HEADER STRIP */
.ss-order-header {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    padding: 6px 12px;
    margin-bottom: 6px;
    box-shadow: var(--ss-shadow-sm);
}

.ss-barcode-box {
    position: relative;
    flex: 1;
    max-width: 320px;
    min-width: 200px;
}
.ss-barcode-box i.fa-search {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #0f766e;
    pointer-events: none;
}
.ss-barcode-input {
    width: 100%;
    height: 32px;
    padding: 4px 10px 4px 30px;
    border: 1.5px solid #cbd5e1;
    border-radius: 16px;
    font-size: 12.5px;
    font-weight: 600;
    outline: none;
    background: #f8fafc;
    transition: all 0.2s ease;
}
.ss-barcode-input:focus {
    border-color: #0f766e;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.18);
}

.ss-btn-add-row {
    height: 32px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #0f766e;
    background: #0f766e;
    color: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.ss-btn-add-row:hover {
    background: #0d9488;
    border-color: #0d9488;
}

.ss-panel-toggle-btn {
    height: 32px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease;
}
.ss-panel-toggle-btn:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

/* ==========================================================================
   EXCEL SPREADSHEET STYLE DATA GRID TABLE (EXACT SALE SCREEN LOOK)
   ========================================================================== */
.ss-table-card {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    box-shadow: var(--ss-shadow-sm);
    overflow: hidden;
}

.ss-table-responsive-wrap {
    max-height: calc(100vh - 250px);
    min-height: 340px;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    position: relative;
    width: 100%;
}
@media (max-width: 991.98px) {
    .ss-table-responsive-wrap {
        overflow-x: auto !important;
    }
}

.ss-table-responsive-wrap::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.ss-table-responsive-wrap::-webkit-scrollbar-track {
    background: #f8fafc;
}
.ss-table-responsive-wrap::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.ss-table-responsive-wrap::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.ss-excel-table {
    width: 100% !important;
    min-width: 0 !important;
    margin-bottom: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    background: #ffffff;
    table-layout: fixed !important;
}

.ss-excel-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f1f5f9 !important;
    color: #334155;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    padding: 6px 2px;
    border-top: 1px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8 !important;
    border-right: 1px solid #cbd5e1;
    border-left: none;
    text-align: center;
    vertical-align: middle;
    height: 34px;
    user-select: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    white-space: nowrap;
}
.ss-excel-table thead th:first-child {
    border-left: 1px solid #cbd5e1;
}

.ss-excel-table thead th.th-return {
    background: #0f766e !important;
    color: #ffffff !important;
    border-bottom: 2px solid #0d9488 !important;
}

.ss-excel-table thead th.th-to-return {
    background: #1e40af !important;
    color: #ffffff !important;
    border-bottom: 2px solid #1d4ed8 !important;
}

.ss-excel-table tbody tr {
    height: 33px;
}
.ss-excel-table tbody tr:hover td {
    background-color: #f8fafc;
}
.ss-excel-table tbody tr.row-selected td {
    background-color: #f0fdf4 !important;
}
.ss-excel-table tbody tr.row-selected:hover td {
    background-color: #ecfdf5 !important;
}

.ss-excel-table tbody td {
    padding: 0 !important;
    margin: 0 !important;
    vertical-align: middle;
    border: 1px solid #cbd5e1 !important;
    font-size: 12.5px;
    position: relative;
    height: 33px;
    background: #ffffff;
}

.excel-cell-idx {
    background: #f8fafc;
    font-size: 11px;
    color: #64748b;
    user-select: none;
    text-align: center;
}

/* Excel Flush Cell Input */
.ss-excel-input {
    width: 100%;
    height: 100%;
    min-height: 33px;
    padding: 2px 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--ss-text-main);
    border: none !important;
    border-radius: 0 !important;
    outline: none !important;
    background: transparent;
    font-family: inherit;
    box-sizing: border-box;
}
.ss-excel-input:focus {
    box-shadow: inset 0 0 0 2px #0f766e !important;
    background: #ffffff !important;
}
.ss-excel-input.text-end { text-align: right; }
.ss-excel-input.text-center { text-align: center; }

/* RETURN QTY & T.O RETURN CELL HIGHLIGHTS */
.ss-return-input {
    font-weight: 800 !important;
    color: #0f766e !important;
}
.ss-return-input.has-qty {
    background: #10b981 !important;
    color: #ffffff !important;
}
.ss-return-input.has-qty:focus {
    background: #059669 !important;
    color: #ffffff !important;
    box-shadow: inset 0 0 0 2px #047857 !important;
}

.ss-to-return-input {
    font-weight: 800 !important;
    color: #1e40af !important;
}
.ss-to-return-input.has-qty {
    background: #2563eb !important;
    color: #ffffff !important;
}
.ss-to-return-input.has-qty:focus {
    background: #1d4ed8 !important;
    color: #ffffff !important;
    box-shadow: inset 0 0 0 2px #1e40af !important;
}

/* Action Cell Buttons */
.ss-row-action-btn {
    border: none;
    background: transparent;
    padding: 2px 4px;
    font-size: 11px;
    cursor: pointer;
    border-radius: 3px;
    transition: all 0.15s ease;
}
.ss-row-action-btn.btn-max {
    color: #0f766e;
    font-weight: 800;
}
.ss-row-action-btn.btn-max:hover {
    background: #ccfbf1;
}
.ss-row-action-btn.btn-zero {
    color: #94a3b8;
}
.ss-row-action-btn.btn-zero:hover {
    background: #fee2e2;
    color: #dc2626;
}

/* Grid Footer Total */
.ss-grid-total-row {
    background: #f1f5f9;
    border-top: 1px solid #cbd5e1;
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}
.ss-grid-total-label {
    font-size: 13px;
    font-weight: 800;
    color: #334155;
    letter-spacing: 0.5px;
}
.ss-grid-total-val {
    font-size: 18px;
    font-weight: 900;
    color: #0f172a;
    min-width: 110px;
    text-align: right;
    font-family: 'Inter', monospace;
}

/* ==========================================================================
   RIGHT COLUMN: POS SUMMARY & PAYMENT CARDS (EXACT SALE SCREEN DESIGN)
   ========================================================================== */
.ss-sidebar-card {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    padding: 12px 14px;
    box-sizing: border-box !important;
    box-shadow: var(--ss-shadow-sm);
    margin-bottom: 8px;
}

.ss-card-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1.5px solid #f1f5f9;
}
.ss-card-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}

.ss-badge-live {
    background: #2563eb;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 10px;
    text-transform: uppercase;
}

.ss-summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 13px;
}
.ss-summary-row .label {
    color: #64748b;
    font-weight: 600;
}
.ss-summary-row .val {
    font-weight: 800;
    color: #0f172a;
    font-family: 'Inter', monospace;
}
.ss-summary-row.text-danger .val { color: #ef4444; }

.ss-summary-input {
    width: 100px;
    height: 28px;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    text-align: right;
    font-size: 13px;
    font-weight: 700;
    padding: 2px 6px;
    outline: none;
    font-family: 'Inter', monospace;
}
.ss-summary-input:focus {
    border-color: #0f766e;
}

.ss-summary-row.net-total {
    border-top: 1.5px dashed #cbd5e1;
    border-bottom: 1.5px dashed #cbd5e1;
    padding: 8px 0;
    margin: 5px 0;
}
.ss-summary-row.net-total .label {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
}
.ss-summary-row.net-total .val {
    font-size: 20px;
    font-weight: 900;
    color: #0f766e;
}

/* Change Box */
.ss-change-box {
    border-radius: 6px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 5px;
    font-size: 13.5px;
    font-weight: 800;
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    color: #16a34a;
}

/* PAYMENT METHODS */
.ss-pm-row {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
    width: 100% !important;
    box-sizing: border-box !important;
}
.ss-pm-select {
    flex: 1.2 1 0% !important;
    min-width: 0 !important;
    width: 0 !important;
    height: 34px;
    font-size: 12px;
    font-weight: 600;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 6px;
    outline: none;
    background: #ffffff;
    box-sizing: border-box !important;
    text-overflow: ellipsis;
}
.ss-pm-input {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    width: 0 !important;
    height: 34px;
    font-size: 13.5px;
    font-weight: 800;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 8px;
    text-align: right;
    outline: none;
    background: #ffffff;
    box-sizing: border-box !important;
    font-family: 'Inter', monospace;
}
.ss-pm-input:focus, .ss-pm-select:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.12);
}

/* BIG SAVE BUTTON */
.ss-btn-save-complete {
    width: 100%;
    height: 48px;
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: #ffffff;
    border: none;
    border-radius: var(--ss-radius);
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.35);
    transition: all 0.2s ease;
}
.ss-btn-save-complete:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 118, 110, 0.45);
}

.ss-btn-cancel-return {
    width: 100%;
    height: 36px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #475569;
    border-radius: var(--ss-radius);
    font-size: 12.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    margin-top: 6px;
    transition: all 0.15s ease;
}
.ss-btn-cancel-return:hover {
    background: #f1f5f9;
    color: #0f172a;
}
</style>

<div class="container-fluid ss-wrapper">
    <form id="saleReturnForm" action="{{ route('sales.return.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

        @php
            $custName  = $customerInfo['name'] ?? 'Walk-in Customer';
            $custType  = $customerInfo['type'] ?? 'Walk-in';
            $custPhone = $customerInfo['phone'] ?? '-';
            $custCode  = $customerInfo['code'] ?? '-';
            $custRawId = $customerInfo['raw_id'] ?? $sale->customer;
        @endphp
        <input type="hidden" name="customer" value="{{ $custRawId }}">
        <input type="hidden" name="total_subtotal" id="inputTotalSubtotal" value="0">
        <input type="hidden" name="total_discount" id="inputTotalDiscount" value="0">
        <input type="hidden" name="total_net" id="inputTotalNet" value="0">
        <input type="hidden" name="total_amount_Words" id="inputTotalAmountWords" value="">
        <input type="hidden" name="total_items" id="inputTotalItems" value="0">
        <input type="hidden" name="change" id="inputChange" value="0">

        <!-- ==========================================
             TOP CONTROLS BAR: CUSTOMER, INVOICE, DATE, M.BILL, SAVE (EXACT SALE SCREEN)
             ========================================== -->
        <div class="ss-topbar-card">
            <div class="row g-2 align-items-end">
                <!-- 1. Customer / Distributor Card -->
                <div class="col-lg-4 col-md-5 col-12">
                    <div class="ss-field-group">
                        <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <label><i class="fas fa-user-check me-1 text-success"></i> Customer / Distributor</label>
                            @if($custType === 'Distributor')
                                <span class="badge bg-primary text-white" style="font-size: 10px; padding: 2px 7px;">
                                    <i class="fas fa-truck me-1"></i> Distributor
                                </span>
                            @elseif($custType === 'Order Booker')
                                <span class="badge bg-info text-dark" style="font-size: 10px; padding: 2px 7px;">
                                    <i class="fas fa-clipboard-list me-1"></i> Order Booker
                                </span>
                            @elseif($custType === 'Retail Outlet')
                                <span class="badge bg-warning text-dark" style="font-size: 10px; padding: 2px 7px;">
                                    <i class="fas fa-store me-1"></i> Retail Outlet
                                </span>
                            @else
                                <span class="badge bg-light text-secondary border" style="font-size: 10px; padding: 2px 6px;">
                                    <i class="fas fa-user me-1"></i> {{ $custType }}
                                </span>
                            @endif
                        </div>
                        <div class="ss-input-control bg-light fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span class="text-truncate">
                                @if($custType === 'Distributor')
                                    <i class="fas fa-truck-loading me-1 text-primary"></i>
                                @else
                                    <i class="fas fa-user-tag me-1 text-primary"></i>
                                @endif
                                {{ $custName }}
                                @if($custCode !== '-' && !empty($custCode))
                                    <span class="text-muted font-monospace small" style="font-size: 10px;">({{ $custCode }})</span>
                                @endif
                            </span>
                            @if($custPhone !== '-')
                            <span class="badge bg-white text-muted border font-monospace ms-1">{{ $custPhone }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Original Invoice Badge -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="fas fa-receipt me-1 text-info"></i> Original Invoice</label>
                        <div class="ss-input-control bg-light fw-bold d-flex align-items-center justify-content-between">
                            <span class="font-monospace text-primary">{{ $sale->invoice_no }}</span>
                            <span class="badge bg-white text-dark border" style="font-size: 10px;">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M') }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Return Date -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-calendar-alt me-1 text-primary"></i> Return Date</label>
                        <input type="date" name="return_date" id="saleDateInput" class="ss-input-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- 4. M.Bill / Reference -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-comment-dots me-1 text-info"></i> M.Bill / Remarks</label>
                        <input type="text" name="reference" id="remarksInput" class="ss-input-control" placeholder="Enter remarks" value="{{ $sale->reference ?? '' }}">
                    </div>
                </div>

                <!-- 5. Top Save & Back Actions -->
                <div class="col-lg-2 col-md-1 col-6 text-end d-flex gap-1">
                    <button type="submit" class="ss-top-btn btn-save-sale w-100" id="btnTopSave" style="height: 34px;">
                        <i class="fas fa-check me-1"></i> Save (F9)
                    </button>
                    <a href="{{ route('sale.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center px-2" title="Back to Sales Register" style="height: 34px;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- ==========================================
             MAIN CONTENT: EXCEL GRID (LEFT 8 COLS) & POS SIDEBAR (RIGHT 4 COLS)
             ========================================== -->
        <div class="row gx-2 gy-2">

            <!-- LEFT COLUMN: SPREADSHEET TABLE -->
            <div class="col-lg-8 col-xl-8 ss-col-left">

                <!-- Search and Quick Action Strip -->
                <div class="ss-order-header d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <div class="ss-barcode-box flex-grow-1">
                            <i class="fas fa-search"></i>
                            <input type="text" id="gridFilterInput" class="ss-barcode-input" placeholder="Filter items in this invoice..." autocomplete="off">
                        </div>
                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                            <i class="fas fa-boxes me-1"></i> {{ count($saleItems) }} Invoiced Items
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="ss-btn-add-row" id="btnMaxAll" title="Return Max Remaining Qty for all items">
                            <i class="fas fa-check-double me-1"></i> Return All Available
                        </button>
                        <button type="button" class="ss-panel-toggle-btn" id="btnClearAll" title="Reset all return quantities to 0">
                            <i class="fas fa-undo me-1"></i> Reset to 0
                        </button>
                    </div>
                </div>

                <!-- EXCEL SPREADSHEET TABLE CARD -->
                <div class="ss-table-card">
                    <div class="ss-table-responsive-wrap">
                        <table class="ss-excel-table" id="returnGridTable">
                            <thead>
                                <tr>
                                    <th style="width: 28px;">#</th>
                                    <th style="width: 25%; min-width: 120px; text-align: left; padding-left: 6px;">ITEM NAME & DETAILS</th>
                                    <th style="width: 44px;">UNIT</th>
                                    <th style="width: 65px;">PRICE</th>
                                    {{-- EXACT REQUESTED RETURN COLUMNS --}}
                                    <th style="width: 64px;">SOLD QTY</th>
                                    <th style="width: 78px;" class="th-return">RETURN QTY</th>
                                    <th style="width: 58px;">T.O QTY</th>
                                    <th style="width: 78px;" class="th-to-return">T.O RETURN</th>
                                    <th style="width: 56px;">DISCOUNT</th>
                                    <th style="width: 74px;">AMOUNT</th>
                                    <th style="width: 44px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="returnGridBody">
                                @foreach ($saleItems as $index => $item)
                                @php
                                    $avail = (float)$item['available_qty'];
                                    $soldQty = (float)$item['qty'];
                                    $soldTO = (float)($item['trade_offer'] ?? 0);
                                    $price = (float)$item['price'];
                                    $unitDisc = (float)($item['discount'] ?? 0);
                                @endphp
                                <tr class="return-row"
                                    data-index="{{ $index }}"
                                    data-product-id="{{ $item['product_id'] }}"
                                    data-variant-id="{{ $item['variant_id'] ?? '' }}"
                                    data-price="{{ $price }}"
                                    data-unit-disc="{{ $unitDisc }}"
                                    data-available-qty="{{ $avail }}"
                                    data-sold-qty="{{ $soldQty }}"
                                    data-sold-to="{{ $soldTO }}">

                                    {{-- Hidden form fields for POST array preservation --}}
                                    <input type="hidden" name="product[]" value="{{ $item['item_name'] }}">
                                    <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="variant_id[]" value="{{ $item['variant_id'] ?? '' }}">
                                    <input type="hidden" name="item_code[]" value="{{ $item['item_code'] }}">
                                    <input type="hidden" name="brand[]" value="{{ $item['brand'] ?? '' }}">
                                    <input type="hidden" name="unit[]" value="{{ $item['unit'] ?? '' }}">
                                    <input type="hidden" name="color[]" value="{{ $item['note'] ?? '' }}">

                                    {{-- 1: S.No --}}
                                    <td class="excel-cell-idx">{{ $index + 1 }}</td>

                                    {{-- 2: Item Name & Details --}}
                                    <td style="padding: 2px 6px !important; overflow: hidden;">
                                        <div class="d-flex flex-column justify-content-center" style="line-height: 1.2;">
                                            <span class="fw-bold text-dark text-truncate d-block" style="font-size: 12px;" title="{{ $item['item_name'] }}">{{ $item['item_name'] }}</span>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                @if(!empty($item['item_code']))
                                                <span class="font-monospace text-muted" style="font-size: 10px;">{{ $item['item_code'] }}</span>
                                                @endif
                                                @if(!empty($item['brand']))
                                                <span class="badge bg-light text-secondary border px-1" style="font-size: 9px; padding: 1px 3px;">{{ $item['brand'] }}</span>
                                                @endif
                                                @if(!empty($item['note']) && $item['note'] !== '-')
                                                <span class="badge bg-warning-subtle text-dark border px-1" style="font-size: 9px; padding: 1px 3px;">{{ $item['note'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3: Unit --}}
                                    <td class="text-center font-monospace" style="font-size: 11px; color: #475569;">
                                        {{ $item['unit'] ?: 'PC' }}
                                    </td>

                                    {{-- 4: Unit Price --}}
                                    <td>
                                        <input type="number" step="any" name="price[]" class="ss-excel-input price-input text-end font-monospace fw-bold" value="{{ $price }}">
                                    </td>

                                    {{-- 5: SOLD QTY (Read-Only Reference Badge) --}}
                                    <td class="text-center" style="background: #f8fafc;">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <span class="badge bg-secondary fw-bold" style="font-size: 11px; padding: 2px 6px;">
                                                {{ (float)$soldQty == (int)$soldQty ? (int)$soldQty : $soldQty }}
                                            </span>
                                            <span class="text-success fw-bold" style="font-size: 9.5px;" title="Remaining Available to Return">
                                                Avail: {{ (float)$avail == (int)$avail ? (int)$avail : $avail }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- 6: RETURN QTY (Direct Input) --}}
                                    <td>
                                        <input type="number" step="any" name="qty[]"
                                               class="ss-excel-input return-qty-input ss-return-input text-center"
                                               value="0"
                                               min="0"
                                               max="{{ $avail }}"
                                               placeholder="0"
                                               {{ $avail <= 0 ? 'disabled' : '' }}
                                               title="Enter quantity to return (Max: {{ $avail }})">
                                    </td>

                                    {{-- 7: TRADE OFFER QTY (Read-Only Reference Badge) --}}
                                    <td class="text-center" style="background: #f8fafc;">
                                        @if($soldTO > 0)
                                        <span class="badge bg-primary-subtle text-primary border border-primary fw-bold" style="font-size: 10px; padding: 2px 5px;">
                                            +{{ (float)$soldTO == (int)$soldTO ? (int)$soldTO : $soldTO }} Free
                                        </span>
                                        @else
                                        <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- 8: TRADE OFFER RETURN QTY (Direct Input) --}}
                                    <td>
                                        <input type="number" step="any" name="trade_offer[]"
                                               class="ss-excel-input return-to-input ss-to-return-input text-center"
                                               value="0"
                                               min="0"
                                               max="{{ $soldTO }}"
                                               placeholder="0"
                                               {{ $soldTO <= 0 ? 'disabled' : '' }}
                                               title="Enter bonus/scheme units to return (Max: {{ $soldTO }})">
                                    </td>

                                    {{-- 9: Discount --}}
                                    <td>
                                        <input type="number" step="any" name="item_disc[]" class="ss-excel-input disc-input text-end font-monospace" value="0" placeholder="0">
                                    </td>

                                    {{-- 10: Return Total Amount --}}
                                    <td style="padding-right: 6px !important;">
                                        <input type="hidden" name="total[]" class="row-total-hidden" value="0">
                                        <div class="row-total-display text-end font-monospace fw-bold" style="font-size: 12.5px; color: #0f766e;">
                                            0.00
                                        </div>
                                    </td>

                                    {{-- 11: Action --}}
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="ss-row-action-btn btn-max" title="Max Return Qty" {{ $avail <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-angle-double-up"></i>
                                            </button>
                                            <button type="button" class="ss-row-action-btn btn-zero" title="Reset to 0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- GRID FOOTER TOTAL ROW -->
                    <div class="ss-grid-total-row">
                        <span class="text-muted" id="gridItemsCountSummary" style="font-size: 12px; font-weight: 600;">
                            0 Items Selected | 0 Return Pieces
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="ss-grid-total-label">GROSS RETURN:</span>
                            <span class="ss-grid-total-val" id="gridTotalAmountDisplay">Rs. 0.00</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: POS SUMMARY & PAYMENT CARDS (EXACT SALE SCREEN UI) -->
            <div class="col-lg-4 col-xl-4">

                <!-- 1. Summary Card -->
                <div class="ss-sidebar-card">
                    <div class="ss-card-hdr">
                        <h6 class="ss-card-title"><i class="fas fa-file-invoice text-primary"></i> Return Summary</h6>
                        <span class="ss-badge-live">Live</span>
                    </div>

                    <div class="ss-summary-row">
                        <span class="label">Gross Subtotal</span>
                        <span class="val" id="summaryTotalAmount">0.00</span>
                    </div>

                    <div class="ss-summary-row text-danger">
                        <span class="label">Item Discounts</span>
                        <span class="val" id="summaryLineDiscount">0.00</span>
                    </div>

                    <div class="ss-summary-row">
                        <span class="label">Extra Discount / Cost</span>
                        <input type="number" step="any" name="total_extra_cost" id="extraDiscount" class="ss-summary-input" value="0" placeholder="0.00">
                    </div>

                    <div class="ss-summary-row net-total">
                        <span class="label">Net Refund (Payable)</span>
                        <span class="val" id="summaryNetTotal">0.00</span>
                    </div>

                    <div class="ss-summary-row">
                        <span class="label">Amount in Words</span>
                        <span class="val small text-muted text-end" id="summaryAmountInWords" style="max-width: 170px; font-size: 11px;">Zero Rupees Only</span>
                    </div>

                    <div class="ss-change-box" id="summaryChangeBox">
                        <span>Change / Balance:</span>
                        <span id="summaryChangeAmount">0.00</span>
                    </div>
                </div>

                <!-- 2. Payment Methods Card -->
                <div class="ss-sidebar-card">
                    <div class="ss-card-hdr">
                        <h6 class="ss-card-title"><i class="fas fa-credit-card text-success"></i> Refund Payment Method</h6>
                    </div>

                    <div id="paymentMethodsContainer">
                        <div class="ss-pm-row" data-index="0">
                            <select class="ss-pm-select pm-account-select" name="payment_account" id="paymentAccountSelect">
                                <option value="cash" selected>💵 Cash in hand</option>
                                @if(isset($bankAccounts))
                                    @foreach($bankAccounts as $acc)
                                        <option value="{{ $acc->id }}">🏦 {{ $acc->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="number" step="any" min="0" class="ss-pm-input pm-amount-input" name="cash" id="cashInput" placeholder="0.00" value="0.00">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="ss-field-group mb-1"><span style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;"><i class="far fa-edit me-1 text-secondary"></i> Return Reason / Note</span></label>
                        <textarea name="return_note" id="return_note" class="ss-input-control" rows="2" placeholder="Optional return reason (e.g. damaged, expired, customer change)..." style="height: auto; width: 100%;"></textarea>
                    </div>
                </div>

                <!-- 3. BIG SAVE BUTTON -->
                <button type="submit" class="ss-btn-save-complete" id="btnSaveComplete">
                    <i class="fas fa-check-circle"></i> Confirm & Complete Return (F9)
                </button>

                <!-- Back button -->
                <a href="{{ route('sale.index') }}" class="ss-btn-cancel-return">
                    <i class="fas fa-arrow-left"></i> Back to Sales Register
                </a>

            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function num(v) {
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function formatNumber(val) {
        var n = parseFloat(val);
        if (isNaN(n)) return '0.00';
        return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Number to words converter
    function numberToWords(amount) {
        var a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
            "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
        ];
        var b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
        var numVal = Math.round(Math.abs(amount));
        if (numVal === 0) return "Zero Rupees Only";
        if (numVal > 999999999) return "Overflow";

        var n = ("000000000" + numVal).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
        if (!n) return "";
        var str = "";
        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + " " + a[n[1][1]]) + " Crore " : "";
        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + " " + a[n[2][1]]) + " Lakh " : "";
        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + " " + a[n[3][1]]) + " Thousand " : "";
        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + " " + a[n[4][1]]) + " " : "";
        return str.trim() + " Rupees Only";
    }

    // Recalculate single row total
    function recalcRow($tr) {
        var price = num($tr.find('.price-input').val());
        var retQty = num($tr.find('.return-qty-input').val());
        var retTO = num($tr.find('.return-to-input').val());
        var maxQty = num($tr.data('available-qty'));
        var maxTO = num($tr.data('sold-to'));
        var disc = num($tr.find('.disc-input').val());

        // Client-side validations
        if (retQty > maxQty) {
            retQty = maxQty;
            $tr.find('.return-qty-input').val(maxQty);
        }
        if (retTO > maxTO) {
            retTO = maxTO;
            $tr.find('.return-to-input').val(maxTO);
        }

        // Visual indicator on input
        if (retQty > 0) {
            $tr.find('.return-qty-input').addClass('has-qty');
        } else {
            $tr.find('.return-qty-input').removeClass('has-qty');
        }

        if (retTO > 0) {
            $tr.find('.return-to-input').addClass('has-qty');
        } else {
            $tr.find('.return-to-input').removeClass('has-qty');
        }

        // Highlight active row
        if (retQty > 0 || retTO > 0) {
            $tr.addClass('row-selected');
        } else {
            $tr.removeClass('row-selected');
        }

        var rowTotal = Math.max(0, (price * retQty) - disc);
        $tr.find('.row-total-hidden').val(rowTotal.toFixed(2));
        $tr.find('.row-total-display').text(formatNumber(rowTotal));
    }

    // Recalculate all totals
    function recalcAll() {
        var gross = 0;
        var totalItemDisc = 0;
        var totalReturnPieces = 0;
        var selectedItemCount = 0;

        $('#returnGridTable tbody tr.return-row').each(function() {
            var $tr = $(this);
            var retQty = num($tr.find('.return-qty-input').val());
            var retTO = num($tr.find('.return-to-input').val());
            var disc = num($tr.find('.disc-input').val());
            var rTotal = num($tr.find('.row-total-hidden').val());

            if (retQty > 0 || retTO > 0) {
                gross += rTotal + disc;
                totalItemDisc += disc;
                totalReturnPieces += (retQty + retTO);
                selectedItemCount++;
            }
        });

        var extraDisc = num($('#extraDiscount').val());
        var net = Math.max(0, gross - totalItemDisc - extraDisc);

        var cash = num($('#cashInput').val());
        var change = cash - net;

        // Grid footer updates
        $('#gridItemsCountSummary').text(selectedItemCount + ' Items Selected | ' + (totalReturnPieces % 1 === 0 ? totalReturnPieces : totalReturnPieces.toFixed(2)) + ' Return Pieces');
        $('#gridTotalAmountDisplay').text('Rs. ' + formatNumber(gross));

        // Right Sidebar Summary Card updates
        $('#summaryTotalAmount').text(formatNumber(gross));
        $('#inputTotalSubtotal').val(gross.toFixed(2));

        $('#summaryLineDiscount').text(formatNumber(totalItemDisc));
        $('#inputTotalDiscount').val(totalItemDisc.toFixed(2));

        $('#summaryNetTotal').text('Rs. ' + formatNumber(net));
        $('#inputTotalNet').val(net.toFixed(2));

        var inWords = numberToWords(net);
        $('#summaryAmountInWords').text(inWords);
        $('#inputTotalAmountWords').val(inWords);

        // Change / balance box
        $('#summaryChangeAmount').text(formatNumber(change));
        $('#inputChange').val(change.toFixed(2));
        if (change >= 0) {
            $('#summaryChangeBox').css({'background': '#f0fdf4', 'border-color': '#bbf7d0', 'color': '#16a34a'});
        } else {
            $('#summaryChangeBox').css({'background': '#fef2f2', 'border-color': '#fecaca', 'color': '#dc2626'});
        }
    }

    // Row input events
    $('#returnGridTable').on('input change', '.return-qty-input, .return-to-input, .price-input, .disc-input', function() {
        var $tr = $(this).closest('tr');
        recalcRow($tr);
        recalcAll();
    });

    // Row Quick Buttons: Max and Reset
    $('#returnGridTable').on('click', '.btn-max', function() {
        var $tr = $(this).closest('tr');
        var maxQty = num($tr.data('available-qty'));
        var maxTO = num($tr.data('sold-to'));
        $tr.find('.return-qty-input').val(maxQty);
        $tr.find('.return-to-input').val(maxTO);
        recalcRow($tr);
        recalcAll();
    });

    $('#returnGridTable').on('click', '.btn-zero', function() {
        var $tr = $(this).closest('tr');
        $tr.find('.return-qty-input').val(0);
        $tr.find('.return-to-input').val(0);
        $tr.find('.disc-input').val(0);
        recalcRow($tr);
        recalcAll();
    });

    // Global Quick Buttons
    $('#btnMaxAll').on('click', function() {
        $('#returnGridTable tbody tr.return-row').each(function() {
            var $tr = $(this);
            var maxQty = num($tr.data('available-qty'));
            var maxTO = num($tr.data('sold-to'));
            $tr.find('.return-qty-input').val(maxQty);
            $tr.find('.return-to-input').val(maxTO);
            recalcRow($tr);
        });
        recalcAll();
    });

    $('#btnClearAll').on('click', function() {
        $('#returnGridTable tbody tr.return-row').each(function() {
            var $tr = $(this);
            $tr.find('.return-qty-input').val(0);
            $tr.find('.return-to-input').val(0);
            $tr.find('.disc-input').val(0);
            recalcRow($tr);
        });
        recalcAll();
    });

    // Extra discount & cash changes
    $('#extraDiscount, #cashInput').on('input change', function() {
        recalcAll();
    });

    // Instant item filter search
    $('#gridFilterInput').on('keyup', function() {
        var q = $(this).val().toLowerCase().trim();
        $('#returnGridTable tbody tr.return-row').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) > -1);
        });
    });

    // Keyboard navigation: Enter key moves to next Return Qty input
    $('#returnGridTable').on('keydown', '.return-qty-input', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            var $inputs = $('#returnGridTable tbody .return-qty-input:visible:not(:disabled)');
            var idx = $inputs.index(this);
            if (idx < $inputs.length - 1) {
                $inputs.eq(idx + 1).focus().select();
            } else {
                $('#cashInput').focus().select();
            }
        }
    });

    // F9 Shortcut to submit return form
    $(document).on('keydown', function(e) {
        if (e.key === 'F9' || e.keyCode === 120) {
            e.preventDefault();
            $('#saleReturnForm').submit();
        }
    });

    // Form Submission Guard
    var isSubmitting = false;
    $('#saleReturnForm').on('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        var hasReturnItem = false;
        $('#returnGridTable tbody tr.return-row').each(function() {
            var q = num($(this).find('.return-qty-input').val());
            var to = num($(this).find('.return-to-input').val());
            if (q > 0 || to > 0) {
                hasReturnItem = true;
            }
        });

        if (!hasReturnItem) {
            e.preventDefault();
            alert('Please enter a Return Quantity or Trade Offer Return Quantity for at least one item.');
            return false;
        }

        isSubmitting = true;
        $('#btnSaveComplete, #btnTopSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');
    });

    // Initial calculation
    recalcAll();
});
</script>
@endsection
