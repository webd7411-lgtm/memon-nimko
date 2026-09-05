@extends('admin_panel.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

.trend-dashboard i.fa,
.trend-dashboard i.fas,
.trend-dashboard i.far,
.trend-dashboard i.fab,
.trend-dashboard i {
  font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
  font-weight: 900 !important;
  display: inline-block !important;
  font-style: normal !important;
}

:root {
  --bg-main: #f3f6fb;
  --card-bg: #ffffff;
  --border-color: #e2e8f0;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --text-sub: #94a3b8;
  
  --primary-purple: #6366f1;
  --primary-blue: #3b82f6;
  --primary-teal: #14b8a6;
  --primary-amber: #f59e0b;
  --primary-emerald: #10b981;
  --primary-rose: #f43f5e;
  --primary-pink: #ec4899;
  
  --radius-card: 16px;
  --radius-sm: 10px;
  --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --shadow-sm: 0 1px 3px rgba(15,23,42,0.03), 0 4px 12px rgba(15,23,42,0.04);
}

.trend-dashboard * {
  font-family: var(--font-family);
  box-sizing: border-box;
}

.trend-dashboard {
  background: var(--bg-main);
  min-height: 100vh;
  padding: 1.25rem 0.5rem 3rem 0.5rem;
}

/* ─── Top Header Card ─── */
.dash-top-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.25rem;
  padding: 0.5rem 0.25rem;
}

.dash-welcome-title {
  font-size: 1.75rem;
  font-weight: 800;
  color: var(--text-main);
  margin: 0;
  letter-spacing: -0.5px;
}

.dash-welcome-sub {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--text-muted);
  margin-top: 4px;
}

.dash-welcome-sub span {
  color: var(--text-main);
  font-weight: 600;
}

.btn-sync-cloud {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  color: #ffffff !important;
  font-weight: 700;
  font-size: 0.85rem;
  padding: 0.65rem 1.35rem;
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.25s ease;
  cursor: pointer;
  text-decoration: none;
}

.btn-sync-cloud:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
}

/* ─── Quick Filter Report Pills ─── */
.quick-reports-wrapper {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  overflow-x: auto;
  padding-bottom: 0.6rem;
  margin-bottom: 1.5rem;
  scrollbar-width: thin;
  flex-wrap: nowrap;
}

.quick-reports-wrapper::-webkit-scrollbar {
  height: 4px;
}
.quick-reports-wrapper::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.quick-report-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 999px;
  padding: 0.5rem 1.1rem;
  font-size: 0.82rem;
  font-weight: 700;
  color: #334155;
  text-decoration: none;
  white-space: nowrap;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
  transition: all 0.2s ease;
}

.quick-report-pill:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-1px);
  color: #0f172a;
}

.quick-report-pill i {
  font-size: 0.9rem;
}

/* ─── Section Header ─── */
.dash-section-label {
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: var(--text-muted);
  margin-bottom: 0.85rem;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* ─── Main Card Base ─── */
.t-card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-sm);
  transition: all 0.25s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.t-card:hover {
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
}

.t-card-header {
  padding: 1.1rem 1.25rem 0.5rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.t-card-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--text-main);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.t-card-body {
  padding: 1.25rem;
  flex: 1;
}

/* ─── KPI Top Cards ─── */
.kpi-card {
  position: relative;
  overflow: hidden;
  padding: 1.15rem 1.25rem;
}

.kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  border-radius: 16px 16px 0 0;
}

.kpi-card.purple::before { background: linear-gradient(90deg, #818cf8, #6366f1); }
.kpi-card.pink::before   { background: linear-gradient(90deg, #f43f5e, #ec4899); }
.kpi-card.amber::before  { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.kpi-card.rose::before   { background: linear-gradient(90deg, #ef4444, #f43f5e); }
.kpi-card.green::before  { background: linear-gradient(90deg, #34d399, #10b981); }
.kpi-card.blue::before   { background: linear-gradient(90deg, #60a5fa, #3b82f6); }

.kpi-hdr {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.kpi-label {
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted);
}

.kpi-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.kpi-value {
  font-size: 1.55rem;
  font-weight: 900;
  color: var(--text-main);
  line-height: 1.1;
  margin-bottom: 0.35rem;
}

.kpi-trend {
  font-size: 0.75rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.kpi-trend.up { color: #10b981; }
.kpi-trend.down { color: #ef4444; }
.kpi-trend-sub { color: var(--text-sub); font-weight: 500; }

/* ─── Category Donut Legend Item ─── */
.cat-legend-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.4rem 0;
  font-size: 0.82rem;
  border-bottom: 1px dashed #f1f5f9;
}
.cat-legend-item:last-child { border-bottom: none; }

.cat-dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
  display: inline-block;
}

.cat-name { font-weight: 600; color: #334155; }
.cat-pct { font-size: 0.75rem; color: var(--text-sub); margin-left: 6px; font-weight: 600; }
.cat-amt { font-weight: 800; color: var(--text-main); }

/* ─── Top Products Ranked List ─── */
.top-prod-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.65rem 0;
  border-bottom: 1px solid #f1f5f9;
}
.top-prod-item:last-child { border-bottom: none; }

.rank-badge {
  width: 24px;
  height: 24px;
  border-radius: 6px;
  background: #f1f5f9;
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.rank-badge.top-1 { background: #fef3c7; color: #d97706; }

.top-prod-info { flex: 1; margin-left: 10px; }
.top-prod-name { font-size: 0.8rem; font-weight: 800; color: var(--text-main); text-transform: uppercase; line-height: 1.2; }
.top-prod-sub { font-size: 0.72rem; color: var(--text-sub); font-weight: 500; margin-top: 2px; }
.top-prod-val { font-size: 0.85rem; font-weight: 800; color: #4f46e5; text-align: right; }

/* ─── Business Summary Grid (Inside Card) ─── */
.biz-summary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.biz-box {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.biz-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.biz-val { font-size: 1.15rem; font-weight: 800; color: var(--text-main); line-height: 1; }
.biz-title { font-size: 0.72rem; color: var(--text-muted); font-weight: 600; margin-top: 2px; }
.biz-growth { font-size: 0.68rem; font-weight: 700; color: #10b981; }

/* ─── Activity List ─── */
.activity-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0;
  border-bottom: 1px solid #f8fafc;
}
.activity-item:last-child { border-bottom: none; }

.activity-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #ecfdf5;
  color: #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.activity-title { font-size: 0.78rem; font-weight: 700; color: var(--text-main); line-height: 1.2; }
.activity-sub { font-size: 0.7rem; color: var(--text-sub); font-weight: 500; }
.activity-amt { font-size: 0.82rem; font-weight: 800; color: var(--text-main); margin-left: auto; text-align: right; }
.activity-time { font-size: 0.68rem; color: var(--text-sub); font-weight: 500; }

/* ─── Financial Position Card ─── */
.fin-card {
  padding: 1rem 1.1rem;
  position: relative;
}

.fin-hdr {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.fin-title { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; }
.fin-icon { font-size: 1.1rem; }
.fin-val { font-size: 1.3rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.4rem; }
.fin-link { font-size: 0.72rem; font-weight: 700; color: #4f46e5; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.fin-link:hover { text-decoration: underline; }

/* ─── Monthly Sparkline Card ─── */
.spark-card {
  padding: 1rem 1.15rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.spark-title { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 2px; }
.spark-val { font-size: 1.2rem; font-weight: 900; color: var(--text-main); }

@media (max-width: 768px) {
  .dash-welcome-title { font-size: 1.3rem; }
  .kpi-value { font-size: 1.3rem; }
  .fin-val { font-size: 1.15rem; }
}
</style>

<div class="trend-dashboard">
  <div class="container-fluid px-2 px-md-3">

    {{-- ─── 1. TOP HEADER & WELCOME ─── --}}
    <div class="dash-top-header">
      <div>
        <h1 class="dash-welcome-title">Welcome back, {{ Auth::user()->name ?? 'Super Admin' }}! 👋</h1>
        <div class="dash-welcome-sub">
          Here's what's happening with your business today — <span id="dashClockDisplay">{{ now()->format('l, d M Y | h:i:s A') }}</span>
        </div>
      </div>
      <div>
        <a href="{{ route('System.Reports') }}" class="btn-sync-cloud">
          <i class="fas fa-cloud-upload-alt"></i> Sync to Cloud
        </a>
      </div>
    </div>

    {{-- ─── 2. QUICK REPORT PILL BUTTONS ─── --}}
    <div class="quick-reports-wrapper">
      <a href="{{ route('report.sale') }}" class="quick-report-pill">
        <i class="fa fa-file-text-o fas fa-file-alt" style="color:#10b981;"></i> Sales Report
      </a>
      <a href="{{ route('report.purchase') }}" class="quick-report-pill">
        <i class="fa fa-shopping-cart fas fa-shopping-cart" style="color:#3b82f6;"></i> Purchase Report
      </a>
      <a href="{{ route('System.Reports') }}" class="quick-report-pill">
        <i class="fa fa-line-chart fas fa-chart-line" style="color:#14b8a6;"></i> Profit & Loss
      </a>
      <a href="{{ route('System.Reports') }}" class="quick-report-pill">
        <i class="fa fa-briefcase fas fa-briefcase" style="color:#8b5cf6;"></i> Executive Report
      </a>
      <a href="{{ route('report.customer.ledger') }}" class="quick-report-pill">
        <i class="fa fa-money fas fa-file-invoice" style="color:#f59e0b;"></i> Recovery Report
      </a>
      <a href="{{ route('report.vendor.ledger') }}" class="quick-report-pill">
        <i class="fa fa-arrow-down fas fa-arrow-down" style="color:#ef4444;"></i> Payable Report
      </a>
      <a href="{{ route('report.customer.ledger') }}" class="quick-report-pill">
        <i class="fa fa-users fas fa-user-friends" style="color:#06b6d4;"></i> Parties Balance
      </a>
      <a href="{{ route('report.item_stock') }}" class="quick-report-pill">
        <i class="fa fa-cubes fas fa-boxes" style="color:#10b981;"></i> On-Hand Stock
      </a>
      <a href="{{ route('view_all') }}" class="quick-report-pill">
        <i class="fa fa-balance-scale fas fa-balance-scale" style="color:#475569;"></i> Balance Sheet
      </a>
    </div>

    {{-- ─── 3. KEY PERFORMANCE INDICATORS ─── --}}
    <div class="dash-section-label">
      <i class="fa fa-pie-chart fas fa-chart-pie" style="color:#6366f1;"></i> Key Performance Indicators
    </div>

    <div class="row g-2 g-md-3 mb-4">
      {{-- Total Sales --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card purple">
          <div class="kpi-hdr">
            <span class="kpi-label">TOTAL SALES (THIS MONTH)</span>
            <div class="kpi-icon-box" style="background:#f3e8ff;color:#9333ea;">
              <i class="fa fa-shopping-bag fas fa-shopping-bag"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($totalSales, 0) }}</div>
          <div class="kpi-trend {{ $salesGrowth >= 0 ? 'up' : 'down' }}">
            <i class="fa {{ $salesGrowth >= 0 ? 'fa-arrow-up fas fa-arrow-up' : 'fa-arrow-down fas fa-arrow-down' }}"></i> {{ $salesGrowth }}% <span class="kpi-trend-sub">vs last month</span>
          </div>
        </div>
      </div>

      {{-- Total Purchases --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card pink">
          <div class="kpi-hdr">
            <span class="kpi-label">TOTAL PURCHASES (THIS MONTH)</span>
            <div class="kpi-icon-box" style="background:#ffe4e6;color:#e11d48;">
              <i class="fa fa-shopping-cart fas fa-shopping-basket"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($totalPurchases, 0) }}</div>
          <div class="kpi-trend {{ $purchaseGrowth >= 0 ? 'up' : 'down' }}">
            <i class="fa {{ $purchaseGrowth >= 0 ? 'fa-arrow-up fas fa-arrow-up' : 'fa-arrow-down fas fa-arrow-down' }}"></i> {{ $purchaseGrowth }}% <span class="kpi-trend-sub">vs last month</span>
          </div>
        </div>
      </div>

      {{-- Gross Profit --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card amber">
          <div class="kpi-hdr">
            <span class="kpi-label">GROSS PROFIT (THIS MONTH)</span>
            <div class="kpi-icon-box" style="background:#fef3c7;color:#d97706;">
              <i class="fa fa-line-chart fas fa-chart-line"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($grossProfit, 0) }}</div>
          <div class="kpi-trend {{ $grossProfitGrowth >= 0 ? 'up' : 'down' }}">
            <i class="fa {{ $grossProfitGrowth >= 0 ? 'fa-arrow-up fas fa-arrow-up' : 'fa-arrow-down fas fa-arrow-down' }}"></i> {{ $grossProfitGrowth }}% <span class="kpi-trend-sub">vs last month</span>
          </div>
        </div>
      </div>

      {{-- Total Expenses --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card rose">
          <div class="kpi-hdr">
            <span class="kpi-label">TOTAL EXPENSES (THIS MONTH)</span>
            <div class="kpi-icon-box" style="background:#fef2f2;color:#dc2626;">
              <i class="fa fa-file-text-o fas fa-file-invoice-dollar"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($totalExpenses, 0) }}</div>
          <div class="kpi-trend {{ $expenseGrowth <= 0 ? 'up' : 'down' }}">
            <i class="fa {{ $expenseGrowth <= 0 ? 'fa-arrow-down fas fa-arrow-down' : 'fa-arrow-up fas fa-arrow-up' }}"></i> {{ abs($expenseGrowth) }}% <span class="kpi-trend-sub">vs last month</span>
          </div>
        </div>
      </div>

      {{-- Net Profit --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card green">
          <div class="kpi-hdr">
            <span class="kpi-label">NET PROFIT (THIS MONTH)</span>
            <div class="kpi-icon-box" style="background:#dcfce7;color:#16a34a;">
              <i class="fa fa-shield fas fa-shield-alt"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($netProfit, 0) }}</div>
          <div class="kpi-trend {{ $netProfitGrowth >= 0 ? 'up' : 'down' }}">
            <i class="fa {{ $netProfitGrowth >= 0 ? 'fa-arrow-up fas fa-arrow-up' : 'fa-arrow-down fas fa-arrow-down' }}"></i> {{ $netProfitGrowth }}% <span class="kpi-trend-sub">vs last month</span>
          </div>
        </div>
      </div>

      {{-- Cash Balance --}}
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card kpi-card blue">
          <div class="kpi-hdr">
            <span class="kpi-label">CASH BALANCE</span>
            <div class="kpi-icon-box" style="background:#dbeafe;color:#2563eb;">
              <i class="fa fa-wallet fas fa-wallet"></i>
            </div>
          </div>
          <div class="kpi-value">Rs {{ number_format($cashBalance, 0) }}</div>
          <div style="font-size:0.72rem;color:var(--text-sub);font-weight:600;">Available Liquid Balance</div>
        </div>
      </div>
    </div>

    {{-- ─── 4. SALES ANALYTICS ─── --}}
    <div class="dash-section-label">
      <i class="fas fa-chart-bar" style="color:#3b82f6;"></i> Sales Analytics
    </div>

    <div class="row g-3 mb-4">
      {{-- Sales Overview (Line Chart) --}}
      <div class="col-12 col-lg-5">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fas fa-chart-line text-indigo-500"></i> Sales Overview</h3>
            <select id="salesFilter" class="form-select form-select-sm border-0 bg-light fw-bold" style="width:auto;font-size:0.75rem;">
              <option value="daily">Last 7 Days</option>
              <option value="weekly">Last 30 Days</option>
              <option value="monthly">This Month</option>
            </select>
          </div>
          <div class="t-card-body p-2">
            <div id="salesOverviewChart" style="min-height: 250px;"></div>
          </div>
        </div>
      </div>

      {{-- By Category (Donut Chart & Legend) --}}
      <div class="col-12 col-lg-4">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fas fa-chart-pie text-cyan-500"></i> By Category</h3>
          </div>
          <div class="t-card-body d-flex flex-column justify-content-between p-3">
            <div id="categoryDonutChart" style="min-height: 180px;"></div>
            <div class="mt-2">
              @foreach($catDonutList as $item)
              <div class="cat-legend-item">
                <div>
                  <span class="cat-dot" style="background:{{ $item['color'] }};"></span>
                  <span class="cat-name">{{ $item['name'] }}</span>
                  <span class="cat-pct">{{ $item['percentage'] }}%</span>
                </div>
                <div class="cat-amt">Rs {{ number_format($item['amount'], 0) }}</div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Top Products (Ranked List) --}}
      <div class="col-12 col-lg-3">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fas fa-fire text-amber-500"></i> Top Products</h3>
            <span class="badge bg-light text-dark border" style="font-size:0.7rem;">By Qty</span>
          </div>
          <div class="t-card-body p-3">
            @foreach($topProducts as $tp)
            <div class="top-prod-item">
              <div class="rank-badge {{ $tp['rank'] == 1 ? 'top-1' : '' }}">{{ $tp['rank'] }}</div>
              <div class="top-prod-info">
                <div class="top-prod-name">{{ $tp['name'] }}</div>
                <div class="top-prod-sub">{{ $tp['units_sold'] }} units sold</div>
              </div>
              <div class="top-prod-val">Rs {{ number_format($tp['revenue'], 0) }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ─── 5. BUSINESS SUMMARY & CASH FLOW ─── --}}
    <div class="dash-section-label">
      <i class="fas fa-briefcase" style="color:#f59e0b;"></i> Business Summary & Cash Flow
    </div>

    <div class="row g-3 mb-4">
      {{-- Business Summary --}}
      <div class="col-12 col-lg-3">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fa fa-building fas fa-building text-blue-500"></i> Business Summary</h3>
          </div>
          <div class="t-card-body p-3">
            <div class="biz-summary-grid">
              <div class="biz-box">
                <div class="biz-icon" style="background:#dbeafe;color:#2563eb;">
                  <i class="fa fa-users fas fa-users"></i>
                </div>
                <div>
                  <div class="biz-val">{{ $customerscount }}</div>
                  <div class="biz-title">Customers</div>
                  <div class="biz-growth">↑ {{ $customersGrowth }}%</div>
                </div>
              </div>
              <div class="biz-box">
                <div class="biz-icon" style="background:#f3e8ff;color:#9333ea;">
                  <i class="fa fa-truck fas fa-truck"></i>
                </div>
                <div>
                  <div class="biz-val">{{ $suppliersCount }}</div>
                  <div class="biz-title">Suppliers</div>
                  <div class="biz-growth">↑ {{ $suppliersGrowth }}%</div>
                </div>
              </div>
              <div class="biz-box">
                <div class="biz-icon" style="background:#fef3c7;color:#d97706;">
                  <i class="fa fa-cubes fas fa-box"></i>
                </div>
                <div>
                  <div class="biz-val">{{ $productCount }}</div>
                  <div class="biz-title">Products</div>
                  <div class="biz-growth">↑ {{ $productsGrowth }}%</div>
                </div>
              </div>
              <div class="biz-box">
                <div class="biz-icon" style="background:#dcfce7;color:#16a34a;">
                  <i class="fa fa-user fas fa-user-tie"></i>
                </div>
                <div>
                  <div class="biz-val">{{ $employeesCount }}</div>
                  <div class="biz-title">Employees</div>
                  <div class="biz-growth">↑ {{ $employeesGrowth }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Cash Flow Overview (Bar Chart) --}}
      <div class="col-12 col-lg-3">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fa fa-exchange fas fa-exchange-alt text-emerald-500"></i> Cash Flow Overview</h3>
            <span class="badge bg-light text-dark border" style="font-size:0.7rem;">This Month</span>
          </div>
          <div class="t-card-body p-2">
            <div id="cashFlowChart" style="min-height:220px;"></div>
          </div>
        </div>
      </div>

      {{-- Expense Breakdown (Donut Chart) --}}
      <div class="col-12 col-lg-3">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fa fa-file-text-o fas fa-receipt text-rose-500"></i> Expense Breakdown</h3>
          </div>
          <div class="t-card-body p-3 d-flex flex-column justify-content-between">
            <div id="expenseDonutChart" style="min-height:160px;"></div>
            <div class="mt-2">
              @php
                $expenseColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444'];
                $expTotalAll = $totalExpenses > 0 ? $totalExpenses : 1;
                $idx = 0;
              @endphp
              @if(!empty($expenseChartData))
                @foreach($expenseChartData as $hid => $head)
                  @php
                    $headTotal = collect($head['series'][0]['data'])->sum();
                    $pct = $expTotalAll > 0 ? round(($headTotal / $expTotalAll) * 100) : 0;
                    $color = $expenseColors[$idx % count($expenseColors)];
                    $idx++;
                  @endphp
                  <div class="cat-legend-item">
                    <div><span class="cat-dot" style="background:{{ $color }};"></span> <span class="cat-name">{{ $head['head_name'] }}</span> <span class="cat-pct">{{ $pct }}%</span></div>
                    <div class="cat-amt">Rs {{ number_format($headTotal, 0) }}</div>
                  </div>
                @endforeach
              @else
                <div class="cat-legend-item">
                  <div><span class="cat-dot" style="background:#6366f1;"></span> <span class="cat-name">No Data</span> <span class="cat-pct">0%</span></div>
                  <div class="cat-amt">Rs 0</div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Recent Activities --}}
      <div class="col-12 col-lg-3">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title"><i class="fa fa-bell-o fas fa-bell text-amber-500"></i> Recent Activities</h3>
            <a href="{{ route('report.sale') }}" class="text-indigo-600 text-decoration-none fw-bold" style="font-size:0.72rem;">View All →</a>
          </div>
          <div class="t-card-body p-3">
            @foreach($recentActivities as $act)
            <div class="activity-item">
              <div class="activity-icon">
                <i class="fa {{ $act['type'] == 'sale' ? 'fa-file-text-o fas fa-file-invoice' : 'fa-shopping-bag fas fa-shopping-bag' }}"></i>
              </div>
              <div>
                <div class="activity-title">{{ $act['title'] }}</div>
                <div class="activity-sub">{{ $act['category'] }}</div>
                <div class="activity-time">{{ $act['time_ago'] }}</div>
              </div>
              <div class="activity-amt">Rs {{ number_format($act['amount'], 0) }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ─── 6. FINANCIAL POSITION ─── --}}
    <div class="dash-section-label">
      <i class="fa fa-money fas fa-coins" style="color:#0ea5e9;"></i> Financial Position
    </div>

    <div class="row g-2 g-md-3 mb-4">
      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #3b82f6;">
          <div class="fin-hdr">
            <span class="fin-title">CUSTOMER RECEIVABLES</span>
            <i class="fa fa-file-text-o fas fa-file-invoice text-blue-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($customerReceivables, 0) }}</div>
          <a href="{{ route('report.customer.ledger') }}" class="fin-link">View Recovery Report →</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #f59e0b;">
          <div class="fin-hdr">
            <span class="fin-title">VENDOR PAYABLES</span>
            <i class="fa fa-money fas fa-hand-holding-usd text-amber-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($vendorPayables, 0) }}</div>
          <a href="{{ route('report.vendor.ledger') }}" class="fin-link">View Payable Report →</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #8b5cf6;">
          <div class="fin-hdr">
            <span class="fin-title">STOCK INVENTORY VALUE</span>
            <i class="fa fa-cubes fas fa-boxes text-purple-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($stockInventoryValue, 0) }}</div>
          <a href="{{ route('report.item_stock') }}" class="fin-link">View On-Hand Stock →</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #3b82f6;">
          <div class="fin-hdr">
            <span class="fin-title">CASH IN HAND</span>
            <i class="fa fa-wallet fas fa-wallet text-blue-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($cashInHand, 0) }}</div>
          <a href="{{ route('cashbook') }}" class="fin-link">View Ledger →</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #06b6d4;">
          <div class="fin-hdr">
            <span class="fin-title">EASY PAISA</span>
            <i class="fa fa-mobile fas fa-mobile-alt text-cyan-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($easyPaisaBalance, 0) }}</div>
          <a href="{{ route('view_all') }}" class="fin-link">View Ledger →</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-2">
        <div class="t-card fin-card" style="border-top:3px solid #06b6d4;">
          <div class="fin-hdr">
            <span class="fin-title">MEEZAN</span>
            <i class="fa fa-university fas fa-university text-cyan-500 fin-icon"></i>
          </div>
          <div class="fin-val">Rs {{ number_format($meezanBalance, 0) }}</div>
          <a href="{{ route('view_all') }}" class="fin-link">View Ledger →</a>
        </div>
      </div>
    </div>

    {{-- ─── 7. MONTHLY PERFORMANCE TREND ─── --}}
    <div class="dash-section-label">
      <i class="fas fa-chart-line" style="color:#10b981;"></i> Monthly Performance Trend
    </div>

    <div class="row g-2 g-md-3 mb-4">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="t-card spark-card">
          <div>
            <div class="spark-title">Total Sales</div>
            <div class="spark-val">Rs {{ number_format($totalSales, 0) }}</div>
            <div class="kpi-trend up" style="font-size:0.72rem;">↑ {{ $salesGrowth }}% vs last month</div>
          </div>
          <svg width="60" height="30" viewBox="0 0 60 30" fill="none"><path d="M2 24C10 20 18 26 26 14C34 2 42 18 58 6" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="t-card spark-card">
          <div>
            <div class="spark-title">Total Purchases</div>
            <div class="spark-val">Rs {{ number_format($totalPurchases, 0) }}</div>
            <div class="kpi-trend up" style="font-size:0.72rem;">↑ {{ $purchaseGrowth }}% vs last month</div>
          </div>
          <svg width="60" height="30" viewBox="0 0 60 30" fill="none"><path d="M2 20C12 24 22 10 32 16C42 22 50 8 58 12" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="t-card spark-card">
          <div>
            <div class="spark-title">Gross Profit</div>
            <div class="spark-val">Rs {{ number_format($grossProfit, 0) }}</div>
            <div class="kpi-trend up" style="font-size:0.72rem;">↑ {{ $grossProfitGrowth }}% vs last month</div>
          </div>
          <svg width="60" height="30" viewBox="0 0 60 30" fill="none"><path d="M2 22C14 18 24 24 34 12C44 0 50 14 58 8" stroke="#14b8a6" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="t-card spark-card">
          <div>
            <div class="spark-title">Net Profit</div>
            <div class="spark-val">Rs {{ number_format($netProfit, 0) }}</div>
            <div class="kpi-trend up" style="font-size:0.72rem;">↑ {{ $netProfitGrowth }}% vs last month</div>
          </div>
          <svg width="60" height="30" viewBox="0 0 60 30" fill="none"><path d="M2 26C12 20 22 22 32 14C42 6 50 16 58 4" stroke="#10b981" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
      </div>
    </div>

    {{-- ─── 8. LOW STOCK ALARM ─── --}}
    <div class="dash-section-label">
      <i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Low Stock Alarm
    </div>

    <div class="row g-3">
      <div class="col-12">
        <div class="t-card">
          <div class="t-card-header">
            <h3 class="t-card-title text-danger">
              <i class="fas fa-exclamation-triangle"></i> Low Stock Alert Products
              <span class="badge bg-danger-subtle text-danger rounded-pill px-2" style="font-size:0.75rem;">
                {{ count($lowStockChart['categories']) }} Items
              </span>
            </h3>
            <a href="{{ route('report.item_stock') }}" class="text-danger text-decoration-none fw-bold" style="font-size:0.75rem;">Manage Inventory →</a>
          </div>
          <div class="t-card-body p-3">
            <div id="lowStockBarChart" style="min-height: 260px;"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
// ── Live Clock Updating Every Second ──
function updateLiveClock() {
  const el = document.getElementById('dashClockDisplay');
  if(!el) return;
  const now = new Date();
  const options = { weekday: 'long', year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
  el.innerHTML = now.toLocaleString('en-US', options).replace(',', ' —');
}
setInterval(updateLiveClock, 1000);

document.addEventListener('DOMContentLoaded', function() {

  // 1. Sales Overview Spline Chart (Real Data)
  const salesChartStats = @json($salesChartStats ?? []);
  const salesData = @json($salesData);
  const salesLabels = @json($labels);
  const targetData = salesData.map(v => Math.round(v * 0.95));

  const salesOverviewOpts = {
    chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent' },
    series: [
      { name: 'Sales', data: salesData.length ? salesData : [0] },
      { name: 'Target', data: targetData.length ? targetData : [0] }
    ],
    xaxis: {
      categories: salesLabels.length ? salesLabels : ['Today'],
      labels: { style: { colors: '#94a3b8', fontSize: '10px' } },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: { labels: { formatter: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v), style: { colors: '#94a3b8', fontSize: '10px' } } },
    stroke: { curve: 'smooth', width: [3, 2], dashArray: [0, 5] },
    colors: ['#6366f1', '#cbd5e1'],
    fill: { type: ['gradient', 'solid'], gradient: { opacityFrom: 0.25, opacityTo: 0.05 } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' }
  };
  new ApexCharts(document.querySelector('#salesOverviewChart'), salesOverviewOpts).render();

  // Filter dropdown handler
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
          { name: 'Sales', data: (data.series && data.series[0] && data.series[0].data) ? data.series[0].data : [0] },
          { name: 'Target', data: (data.series && data.series[0] && data.series[0].data) ? data.series[0].data.map(v => Math.round(v * 0.95)) : [0] }
        ]
      });
    }
  });

  // 2. By Category Donut Chart (Real Data)
  const catDonutList = @json($catDonutList);
  const catSeries = catDonutList.map(item => item.amount);
  const catLabels = catDonutList.map(item => item.name);
  const catColors = catDonutList.map(item => item.color);
  const totalCatAmount = catSeries.reduce((a, b) => a + b, 0);

  const categoryDonutOpts = {
    chart: { type: 'donut', height: 190 },
    series: catSeries.length ? catSeries : [1],
    labels: catLabels.length ? catLabels : ['No Data'],
    colors: catColors.length ? catColors : ['#6366f1'],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
      pie: {
        donut: {
          size: '75%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'TOTAL',
              fontSize: '10px',
              fontWeight: 800,
              color: '#94a3b8',
              formatter: () => 'Rs ' + totalCatAmount.toLocaleString()
            }
          }
        }
      }
    }
  };
  new ApexCharts(document.querySelector('#categoryDonutChart'), categoryDonutOpts).render();

  // 3. Cash Flow Overview Bar Chart (Real Data)
  const cashFlowOpts = {
    chart: { type: 'bar', height: 220, toolbar: { show: false } },
    series: [{ name: 'Amount', data: [@json($todayIn), @json($todayOut), @json($totalIn), @json($totalOut)] }],
    xaxis: {
      categories: ['Today In', 'Today Out', 'Total In', 'Total Out'],
      labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } }
    },
    yaxis: { labels: { formatter: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v), style: { colors: '#94a3b8', fontSize: '10px' } } },
    plotOptions: { bar: { columnWidth: '45%', borderRadius: 6, distributed: true } },
    colors: ['#34d399', '#f87171', '#34d399', '#f87171'],
    legend: { show: false },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
  };
  new ApexCharts(document.querySelector('#cashFlowChart'), cashFlowOpts).render();

  // 4. Expense Breakdown Donut Chart (Real Data)
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
    chart: { type: 'donut', height: 160 },
    series: expHeadSeries.length && expHeadSeries.some(v => v > 0) ? expHeadSeries : [1],
    labels: expHeadNames.length ? expHeadNames : ['No Expenses'],
    colors: ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444'],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
      pie: {
        donut: {
          size: '75%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'TOTAL',
              fontSize: '9px',
              color: '#94a3b8',
              formatter: () => 'Rs {{ number_format($totalExpenses, 0) }}'
            }
          }
        }
      }
    }
  };
  new ApexCharts(document.querySelector('#expenseDonutChart'), expenseDonutOpts).render();

  // 5. Low Stock Alarm Bar Chart (Real Data)
  const lowStockData = @json($lowStockChart);
  const lowStockBarOpts = {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: lowStockData.series && lowStockData.series.length ? lowStockData.series : [
      { name: 'Stock Qty', data: [] },
      { name: 'Alert Level', data: [] }
    ],
    xaxis: {
      categories: lowStockData.categories && lowStockData.categories.length ? lowStockData.categories : ['None'],
      labels: {
        style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 },
        rotate: -45,
        rotateAlways: true,
        trim: true,
        maxHeight: 100
      },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '10px' } } },
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
    colors: ['#ff4d4d', '#818cf8'],
    dataLabels: { enabled: false },
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
  };
  new ApexCharts(document.querySelector('#lowStockBarChart'), lowStockBarOpts).render();

});
</script>
@endsection
