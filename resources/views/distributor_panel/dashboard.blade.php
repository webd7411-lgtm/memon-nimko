@extends('distributor_panel.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --erp-bg: #f8fafc;
  --erp-card: #ffffff;
  --erp-border: #e2e8f0;
  --erp-border-hover: #cbd5e1;
  --erp-text-main: #0f172a;
  --erp-text-muted: #64748b;
  --erp-text-sub: #94a3b8;
  
  --erp-teal: #0f766e;
  --erp-teal-light: #f0fdf4;
  --erp-emerald: #10b981;
  --erp-emerald-light: #ecfdf5;
  
  --erp-blue: #2563eb;
  --erp-blue-light: #eff6ff;
  
  --erp-amber: #d97706;
  --erp-amber-light: #fffbeb;
  
  --erp-rose: #e11d48;
  --erp-rose-light: #fff1f2;
  
  --erp-indigo: #4f46e5;
  --erp-indigo-light: #eef2ff;

  --erp-radius: 10px;
  --erp-radius-sm: 6px;
  --erp-shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
  --erp-shadow-card: 0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
  --erp-shadow-hover: 0 6px 16px -2px rgba(15, 23, 42, 0.08), 0 3px 6px -2px rgba(15, 23, 42, 0.04);
}

.erp-dashboard {
  background: var(--erp-bg);
  min-height: 100vh;
  padding: 0.5rem 0.35rem 2.5rem 0.35rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  color: var(--erp-text-main);
  -webkit-font-smoothing: antialiased;
  overflow-x: hidden;
  max-width: 100vw;
  width: 100%;
}

.erp-dashboard * {
  box-sizing: border-box;
}

/* ─── Enterprise Top Bar ─── */
.erp-top-header {
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius);
  padding: 0.85rem 1.15rem;
  margin-bottom: 0.85rem;
  box-shadow: var(--erp-shadow-sm);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  width: 100%;
}

.erp-breadcrumb {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--erp-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 2px;
}

.erp-page-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--erp-text-main);
  margin: 0;
  letter-spacing: -0.3px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.erp-filter-form {
  display: flex;
  align-items: center;
  gap: 5px;
  background: #f8fafc;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius-sm);
  padding: 0.25rem 0.5rem;
}

.erp-filter-input {
  border: none;
  background: transparent;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--erp-text-main);
  outline: none;
  padding: 2px 4px;
}

/* ─── Enterprise Operational Alerts ─── */
.erp-alert-bar {
  border-radius: var(--erp-radius-sm);
  padding: 0.65rem 0.95rem;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  border-left: 4px solid;
  font-size: 0.8rem;
  width: 100%;
}

.erp-alert-bar.danger {
  background: #fff1f2;
  border-color: var(--erp-rose);
  color: #9f1239;
}

.erp-alert-bar.warning {
  background: #fffbeb;
  border-color: var(--erp-amber);
  color: #92400e;
}

.erp-alert-bar.info {
  background: #eff6ff;
  border-color: var(--erp-blue);
  color: #1e40af;
}

.erp-alert-btn {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.3rem 0.75rem;
  border-radius: 4px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.15s ease;
  white-space: nowrap;
}

/* ─── Quick Operations Strip ─── */
.erp-quick-nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.45rem;
  margin-bottom: 1rem;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  width: 100%;
}
.erp-quick-nav::-webkit-scrollbar {
  display: none;
}

.erp-nav-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius-sm);
  padding: 0.38rem 0.8rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #334155;
  text-decoration: none;
  white-space: nowrap;
  box-shadow: var(--erp-shadow-sm);
  transition: all 0.15s ease;
  flex-shrink: 0;
}

.erp-nav-pill:hover {
  background: #f1f5f9;
  border-color: var(--erp-border-hover);
  color: #0f172a;
}

.erp-nav-pill.primary {
  background: #0f766e;
  border-color: #0f766e;
  color: #ffffff;
}
.erp-nav-pill.primary:hover {
  background: #0d9488;
  color: #ffffff;
}

/* ─── Suite Container (Clean Grouping) ─── */
.erp-section-container {
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius);
  padding: 1.1rem 1.15rem;
  margin-bottom: 1rem;
  box-shadow: var(--erp-shadow-card);
  width: 100%;
}

.erp-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.9rem;
  padding-bottom: 0.6rem;
  border-bottom: 1px solid #f1f5f9;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.erp-section-title {
  font-size: 0.86rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: var(--erp-text-main);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.erp-section-tag {
  font-size: 0.66rem;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 4px;
  background: #f1f5f9;
  color: var(--erp-text-muted);
}

/* ─── Professional Clickable KPI Tiles ─── */
.erp-kpi-tile {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius-sm);
  padding: 0.9rem 1rem;
  height: 100%;
  text-decoration: none !important;
  color: inherit !important;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.erp-kpi-tile::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 3.5px;
  background: #cbd5e1;
  transition: width 0.15s ease;
}

.erp-kpi-tile:hover {
  border-color: var(--erp-border-hover);
  box-shadow: var(--erp-shadow-hover);
  transform: translateY(-2px);
}

.erp-kpi-tile:hover::before {
  width: 5px;
}

.kpi-accent-teal::before   { background: var(--erp-teal); }
.kpi-accent-emerald::before{ background: var(--erp-emerald); }
.kpi-accent-amber::before { background: var(--erp-amber); }
.kpi-accent-blue::before  { background: var(--erp-blue); }
.kpi-accent-indigo::before{ background: var(--erp-indigo); }
.kpi-accent-rose::before  { background: var(--erp-rose); }
.kpi-accent-slate::before { background: var(--erp-text-muted); }

.kpi-top-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.4rem;
}

.kpi-label-text {
  font-size: 0.68rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: var(--erp-text-muted);
}

.kpi-icon-box {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  flex-shrink: 0;
}

.kpi-amount-val {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--erp-text-main);
  line-height: 1.2;
  margin-bottom: 0.35rem;
  letter-spacing: -0.3px;
}

.kpi-sub-detail {
  font-size: 0.72rem;
  font-weight: 500;
  color: var(--erp-text-muted);
  display: flex;
  align-items: center;
  gap: 4px;
}

.kpi-arrow-link {
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--erp-teal);
  margin-top: 0.65rem;
  padding-top: 0.45rem;
  border-top: 1px dashed #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.erp-kpi-tile:hover .kpi-arrow-link {
  color: #0f172a;
}

/* ─── Compact Surface Card for Charts & Tables ─── */
.erp-card-surface {
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius);
  padding: 1.1rem;
  height: 100%;
  box-shadow: var(--erp-shadow-card);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.erp-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.85rem;
  padding-bottom: 0.55rem;
  border-bottom: 1px solid #f1f5f9;
  width: 100%;
}

.erp-card-heading {
  font-size: 0.84rem;
  font-weight: 700;
  color: var(--erp-text-main);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* ─── Enterprise Data Tables ─── */
.erp-table {
  width: 100%;
  font-size: 0.78rem;
  border-collapse: separate;
  border-spacing: 0;
}

.erp-table thead th {
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.68rem;
  letter-spacing: 0.4px;
  padding: 0.55rem 0.65rem;
  border-bottom: 1px solid var(--erp-border);
  white-space: nowrap;
}

.erp-table tbody td {
  padding: 0.55rem 0.65rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.erp-table tbody tr:hover {
  background: #f8fafc;
}

/* ─── Mobile Responsiveness Enhancements ─── */
@media (max-width: 768px) {
  .erp-dashboard {
    padding: 0.4rem 0.2rem 2rem 0.2rem;
  }
  .erp-top-header {
    padding: 0.75rem;
    gap: 0.5rem;
  }
  .erp-breadcrumb {
    font-size: 0.68rem;
  }
  .erp-page-title {
    font-size: 1rem;
    gap: 5px;
    line-height: 1.35;
  }
  .erp-filter-form {
    width: 100% !important;
    justify-content: space-between;
    padding: 0.35rem 0.5rem;
  }
  .erp-filter-input {
    width: 38% !important;
    min-width: 0 !important;
    font-size: 0.72rem !important;
    padding: 1px 2px;
  }
  .erp-filter-form button {
    padding: 2px 8px !important;
    font-size: 0.72rem !important;
  }
  .erp-top-header a.btn {
    width: 100% !important;
    text-align: center;
    justify-content: center;
  }
  .erp-alert-bar {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
    padding: 0.6rem 0.75rem;
  }
  .erp-alert-btn {
    width: 100%;
    justify-content: center;
  }
  .erp-section-container {
    padding: 0.85rem 0.75rem;
  }
  .erp-section-title {
    font-size: 0.82rem;
  }
  .erp-kpi-tile {
    padding: 0.75rem 0.85rem;
  }
  .kpi-amount-val {
    font-size: 1.15rem;
  }
  .erp-card-surface {
    padding: 0.85rem;
  }
  .erp-card-heading {
    font-size: 0.82rem;
    width: 100%;
    line-height: 1.3;
  }
}
</style>

<div class="erp-dashboard">
  <div class="container-fluid px-1 px-md-3">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. TOP EXECUTIVE HEADER & CONTROLS                             --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-top-header">
      <div class="w-100 w-md-auto mb-1 mb-md-0">
        <div class="erp-breadcrumb">Memon Nimko Distributor Portal &bull; Route Operations</div>
        <h1 class="erp-page-title">
          <span>Distributor Operations Hub</span>
          <div class="d-inline-flex align-items-center gap-1 flex-wrap mt-1 mt-sm-0">
            <span class="badge rounded-pill fw-semibold" style="font-size:0.65rem; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">
              <i class="fas fa-circle me-1" style="font-size:0.45rem;"></i> Active Route
            </span>
            <span class="badge rounded-pill fw-semibold bg-light text-dark border" style="font-size:0.68rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              <i class="fas fa-truck text-teal me-1" style="color: #0f766e;"></i> {{ $distributor->name }} ({{ $distributor->code }})
            </span>
          </div>
        </h1>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto">
        {{-- Date Range Filter --}}
        <form method="GET" action="{{ route('distributor.dashboard') }}" class="erp-filter-form mb-0">
          @if(request('distributor_id'))
            <input type="hidden" name="distributor_id" value="{{ request('distributor_id') }}">
          @endif
          <i class="fas fa-calendar-day text-secondary" style="font-size:0.75rem;"></i>
          <input type="date" name="start_date" value="{{ $startDate }}" class="erp-filter-input" title="Start Date">
          <span class="text-muted" style="font-size:0.75rem;">—</span>
          <input type="date" name="end_date" value="{{ $endDate }}" class="erp-filter-input" title="End Date">
          <button type="submit" class="btn btn-sm btn-dark px-2.5 py-0.5 fw-bold" style="font-size:0.72rem; border-radius:4px;">
            Filter
          </button>
          @if(request('start_date') || request('end_date'))
            <a href="{{ route('distributor.dashboard') }}" class="btn btn-sm btn-outline-secondary px-2 py-0.5" style="font-size:0.72rem; border-radius:4px;" title="Clear Filter">
              <i class="fas fa-times"></i>
            </a>
          @endif
        </form>

        <a href="{{ route('distributor.company_ledger.index') }}" class="btn btn-sm btn-outline-dark px-3 py-1 fw-bold" style="font-size:0.76rem; border-radius:6px;">
          <i class="fas fa-file-invoice me-1"></i> Account Statement
        </a>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. OPERATIONAL ACTION NOTIFICATIONS                            --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($payableBalanceToCompany > 0)
    <div class="erp-alert-bar danger">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-file-invoice-dollar text-danger"></i>
        <span><strong>Outstanding Company Balance:</strong> You have an open payable balance of <strong>Rs {{ number_format($payableBalanceToCompany, 2) }}</strong> due to Head Office account.</span>
      </div>
      <a href="{{ route('distributor.payments.index') }}" class="erp-alert-btn bg-danger text-white">
        Deposit Payment <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    @if($pendingDemandsCount > 0)
    <div class="erp-alert-bar warning">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-truck-loading text-warning"></i>
        <span><strong>{{ $pendingDemandsCount }} Stock Demand Requisition(s)</strong> submitted to Head Office awaiting approval and dispatch.</span>
      </div>
      <a href="{{ route('distributor.orders.index') }}" class="erp-alert-btn bg-warning text-dark">
        Track Orders <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    @if($customersWithDueCount > 0)
    <div class="erp-alert-bar info">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-store text-primary"></i>
        <span><strong>{{ $customersWithDueCount }} Route Retailer(s)</strong> have outstanding trade credit balances totaling <strong>Rs {{ number_format($totalMarketDue, 2) }}</strong>.</span>
      </div>
      <a href="{{ route('distributor.customers.index') }}" class="erp-alert-btn bg-primary text-white">
        Collect Recovery <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. QUICK OPERATIONS STRIP                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-quick-nav">
      <a href="{{ route('distributor.sales.create') }}" class="erp-nav-pill primary">
        <i class="fas fa-cart-plus"></i> + New Retail Sale
      </a>
      <a href="{{ route('distributor.orders.create') }}" class="erp-nav-pill">
        <i class="fas fa-cart-arrow-down text-teal" style="color: #0f766e;"></i> Demand Stock
      </a>
      <a href="{{ route('distributor.customers.index') }}" class="erp-nav-pill">
        <i class="fas fa-hand-holding-usd text-success"></i> Customer Recovery
      </a>
      <a href="{{ route('distributor.payments.index') }}" class="erp-nav-pill">
        <i class="fas fa-money-bill-wave text-warning"></i> Deposit Company Payment
      </a>
      <a href="{{ route('distributor.company_ledger.index') }}" class="erp-nav-pill">
        <i class="fas fa-file-invoice-dollar text-primary"></i> Company Ledger
      </a>
      <a href="{{ route('distributor.stock.index') }}" class="erp-nav-pill">
        <i class="fas fa-boxes-stacked text-secondary"></i> Stock
      </a>
      <a href="{{ route('distributor.customers.index') }}" class="erp-nav-pill">
        <i class="fas fa-store text-info"></i> Route Retailers
      </a>
      <a href="{{ route('distributor.sales.index') }}" class="erp-nav-pill">
        <i class="fas fa-receipt text-danger"></i> Sales Register
      </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 4. SUITE 1: HEAD OFFICE & COMPANY ACCOUNT                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <h3 class="erp-section-title">
          <i class="fas fa-building-columns text-teal" style="color: #0f766e;"></i>
          <span>Head Office Account & Supply Settlement</span>
        </h3>
        <span class="erp-section-tag">Direct Company Ledger</span>
      </div>

      <div class="row g-2.5">
        {{-- Total Goods Received --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.company_ledger.index') }}" class="erp-kpi-tile kpi-accent-teal">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Goods Received from Company</span>
              <div class="kpi-icon-box" style="background:#f0fdf4; color:#0f766e;">
                <i class="fas fa-boxes-stacked"></i>
              </div>
            </div>
            <div class="kpi-amount-val">Rs {{ number_format($totalGoodsFromCompany, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Billed factory shipments</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Supply Ledger</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Payments Deposited --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.payments.index') }}" class="erp-kpi-tile kpi-accent-emerald">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Payments Deposited to Company</span>
              <div class="kpi-icon-box" style="background:#ecfdf5; color:#10b981;">
                <i class="fas fa-hand-holding-usd"></i>
              </div>
            </div>
            <div class="kpi-amount-val text-success">Rs {{ number_format($totalPaidToCompany, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Verified bank & cash deposits</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Deposit Vouchers</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Net Outstanding Payable --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.company_ledger.index') }}" class="erp-kpi-tile kpi-accent-rose" style="background: #fffafa;">
            <div class="kpi-top-meta">
              <span class="kpi-label-text text-danger">Company Outstanding Payable</span>
              <div class="kpi-icon-box" style="background:#fff1f2; color:#e11d48;">
                <i class="fas fa-scale-unbalanced"></i>
              </div>
            </div>
            <div class="kpi-amount-val text-danger">Rs {{ number_format($payableBalanceToCompany, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Current balance owed to Head Office</span>
            </div>
            <div class="kpi-arrow-link text-danger">
              <span>Settle Liability</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Demand Requisitions --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.orders.index') }}" class="erp-kpi-tile kpi-accent-indigo">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Stock Demand Requisitions</span>
              <div class="kpi-icon-box" style="background:#eef2ff; color:#4f46e5;">
                <i class="fas fa-clipboard-check"></i>
              </div>
            </div>
            <div class="kpi-amount-val">{{ $pendingDemandsCount }} <span class="fs-6 fw-normal text-muted">Pending</span></div>
            <div class="kpi-sub-detail">
              <span>{{ $approvedDemandsCount }} Approved &bull; {{ $totalDemandsCount }} Total Placed</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Track Demands</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 5. SUITE 2: ROUTE TRADE RECEIVABLES & FIELD CASH RECOVERIES   --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <h3 class="erp-section-title">
          <i class="fas fa-store text-amber" style="color: #d97706;"></i>
          <span>Route Trade Receivables & Field Cash Collections</span>
        </h3>
        <span class="erp-section-tag">Market Operations</span>
      </div>

      <div class="row g-2.5">
        {{-- Retail Trade Receivables --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.customers.index') }}" class="erp-kpi-tile kpi-accent-amber">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Retail Trade Receivables (Due)</span>
              <div class="kpi-icon-box" style="background:#fffbeb; color:#d97706;">
                <i class="fas fa-clock-rotate-left"></i>
              </div>
            </div>
            <div class="kpi-amount-val text-warning">Rs {{ number_format($totalMarketDue, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Pending from {{ $customersWithDueCount }} retail stores</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Store Receivables</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Customer Recoveries Collected --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.customers.index') }}" class="erp-kpi-tile kpi-accent-emerald">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Customer Recoveries Collected</span>
              <div class="kpi-icon-box" style="background:#ecfdf5; color:#10b981;">
                <i class="fas fa-circle-dollar-to-slot"></i>
              </div>
            </div>
            <div class="kpi-amount-val text-success">Rs {{ number_format($totalCustomerRecovery, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Cash recovered from market route</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Collection Ledger</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Registered Route Retailers --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.customers.index') }}" class="erp-kpi-tile kpi-accent-blue">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Registered Route Retailers</span>
              <div class="kpi-icon-box" style="background:#eff6ff; color:#2563eb;">
                <i class="fas fa-store"></i>
              </div>
            </div>
            <div class="kpi-amount-val">{{ $totalCustomers }} <span class="fs-6 fw-normal text-muted">Shops</span></div>
            <div class="kpi-sub-detail">
              <span>Active booking customer accounts</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Retailer Directory</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Stock in Custody Valuation --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.stock.index') }}" class="erp-kpi-tile kpi-accent-slate">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Stock in Custody</span>
              <div class="kpi-icon-box" style="background:#f1f5f9; color:#475569;">
                <i class="fas fa-boxes-stacked"></i>
              </div>
            </div>
            <div class="kpi-amount-val">{{ number_format($totalStockUnits, 0) }} <span class="fs-6 fw-normal text-muted">Pcs</span></div>
            <div class="kpi-sub-detail">
              <span>Valuation: Rs {{ number_format($stockValuation, 0) }}</span>
            </div>
            <div class="kpi-arrow-link">
              <span>View Stock</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 6. SUITE 3: SECONDARY SALES & RETAIL BILLING PERFORMANCE       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <h3 class="erp-section-title">
          <i class="fas fa-chart-line text-blue" style="color: #2563eb;"></i>
          <span>Secondary Sales & Retail Billing Performance</span>
        </h3>
        <span class="erp-section-tag">Sales Register</span>
      </div>

      <div class="row g-2.5">
        {{-- Today's Sales --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.sales.index') }}" class="erp-kpi-tile kpi-accent-blue">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Today's Secondary Sales</span>
              <div class="kpi-icon-box" style="background:#eff6ff; color:#2563eb;">
                <i class="fas fa-receipt"></i>
              </div>
            </div>
            <div class="kpi-amount-val">Rs {{ number_format($todaySales, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>Billed to retailers today</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Today's Invoices</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Month's Sales --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.sales.index') }}" class="erp-kpi-tile kpi-accent-indigo">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Current Month Sales</span>
              <div class="kpi-icon-box" style="background:#eef2ff; color:#4f46e5;">
                <i class="fas fa-calendar-check"></i>
              </div>
            </div>
            <div class="kpi-amount-val">Rs {{ number_format($monthSales, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>{{ now()->format('F Y') }} distribution</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Monthly Ledger</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Filtered Period Turnover --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('distributor.sales.index') }}" class="erp-kpi-tile kpi-accent-slate">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Selected Period Sales</span>
              <div class="kpi-icon-box" style="background:#f1f5f9; color:#475569;">
                <i class="fas fa-file-invoice"></i>
              </div>
            </div>
            <div class="kpi-amount-val">Rs {{ number_format($periodSales, 2) }}</div>
            <div class="kpi-sub-detail">
              <span>{{ $periodSalesCount }} Invoices &bull; Avg: Rs {{ number_format($averageSaleValue, 0) }}</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Sales Register</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>

        {{-- Cash Recovery Efficiency --}}
        <div class="col-12 col-sm-6 col-xl-3">
          @php
            $recoveryEfficiency = $totalSalesSum > 0 ? round(($totalCustomerRecovery / $totalSalesSum) * 100, 1) : 0;
          @endphp
          <a href="{{ route('distributor.customers.index') }}" class="erp-kpi-tile kpi-accent-teal">
            <div class="kpi-top-meta">
              <span class="kpi-label-text">Cash Recovery vs Sales Ratio</span>
              <div class="kpi-icon-box" style="background:#f0fdf4; color:#0f766e;">
                <i class="fas fa-percent"></i>
              </div>
            </div>
            <div class="kpi-amount-val text-teal" style="color: #0f766e;">{{ $recoveryEfficiency }}%</div>
            <div class="kpi-sub-detail">
              <span>Collection efficiency status</span>
            </div>
            <div class="kpi-arrow-link">
              <span>Collection Audit</span>
              <i class="fas fa-arrow-right"></i>
            </div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 7. VISUAL ANALYTICS: APEXCHARTS                                --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3 mb-md-4">
      {{-- Daily Sales & Recoveries Trend Area Spline --}}
      <div class="col-12 col-lg-8">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-chart-area text-blue" style="color: #2563eb;"></i>
              <span>Daily Sales & Recoveries Trend</span>
            </h4>
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.68rem;">
                ● Secondary Sales
              </span>
              <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.68rem;">
                ● Cash Recoveries
              </span>
            </div>
          </div>
          <div id="chartDistributorTrend" style="min-height: 260px;"></div>
        </div>
      </div>

      {{-- Working Capital Breakdown Donut --}}
      <div class="col-12 col-lg-4">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-chart-pie text-teal" style="color: #0f766e;"></i>
              <span>Working Capital & Settlement</span>
            </h4>
            <span class="text-muted" style="font-size:0.7rem;">Live Ledger</span>
          </div>
          <div id="chartWorkingCapital" style="min-height: 200px;"></div>
          <div class="pt-2 border-top mt-2">
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size:0.75rem;">
              <span class="text-muted"><i class="fas fa-circle text-success me-1" style="font-size: 0.6rem;"></i> Recoveries Collected:</span>
              <strong class="text-dark">Rs {{ number_format($totalCustomerRecovery, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size:0.75rem;">
              <span class="text-muted"><i class="fas fa-circle text-warning me-1" style="font-size: 0.6rem;"></i> Market Trade Due:</span>
              <strong class="text-warning">Rs {{ number_format($totalMarketDue, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center py-1" style="font-size:0.75rem;">
              <span class="text-muted"><i class="fas fa-circle text-danger me-1" style="font-size: 0.6rem;"></i> Company Net Due:</span>
              <strong class="text-danger">Rs {{ number_format($payableBalanceToCompany, 2) }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 8. ENTERPRISE DATA TABLES: INVOICES, ORDERS & CUSTOMERS        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-3">
      {{-- Left Column: Recent Sales & Recoveries --}}
      <div class="col-12 col-lg-7">
        {{-- Recent Secondary Sales Invoices --}}
        <div class="erp-card-surface mb-3">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-receipt text-blue" style="color: #2563eb;"></i>
              <span>Recent Secondary Sales Invoices</span>
            </h4>
            <a href="{{ route('distributor.sales.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.74rem;">
              View Sales Register <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <div class="table-responsive">
            <table class="erp-table">
              <thead>
                <tr>
                  <th class="ps-2">Invoice #</th>
                  <th>Retail Store</th>
                  <th>Date</th>
                  <th class="text-end">Amount</th>
                  <th class="text-center">Status</th>
                  <th class="text-center pe-2">Receipt</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentSales as $sale)
                <tr>
                  <td class="ps-2">
                    <span class="fw-bold font-monospace text-dark">{{ $sale->invoice_no }}</span>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;">
                      {{ $sale->customer->shop_name ?? 'Retail Store' }}
                    </div>
                    <span class="text-muted" style="font-size: 0.68rem;">{{ $sale->customer->area ?? 'Market' }}</span>
                  </td>
                  <td>
                    <span class="text-muted" style="font-size: 0.74rem;">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    Rs {{ number_format($sale->grand_total, 2) }}
                  </td>
                  <td class="text-center">
                    @if($sale->due_amount <= 0)
                      <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">Paid</span>
                    @elseif($sale->paid_amount > 0)
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">Partial</span>
                    @else
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;">Due</span>
                    @endif
                  </td>
                  <td class="text-center pe-2">
                    <a href="{{ route('distributor.sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5" title="Print Bill">
                      <i class="fas fa-print"></i>
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted small">No secondary sales generated yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Recent Customer Recovery Collections --}}
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-circle-dollar-to-slot text-success"></i>
              <span>Recent Customer Recovery Collections</span>
            </h4>
            <a href="{{ route('distributor.customers.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.74rem;">
              View Retailers <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <div class="table-responsive">
            <table class="erp-table">
              <thead>
                <tr>
                  <th class="ps-2">Retail Store</th>
                  <th>Date</th>
                  <th>Receipt / Ref</th>
                  <th class="text-end pe-2">Recovered Amount</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentRecoveries as $rec)
                <tr>
                  <td class="ps-2">
                    <div class="fw-bold text-dark">{{ $rec->customer->shop_name ?? 'Retail Store' }}</div>
                    <span class="text-muted" style="font-size: 0.68rem;">{{ $rec->customer->owner_name ?? '' }} &bull; {{ $rec->customer->area ?? '' }}</span>
                  </td>
                  <td>
                    <span class="text-muted" style="font-size: 0.74rem;">{{ \Carbon\Carbon::parse($rec->date)->format('d M, Y') }}</span>
                  </td>
                  <td>
                    <span class="font-monospace text-muted" style="font-size: 0.72rem;">{{ $rec->reference_no ?: 'RCP' }}</span>
                  </td>
                  <td class="text-end pe-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.74rem;">
                      + Rs {{ number_format($rec->credit, 2) }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted small">No recovery collections recorded yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Right Column: Demand Orders & Priority Retailers --}}
      <div class="col-12 col-lg-5">
        {{-- Demand Requisitions to Head Office --}}
        <div class="erp-card-surface mb-3">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-cart-flatbed text-teal" style="color: #0f766e;"></i>
              <span>Demand Requisitions to Head Office</span>
            </h4>
            <a href="{{ route('distributor.orders.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.74rem;">
              All Demands <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <div class="table-responsive">
            <table class="erp-table">
              <thead>
                <tr>
                  <th class="ps-2">Order #</th>
                  <th>Date</th>
                  <th class="text-end">Amount</th>
                  <th class="text-center pe-2">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentOrders as $order)
                <tr>
                  <td class="ps-2">
                    <a href="{{ route('distributor.orders.show', $order->id) }}" class="fw-bold font-monospace text-decoration-none" style="color: #0f766e;">
                      {{ $order->order_no }}
                    </a>
                  </td>
                  <td>
                    <span class="text-muted" style="font-size: 0.74rem;">{{ \Carbon\Carbon::parse($order->order_date)->format('d M') }}</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    Rs {{ number_format($order->total_amount, 2) }}
                  </td>
                  <td class="text-center pe-2">
                    @if($order->status === 'approved')
                      <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">Approved</span>
                    @elseif($order->status === 'dispatched' || $order->status === 'delivered')
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">Delivered</span>
                    @elseif($order->status === 'rejected')
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;">Rejected</span>
                    @else
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">Pending</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted small">
                    <div>No requisitions placed yet.</div>
                    <a href="{{ route('distributor.orders.create') }}" class="btn btn-sm btn-outline-dark rounded-pill mt-2" style="font-size:0.72rem;">
                      + Place Demand Requisition
                    </a>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Priority Route Retailers with Outstanding Receivables --}}
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h4 class="erp-card-heading">
              <i class="fas fa-store-slash text-amber" style="color: #d97706;"></i>
              <span>Priority Retailers with Overdue Credit</span>
            </h4>
            <a href="{{ route('distributor.customers.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.74rem;">
              Credit Ledger <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <div class="table-responsive">
            <table class="erp-table">
              <thead>
                <tr>
                  <th class="ps-2">Retail Store</th>
                  <th>Contact</th>
                  <th class="text-end pe-2">Overdue Balance</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topRetailers as $ret)
                <tr>
                  <td class="ps-2">
                    <div class="fw-bold text-dark">{{ $ret->shop_name }}</div>
                    <span class="text-muted" style="font-size: 0.68rem;">{{ $ret->owner_name ?? 'Owner' }} &bull; {{ $ret->area ?? '' }}</span>
                  </td>
                  <td>
                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $ret->phone ?: '—' }}</span>
                  </td>
                  <td class="text-end pe-2">
                    <span class="fw-bold {{ $ret->current_balance > 0 ? 'text-warning' : 'text-success' }}">
                      Rs {{ number_format($ret->current_balance, 2) }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted small">All route retailers are clear on trade credit!</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Daily Secondary Sales & Market Recoveries Trend Area Spline
    var salesTrendLabels = {!! json_encode($salesTrendLabels) !!};
    var salesTrendData = {!! json_encode($salesTrendData) !!};
    var recoveryTrendData = {!! json_encode($recoveryTrendData) !!};

    var optionsSalesTrend = {
        series: [
            {
                name: 'Secondary Sales (Rs)',
                data: salesTrendData
            },
            {
                name: 'Cash Recoveries (Rs)',
                data: recoveryTrendData
            }
        ],
        chart: {
            type: 'area',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#2563eb', '#10b981'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: [2.5, 2.5]
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        xaxis: {
            categories: salesTrendLabels,
            labels: {
                style: { colors: '#64748b', fontSize: '10px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '10px' },
                formatter: function (val) {
                    return val >= 1000 ? (val/1000).toFixed(0) + 'k' : val;
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rs ' + Number(val).toLocaleString();
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '11px',
            markers: { radius: 12 }
        },
        responsive: [
            {
                breakpoint: 576,
                options: {
                    chart: { height: 230 },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            style: { fontSize: '9px' }
                        },
                        tickAmount: 7
                    },
                    yaxis: {
                        labels: {
                            style: { fontSize: '9px' },
                            formatter: function (val) {
                                return val >= 1000 ? (val/1000).toFixed(0) + 'k' : val;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '10px'
                    }
                }
            }
        ]
    };
    var chartSales = new ApexCharts(document.querySelector("#chartDistributorTrend"), optionsSalesTrend);
    chartSales.render();

    // 2. Working Capital & Settlement Breakdown Donut Chart
    var recoveryTotal = {{ (float)$totalCustomerRecovery }};
    var marketDueTotal = {{ (float)$totalMarketDue }};
    var companyPayableTotal = {{ (float)$payableBalanceToCompany }};

    var donutSeries = [recoveryTotal, marketDueTotal, companyPayableTotal];
    var donutLabels = ['Recoveries Collected', 'Market Trade Due', 'Company Net Due'];

    if (recoveryTotal === 0 && marketDueTotal === 0 && companyPayableTotal === 0) {
        donutSeries = [1];
        donutLabels = ['No Balance Recorded'];
    }

    var optionsWorkingCapital = {
        series: donutSeries,
        labels: donutLabels,
        chart: {
            type: 'donut',
            height: 200,
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#10b981', '#d97706', '#e11d48'],
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '11px', color: '#64748b' },
                        value: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 700,
                            formatter: function (val) {
                                return 'Rs ' + Number(val).toLocaleString();
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Capital',
                            fontSize: '10px',
                            color: '#94a3b8',
                            formatter: function (w) {
                                var sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return 'Rs ' + Number(sum).toLocaleString();
                            }
                        }
                    }
                }
            }
        },
        legend: { show: false },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rs ' + Number(val).toLocaleString();
                }
            }
        },
        responsive: [
            {
                breakpoint: 576,
                options: {
                    chart: { height: 180 }
                }
            }
        ]
    };
    var chartDonut = new ApexCharts(document.querySelector("#chartWorkingCapital"), optionsWorkingCapital);
    chartDonut.render();
});
</script>
@endsection
