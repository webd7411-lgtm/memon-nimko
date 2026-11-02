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

.evc-actions { display: flex; gap: .45rem; flex-wrap: wrap; padding: 0 .9rem .9rem; }
.evc-actions .btn {
  flex: 1 1 40%; min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
  border-radius: 10px !important; font-size: .78rem !important; font-weight: 700; margin: 0 !important; white-space: normal;
  text-decoration: none;
}
.evc-actions .btn:hover { transform: translateY(-1px); }
.evc-actions .btn-outline-danger,
.evc-actions .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, .25); }

.evc-empty {
  text-align: center; padding: 2.5rem 1rem; color: #64748b;
  display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.evc-empty i { font-size: 2.2rem; color: #cbd5e1; }

/* ═══════ FULL MOBILE RESPONSIVE (no horizontal scroll) ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .ev-page { padding-bottom: 2rem; }

  .ev-hero { padding: 1.1rem 1rem; gap: .75rem; }
  .ev-hero-title { gap: .6rem; align-items: flex-start; }
  .ev-hero-title h2 { font-size: 1.05rem; }
  .ev-hero-icon { width: 40px; height: 40px; font-size: 1.15rem; border-radius: 10px; }
  .ev-hero-badge { font-size: .6rem; padding: .22rem .6rem; }
  .ev-hero .d-flex { width: 100%; flex-direction: column; align-items: stretch; }
  .ev-hero .d-flex .ev-btn { width: 100%; justify-content: center; }

  .ev-kpi-grid { gap: .65rem; margin-bottom: 1rem; }
  .ev-kpi-card { padding: .9rem .85rem; border-radius: 14px; }
  .ev-kpi-info p { font-size: .66rem; }
  .ev-kpi-info h3 { font-size: 1.2rem; }
  .ev-kpi-icon { width: 38px; height: 38px; font-size: 1rem; }

  .ev-filter-card { padding: 1rem; border-radius: 14px; margin-bottom: 1rem; }
  .ev-filter-card .row { margin: 0; }
  .ev-filter-card [class*="col-"] { padding: 0; margin-bottom: .6rem; }
  .ev-filter-card [class*="col-"]:last-child { margin-bottom: 0; }

  .ev-table-card { border-radius: 14px; }
  .ev-table-header { padding: .9rem 1rem; }
  .ev-table-title { font-size: .92rem; }

  /* DataTables controls: full width, no float overlap */
  .ev-table-wrapper { overflow: hidden; padding: 0; }
  .ev-table-wrapper > .dataTables_wrapper > .row > div { flex: 0 0 100%; max-width: 100%; }
  .ev-table-wrapper .dataTables_filter,
  .ev-table-wrapper .dataTables_length { float: none !important; text-align: left !important; margin: .35rem 0 0; }
  .ev-table-wrapper .dataTables_length { margin-top: .75rem; }
  .ev-table-wrapper .dataTables_length label { font-size: .78rem; color: var(--ev-text-muted); white-space: normal; }
  .ev-table-wrapper .dataTables_length select,
  .ev-table-wrapper .dataTables_filter input {
    display: inline-block;
    width: auto;
    padding: .45rem .7rem;
    border: 1px solid var(--ev-border);
    border-radius: 10px;
    font-size: .85rem;
    background: #fff;
    color: var(--ev-text);
    margin: 0 0 0 .35rem !important;
  }
  .ev-table-wrapper .dataTables_filter input { width: 100% !important; margin: .35rem 0 0 0 !important; }
  .ev-table-wrapper .dataTables_filter input:focus,
  .ev-table-wrapper .dataTables_length select:focus {
    border-color: var(--ev-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    outline: none;
  }

  /* Hide actual table on mobile, render premium cards instead */
  #productTable { display: none !important; }
  .ev-cards { display: block; padding: .5rem .6rem 1rem; }

  /* Pagination */
  .ev-table-wrapper .dataTables_info { text-align: center; font-size: .72rem; padding: 1rem 0 .35rem; }
  .ev-table-wrapper .pagination { flex-wrap: wrap; justify-content: center; gap: .25rem; }
  .ev-table-wrapper .page-item { margin: 0; }
  .ev-table-wrapper .page-link {
    min-width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 .5rem;
    font-size: .75rem;
    border: 1px solid var(--ev-border);
    border-radius: 8px;
    color: var(--ev-text-sec);
    background: #fff;
  }
  .ev-table-wrapper .page-item.active .page-link {
    background: var(--ev-primary);
    border-color: var(--ev-primary);
    color: #fff;
  }
}

/* Modal polish on small screens */
@media (max-width: 575.98px) {
  .ev-modal .modal-body,
  .ev-modal form.p-4 { padding: 1rem !important; }
  .ev-modal .modal-dialog { margin: .5rem; }
  .ev-modal .modal-title { font-size: 1rem; }
  .ev-modal [class*="col-"] { margin-bottom: .5rem; }
}

@media (max-width: 399.98px) {
  .ev-hero .d-flex { gap: .4rem; }
  .ev-table tbody td { font-size: .8rem; }
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

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalVouchersCount = count($vouchers);
      $totalExpenseSum = $vouchers->sum('total_amount');
    @endphp

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
</div>

{{-- ═══════ ADD EXPENSE MODAL ═══════ --}}
<div class="modal fade ev-modal" id="expenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1040px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-receipt me-2 text-primary"></i>Add Expense Voucher</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="expenseForm" class="p-4">
        @csrf
        <input type="hidden" name="evid" value="{{ $nextRvid ?? 'EVID-001' }}">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="ev-label">EVID</label>
            <input type="text" class="ev-input" value="{{ $nextRvid ?? 'EVID-001' }}" readonly style="background:#f8fafc;font-weight:600;">
          </div>
          <div class="col-md-4">
            <label class="ev-label">Date</label>
            <input type="date" name="date" class="ev-input" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-md-4">
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
            <div class="col-md-5">
              <input type="text" name="remarks[]" class="ev-input" placeholder="Remark / Description" required>
            </div>
            <div class="col-md-3">
              <select name="row_account_id[]" class="ev-input row-account-select" required>
                <option value="">Select Account (Paid From)</option>
                @foreach($accounts ?? [] as $acc)
                  <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <input type="number" step="0.01" name="amount[]" class="ev-input expense-amount" placeholder="0.00" required>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-center gap-2">
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
        <div class="d-flex gap-2 justify-content-end mt-4">
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
            <label class="ev-label">Expense Head Name</label>
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
            var actions = $(tds[8]).html() || '';

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
                + '<span class="evc-id">#' + esc(id) + '</span>'
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
            <div class="col-md-5">\
                <input type="text" name="remarks[]" class="ev-input" placeholder="Remark / Description" required>\
            </div>\
            <div class="col-md-3">\
                <select name="row_account_id[]" class="ev-input row-account-select" required>\
                    <option value="">Select Account (Paid From)</option>' + accountOptionsHtml + '</select>\
            </div>\
            <div class="col-md-2">\
                <input type="number" step="0.01" name="amount[]" class="ev-input expense-amount" placeholder="0.00" required>\
            </div>\
            <div class="col-md-2 d-flex align-items-center justify-content-center gap-2">\
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
});
</script>
@endsection