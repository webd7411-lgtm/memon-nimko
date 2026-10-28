@extends('admin_panel.layout.app')

@section('content')
<style>
/* ==========================================================================
   SALE SCREEN — EXCEL SPREADSHEET STYLE POS & BILLING INTERFACE
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

/* FLUSH FULL-WIDTH WRAPPER (NO SIDE SPACING) */
.ss-wrapper {
    font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--ss-text-main);
    padding: 0 2px 14px 2px !important;
    margin: 0 !important;
    width: 100% !important;
    position: relative;
}

/* SELECT2 ENHANCEMENTS FOR SALE SCREEN */
.select2-container {
    width: 100% !important;
}
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
.select2-search--dropdown .select2-search__field:focus {
    border-color: var(--ss-primary) !important;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.12) !important;
}
.select2-results__group {
    background: #f1f5f9 !important;
    color: #0f766e !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 5px 10px !important;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--ss-primary) !important;
    color: #ffffff !important;
}
.select2-results__option {
    padding: 6px 10px !important;
    font-weight: 600;
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

/* SECTION HEADER & BARCODE */
.ss-order-header {
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-bottom: none;
    border-radius: var(--ss-radius) var(--ss-radius) 0 0;
    padding: 7px 12px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.ss-order-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.5px;
}

.ss-order-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #0284c7;
    border-radius: 2px;
}

.ss-barcode-box {
    position: relative;
    flex: 1;
    max-width: 380px;
    min-width: 240px;
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
}
.ss-panel-toggle-btn:hover, .ss-panel-toggle-btn.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

.ss-btn-create-prod {
    height: 32px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #fef08a;
    background: #fefce8;
    color: #ca8a04;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ss-btn-create-prod:hover {
    background: #fef08a;
    color: #854d0e;
}

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
.ss-btn-add-row:hover {
    background: #1d4ed8;
}

/* QUICK PRODUCTS PANEL DRAWER */
.ss-quick-panel {
    display: none;
    background: #ffffff;
    border: 1px solid var(--ss-border);
    border-top: none;
    padding: 10px 12px;
    border-radius: 0 0 var(--ss-radius) var(--ss-radius);
    margin-bottom: 8px;
    box-shadow: var(--ss-shadow-sm);
}

.ss-cat-tabs {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 6px;
    margin-bottom: 8px;
    scrollbar-width: thin;
}
.ss-cat-tab {
    white-space: nowrap;
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 11.5px;
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
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 6px;
    max-height: 220px;
    overflow-y: auto;
}

.ss-qp-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px;
    cursor: pointer;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 85px;
}
.ss-qp-card:hover {
    border-color: #0f766e;
    background: #f0fdfa;
}
.ss-qp-name {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
    margin-bottom: 3px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.ss-qp-price {
    font-size: 12px;
    font-weight: 800;
    color: #0f766e;
}
.ss-qp-stock {
    font-size: 9px;
    font-weight: 600;
    color: #64748b;
}

/* ==========================================================================
   EXCEL SPREADSHEET STYLE DATA GRID TABLE (FLUSH & ZERO CELL SPACING)
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
    min-height: 320px;
    overflow-y: auto !important;
    overflow-x: auto;
    position: relative;
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
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}
.ss-excel-table thead th:first-child {
    border-left: 1px solid #cbd5e1;
}

.ss-excel-table tbody tr {
    height: 33px;
}

.ss-excel-table tbody tr:hover {
    background-color: #f8fafc;
}

.ss-excel-table tbody td {
    padding: 0 !important;
    margin: 0 !important;
    vertical-align: middle;
    border-bottom: 1px solid #cbd5e1;
    border-right: 1px solid #cbd5e1;
    border-top: none;
    border-left: none;
    font-size: 12.5px;
    position: relative;
    height: 33px;
}
.ss-excel-table tbody td:first-child {
    border-left: 1px solid #cbd5e1;
}

.ss-excel-table tbody tr {
    height: 33px;
}

.ss-excel-table tbody tr:hover {
    background-color: #f8fafc;
}

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

/* Product Search Input Cell */
.ss-prod-search-wrap {
    position: relative;
    width: 100%;
    height: 100%;
}
.ss-prod-search-wrap .ss-excel-input {
    padding-right: 24px;
}
.ss-prod-search-icon {
    position: absolute;
    right: 7px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}

/* DISCOUNT CELL WITH % / PKR TOGGLE */
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
    font-size: 10.5px;
    font-weight: 800;
    cursor: pointer;
    margin: 0 4px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    user-select: none;
}
.ss-disc-toggle-btn.btn-pct {
    background: #0f766e;
    color: #ffffff;
}
.ss-disc-toggle-btn.btn-pct:hover {
    background: #0d9488;
}
.ss-disc-toggle-btn.btn-pkr {
    background: #0284c7;
    color: #ffffff;
}
.ss-disc-toggle-btn.btn-pkr:hover {
    background: #0369a1;
}

/* Delete Row Button */
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
.ss-btn-del-row:hover {
    background: #fee2e2;
    color: #b91c1c;
}

/* Grid Footer */
.ss-grid-total-row {
    background: #f1f5f9;
    border-top: 1px solid #cbd5e1;
    padding: 8px 14px;
    display: flex;
    justify-content: flex-end;
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
}

/* RIGHT COLUMN: SUMMARY & PAYMENT */
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
}
.ss-summary-row.text-danger .val { color: #ef4444; }

.ss-summary-row.net-total {
    border-top: 1.5px dashed #cbd5e1;
    border-bottom: 1.5px dashed #cbd5e1;
    padding: 8px 0;
    margin: 5px 0;
}
.ss-summary-row.net-total .label {
    font-size: 14.5px;
    font-weight: 800;
    color: #0f172a;
}
.ss-summary-row.net-total .val {
    font-size: 19px;
    font-weight: 900;
    color: #0284c7;
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
.ss-btn-add-account:hover {
    background: #2563eb;
    color: #ffffff;
}

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
}
.ss-pm-input:focus, .ss-pm-select:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.12);
}
.ss-pm-remove {
    flex: 0 0 24px !important;
    width: 24px;
    height: 32px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 12px;
    font-weight: 800;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: all 0.15s ease;
}
.ss-pm-remove:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

/* BIG SAVE BUTTON */
.ss-btn-save-complete {
    width: 100%;
    height: 48px;
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
}
.ss-btn-save-complete:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
}

/* ==========================================================================
   GLOBAL FLOATING PRODUCT SEARCH DROPDOWN (FRONT & UNCLIPPED)
   ========================================================================== */
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
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 800;
    color: #0f766e;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #ccfbf1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ss-floating-dropdown::-webkit-scrollbar {
    width: 5px;
}
.ss-floating-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.ss-prod-item {
    padding: 7px 10px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    font-size: 12.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ss-prod-item:last-child { border-bottom: none; }
.ss-prod-item:hover, .ss-prod-item.active {
    background: #f0fdfa;
    border-left: 3px solid #0f766e;
}

.ss-prod-item-title {
    font-weight: 700;
    color: #0f172a;
    font-size: 12.5px;
    margin-bottom: 1px;
}
.ss-prod-item-sub {
    font-size: 11px;
    color: #64748b;
    display: flex;
    gap: 6px;
    align-items: center;
}

.ss-prod-item-price {
    font-weight: 900;
    color: #0f766e;
    font-size: 13.5px;
    text-align: right;
    white-space: nowrap;
}

.ss-prod-item-stock-badge {
    padding: 1px 5px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
}
.ss-prod-item-stock-badge.in-stock { background: #dcfce7; color: #15803d; }
.ss-prod-item-stock-badge.out-stock { background: #fee2e2; color: #b91c1c; }
</style>

<div class="container-fluid ss-wrapper">
    <form id="saleScreenForm" action="{{ isset($sale) && $sale->id ? route('sales.update', $sale->id) : route('sales.store') }}" method="POST">
        @csrf
        @if(isset($sale) && $sale->id)
            @method('PUT')
            <input type="hidden" name="sale_id" id="editSaleId" value="{{ $sale->id }}">
        @endif
        <input type="hidden" name="action" id="formAction" value="sale">
        <input type="hidden" name="source" value="screen">
        <input type="hidden" name="order_type" value="Walk-in">
        <input type="hidden" name="total_subtotal" id="inputTotalSubtotal" value="0">
        <input type="hidden" name="total_extra_cost" id="inputTotalExtraCost" value="0">
        <input type="hidden" name="total_net" id="inputTotalNet" value="0">
        <input type="hidden" name="cash" id="inputCash" value="0">
        <input type="hidden" name="card" id="inputCard" value="0">
        <input type="hidden" name="card_account_id" id="inputCardAccountId" value="">
        <input type="hidden" name="change" id="inputChange" value="0">
        <input type="hidden" name="total_items" id="inputTotalItems" value="0">
        <input type="hidden" name="total_pieces" id="inputTotalPieces" value="0">

        <!-- ==========================================
             TOP CONTROLS BAR: 1. CUSTOMER / BOOKER / DISTRIBUTOR (FIRST), 2. DATE, 3. M.BILL, 4. QUICK PANEL, 5. SAVE
             ========================================== -->
        <div class="ss-topbar-card">
            <div class="row g-2 align-items-end">
                <!-- 1. Customer (FIRST & PROMINENT) -->
                <div class="col-lg-4 col-md-5 col-12">
                    <div class="ss-field-group">
                        <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <label>
                                <i class="fas fa-user-check me-1 text-success"></i> Customer / Booker / Dist. <span class="text-danger">*</span>
                                @if(isset($sale))
                                    <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;"><i class="fas fa-edit me-1"></i> Edit Mode: {{ $sale->invoice_no }}</span>
                                    <a href="{{ route('sale.screen') }}" class="btn btn-outline-secondary btn-sm py-0 px-1 ms-1" style="font-size: 10px;" title="Exit Edit Mode"><i class="fas fa-plus"></i> New</a>
                                @endif
                            </label>
                            <span id="customerTypeBadge" class="badge bg-light text-secondary border" style="font-size: 10px; padding: 2px 6px;">
                                <i class="fas fa-user me-1"></i> Walk-in
                            </span>
                        </div>
                        <div class="d-flex gap-1 align-items-center">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <select name="customer" id="customerSelect" class="ss-select-control w-100" required>
                                    <option value="Walk-in Customer" data-type="walkin" {{ (!isset($sale) || $sale->customer === "Walk-in Customer") ? "selected" : "" }}>👤 Walk-in Customer</option>

                                    @if((isset($orderBookers) && $orderBookers->count() > 0) || (isset($salesOfficers) && $salesOfficers->count() > 0))
                                        <optgroup label="🛵 ORDER BOOKERS">
                                            @if(isset($orderBookers))
                                                @foreach($orderBookers as $ob)
                                                    <option value="booker_{{ $ob->id }}" data-type="order_booker" {{ (isset($sale) && $sale->customer === "booker_" . $ob->id) ? "selected" : "" }} data-phone="{{ $ob->phone }}" data-balance="{{ (float)$ob->current_balance }}">
                                                        🛵 {{ $ob->name }} ({{ $ob->code ?? 'Booker' }}) {{ $ob->phone ? '- ' . $ob->phone : '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                            @if(isset($salesOfficers))
                                                @foreach($salesOfficers as $so)
                                                    <option value="officer_{{ $so->id }}" data-type="order_booker" {{ (isset($sale) && $sale->customer === "officer_" . $so->id) ? "selected" : "" }} data-phone="{{ $so->mobile }}" data-balance="0">
                                                        🛵 {{ $so->name }} (Sales Officer) {{ $so->mobile ? '- ' . $so->mobile : '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                    @endif

                                    @if(isset($distributors) && $distributors->count() > 0)
                                        <optgroup label="🚚 DISTRIBUTORS">
                                            @foreach($distributors as $dist)
                                                <option value="dist_{{ $dist->id }}" data-type="distributor" {{ (isset($sale) && $sale->customer === "dist_" . $dist->id) ? "selected" : "" }} data-phone="{{ $dist->phone }}" data-balance="{{ (float)$dist->current_balance }}">
                                                    🚚 {{ $dist->name }} ({{ $dist->code ?? 'Distributor' }}) {{ $dist->phone ? '- ' . $dist->phone : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if(isset($customers) && $customers->count() > 0)
                                        <optgroup label="📋 REGULAR CUSTOMERS">
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}" data-type="customer" {{ (isset($sale) && $sale->customer == $c->id) ? "selected" : "" }} data-credit="{{ $c->credit_allowed ? '1' : '0' }}" data-phone="{{ $c->mobile }}">
                                                    👤 {{ $c->customer_name }} {{ $c->mobile ? '- ' . $c->mobile : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if(isset($distributorCustomers) && $distributorCustomers->count() > 0)
                                        <optgroup label="🏪 MARKET / RETAIL SHOPS">
                                            @foreach($distributorCustomers as $dc)
                                                <option value="dc_{{ $dc->id }}" data-type="distributor_customer" {{ (isset($sale) && $sale->customer === "dc_" . $dc->id) ? "selected" : "" }} data-phone="{{ $dc->phone }}" data-balance="{{ (float)$dc->current_balance }}">
                                                    🏪 {{ $dc->shop_name }} ({{ $dc->owner_name }}) {{ $dc->area ? '- ' . $dc->area : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <button type="button" class="ss-top-btn ss-btn-new-cust px-2" data-bs-toggle="modal" data-bs-target="#newCustomerModal" title="Create New Customer">
                                <i class="fas fa-plus"></i> New
                            </button>
                            <button type="button" class="ss-top-btn ss-btn-walkin px-2" id="btnSetWalkin" title="Reset to Walk-in (Alt+W)">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Date -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-calendar-alt me-1 text-primary"></i> Date</label>
                        <input type="date" name="sale_date" id="saleDateInput" class="ss-input-control" value="{{ isset($sale) && $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                </div>

                <!-- 3. M.Bill / Remarks -->
                <div class="col-lg-2 col-md-2 col-6">
                    <div class="ss-field-group">
                        <label><i class="far fa-comment-dots me-1 text-info"></i> M.Bill / Remarks</label>
                        <input type="text" name="reference" id="remarksInput" class="ss-input-control" placeholder="Enter remarks" value="{{ $sale->reference ?? '' }}">
                    </div>
                </div>

                <!-- 4. Quick Products Panel Toggle Button -->
                <div class="col-lg-2 col-md-1 col-6">
                    <button type="button" class="ss-panel-toggle-btn w-100 justify-content-center" id="btnToggleQuickPanel" style="height: 34px;">
                        <i class="fas fa-th-large me-1"></i> Quick Panel
                    </button>
                </div>

                <!-- 5. Quick Save Top Button -->
                <div class="col-lg-2 col-md-2 col-6 text-end">
                    <button type="button" class="ss-top-btn btn-save-sale w-100" id="btnTopSave" style="height: 34px;">
                        <i class="fas fa-{{ isset($sale) ? 'save' : 'check' }} me-1"></i> {{ isset($sale) ? 'Update Sale (F9)' : 'Save Sale (F9)' }}
                    </button>
                </div>
            </div>
        </div>

                <!-- ==========================================
             MAIN CONTENT: EXCEL GRID (LEFT) & SIDEBAR (RIGHT)
             ========================================== -->
        <div class="row gx-2 gy-2">
            <!-- LEFT COLUMN: ORDER ITEMS EXCEL GRID -->
            <div class="col-lg-8 col-xl-8 ss-col-left">
                <!-- Clean Barcode Scanner Strip with Add Row (F4) button -->
                <div class="ss-order-header d-flex align-items-center gap-2">
                    <div class="ss-barcode-box flex-grow-1" style="max-width: 100%;">
                        <i class="fas fa-barcode"></i>
                        <input type="text" id="barcodeScanner" class="ss-barcode-input" placeholder="Scan Barcode (Variant / SKU)..." autocomplete="off" autofocus>
                        <button type="button" id="btnClearBarcode" class="ss-btn-clear-barcode">✕</button>
                    </div>
                    <button type="button" class="ss-btn-add-row" id="btnAddGridRow" title="Add New Row (F4)" style="height: 32px; white-space: nowrap;">
                        <i class="fas fa-plus"></i> Add Row (F4)
                    </button>
                </div>

                <!-- COLLAPSIBLE QUICK PRODUCTS PANEL -->
                <div class="ss-quick-panel" id="quickProductsPanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <!-- Category Tabs -->
                        <div class="ss-cat-tabs flex-grow-1" id="qpCategoryTabs">
                            <span class="ss-cat-tab active" data-cat="all">All Items</span>
                            @foreach($categories as $cat)
                                <span class="ss-cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                        <input type="text" id="qpSearchFilter" class="ss-input-control" placeholder="Filter items..." style="width: 160px; height: 28px; font-size: 11px;">
                    </div>
                    <div class="ss-qp-grid" id="qpGridContainer">
                        @foreach($categories as $cat)
                            @foreach($cat->products as $p)
                                @php
                                    $price = (float)($p->price ?? 0);
                                    if ($p->variants && $p->variants->count() > 0) {
                                        $vDef = $p->variants->firstWhere('is_default', true) ?? $p->variants->first();
                                        if ($vDef) $price = (float)$vDef->price;
                                    }
                                    $stk = (float)($p->total_stock ?? 0);
                                @endphp
                                <div class="ss-qp-card" 
                                     data-cat="{{ $cat->id }}" 
                                     data-name="{{ strtolower($p->item_name) }}"
                                     data-product-id="{{ $p->id }}"
                                     data-item-code="{{ $p->item_code }}"
                                     data-item-name="{{ $p->item_name }}"
                                     data-price="{{ $price }}"
                                     data-stock="{{ $stk }}"
                                     data-unit="{{ $p->unit_type ?? 'piece' }}"
                                     data-variants="{{ json_encode($p->variants) }}">
                                     <div class="ss-qp-name">{{ $p->item_name }}</div>
                                     <div class="ss-qp-price">Rs. {{ number_format($price, 0) }}</div>
                                     <div class="ss-qp-stock"><i class="fas fa-boxes me-1"></i>{{ $stk }} in stock</div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <!-- EXCEL SPREADSHEET TABLE (Color & PCS removed, Scrollable with Fixed Height) -->
                <div class="ss-table-card">
                    <div class="ss-table-responsive-wrap">
                        <table class="ss-excel-table" id="saleGridTable">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">#</th>
                                    <th style="width: 42%;">PRODUCT</th>
                                    <th style="width: 75px;">STOCK</th>
                                    <th style="width: 80px;">QTY</th>
                                    <th style="width: 90px;">SIZE</th>
                                    <th style="width: 95px;">PRICE</th>
                                    <th style="width: 75px;" class="text-center" title="Trade Offer (Free / Scheme Qty)">T.O</th>
                                    <th style="width: 130px;">DISCOUNT</th>
                                    <th style="width: 115px;">AMOUNT</th>
                                    <th style="width: 36px;">✕</th>
                                </tr>
                            </thead>
                            <tbody id="saleGridBody">
                                <!-- Dynamic Excel Rows appended via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- GRID FOOTER TOTAL -->
                    <div class="ss-grid-total-row">
                        <span class="text-muted" id="gridItemsCountSummary" style="font-size: 12px; font-weight: 600;">0 Items | 0 Qty</span>
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
                        <!-- Primary default payment row -->
                        <div class="ss-pm-row" data-index="0">
                            <select class="ss-pm-select pm-account-select" name="payment_account[]">
                                <option value="cash" selected>Cash in hand</option>
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="any" min="0" class="ss-pm-input pm-amount-input" name="payment_amount[]" placeholder="0.00" value="0.00">
                        </div>
                    </div>
                </div>

                <!-- BIG SAVE BUTTON -->
                <button type="submit" class="ss-btn-save-complete" id="btnSaveComplete">
                    <i class="fas fa-save"></i> {{ isset($sale) ? 'Update & Save Changes (F9)' : 'Save & Complete (F9)' }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- ==========================================================================
     GLOBAL FLOATING PRODUCT SEARCH DROPDOWN
     Direct child of body - NEVER clipped, ALWAYS on front of everything!
     ========================================================================== -->
<div id="floatingProductDropdown" class="ss-floating-dropdown">
    <div class="ss-dropdown-header">
        <span><i class="fas fa-boxes me-1"></i> Select Product</span>
        <span class="text-muted" style="font-size: 10px;">Click or press Enter</span>
    </div>
    <div id="floatingDropdownContent">
        <!-- Rendered items go here -->
    </div>
</div>

<!-- ==========================================
     MODAL: QUICK NEW CUSTOMER
     ========================================== -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #0f766e; color: #fff; border-radius: 12px 12px 0 0;">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> Add New Customer</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickCustomerForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="Full Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Mobile / Phone</label>
                        <input type="text" name="mobile" class="form-control" placeholder="03XXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Customer address..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Opening Balance (PKR)</label>
                        <input type="number" step="any" name="opening_balance" class="form-control" placeholder="0.00" value="0">
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold"><i class="fas fa-check me-1"></i> Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL: QUICK CREATE PRODUCT
     ========================================== -->
<div class="modal fade" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #0284c7; color: #fff; border-radius: 12px 12px 0 0;">
                <h6 class="modal-title fw-bold"><i class="fas fa-bolt me-2"></i> Quick Create Product</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickProductForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control" required placeholder="Product / Item Name">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Unit Type</label>
                            <select name="unit_type" class="form-select">
                                <option value="piece" selected>Piece</option>
                                <option value="kg">KG</option>
                                <option value="pound">Pound</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Sale Price <span class="text-danger">*</span></label>
                            <input type="number" step="any" min="0" name="price" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Initial Stock</label>
                            <input type="number" step="any" min="0" name="stock" class="form-control" placeholder="0" value="10">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Barcode / SKU (Optional)</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Leave empty to auto-generate">
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fas fa-check me-1"></i> Add Product & Insert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL: SALE SUCCESS & PRINT
     ========================================== -->
<div class="modal fade" id="saleSuccessModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius: 16px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="mb-3">
                <div style="width: 68px; height: 68px; border-radius: 50%; background: #dcfce7; color: #16a34a; font-size: 34px; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">Sale Saved Successfully!</h4>
            <p class="text-muted mb-3" id="successInvoiceText">Invoice generated successfully.</p>

            <div class="p-3 mb-4 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted fw-bold">Net Total:</span>
                    <span class="fw-bold text-dark" id="successModalNetTotal">Rs. 0.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fw-bold">Change Returned:</span>
                    <span class="fw-bold text-success" id="successModalChange">Rs. 0.00</span>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="#" target="_blank" id="btnReceiptPrint" class="btn btn-success fw-bold py-2">
                    <i class="fas fa-receipt me-1"></i> Print Thermal Receipt (80mm)
                </a>
                <a href="#" target="_blank" id="btnInvoicePrint" class="btn btn-outline-primary fw-bold py-2">
                    <i class="fas fa-print me-1"></i> Print A4 Invoice
                </a>
                <button type="button" class="btn btn-light fw-bold py-2" id="btnNewSaleReset">
                    <i class="fas fa-plus me-1"></i> Start New Sale (Enter)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ⚡ IN-MEMORY PRODUCT CATALOG FOR 0-MILLISECOND LIGHTNING SEARCH & SCAN
    window.POS_CATALOG = @json($productsCatalog ?? []);

    function searchLocalCatalog(query) {
        if (!window.POS_CATALOG || !window.POS_CATALOG.length) return [];
        if (!query || !query.trim()) {
            return window.POS_CATALOG.slice(0, 30);
        }

        const cleanQ = query.toLowerCase().trim();
        const keywords = cleanQ.split(/\s+/).filter(Boolean);

        // 1. Direct barcode / SKU exact match gets top priority
        const exactMatch = window.POS_CATALOG.filter(p => 
            (p.barcode && String(p.barcode).toLowerCase() === cleanQ) || 
            (p.item_code && String(p.item_code).toLowerCase() === cleanQ)
        );
        if (exactMatch.length) {
            return exactMatch;
        }

        // 2. Multi-word keyword matching across name, code, barcode, brand, size
        return window.POS_CATALOG.filter(item => {
            const name = (item.item_name || '').toLowerCase();
            const code = (item.item_code || '').toLowerCase();
            const barcode = (item.barcode || '').toLowerCase();
            const brand = (item.brand || '').toLowerCase();
            const size = (item.size || '').toLowerCase();

            return keywords.every(kw => 
                name.includes(kw) || 
                code.includes(kw) || 
                barcode.includes(kw) || 
                brand.includes(kw) ||
                size.includes(kw)
            );
        }).slice(0, 35);
    }

    // Move floating dropdown directly to body to guarantee it breaks out of all parent containers
    const $floatingDropdown = $('#floatingProductDropdown').appendTo('body');
    const $floatingContent = $('#floatingDropdownContent');

    let activeSearchInput = null;
    let activeRow = null;

    // --- SYNTHETIC BEEP AUDIO GENERATOR ---
    function playBeep(frequency, duration) {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.value = frequency || 850;
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + (duration || 0.12));
            osc.start();
            osc.stop(audioCtx.currentTime + (duration || 0.12));
        } catch(e) {}
    }

    // --- ROW TEMPLATE GENERATION (EXCEL STYLE - COLOR & PCS REMOVED) ---
    const MIN_BLANK_ROWS = 8;
    let rowCounter = 0;

    function createRowHtml(item) {
        rowCounter++;
        const id         = item ? (item.product_id || item.id || '') : '';
        const variantId  = item ? (item.variant_id || '') : '';
        const name       = item ? (item.item_name || item.base_name || item.name || '') : '';
        const code       = item ? (item.code || item.item_code || item.barcode || '') : '';
        const hasItem    = !!id;
        const stockVal   = item ? parseFloat(item.stock !== undefined ? item.stock : 0) : 0;
        const stockDisplay = hasItem ? stockVal : '-';
        const qty        = item ? (parseFloat(item.qty || 1)) : 1;
        const size       = item ? (item.size || item.size_label || item.variant_name || '-') : '-';
        const color      = item ? (item.color || '-') : '-';
        const pcs        = item ? (item.pcs || item.unit_type || 'piece') : 'piece';
        const price      = item ? (parseFloat(item.price || 0)) : 0;
        const origPrice  = item ? (parseFloat(item.original_price || item.price || 0)) : price;
        const tradeOffer = item ? (parseFloat(item.trade_offer || 0)) : 0;

        // Default discount is 0 with percent (%) mode
        const discVal    = item ? (parseFloat(item.disc_val || item.disc || 0)) : 0;
        const discType   = item ? (item.disc_type || 'percent') : 'percent';

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
                        <input type="hidden" name="item_code[]" class="row-item-code" value="${code}">
                        <input type="hidden" name="uom[]" class="row-uom" value="">
                        <input type="hidden" name="unit[]" class="row-unit" value="${pcs}">
                        <input type="hidden" name="color[]" class="row-color" value="${color}">

                        <input type="text" class="ss-excel-input ss-prod-search-input" placeholder="Search Product (Name / SKU / Barcode)..." value="${escapeHtml(name)}" autocomplete="off">
                        <i class="fas fa-search ss-prod-search-icon"></i>
                    </div>
                </td>
                <td class="excel-cell text-center ${stockColorClass} row-stock-badge">${stockDisplay}</td>
                <td class="excel-cell">
                    <input type="number" step="any" min="0.01" name="qty[]" class="ss-excel-input text-center row-qty" value="${qty}">
                </td>
                <td class="excel-cell text-center text-muted small fw-semibold row-size-col">${size}</td>
                <td class="excel-cell">
                    <input type="number" step="any" min="0" name="price[]" class="ss-excel-input text-end row-price" value="${price.toFixed(2)}" data-orig-price="${origPrice}">
                </td>
                <td class="excel-cell">
                    <input type="number" step="any" min="0" name="trade_offer[]" class="ss-excel-input text-center row-trade-offer" value="${tradeOffer > 0 ? tradeOffer : ''}" placeholder="0" title="Trade Offer (Free Qty)">
                </td>
                <td class="excel-cell excel-disc-cell">
                    <div class="ss-disc-wrap">
                        <input type="number" step="any" min="0" class="ss-excel-input text-end row-disc-val" value="${discVal}">
                        <button type="button" class="ss-disc-toggle-btn ${discType === 'percent' ? 'btn-pct' : 'btn-pkr'}" data-type="${discType}" title="Click to toggle % / PKR">${discType === 'percent' ? '%' : 'PKR'}</button>
                        <input type="hidden" name="disc_type[]" class="row-disc-type" value="${discType}">
                        <input type="hidden" name="item_disc[]" class="row-disc-amount" value="${calcDisc.toFixed(2)}">
                    </div>
                </td>
                <td class="excel-cell">
                    <input type="number" step="any" readonly name="total[]" class="ss-excel-input text-end fw-bold row-amount" value="${amount.toFixed(2)}" style="background: #f8fafc; color: #0f172a;">
                </td>
                <td class="excel-cell text-center">
                    <button type="button" class="ss-btn-del-row" title="Delete / Clear Row">✕</button>
                </td>
            </tr>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // --- RE-INDEX ROWS ---
    function reindexRows() {
        let count = 0;
        let filledCount = 0;
        $('#saleGridBody tr.ss-grid-row').each(function(i) {
            count++;
            $(this).find('.row-idx').text(count);
            if ($(this).find('.row-product-id').val()) {
                filledCount++;
            }
        });
        if ($('#orderItemsCountTitle').length) {
            $('#orderItemsCountTitle').text(`ORDER ITEMS (${filledCount})`);
        }
    }

    // --- ENSURE MINIMUM BLANK ROWS ---
    function ensureMinimumBlankRows() {
        let totalCount = 0;
        let blankCount = 0;
        $('#saleGridBody tr.ss-grid-row').each(function() {
            totalCount++;
            if (!$(this).find('.row-product-id').val()) {
                blankCount++;
            }
        });

        if (totalCount < MIN_BLANK_ROWS) {
            const need = MIN_BLANK_ROWS - totalCount;
            for (let i = 0; i < need; i++) {
                $('#saleGridBody').append(createRowHtml(null));
            }
        } else if (blankCount < 2) {
            // Always keep at least 2 blank rows ready at the bottom so user never needs to manually scroll
            const need = 2 - blankCount;
            for (let i = 0; i < need; i++) {
                $('#saleGridBody').append(createRowHtml(null));
            }
        }

        reindexRows();
    }

    // --- CLEAR A ROW TO BLANK STATE ---
    function clearRow($row) {
        $row.find('.row-product-id').val('');
        $row.find('.row-variant-id').val('');
        $row.find('.row-product-name').val('');
        $row.find('.row-item-code').val('');
        $row.find('.row-uom').val('');
        $row.find('.row-unit').val('piece');
        $row.find('.row-color').val('-');

        $row.find('.ss-prod-search-input').val('');
        $row.find('.row-stock-badge').text('-').removeClass('text-success text-danger').addClass('text-muted');
        $row.find('.row-qty').val('1');
        $row.find('.row-size-col').text('-');
        $row.find('.row-price').val('0.00').data('orig-price', 0);
        $row.find('.row-trade-offer').val('');
        $row.find('.row-disc-val').val('0');
        $row.find('.row-disc-type').val('percent');
        $row.find('.row-disc-amount').val('0.00');
        $row.find('.row-amount').val('0.00');

        const $btn = $row.find('.ss-disc-toggle-btn');
        $btn.text('%').removeClass('btn-pkr').addClass('btn-pct').attr('title', 'Switch to PKR');

        recalculateRow($row);
        reindexRows();
    }

    // --- FILL AN EXISTING ROW WITH AN ITEM ---
    function fillRowWithItem($row, item) {
        const id         = item ? (item.product_id || item.id || '') : '';
        const variantId  = item ? (item.variant_id || '') : '';
        const name       = item ? (item.item_name || item.base_name || item.name || '') : '';
        const code       = item ? (item.code || item.item_code || item.barcode || '') : '';
        const stock      = item ? (parseFloat(item.stock !== undefined ? item.stock : 0)) : 0;
        const qty        = item ? (parseFloat(item.qty || 1)) : 1;
        const size       = item ? (item.size || item.size_label || item.variant_name || '-') : '-';
        const color      = item ? (item.color || '-') : '-';
        const pcs        = item ? (item.pcs || item.unit_type || 'piece') : 'piece';
        const price      = item ? (parseFloat(item.price || 0)) : 0;
        const origPrice  = item ? (parseFloat(item.original_price || item.price || 0)) : price;
        const tradeOffer = item ? (parseFloat(item.trade_offer || 0)) : 0;
        const discVal    = item ? (parseFloat(item.disc_val || item.disc || 0)) : 0;
        const discType   = item ? (item.disc_type || 'percent') : 'percent';

        $row.find('.row-product-id').val(id);
        $row.find('.row-variant-id').val(variantId);
        $row.find('.row-product-name').val(name);
        $row.find('.row-item-code').val(code);
        $row.find('.row-unit').val(pcs);
        $row.find('.row-color').val(color);

        $row.find('.ss-prod-search-input').val(name);
        $row.find('.row-qty').val(qty);
        $row.find('.row-size-col').text(size);

        const stockBadge = $row.find('.row-stock-badge');
        stockBadge.text(stock).removeClass('text-success text-danger text-muted').addClass(stock > 0 ? 'text-success' : 'text-danger');

        $row.find('.row-price').val(price.toFixed(2)).data('orig-price', origPrice);
        $row.find('.row-trade-offer').val(tradeOffer > 0 ? tradeOffer : '');
        $row.find('.row-disc-val').val(discVal);
        $row.find('.row-disc-type').val(discType);

        const $btn = $row.find('.ss-disc-toggle-btn');
        if (discType === 'percent') {
            $btn.text('%').removeClass('btn-pkr').addClass('btn-pct').attr('title', 'Switch to PKR');
        } else {
            $btn.text('PKR').removeClass('btn-pct').addClass('btn-pkr').attr('title', 'Switch to %');
        }

        recalculateRow($row);
        reindexRows();
    }

    // --- ADD ROW BUTTON ---
    $('#btnAddGridRow').on('click', function() {
        const html = createRowHtml(null);
        $('#saleGridBody').append(html);
        reindexRows();
        const $newRow = $('#saleGridBody tr.ss-grid-row:last-child');
        scrollRowIntoTableWrap($newRow);
        setTimeout(() => {
            $newRow.find('.ss-prod-search-input').focus();
        }, 30);
    });

    // --- REMOVE / CLEAR ROW ---
    $(document).on('click', '.ss-btn-del-row', function() {
        const $row = $(this).closest('tr.ss-grid-row');
        const totalRows = $('#saleGridBody tr.ss-grid-row').length;

        if (totalRows <= MIN_BLANK_ROWS) {
            clearRow($row);
            // Move cleared row to the end if there are filled rows after it
            const $filledAfter = $row.nextAll('tr.ss-grid-row').filter(function() {
                return !!$(this).find('.row-product-id').val();
            });
            if ($filledAfter.length) {
                $row.appendTo('#saleGridBody');
            }
        } else {
            $row.remove();
            ensureMinimumBlankRows();
        }

        reindexRows();
        recalculateTotals();
        closeFloatingDropdown();
    });

    // --- DISCOUNT % / PKR TOGGLE BUTTON ---
    $(document).on('click', '.ss-disc-toggle-btn', function() {
        const $btn = $(this);
        const $row = $btn.closest('tr.ss-grid-row');
        const $typeInput = $row.find('.row-disc-type');

        if ($typeInput.val() === 'percent') {
            $typeInput.val('pkr');
            $btn.text('PKR').removeClass('btn-pct').addClass('btn-pkr').attr('title', 'Switch to %');
        } else {
            $typeInput.val('percent');
            $btn.text('%').removeClass('btn-pkr').addClass('btn-pct').attr('title', 'Switch to PKR');
        }

        recalculateRow($row);
    });

    // --- ROW INPUT CHANGES ---
    $(document).on('input change', '.row-qty, .row-price, .row-trade-offer, .row-disc-val', function() {
        const $row = $(this).closest('tr.ss-grid-row');
        recalculateRow($row);
    });

    function recalculateRow($row) {
        const qty      = parseFloat($row.find('.row-qty').val()) || 0;
        const price    = parseFloat($row.find('.row-price').val()) || 0;
        const discVal  = parseFloat($row.find('.row-disc-val').val()) || 0;
        const discType = $row.find('.row-disc-type').val() || 'percent';

        let calculatedDisc = 0;
        if (discType === 'percent') {
            calculatedDisc = (qty * price) * (discVal / 100);
        } else {
            calculatedDisc = discVal;
        }

        calculatedDisc = Math.min(qty * price, Math.max(0, calculatedDisc));
        const lineAmount = Math.max(0, (qty * price) - calculatedDisc);

        $row.find('.row-disc-amount').val(calculatedDisc.toFixed(2));
        $row.find('.row-amount').val(lineAmount.toFixed(2));

        recalculateTotals();
    }

    // --- RECALCULATE TOTALS & CHANGE ---
    function recalculateTotals() {
        let totalAmount = 0;
        let lineDiscount = 0;
        let netTotal = 0;
        let totalQty = 0;
        let itemCount = 0;

        $('#saleGridBody tr.ss-grid-row').each(function() {
            const pid = $(this).find('.row-product-id').val();
            const qty = parseFloat($(this).find('.row-qty').val()) || 0;
            const tradeOffer = parseFloat($(this).find('.row-trade-offer').val()) || 0;
            const price = parseFloat($(this).find('.row-price').val()) || 0;
            const discAmount = parseFloat($(this).find('.row-disc-amount').val()) || 0;
            const amount = parseFloat($(this).find('.row-amount').val()) || 0;

            // ONLY accumulate for rows where a product is actually selected!
            if (pid && qty > 0) {
                itemCount++;
                totalQty += qty;
                totalAmount += (qty * price);
                lineDiscount += discAmount;
                netTotal += amount;
            }
        });

        // Update Grid Footer
        $('#gridTotalAmountDisplay').text('Rs. ' + netTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#gridItemsCountSummary').text(`${itemCount} Item(s) | ${totalQty} Total Qty`);

        // Update Summary Card
        $('#summaryTotalAmount').text(totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryLineDiscount').text(lineDiscount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryNetTotal').text(netTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Form Hiddens
        $('#inputTotalSubtotal').val(totalAmount.toFixed(2));
        $('#inputTotalExtraCost').val(lineDiscount.toFixed(2));
        $('#inputTotalNet').val(netTotal.toFixed(2));
        $('#inputTotalItems').val(itemCount);
        $('#inputTotalPieces').val(totalQty);

        // Auto-update first payment input if untouched
        const $firstPmInput = $('#paymentMethodsContainer .pm-amount-input').first();
        if ($('#paymentMethodsContainer .ss-pm-row').length === 1 && ($firstPmInput.data('user-edited') !== true)) {
            $firstPmInput.val(netTotal.toFixed(2));
        }

        recalculatePayments(netTotal);
    }

    // --- RECALCULATE PAYMENTS & CHANGE ---
    function recalculatePayments(netTotalOverride) {
        const netTotal = (netTotalOverride !== undefined) ? netTotalOverride : (parseFloat($('#inputTotalNet').val()) || 0);

        let totalPaid = 0;
        let cashPaid = 0;
        let cardPaid = 0;
        let cardAccId = '';

        $('#paymentMethodsContainer .ss-pm-row').each(function() {
            const accType = $(this).find('.pm-account-select').val();
            const amt = parseFloat($(this).find('.pm-amount-input').val()) || 0;
            totalPaid += amt;

            if (accType === 'cash') {
                cashPaid += amt;
            } else {
                cardPaid += amt;
                cardAccId = accType;
            }
        });

        $('#inputCash').val(cashPaid.toFixed(2));
        $('#inputCard').val(cardPaid.toFixed(2));
        $('#inputCardAccountId').val(cardAccId);

        $('#summaryTotalPaid').text(totalPaid.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        const change = totalPaid - netTotal;
        $('#inputChange').val(change.toFixed(2));

        const $changeBox = $('#summaryChangeBox');
        if (change >= 0 && netTotal > 0) {
            $changeBox.addClass('paid');
            $('#summaryChangeAmount').text('+' + change.toFixed(2));
        } else {
            $changeBox.removeClass('paid');
            $('#summaryChangeAmount').text(change.toFixed(2));
        }
    }

    $(document).on('input change', '.pm-amount-input, .pm-account-select', function() {
        if ($(this).hasClass('pm-amount-input')) {
            $(this).data('user-edited', true);
        }
        recalculatePayments();
    });

    // --- PAYMENT METHOD ROWS SPLIT ---
    $('#btnAddAccountRow').on('click', function() {
        const rowCount = $('#paymentMethodsContainer .ss-pm-row').length;
        const netTotal = parseFloat($('#inputTotalNet').val()) || 0;
        let currentPaid = 0;
        $('#paymentMethodsContainer .pm-amount-input').each(function() {
            currentPaid += (parseFloat($(this).val()) || 0);
        });
        const remaining = Math.max(0, netTotal - currentPaid);

        let bankOptions = '';
        @foreach($bankAccounts as $acc)
            bankOptions += `<option value="{{ $acc->id }}">{{ $acc->title }}</option>`;
        @endforeach

        const html = `
            <div class="ss-pm-row" data-index="${rowCount}">
                <select class="ss-pm-select pm-account-select" name="payment_account[]">
                    ${bankOptions || '<option value="cash">Cash in hand</option>'}
                </select>
                <input type="number" step="any" min="0" class="ss-pm-input pm-amount-input" name="payment_amount[]" placeholder="0.00" value="${remaining.toFixed(2)}">
                <button type="button" class="ss-pm-remove" title="Remove Account">✕</button>
            </div>
        `;
        $('#paymentMethodsContainer').append(html);
        recalculatePayments();
    });

    $(document).on('click', '.ss-pm-remove', function() {
        $(this).closest('.ss-pm-row').remove();
        recalculatePayments();
    });

    // --- BARCODE SCANNER LOGIC ---
    const $barcodeInput = $('#barcodeScanner');
    const $clearBarcodeBtn = $('#btnClearBarcode');

    $barcodeInput.on('input', function() {
        if ($(this).val().length > 0) {
            $clearBarcodeBtn.show();
        } else {
            $clearBarcodeBtn.hide();
        }
    });

    $clearBarcodeBtn.on('click', function() {
        $barcodeInput.val('').focus();
        $clearBarcodeBtn.hide();
    });

    $barcodeInput.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = $(this).val().trim();
            if (!barcode) return;

            processBarcodeScan(barcode);
        }
    });

    function processBarcodeScan(barcode) {
        const cleanBar = String(barcode).trim();
        if (!cleanBar) return;

        // ⚡ INSTANT 0ms FAST-PATH: Match barcode in local in-memory catalog first!
        if (window.POS_CATALOG && window.POS_CATALOG.length) {
            const localMatch = window.POS_CATALOG.find(p => 
                String(p.barcode || '').trim() === cleanBar || 
                String(p.item_code || '').trim() === cleanBar
            );

            if (localMatch) {
                $barcodeInput.val('').focus();
                $clearBarcodeBtn.hide();
                playBeep(920, 0.14);
                addOrIncrementItem(localMatch);
                return;
            }
        }

        // FALLBACK: Query server only if not in local catalog
        $barcodeInput.prop('disabled', true);
        $.ajax({
            url: "{{ route('pos.scan.barcode') }}",
            type: 'GET',
            data: { barcode: cleanBar },
            success: function(resp) {
                $barcodeInput.prop('disabled', false).val('').focus();
                $clearBarcodeBtn.hide();

                if (resp.found && resp.item) {
                    playBeep(920, 0.14);
                    window.POS_CATALOG.unshift(resp.item);
                    addOrIncrementItem(resp.item);
                } else {
                    playBeep(350, 0.25);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: resp.message || 'Product not found!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function() {
                $barcodeInput.prop('disabled', false).val('').focus();
                $clearBarcodeBtn.hide();
                playBeep(300, 0.3);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Barcode not found',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    // --- ADD OR INCREMENT ITEM IN GRID & JUMP TO QTY ---
    function addOrIncrementItem(item) {
        const prodId = String(item.product_id || item.id);
        const varId  = String(item.variant_id || '');

        let existingRow = null;
        $('#saleGridBody tr.ss-grid-row').each(function() {
            const rowPid = String($(this).find('.row-product-id').val());
            const rowVid = String($(this).find('.row-variant-id').val());
            if (rowPid && rowPid === prodId && rowVid === varId) {
                existingRow = $(this);
                return false;
            }
        });

        let $targetRow = null;

        if (existingRow) {
            const $qtyInput = existingRow.find('.row-qty');
            const currentQty = parseFloat($qtyInput.val()) || 0;
            $qtyInput.val(currentQty + 1);
            recalculateRow(existingRow);
            existingRow.addClass('table-success');
            setTimeout(() => existingRow.removeClass('table-success'), 600);
            $targetRow = existingRow;
        } else {
            // Find first available blank row
            $('#saleGridBody tr.ss-grid-row').each(function() {
                if (!$(this).find('.row-product-id').val()) {
                    $targetRow = $(this);
                    return false;
                }
            });

            if ($targetRow && $targetRow.length) {
                fillRowWithItem($targetRow, item);
                $targetRow.addClass('table-success');
                setTimeout(() => $targetRow.removeClass('table-success'), 600);
            } else {
                const html = createRowHtml(item);
                $('#saleGridBody').append(html);
                reindexRows();
                recalculateTotals();

                $targetRow = $('#saleGridBody tr.ss-grid-row:last-child');
                $targetRow.addClass('table-success');
                setTimeout(() => $targetRow.removeClass('table-success'), 600);
            }

            ensureMinimumBlankRows();
        }

        // Immediately focus QTY input with value selected as requested!
        if ($targetRow && $targetRow.length) {
            scrollRowIntoTableWrap($targetRow);
            setTimeout(() => {
                $targetRow.find('.row-qty').focus().select();
            }, 30);
        }
    }

    // --- QUICK PRODUCTS PANEL TOGGLE & TABS ---
    $('#btnToggleQuickPanel').on('click', function() {
        $(this).toggleClass('active');
        $('#quickProductsPanel').slideToggle(180);
    });

    $('#qpCategoryTabs .ss-cat-tab').on('click', function() {
        $('#qpCategoryTabs .ss-cat-tab').removeClass('active');
        $(this).addClass('active');

        const cat = $(this).data('cat');
        filterQuickItems(cat, $('#qpSearchFilter').val().trim().toLowerCase());
    });

    $('#qpSearchFilter').on('input', function() {
        const query = $(this).val().trim().toLowerCase();
        const cat = $('#qpCategoryTabs .ss-cat-tab.active').data('cat');
        filterQuickItems(cat, query);
    });

    function filterQuickItems(cat, query) {
        $('#qpGridContainer .ss-qp-card').each(function() {
            const itemCat = String($(this).data('cat'));
            const itemName = String($(this).data('name'));

            const catMatch = (cat === 'all' || itemCat === String(cat));
            const queryMatch = (!query || itemName.includes(query));

            if (catMatch && queryMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // CLICK QUICK PRODUCT CARD
    $(document).on('click', '.ss-qp-card', function() {
        const card = $(this);
        const variants = card.data('variants');

        let variantId = '';
        let size = '-';
        if (variants && variants.length > 0) {
            const defV = variants.find(v => v.is_default) || variants[0];
            variantId = defV.id;
            size = defV.size_label || defV.variant_name || '-';
        }

        const item = {
            id: card.data('product-id'),
            product_id: card.data('product-id'),
            variant_id: variantId,
            item_name: card.data('item-name'),
            code: card.data('item-code'),
            price: parseFloat(card.data('price')) || 0,
            original_price: parseFloat(card.data('price')) || 0,
            stock: parseFloat(card.data('stock')) || 0,
            unit_type: card.data('unit'),
            size: size,
            color: '-',
            qty: 1,
            disc_val: 0,
            disc_type: 'percent'
        };

        playBeep(980, 0.1);
        addOrIncrementItem(item);
    });

    // ==========================================================================
    // FLOATING PRODUCT SEARCH DROPDOWN (FRONT & UNCLIPPED)
    // ==========================================================================
    // --- SCROLL ROW INTO TABLE WRAP (ZERO WINDOW SCROLL - KEEPS HEADINGS STATIC) ---
    function scrollRowIntoTableWrap($row) {
        const wrap = document.querySelector('.ss-table-responsive-wrap');
        if (!wrap || !$row || !$row.length) return;

        setTimeout(function() {
            const rowEl = $row[0];
            const targetTop = rowEl.offsetTop;
            const targetBottom = targetTop + rowEl.offsetHeight;
            const headerHeight = 36;
            const currentScroll = wrap.scrollTop;
            const wrapHeight = wrap.clientHeight;

            // If it's the last row or 2nd-last row, scroll completely to the bottom!
            if ($row.is(':last-child') || $row.is(':nth-last-child(2)')) {
                wrap.scrollTop = wrap.scrollHeight;
                return;
            }

            // If targetBottom exceeds visible view
            if (targetBottom > currentScroll + wrapHeight) {
                wrap.scrollTop = targetBottom - wrapHeight + 15;
            } else if (targetTop < currentScroll + headerHeight) {
                wrap.scrollTop = Math.max(0, targetTop - headerHeight - 6);
            }
        }, 20);
    }

    function positionFloatingDropdown(inputEl) {
        if (!inputEl) return;
        const rect = inputEl.getBoundingClientRect();
        const dropWidth = Math.max(430, rect.width + 120);
        let leftPos = rect.left;

        if (leftPos + dropWidth > window.innerWidth - 10) {
            leftPos = Math.max(10, window.innerWidth - dropWidth - 15);
        }

        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;
        const currentHeight = $floatingDropdown.outerHeight() || 260;

        // SMART FLIP: If space below is limited (< 220px) and space above is bigger -> FLIP UPWARDS!
        if (spaceBelow < 220 && spaceAbove > spaceBelow) {
            const maxHeight = Math.min(320, Math.max(160, spaceAbove - 20));
            const topPos = Math.max(10, rect.top - Math.min(currentHeight, maxHeight) - 4);
            $floatingDropdown.css({
                top: topPos + 'px',
                bottom: 'auto',
                left: leftPos + 'px',
                width: dropWidth + 'px',
                maxHeight: maxHeight + 'px',
                display: 'block'
            });
        } else {
            // Normal: open DOWNWARDS
            const maxHeight = Math.min(320, Math.max(160, spaceBelow - 20));
            $floatingDropdown.css({
                top: (rect.bottom + 2) + 'px',
                bottom: 'auto',
                left: leftPos + 'px',
                width: dropWidth + 'px',
                maxHeight: maxHeight + 'px',
                display: 'block'
            });
        }
    }

    function closeFloatingDropdown() {
        $floatingDropdown.hide();
        activeSearchInput = null;
        activeRow = null;
    }

    function renderFloatingResults(items) {
        if (!items || !items.length) {
            $floatingContent.html('<div class="p-3 text-center text-muted small"><i class="fas fa-search me-1"></i> No matching products found</div>');
            return;
        }

        let html = '';
        items.forEach((p, idx) => {
            const price = parseFloat(p.price || 0);
            const stock = parseFloat(p.stock !== undefined ? p.stock : 0);
            const code = p.item_code || p.barcode || '';
            const size = p.size && p.size !== '-' ? `(${p.size})` : '';
            const stockClass = stock > 0 ? 'in-stock' : 'out-stock';
            const stockText = stock > 0 ? `${stock} in stock` : 'Out of stock';

            html += `
                <div class="ss-prod-item ${idx === 0 ? 'active' : ''}" 
                     data-json='${escapeHtml(JSON.stringify(p))}'>
                    <div style="flex: 1; min-width: 0;">
                        <div class="ss-prod-item-title">${escapeHtml(p.item_name || p.base_name)}</div>
                        <div class="ss-prod-item-sub">
                            ${code ? `<span><i class="fas fa-barcode me-1"></i>${escapeHtml(code)}</span>` : ''}
                            ${size ? `<span class="badge bg-light text-dark border">${escapeHtml(size)}</span>` : ''}
                            <span class="ss-prod-item-stock-badge ${stockClass}">${stockText}</span>
                        </div>
                    </div>
                    <div class="ss-prod-item-price">Rs. ${price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                </div>
            `;
        });

        $floatingContent.html(html);

        // Re-adjust dropdown position immediately after rendering items so upward flip is perfectly aligned to the top edge!
        if (activeSearchInput && activeSearchInput.length) {
            positionFloatingDropdown(activeSearchInput[0]);
        }
    }

    let searchTimer = null;
    function fetchAndShowProducts(query, inputEl) {
        activeSearchInput = $(inputEl);
        activeRow = activeSearchInput.closest('tr.ss-grid-row');

        scrollRowIntoTableWrap(activeRow);
        positionFloatingDropdown(inputEl);

        const cleanQ = (query || '').trim();

        // ⚡ INSTANT 0-MILLISECOND LOCAL FILTER & RENDER (ZERO SPINNER, ZERO LAG)
        const localResults = searchLocalCatalog(cleanQ);
        renderFloatingResults(localResults);

        // Background server search only if user typed text and local results are sparse
        if (cleanQ.length >= 2 && localResults.length === 0) {
            $floatingContent.html('<div class="p-2 text-center text-muted small"><i class="fas fa-spinner fa-spin me-1"></i> Searching server...</div>');
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('search-product-name') }}",
                    type: 'GET',
                    data: { q: cleanQ },
                    success: function(resp) {
                        const serverItems = Array.isArray(resp) ? resp : (resp.data || []);
                        if (serverItems.length) {
                            serverItems.forEach(si => {
                                const exists = window.POS_CATALOG.some(ci => 
                                    ci.id == si.id && ci.variant_id == si.variant_id
                                );
                                if (!exists) {
                                    window.POS_CATALOG.push(si);
                                }
                            });
                            renderFloatingResults(searchLocalCatalog(cleanQ));
                        } else {
                            $floatingContent.html('<div class="p-3 text-center text-muted small"><i class="fas fa-search me-1"></i> No matching products found</div>');
                        }
                    },
                    error: function() {
                        $floatingContent.html('<div class="p-3 text-center text-muted small"><i class="fas fa-search me-1"></i> No matching products found</div>');
                    }
                });
            }, 200);
        }
    }

    // ON FOCUS OR CLICK IN PRODUCT INPUT -> SHOW LIST IMMEDIATELY
    $(document).on('focus click', '.ss-prod-search-input', function() {
        const query = $(this).val().trim();
        fetchAndShowProducts(query, this);
    });

    // ON TYPING IN PRODUCT INPUT
    $(document).on('input', '.ss-prod-search-input', function() {
        const query = $(this).val().trim();
        fetchAndShowProducts(query, this);
    });

    // ON BLUR PRODUCT INPUT: IF EMPTIED, CLEAR PRODUCT ROW
    $(document).on('blur', '.ss-prod-search-input', function() {
        const $row = $(this).closest('tr.ss-grid-row');
        if (!$(this).val().trim() && $row.find('.row-product-id').val()) {
            clearRow($row);
        }
    });

    // KEYBOARD NAVIGATION IN FLOATING DROPDOWN
    $(document).on('keydown', '.ss-prod-search-input', function(e) {
        if (!$floatingDropdown.is(':visible')) {
            // If dropdown is closed and user hits Enter, jump to QTY input
            if (e.key === 'Enter') {
                e.preventDefault();
                const $row = $(this).closest('tr.ss-grid-row');
                $row.find('.row-qty').focus().select();
            }
            return;
        }

        const $items = $floatingContent.find('.ss-prod-item');
        let $active = $items.filter('.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!$active.length || $active.is(':last-child')) {
                $items.removeClass('active').first().addClass('active');
            } else {
                $active.removeClass('active').next().addClass('active');
            }
            scrollToActiveDropdownItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (!$active.length || $active.is(':first-child')) {
                $items.removeClass('active').last().addClass('active');
            } else {
                $active.removeClass('active').prev().addClass('active');
            }
            scrollToActiveDropdownItem();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if ($active.length) {
                $active.trigger('click');
            }
        } else if (e.key === 'Escape') {
            closeFloatingDropdown();
        }
    });

    function scrollToActiveDropdownItem() {
        const $active = $floatingContent.find('.ss-prod-item.active');
        if ($active.length) {
            const container = $floatingDropdown[0];
            const item = $active[0];
            const offsetTop = item.offsetTop;
            if (offsetTop < container.scrollTop || (offsetTop + item.offsetHeight) > (container.scrollTop + container.clientHeight)) {
                container.scrollTop = offsetTop - 40;
            }
        }
    }

    // CLICK ITEM IN FLOATING DROPDOWN -> JUMP TO QTY DIRECTLY!
    $(document).on('click', '.ss-prod-item', function(e) {
        e.stopPropagation();
        if (!activeRow || !activeRow.length) return;

        const p = $(this).data('json');
        if (!p) return;

        fillRowWithItem(activeRow, {
            id: p.id,
            product_id: p.id,
            variant_id: p.variant_id || '',
            item_name: p.item_name || p.base_name,
            code: p.item_code || p.barcode || '',
            price: parseFloat(p.price || 0),
            original_price: parseFloat(p.price || 0),
            stock: parseFloat(p.stock !== undefined ? p.stock : 0),
            unit_type: p.unit_type || 'piece',
            size: p.size || '-',
            color: p.color || '-',
            qty: 1,
            disc_val: 0,
            disc_type: 'percent'
        });

        closeFloatingDropdown();
        playBeep(980, 0.1);

        ensureMinimumBlankRows();

        // Cursor IMMEDIATELY moves to QTY input and selects text as requested!
        activeRow.find('.row-qty').focus().select();
    });

    // CLOSE DROPDOWN WHEN CLICKING OUTSIDE
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#floatingProductDropdown, .ss-prod-search-wrap').length) {
            closeFloatingDropdown();
        }
    });

    // REPOSITION FLOATING DROPDOWN ON WINDOW RESIZE OR SCROLL (INCLUDING INNER TABLE WRAP SCROLL)
    $(window).on('resize scroll', function() {
        if ($floatingDropdown.is(':visible') && activeSearchInput && activeSearchInput.length) {
            positionFloatingDropdown(activeSearchInput[0]);
        }
    });
    $('.ss-table-responsive-wrap').on('scroll', function() {
        if ($floatingDropdown.is(':visible') && activeSearchInput && activeSearchInput.length) {
            positionFloatingDropdown(activeSearchInput[0]);
        }
    });

    // --- CUSTOMER / BOOKER / DISTRIBUTOR SELECT2 & BADGES ---
    function updateCustomerBadge() {
        const $selected = $('#customerSelect').find(':selected');
        const type = $selected.data('type') || 'walkin';
        const val = $('#customerSelect').val();
        const $badge = $('#customerTypeBadge');
        const balance = parseFloat($selected.data('balance') || 0);

        if (!val || val === 'Walk-in Customer' || type === 'walkin') {
            $badge.attr('class', 'badge bg-light text-secondary border').html('<i class="fas fa-user me-1"></i> Walk-in');
        } else if (type === 'order_booker') {
            $badge.attr('class', 'badge bg-warning text-dark border-0 fw-bold').html('<i class="fas fa-motorcycle me-1"></i> Order Booker' + (balance > 0 ? ` (Due: Rs. ${balance.toLocaleString()})` : ''));
        } else if (type === 'distributor') {
            $badge.attr('class', 'badge bg-primary text-white border-0 fw-bold').html('<i class="fas fa-truck me-1"></i> Distributor' + (balance > 0 ? ` (Due: Rs. ${balance.toLocaleString()})` : ''));
        } else if (type === 'distributor_customer') {
            $badge.attr('class', 'badge bg-info text-dark border-0 fw-bold').html('<i class="fas fa-store me-1"></i> Retail Shop' + (balance > 0 ? ` (Due: Rs. ${balance.toLocaleString()})` : ''));
        } else if (type === 'customer') {
            const creditAllowed = $selected.data('credit') == '1';
            if (creditAllowed) {
                $badge.attr('class', 'badge bg-success text-white border-0 fw-bold').html('<i class="fas fa-check-circle me-1"></i> Credit Customer');
            } else {
                $badge.attr('class', 'badge bg-secondary text-white border-0').html('<i class="fas fa-user-tag me-1"></i> Regular Customer');
            }
        }
    }

    if ($.fn.select2) {
        $('#customerSelect').select2({
            placeholder: "Search Customer / Booker / Distributor...",
            width: '100%',
            dropdownAutoWidth: true
        });

        // When selection made, update badge & move focus to barcode scanner
        $('#customerSelect').on('select2:select', function() {
            updateCustomerBadge();
            setTimeout(function() {
                $('#barcodeScanner').focus();
            }, 60);
        });
    }

    $('#customerSelect').on('change', updateCustomerBadge);
    updateCustomerBadge();

    // Hotkey F1 to open customer selection
    $(document).on('keydown', function(e) {
        if (e.key === 'F1') {
            e.preventDefault();
            if ($.fn.select2) {
                $('#customerSelect').select2('open');
            } else {
                $('#customerSelect').focus();
            }
        } else if (e.altKey && (e.key === 'w' || e.key === 'W')) {
            e.preventDefault();
            $('#btnSetWalkin').trigger('click');
        }
    });

    // --- QUICK CUSTOMER MODAL AJAX SUBMIT ---
    $('#quickCustomerForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const data = $(this).serialize();

        $.ajax({
            url: "{{ route('customers.store') }}",
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}' },
            beforeSend: function() { $btn.prop('disabled', true).text('Saving...'); },
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Customer');
                if (resp.success && resp.customer) {
                    const cust = resp.customer;
                    const opt = new Option(`👤 ${cust.customer_name} (${cust.mobile || 'No Ph'})`, cust.id, true, true);
                    $(opt).attr('data-type', 'customer');
                    $('#customerSelect').append(opt).trigger('change');
                    if ($.fn.select2) {
                        $('#customerSelect').trigger('change.select2');
                    }

                    $('#newCustomerModal').modal('hide');
                    $('#quickCustomerForm')[0].reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Customer created and selected!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Customer');
                const err = xhr.responseJSON?.message || 'Error creating customer';
                Swal.fire({ icon: 'error', title: 'Customer Error', text: err });
            }
        });
    });

    // --- RESET WALK-IN CUSTOMER BUTTON ---
    $('#btnSetWalkin').on('click', function() {
        $('#customerSelect').val('Walk-in Customer').trigger('change');
        if ($.fn.select2) {
            $('#customerSelect').trigger('change.select2');
        }
    });

    // --- QUICK PRODUCT MODAL AJAX SUBMIT ---
    $('#quickProductForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const data = $(this).serialize();

        $.ajax({
            url: "{{ route('sale.quick_product') }}",
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}' },
            beforeSend: function() { $btn.prop('disabled', true).text('Creating...'); },
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Add Product & Insert');
                if (resp.success && resp.product) {
                    $('#quickProductModal').modal('hide');
                    $('#quickProductForm')[0].reset();

                    playBeep(980, 0.15);
                    const np = resp.product;
                    const catItem = {
                        id: np.id,
                        product_id: np.id,
                        variant_id: null,
                        item_name: np.item_name,
                        base_name: np.item_name,
                        size: '-',
                        color: np.color || '-',
                        unit_type: np.unit_type || 'piece',
                        item_code: np.item_code,
                        barcode: np.barcode_path || np.item_code,
                        brand: np.brand?.name || '',
                        price: parseFloat(np.price) || 0,
                        original_price: parseFloat(np.price) || 0,
                        stock: parseFloat(np.stock) || 0,
                        category_id: np.category_id || null
                    };
                    if (window.POS_CATALOG) {
                        window.POS_CATALOG.unshift(catItem);
                    }
                    addOrIncrementItem(catItem);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Product added to sale!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Add Product & Insert');
                const err = xhr.responseJSON?.message || 'Failed to create product';
                Swal.fire({ icon: 'error', title: 'Product Creation Failed', text: err });
            }
        });
    });

    // --- EXCEL NAVIGATION & ENTER KEY AUTO-ROW SCROLL BEHAVIOR ---
    // User requested: "enter per he 2nd last row mai aik or row open ho jay or pehyl wali row upar scroll hoti rahyn mtlb humri screen es sy set rahy gi aik jagha"
    function advanceToNextRow($currentRow) {
        const $allRows = $('#saleGridBody tr.ss-grid-row');
        const currentIndex = $allRows.index($currentRow);
        const totalRows = $allRows.length;

        // When user reaches the 2nd-to-last row (or last row), automatically append another row!
        if (currentIndex >= totalRows - 2) {
            $('#saleGridBody').append(createRowHtml(null));
            reindexRows();
        }

        const $updatedRows = $('#saleGridBody tr.ss-grid-row');
        const $nextRow = $updatedRows.eq(currentIndex + 1);

        if ($nextRow.length) {
            scrollRowIntoTableWrap($nextRow);
            setTimeout(() => {
                $nextRow.find('.ss-prod-search-input').focus();
            }, 30);
        }
    }

    // --- ENTER KEY NAVIGATION: PRODUCT -> QTY -> PRICE -> DISCOUNT -> NEXT ROW ---
    $(document).on('keydown', '.row-qty', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            recalculateRow($row);
            $row.find('.row-price').focus().select();
        }
    });

    $(document).on('keydown', '.row-price', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            recalculateRow($row);
            $row.find('.row-trade-offer').focus().select();
        }
    });

    $(document).on('keydown', '.row-trade-offer', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            recalculateRow($row);
            $row.find('.row-disc-val').focus().select();
        }
    });

    $(document).on('keydown', '.row-disc-val', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $row = $(this).closest('tr.ss-grid-row');
            recalculateRow($row);
            advanceToNextRow($row);
        }
    });

    // --- FORM SUBMISSION (TOP SAVE & MAIN SAVE) ---
    $('#btnTopSave').on('click', function() {
        $('#saleScreenForm').submit();
    });

    $('#saleScreenForm').on('submit', function(e) {
        e.preventDefault();

        // 1. Validation: ensure at least one row has a product selected
        let validRows = 0;
        $('#saleGridBody tr.ss-grid-row').each(function() {
            const pid = $(this).find('.row-product-id').val();
            const qty = parseFloat($(this).find('.row-qty').val()) || 0;
            const tradeOffer = parseFloat($(this).find('.row-trade-offer').val()) || 0;
            const price = parseFloat($(this).find('.row-price').val()) || 0;
            if (pid && qty > 0 && price > 0) {
                validRows++;
            }
        });

        if (validRows === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Order Items',
                text: 'Please select at least one product with quantity and price.'
            });
            return;
        }

        // 2. CRITICAL: Disable all blank rows so that serialize() completely skips them!
        // Blank rows will NEVER be sent to the server or saved to the DB!
        const $blankRows = $('#saleGridBody tr.ss-grid-row').filter(function() {
            return !$(this).find('.row-product-id').val();
        });
        $blankRows.find('input, select').prop('disabled', true);

        const formData = $(this).serialize();

        // Immediately re-enable so table remains fully editable and intact
        $blankRows.find('input, select').prop('disabled', false);

        const $submitBtn = $('#btnSaveComplete');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving Sale...');
            },
            success: function(resp) {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i> Save & Complete (F9)');
                if (resp.success) {
                    playBeep(1200, 0.2);

                    const netTotal = $('#summaryNetTotal').text();
                    const change = $('#summaryChangeAmount').text();

                    $('#successInvoiceText').text(`Invoice #: ${resp.invoice_no || 'Saved'}`);
                    $('#successModalNetTotal').text(`Rs. ${netTotal}`);
                    $('#successModalChange').text(`Rs. ${change}`);

                    $('#btnReceiptPrint').attr('href', resp.receipt_url || '#');
                    $('#btnInvoicePrint').attr('href', resp.invoice_url || '#');

                    $('#saleSuccessModal').modal('show');
                } else {
                    Swal.fire({ icon: 'error', title: 'Sale Failed', text: resp.message || 'Error occurred.' });
                }
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i> Save & Complete (F9)');
                const err = xhr.responseJSON?.message || 'Error saving sale. Check quantities & inputs.';
                Swal.fire({ icon: 'error', title: 'Sale Error', text: err });
            }
        });
    });

    // START NEW SALE BUTTON
    $('#btnNewSaleReset').on('click', function() {
        $('#saleSuccessModal').modal('hide');
        resetSaleScreen();
    });

    function resetSaleScreen() {
        $('#saleGridBody').empty();
        $('#remarksInput').val('');
        $('#customerSelect').val('Walk-in Customer').trigger('change');
        $('#saleDateInput').val("{{ date('Y-m-d') }}");

        $('#paymentMethodsContainer').html(`
            <div class="ss-pm-row" data-index="0">
                <select class="ss-pm-select pm-account-select" name="payment_account[]">
                    <option value="cash" selected>Cash in hand</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                    @endforeach
                </select>
                <input type="number" step="any" min="0" class="ss-pm-input pm-amount-input" name="payment_amount[]" placeholder="0.00" value="0.00">
            </div>
        `);

        ensureMinimumBlankRows();
        recalculateTotals();
        $('#barcodeScanner').focus();
    }

    // --- KEYBOARD SHORTCUTS ---
    $(document).on('keydown', function(e) {
        if (e.key === 'F9') {
            e.preventDefault();
            $('#saleScreenForm').submit();
        } else if (e.key === 'F2') {
            e.preventDefault();
            $('#barcodeScanner').focus().select();
        } else if (e.key === 'F4') {
            e.preventDefault();
            $('#btnAddGridRow').trigger('click');
        } else if (e.key === 'Enter' && $('#saleSuccessModal').hasClass('show')) {
            e.preventDefault();
            $('#btnNewSaleReset').trigger('click');
        }
    });

    // --- INITIALIZE SCREEN WITH EXISTING ITEMS (IF EDIT MODE) OR BLANK ROWS ---
    window.EXISTING_ITEMS = @json($items ?? []);
    window.IS_EDIT_MODE = {{ isset($sale) ? 'true' : 'false' }};

    if (window.EXISTING_ITEMS && window.EXISTING_ITEMS.length > 0) {
        window.EXISTING_ITEMS.forEach(function(item) {
            const html = createRowHtml(item);
            $('#saleGridBody').append(html);
        });
        reindexRows();

        @if(isset($sale))
            $('#remarksInput').val(@json($sale->reference ?? ''));
            $('#extraDiscountInput').val({{ (float)($sale->total_extradiscount ?? 0) }});
            $('#paymentMethodsContainer .pm-amount-input').first().val({{ (float)($sale->cash ?? 0) }});
        @endif
    }

    ensureMinimumBlankRows();
    recalculateTotals();
});
</script>
@endsection
