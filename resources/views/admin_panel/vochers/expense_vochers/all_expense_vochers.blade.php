@extends('admin_panel.layout.app')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --ev-bg: #f8fafc;
  --ev-surface: #ffffff;
  --ev-border: #e2e8f0;
  --ev-border-lt: #f1f5f9;
  --ev-text: #0f172a;
  --ev-text-sec: #475569;
  --ev-text-muted: #64748b;
  --ev-primary: #2563eb;
  --ev-radius: 16px;
  --ev-radius-sm: 10px;
  --ev-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --ev-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --ev-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.ev-page * {
  font-family: var(--ev-font);
}

.ev-page {
  background-color: var(--ev-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.ev-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--ev-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.3);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.ev-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(239, 68, 68, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.ev-hero > * {
  position: relative;
  z-index: 1;
}

.ev-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.ev-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.ev-hero-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  font-size: 1.4rem;
}

.ev-hero-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #93c5fd;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.ev-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--ev-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.ev-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.ev-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.ev-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.ev-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

.ev-btn-outline {
  background: #ffffff;
  color: #1e293b !important;
  border: 1.5px solid #cbd5e1;
}
.ev-btn-outline:hover {
  background: #f1f5f9;
  color: #0f172a !important;
  border-color: #94a3b8;
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.ev-kpi-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .ev-kpi-grid { grid-template-columns: 1fr; }
}

.ev-kpi-card {
  background: var(--ev-surface);
  border: 1px solid var(--ev-border);
  border-radius: var(--ev-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--ev-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.ev-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--ev-shadow-lg);
}

.ev-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--ev-text-muted);
}
.ev-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--ev-text);
  letter-spacing: -0.02em;
}

.ev-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.ev-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.ev-kpi-icon.red { background: #fef2f2; color: #ef4444; }

/* ═══════ FILTER CARD ═══════ */
.ev-filter-card {
  background: var(--ev-surface);
  border: 1px solid var(--ev-border);
  border-radius: var(--ev-radius);
  padding: 1.15rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--ev-shadow);
}

.ev-input {
  border: 1px solid var(--ev-border);
  border-radius: var(--ev-radius-sm);
  padding: 0.5rem 0.85rem;
  font-size: 0.88rem;
  color: var(--ev-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.ev-input:focus {
  border-color: var(--ev-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.ev-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--ev-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.ev-table-card {
  background: var(--ev-surface);
  border: 1px solid var(--ev-border);
  border-radius: var(--ev-radius);
  box-shadow: var(--ev-shadow-lg);
  overflow: hidden;
}

.ev-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--ev-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.ev-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--ev-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ev-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.ev-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: fixed;
}

.ev-table thead th {
  background: #f1f5f9;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #475569;
  padding: 0.85rem 0.75rem;
  border-bottom: 2px solid var(--ev-border);
  border-top: none;
  white-space: normal !important;
  vertical-align: middle;
}

.ev-table tbody td {
  padding: 0.85rem 0.75rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--ev-border-lt);
  font-size: 0.84rem;
  color: var(--ev-text-sec);
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: break-word;
}

.ev-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* Account Badges */
.ev-acc-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #eff6ff;
  color: #1e40af;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 0.35rem 0.6rem;
  font-size: 0.76rem;
  font-weight: 600;
  line-height: 1.35;
  white-space: normal !important;
  word-break: break-word;
  max-width: 100%;
  box-shadow: 0 1px 2px rgba(37, 99, 235, 0.05);
}

/* Breakdown Cards */
.ev-breakdown-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-left: 3px solid #3b82f6;
  border-radius: 8px;
  padding: 0.4rem 0.65rem;
  margin-bottom: 0.35rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.5rem;
}
.ev-breakdown-card:last-child {
  margin-bottom: 0;
}
.ev-breakdown-desc {
  flex: 1 1 auto;
  min-width: 0;
  font-size: 0.79rem;
  font-weight: 500;
  color: #1e293b;
  line-height: 1.35;
  word-break: break-word;
}
.ev-breakdown-acc {
  font-size: 0.68rem;
  font-weight: 600;
  color: #475569;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  padding: 0.08rem 0.35rem;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  margin-top: 3px;
}
.ev-breakdown-amt {
  flex: 0 0 auto;
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 0.2rem 0.5rem;
  white-space: nowrap;
}

/* Plus / Minus Action Buttons */
.ev-row-btn {
  width: 38px;
  height: 38px;
  min-width: 38px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 0;
  outline: none;
}
.ev-row-btn-add {
  background: #ecfdf5;
  color: #059669;
  border-color: #a7f3d0;
}
.ev-row-btn-add:hover {
  background: #10b981;
  color: #ffffff;
  border-color: #10b981;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}
.ev-row-btn-remove {
  background: #fff1f2;
  color: #e11d48;
  border-color: #fecdd3;
}
.ev-row-btn-remove:hover {
  background: #f43f5e;
  color: #ffffff;
  border-color: #f43f5e;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(244, 63, 94, 0.25);
}

.ev-table-wrapper { overflow-x: auto; }

/* ═══════ MODAL STYLING ═══════ */
.ev-modal .modal-content {
  border: none;
  border-radius: var(--ev-radius);
  box-shadow: var(--ev-shadow-lg);
  overflow: hidden;
}

.ev-modal .modal-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}

.ev-modal .modal-title {
  font-weight: 700;
  font-size: 1.15rem;
}

/* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
.ev-cards { display: none; }

.evc-card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
  margin-bottom: .85rem;
  overflow: hidden;
}
.evc-head {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  padding: .8rem .9rem .7rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e9edf2;
}
.evc-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
.evc-inv { font-size: .98rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: break-word; }
.evc-sub { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: #64748b; font-weight: 600; word-break: break-word; }
.evc-sub i { color: #94a3b8; font-size: .82rem; }
.evc-id { flex: 0 0 auto; font-size: .74rem; font-weight: 800; color: #1e40af; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 20px; padding: .22rem .6rem; white-space: nowrap; }

.evc-meta { display: flex; flex-wrap: wrap; gap: .4rem .8rem; padding: .6rem .9rem; }
.evc-meta span { display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; color: #475569; font-weight: 600; word-break: break-word; }
.evc-meta span i { color: #94a3b8; font-size: .82rem; }

.evc-items { margin: .1rem .9rem .7rem; border: 1px solid #e9edf2; border-radius: 12px; overflow: hidden; }
.evc-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .5rem .7rem; background: #f8fafc; border-bottom: 1px solid #e9edf2;
  font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #475569;
}
.evc-items-hd i { color: #ef4444; font-size: .82rem; }
.evc-items-hd b { color: #dc2626; background: #fef2f2; border-radius: 20px; padding: .03rem .5rem; font-size: .66rem; }
.evc-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .5rem .7rem; background: #fff; border-bottom: 1px solid #f1f5f9;
}
.evc-item:last-child { border-bottom: none; }
.evc-item-nm { flex: 1 1 auto; min-width: 0; font-size: .82rem; font-weight: 600; color: #0f172a; word-break: break-word; line-height: 1.3; }
.evc-item-total { flex: 0 0 auto; font-size: .78rem; font-weight: 800; color: #dc2626; white-space: nowrap; }

.evc-total {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  margin: 0 .9rem .7rem; padding: .6rem .8rem;
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  border: 1px solid #fecaca; border-radius: 12px;
}
.evc-total span { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #991b1b; }
.evc-total b { font-size: 1.02rem; font-weight: 900; color: #dc2626; word-break: break-word; }

.evc-actions { display: flex; gap: .45rem; flex-wrap: wrap; padding: 0 .78rem .78rem; }
.evc-btn-print {
  width: 100% !important;
  min-height: 38px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.45rem !important;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 10px !important;
  font-size: 0.8rem !important;
  font-weight: 700 !important;
  text-decoration: none !important;
  box-shadow: 0 3px 12px rgba(220, 38, 38, 0.28) !important;
  transition: all 0.2s ease !important;
}
.evc-btn-print:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
  color: #ffffff !important;
  transform: translateY(-1px);
}

.evc-empty {
  text-align: center; padding: 2.5rem 1rem; color: #64748b;
  display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.evc-empty i { font-size: 2.2rem; color: #cbd5e1; }

/* ═══════ FULL MOBILE RESPONSIVE (no horizontal scroll) ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .ev-page { padding-bottom: 1.5rem; overflow-x: hidden !important; max-width: 100% !important; }
  .ev-page > .container-fluid { padding: .6rem .65rem 1.25rem !important; }

  .ev-hero { padding: .85rem .9rem; gap: .65rem; border-radius: 14px; margin-bottom: .75rem; }
  .ev-hero-title { gap: .55rem; align-items: center; }
  .ev-hero-title h2 { font-size: 1rem; margin: 0; }
  .ev-hero-icon { width: 38px; height: 38px; font-size: 1.05rem; border-radius: 10px; }
  .ev-hero-badge { font-size: .58rem; padding: .18rem .5rem; }
  .ev-hero .d-flex { width: 100%; flex-direction: column; align-items: stretch; }
  .ev-hero .d-flex .ev-btn { width: 100%; min-height: 36px !important; padding: .35rem .65rem !important; font-size: .78rem !important; justify-content: center; border-radius: 9px !important; }

  .ev-tabs-bar {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: .4rem !important;
    padding: .35rem !important;
    margin-bottom: .75rem !important;
  }
  .ev-tab-btn {
    width: 100% !important;
    min-height: 36px !important;
    padding: .35rem .5rem !important;
    font-size: .72rem !important;
    justify-content: center !important;
    text-align: center !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }

  .ev-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: .5rem; margin-bottom: .75rem; }
  .ev-kpi-card { padding: .65rem .75rem; border-radius: 12px; gap: .45rem; }
  .ev-kpi-info p { font-size: .6rem; }
  .ev-kpi-info h3 { font-size: 1rem; word-break: break-word; }
  .ev-kpi-icon { width: 34px; height: 34px; font-size: .95rem; border-radius: 9px; }

  .ev-filter-card { padding: .7rem .75rem !important; border-radius: 12px; margin-bottom: .75rem; }
  .ev-filter-card .row { margin: 0 !important; display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: .45rem .5rem !important; }
  .ev-filter-card [class*="col-"] { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
  .ev-filter-card .col-md-3:nth-child(3):not(:last-child),
  .ev-filter-card .col-md-3:last-child { grid-column: 1 / -1 !important; }
  .ev-label { font-size: .62rem !important; margin-bottom: .2rem !important; }
  .ev-input { min-height: 36px !important; height: 36px !important; font-size: .78rem !important; padding: .28rem .55rem !important; border-radius: 8px !important; }
  .ev-filter-card .ev-btn { min-height: 36px !important; height: 36px !important; padding: .3rem .55rem !important; font-size: .76rem !important; border-radius: 8px !important; }

  .ev-table-card { border-radius: 14px; }
  .ev-table-header { padding: .75rem .85rem !important; flex-wrap: wrap !important; gap: .5rem !important; }
  .ev-table-title { font-size: .88rem; }
  .ev-table-header .ev-btn { min-height: 34px !important; padding: .3rem .65rem !important; font-size: .74rem !important; width: 100%; justify-content: center; }

  /* DataTables controls: full width, no float overlap */
  .ev-table-wrapper { overflow: hidden !important; padding: .55rem !important; }
  .ev-table-wrapper .table-responsive { overflow: visible !important; }
  .ev-table-wrapper > .dataTables_wrapper > .row,
  .ev-table-wrapper .dataTables_wrapper > .row { margin: 0 !important; }
  .ev-table-wrapper .dataTables_wrapper > .row > div { flex: 0 0 100%; max-width: 100%; padding: 0 !important; }
  .ev-table-wrapper .dataTables_filter,
  .ev-table-wrapper .dataTables_length { float: none !important; text-align: left !important; margin: 0 0 .45rem 0 !important; width: 100% !important; }
  .ev-table-wrapper .dataTables_length label,
  .ev-table-wrapper .dataTables_filter label {
    display: flex !important; align-items: center !important; gap: .45rem !important; width: 100% !important;
    font-size: .74rem; color: var(--ev-text-muted); white-space: nowrap; margin: 0;
  }
  .ev-table-wrapper .dataTables_length select,
  .ev-table-wrapper .dataTables_filter input {
    flex: 1 1 auto !important;
    min-height: 34px !important;
    height: 34px !important;
    padding: .25rem .6rem !important;
    border: 1px solid var(--ev-border) !important;
    border-radius: 8px !important;
    font-size: .78rem !important;
    background: #fff !important;
    color: var(--ev-text) !important;
    margin: 0 !important;
  }
  .ev-table-wrapper .dataTables_filter input:focus,
  .ev-table-wrapper .dataTables_length select:focus {
    border-color: var(--ev-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    outline: none;
  }

  /* Hide actual table on mobile, render premium cards instead */
  #productTable { display: none !important; }
  .ev-cards { display: block; padding: .25rem 0 .5rem; }

  /* Tab 2 (#expenseHeadsTable) -> Mobile Box Cards */
  #expenseHeadsTable,
  #expenseHeadsTable thead,
  #expenseHeadsTable tbody { display: block !important; width: 100% !important; }
  #expenseHeadsTable thead { display: none !important; }
  #expenseHeadsTable tbody tr {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: .35rem .55rem;
    background: #fff;
    border: 1px solid var(--ev-border);
    border-radius: 12px;
    padding: .65rem .75rem;
    margin-bottom: .55rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }
  #expenseHeadsTable tbody td {
    display: flex !important;
    align-items: center;
    border: none !important;
    padding: 0 !important;
    min-width: 0;
    white-space: normal !important;
    word-break: break-word;
    font-size: .74rem;
  }
  #expenseHeadsTable tbody td:nth-child(1) {
    order: 0; grid-column: 1; justify-self: start; width: fit-content;
    background: #eef4ff; color: #2b5bb3 !important; border-radius: 6px; padding: .08rem .45rem !important; font-size: .64rem;
  }
  #expenseHeadsTable tbody td:nth-child(6) { order: 0; grid-column: 2; justify-self: end; }
  #expenseHeadsTable tbody td:nth-child(2) { order: 1; grid-column: 1 / -1; }
  #expenseHeadsTable tbody td:nth-child(3) { order: 2; grid-column: 1 / -1; }
  #expenseHeadsTable tbody td:nth-child(4) { order: 3; }
  #expenseHeadsTable tbody td:nth-child(5) { order: 4; justify-content: flex-end; }
  #expenseHeadsTable tbody td:nth-child(7) {
    order: 5; grid-column: 1 / -1;
    border-top: 1px dashed #e2e8f0 !important;
    padding-top: .45rem !important;
    margin-top: .15rem;
    justify-content: flex-end;
  }
  #expenseHeadsTable tbody td:nth-child(7) .btn { min-height: 32px !important; padding: .22rem .55rem !important; font-size: .72rem !important; }
  #expenseHeadsTable tbody td[colspan],
  #expenseHeadsTable tbody td.dataTables_empty { grid-column: 1 / -1 !important; justify-content: center !important; padding: 1.5rem .5rem !important; }

  /* Pagination */
  .ev-table-wrapper .dataTables_info { text-align: center; font-size: .68rem; padding: .45rem 0 .25rem; }
  .ev-table-wrapper .pagination { flex-wrap: wrap; justify-content: center; gap: .2rem; margin: .25rem 0 0 !important; }
  .ev-table-wrapper .page-item { margin: 0; }
  .ev-table-wrapper .page-link {
    min-width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 .4rem;
    font-size: .7rem;
    border: 1px solid var(--ev-border);
    border-radius: 6px;
    color: var(--ev-text-sec);
    background: #fff;
  }
  .ev-table-wrapper .page-item.active .page-link {
    background: var(--ev-primary);
    border-color: var(--ev-primary);
    color: #fff;
  }
}

/* Modal polish & scrolling on small screens */
@media (max-width: 767.98px) {
  .ev-modal .modal-dialog {
    margin: .5rem !important;
    max-width: calc(100% - 1rem) !important;
    max-height: calc(100dvh - 1rem) !important;
  }
  .ev-modal .modal-content {
    max-height: calc(100dvh - 1rem) !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    border-radius: 14px !important;
  }
  .ev-modal .modal-header {
    padding: .7rem .85rem !important;
    flex-shrink: 0 !important;
  }
  .ev-modal .modal-body,
  .ev-modal form.p-4,
  .ev-modal #expenseForm {
    padding: .75rem .85rem !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
    flex: 1 1 auto !important;
  }
  .ev-modal .modal-title { font-size: .95rem !important; }
  .ev-modal .row.g-3 { --bs-gutter-x: .5rem; --bs-gutter-y: .45rem; }
  .ev-modal .row.g-2 { --bs-gutter-x: .45rem; --bs-gutter-y: .4rem; }
  .ev-modal .ev-label { font-size: .62rem !important; margin-bottom: .18rem !important; }
  .ev-modal .ev-input,
  .ev-modal .form-control,
  .ev-modal .form-select { min-height: 36px !important; height: 36px !important; font-size: .8rem !important; padding: .28rem .55rem !important; border-radius: 8px !important; }
  .ev-modal .btn { min-height: 34px !important; font-size: .78rem !important; }
  .ev-modal .ev-row-btn { width: 34px !important; height: 34px !important; }
  .ev-modal #modalTotalAmount { font-size: .95rem !important; }
}

@media (max-width: 399.98px) {
  .ev-hero .d-flex { gap: .4rem; }
  .ev-table tbody td { font-size: .8rem; }
}

/* ═══════ TABS STYLING ═══════ */
.ev-tabs-bar {
  display: flex;
  background: #ffffff;
  border: 1px solid var(--ev-border);
  border-radius: var(--ev-radius);
  padding: 0.45rem;
  box-shadow: var(--ev-shadow);
  margin-bottom: 1.5rem;
  overflow-x: auto;
}

.ev-tab-pill {
  border: none;
  background: transparent;
  color: var(--ev-text-sec);
  font-weight: 600;
  font-size: 0.92rem;
  padding: 0.65rem 1.35rem;
  border-radius: var(--ev-radius-sm);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  cursor: pointer;
  white-space: nowrap;
}

.ev-tab-pill:hover {
  background: #f1f5f9;
  color: var(--ev-text);
}

.ev-tab-pill.active {
  background: var(--ev-primary);
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

.ev-tab-pill.active .badge {
  background: #ffffff !important;
  color: var(--ev-primary) !important;
}

.ev-head-icon {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  background: #eff6ff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.05rem;
  flex-shrink: 0;
}
</style>

<div class="ev-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="ev-hero">
      <div class="ev-hero-title">
        <div class="ev-hero-icon">
          <i class="bi bi-wallet2"></i>
        </div>
        <div>
          <h2>Expense Vouchers Register</h2>
          <span class="ev-hero-badge">Audit & Voucher History</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="ev-btn ev-btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
          <i class="bi bi-plus-lg me-1"></i> Add Expense Voucher
        </button>

        <button class="ev-btn ev-btn-glass" data-bs-toggle="modal" data-bs-target="#addExpenseHeadModal">
          <i class="bi bi-folder-plus me-1"></i> Add Expense Head
        </button>

        <a class="ev-btn ev-btn-glass" href="{{ route('expense-vochers') }}">
          <i class="bi bi-box-arrow-up-right me-1"></i> Full Page Form
        </a>

        <a href="{{ url()->previous() }}" class="ev-btn ev-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
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

    {{-- ═══════ TABS NAVIGATION ═══════ --}}
    @php
      $totalVouchersCount = count($vouchers);
      $totalExpenseSum = $vouchers->sum('total_amount');
      $headsCount = count($allExpenseHeads ?? []);
    @endphp

    <div class="ev-tabs-bar">
      <ul class="nav nav-pills border-0 m-0" id="expenseTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="ev-tab-pill active" id="tab-vouchers-btn" data-bs-toggle="pill" data-bs-target="#tab-vouchers" type="button" role="tab" aria-controls="tab-vouchers" aria-selected="true">
            <i class="bi bi-receipt"></i>
            <span>Expense Vouchers</span>
            <span class="badge rounded-pill bg-light text-primary border ms-1">{{ number_format($totalVouchersCount) }}</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="ev-tab-pill" id="tab-heads-btn" data-bs-toggle="pill" data-bs-target="#tab-heads" type="button" role="tab" aria-controls="tab-heads" aria-selected="false">
            <i class="bi bi-tags"></i>
            <span>Expense Heads</span>
            <span class="badge rounded-pill bg-light text-secondary border ms-1" id="badgeTotalExpenseHeads">{{ number_format($headsCount) }}</span>
          </button>
        </li>
      </ul>
    </div>

    <div class="tab-content" id="expenseTabsContent">
      <!-- ═══════ TAB 1: EXPENSE VOUCHERS ═══════ -->
      <div class="tab-pane fade show active" id="tab-vouchers" role="tabpanel" aria-labelledby="tab-vouchers-btn">

        {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
        <div class="ev-kpi-grid">
      <div class="ev-kpi-card">
        <div class="ev-kpi-info">
          <p>Total Vouchers Issued</p>
          <h3>{{ number_format($totalVouchersCount) }}</h3>
        </div>
        <div class="ev-kpi-icon blue">
          <i class="bi bi-receipt"></i>
        </div>
      </div>

      <div class="ev-kpi-card">
        <div class="ev-kpi-info">
          <p>Total Expense Amount</p>
          <h3>Rs {{ number_format($totalExpenseSum, 2) }}</h3>
        </div>
        <div class="ev-kpi-icon red">
          <i class="bi bi-cash-stack"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="ev-filter-card">
      <form action="{{ route('all-expense-vochers') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label class="ev-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
          <input type="date" name="start_date" class="ev-input" value="{{ request('start_date') }}">
        </div>

        <div class="col-12 col-md-3">
          <label class="ev-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
          <input type="date" name="end_date" class="ev-input" value="{{ request('end_date') }}">
        </div>

        @if(auth()->user()->hasRole('Admin'))
        <div class="col-12 col-md-3">
          <label class="ev-label"><i class="bi bi-person me-1 text-primary"></i> User / Cashier</label>
          <select name="user_id" class="ev-input">
            <option value="all">All Users</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                {{ $u->name }}
              </option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="col-12 col-md-3 d-flex gap-2">
          <button type="submit" class="ev-btn ev-btn-primary w-100">
            <i class="bi bi-filter me-1"></i> Filter
          </button>
          <a href="{{ route('all-expense-vochers') }}" class="ev-btn ev-btn-outline w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="ev-table-card">
      <div class="ev-table-header">
        <h3 class="ev-table-title">
          <i class="bi bi-card-checklist text-primary me-1"></i> Recorded Expense Vouchers
        </h3>
      </div>

      <div class="ev-table-wrapper">
        <table id="productTable" class="table ev-table align-middle" style="width:100%">
          <thead>
            <tr>
              <th width="4%">ID</th>
              <th width="9%">Voucher No</th>
              <th width="14%">Expense Head</th>
              <th width="16%">Paid From (Account)</th>
              <th width="27%">Remarks & Breakdown</th>
              <th width="11%" class="text-end">Total Amount</th>
              <th width="9%">Date</th>
              <th width="6%">User</th>
              <th width="4%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vouchers as $voucher)
            <tr>
              <td class="fw-bold" data-order="{{ $voucher->id }}">#{{ $voucher->id }}</td>
              <td>
                <span class="badge font-monospace fw-bold" style="background:#f8fafc; color:#0f172a; border:1px solid #cbd5e1; font-size:0.8rem; padding:0.3rem 0.55rem; border-radius:6px;">
                  {{ $voucher->evid }}
                </span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-1">
                  <i class="bi bi-folder2-open text-primary fs-6 flex-shrink-0"></i>
                  <span class="fw-bold text-dark" style="font-size:0.84rem; line-height:1.35; word-break:break-word;">{{ $voucher->type_name }}</span>
                </div>
              </td>
              <td>
                @if($voucher->is_stock_adjustment)
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-wrap text-start" style="font-size:0.75rem; line-height:1.35;">
                    <i class="bi bi-box-seam me-1"></i> Stock Adjustment (Non-Cash)
                  </span>
                @else
                  @php
                    $rowAccIds = is_array($voucher->row_account_id)
                      ? $voucher->row_account_id
                      : (json_decode($voucher->row_account_id, true) ?? []);
                    $uniqueAccIds = array_filter(array_unique($rowAccIds));
                  @endphp
                  @if(!empty($uniqueAccIds))
                    @foreach($uniqueAccIds as $accId)
                      <div class="ev-acc-badge mb-1">
                        <i class="bi bi-wallet2 flex-shrink-0"></i>
                        <span>{{ $accountsMap[$accId] ?? 'Account #'.$accId }}</span>
                      </div>
                    @endforeach
                  @elseif($voucher->party_name && $voucher->party_name !== 'N/A')
                    <div class="ev-acc-badge">
                      <i class="bi bi-wallet2 flex-shrink-0"></i>
                      <span>{{ $voucher->party_name }}</span>
                    </div>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                @endif
              </td>
              <td>
                @php
                  $remarks = is_array($voucher->remarks)
                    ? $voucher->remarks
                    : json_decode($voucher->remarks, true) ?? [];

                  $amounts = is_array($voucher->amount)
                    ? $voucher->amount
                    : json_decode($voucher->amount, true) ?? [];
                @endphp

                @foreach ($remarks as $i => $remark)
                  <div class="ev-breakdown-card">
                    <div class="ev-breakdown-desc">
                      <i class="bi bi-chat-left-text me-1 text-secondary"></i> {{ $remark }}
                      @if(isset($rowAccIds[$i]) && isset($accountsMap[$rowAccIds[$i]]))
                        <div class="ev-breakdown-acc">
                          <i class="bi bi-wallet2"></i> {{ $accountsMap[$rowAccIds[$i]] }}
                        </div>
                      @endif
                    </div>
                    <div class="ev-breakdown-amt">
                      Rs {{ number_format($amounts[$i] ?? 0, 2) }}
                    </div>
                  </div>
                @endforeach
              </td>
              <td class="text-end fw-bold text-success" style="font-size:0.92rem;">
                Rs {{ number_format($voucher->total_amount, 2) }}
              </td>
              <td data-order="{{ \Carbon\Carbon::parse($voucher->date)->format('Y-m-d') }}">
                <span class="badge bg-light text-dark border fw-medium" style="font-size:0.76rem;">
                  <i class="bi bi-calendar3 me-1 text-muted"></i>{{ \Carbon\Carbon::parse($voucher->date)->format('d-m-Y') }}
                </span>
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary border fw-medium" style="font-size:0.74rem;">
                  <i class="bi bi-person me-1"></i>{{ $voucher->user->name ?? 'Admin' }}
                </span>
              </td>
              <td class="text-center">
                <a href="{{ route('expenseVoucher.print', $voucher->id) }}"
                  target="_blank"
                  class="btn btn-sm btn-outline-danger rounded-2" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center;" title="Print Receipt">
                  <i class="bi bi-printer"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- ═══════ MOBILE / TABLET PREMIUM CARDS (rendered by JS from DataTable rows) ═══════ --}}
      <div id="expenseCards" class="ev-cards"></div>
    </div>
  </div>
  <!-- ═══════ END TAB 1: EXPENSE VOUCHERS ═══════ -->

  <!-- ═══════ TAB 2: EXPENSE HEADS ═══════ -->
  <div class="tab-pane fade" id="tab-heads" role="tabpanel" aria-labelledby="tab-heads-btn">
    <div class="ev-table-card">
      <div class="ev-table-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h3 class="ev-table-title mb-1">
            <i class="bi bi-tags text-primary me-1"></i> Expense Heads Register
          </h3>
          <p class="text-muted small mb-0">Manage expense categories and account heads used across expense vouchers</p>
        </div>
        <button type="button" class="ev-btn ev-btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseHeadModal">
          <i class="bi bi-plus-lg me-1"></i> Add New Expense Head
        </button>
      </div>

      <div class="ev-table-wrapper p-3">
        <div class="table-responsive">
          <table id="expenseHeadsTable" class="table ev-table align-middle" style="width:100%">
            <thead>
              <tr>
                <th width="6%">ID</th>
                <th width="28%">Expense Head Name</th>
                <th width="12%">Type</th>
                <th width="14%">Linked Accounts</th>
                <th width="14%">Linked Vouchers</th>
                <th width="10%">Status</th>
                <th width="16%" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($allExpenseHeads ?? [] as $head)
              <tr>
                <td class="fw-bold" data-order="{{ $head->id }}">#{{ $head->id }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="ev-head-icon">
                      <i class="bi bi-tag-fill text-primary"></i>
                    </div>
                    <div>
                      <span class="fw-bold text-dark d-block" style="font-size:0.92rem;">{{ $head->name }}</span>
                      <span class="text-muted" style="font-size:0.75rem;">Created: {{ $head->created_at ? $head->created_at->format('d M, Y') : '-' }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border fw-medium" style="font-size:0.78rem;">
                    {{ $head->type ?? 'Expense' }}
                  </span>
                </td>
                <td>
                  @if(($head->accounts_count ?? 0) > 0)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold" style="font-size:0.78rem;">
                      <i class="bi bi-wallet2 me-1"></i>{{ $head->accounts_count }} Account(s)
                    </span>
                  @else
                    <span class="badge bg-light text-muted border" style="font-size:0.76rem;">0 Accounts</span>
                  @endif
                </td>
                <td>
                  @if(($head->expense_vouchers_count ?? 0) > 0)
                    <span class="badge bg-info-subtle text-info border border-info-subtle fw-semibold" style="font-size:0.78rem;">
                      <i class="bi bi-receipt me-1"></i>{{ $head->expense_vouchers_count }} Voucher(s)
                    </span>
                  @else
                    <span class="badge bg-light text-muted border" style="font-size:0.76rem;">0 Vouchers</span>
                  @endif
                </td>
                <td>
                  @if($head->status == 1)
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size:0.78rem;">
                      <i class="bi bi-check-circle me-1"></i>Active
                    </span>
                  @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold" style="font-size:0.78rem;">
                      <i class="bi bi-x-circle me-1"></i>Inactive
                    </span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="d-inline-flex gap-1">
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-2 btn-edit-head" 
                            data-id="{{ $head->id }}" 
                            data-name="{{ $head->name }}" 
                            data-status="{{ $head->status }}"
                            title="Edit Expense Head">
                      <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger rounded-2 btn-delete-head" 
                            data-id="{{ $head->id }}" 
                            data-name="{{ $head->name }}"
                            data-vouchers="{{ $head->expense_vouchers_count ?? 0 }}"
                            data-accounts="{{ $head->accounts_count ?? 0 }}"
                            title="Delete Expense Head">
                      <i class="bi bi-trash me-1"></i> Delete
                    </button>
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
  <!-- ═══════ END TAB 2: EXPENSE HEADS ═══════ -->
</div>
<!-- ═══════ END TAB CONTENT ═══════ -->

  </div>
</div>

{{-- ═══════ ADD EXPENSE MODAL ═══════ --}}
<div class="modal fade ev-modal" id="expenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1040px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-receipt me-2 text-primary"></i>Add Expense Voucher</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="expenseForm" class="d-flex flex-column h-100" style="min-height:0;">
        <div class="modal-body p-3 p-md-4" style="overflow-y:auto; -webkit-overflow-scrolling:touch; max-height: calc(85vh - 120px);">
          @csrf
          <input type="hidden" name="evid" value="{{ $nextRvid ?? 'EVID-001' }}">
          <div class="row g-3 mb-3">
            <div class="col-6 col-md-4">
              <label class="ev-label">EVID</label>
              <input type="text" class="ev-input" value="{{ $nextRvid ?? 'EVID-001' }}" readonly style="background:#f8fafc;font-weight:600;">
            </div>
            <div class="col-6 col-md-4">
              <label class="ev-label">Date</label>
              <input type="date" name="date" class="ev-input" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-12 col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="ev-label mb-0">Expense Head</label>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#addExpenseHeadModal">
                  <i class="bi bi-plus-circle me-1"></i>+ New Head
                </button>
              </div>
              <select name="vendor_type" id="modalExpenseHead" class="ev-input" required>
                <option value="">Select Expense Head</option>
                @foreach($AccountHeads ?? [] as $head)
                  <option value="{{ $head->id }}">{{ $head->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <hr class="my-3 text-muted">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold fs-6 text-dark"><i class="bi bi-list-stars me-1 text-primary"></i> Expense Lines</div>
            <small class="text-muted">Select paying account for each item</small>
          </div>

          <div class="row g-2 text-muted small fw-bold text-uppercase mb-2 px-1 d-none d-md-flex align-items-center">
            <div class="col-md-5"><i class="bi bi-card-text me-1"></i> Remark / Description</div>
            <div class="col-md-3"><i class="bi bi-wallet2 me-1"></i> Paid From (Account)</div>
            <div class="col-md-2"><i class="bi bi-cash me-1"></i> Amount (PKR)</div>
            <div class="col-md-2 text-center"><i class="bi bi-gear me-1"></i> Action</div>
          </div>

          <div id="modalExpenseRows">
            <div class="row g-2 mb-2 expense-row align-items-center">
              <div class="col-12 col-md-5">
                <input type="text" name="remarks[]" class="ev-input" placeholder="Remark / Description" required>
              </div>
              <div class="col-12 col-md-3">
                <select name="row_account_id[]" class="ev-input row-account-select" required>
                  <option value="">Select Account (Paid From)</option>
                  @foreach($accounts ?? [] as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-7 col-md-2">
                <input type="number" step="0.01" name="amount[]" class="ev-input expense-amount" placeholder="0.00" required>
              </div>
              <div class="col-5 col-md-2 d-flex align-items-center justify-content-end justify-content-md-center gap-2">
                <button type="button" class="ev-row-btn ev-row-btn-add add-expense-row" title="Add Row">
                  <i class="bi bi-plus-lg"></i>
                </button>
                <button type="button" class="ev-row-btn ev-row-btn-remove remove-expense-row" title="Remove Row">
                  <i class="bi bi-dash-lg"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="row mt-3 align-items-center">
            <div class="col-md-6">
              <button type="button" class="btn btn-sm btn-outline-primary add-expense-row rounded-2 px-3 py-2 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Add Another Line
              </button>
            </div>
            <div class="col-md-4 offset-md-2">
              <label class="ev-label">Total Amount (PKR)</label>
              <input type="text" id="modalTotalAmount" class="ev-input fw-bold text-primary fs-5" value="0.00" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2.5 p-md-3 bg-light border-top d-flex gap-2 justify-content-end">
          <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="ev-btn ev-btn-primary px-4">
            <i class="bi bi-save me-1"></i> Save Voucher
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ═══════ ADD EXPENSE HEAD MODAL ═══════ --}}
<div class="modal fade ev-modal" id="addExpenseHeadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-folder-plus me-2 text-primary"></i>Add Expense Head</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="addExpenseHeadForm">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="ev-label">Expense Head Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="newHeadName" class="ev-input" placeholder="e.g. Utility Bills, Office Supplies, Fuel..." required>
          </div>
          <div id="headFormAlert" class="alert d-none py-2 px-3 small rounded-2 mb-0"></div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="saveHeadBtn" class="ev-btn ev-btn-primary">
            <i class="bi bi-check-lg me-1"></i> Save Head
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ═══════ EDIT EXPENSE HEAD MODAL ═══════ --}}
<div class="modal fade ev-modal" id="editExpenseHeadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Expense Head</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editExpenseHeadForm">
        @csrf
        <input type="hidden" name="head_id" id="editHeadId">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="ev-label">Expense Head Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="editHeadName" class="ev-input" placeholder="e.g. Utility Bills, Fuel, Staff Tea..." required>
          </div>
          <div class="mb-3">
            <label class="ev-label">Status</label>
            <select name="status" id="editHeadStatus" class="ev-input">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div id="editHeadAlert" class="alert d-none py-2 px-3 small rounded-2 mb-0"></div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="updateHeadBtn" class="ev-btn ev-btn-primary">
            <i class="bi bi-check-lg me-1"></i> Update Head
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    if (typeof $.fn === 'undefined' || typeof $.fn.DataTable === 'undefined') {
        console.warn('DataTables JS is not loaded yet.');
        return;
    }
    if ($.fn.DataTable.isDataTable('#productTable')) {
        $('#productTable').DataTable().destroy();
    }
    var table = $('#productTable').DataTable({
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        order: [
            [0, 'desc']
        ],
        columnDefs: [],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search vouchers..."
        },
        drawCallback: function () {
            if (window.matchMedia('(max-width: 991.98px)').matches) renderEvCards(this);
        }
    });

    window.setTimeout(function () {
        if (window.matchMedia('(max-width: 991.98px)').matches) renderEvCards(table);
    }, 0);

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    function renderEvCards(dt) {
        if (!dt || !dt.rows) return;
        var rows = dt.rows({ page: 'current' }).nodes().toArray();
        var $wrap = $('#expenseCards');
        if (!rows.length) {
            $wrap.html('<div class="evc-empty"><i class="bi bi-inbox"></i><span>No vouchers found.</span></div>');
            return;
        }
        var html = '';
        rows.forEach(function (tr) {
            var tds = $(tr).children('td');
            var id = $(tds[0]).text().trim();
            var vno = $(tds[1]).text().trim();
            var head = $(tds[2]).text().trim();
            var party = $(tds[3]).text().trim();
            var total = $(tds[5]).text().trim();
            var date = $(tds[6]).text().trim();
            var user = $(tds[7]).text().trim();
            var printUrl = $(tds[8]).find('a').attr('href') || '#';
            var actions = '<a href="' + printUrl + '" target="_blank" class="evc-btn-print"><i class="bi bi-printer-fill me-1"></i> Print Voucher</a>';

            var itemCount = 0;
            var items = '';
            var $breakdowns = $('<div>').html($(tds[4]).html()).find('.ev-breakdown-card');
            if ($breakdowns.length) {
                $breakdowns.each(function () {
                    var remark = $(this).find('.ev-breakdown-desc').clone().children().remove().end().text().trim();
                    var amt = $(this).find('.ev-breakdown-amt').text().trim();
                    itemCount++;
                    items += '<div class="evc-item">'
                        + '<span class="evc-item-nm">' + esc(remark) + '</span>'
                        + '<span class="evc-item-total">' + esc(amt) + '</span>'
                        + '</div>';
                });
            } else {
                $('<div>').html($(tds[4]).html()).find('.badge').each(function() {
                    itemCount++;
                    items += '<div class="evc-item"><span class="evc-item-nm">Item</span><span class="evc-item-total">' + esc($(this).text().trim()) + '</span></div>';
                });
            }

            html += '<div class="evc-card">'
                + '<div class="evc-head">'
                + '<div class="evc-head-l">'
                + '<span class="evc-inv">' + esc(vno) + '</span>'
                + '<span class="evc-sub"><i class="bi bi-folder2-open"></i>' + esc(head) + '</span>'
                + '</div>'
                + '<span class="evc-id">' + esc(id) + '</span>'
                + '</div>'
                + '<div class="evc-meta">'
                + '<span><i class="bi bi-wallet2"></i>' + esc(party) + '</span>'
                + '<span><i class="bi bi-calendar3"></i>' + esc(date) + '</span>'
                + '<span><i class="bi bi-person"></i>' + esc(user) + '</span>'
                + '</div>'
                + (items ? '<div class="evc-items"><div class="evc-items-hd"><span><i class="bi bi-chat-left-dots"></i>Remarks & Breakdown</span><b>' + itemCount + '</b></div>' + items + '</div>' : '')
                + '<div class="evc-total"><span>Total Amount</span><b>' + esc(total) + '</b></div>'
                + '<div class="evc-actions">' + actions + '</div>'
                + '</div>';
        });
        $wrap.html(html);
    }

    $(window).on('resize.evc', function () {
        if (window.matchMedia('(max-width: 991.98px)').matches) renderEvCards(table);
    });

    // Prevent aria-hidden on focused element — move focus before modal closes
    $('.modal').on('hide.bs.modal', function() {
        $(document.body).focus();
    });

    // Accounts HTML template for dynamic rows
    var accountOptionsHtml = `@foreach($accounts ?? [] as $acc)<option value="{{ $acc->id }}">{{ addslashes($acc->title) }} ({{ addslashes($acc->head->name ?? 'Account') }})</option>@endforeach`;

    // Add Expense Row
    $(document).on('click', '.add-expense-row', function() {
        var row = '<div class="row g-2 mb-2 expense-row align-items-center">\
            <div class="col-12 col-md-5">\
                <input type="text" name="remarks[]" class="ev-input" placeholder="Remark / Description" required>\
            </div>\
            <div class="col-12 col-md-3">\
                <select name="row_account_id[]" class="ev-input row-account-select" required>\
                    <option value="">Select Account (Paid From)</option>' + accountOptionsHtml + '</select>\
            </div>\
            <div class="col-7 col-md-2">\
                <input type="number" step="0.01" name="amount[]" class="ev-input expense-amount" placeholder="0.00" required>\
            </div>\
            <div class="col-5 col-md-2 d-flex align-items-center justify-content-end justify-content-md-center gap-2">\
                <button type="button" class="ev-row-btn ev-row-btn-add add-expense-row" title="Add Row"><i class="bi bi-plus-lg"></i></button>\
                <button type="button" class="ev-row-btn ev-row-btn-remove remove-expense-row" title="Remove Row"><i class="bi bi-dash-lg"></i></button>\
            </div>\
        </div>';
        $('#modalExpenseRows').append(row);
    });

    // Remove Expense Row
    $(document).on('click', '.remove-expense-row', function() {
        if ($('.expense-row').length > 1) {
            $(this).closest('.expense-row').remove();
        }
        calcModalTotal();
    });

    $(document).on('input', '.expense-amount', calcModalTotal);

    function calcModalTotal() {
        var total = 0;
        $('.expense-amount').each(function() { total += parseFloat($(this).val()) || 0; });
        $('#modalTotalAmount').val(total.toFixed(2));
    }

    // Save Voucher Submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $.ajax({
            url: '{{ route("expense.vochers.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $('#expenseModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var msg = 'Error saving voucher';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = Object.values(xhr.responseJSON.errors).flat();
                    msg = errs.join('\n');
                }
                alert(msg);
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Voucher');
            }
        });
    });

    // Add Expense Head AJAX Submission
    var wasExpenseModalOpen = false;
    $('#addExpenseHeadModal').on('show.bs.modal', function() {
        if ($('#expenseModal').hasClass('show')) {
            wasExpenseModalOpen = true;
            $('#expenseModal').modal('hide');
        }
    });

    $('#addExpenseHeadModal').on('hidden.bs.modal', function() {
        if (wasExpenseModalOpen) {
            $('#expenseModal').modal('show');
            wasExpenseModalOpen = false;
        }
    });

    $('#addExpenseHeadForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#saveHeadBtn');
        var $alert = $('#headFormAlert');
        var headName = $('#newHeadName').val().trim();

        if (!headName) return;

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');

        $.ajax({
            url: '{{ route("coa.head.store") }}',
            method: 'POST',
            data: $form.serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Head');
                var newHead = res.head || { id: res.id, name: headName };
                var headId = newHead.id || newHead.name;

                // Add to Expense Head select in Voucher Modal and select it
                $('#modalExpenseHead').append(
                    $('<option>', { value: headId, text: newHead.name, selected: true })
                );

                $alert.removeClass('d-none').addClass('alert-success').text('Head "' + newHead.name + '" added successfully!');
                $('#newHeadName').val('');

                setTimeout(function() {
                    $('#addExpenseHeadModal').modal('hide');
                    $alert.addClass('d-none');
                    if (!wasExpenseModalOpen) {
                        localStorage.setItem('activeExpenseTab', '#tab-heads');
                        location.reload();
                    }
                }, 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Head');
                var msg = 'Failed to add Expense Head.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = Object.values(xhr.responseJSON.errors).flat();
                    msg = errs.join(' ');
                }
                $alert.removeClass('d-none').addClass('alert-danger').text(msg);
            }
        });
    });

    // ═══════ EXPENSE HEADS EDIT & DELETE ═══════
    // Initialize DataTables for Expense Heads
    if ($.fn.DataTable && $('#expenseHeadsTable').length) {
        if (!$.fn.DataTable.isDataTable('#expenseHeadsTable')) {
            $('#expenseHeadsTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [
                    [0, 'desc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search expense heads..."
                }
            });
        }
    }

    // Open Edit Head Modal
    $(document).on('click', '.btn-edit-head', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var status = $(this).data('status');

        $('#editHeadId').val(id);
        $('#editHeadName').val(name);
        $('#editHeadStatus').val(status);
        $('#editHeadAlert').addClass('d-none').removeClass('alert-success alert-danger').text('');
        $('#editExpenseHeadModal').modal('show');
    });

    // Submit Edit Head Form
    $('#editExpenseHeadForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#editHeadId').val();
        var btn = $('#updateHeadBtn');
        var alertBox = $('#editHeadAlert');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
        alertBox.addClass('d-none').removeClass('alert-success alert-danger').text('');

        $.ajax({
            url: '/coa/head/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                name: $('#editHeadName').val(),
                status: $('#editHeadStatus').val()
            },
            success: function(res) {
                alertBox.removeClass('d-none').addClass('alert-success').text(res.message || 'Head updated successfully!');
                setTimeout(function() {
                    $('#editExpenseHeadModal').modal('hide');
                    localStorage.setItem('activeExpenseTab', '#tab-heads');
                    location.reload();
                }, 700);
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Update Head');
                var msg = 'Failed to update Expense Head.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = Object.values(xhr.responseJSON.errors).flat();
                    msg = errs.join(' ');
                }
                alertBox.removeClass('d-none').addClass('alert-danger').text(msg);
            }
        });
    });

    // Delete Head
    $(document).on('click', '.btn-delete-head', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var vouchersCount = parseInt($(this).data('vouchers') || 0);
        var accountsCount = parseInt($(this).data('accounts') || 0);

        if (vouchersCount > 0 || accountsCount > 0) {
            var reasons = [];
            if (vouchersCount > 0) reasons.push(vouchersCount + ' expense voucher(s)');
            if (accountsCount > 0) reasons.push(accountsCount + ' account(s)');
            alert('Cannot delete "' + name + '" because it is linked to ' + reasons.join(' and ') + '.\nPlease delete or reassign them first.');
            return;
        }

        if (confirm('Are you sure you want to delete the Expense Head "' + name + '"?')) {
            $.ajax({
                url: '/coa/head/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(res) {
                    localStorage.setItem('activeExpenseTab', '#tab-heads');
                    location.reload();
                },
                error: function(xhr) {
                    var msg = 'Failed to delete Expense Head.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                }
            });
        }
    });

    // Tab state memory across reloads
    var activeTab = localStorage.getItem('activeExpenseTab');
    if (window.location.hash) {
        activeTab = window.location.hash;
    }
    if (activeTab && $(activeTab).length) {
        var tabTrigger = $('button[data-bs-target="' + activeTab + '"]');
        if (tabTrigger.length) {
            new bootstrap.Tab(tabTrigger[0]).show();
        }
    }

    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('data-bs-target');
        localStorage.setItem('activeExpenseTab', target);
        window.location.hash = target;
        if ($.fn.DataTable) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    });
});
</script>
@endsection