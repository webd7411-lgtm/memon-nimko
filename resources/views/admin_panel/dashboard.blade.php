@extends('admin_panel.layout.app')

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
  padding: 0.75rem 0.5rem 3rem 0.5rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  color: var(--erp-text-main);
  -webkit-font-smoothing: antialiased;
}

.erp-dashboard * {
  box-sizing: border-box;
}

/* ─── Enterprise Top Bar ─── */
.erp-top-header {
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius);
  padding: 0.85rem 1.25rem;
  margin-bottom: 1rem;
  box-shadow: var(--erp-shadow-sm);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.erp-breadcrumb {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--erp-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 2px;
}

.erp-page-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--erp-text-main);
  margin: 0;
  letter-spacing: -0.3px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.erp-filter-form {
  display: flex;
  align-items: center;
  gap: 6px;
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
  padding: 0.65rem 1rem;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  border-left: 4px solid;
  font-size: 0.82rem;
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
  padding: 0.3rem 0.8rem;
  border-radius: 4px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.15s ease;
}

/* ─── Quick Operations Strip ─── */
.erp-quick-nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.4rem;
  margin-bottom: 1rem;
  scrollbar-width: thin;
}

.erp-nav-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ffffff;
  border: 1px solid var(--erp-border);
  border-radius: var(--erp-radius-sm);
  padding: 0.4rem 0.85rem;
  font-size: 0.76rem;
  font-weight: 600;
  color: #334155;
  text-decoration: none;
  white-space: nowrap;
  box-shadow: var(--erp-shadow-sm);
  transition: all 0.15s ease;
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
  padding: 1.15rem 1.25rem;
  margin-bottom: 1.15rem;
  box-shadow: var(--erp-shadow-card);
}

.erp-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.95rem;
  padding-bottom: 0.65rem;
  border-bottom: 1px solid #f1f5f9;
}

.erp-section-title {
  font-size: 0.88rem;
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
  font-size: 0.68rem;
  font-weight: 600;
  padding: 2px 8px;
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
  padding: 0.95rem 1.05rem;
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
  margin-bottom: 0.45rem;
}

.kpi-label-text {
  font-size: 0.7rem;
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
  font-size: 0.85rem;
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
  padding: 1.15rem;
  height: 100%;
  box-shadow: var(--erp-shadow-card);
  display: flex;
  flex-direction: column;
}

.erp-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.85rem;
  padding-bottom: 0.55rem;
  border-bottom: 1px solid #f1f5f9;
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
}

.erp-table tbody td {
  padding: 0.55rem 0.65rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.erp-table tbody tr:hover {
  background: #f8fafc;
}

.erp-rank-tag {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.68rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.erp-rank-tag.top {
  background: #ecfdf5;
  color: #059669;
}

@media (max-width: 768px) {
  .erp-page-title { font-size: 1.1rem; }
  .kpi-amount-val { font-size: 1.15rem; }
  .erp-section-container { padding: 0.85rem; }
}
</style>

<div class="erp-dashboard">
  <div class="container-fluid px-2 px-md-3">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. TOP EXECUTIVE HEADER & DATE CONTROLS                         --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-top-header">
      <div>
        <div class="erp-breadcrumb">Memon Nimko ERP &bull; Executive Operations</div>
        <h1 class="erp-page-title">
          <span>Enterprise Management Dashboard</span>
          <span class="badge rounded-pill fw-semibold" style="font-size:0.68rem; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">
            <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> System Live
          </span>
        </h1>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Date Filter --}}
        <form method="GET" action="{{ url('/home') }}" class="erp-filter-form mb-0">
          <i class="fas fa-calendar-day text-secondary" style="font-size:0.75rem;"></i>
          <input type="date" name="start_date" value="{{ $startDate }}" class="erp-filter-input" style="width:125px;" title="Start Date">
          <span class="text-muted" style="font-size:0.75rem;">—</span>
          <input type="date" name="end_date" value="{{ $endDate }}" class="erp-filter-input" style="width:125px;" title="End Date">
          <button type="submit" class="btn btn-sm btn-dark px-2.5 py-0.5 fw-bold" style="font-size:0.72rem; border-radius:4px;">
            Filter
          </button>
          @if(request('start_date') || request('end_date'))
            <a href="{{ url('/home') }}" class="btn btn-sm btn-outline-secondary px-2 py-0.5" style="font-size:0.72rem; border-radius:4px;" title="Clear Filter">
              <i class="fas fa-times"></i>
            </a>
          @endif
        </form>

        <a href="{{ route('System.Reports') }}" class="btn btn-sm btn-outline-dark px-3 py-1 fw-bold" style="font-size:0.76rem; border-radius:6px;">
          <i class="fas fa-file-pdf me-1"></i> Reports
        </a>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. OPERATIONAL ACTION NOTIFICATIONS                            --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($pendingDistPaymentsCount > 0)
    <div class="erp-alert-bar danger">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-money-check-alt text-danger"></i>
        <span><strong>{{ $pendingDistPaymentsCount }} Distributor Payment Verification(s)</strong> awaiting approval (Total: Rs {{ number_format($pendingDistPaymentsTotal, 0) }}).</span>
      </div>
      <a href="{{ route('admin.distributors.payments', ['status' => 'pending']) }}" class="erp-alert-btn bg-danger text-white">
        Verify & Approve <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    @if($pendingDistOrdersCount > 0)
    <div class="erp-alert-bar warning">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-truck-loading text-warning"></i>
        <span><strong>{{ $pendingDistOrdersCount }} Distributor Demand Order(s)</strong> awaiting stock dispatch and delivery challan.</span>
      </div>
      <a href="{{ route('admin.distributors.orders') }}" class="erp-alert-btn bg-warning text-dark">
        Process Orders <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    @if($pendingTransfersCount > 0)
    <div class="erp-alert-bar info">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-exchange-alt text-primary"></i>
        <span><strong>{{ $pendingTransfersCount }} Inter-Branch Stock Transfer(s)</strong> in transit awaiting receiving branch acceptance.</span>
      </div>
      <a href="{{ route('stock_transfers.index') }}" class="erp-alert-btn bg-primary text-white">
        Review Transfers <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    @endif

    @if(count($lowStockItems) > 0)
    <div class="erp-alert-bar warning">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-exclamation-triangle text-warning"></i>
        <span><strong>{{ count($lowStockItems) }} Item(s)</strong> reached critical low stock threshold. Reorder required.</span>
      </div>
      <a href="#erp-low-stock-table" class="erp-alert-btn bg-dark text-white">
        View Reorder Priority <i class="fas fa-arrow-down"></i>
      </a>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. QUICK OPERATIONS LAUNCHPAD                                  --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-quick-nav">
      <a href="{{ route('sale.screen') }}" class="erp-nav-pill primary">
        <i class="fas fa-cash-register"></i> POS Screen
      </a>
      <a href="{{ route('sale.index') }}" class="erp-nav-pill">
        <i class="fas fa-receipt text-teal"></i> Sales Register
      </a>
      <a href="{{ route('add_purchase') }}" class="erp-nav-pill">
        <i class="fas fa-shopping-cart text-primary"></i> Purchase Bill
      </a>
      <a href="{{ route('production.create') }}" class="erp-nav-pill">
        <i class="fas fa-industry text-warning"></i> Factory Production
      </a>
      <a href="{{ route('admin.distributors.orders') }}" class="erp-nav-pill">
        <i class="fas fa-truck-loading text-danger"></i> Distributor Orders
      </a>
      <a href="{{ route('admin.distributors.index') }}" class="erp-nav-pill">
        <i class="fas fa-users-cog text-info"></i> Distributors
      </a>
      <a href="{{ route('stock_transfers.index') }}" class="erp-nav-pill">
        <i class="fas fa-exchange-alt text-primary"></i> Stock Transfers
      </a>
      <a href="{{ route('recepit-vochers') }}" class="erp-nav-pill">
        <i class="fas fa-hand-holding-usd text-success"></i> Customer Receipt
      </a>
      <a href="{{ route('Payment-vochers') }}" class="erp-nav-pill">
        <i class="fas fa-money-bill-wave text-secondary"></i> Vendor Payment
      </a>
      <a href="{{ route('all-expense-vochers') }}" class="erp-nav-pill">
        <i class="fas fa-file-invoice text-danger"></i> Expense Voucher
      </a>
      <a href="{{ route('cashbook') }}" class="erp-nav-pill">
        <i class="fas fa-book text-dark"></i> Cashbook
      </a>
      <a href="{{ route('view_all') }}" class="erp-nav-pill">
        <i class="fas fa-balance-scale text-primary"></i> Chart of Accounts
      </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SUITE 1: 💰 SALES, REVENUE & MARGINS (CLICKABLE TO REPORTS)    --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <div class="erp-section-title">
          <i class="fas fa-chart-line text-success"></i>
          <span>Commercial Revenue & Margin Performance</span>
        </div>
        <span class="erp-section-tag">Sales Operations</span>
      </div>

      <div class="row g-2 g-md-3">
        {{-- Card 1: Retail POS Sales --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('report.sale') }}" class="erp-kpi-tile kpi-accent-teal">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">POS & Retail Sales</span>
                <div class="kpi-icon-box" style="background:#ecfdf5; color:#0f766e;">
                  <i class="fas fa-cash-register"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($totalSales, 0) }}</div>
              <div class="kpi-sub-detail">
                <span class="text-success fw-bold"><i class="fas fa-arrow-up"></i> {{ $salesGrowth }}%</span>
                <span>&bull; {{ $salesCount }} Total Invoices</span>
              </div>
            </div>
            <div class="kpi-arrow-link">
              <span>View Sales Register</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 2: Distributor Demand Orders --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('admin.distributors.orders') }}" class="erp-kpi-tile kpi-accent-amber">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Distributor Demand Orders</span>
                <div class="kpi-icon-box" style="background:#fffbeb; color:#d97706;">
                  <i class="fas fa-truck-loading"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($allTimeDistOrdersValue > 0 ? $allTimeDistOrdersValue : $totalDistOrdersValue, 0) }}</div>
              <div class="kpi-sub-detail">
                <span>{{ $allTimeDistOrdersCount }} Total Orders</span>
                <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $pendingDistOrdersCount }} Pending</span>
              </div>
            </div>
            <div class="kpi-arrow-link" style="color:#d97706;">
              <span>Distributor Orders Portal</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 3: Gross Profit --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('report.profit_loss') }}" class="erp-kpi-tile kpi-accent-emerald">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Gross Profit</span>
                <div class="kpi-icon-box" style="background:#ecfdf5; color:#10b981;">
                  <i class="fas fa-chart-pie"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($grossProfit, 0) }}</div>
              <div class="kpi-sub-detail">
                <span>Margin: {{ $grossProfitGrowth }}% vs prev</span>
              </div>
            </div>
            <div class="kpi-arrow-link">
              <span>P&L Statement</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 4: Net Operating Profit --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('report.profit_loss') }}" class="erp-kpi-tile kpi-accent-teal">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Net Operating Profit</span>
                <div class="kpi-icon-box" style="background:#f0fdf4; color:#0f766e;">
                  <i class="fas fa-shield-alt"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($netProfit, 0) }}</div>
              <div class="kpi-sub-detail">
                <span>Net Margin (After Expenses)</span>
              </div>
            </div>
            <div class="kpi-arrow-link">
              <span>Profit & Loss Report</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SUITE 2: 🏭 FACTORY & PROCUREMENT METRICS (CLICKABLE)          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <div class="erp-section-title">
          <i class="fas fa-industry text-primary"></i>
          <span>Factory Production, Procurement & Expenses</span>
        </div>
        <span class="erp-section-tag">Manufacturing & Supply</span>
      </div>

      <div class="row g-2 g-md-3">
        {{-- Card 1: Commercial Purchases --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('report.purchase') }}" class="erp-kpi-tile kpi-accent-slate">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Commercial Purchases</span>
                <div class="kpi-icon-box" style="background:#f1f5f9; color:#475569;">
                  <i class="fas fa-shopping-cart"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($totalPurchases, 0) }}</div>
              <div class="kpi-sub-detail">Finished Goods Purchases</div>
            </div>
            <div class="kpi-arrow-link" style="color:#475569;">
              <span>Purchase Bills Report</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 2: Raw Material Purchases --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('raw-materials.index') }}" class="erp-kpi-tile kpi-accent-amber">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Raw Material Purchases</span>
                <div class="kpi-icon-box" style="background:#fffbeb; color:#d97706;">
                  <i class="fas fa-cubes"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($totalRawMaterialPurchases, 0) }}</div>
              <div class="kpi-sub-detail">Factory Ingredients & Flour</div>
            </div>
            <div class="kpi-arrow-link" style="color:#d97706;">
              <span>Raw Materials Inventory</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 3: Factory Production Batches --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('production.index') }}" class="erp-kpi-tile kpi-accent-indigo">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Production Batches</span>
                <div class="kpi-icon-box" style="background:#eef2ff; color:#4f46e5;">
                  <i class="fas fa-industry"></i>
                </div>
              </div>
              <div class="kpi-amount-val">{{ $productionCount }} Batches</div>
              <div class="kpi-sub-detail">Production Cost: Rs {{ number_format($productionTotalCost, 0) }}</div>
            </div>
            <div class="kpi-arrow-link" style="color:#4f46e5;">
              <span>Factory Production Hub</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>

        {{-- Card 4: Audited Operating Expenses --}}
        <div class="col-12 col-sm-6 col-xl-3">
          <a href="{{ route('all-expense-vochers') }}" class="erp-kpi-tile kpi-accent-rose">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Operating Expenses</span>
                <div class="kpi-icon-box" style="background:#fff1f2; color:#e11d48;">
                  <i class="fas fa-file-invoice-dollar"></i>
                </div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($totalExpenses, 0) }}</div>
              <div class="kpi-sub-detail">Audited Vouchers Posted</div>
            </div>
            <div class="kpi-arrow-link" style="color:#e11d48;">
              <span>Expense Vouchers</span>
              <i class="fas fa-chevron-right"></i>
            </div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SUITE 3: 🏦 WORKING CAPITAL, TREASURY & BALANCES (CLICKABLE)   --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="erp-section-container">
      <div class="erp-section-header">
        <div class="erp-section-title">
          <i class="fas fa-wallet text-primary"></i>
          <span>Working Capital, Treasury & Ledger Balances</span>
        </div>
        <span class="erp-section-tag">Liquidity & Debtors</span>
      </div>

      <div class="row g-2 g-md-3">
        {{-- Customer Receivables --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('report.customer.ledger') }}" class="erp-kpi-tile kpi-accent-teal">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Customer Receivables</span>
                <div class="kpi-icon-box" style="background:#ecfdf5; color:#0f766e;"><i class="fas fa-users"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($customerReceivables, 0) }}</div>
              <div class="kpi-sub-detail">Market Recovery</div>
            </div>
            <div class="kpi-arrow-link"><span>Ledger</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        {{-- Distributor Receivables --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('admin.distributors.index') }}" class="erp-kpi-tile kpi-accent-amber">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Distributor Receivables</span>
                <div class="kpi-icon-box" style="background:#fffbeb; color:#d97706;"><i class="fas fa-store"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($distributorReceivables, 0) }}</div>
              <div class="kpi-sub-detail">Network Credit</div>
            </div>
            <div class="kpi-arrow-link" style="color:#d97706;"><span>Distributors</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        {{-- Vendor Payables --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('report.vendor.ledger') }}" class="erp-kpi-tile kpi-accent-rose">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Vendor Payables</span>
                <div class="kpi-icon-box" style="background:#fff1f2; color:#e11d48;"><i class="fas fa-hand-holding-usd"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($vendorPayables, 0) }}</div>
              <div class="kpi-sub-detail">Supplier Dues</div>
            </div>
            <div class="kpi-arrow-link" style="color:#e11d48;"><span>Payables</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        {{-- Stock Valuation --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('report.item_stock') }}" class="erp-kpi-tile kpi-accent-indigo">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Stock Inventory</span>
                <div class="kpi-icon-box" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-boxes"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($stockInventoryValue, 0) }}</div>
              <div class="kpi-sub-detail">On-Hand Goods</div>
            </div>
            <div class="kpi-arrow-link" style="color:#4f46e5;"><span>Inventory</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        {{-- Cash in Hand (Drawer) --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('cashbook') }}" class="erp-kpi-tile kpi-accent-blue">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Cash in Hand</span>
                <div class="kpi-icon-box" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-wallet"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($cashInHand, 0) }}</div>
              <div class="kpi-sub-detail">Drawer Liquid</div>
            </div>
            <div class="kpi-arrow-link" style="color:#2563eb;"><span>Cashbook</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        {{-- Bank & Digital (Meezan) --}}
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
          <a href="{{ route('view_all') }}" class="erp-kpi-tile kpi-accent-teal">
            <div>
              <div class="kpi-top-meta">
                <span class="kpi-label-text">Bank Balances</span>
                <div class="kpi-icon-box" style="background:#f0fdf4; color:#0f766e;"><i class="fas fa-university"></i></div>
              </div>
              <div class="kpi-amount-val">Rs {{ number_format($meezanBalance + $easyPaisaBalance, 0) }}</div>
              <div class="kpi-sub-detail">Meezan & Digital</div>
            </div>
            <div class="kpi-arrow-link"><span>Accounts</span><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SUITE 4: 📊 CHARTS MATRIX (SPLINE AREA, CHANNEL DONUT, BARS)    --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-2 g-md-3 mb-3">
      {{-- Chart 1: Revenue Velocity (Spline Area Chart) --}}
      <div class="col-12 col-xl-7">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-chart-area text-teal"></i>
              <span>Sales Revenue Velocity (Multi-Layer Spline)</span>
            </h3>
            <div class="d-flex align-items-center gap-1.5">
              <select id="salesFilter" class="form-select form-select-sm border py-0 px-2 fw-semibold" style="font-size:0.74rem; height:28px;">
                <option value="daily">Daily Timeline</option>
                <option value="weekly">Weekly View</option>
                <option value="monthly">Monthly Aggregate</option>
              </select>
              <a href="{{ route('report.sale') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.72rem; height:28px; line-height:26px;">
                Report &rarr;
              </a>
            </div>
          </div>
          <div style="flex:1;">
            <div id="salesOverviewChart" style="min-height: 250px;"></div>
          </div>
        </div>
      </div>

      {{-- Chart 2: Revenue Channel Distribution (Donut Chart) --}}
      <div class="col-12 col-xl-5">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-pie-chart text-amber"></i>
              <span>Revenue Channel Contribution (POS vs B2B)</span>
            </h3>
            <span class="badge bg-light text-secondary border" style="font-size:0.68rem;">Share</span>
          </div>
          <div class="d-flex flex-column justify-content-between" style="flex:1;">
            <div id="channelShareDonutChart" style="min-height: 180px;"></div>
            <div class="pt-2 border-top">
              <div class="d-flex align-items-center justify-content-between py-1 border-bottom" style="font-size:0.8rem;">
                <div class="d-flex align-items-center gap-2">
                  <span style="width:9px; height:9px; border-radius:50%; background:#0f766e; display:inline-block;"></span>
                  <strong>Retail / POS Sales</strong>
                </div>
                <span class="fw-bold text-teal">Rs {{ number_format($channelRetail, 0) }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between py-1" style="font-size:0.8rem;">
                <div class="d-flex align-items-center gap-2">
                  <span style="width:9px; height:9px; border-radius:50%; background:#d97706; display:inline-block;"></span>
                  <strong>Distributor Wholesale Demand</strong>
                </div>
                <span class="fw-bold" style="color:#d97706;">Rs {{ number_format($channelDistributor, 0) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Chart 3: Cash Inflow vs Outflow (Clean 2-Bar Comparison) --}}
      <div class="col-12 col-md-6 col-xl-4">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-exchange-alt text-primary"></i>
              <span>Cash Flow (Inflow vs Outflow)</span>
            </h3>
            <div class="d-flex align-items-center gap-1.5">
              <div class="btn-group btn-group-sm p-0.5 rounded bg-light border" role="group">
                <button type="button" class="btn btn-xs py-0.5 px-2 fw-bold" id="flowBtnPeriod" onclick="switchCashFlow('period')" style="font-size:0.68rem; border-radius:4px; background:#0f766e; color:#ffffff; border:none;">Period</button>
                <button type="button" class="btn btn-xs py-0.5 px-2 fw-semibold" id="flowBtnToday" onclick="switchCashFlow('today')" style="font-size:0.68rem; border-radius:4px; background:transparent; color:#64748b; border:none;">Today</button>
              </div>
              <a href="{{ route('cashbook') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">Cashbook</a>
            </div>
          </div>
          
          <div class="d-flex flex-column justify-content-between" style="flex:1;">
            {{-- Mini Cash Flow Financial Summary Strip --}}
            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border mb-2" id="flowStatsStrip">
              <div class="text-center flex-fill">
                <div class="text-muted" style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">Inflow (Rec)</div>
                <div class="fw-bold" id="flowInflowVal" style="font-size:0.85rem; color:#0f766e;">Rs {{ number_format($totalIn, 0) }}</div>
              </div>
              <div class="border-end" style="height:22px;"></div>
              <div class="text-center flex-fill">
                <div class="text-muted" style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">Outflow (Exp)</div>
                <div class="fw-bold text-danger" id="flowOutflowVal" style="font-size:0.85rem;">Rs {{ number_format($totalOut, 0) }}</div>
              </div>
              <div class="border-end" style="height:22px;"></div>
              <div class="text-center flex-fill">
                <div class="text-muted" style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">Net Cash</div>
                <div class="fw-bold" id="flowNetVal" style="font-size:0.85rem; color: {{ ($totalIn - $totalOut) >= 0 ? '#0f766e' : '#e11d48' }};">
                  {{ ($totalIn - $totalOut) >= 0 ? '+' : '' }}Rs {{ number_format($totalIn - $totalOut, 0) }}
                </div>
              </div>
            </div>

            {{-- ApexChart Container --}}
            <div id="cashFlowChart" style="min-height: 180px;"></div>
          </div>
        </div>
      </div>

      {{-- Chart 4: Top Nimko Categories Breakdown (Pie Chart) --}}
      <div class="col-12 col-md-6 col-xl-4">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-tags text-teal"></i>
              <span>Top Nimko Categories Contribution</span>
            </h3>
            <a href="{{ route('report.sale.category') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">Categories</a>
          </div>
          <div class="d-flex flex-column justify-content-between" style="flex:1;">
            <div id="categoryPieChart" style="min-height: 180px;"></div>
            <div class="pt-1" style="max-height: 90px; overflow-y: auto;">
              @foreach(array_slice($catDonutList, 0, 4) as $cItem)
              <div class="d-flex align-items-center justify-content-between py-0.5 border-bottom" style="font-size:0.74rem;">
                <div class="d-flex align-items-center gap-1.5 text-truncate" style="max-width:160px;">
                  <span style="width:7px; height:7px; border-radius:50%; background:{{ $cItem['color'] }}; flex-shrink:0;"></span>
                  <span class="text-truncate">{{ $cItem['name'] }}</span>
                </div>
                <span class="badge bg-light text-dark border">{{ $cItem['percentage'] }}% (Rs {{ number_format($cItem['amount'], 0) }})</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Chart 5: Operating Expenses Audit (Donut Chart) --}}
      <div class="col-12 col-md-6 col-xl-4">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-file-invoice text-danger"></i>
              <span>Operating Expense Audit</span>
            </h3>
            <a href="{{ route('all-expense-vochers') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">Vouchers</a>
          </div>
          <div class="d-flex flex-column justify-content-between" style="flex:1;">
            <div id="expenseDonutChart" style="min-height: 180px;"></div>
            <div class="pt-2 text-center text-muted fw-bold" style="font-size:0.75rem;">
              Audited Total: <span class="text-danger">Rs {{ number_format($totalExpenses, 0) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SUITE 5: 📑 DETAILED ENTERPRISE DATA TABLES                     --}}
    {{-- (TOP 10 PRODUCTS, DISTRIBUTORS DIRECTORY, RECENT SALES)        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-2 g-md-3 mb-3">
      {{-- TABLE 1: TOP 10 SELLING PRODUCTS --}}
      <div class="col-12 col-xl-6">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-award text-amber"></i>
              <span>Top 10 Selling Nimko Products</span>
            </h3>
            <a href="{{ route('report.item_stock') }}" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size:0.72rem;">
              Stock Report &rarr;
            </a>
          </div>
          <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
            <table class="erp-table">
              <thead>
                <tr>
                  <th style="width: 8%;">Rank</th>
                  <th style="width: 20%;">Item Code</th>
                  <th style="width: 32%;">Product Name</th>
                  <th class="text-center" style="width: 15%;">Units Sold</th>
                  <th class="text-end" style="width: 15%;">Revenue</th>
                  <th class="text-center" style="width: 10%;">Stock</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topProducts as $tp)
                <tr>
                  <td>
                    <span class="erp-rank-tag {{ $tp['rank'] <= 3 ? 'top' : '' }}">#{{ $tp['rank'] }}</span>
                  </td>
                  <td>
                    <span class="font-monospace fw-bold text-secondary" style="font-size:0.74rem;">{{ $tp['code'] }}</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $tp['name'] }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">{{ $tp['category'] }}</div>
                  </td>
                  <td class="text-center fw-bold">{{ number_format($tp['units_sold']) }}</td>
                  <td class="text-end fw-bold text-teal">Rs {{ number_format($tp['revenue'], 0) }}</td>
                  <td class="text-center">
                    <span class="badge {{ $tp['stock_qty'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill" style="font-size:0.68rem;">
                      {{ number_format($tp['stock_qty']) }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No product sales recorded yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- TABLE 2: TOP DISTRIBUTORS DIRECTORY & NETWORK BALANCES --}}
      <div class="col-12 col-xl-6">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-truck-moving text-info"></i>
              <span>Distributor Network & B2B Balances</span>
            </h3>
            <a href="{{ route('admin.distributors.index') }}" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size:0.72rem;">
              Distributors &rarr;
            </a>
          </div>
          <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
            <table class="erp-table">
              <thead>
                <tr>
                  <th style="width: 35%;">Distributor</th>
                  <th style="width: 20%;">City / Phone</th>
                  <th class="text-center" style="width: 15%;">Orders</th>
                  <th class="text-end" style="width: 20%;">Current Balance</th>
                  <th class="text-center" style="width: 10%;">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topDistributors as $dItem)
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $dItem->name }}</div>
                    <div class="text-muted font-monospace" style="font-size:0.68rem;">{{ $dItem->code }}</div>
                  </td>
                  <td>
                    <div class="fw-semibold text-secondary">{{ $dItem->city ?? 'N/A' }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">{{ $dItem->phone }}</div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border px-2 py-0.5">{{ $dItem->orders_count }} Orders</span>
                  </td>
                  <td class="text-end fw-bold" style="color: {{ $dItem->current_balance > 0 ? '#d97706' : '#059669' }};">
                    Rs {{ number_format($dItem->current_balance, 0) }}
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $dItem->status == 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill" style="font-size:0.65rem;">
                      {{ ucfirst($dItem->status) }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No distributors registered yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- TABLE 3: RECENT POS SALES REGISTER --}}
      <div class="col-12 col-xl-6">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-file-invoice text-teal"></i>
              <span>Recent Sales Invoices Register</span>
            </h3>
            <a href="{{ route('sale.index') }}" class="btn btn-sm btn-outline-teal py-0 px-2" style="font-size:0.72rem; color:#0f766e;">
              All Sales &rarr;
            </a>
          </div>
          <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
            <table class="erp-table">
              <thead>
                <tr>
                  <th>Invoice #</th>
                  <th>Customer</th>
                  <th>Payment Mode</th>
                  <th class="text-end">Net Total</th>
                  <th class="text-center">Date &bull; Time</th>
                  <th class="text-center">View</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentSalesList as $rSale)
                <tr>
                  <td>
                    <span class="fw-bold font-monospace text-dark">{{ $rSale->invoice_no }}</span>
                  </td>
                  <td>
                    <span class="fw-semibold text-secondary">{{ $rSale->customer_name ?: ($rSale->customer_field ?? 'Walk-in Customer') }}</span>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border" style="font-size:0.68rem;">
                      {{ $rSale->card > 0 ? 'Card' : 'Cash' }}
                    </span>
                  </td>
                  <td class="text-end fw-bold text-teal">Rs {{ number_format($rSale->total_net, 0) }}</td>
                  <td class="text-center text-muted" style="font-size:0.7rem;">
                    {{ \Carbon\Carbon::parse($rSale->created_at)->format('d M, h:i A') }}
                  </td>
                  <td class="text-center">
                    <a href="{{ route('sales.invoice', $rSale->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-1.5" title="View Invoice Receipt">
                      <i class="fas fa-receipt"></i>
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No sales recorded yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- TABLE 4: CRITICAL LOW STOCK REORDER (ID: erp-low-stock-table) --}}
      <div id="erp-low-stock-table" class="col-12 col-xl-6">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-exclamation-triangle text-danger"></i>
              <span>Critical Low Stock Reorder Priority</span>
            </h3>
            <a href="{{ route('add_purchase') }}" class="btn btn-sm btn-dark py-0 px-2 fw-bold" style="font-size:0.72rem;">
              <i class="fas fa-plus me-1"></i> + Purchase Order
            </a>
          </div>
          <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
            <table class="erp-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Category</th>
                  <th class="text-center">Current Qty</th>
                  <th class="text-center">Alert Level</th>
                  <th class="text-center">Deficit</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($lowStockItems as $lItem)
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $lItem->item_name }}</div>
                    <div class="text-muted font-monospace" style="font-size:0.68rem;">{{ $lItem->item_code }}</div>
                  </td>
                  <td class="text-secondary">{{ $lItem->category_name ?? 'Nimko' }}</td>
                  <td class="text-center fw-bold text-danger">{{ $lItem->qty }}</td>
                  <td class="text-center text-muted">{{ $lItem->alert_quantity }}</td>
                  <td class="text-center fw-bold text-dark">+{{ $lItem->deficit }}</td>
                  <td class="text-center">
                    <span class="badge {{ $lItem->qty <= 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }} rounded-pill" style="font-size:0.65rem;">
                      {{ $lItem->status }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-check-circle text-success me-1"></i> All stock levels are healthy!
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- TABLE 5: MULTI-BRANCH SALES PERFORMANCE SUMMARY --}}
      <div class="col-12">
        <div class="erp-card-surface">
          <div class="erp-card-header">
            <h3 class="erp-card-heading">
              <i class="fas fa-store text-teal"></i>
              <span>Multi-Branch Sales Performance Summary</span>
            </h3>
            <a href="{{ route('report.sale') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.72rem;">
              Branch Sales Report &rarr;
            </a>
          </div>
          <div class="table-responsive">
            <table class="erp-table">
              <thead>
                <tr>
                  <th>Branch Name</th>
                  <th class="text-center">Invoices</th>
                  <th class="text-end">Gross Sales</th>
                  <th class="text-end">Returns</th>
                  <th class="text-end">Net Sales</th>
                  <th style="width: 25%;">Share & Contribution</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchSalesPerformance as $bItem)
                <tr style="{{ $bItem['is_active'] ? 'background:#f8fafc;' : '' }}">
                  <td>
                    <div class="fw-bold text-dark">
                      {{ $bItem['name'] }}
                      @if($bItem['is_active'])
                        <span class="badge bg-teal text-white rounded-pill px-2 py-0.5 ms-1" style="font-size:0.62rem; background:#0f766e;">Active</span>
                      @endif
                    </div>
                    <div class="text-muted" style="font-size:0.68rem;">{{ $bItem['address'] }}</div>
                  </td>
                  <td class="text-center fw-bold">{{ number_format($bItem['invoice_count']) }}</td>
                  <td class="text-end fw-semibold">Rs {{ number_format($bItem['total_sales'], 0) }}</td>
                  <td class="text-end text-danger fw-semibold">Rs {{ number_format($bItem['total_returns'], 0) }}</td>
                  <td class="text-end fw-bold text-teal">Rs {{ number_format($bItem['net_sales'], 0) }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-fill" style="height: 5px; background: #e2e8f0; border-radius: 4px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ max($bItem['share_pct'], 4) }}%; background-color: #0f766e;"></div>
                      </div>
                      <span class="text-muted small fw-semibold">{{ number_format($bItem['share_pct'], 1) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('report.sale') }}" class="btn btn-xs btn-outline-secondary py-0.5 px-2" style="font-size:0.7rem;">
                      History
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-3">No branch sales recorded for this period.</td>
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
// ── Real-time Live Clock ──
function updateLiveClock() {
  const el = document.getElementById('dashClockDisplay');
  if(!el) return;
  const now = new Date();
  const options = { weekday: 'short', year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
  el.innerHTML = now.toLocaleString('en-US', options).replace(',', ' &bull;');
}
setInterval(updateLiveClock, 1000);

document.addEventListener('DOMContentLoaded', function() {

  // 1. Revenue Velocity Area Spline Chart (Layered Actual vs Baseline)
  const salesChartStats = @json($salesChartStats ?? []);
  const salesData = @json($salesData);
  const salesLabels = @json($labels);
  const targetData = salesData.map(v => Math.round(v * 0.95));

  const salesOverviewOpts = {
    chart: { type: 'area', height: 250, toolbar: { show: false }, background: 'transparent' },
    series: [
      { name: 'Actual Sales', data: salesData.length ? salesData : [0] },
      { name: 'Target Baseline', data: targetData.length ? targetData : [0] }
    ],
    xaxis: {
      categories: salesLabels.length ? salesLabels : ['Period'],
      labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: { labels: { formatter: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v), style: { colors: '#94a3b8', fontSize: '10px' } } },
    stroke: { curve: 'smooth', width: [2.5, 1.5], dashArray: [0, 4] },
    colors: ['#0f766e', '#94a3b8'],
    fill: { type: ['gradient', 'solid'], gradient: { opacityFrom: 0.3, opacityTo: 0.04 } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' }
  };
  new ApexCharts(document.querySelector('#salesOverviewChart'), salesOverviewOpts).render();

  // Dynamic filter change handler
  document.getElementById('salesFilter')?.addEventListener('change', function() {
    const type = this.value || 'daily';
    const data = salesChartStats[type] || salesChartStats['daily'];
    if (!data) return;
    const chartEl = document.querySelector('#salesOverviewChart');
    const chart = ApexCharts.getChartByEl ? ApexCharts.getChartByEl(chartEl) : null;
    if (chart) {
      chart.updateOptions({
        xaxis: { categories: data.categories || [] },
        series: [
          { name: 'Actual Sales', data: (data.series && data.series[0] && data.series[0].data) ? data.series[0].data : [0] },
          { name: 'Target Baseline', data: (data.series && data.series[0] && data.series[0].data) ? data.series[0].data.map(v => Math.round(v * 0.95)) : [0] }
        ]
      });
    }
  });

  // 2. Channel Distribution Donut Chart (Retail POS vs Distributor B2B)
  const channelRetailVal = @json($channelRetail) || 0;
  const channelDistributorVal = @json($channelDistributor) || 0;
  const channelSeries = [channelRetailVal, channelDistributorVal];
  const totalChannelVal = channelRetailVal + channelDistributorVal;

  const channelDonutOpts = {
    chart: { type: 'donut', height: 180 },
    series: totalChannelVal > 0 ? channelSeries : [1],
    labels: totalChannelVal > 0 ? ['Retail POS Sales', 'Distributor Demand'] : ['No Recorded Sales'],
    colors: ['#0f766e', '#d97706'],
    legend: { show: false },
    dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'TOTAL DEMAND',
              fontSize: '9px',
              fontWeight: 700,
              color: '#64748b',
              formatter: () => 'Rs ' + totalChannelVal.toLocaleString()
            }
          }
        }
      }
    }
  };
  new ApexCharts(document.querySelector('#channelShareDonutChart'), channelDonutOpts).render();

  // 3. Cash Flow Inflow vs Outflow Bar Chart (Clean 2-Bar Comparison)
  const cashFlowPeriodData = [@json($totalIn), @json($totalOut)];
  const cashFlowTodayData = [@json($todayIn), @json($todayOut)];

  const cashFlowOpts = {
    chart: { type: 'bar', height: 180, toolbar: { show: false }, background: 'transparent' },
    series: [{ name: 'Amount', data: cashFlowPeriodData }],
    xaxis: {
      categories: ['Cash Inflow', 'Cash Outflow'],
      labels: {
        rotate: 0,
        rotateAlways: false,
        style: { colors: ['#0f766e', '#e11d48'], fontSize: '11px', fontWeight: 700 }
      },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: {
      labels: {
        formatter: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
        style: { colors: '#94a3b8', fontSize: '10px' }
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '36%',
        borderRadius: 5,
        distributed: true,
        dataLabels: { position: 'top' }
      }
    },
    colors: ['#0f766e', '#e11d48'],
    legend: { show: false },
    dataLabels: {
      enabled: true,
      formatter: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v),
      offsetY: -18,
      style: { fontSize: '10px', fontWeight: 700, colors: ['#0f172a'] }
    },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 3 }
  };
  const cashFlowChartInstance = new ApexCharts(document.querySelector('#cashFlowChart'), cashFlowOpts);
  cashFlowChartInstance.render();

  window.switchCashFlow = function(mode) {
    const btnPeriod = document.getElementById('flowBtnPeriod');
    const btnToday = document.getElementById('flowBtnToday');
    const inValEl = document.getElementById('flowInflowVal');
    const outValEl = document.getElementById('flowOutflowVal');
    const netValEl = document.getElementById('flowNetVal');

    if (mode === 'today') {
      btnToday.style.background = '#0f766e';
      btnToday.style.color = '#ffffff';
      btnPeriod.style.background = 'transparent';
      btnPeriod.style.color = '#64748b';

      const tIn = @json($todayIn);
      const tOut = @json($todayOut);
      const tNet = tIn - tOut;

      inValEl.innerHTML = 'Rs ' + Number(tIn).toLocaleString();
      outValEl.innerHTML = 'Rs ' + Number(tOut).toLocaleString();
      netValEl.innerHTML = (tNet >= 0 ? '+' : '') + 'Rs ' + Number(tNet).toLocaleString();
      netValEl.style.color = tNet >= 0 ? '#0f766e' : '#e11d48';

      cashFlowChartInstance.updateSeries([{ name: 'Amount', data: cashFlowTodayData }]);
    } else {
      btnPeriod.style.background = '#0f766e';
      btnPeriod.style.color = '#ffffff';
      btnToday.style.background = 'transparent';
      btnToday.style.color = '#64748b';

      const pIn = @json($totalIn);
      const pOut = @json($totalOut);
      const pNet = pIn - pOut;

      inValEl.innerHTML = 'Rs ' + Number(pIn).toLocaleString();
      outValEl.innerHTML = 'Rs ' + Number(pOut).toLocaleString();
      netValEl.innerHTML = (pNet >= 0 ? '+' : '') + 'Rs ' + Number(pNet).toLocaleString();
      netValEl.style.color = pNet >= 0 ? '#0f766e' : '#e11d48';

      cashFlowChartInstance.updateSeries([{ name: 'Amount', data: cashFlowPeriodData }]);
    }
  };

  // 4. Top Nimko Categories Contribution Pie Chart
  const catDonutList = @json($catDonutList);
  const catSeries = catDonutList.map(item => item.amount);
  const catLabels = catDonutList.map(item => item.name);
  const catColors = catDonutList.map(item => item.color);
  const totalCatAmount = catSeries.reduce((a, b) => a + b, 0);

  const categoryPieOpts = {
    chart: { type: 'pie', height: 180 },
    series: catSeries.length && totalCatAmount > 0 ? catSeries : [1],
    labels: catLabels.length && totalCatAmount > 0 ? catLabels : ['General Nimko'],
    colors: catColors.length ? catColors : ['#0f766e', '#14b8a6', '#d97706', '#334155'],
    legend: { show: false },
    dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' }
  };
  new ApexCharts(document.querySelector('#categoryPieChart'), categoryPieOpts).render();

  // 5. Operating Expense Breakdown Donut Chart
  const expenseChartData = @json($expenseChartData);
  let expHeadNames = [];
  let expHeadSeries = [];
  
  if (Object.keys(expenseChartData).length > 0) {
    Object.values(expenseChartData).forEach(head => {
      expHeadNames.push(head.head_name);
      const headTotal = head.series[0].data.reduce((a, b) => a + b, 0);
      expHeadSeries.push(headTotal);
    });
  } else {
    expHeadNames = ['Expenses'];
    expHeadSeries = [@json($totalExpenses) || 0];
  }

  const expenseDonutOpts = {
    chart: { type: 'donut', height: 180 },
    series: expHeadSeries.length && expHeadSeries.some(v => v > 0) ? expHeadSeries : [1],
    labels: expHeadNames.length ? expHeadNames : ['No Audited Expenses'],
    colors: ['#e11d48', '#0f766e', '#d97706', '#334155', '#4f46e5'],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'EXPENSES',
              fontSize: '9px',
              fontWeight: 700,
              color: '#94a3b8',
              formatter: () => 'Rs {{ number_format($totalExpenses, 0) }}'
            }
          }
        }
      }
    }
  };
  new ApexCharts(document.querySelector('#expenseDonutChart'), expenseDonutOpts).render();

});
</script>
@endsection
