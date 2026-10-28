@extends('admin_panel.layout.app')

@section('content')
@php
    $fmt = function($val) {
        $val = (float)$val;
        return ($val == (int)$val) ? number_format($val, 0) : number_format($val, 2);
    };
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --sl-bg: #f8fafc;
  --sl-surface: #ffffff;
  --sl-border: #e2e8f0;
  --sl-border-lt: #f1f5f9;
  --sl-text: #0f172a;
  --sl-text-sec: #475569;
  --sl-text-muted: #64748b;
  --sl-primary: #2563eb;
  --sl-primary-hover: #1d4ed8;
  --sl-radius: 12px;
  --sl-radius-sm: 8px;
  --sl-shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
  --sl-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
  --sl-shadow-lg: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
  --sl-font: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sl-page *:not(i):not([class*="bi"]):not([class*="fa"]):not([class*="ft"]) {
  font-family: var(--sl-font);
}

.sl-page {
  background-color: var(--sl-bg);
  min-height: calc(100vh - 60px);
  padding: 0.5rem 0.85rem 1.5rem;
  overflow-x: hidden !important;
}

/* ═══════ COMPACT ERP HERO HEADER ═══════ */
.sl-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--sl-radius);
  padding: 0.55rem 1rem;
  margin-bottom: 0.5rem;
  box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.2);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.65rem;
  overflow: hidden;
}

.sl-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.22) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.16) 0%, transparent 50%);
  pointer-events: none;
}

.sl-hero > * {
  position: relative;
  z-index: 1;
}

.sl-hero-title {
  display: flex;
  align-items: center;
  gap: 0.55rem;
}

.sl-hero-title h2 {
  font-size: 1.12rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.01em;
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.sl-hero-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  font-size: 1.05rem;
}

.sl-hero-badge {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 20px;
  padding: 0.15rem 0.55rem;
  font-size: 0.66rem;
  font-weight: 700;
  color: #93c5fd;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.sl-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.sl-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.38rem 0.75rem;
  border-radius: var(--sl-radius-sm);
  font-size: 0.78rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
  text-decoration: none !important;
  line-height: 1.2;
}

.sl-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}
.sl-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.sl-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.sl-btn-glass:hover, .sl-btn-glass.active {
  background: rgba(255, 255, 255, 0.24);
  color: #ffffff !important;
  border-color: rgba(255, 255, 255, 0.35);
}

.sl-btn-outline {
  background: #ffffff;
  color: #1e293b !important;
  border: 1.5px solid #cbd5e1;
}
.sl-btn-outline:hover {
  background: #f1f5f9;
  color: #0f172a !important;
  border-color: #94a3b8;
}

/* ═══════ SLEEK TOP KPI METRIC STRIP ═══════ */
.sl-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.55rem;
  margin-bottom: 0.55rem;
}

.sl-kpi-card {
  background: #ffffff;
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  padding: 0.55rem 0.85rem;
  box-shadow: var(--sl-shadow-sm);
  display: flex;
  align-items: center;
  gap: 0.65rem;
  position: relative;
  overflow: hidden;
  transition: transform 0.15s ease;
}
.sl-kpi-card:hover {
  transform: translateY(-1px);
}

.sl-kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 3.5px;
  height: 100%;
}
.sl-kpi-blue::before { background: #2563eb; }
.sl-kpi-emerald::before { background: #059669; }
.sl-kpi-amber::before { background: #d97706; }
.sl-kpi-purple::before { background: #7c3aed; }

.sl-kpi-icon {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.05rem;
  flex-shrink: 0;
}
.sl-kpi-blue .sl-kpi-icon { background: #eff6ff; color: #2563eb; }
.sl-kpi-emerald .sl-kpi-icon { background: #ecfdf5; color: #059669; }
.sl-kpi-amber .sl-kpi-icon { background: #fffbeb; color: #d97706; }
.sl-kpi-purple .sl-kpi-icon { background: #f5f3ff; color: #7c3aed; }

.sl-kpi-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
  line-height: 1.2;
}
.sl-kpi-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sl-text-muted);
}
.sl-kpi-value {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--sl-text);
  white-space: nowrap;
}
.sl-kpi-sub {
  font-size: 0.66rem;
  color: var(--sl-text-sec);
  font-weight: 600;
}

/* Cashier Drawer Bar */
.cashier-top-bar {
  display: flex;
  gap: 0.45rem;
  flex-wrap: wrap;
  margin-bottom: 0.55rem;
  background: #ffffff;
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  padding: 0.4rem 0.75rem;
}
.cashier-top-chip {
  font-size: 0.75rem;
  color: var(--sl-text-sec);
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.15rem 0.45rem;
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}
.cashier-top-chip strong {
  color: var(--sl-text);
  font-weight: 800;
}

/* ═══════ COLLAPSIBLE FILTER CARD ═══════ */
.sl-filter-card {
  background: var(--sl-surface);
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  padding: 0.75rem 1rem;
  margin-bottom: 0.55rem;
  box-shadow: var(--sl-shadow);
}

.sl-quick-filters {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  flex-wrap: wrap;
  margin-bottom: 0.55rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--sl-border-lt);
}

.sl-qf-label {
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--sl-text-muted);
  margin-right: 0.3rem;
}

.sl-qf-btn {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: var(--sl-text-sec);
  border-radius: 16px;
  padding: 0.2rem 0.65rem;
  font-size: 0.72rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}
.sl-qf-btn:hover, .sl-qf-btn.active {
  background: #2563eb;
  border-color: #2563eb;
  color: #ffffff;
}

.sl-input {
  border: 1.5px solid var(--sl-border);
  border-radius: var(--sl-radius-sm);
  padding: 0.38rem 0.7rem;
  font-size: 0.8rem;
  color: var(--sl-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.sl-input:focus {
  border-color: var(--sl-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.sl-label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sl-text-sec);
  margin-bottom: 0.2rem;
  display: block;
}

/* ═══════ MAIN DATA TABLE CARD (FULL RESPONSIVE) ═══════ */
.sl-table-card {
  background: var(--sl-surface);
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  box-shadow: var(--sl-shadow-lg);
  overflow: visible;
  width: 100% !important;
}

.sl-table-wrapper {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch;
  padding: 0.35rem 0.55rem 0.55rem;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
  position: relative;
}

/* Custom smooth scrollbar for table */
.sl-table-wrapper::-webkit-scrollbar {
  height: 7px;
}
.sl-table-wrapper::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.sl-table-wrapper::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.sl-table-wrapper::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* DataTables wrapper spacing so controls (Length, Filter search) breathe without clipping */
.sl-table-card .dataTables_wrapper {
  width: 100% !important;
  max-width: 100% !important;
  overflow: visible !important;
  box-sizing: border-box !important;
  padding: 0.25rem 0.25rem 0.45rem;
}

.sl-table-card .dataTables_wrapper .row {
  margin-left: 0 !important;
  margin-right: 0 !important;
  padding: 0.25rem 0.25rem;
  max-width: 100% !important;
  box-sizing: border-box !important;
  align-items: center;
}

.sl-table-card .dataTables_wrapper .col-sm-12,
.sl-table-card .dataTables_wrapper .col-md-6,
.sl-table-card .dataTables_wrapper .col-12 {
  padding-left: 0.35rem !important;
  padding-right: 0.35rem !important;
  box-sizing: border-box !important;
}

/* Responsive Table Layout */
.sl-table {
  width: 100% !important;
  min-width: 1060px;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: auto !important;
  box-sizing: border-box !important;
}

.sl-table th,
.sl-table td {
  box-sizing: border-box !important;
  vertical-align: middle;
}

.sl-table thead th {
  background: #f8fafc;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sl-text-muted);
  padding: 0.65rem 0.55rem;
  border-bottom: 1.5px solid var(--sl-border);
  border-top: none;
  white-space: nowrap;
  vertical-align: middle;
}

.sl-table tbody td {
  padding: 0.5rem 0.55rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--sl-border-lt);
  font-size: 0.82rem;
  color: var(--sl-text-sec);
  line-height: 1.3;
  background: #ffffff;
}

.sl-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* Specific Column Classes */
.sl-table .col-sno {
  width: 50px;
  min-width: 50px;
  text-align: center;
  font-weight: 700;
  color: #64748b;
}

.sl-table .col-invoice {
  min-width: 95px;
  white-space: nowrap;
}

.sl-table .col-datetime {
  min-width: 115px;
  white-space: nowrap;
}

.sl-table .col-customer {
  min-width: 135px;
}

.sl-table .col-cashier {
  min-width: 100px;
  white-space: nowrap;
}

.sl-table .col-items {
  min-width: 105px;
  text-align: center;
  white-space: nowrap;
}

.sl-table .col-amount {
  min-width: 120px;
  white-space: nowrap;
}

.sl-table .col-status {
  min-width: 110px;
  text-align: center;
  white-space: nowrap;
}

/* ═══════ PINNED / STICKY ACTION COLUMN (NEVER HIDDEN) ═══════ */
.sl-table th.col-action {
  position: sticky;
  right: 0;
  background: #f8fafc;
  z-index: 5;
  min-width: 220px;
  width: 220px;
  text-align: center;
  box-shadow: -4px 0 8px -2px rgba(15, 23, 42, 0.06);
}

.sl-table td.col-action {
  position: sticky;
  right: 0;
  background: #ffffff;
  z-index: 4;
  min-width: 220px;
  width: 220px;
  text-align: center;
  box-shadow: -4px 0 8px -2px rgba(15, 23, 42, 0.06);
  overflow: visible !important;
}

.sl-table tbody tr:hover td.col-action {
  background: #f8fafc;
}

/* Ensure opened dropdown always floats on top of subsequent table rows */
.sl-table tbody tr.sl-row-active-dropdown,
.sl-table tbody tr:has(.sl-dropdown.show),
.sl-table tbody tr:has([aria-expanded="true"]) {
  position: relative !important;
  z-index: 1050 !important;
}

.sl-table tbody tr.sl-row-active-dropdown td.col-action,
.sl-table tbody tr:has(.sl-dropdown.show) td.col-action,
.sl-table tbody tr:has([aria-expanded="true"]) td.col-action,
.sl-table tbody td.sl-cell-active-dropdown {
  z-index: 1055 !important;
}

/* ═══════ DATE & TIME CELL ═══════ */
.sl-date-wrap {
  display: flex;
  flex-direction: column;
  line-height: 1.25;
  white-space: nowrap;
}
.sl-date-wrap .sl-d {
  font-weight: 700;
  color: #0f172a;
  font-size: 0.8rem;
}
.sl-date-wrap .sl-t {
  font-size: 0.7rem;
  color: #64748b;
  font-weight: 600;
}

/* ═══════ ERP AMOUNT & CALCULATION CELL ═══════ */
.sl-amt-block {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.sl-amt-net {
  font-size: 0.92rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.01em;
  white-space: nowrap;
}
.sl-amt-net .curr {
  font-size: 0.7rem;
  font-weight: 700;
  color: #64748b;
  margin-right: 2px;
}
.sl-amt-breakdown {
  display: flex;
  align-items: center;
  gap: 3px;
  font-size: 0.7rem;
  line-height: 1;
  white-space: nowrap;
}
.sl-amt-breakdown .sub {
  color: #64748b;
  font-weight: 600;
}
.sl-amt-breakdown .disc {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fee2e2;
  border-radius: 4px;
  padding: 1px 3px;
  font-size: 0.68rem;
}

/* ═══════ STATUS BADGES ═══════ */
.badge-erp {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 0.25rem 0.6rem;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  white-space: nowrap;
}
.badge-erp-success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}
.badge-erp-return {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}
.badge-erp-exchange {
  background: #f5f3ff;
  color: #5b21b6;
  border: 1px solid #ddd6fe;
}

/* ═══════ FULL RESPONSIVE ACTION BUTTONS ═══════ */
.sl-actions-wrap {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  white-space: nowrap;
  flex-wrap: nowrap;
}

.sl-act {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  height: 26px;
  padding: 0 7px;
  border-radius: 6px;
  font-size: 0.72rem;
  font-weight: 700;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none !important;
  line-height: 1;
  white-space: nowrap;
  flex-shrink: 0;
  box-sizing: border-box;
}
.sl-act i {
  font-size: 0.78rem;
  line-height: 1;
}
.sl-act:hover {
  transform: translateY(-1px);
}

.sl-act-view {
  background: #eff6ff;
  color: #1d4ed8 !important;
  border-color: #bfdbfe;
}
.sl-act-view:hover {
  background: #2563eb;
  color: #ffffff !important;
  border-color: #2563eb;
  box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
}

.sl-act-print {
  background: #f1f5f9;
  color: #0f172a !important;
  border-color: #cbd5e1;
}
.sl-act-print:hover {
  background: #0f172a;
  color: #ffffff !important;
  border-color: #0f172a;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}

.sl-act-inv {
  background: #f0fdf4;
  color: #15803d !important;
  border-color: #bbf7d0;
}
.sl-act-inv:hover {
  background: #16a34a;
  color: #ffffff !important;
  border-color: #16a34a;
  box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
}

.sl-act-edit {
  background: #f5f3ff;
  color: #6d28d9 !important;
  border-color: #ddd6fe;
  width: 26px;
  height: 26px;
  padding: 0;
}
.sl-act-edit:hover {
  background: #7c3aed;
  color: #ffffff !important;
  border-color: #7c3aed;
  box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);
}

.sl-dropdown {
  position: relative;
  display: inline-block;
}

.sl-act-more {
  background: #f8fafc;
  color: #475569 !important;
  border-color: #cbd5e1;
  width: 26px;
  height: 26px;
  padding: 0;
}
.sl-act-more:hover, .sl-act-more[aria-expanded="true"] {
  background: #e2e8f0;
  color: #0f172a !important;
  border-color: #94a3b8;
}

.sl-dropdown-menu {
  position: absolute !important;
  top: 100% !important;
  right: 0 !important;
  left: auto !important;
  margin-top: 4px !important;
  z-index: 1060 !important;
  border-radius: 10px !important;
  box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.22) !important;
  border: 1.5px solid #cbd5e1 !important;
  padding: 0.35rem !important;
  min-width: 185px;
  background: #ffffff !important;
}

.dropup .sl-dropdown-menu {
  top: auto !important;
  bottom: 100% !important;
  margin-top: 0 !important;
  margin-bottom: 4px !important;
}
.sl-dropdown-menu .dropdown-item {
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.45rem 0.7rem;
  display: flex;
  align-items: center;
  gap: 0.45rem;
  color: #1e293b;
}
.sl-dropdown-menu .dropdown-item:hover {
  background-color: #f1f5f9;
  color: #0f172a;
}

/* ═══════ MOBILE CARDS ═══════ */
.sl-cards { display: none; }

.slc-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
  margin-bottom: 0.65rem;
  overflow: visible;
}
.slc-head {
  display: flex; align-items: center; justify-content: space-between; gap: .5rem;
  padding: .65rem .85rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e9edf2;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}
.slc-head-l { display: flex; flex-direction: column; min-width: 0; gap: 2px; }
.slc-inv { font-size: .95rem; font-weight: 800; color: #0f172a; }
.slc-sub { display: flex; align-items: center; gap: 4px; font-size: .72rem; color: #64748b; font-weight: 600; }

.slc-meta { display: flex; flex-wrap: wrap; gap: .35rem .75rem; padding: .55rem .85rem; }
.slc-meta span { display: inline-flex; align-items: center; gap: 4px; font-size: .74rem; color: #475569; font-weight: 600; }

.slc-items-badge-row { padding: 0 .85rem .5rem; }

.slc-total {
  display: flex; align-items: center; justify-content: space-between; gap: .5rem;
  margin: 0 .85rem .65rem; padding: .55rem .75rem;
  background: linear-gradient(135deg, #eff6ff 0%, #e0ecff 100%);
  border: 1px solid #dbeafe; border-radius: 10px;
}
.slc-total .label { font-size: .7rem; font-weight: 800; text-transform: uppercase; color: #1e40af; }
.slc-total .val { font-size: 1rem; font-weight: 900; color: #1d4ed8; }

.slc-actions {
  padding: 0.65rem 0.85rem 0.75rem;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.slc-actions .sl-actions-wrap {
  display: flex !important;
  width: 100% !important;
  gap: 0.35rem !important;
  flex-wrap: wrap !important;
  justify-content: stretch !important;
}
.slc-actions .sl-act {
  height: 32px !important;
  font-size: 0.78rem !important;
  padding: 0 10px !important;
}
.slc-actions .sl-act-view,
.slc-actions .sl-act-print,
.slc-actions .sl-act-inv {
  flex: 1 1 calc(33.333% - 0.35rem) !important;
  min-width: 60px !important;
}
.slc-actions .sl-act-edit {
  flex: 0 0 32px !important;
  width: 32px !important;
  height: 32px !important;
  padding: 0 !important;
}
.slc-actions .sl-dropdown {
  flex: 0 0 32px !important;
}
.slc-actions .sl-act-more {
  width: 32px !important;
  height: 32px !important;
  padding: 0 !important;
}
/* ═══════ INVOICE DETAILS MODAL (FULL PRODUCTS DETAIL) ═══════ */
.erp-modal .modal-content {
  border-radius: 16px;
  border: 1px solid #cbd5e1;
  box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
  overflow: hidden;
}
.erp-modal-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  padding: 1rem 1.35rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.erp-modal-header h4 {
  font-size: 1.18rem;
  font-weight: 800;
  margin: 0;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 0.55rem;
}
.erp-modal-body {
  padding: 1.25rem;
  background: #f8fafc;
}
.erp-card-box {
  background: #ffffff;
  border: 1px solid var(--sl-border);
  border-radius: 10px;
  padding: 0.85rem 1.15rem;
  margin-bottom: 1rem;
  box-shadow: var(--sl-shadow-sm);
}
.erp-card-title {
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--sl-text-muted);
  margin-bottom: 0.55rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.erp-kv-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 0.55rem 0.85rem;
}
.erp-kv {
  display: flex;
  flex-direction: column;
}
.erp-kv span.k {
  font-size: 0.7rem;
  font-weight: 700;
  color: var(--sl-text-muted);
  text-transform: uppercase;
}
.erp-kv span.v {
  font-size: 0.86rem;
  font-weight: 700;
  color: var(--sl-text);
  word-break: break-word;
}

.erp-items-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  background: #ffffff;
}
.erp-items-table th {
  background: #f1f5f9;
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  color: #475569;
  padding: 0.55rem 0.65rem;
  border-bottom: 1px solid #e2e8f0;
}
.erp-items-table td {
  padding: 0.55rem 0.65rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.8rem;
  color: #1e293b;
  vertical-align: middle;
}
.erp-items-table tr:last-child td {
  border-bottom: none;
}

.erp-calc-panel {
  background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
  border: 1.5px solid #bfdbfe;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-top: 0.85rem;
}
.erp-calc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 0.75rem;
  margin-bottom: 0.85rem;
}
.erp-calc-item {
  display: flex;
  flex-direction: column;
}
.erp-calc-item .label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #3b82f6;
}
.erp-calc-item .val {
  font-size: 1.05rem;
  font-weight: 800;
  color: #1e3a8a;
}

.erp-net-banner {
  background: #0f172a;
  border-radius: 8px;
  padding: 0.75rem 1.15rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #ffffff;
}
.erp-net-banner .banner-label {
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #93c5fd;
}
.erp-net-banner .banner-val {
  font-size: 1.35rem;
  font-weight: 900;
  color: #34d399;
}

.erp-modal-footer {
  background: #ffffff;
  padding: 0.85rem 1.25rem;
  border-top: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

@media (max-width: 767.98px) {
  .sl-hero { padding: 0.65rem 0.85rem; flex-direction: column; align-items: stretch; }
  .sl-hero-actions { width: 100%; justify-content: stretch; }
  .sl-hero-actions .sl-btn { flex: 1 1 auto; justify-content: center; }
  .sl-kpi-grid { grid-template-columns: 1fr 1fr; }
  .sl-table-wrapper { display: none !important; }
  .sl-cards { display: block; padding: 0.35rem 0.35rem 0.85rem; }
}
@media (max-width: 480px) {
  .sl-kpi-grid { grid-template-columns: 1fr; }
}
@media (min-width: 768px) {
  .sl-table-wrapper { display: block !important; }
  .sl-cards { display: none !important; }
}
</style>

<div class="sl-page">
  <div class="container-fluid px-1 px-md-2">

    {{-- ═══════ COMPACT HERO HEADER ═══════ --}}
    <div class="sl-hero">
      <div class="sl-hero-title">
        <div class="sl-hero-icon">
          <i class="bi bi-cart-check-fill"></i>
        </div>
        <div>
          <h2>Sales Register <span class="sl-hero-badge">Billing & Ledger</span></h2>
        </div>
      </div>

      <div class="sl-hero-actions">
        <a href="{{ route('sale.add') }}" class="sl-btn sl-btn-primary">
          <i class="bi bi-plus-circle-fill"></i> Add Sale
        </a>

        <a href="{{ url('bookings') }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-journal-bookmark"></i> All Bookings
        </a>

        <a href="{{ url('sale-returns') }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-arrow-counterclockwise"></i> Sale Returns
        </a>

        {{-- Filter Toggle Button with icon --}}
        <button type="button" id="btnToggleFilter" class="sl-btn sl-btn-glass">
          <i class="bi bi-funnel-fill"></i> Filters
          <span id="activeFilterBadge" class="badge bg-warning text-dark ms-1 d-none" style="font-size: 0.65rem;">Active</span>
        </button>

        <a href="{{ url()->previous() }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-arrow-left"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ SLEEK TOP KPI METRIC STRIP (PLACED AT TOP) ═══════ --}}
    <div class="sl-kpi-grid">
      <div class="sl-kpi-card sl-kpi-blue">
        <div class="sl-kpi-icon">
          <i class="bi bi-receipt-cutoff"></i>
        </div>
        <div class="sl-kpi-info">
          <span class="sl-kpi-label">Total Invoices</span>
          <span class="sl-kpi-value">{{ number_format($totalSalesCount ?? 0) }}</span>
          <span class="sl-kpi-sub">Processed Orders</span>
        </div>
      </div>

      <div class="sl-kpi-card sl-kpi-amber">
        <div class="sl-kpi-icon">
          <i class="bi bi-calculator"></i>
        </div>
        <div class="sl-kpi-info">
          <span class="sl-kpi-label">Gross Sales (Subtotal)</span>
          <span class="sl-kpi-value">PKR {{ $fmt($totalGrossSales ?? 0) }}</span>
          <span class="sl-kpi-sub">Before Discounts</span>
        </div>
      </div>

      <div class="sl-kpi-card sl-kpi-purple">
        <div class="sl-kpi-icon">
          <i class="bi bi-tags-fill"></i>
        </div>
        <div class="sl-kpi-info">
          <span class="sl-kpi-label">Total Discounts Given</span>
          <span class="sl-kpi-value">PKR {{ $fmt($totalDiscountGiven ?? 0) }}</span>
          <span class="sl-kpi-sub">Customer Savings</span>
        </div>
      </div>

      <div class="sl-kpi-card sl-kpi-emerald">
        <div class="sl-kpi-icon">
          <i class="bi bi-cash-stack"></i>
        </div>
        <div class="sl-kpi-info">
          <span class="sl-kpi-label">Net Sales Revenue</span>
          <span class="sl-kpi-value text-success">PKR {{ $fmt($totalNetSales ?? 0) }}</span>
          <span class="sl-kpi-sub">After Discount (Final)</span>
        </div>
      </div>
    </div>

    @if(auth()->user()->roles->filter(function($role) { return stripos($role->name, 'cashier') !== false; })->isNotEmpty())
    <div class="cashier-top-bar">
      <div class="cashier-top-chip">
        <i class="bi bi-wallet2 text-warning"></i> Opening Cash: <strong>PKR {{ $fmt($openingBalance ?? 0) }}</strong>
      </div>
      <div class="cashier-top-chip">
        <i class="bi bi-graph-up-arrow text-success"></i> Today's Net Sale: <strong>PKR {{ $fmt($todayNetSales ?? ($todaySales ?? 0)) }}</strong>
      </div>
      <div class="cashier-top-chip">
        <i class="bi bi-dash-circle text-danger"></i> Today's Expense: <strong>PKR {{ $fmt($todayExpense ?? 0) }}</strong>
      </div>
      <div class="cashier-top-chip">
        <i class="bi bi-currency-dollar text-info"></i> Net Cash: <strong>PKR {{ $fmt($netCash ?? 0) }}</strong>
      </div>
    </div>
    @endif

    {{-- ═══════ COLLAPSIBLE FILTER CARD (HIDDEN BY DEFAULT) ═══════ --}}
    <div id="filterCollapse" class="sl-filter-card" style="display: none;">
      <div class="sl-quick-filters">
        <span class="sl-qf-label"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Quick Date:</span>
        <button type="button" class="sl-qf-btn" data-preset="today">Today</button>
        <button type="button" class="sl-qf-btn" data-preset="yesterday">Yesterday</button>
        <button type="button" class="sl-qf-btn" data-preset="this_week">This Week</button>
        <button type="button" class="sl-qf-btn" data-preset="this_month">This Month</button>
        <button type="button" class="sl-qf-btn active" data-preset="all">All Time</button>
      </div>

      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> From Date</label>
          <input type="date" id="filterFrom" class="sl-input">
        </div>

        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> To Date</label>
          <input type="date" id="filterTo" class="sl-input">
        </div>

        @if(auth()->user()->hasRole('Admin'))
        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-person me-1 text-primary"></i> Cashier / User</label>
          <select id="filterUser" class="sl-input">
            <option value="">All Users</option>
            @foreach(\App\Models\User::all() as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="col-12 col-md-3 d-flex gap-2">
          <button id="btnFilter" class="sl-btn sl-btn-primary w-100">
            <i class="bi bi-funnel me-1"></i> Apply
          </button>
          <button id="btnReset" class="sl-btn sl-btn-outline w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </button>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN DATA TABLE CARD (100% FIT — NO HORIZONTAL SCROLL) ═══════ --}}
    <div class="sl-table-card">
      <div class="sl-table-wrapper">
        <table id="productTable" class="table sl-table align-middle" style="width:100%">
          <thead>
            <tr>
              <th class="col-sno text-center">S.No</th>
              <th class="col-invoice">Invoice No</th>
              <th class="col-datetime">Date | Time</th>
              <th class="col-customer">Customer</th>
              <th class="col-cashier">Cashier</th>
              <th class="col-items text-center">Total Items</th>
              <th class="col-amount">Amount (Net)</th>
              <th class="col-status text-center">Status</th>
              <th class="col-action text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loaded via DataTables Server-Side AJAX -->
          </tbody>
        </table>
      </div>

      {{-- ═══════ MOBILE CARDS (rendered by JS from DataTable data) ═══════ --}}
      <div id="saleCards" class="sl-cards"></div>
    </div>

  </div>
</div>

{{-- ═══════ INVOICE DETAILS MODAL (FULL PRODUCTS DETAIL) ═══════ --}}
<div class="modal fade erp-modal" id="saleDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="erp-modal-header">
        <div>
          <h4>
            <i class="bi bi-file-earmark-text-fill text-info"></i>
            Invoice Details <span id="mdlInvoiceNo" class="badge bg-primary ms-2">SAL-000</span>
          </h4>
          <div class="d-flex align-items-center gap-2 mt-1">
            <span id="mdlStatusBadge" class="badge-erp badge-erp-success">Completed</span>
            <span class="text-light opacity-75" style="font-size: 0.75rem;">•</span>
            <span id="mdlOrderType" class="badge bg-secondary" style="font-size: 0.7rem;">Walk-in</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="erp-modal-body">
        {{-- Loading Spinner --}}
        <div id="mdlLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
          <p class="mt-2 text-muted fw-bold">Loading invoice data...</p>
        </div>

        <div id="mdlContent" style="display: none;">
          {{-- Top Summary Cards --}}
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <div class="erp-card-box h-100">
                <div class="erp-card-title">
                  <i class="bi bi-person-badge-fill text-primary"></i> Customer Information
                </div>
                <div class="erp-kv-grid">
                  <div class="erp-kv">
                    <span class="k">Customer Name</span>
                    <span class="v" id="mdlCustomerName">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Type</span>
                    <span class="v" id="mdlCustomerType">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Phone</span>
                    <span class="v" id="mdlCustomerPhone">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Address</span>
                    <span class="v" id="mdlCustomerAddress">-</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="erp-card-box h-100">
                <div class="erp-card-title">
                  <i class="bi bi-info-circle-fill text-primary"></i> Transaction Meta
                </div>
                <div class="erp-kv-grid">
                  <div class="erp-kv">
                    <span class="k">Date & Time</span>
                    <span class="v" id="mdlDate">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Billed By (Cashier)</span>
                    <span class="v" id="mdlCashier">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Branch</span>
                    <span class="v" id="mdlBranch">-</span>
                  </div>
                  <div class="erp-kv">
                    <span class="k">Reference / M.Bill</span>
                    <span class="v" id="mdlReference">-</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Itemized Products Table (ALL Products Shown Here) --}}
          <div class="erp-card-box mt-2 mb-0">
            <div class="erp-card-title">
              <i class="bi bi-box-seam-fill text-primary"></i> Itemized Products (<span id="mdlItemCount">0</span> items)
            </div>
            <div class="table-responsive">
              <table class="erp-items-table">
                <thead>
                  <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 32%;">Item Name & Variant</th>
                    <th style="width: 14%;">Barcode / SKU</th>
                    <th style="width: 8%;">Unit</th>
                    <th style="width: 8%; text-align: center;">Qty</th>
                    <th style="width: 11%; text-align: right;">Unit Price</th>
                    <th style="width: 8%; text-align: center;" title="Trade Offer (Free / Scheme Qty)">T.O</th>
                    <th style="width: 8%; text-align: right;">Discount</th>
                    <th style="width: 10%; text-align: right;">Total</th>
                  </tr>
                </thead>
                <tbody id="mdlItemsBody">
                  <!-- Populated via JS -->
                </tbody>
              </table>
            </div>
          </div>

          {{-- Calculation & Financial Settlement Panel --}}
          <div class="erp-calc-panel">
            <div class="erp-calc-grid">
              <div class="erp-calc-item">
                <span class="label">Gross Subtotal</span>
                <span class="val" id="mdlGross">PKR 0</span>
              </div>
              <div class="erp-calc-item">
                <span class="label">Total Discount</span>
                <span class="val text-danger" id="mdlDiscount">- PKR 0</span>
              </div>
              <div class="erp-calc-item">
                <span class="label">Cash Paid</span>
                <span class="val" id="mdlCash">PKR 0</span>
              </div>
              <div class="erp-calc-item">
                <span class="label">Card / Bank</span>
                <span class="val" id="mdlCard">PKR 0</span>
              </div>
            </div>

            <div class="erp-net-banner">
              <div class="banner-label">
                <i class="bi bi-check2-circle me-1"></i> Net Amount Payable (After Discount)
              </div>
              <div class="banner-val" id="mdlNet">
                PKR 0
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="erp-modal-footer">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a id="mdlBtnSlip" href="#" target="_blank" class="sl-btn sl-btn-outline">
            <i class="bi bi-printer-fill text-dark"></i> Print Slip
          </a>
          <a id="mdlBtnInvoice" href="#" target="_blank" class="sl-btn sl-btn-primary">
            <i class="bi bi-file-earmark-text-fill"></i> Print A4 Invoice
          </a>
          <a id="mdlBtnDC" href="#" target="_blank" class="sl-btn sl-btn-outline">
            <i class="bi bi-truck text-emerald"></i> Delivery Challan
          </a>
        </div>

        <div class="d-flex align-items-center gap-2">
          <a id="mdlBtnEdit" href="#" class="sl-btn sl-btn-outline">
            <i class="bi bi-pencil-square text-primary"></i> Edit
          </a>
          <a id="mdlBtnReturn" href="#" class="sl-btn sl-btn-outline text-danger">
            <i class="bi bi-arrow-return-left"></i> Return
          </a>
          <button type="button" class="sl-btn sl-btn-outline" data-bs-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    margin-left: -100px;
    margin-top: -45px;
    text-align: center;
    padding: 12px;
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    border-radius: 10px;
    z-index: 1000;
    font-size: 0.95rem;
    color: #0f172a;
}
.dataTables_filter input {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 7px !important;
    padding: 0.35rem 0.75rem !important;
    font-size: 0.82rem !important;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX: false,
        responsive: false,
        ajax: {
            url: "{{ url('sale') }}",
            data: function (d) {
                d.from_date = $('#filterFrom').val();
                d.to_date = $('#filterTo').val();
                d.filter_user = $('#filterUser').val();
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false, className: 'col-sno text-center' },
            { data: 1, className: 'col-invoice' },
            { data: 2, className: 'col-datetime' },
            { data: 3, className: 'col-customer' },
            { data: 4, className: 'col-cashier' },
            { data: 5, orderable: false, searchable: false, className: 'col-items text-center' },
            { data: 6, className: 'col-amount' },
            { data: 7, className: 'col-status text-center' },
            { data: 8, orderable: false, searchable: false, className: 'col-action text-center' }
        ],
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        order: [
            [1, 'desc']
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search Invoice, Customer, SKU...",
            processing: '<div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div><div class="fw-bold">Loading Sales...</div>'
        },
        drawCallback: function () {
            if (window.matchMedia('(max-width: 767.98px)').matches) renderSaleCards();
        }
    });

    // ═══════ FIX TABLE DROPDOWN STACKING ORDER ON OPEN ═══════
    $(document).on('show.bs.dropdown', '.sl-dropdown', function () {
        var $dropdown = $(this);
        var $tr = $dropdown.closest('tr');
        var $td = $dropdown.closest('td');
        $tr.addClass('sl-row-active-dropdown');
        $td.addClass('sl-cell-active-dropdown');

        // Check if near bottom of table wrapper or viewport, auto dropup
        var $wrapper = $dropdown.closest('.sl-table-wrapper');
        if ($wrapper.length) {
            var wrapperBottom = $wrapper.offset().top + $wrapper.outerHeight();
            var dropdownTop = $dropdown.offset().top;
            if ((wrapperBottom - dropdownTop) < 170) {
                $dropdown.addClass('dropup');
            } else {
                $dropdown.removeClass('dropup');
            }
        }
    });

    $(document).on('hidden.bs.dropdown', '.sl-dropdown', function () {
        $(this).removeClass('dropup');
        $('.sl-row-active-dropdown').removeClass('sl-row-active-dropdown');
        $('.sl-cell-active-dropdown').removeClass('sl-cell-active-dropdown');
    });

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    function stripTags(html) {
        var d = document.createElement('div');
        d.innerHTML = html == null ? '' : String(html);
        return d.textContent;
    }

    function formatNumber(val) {
        var n = parseFloat(val);
        if (isNaN(n)) return '0';
        return (n % 1 === 0) ? n.toLocaleString('en-US') : n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderSaleCards() {
        var data = table.rows({ page: 'current' }).data().toArray();
        var $wrap = $('#saleCards');
        if (!data.length) {
            $wrap.html('<div class="slc-empty text-center py-3 text-muted"><i class="bi bi-inbox fs-3"></i><div>No sales found.</div></div>');
            return;
        }
        var html = '';
        data.forEach(function (r) {
            var invoice = stripTags(r[1]),
                dateHtml = r[2],
                customer = r[3],
                user = r[4],
                itemsBadge = r[5],
                amountHtml = r[6],
                statusHtml = r[7],
                actionsHtml = r[8];

            html += '<div class="slc-card">'
                + '<div class="slc-head">'
                + '<div class="slc-head-l">'
                + '<span class="slc-inv">' + esc(invoice) + '</span>'
                + '<span class="slc-sub"><i class="bi bi-person"></i>' + esc(user) + '</span>'
                + '</div>'
                + '<div>' + statusHtml + '</div>'
                + '</div>'
                + '<div class="slc-meta">'
                + '<span><i class="bi bi-calendar3"></i>' + dateHtml + '</span>'
                + '<span><i class="bi bi-person-badge"></i>' + esc(customer) + '</span>'
                + '</div>'
                + '<div class="slc-items-badge-row">' + itemsBadge + '</div>'
                + '<div class="slc-total">'
                + '<div><span class="label">Net Amount</span></div>'
                + '<div>' + amountHtml + '</div>'
                + '</div>'
                + '<div class="slc-actions">' + actionsHtml + '</div>'
                + '</div>';
        });
        $wrap.html(html);
    }

    // ═══════ FILTER TOGGLE (OPEN / CLOSE ON CLICK) ═══════
    $('#btnToggleFilter').on('click', function() {
        $('#filterCollapse').slideToggle(180);
        $(this).toggleClass('active');
    });

    // ═══════ FILTER ACTIONS ═══════
    function checkActiveFilters() {
        var hasFilter = $('#filterFrom').val() || $('#filterTo').val() || ($('#filterUser').length && $('#filterUser').val());
        if (hasFilter) {
            $('#activeFilterBadge').removeClass('d-none');
        } else {
            $('#activeFilterBadge').addClass('d-none');
        }
    }

    $('#btnFilter').on('click', function() {
        checkActiveFilters();
        table.draw();
    });

    $('#btnReset').on('click', function() {
        $('#filterFrom').val('');
        $('#filterTo').val('');
        $('#filterUser').val('');
        $('.sl-qf-btn').removeClass('active');
        $('.sl-qf-btn[data-preset="all"]').addClass('active');
        checkActiveFilters();
        table.draw();
    });

    // ═══════ QUICK DATE FILTER PRESETS ═══════
    $('.sl-qf-btn').on('click', function() {
        $('.sl-qf-btn').removeClass('active');
        $(this).addClass('active');

        var preset = $(this).data('preset');
        var today = new Date();
        var formatDate = function(d) {
            var yyyy = d.getFullYear();
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return yyyy + '-' + mm + '-' + dd;
        };

        if (preset === 'today') {
            var tStr = formatDate(today);
            $('#filterFrom').val(tStr);
            $('#filterTo').val(tStr);
        } else if (preset === 'yesterday') {
            var y = new Date();
            y.setDate(today.getDate() - 1);
            var yStr = formatDate(y);
            $('#filterFrom').val(yStr);
            $('#filterTo').val(yStr);
        } else if (preset === 'this_week') {
            var firstDay = new Date(today.setDate(today.getDate() - today.getDay() + 1));
            var lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 7));
            $('#filterFrom').val(formatDate(firstDay));
            $('#filterTo').val(formatDate(lastDay));
        } else if (preset === 'this_month') {
            var firstDayM = new Date(today.getFullYear(), today.getMonth(), 1);
            var lastDayM = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            $('#filterFrom').val(formatDate(firstDayM));
            $('#filterTo').val(formatDate(lastDayM));
        } else { // 'all'
            $('#filterFrom').val('');
            $('#filterTo').val('');
        }

        checkActiveFilters();
        table.draw();
    });

    // ═══════ VIEW MORE (INVOICE DETAILS MODAL) ═══════
    $(document).on('click', '.btn-view-sale', function(e) {
        e.preventDefault();
        var saleId = $(this).data('id');
        if (!saleId) return;

        $('#saleDetailModal').modal('show');
        $('#mdlLoading').show();
        $('#mdlContent').hide();

        $.ajax({
            url: "{{ url('sales') }}/" + saleId + "/details",
            type: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.success && resp.sale) {
                    var s = resp.sale;
                    $('#mdlInvoiceNo').text(s.invoice_no);
                    $('#mdlStatusBadge').attr('class', 'badge-erp ' + (s.status_class || 'badge-erp-success')).text(s.status);
                    $('#mdlOrderType').text(s.order_type);

                    // Customer Info
                    $('#mdlCustomerName').text(s.customer.name || '-');
                    $('#mdlCustomerType').text(s.customer.type || 'Walk-in');
                    $('#mdlCustomerPhone').text(s.customer.phone || '-');
                    $('#mdlCustomerAddress').text(s.customer.address || '-');

                    // Meta Info
                    $('#mdlDate').text(s.date || '-');
                    $('#mdlCashier').text(s.cashier || '-');
                    $('#mdlBranch').text(s.branch || '-');
                    $('#mdlReference').text(s.reference || '-');

                    // Line Items (All products listed here cleanly)
                    $('#mdlItemCount').text(s.items ? s.items.length : 0);
                    var rowsHtml = '';
                    if (s.items && s.items.length) {
                        s.items.forEach(function(item, idx) {
                            var vText = item.variant ? '<span class="badge bg-light text-dark border ms-1">' + esc(item.variant) + '</span>' : '';
                            rowsHtml += '<tr>'
                                + '<td>' + (idx + 1) + '</td>'
                                + '<td><strong>' + esc(item.item_name) + '</strong>' + vText + '</td>'
                                + '<td><span class="font-monospace text-muted">' + esc(item.item_code || '-') + '</span></td>'
                                + '<td>' + esc(item.unit || 'PC') + '</td>'
                                + '<td style="text-align: center;"><strong>' + formatNumber(item.qty) + '</strong></td>'
                                + '<td style="text-align: right;">PKR ' + formatNumber(item.price) + '</td>'
                                + '<td style="text-align: center;">' + (item.trade_offer > 0 ? ('<span class="badge bg-success-subtle text-success border border-success fw-bold">+' + formatNumber(item.trade_offer) + ' Free</span>') : '<span class="text-muted">-</span>') + '</td>'
                                + '<td style="text-align: right;" class="text-danger">' + (item.discount > 0 ? ('- PKR ' + formatNumber(item.discount)) : '-') + '</td>'
                                + '<td style="text-align: right;" class="fw-bold">PKR ' + formatNumber(item.total) + '</td>'
                                + '</tr>';
                        });
                    } else {
                        rowsHtml = '<tr><td colspan="9" class="text-center py-3 text-muted">No items recorded.</td></tr>';
                    }
                    $('#mdlItemsBody').html(rowsHtml);

                    // Financial Panel
                    var fin = s.financial || {};
                    $('#mdlGross').text('PKR ' + formatNumber(fin.gross || 0));
                    $('#mdlDiscount').text('- PKR ' + formatNumber(fin.total_discount || 0));
                    $('#mdlCash').text('PKR ' + formatNumber(fin.cash || 0));
                    
                    var cardText = 'PKR ' + formatNumber(fin.card || 0);
                    if (fin.card_account) cardText += ' (' + esc(fin.card_account) + ')';
                    $('#mdlCard').text(cardText);

                    $('#mdlNet').text('PKR ' + formatNumber(fin.net || 0));

                    // Action Button URLs
                    var urls = s.urls || {};
                    $('#mdlBtnSlip').attr('href', urls.receipt || '#');
                    $('#mdlBtnInvoice').attr('href', urls.invoice || '#');
                    $('#mdlBtnDC').attr('href', urls.dc || '#');
                    $('#mdlBtnEdit').attr('href', urls.edit || '#');
                    $('#mdlBtnReturn').attr('href', urls.return || '#');

                    $('#mdlLoading').hide();
                    $('#mdlContent').fadeIn(150);
                } else {
                    $('#mdlLoading').html('<div class="text-danger py-4"><i class="bi bi-exclamation-triangle fs-2"></i><div>Failed to load invoice details.</div></div>');
                }
            },
            error: function() {
                $('#mdlLoading').html('<div class="text-danger py-4"><i class="bi bi-exclamation-triangle fs-2"></i><div>Server error fetching details.</div></div>');
            }
        });
    });

    $(window).on('resize.slc', function () {
        if (window.matchMedia('(max-width: 767.98px)').matches) renderSaleCards();
    });
});
</script>
@endsection
