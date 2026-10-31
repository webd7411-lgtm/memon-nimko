@extends('distributor_panel.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<style>
/* ==========================================================================
   EXCEL SPREADSHEET STYLE POS & BILLING INTERFACE (MIRROR OF SALE SCREEN)
   ========================================================================== */
:root {
    --ss-primary: #0f766e;
    --ss-primary-hover: #0d9488;
    --ss-primary-light: #f0fdfa;
    --ss-accent: #0284c7;
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

/* SELECT2 ENHANCEMENTS */
.ss-wrapper .select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    height: 34px !important;
    border: 1.5px solid var(--ss-border) !important;
    border-radius: var(--ss-radius-sm) !important;
    padding: 3px 8px !important;
    display: flex !important;
    align-items: center !important;
    background-color: #ffffff !important;
    transition: all 0.15s ease;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--ss-primary) !important;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.15) !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px !important;
    color: var(--ss-text-main) !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding-left: 0 !important;
    padding-right: 20px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px !important;
    right: 6px !important;
}
.select2-dropdown {
    border: 1.5px solid var(--ss-primary) !important;
    border-radius: var(--ss-radius) !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.18) !important;
    font-size: 12.5px !important;
    z-index: 999999 !important;
}
.select2-search--dropdown {
    padding: 6px 8px !important;
    background: #f8fafc;
}
.select2-search--dropdown .select2-search__field {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 5px 8px !important;
    outline: none !important;
    font-size: 12.5px !important;
}
.select2-results__option {
    padding: 6px 10px !important;
    font-size: 12.5px !important;
    white-space: normal !important;
    word-break: break-word;
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

.ss-select-control {
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
    cursor: pointer;
}
.ss-select-control:focus {
    border-color: var(--ss-primary);
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.15);
}

.ss-top-btn {
    height: 34px;
    border-radius: var(--ss-radius-sm);
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 0 12px;
    white-space: nowrap;
    transition: all 0.15s ease;
    border: 1.5px solid transparent;
    cursor: pointer;
}
.ss-top-btn.btn-save-sale {
    background: linear-gradient(135deg, #0f766e, #0d9488);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
}
.ss-top-btn.btn-save-sale:hover {
    background: linear-gradient(135deg, #0d9488, #115e59);
}
.ss-btn-new-cust {
    border-color: #10b981;
    color: #059669;
    background: #ecfdf5;
}
.ss-btn-new-cust:hover {
    background: #10b981;
    color: #fff;
}
.ss-btn-walkin {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}
.ss-btn-walkin:hover {
    background: #0284c7;
    color: #fff;
}

.ss-panel-toggle-btn {
    height: 34px;
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
}
.ss-panel-toggle-btn:hover, .ss-panel-toggle-btn.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

/* BARCODE & ADD ROW BAR */
.ss-order-header {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-bottom: none;
    border-radius: var(--ss-radius) var(--ss-radius) 0 0;
    padding: 6px 12px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.ss-barcode-box {
    position: relative;
    flex: 1;
    max-width: 100%;
}
.ss-barcode-box i.fa-barcode {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #0284c7;
    pointer-events: none;
}
.ss-barcode-input {
    width: 100%;
    height: 32px;
    padding: 4px 30px 4px 34px;
    border: 1.5px solid #bae6fd;
    border-radius: 16px;
    font-size: 12.5px;
    font-weight: 600;
    outline: none;
    background: #f0f9ff;
    transition: all 0.2s ease;
}
.ss-barcode-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.18);
}
.ss-btn-clear-barcode {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 13px;
    display: none;
}
.ss-btn-clear-barcode:hover { color: #ef4444; }

.ss-btn-add-row {
    height: 32px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #2563eb;
    background: #2563eb;
    color: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ss-btn-add-row:hover { background: #1d4ed8; }

/* QUICK PRODUCTS PANEL */
.ss-quick-panel {
    display: none;
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-top: none;
    padding: 8px 12px;
    border-radius: 0 0 var(--ss-radius) var(--ss-radius);
    margin-bottom: 8px;
    box-shadow: var(--ss-shadow-sm);
}
.ss-cat-tabs {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 6px;
}
.ss-cat-tab {
    white-space: nowrap;
    padding: 3px 9px;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    cursor: pointer;
}
.ss-cat-tab:hover, .ss-cat-tab.active {
    background: #0f766e;
    color: #ffffff;
    border-color: #0f766e;
}
.ss-qp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
    gap: 6px;
    max-height: 200px;
    overflow-y: auto;
}
.ss-qp-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px;
    cursor: pointer;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 75px;
}
.ss-qp-card:hover {
    border-color: #0f766e;
    background: #f0fdfa;
}
.ss-qp-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
    margin-bottom: 2px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.ss-qp-price { font-size: 11.5px; font-weight: 800; color: #0f766e; }
.ss-qp-stock { font-size: 9px; font-weight: 600; color: #64748b; }

/* EXCEL SPREADSHEET TABLE */
.ss-table-card {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    box-shadow: var(--ss-shadow-sm);
    overflow: hidden;
}

.ss-table-responsive-wrap {
    max-height: calc(100vh - 250px);
    min-height: 310px;
    overflow-y: auto !important;
    overflow-x: auto;
    position: relative;
}

.ss-excel-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    table-layout: fixed;
    background: #ffffff;
}

.ss-excel-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f1f5f9 !important;
    color: #334155;
    font-size: 11.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 7px 4px;
    border-top: 1px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8 !important;
    border-right: 1px solid #cbd5e1;
    border-left: none;
    text-align: center;
    vertical-align: middle;
    height: 34px;
    user-select: none;
}
.ss-excel-table thead th:first-child { border-left: 1px solid #cbd5e1; }

.ss-excel-table tbody tr { height: 33px; }
.ss-excel-table tbody tr:hover { background-color: #f8fafc; }
.ss-excel-table tbody td {
    padding: 0 !important;
    margin: 0 !important;
    vertical-align: middle;
    border: 1px solid #cbd5e1 !important;
    font-size: 12.5px;
    position: relative;
    height: 33px;
}

.excel-cell-idx {
    background: #f8fafc;
    font-size: 11px;
    color: #64748b;
    user-select: none;
}

/* Excel Cell Input */
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

/* Product Search Input Cell */
.ss-prod-search-wrap {
    position: relative;
    width: 100%;
    height: 100%;
}
.ss-prod-search-wrap .ss-excel-input { padding-right: 22px; }
.ss-prod-search-icon {
    position: absolute;
    right: 7px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}

/* DISCOUNT CELL */
.ss-disc-wrap {
    display: flex;
    align-items: center;
    width: 100%;
    height: 100%;
}
.ss-disc-wrap .row-disc-val {
    flex: 1;
    min-width: 0;
    height: 100%;
    border: none !important;
}
.ss-disc-toggle-btn {
    border: none;
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
    margin: 0 3px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    user-select: none;
}
.ss-disc-toggle-btn.btn-pct { background: #0f766e; color: #fff; }
.ss-disc-toggle-btn.btn-pkr { background: #0284c7; color: #fff; }

.ss-btn-del-row {
    background: transparent;
    border: none;
    color: #ef4444;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.ss-btn-del-row:hover { background: #fee2e2; color: #b91c1c; }

/* Grid Total Footer */
.ss-grid-total-row {
    background: #f1f5f9;
    border-top: 1px solid #cbd5e1;
    padding: 6px 14px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 16px;
}
.ss-grid-total-label {
    font-size: 12.5px;
    font-weight: 800;
    color: #334155;
    letter-spacing: 0.5px;
}
.ss-grid-total-val {
    font-size: 17px;
    font-weight: 900;
    color: #0f172a;
    min-width: 110px;
    text-align: right;
}

/* RIGHT SIDEBAR */
.ss-sidebar-card {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-radius: var(--ss-radius);
    padding: 12px 14px;
    box-shadow: var(--ss-shadow-sm);
    margin-bottom: 8px;
}
.ss-card-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 5px;
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
    padding: 4px 0;
    font-size: 13px;
}
.ss-summary-row .label { color: #64748b; font-weight: 600; }
.ss-summary-row .val { font-weight: 800; color: #0f172a; }
.ss-summary-row.text-danger .val { color: #ef4444; }

.ss-summary-row.net-total {
    border-top: 1.5px dashed #cbd5e1;
    border-bottom: 1.5px dashed #cbd5e1;
    padding: 7px 0;
    margin: 4px 0;
}
.ss-summary-row.net-total .label { font-size: 14.5px; font-weight: 800; color: #0f172a; }
.ss-summary-row.net-total .val { font-size: 19px; font-weight: 900; color: #0284c7; }

.ss-change-box {
    border-radius: 6px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 5px;
    font-size: 13.5px;
    font-weight: 800;
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #dc2626;
}
.ss-change-box.paid {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #16a34a;
}

/* PAYMENT METHODS */
.ss-btn-add-account {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    cursor: pointer;
}
.ss-btn-add-account:hover { background: #2563eb; color: #fff; }

.ss-pm-row {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
    width: 100%;
}
.ss-pm-select {
    flex: 1.2 1 0%;
    height: 34px;
    font-size: 12px;
    font-weight: 600;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 6px;
    outline: none;
    background: #ffffff;
}
.ss-pm-input {
    flex: 1 1 0%;
    height: 34px;
    font-size: 13.5px;
    font-weight: 800;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 8px;
    text-align: right;
    outline: none;
    background: #ffffff;
}
.ss-pm-input:focus, .ss-pm-select:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.12);
}

/* BIG GREEN SAVE BUTTON */
.ss-btn-save-complete {
    width: 100%;
    height: 46px;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    transition: all 0.15s ease;
}
.ss-btn-save-complete:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.45);
}

/* FLOATING PRODUCT SEARCH DROPDOWN */
.ss-floating-dropdown {
    position: fixed;
    z-index: 99999999 !important;
    background: #ffffff;
    border: 2px solid #0f766e;
    border-radius: 6px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25), 0 4px 12px rgba(15, 118, 110, 0.2);
    max-height: 380px;
    overflow-y: auto;
    display: none;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
.ss-dropdown-header {
    background: #f0fdfa;
    border-bottom: 1.5px solid #ccfbf1;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 800;
    color: #0f766e;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ss-drop-item {
    padding: 6px 10px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ss-drop-item:hover, .ss-drop-item.selected {
    background: #0f766e;
    color: #ffffff;
}
.ss-drop-item:hover .text-muted, .ss-drop-item.selected .text-muted,
.ss-drop-item:hover .text-success, .ss-drop-item.selected .text-success,
.ss-drop-item:hover .text-primary, .ss-drop-item.selected .text-primary {
    color: #ffffff !important;
}
</style>

<div class="ss-wrapper">
    <form action="{{ route('distributor.sales.store') }}" method="POST" id="dposScreenForm">
        @csrf
        <input type="hidden" name="discount" id="inputTotalDiscount" value="0">
        <input type="hidden" name="paid_amount" id="inputPaidAmount" value="0">
        <input type="hidden" name="payment_type" id="inputPaymentType" value="cash">

        <!-- ==========================================
             TOP CONTROLS BAR: CUSTOMER, DATE, M.BILL, QUICK PANEL, SAVE
             ========================================== -->
        <div class="ss-topbar-card">
            <div class="row g-2 align-items-end">
                <!-- 1. Customer (FIRST & PROMINENT) -->
                <div class="col-lg-4 col-md-5 col-12">
                    <div class="ss-field-group">
                        <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <label>
                                <i class="fas fa-user-check me-1 text-success"></i> Customer / Booker / Dist. <span class="text-danger">*</span>
                            </label>
                            <span id="customerTypeBadge" class="badge bg-light text-secondary border" style="font-size: 10px; padding: 2px 6px;">
                                <i class="fas fa-store me-1"></i> Retail Shop
                            </span>
                        </div>
                        <div class="d-flex gap-1 align-items-center">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <select name="distributor_customer_id" id="customerSelect" class="ss-select-control w-100" required>
                                    @if(isset($customers) && $customers->count() > 0)
                                        <option value="" disabled selected>-- Select Retail Shop / Customer --</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" data-type="retailer" data-phone="{{ $c->phone }}" data-balance="{{ (float)$c->current_balance }}" data-approved-discount="{{ (float)($c->approved_discount ?? 0) }}">
                                                {{ $c->shop_name }} ({{ $c->owner_name ?: 'Shop' }}) {{ $c->area ? '- ' . $c->area : '' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>No Retailers Registered &bull; Click + New</option>
                                    @endif
                                </select>
                            </div>
                            <button type="button" class="ss-top-btn ss-btn-new-cust px-2" data-bs-toggle="modal" data-bs-target="#newCustomerModal" title="Create New Retailer">
                                <i class="fas fa-plus"></i> New
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Date -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-calendar-alt me-1 text-primary"></i> Date</label>
                        <input type="date" name="sale_date" id="saleDateInput" class="ss-input-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- 3. M.Bill / Remarks -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-comment-dots me-1 text-info"></i> M.Bill / Remarks</label>
                        <input type="text" name="notes" id="remarksInput" class="ss-input-control" placeholder="Enter remarks">
                    </div>
                </div>

                <!-- 4. Quick Panel Toggle Button -->
                <div class="col-lg-2 col-md-1 col-6">
                    <button type="button" class="ss-panel-toggle-btn w-100 justify-content-center" id="btnToggleQuickPanel" style="height: 34px;">
                        <i class="fas fa-th-large me-1"></i> Quick Panel
                    </button>
                </div>

                <!-- 5. Top Save Sale (F9) Button -->
                <div class="col-lg-2 col-md-2 col-6 text-end">
                    <button type="button" class="ss-top-btn btn-save-sale w-100" id="btnTopSave" style="height: 34px;">
                        <i class="fas fa-check me-1"></i> Save Sale (F9)
                    </button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             MAIN CONTENT: EXCEL GRID (LEFT) & SIDEBAR (RIGHT)
             ========================================== -->
        <div class="row gx-2 gy-2">
            <!-- LEFT COLUMN: EXCEL SPREADSHEET GRID -->
            <div class="col-lg-8 col-xl-8">
                <!-- Barcode Scanner Bar & + Add Row (F4) -->
                <div class="ss-order-header d-flex align-items-center gap-2">
                    <div class="ss-barcode-box flex-grow-1">
                        <i class="fas fa-barcode"></i>
                        <input type="text" id="barcodeScanner" class="ss-barcode-input" placeholder="Scan Barcode (Variant / SKU)..." autocomplete="off" autofocus>
                        <button type="button" id="btnClearBarcode" class="ss-btn-clear-barcode" title="Clear Barcode"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="ss-btn-add-row" id="btnAddGridRow" title="Add New Row (F4)" style="height: 32px; white-space: nowrap;">
                        <i class="fas fa-plus"></i> Add Row (F4)
                    </button>
                </div>

                <!-- Quick Products Drawer Panel -->
                <div class="ss-quick-panel" id="quickProductsPanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="ss-cat-tabs flex-grow-1" id="qpCategoryTabs">
                            <span class="ss-cat-tab active" data-cat="all">All Van Items</span>
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <span class="ss-cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</span>
                                @endforeach
                            @endif
                        </div>
                        <input type="text" id="qpSearchFilter" class="ss-input-control" placeholder="Filter items..." style="width: 140px; height: 26px; font-size: 11px;">
                    </div>
                    <div class="ss-qp-grid" id="qpGridContainer">
                        @foreach($stocks as $stk)
                            @php
                                $price = (float)($stk->product->price ?? $stk->avg_price);
                                $vLabel = $stk->variant ? ($stk->variant->size_label ?: $stk->variant->variant_name) : '-';
                                $catId = $stk->product->category_id ?? 'all';
                            @endphp
                            <div class="ss-qp-card" 
                                 data-cat="{{ $catId }}" 
                                 data-name="{{ strtolower($stk->product->item_name) }}"
                                 data-stock-id="{{ $stk->id }}"
                                 data-product-id="{{ $stk->product_id }}"
                                 data-variant-id="{{ $stk->variant_id ?? '' }}"
                                 data-item-name="{{ $stk->product->item_name }}"
                                 data-size="{{ $vLabel }}"
                                 data-price="{{ $price }}"
                                 data-stock="{{ (float)$stk->qty }}"
                                 data-unit="{{ $stk->product->unit->name ?? 'Pc' }}">
                                <div class="ss-qp-name">{{ $stk->product->item_name }}</div>
                                <div class="ss-qp-price">Rs. {{ number_format($price, 0) }}</div>
                                <div class="ss-qp-stock"><i class="fas fa-truck me-1"></i>{{ (float)$stk->qty }} in van</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- EXCEL SPREADSHEET TABLE -->
                <div class="ss-table-card">
                    <div class="ss-table-responsive-wrap">
                        <table class="ss-excel-table" id="saleGridTable">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">#</th>
                                    <th style="width: 40%;">PRODUCT</th>
                                    <th style="width: 75px;">STOCK</th>
                                    <th style="width: 75px;">QTY</th>
                                    <th style="width: 80px;">SIZE</th>
                                    <th style="width: 100px;">PRICE</th>
                                    <th style="width: 125px;">DISCOUNT</th>
                                    <th style="width: 110px;">AMOUNT</th>
                                    <th style="width: 36px;" class="text-center" title="Delete Row"><i class="fas fa-trash-alt text-muted"></i></th>
                                </tr>
                            </thead>
                            <tbody id="saleGridBody">
                                <!-- Initial 8 Blank Rows rendered by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- GRID TOTAL FOOTER -->
                    <div class="ss-grid-total-row">
                        <span class="text-muted" id="gridItemsCountSummary" style="font-size: 12px; font-weight: 600;">0 Item(s) | 0 Total Qty</span>
                        <span class="ss-grid-total-label">GRID TOTAL:</span>
                        <span class="ss-grid-total-val" id="gridTotalAmountDisplay">Rs. 0.00</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: SUMMARY & PAYMENT -->
            <div class="col-lg-4 col-xl-4">
                <!-- Summary Card -->
                <div class="ss-sidebar-card">
                    <div class="ss-card-hdr">
                        <h6 class="ss-card-title"><i class="fas fa-file-invoice text-primary"></i> Summary</h6>
                        <span class="ss-badge-live">Live</span>
                    </div>

                    <div class="ss-summary-row">
                        <span class="label">Total Amount</span>
                        <span class="val" id="summaryTotalAmount">0.00</span>
                    </div>

                    <div class="ss-summary-row text-danger">
                        <span class="label">Line Discount</span>
                        <span class="val" id="summaryLineDiscount">0.00</span>
                    </div>

                    <div class="ss-summary-row net-total">
                        <span class="label">Net Total</span>
                        <span class="val" id="summaryNetTotal">0.00</span>
                    </div>

                    <div class="ss-summary-row">
                        <span class="label">Total Paid</span>
                        <span class="val text-success" id="summaryTotalPaid">0.00</span>
                    </div>

                    <div class="ss-change-box" id="summaryChangeBox">
                        <span>Change / Balance:</span>
                        <span id="summaryChangeAmount">0.00</span>
                    </div>
                </div>

                <!-- Payment Methods Card -->
                <div class="ss-sidebar-card">
                    <div class="ss-card-hdr">
                        <h6 class="ss-card-title"><i class="fas fa-credit-card text-success"></i> Payment Methods</h6>
                        <button type="button" class="ss-btn-add-account" id="btnAddAccountRow">
                            <i class="fas fa-plus"></i> Add Account
                        </button>
                    </div>

                    <div id="paymentMethodsContainer">
                        <div class="ss-pm-row" data-index="0">
                            @php
                                $cashAccount = $bankAccounts->first(function($a) {
                                    return stripos($a->title, "cash") !== false;
                                });
                                $otherAccounts = $bankAccounts->filter(function($a) {
                                    return stripos($a->title, "cash") === false;
                                });
                            @endphp
                            <select class="ss-pm-select pm-account-select" name="payment_account[]" id="pmAccountSelect">
                                <option value="{{ $cashAccount ? $cashAccount->id : "cash" }}" selected>
                                    {{ $cashAccount ? $cashAccount->title : "Cash in hand" }}
                                </option>
                                @if($otherAccounts->count() > 0)
                                    <optgroup label="Bank & Online Accounts">
                                        @foreach($otherAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->title }} @if($acc->account_number)({{ $acc->account_number }})@endif</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            <input type="number" step="any" min="0" class="ss-pm-input pm-amount-input" name="payment_amount[]" id="pmAmountInput" placeholder="0.00" value="0.00">
                        </div>
                    </div>

                    <!-- Smart Quick Cash Buttons -->
                    <div class="d-flex flex-wrap gap-1 mt-2" id="smartPayButtons">
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold flex-fill smart-pay-btn" data-action="full" title="Pay Full Bill">
                            <i class="fas fa-check-double me-1"></i> Full
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold flex-fill smart-pay-btn" data-val="5000">
                            5,000
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold flex-fill smart-pay-btn" data-val="1000">
                            1,000
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold flex-fill smart-pay-btn" data-val="500">
                            500
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger fw-bold flex-fill smart-pay-btn" data-action="clear" title="Reset to 0 (Full Credit / Due)">
                            <i class="fas fa-undo me-1"></i> 0
                        </button>
                    </div>
                </div>

                <!-- BIG SAVE & COMPLETE BUTTON -->
                <button type="button" class="ss-btn-save-complete" id="btnSaveComplete">
                    <i class="fas fa-save"></i> Save & Complete (F9)
                </button>
            </div>
        </div>
    </form>
</div>

<!-- FLOATING PRODUCT SEARCH DROPDOWN -->
<div id="floatingProductDropdown" class="ss-floating-dropdown">
    <div class="ss-dropdown-header">
        <span><i class="fas fa-boxes me-1"></i> Available Van Custody Stock</span>
        <span class="text-muted" style="font-size: 10px;">Click or press Enter</span>
    </div>
    <div id="floatingDropdownContent">
        <!-- Rendered live items -->
    </div>
</div>

<!-- MODAL: QUICK NEW RETAILER -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #0f766e; color: #fff; border-radius: 12px 12px 0 0;">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> Add New Retailer Shop</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRetailerForm" action="{{ route('distributor.customers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Shop / Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="shop_name" class="form-control" required placeholder="e.g. Madina General Store">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Owner / Contact Person</label>
                            <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Phone / Mobile</label>
                            <input type="text" name="phone" class="form-control" placeholder="0300-1234567">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Area / Route</label>
                            <input type="text" name="area" class="form-control" placeholder="e.g. Saddar, Market">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Opening Balance Due</label>
                            <input type="number" step="any" name="opening_balance" class="form-control" placeholder="0.00" value="0">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Shop #, Street / Market">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Approved Discount Limit (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="approved_discount" class="form-control" placeholder="0.00" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold" id="btnSaveQuickCust"><i class="fas fa-check me-1"></i> Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Add Account for Distributor -->
<div class="modal fade" id="quickAddAccountModal" tabindex="-1" aria-labelledby="quickAddAccountModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                <h6 class="modal-title fw-bold" id="quickAddAccountModalLabel">
                    <i class="fas fa-university me-1.5"></i> Open My Bank / Cash Account
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddAccountForm">
                @csrf
                <div class="modal-body p-3.5">
                    <div class="mb-2.5">
                        <label class="form-label fw-bold text-dark small mb-1">Account Category / Head <span class="text-danger">*</span></label>
                        <select name="head_id" id="qaHeadId" class="form-select form-select-sm rounded-3 shadow-none">
                            @if(isset($accountHeads))
                                @foreach($accountHeads as $h)
                                    <option value="{{ $h->id }}" {{ str_contains(strtolower($h->name), "bank") ? "selected" : "" }}>{{ $h->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold text-dark small mb-1">Account Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="qaTitle" class="form-control form-control-sm rounded-3 shadow-none" placeholder="e.g. Meezan Bank (Distributor Current) or Cash Register" required>
                    </div>

                    <div class="row g-2 mb-2.5">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Bank Name (Optional)</label>
                            <input type="text" name="bank_name" id="qaBankName" class="form-control form-control-sm rounded-3 shadow-none" placeholder="e.g. Meezan Bank">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Account / IBAN Number</label>
                            <input type="text" name="account_number" id="qaAccountNumber" class="form-control form-control-sm rounded-3 shadow-none" placeholder="e.g. 010203040506">
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold text-dark small mb-1">Opening Balance (PKR)</label>
                        <input type="number" step="any" min="0" name="opening_balance" id="qaOpeningBalance" class="form-control form-control-sm rounded-3 shadow-none" placeholder="0.00" value="0">
                    </div>
                </div>
                <div class="modal-footer bg-light px-3 py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-teal rounded-pill px-3" id="btnSubmitQuickAccount">
                        <i class="fas fa-check me-1"></i> Save & Select Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Available Van Stock catalog in memory for instant lookup & floating search
window.PRODUCTS = [
    @foreach($stocks as $stk)
    @php
        $vName = $stk->variant ? ($stk->variant->size_label ?: $stk->variant->variant_name) : '-';
        $price = (float)($stk->product->price ?? $stk->avg_price);
    @endphp
    {
        product_id: {{ $stk->product_id }},
        variant_id: "{{ $stk->variant_id ?? '' }}",
        name: "{{ addslashes($stk->product->item_name) }}",
        item_name: "{{ addslashes($stk->product->item_name) }}",
        code: "{{ $stk->product->item_code ?? '' }}",
        barcode: "{{ $stk->product->barcode ?? '' }}",
        size: "{{ $vName }}",
        unit: "{{ $stk->product->unit->name ?? 'Pc' }}",
        stock: {{ (float)$stk->qty }},
        price: {{ $price }},
        original_price: {{ $price }},
        category_id: "{{ $stk->product->category_id ?? '' }}"
    },
    @endforeach
];

$(document).ready(function() {
    const MIN_BLANK_ROWS = 8;
    let rowCounter = 0;
    const $floatingDropdown = $('#floatingProductDropdown').appendTo('body');
    const $floatingContent = $('#floatingDropdownContent');
    let activeSearchInput = null;
    let activeRow = null;

    // --- 1. INITIALIZE CUSTOMER SELECT2 ---
    $('#customerSelect').select2({
        placeholder: "Search Customer / Retailer...",
        width: '100%',
        dropdownAutoWidth: false
    }).on('select2:select', function() {
        updateCustomerBadge();
        setTimeout(function() { $('#barcodeScanner').focus(); }, 60);
    });

    function updateCustomerBadge() {
        const $selected = $('#customerSelect').find(':selected');
        const val = $selected.val();
        const $badge = $('#customerTypeBadge');

        if (!val) {
            $badge.attr('class', 'badge bg-light text-secondary border').html('<i class="fas fa-store me-1"></i> Select Shop');
            return;
        }

        const bal = parseFloat($selected.data('balance') || 0);
        if (bal > 0) {
            $badge.attr('class', 'badge bg-danger text-white border-0 fw-bold').html('<i class="fas fa-store me-1"></i> Due: Rs ' + bal.toLocaleString());
        } else {
            $badge.attr('class', 'badge bg-success text-white border-0 fw-bold').html('<i class="fas fa-store me-1"></i> Clear Balance');
        }
    }
    updateCustomerBadge();

    // --- 2. CREATE ROW HTML (8 BLANK ROWS DEFAULT) ---
    function createRowHtml(item) {
        rowCounter++;
        const id        = item ? (item.product_id || item.id || '') : '';
        const variantId = item ? (item.variant_id || '') : '';
        const name      = item ? (item.item_name || item.name || '') : '';
        const code      = item ? (item.code || item.item_code || item.barcode || '') : '';
        const hasItem   = !!id;
        const stockVal  = item ? parseFloat(item.stock !== undefined ? item.stock : 0) : 0;
        const stockDisp = hasItem ? stockVal : '-';
        const qty       = item ? (parseFloat(item.qty || 1)) : 1;
        const size      = item ? (item.size || '-') : '-';
        const price     = item ? (parseFloat(item.price || 0)) : 0;
        const origPrice = item ? (parseFloat(item.original_price || item.price || 0)) : price;
        const discVal   = item ? (parseFloat(item.disc_val || 0)) : 0;
        const discType  = item ? (item.disc_type || 'percent') : 'percent';

        let calcDisc = 0;
        if (hasItem) {
            if (discType === 'percent') {
                calcDisc = (qty * price) * (discVal / 100);
            } else {
                calcDisc = discVal;
            }
            calcDisc = Math.min(qty * price, Math.max(0, calcDisc));
        }
        const amount = Math.max(0, (qty * price) - calcDisc);

        let stockColorClass = 'text-muted';
        if (hasItem) {
            stockColorClass = stockVal > 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
        }

        return `
            <tr class="ss-grid-row" id="gridRow_${rowCounter}">
                <td class="excel-cell-idx text-center fw-bold text-muted row-idx">${rowCounter}</td>
                <td class="excel-cell-prod">
                    <div class="ss-prod-search-wrap">
                        <input type="hidden" name="product_id[]" class="row-product-id" value="${id}">
                        <input type="hidden" name="variant_id[]" class="row-variant-id" value="${variantId}">
                        <input type="hidden" name="product_name[]" class="row-product-name" value="${escapeHtml(name)}">
                        <input type="text" class="ss-excel-input ss-prod-search-input" placeholder="Search Product (Name / SKU / Barcode)..." value="${escapeHtml(name)}" autocomplete="off">
                        <i class="fas fa-search ss-prod-search-icon"></i>
                    </div>
                </td>
                <td class="excel-cell text-center ${stockColorClass} row-stock-badge">${stockDisp}</td>
                <td class="excel-cell">
                    <input type="number" step="any" min="0.01" name="qty[]" class="ss-excel-input text-center row-qty" value="${qty}">
                </td>
                <td class="excel-cell text-center text-muted small fw-semibold row-size-col">${size}</td>
                <td class="excel-cell">
                    <input type="number" step="any" min="0" name="unit_price[]" class="ss-excel-input text-end row-price" value="${price.toFixed(2)}" data-orig-price="${origPrice}">
                </td>
                <td class="excel-cell excel-disc-cell">
                    <div class="ss-disc-wrap">
                        <input type="number" step="any" min="0" name="disc_val[]" class="ss-excel-input text-end row-disc-val" value="${discVal}">
                        <button type="button" class="ss-disc-toggle-btn ${discType === 'percent' ? 'btn-pct' : 'btn-pkr'}" data-type="${discType}" title="Click to toggle % / PKR">${discType === 'percent' ? '%' : 'PKR'}</button>
                        <input type="hidden" name="disc_type[]" class="row-disc-type" value="${discType}">
                        <input type="hidden" name="item_disc[]" class="row-disc-amount" value="${calcDisc.toFixed(2)}">
                    </div>
                </td>
                <td class="excel-cell">
                    <input type="number" step="any" readonly name="total[]" class="ss-excel-input text-end fw-bold row-amount" value="${amount.toFixed(2)}" style="background: #f8fafc; color: #0f172a;">
                </td>
                <td class="excel-cell text-center">
                    <button type="button" class="ss-btn-del-row" title="Delete / Clear Row"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Populate initial 8 rows
    for (let i = 0; i < MIN_BLANK_ROWS; i++) {
        $('#saleGridBody').append(createRowHtml(null));
    }
    reindexRows();

    function reindexRows() {
        let count = 0;
        $('#saleGridBody tr.ss-grid-row').each(function() {
            count++;
            $(this).find('.row-idx').text(count);
        });
    }

    function ensureMinimumBlankRows() {
        let totalCount = $('#saleGridBody tr.ss-grid-row').length;
        if (totalCount < MIN_BLANK_ROWS) {
            const need = MIN_BLANK_ROWS - totalCount;
            for (let i = 0; i < need; i++) {
                $('#saleGridBody').append(createRowHtml(null));
            }
            reindexRows();
        }
    }

    // --- 3. FLOATING DROPDOWN PRODUCT SEARCH ---
    function positionFloatingDropdown(inputEl) {
        if (!inputEl) return;
        const rect = inputEl.getBoundingClientRect();
        const winWidth = $(window).width();
        const dropdownWidth = Math.max(340, rect.width);
        let left = rect.left;
        if (left + dropdownWidth > winWidth - 10) {
            left = winWidth - dropdownWidth - 10;
        }
        $floatingDropdown.css({
            top: (rect.bottom + 2) + 'px',
            left: left + 'px',
            width: dropdownWidth + 'px'
        });
    }

    function renderFloatingDropdown(items) {
        $floatingContent.empty();
        if (!items || items.length === 0) {
            $floatingContent.html('<div class="p-3 text-center text-muted small"><i class="fas fa-box-open me-1"></i> No matching van stock found</div>');
            return;
        }

        items.forEach(function(p, idx) {
            const sizeLabel = p.size && p.size !== '-' ? ` <span class="badge bg-secondary text-white" style="font-size:9px;">${p.size}</span>` : '';
            const stockColor = p.stock > 0 ? 'text-success' : 'text-danger';
            let codeParts = [];
            if (p.code) codeParts.push(p.code);
            if (p.barcode) codeParts.push(p.barcode);
            const codeSub = codeParts.length > 0 ? `<div class="text-muted" style="font-size:10px;">${codeParts.join(' &bull; ')}</div>` : '';
            const $item = $(`
                <div class="ss-drop-item ${idx === 0 ? 'selected' : ''}" data-idx="${idx}">
                    <div>
                        <div class="fw-bold" style="font-size:12px;">${p.name}${sizeLabel}</div>
                        ${codeSub}
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-dark" style="font-size:12px;">Rs. ${Number(p.price).toFixed(2)}</div>
                        <div class="${stockColor}" style="font-size:10px; font-weight:700;">${p.stock} in van</div>
                    </div>
                </div>
            `);
            $item.data('product', p);
            $floatingContent.append($item);
        });
    }

    function filterProducts(query) {
        if (!query) return window.PRODUCTS.slice(0, 30);
        const kw = query.toLowerCase().trim();
        return window.PRODUCTS.filter(p => 
            p.name.toLowerCase().includes(kw) ||
            p.code.toLowerCase().includes(kw) ||
            p.barcode.toLowerCase().includes(kw) ||
            p.size.toLowerCase().includes(kw)
        ).slice(0, 30);
    }

    $(document).on('focus', '.ss-prod-search-input', function() {
        activeSearchInput = $(this);
        activeRow = $(this).closest('tr.ss-grid-row');
        positionFloatingDropdown(this);
        const matches = filterProducts($(this).val());
        renderFloatingDropdown(matches);
        $floatingDropdown.show();
    });

    $(document).on('input', '.ss-prod-search-input', function() {
        activeSearchInput = $(this);
        activeRow = $(this).closest('tr.ss-grid-row');
        positionFloatingDropdown(this);
        const matches = filterProducts($(this).val());
        renderFloatingDropdown(matches);
        $floatingDropdown.show();
    });

    // Select item from floating dropdown
    function selectProductItem(p) {
        if (!activeRow || !p) return;
        fillRowWithProduct(activeRow, p);
        $floatingDropdown.hide();
        // Move focus to Qty
        setTimeout(() => {
            activeRow.find('.row-qty').focus().select();
        }, 50);
    }

    function fillRowWithProduct($row, p) {
        $row.find('.row-product-id').val(p.product_id);
        $row.find('.row-variant-id').val(p.variant_id);
        $row.find('.row-product-name').val(p.name);
        $row.find('.ss-prod-search-input').val(p.name);
        $row.find('.row-size-col').text(p.size);
        $row.find('.row-price').val(Number(p.price).toFixed(2)).data('orig-price', p.price);

        const stockColorClass = p.stock > 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
        $row.find('.row-stock-badge').attr('class', 'excel-cell text-center ' + stockColorClass + ' row-stock-badge').text(p.stock);

        recalculateRow($row);
        recalculateTotals();

        // If this was the last row, append a new blank row
        if ($row.is($('#saleGridBody tr.ss-grid-row:last'))) {
            $('#saleGridBody').append(createRowHtml(null));
            reindexRows();
        }
    }

    $(document).on('mousedown', '.ss-drop-item', function(e) {
        e.preventDefault();
        const p = $(this).data('product');
        selectProductItem(p);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#floatingProductDropdown, .ss-prod-search-wrap').length) {
            $floatingDropdown.hide();
        }
    });

    // --- 4. EXCEL KEYBOARD NAVIGATION ---
    $(document).on('keydown', '.ss-prod-search-input', function(e) {
        if ($floatingDropdown.is(':visible')) {
            const $items = $floatingContent.find('.ss-drop-item');
            const $selected = $items.filter('.selected');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const $next = $selected.next('.ss-drop-item');
                if ($next.length) {
                    $selected.removeClass('selected');
                    $next.addClass('selected')[0].scrollIntoView({ block: 'nearest' });
                }
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const $prev = $selected.prev('.ss-drop-item');
                if ($prev.length) {
                    $selected.removeClass('selected');
                    $prev.addClass('selected')[0].scrollIntoView({ block: 'nearest' });
                }
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                if ($selected.length) {
                    selectProductItem($selected.data('product'));
                }
                return;
            }
            if (e.key === 'Escape') {
                $floatingDropdown.hide();
                return;
            }
        }

        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            $row.find('.row-qty').focus().select();
        }
    });

    $(document).on('keydown', '.row-qty', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            $row.find('.row-price').focus().select();
        }
    });

    $(document).on('keydown', '.row-price', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            const $nextRow = $row.next('tr.ss-grid-row');
            if ($nextRow.length) {
                $nextRow.find('.ss-prod-search-input').focus();
            } else {
                $('#btnAddGridRow').trigger('click');
            }
        }
    });

    // Toggle Discount % / PKR button
    $(document).on('click', '.ss-disc-toggle-btn', function() {
        const curr = $(this).attr('data-type');
        const next = curr === 'percent' ? 'pkr' : 'percent';
        $(this).attr('data-type', next).text(next === 'percent' ? '%' : 'PKR');
        if (next === 'percent') {
            $(this).removeClass('btn-pkr').addClass('btn-pct');
        } else {
            $(this).removeClass('btn-pct').addClass('btn-pkr');
        }
        $(this).closest('.ss-disc-wrap').find('.row-disc-type').val(next);
        recalculateRow($(this).closest('tr.ss-grid-row'));
        recalculateTotals();
    });

    // Delete row
    $(document).on('click', '.ss-btn-del-row', function() {
        const $row = $(this).closest('tr.ss-grid-row');
        $row.remove();
        ensureMinimumBlankRows();
        reindexRows();
        recalculateTotals();
    });

    // Add Row button
    $('#btnAddGridRow').on('click', function() {
        const newHtml = createRowHtml(null);
        $('#saleGridBody').append(newHtml);
        reindexRows();
        const $last = $('#saleGridBody tr.ss-grid-row:last');
        $last.find('.ss-prod-search-input').focus();
    });

    // Barcode Scanner input
    $('#barcodeScanner').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = $(this).val().trim();
            if (!query) return;

            const qLower = query.toLowerCase();
            const matched = window.PRODUCTS.find(p => 
                (p.barcode && p.barcode.toLowerCase() === qLower) ||
                (p.code && p.code.toLowerCase() === qLower) ||
                (p.name && p.name.toLowerCase().includes(qLower))
            );

            if (!matched) {
                alert("No item matching '" + query + "' found in your van custody stock.");
                $(this).val('').focus();
                return;
            }

            // Check if already in grid
            let existingRow = null;
            $('#saleGridBody tr.ss-grid-row').each(function() {
                if ($(this).find('.row-product-id').val() == matched.product_id) {
                    existingRow = $(this);
                    return false;
                }
            });

            if (existingRow) {
                const $qInput = existingRow.find('.row-qty');
                const curr = parseFloat($qInput.val()) || 0;
                $qInput.val(curr + 1);
                recalculateRow(existingRow);
                recalculateTotals();
                $qInput.focus().select();
            } else {
                let targetRow = null;
                $('#saleGridBody tr.ss-grid-row').each(function() {
                    if (!$(this).find('.row-product-id').val()) {
                        targetRow = $(this);
                        return false;
                    }
                });
                if (!targetRow) {
                    $('#saleGridBody').append(createRowHtml(null));
                    reindexRows();
                    targetRow = $('#saleGridBody tr.ss-grid-row:last');
                }
                fillRowWithProduct(targetRow, matched);
                targetRow.find('.row-qty').focus().select();
            }

            $(this).val('');
        }
    });

    // Quick Panel Toggle
    $('#btnToggleQuickPanel').on('click', function() {
        $('#quickProductsPanel').slideToggle(150);
        $(this).toggleClass('active');
    });

    // Quick Panel Card Click
    $(document).on('click', '.ss-qp-card', function() {
        const prodId = $(this).data('product-id');
        const matched = window.PRODUCTS.find(p => p.product_id == prodId);
        if (matched) {
            let targetRow = null;
            $('#saleGridBody tr.ss-grid-row').each(function() {
                if (!$(this).find('.row-product-id').val()) {
                    targetRow = $(this);
                    return false;
                }
            });
            if (!targetRow) {
                $('#saleGridBody').append(createRowHtml(null));
                reindexRows();
                targetRow = $('#saleGridBody tr.ss-grid-row:last');
            }
            fillRowWithProduct(targetRow, matched);
            targetRow.find('.row-qty').focus().select();
        }
    });

    // Quick Panel Category Filter
    $(document).on('click', '.ss-cat-tab', function() {
        $('.ss-cat-tab').removeClass('active');
        $(this).addClass('active');
        const cat = $(this).data('cat');
        if (cat === 'all') {
            $('.ss-qp-card').show();
        } else {
            $('.ss-qp-card').hide();
            $(`.ss-qp-card[data-cat="${cat}"]`).show();
        }
    });

    // Quick Panel Search Filter
    $('#qpSearchFilter').on('input', function() {
        const q = $(this).val().toLowerCase();
        $('.ss-qp-card').each(function() {
            const name = $(this).data('name') || '';
            $(this).toggle(name.includes(q));
        });
    });

    // --- 5. RE-CALCULATIONS ---
    $(document).on('input', '.row-qty, .row-price, .row-disc-val', function() {
        const $row = $(this).closest('tr.ss-grid-row');
        recalculateRow($row);
        recalculateTotals();
    });

    function recalculateRow($row) {
        const qty   = parseFloat($row.find('.row-qty').val()) || 0;
        const price = parseFloat($row.find('.row-price').val()) || 0;
        const discVal = parseFloat($row.find('.row-disc-val').val()) || 0;
        const discType = $row.find('.row-disc-type').val() || 'percent';

        let calcDisc = 0;
        if (discType === 'percent') {
            calcDisc = (qty * price) * (discVal / 100);
        } else {
            calcDisc = discVal;
        }
        calcDisc = Math.min(qty * price, Math.max(0, calcDisc));
        const amount = Math.max(0, (qty * price) - calcDisc);

        $row.find('.row-disc-amount').val(calcDisc.toFixed(2));
        $row.find('.row-amount').val(amount.toFixed(2));
    }

    function recalculateTotals() {
        let totalAmount = 0;
        let lineDiscount = 0;
        let totalItems = 0;
        let totalQty = 0;

        $('#saleGridBody tr.ss-grid-row').each(function() {
            const pId = $(this).find('.row-product-id').val();
            if (pId) {
                const q = parseFloat($(this).find('.row-qty').val()) || 0;
                const p = parseFloat($(this).find('.row-price').val()) || 0;
                const d = parseFloat($(this).find('.row-disc-amount').val()) || 0;
                totalAmount += (q * p);
                lineDiscount += d;
                totalItems++;
                totalQty += q;
            }
        });

        const netTotal = Math.max(0, totalAmount - lineDiscount);

        $('#gridItemsCountSummary').text(`${totalItems} Item(s) | ${totalQty} Total Qty`);
        $('#gridTotalAmountDisplay').text('Rs. ' + netTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('#summaryTotalAmount').text(totalAmount.toFixed(2));
        $('#summaryLineDiscount').text(lineDiscount.toFixed(2));
        $('#summaryNetTotal').text(netTotal.toFixed(2));

        // Payment logic - amount is NOT auto-filled; user can enter or click Smart Buttons
        const $pmInput = $('#pmAmountInput');
        const totalPaid = parseFloat($pmInput.val()) || 0;
        $('#summaryTotalPaid').text(totalPaid.toFixed(2));

        const balance = netTotal - totalPaid;
        $('#summaryChangeAmount').text(Math.abs(balance).toFixed(2));

        if (balance <= 0) {
            $('#summaryChangeBox').attr('class', 'ss-change-box paid').html(`<span>Change Due:</span> <span>${Math.abs(balance).toFixed(2)}</span>`);
        } else {
            $('#summaryChangeBox').attr('class', 'ss-change-box').html(`<span>Change / Balance:</span> <span>${balance.toFixed(2)}</span>`);
        }

        // Payment type determination
        if (totalPaid >= netTotal && netTotal > 0) {
            $('#inputPaymentType').val('cash');
        } else if (totalPaid > 0) {
            $('#inputPaymentType').val('partial');
        } else {
            $('#inputPaymentType').val('credit');
        }

        $('#inputTotalDiscount').val(lineDiscount.toFixed(2));
        $('#inputPaidAmount').val(totalPaid.toFixed(2));
    }

    // Payment account change
    $('#pmAccountSelect').on('change', function() {
        const val = $(this).val();
        if (val === 'credit') {
            $('#pmAmountInput').val('0.00');
            $('#inputPaymentType').val('credit');
        } else {
            const net = parseFloat($('#summaryNetTotal').text()) || 0;
            $('#pmAmountInput').val(net.toFixed(2));
            $('#inputPaymentType').val('cash');
        }
        recalculateTotals();
    });

    $('#pmAmountInput').on('input', function() {
        const paid = parseFloat($(this).val()) || 0;
        const net = parseFloat($('#summaryNetTotal').text()) || 0;
        if (paid >= net && net > 0) {
            $('#inputPaymentType').val('cash');
        } else if (paid > 0) {
            $('#inputPaymentType').val('partial');
        } else {
            $('#inputPaymentType').val('credit');
        }
        recalculateTotals();
    });

    // --- SMART PAYMENT BUTTONS ---
    $(document).on('click', '.smart-pay-btn', function() {
        const action = $(this).data('action');
        const val = $(this).data('val');
        const $pmInput = $('#pmAmountInput');
        const netTotal = parseFloat($('#summaryNetTotal').text()) || 0;

        if (action === 'full') {
            $pmInput.val(netTotal.toFixed(2));
        } else if (action === 'clear') {
            $pmInput.val('0.00');
        } else if (val) {
            $pmInput.val(parseFloat(val).toFixed(2));
        }
        recalculateTotals();
    });

    $(document).on('input', '#pmAmountInput', function() {
        recalculateTotals();
    });

    // --- FORM SUBMISSION (TOP SAVE & MAIN SAVE) ---
    function submitDistributorSale() {
        const custId = $('#customerSelect').val();
        if (!custId) {
            alert('Please select a Retail Shop / Customer before saving.');
            $('#customerSelect').select2('open');
            return;
        }

        let validRows = 0;
        $('#saleGridBody tr.ss-grid-row').each(function() {
            const pId = $(this).find('.row-product-id').val();
            const q = parseFloat($(this).find('.row-qty').val()) || 0;
            if (pId && q > 0) {
                validRows++;
            }
        });

        if (validRows === 0) {
            alert('Please add at least one product with quantity before saving.');
            $('#saleGridBody tr.ss-grid-row:first .ss-prod-search-input').focus();
            return;
        }

        // Disable unselected rows so no empty arrays are submitted
        $('#saleGridBody tr.ss-grid-row').each(function() {
            const pId = $(this).find('.row-product-id').val();
            if (!pId) {
                $(this).find('input, select').prop('disabled', true);
            }
        });

        $('#btnSaveComplete').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving Sale...');
        $('#btnTopSave').prop('disabled', true);
        document.getElementById('dposScreenForm').submit();
    }

    $('#btnTopSave, #btnSaveComplete').on('click', function(e) {
        e.preventDefault();
        submitDistributorSale();
    });

    // Open Quick Add Account Modal
    $("#btnAddAccountRow").on("click", function() {
        $("#qaTitle").val("");
        $("#qaBankName").val("");
        $("#qaAccountNumber").val("");
        $("#qaOpeningBalance").val("0");
        var modal = new bootstrap.Modal(document.getElementById("quickAddAccountModal"));
        modal.show();
    });

    // Submit Quick Add Account via AJAX
    $("#quickAddAccountForm").on("submit", function(e) {
        e.preventDefault();
        const $btn = $("#btnSubmitQuickAccount");
        $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('distributor.accounts.store_quick') }}",
            type: "POST",
            data: $(this).serialize(),
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(res) {
                $btn.prop("disabled", false).html('<i class="fas fa-check me-1"></i> Save & Select Account');
                if (res.success && res.account) {
                    var mEl = document.getElementById("quickAddAccountModal");
                    var mInst = bootstrap.Modal.getInstance(mEl);
                    if (mInst) mInst.hide();
                    
                    let label = res.account.title;
                    if (res.account.account_number) {
                        label += ` (${res.account.account_number})`;
                    }
                    
                    let optGroup = $("#pmAccountSelect optgroup");
                    if (optGroup.length === 0) {
                        $("#pmAccountSelect").append('<optgroup label="My Bank & Online Accounts"></optgroup>');
                        optGroup = $("#pmAccountSelect optgroup");
                    }
                    optGroup.append(`<option value="${res.account.id}" selected>${label}</option>`);
                    $("#pmAccountSelect").val(res.account.id).trigger("change");
                }
            },
            error: function(xhr) {
                $btn.prop("disabled", false).html('<i class="fas fa-check me-1"></i> Save & Select Account');
                alert(xhr.responseJSON?.message || "Error creating account. Please check inputs.");
            }
        });
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'F9') {
            e.preventDefault();
            submitDistributorSale();
        }
        if (e.key === 'F4') {
            e.preventDefault();
            $('#btnAddGridRow').trigger('click');
        }
    });

    // Quick Add Retailer AJAX
    $('#quickRetailerForm').on('submit', function(e) {
        e.preventDefault();
        const $f = $(this);
        const $btn = $('#btnSaveQuickCust');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: $f.attr('action'),
            method: 'POST',
            data: $f.serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save & Select');
                $('#newCustomerModal').modal('hide');
                $f[0].reset();

                if (res.customer) {
                    const c = res.customer;
                    const optText = c.shop_name + (c.owner_name ? ' (' + c.owner_name + ')' : '') + (c.area ? ' - ' + c.area : '');
                    const newOpt = new Option(optText, c.id, true, true);
                    $(newOpt).attr('data-type', 'retailer');
                    $(newOpt).attr('data-balance', c.current_balance || 0);
                    $(newOpt).attr('data-phone', c.phone || '');
                    $('#customerSelect').append(newOpt).trigger('change');
                    updateCustomerBadge();
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save & Select');
                alert(xhr.responseJSON?.message || 'Error saving retail customer.');
            }
        });
    });
});
</script>
@endsection