@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --bg: #f0f2f5;
  --card: #ffffff;
  --border: rgba(0,0,0,0.04);
  --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
  --text: #1e293b;
  --text-sec: #64748b;
  --text-muted: #94a3b8;
  --accent-1: #4f46e5;
  --accent-2: #0ea5e9;
  --accent-3: #ef4444;
  --accent-4: #f59e0b;
  --accent-5: #10b981;
  --accent-6: #f97316;
  --accent-7: #8b5cf6;
  --accent-8: #ec4899;
  --radius: 18px;
  --font: 'Inter', -apple-system, 'Segoe UI', sans-serif;
}

.dash * { font-family: var(--font); }

.dash {
  background: var(--bg);
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

/* Hero */
.dash-hero {
  position: relative;
  height: 220px;
  border-radius: var(--radius);
  overflow: hidden;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dash-hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('{{ asset("assets/images/memon_nimko_hero.png") }}');
  background-size: cover;
  background-position: center;
  animation: heroZoom 20s infinite alternate;
}

@keyframes heroZoom {
  from { transform: scale(1); }
  to { transform: scale(1.08); }
}

.dash-hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #fff;
}

.dash-hero-content h1 {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 6px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.3);
}

.dash-hero-content p {
  font-size: .95rem;
  color: rgba(255,255,255,0.8);
  font-weight: 500;
}

.dash-chip {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  background: rgba(255,255,255,.16);
  border: 1px solid rgba(255,255,255,.35);
  color: #fff;
  font-size: .78rem;
  font-weight: 700;
  padding: .35rem .85rem;
  border-radius: 999px;
  margin-top: .7rem;
  backdrop-filter: blur(4px);
}

.dash-chip i { color: #fcd34d; }

/* Glass Card */
.g-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  transition: all .3s ease;
  overflow: hidden;
}

.g-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.06), 0 20px 40px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

.g-card-body { padding: 1.25rem; }

/* Stat Card */
.stat-card {
  padding: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.stat-card::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px;
  height: 100%;
  border-radius: 0 4px 4px 0;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.stat-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: var(--text-muted);
  margin-bottom: 2px;
}

.stat-value {
  font-size: 26px;
  font-weight: 900;
  color: var(--text);
  line-height: 1.15;
}

.stat-sub {
  font-size: 12px;
  color: var(--text-muted);
  margin-top: 1px;
  font-weight: 500;
}

.stat-card.b1::after { background: linear-gradient(180deg, #4f46e5, #818cf8); }
.stat-card.b2::after { background: linear-gradient(180deg, #0ea5e9, #38bdf8); }
.stat-card.b3::after { background: linear-gradient(180deg, #ef4444, #f87171); }
.stat-card.b4::after { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
.stat-card.b5::after { background: linear-gradient(180deg, #10b981, #34d399); }
.stat-card.b6::after { background: linear-gradient(180deg, #f97316, #fb923c); }

/* Chart */
.chart-box {
  padding: 1.25rem;
}

.chart-hdr {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
  flex-wrap: wrap;
  gap: 8px;
}

.chart-title {
  font-size: 15px;
  font-weight: 800;
  color: var(--text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.chart-sub {
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 500;
}

.chart-select {
  background: #f1f5f9;
  border: 2px solid transparent;
  color: var(--text);
  border-radius: 10px;
  padding: 5px 12px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s;
}

.chart-select:focus {
  border-color: var(--accent-1);
  outline: none;
}

.chart-wrap {
  border-radius: 12px;
  overflow: hidden;
  background: #fafbfc;
  border: 1px solid rgba(0,0,0,0.03);
}

/* Low Stock */
.low-stock-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-radius: 10px;
  background: #fafbfc;
  border: 1px solid #f1f5f9;
  margin-bottom: 8px;
  transition: all .2s;
}

.low-stock-item:hover { background: #f1f5f9; }

.low-stock-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}

.low-stock-qty {
  font-size: 12px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
}

.low-stock-qty.danger {
  background: #fef2f2;
  color: #dc2626;
}

.low-stock-qty.warning {
  background: #fffbeb;
  color: #d97706;
}

.low-stock-count {
  font-size: 12px;
  font-weight: 800;
  background: #fef2f2;
  color: #dc2626;
  border-radius: 999px;
  padding: 4px 12px;
  border: 1px solid #fecaca;
}

@media (max-width: 768px) {
  .dash-hero { height: 160px; margin-bottom: 1rem; }
  .dash-hero-content h1 { font-size: 1.3rem; }
  .stat-value { font-size: 20px; }
  .stat-icon { width: 42px; height: 42px; font-size: 17px; }
}

@media (max-width: 575.98px) {
  .dash { padding-bottom: 1.5rem; }
  .dash-hero { height: 125px; border-radius: 12px; margin-bottom: 0.85rem; }
  .dash-hero-content h1 { font-size: 1.05rem; margin-bottom: 2px; }
  .dash-hero-content p { font-size: 0.78rem; }
  
  .stat-card {
    padding: 0.85rem 0.75rem;
    border-radius: 14px;
    align-items: center;
  }
  .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 14px;
  }
  .stat-label {
    font-size: 9.5px;
    letter-spacing: 0.3px;
  }
  .stat-value {
    font-size: 17px !important;
  }
  .stat-sub {
    font-size: 10px;
  }

  .chart-box { padding: 0.85rem; }
  .chart-hdr { margin-bottom: 8px; flex-wrap: wrap; gap: 4px; }
  .chart-title { font-size: 13px; }
  .chart-sub { font-size: 10.5px; }
  .chart-select { padding: 4px 8px; font-size: 11px; }

  .low-stock-item {
    padding: 8px 10px;
    border-radius: 8px;
    gap: 6px;
  }
  .low-stock-name {
    font-size: 11.5px;
    line-height: 1.3;
    max-width: 65%;
  }
  .low-stock-qty {
    font-size: 10px;
    padding: 2px 7px;
  }
}
</style>

<div class="dash">
  <div class="container-fluid px-2 px-md-4 py-2 py-md-3">

    {{-- HERO --}}
    <div class="dash-hero">
      <div class="dash-hero-bg"></div>
      <div class="dash-hero-content">
        <h1>Welcome to {{ \App\Models\Setting::where('key','software_name')->value('value') ?? 'Memon Nimko' }}</h1>
        <p>Management & Reporting Dashboard</p>
        <span class="dash-chip"><i class="fas fa-calendar-alt"></i> Data for {{ now()->format('F Y') }}</span>
      </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b1">
          <div>
            <div class="stat-label">Categories</div>
            <div class="stat-value">{{ $categoryCount }}</div>
            <div class="stat-sub">Total categories</div>
          </div>
          <div class="stat-icon" style="background:#eef2ff;color:#4f46e5;">
            <i class="fas fa-layer-group"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b2">
          <div>
            <div class="stat-label">Subcategories</div>
            <div class="stat-value">{{ $subcategoryCount }}</div>
            <div class="stat-sub">Total subcategories</div>
          </div>
          <div class="stat-icon" style="background:#f0f9ff;color:#0ea5e9;">
            <i class="fas fa-sitemap"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b3">
          <div>
            <div class="stat-label">Products</div>
            <div class="stat-value">{{ $productCount }}</div>
            <div class="stat-sub">Total products</div>
          </div>
          <div class="stat-icon" style="background:#fef2f2;color:#ef4444;">
            <i class="fas fa-box-open"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b4">
          <div>
            <div class="stat-label">Customers</div>
            <div class="stat-value">{{ $customerscount }}</div>
            <div class="stat-sub">Total customers</div>
          </div>
          <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
            <i class="fas fa-users"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- FINANCIAL CARDS --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b5">
          <div>
            <div class="stat-label">Total Purchases</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($totalPurchases, 0) }}</div>
            <div class="stat-sub">This month</div>
          </div>
          <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b3">
          <div>
            <div class="stat-label">Purchase Returns</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($totalPurchaseReturns, 0) }}</div>
            <div class="stat-sub">Returned value</div>
          </div>
          <div class="stat-icon" style="background:#fef2f2;color:#ef4444;">
            <i class="fas fa-undo-alt"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b6">
          <div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($totalSales, 0) }}</div>
            <div class="stat-sub">This month</div>
          </div>
          <div class="stat-icon" style="background:#fff7ed;color:#f97316;">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b4">
          <div>
            <div class="stat-label">Sales Returns</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($totalSalesReturns, 0) }}</div>
            <div class="stat-sub">Returned value</div>
          </div>
          <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
            <i class="fas fa-undo"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- PROFIT / FINANCIAL SUMMARY --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b6">
          <div>
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($totalExpenses, 0) }}</div>
            <div class="stat-sub">This month</div>
          </div>
          <div class="stat-icon" style="background:#fff7ed;color:#f97316;">
            <i class="fas fa-file-invoice"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b2">
          <div>
            <div class="stat-label">Net Sales</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($netSales, 0) }}</div>
            <div class="stat-sub">After returns</div>
          </div>
          <div class="stat-icon" style="background:#f0f9ff;color:#0ea5e9;">
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b1">
          <div>
            <div class="stat-label">Net Purchases</div>
            <div class="stat-value" style="font-size:20px;">Rs {{ number_format($netPurchases, 0) }}</div>
            <div class="stat-sub">After returns</div>
          </div>
          <div class="stat-icon" style="background:#eef2ff;color:#4f46e5;">
            <i class="fas fa-truck"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="g-card stat-card b5" style="{{ $grossProfit < 0 ? 'background:#fef2f2;' : '' }}">
          <div>
            <div class="stat-label" style="{{ $grossProfit < 0 ? 'color:#b91c1c;' : '' }}">Gross Profit</div>
            <div class="stat-value" style="font-size:22px;{{ $grossProfit < 0 ? 'color:#dc2626;' : '' }}">Rs {{ number_format($grossProfit, 0) }}</div>
            <div class="stat-sub">Net sales - purchases - expenses</div>
          </div>
          <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
            <i class="fas fa-hand-holding-usd"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mb-3 mb-md-4">
      {{-- Sales Chart --}}
      <div class="col-12 col-lg-6">
        <div class="g-card chart-box">
          <div class="chart-hdr">
            <div>
              <div class="chart-title"><i class="fas fa-chart-line" style="color:var(--accent-1);"></i>Sales Report</div>
              <div class="chart-sub">Revenue overview — this month</div>
            </div>
            <select id="salesFilter" class="chart-select">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
          <div class="chart-wrap">
            <div id="salesChart" style="min-height:260px;"></div>
          </div>
        </div>
      </div>

      {{-- Purchase Chart --}}
      <div class="col-12 col-lg-6">
        <div class="g-card chart-box">
          <div class="chart-hdr">
            <div>
              <div class="chart-title"><i class="fas fa-chart-bar" style="color:var(--accent-5);"></i>Purchase Report</div>
              <div class="chart-sub">Procurement spending — this month</div>
            </div>
            <select id="purchaseFilter" class="chart-select">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
          <div class="chart-wrap">
            <div id="purchaseChart" style="min-height:260px;"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3 mb-md-4">
      {{-- Category Products --}}
      <div class="col-12 col-lg-7">
        <div class="g-card chart-box">
          <div class="chart-hdr">
            <div>
              <div class="chart-title"><i class="fas fa-cubes" style="color:var(--accent-7);"></i>Category Wise Products</div>
              <div class="chart-sub">Click a bar to view products</div>
            </div>
          </div>
          <div class="chart-wrap">
            <div id="categoryChart" style="min-height:280px;"></div>
          </div>
        </div>
      </div>

      {{-- Low Stock --}}
      <div class="col-12 col-lg-5">
        <div class="g-card chart-box">
          <div class="chart-hdr">
            <div>
              <div class="chart-title"><i class="fas fa-exclamation-triangle" style="color:var(--accent-3);"></i>Low Stock Alerts</div>
              <div class="chart-sub">Products below alert quantity</div>
            </div>
            <span class="low-stock-count">{{ count($lowStockChart['categories']) }} items</span>
          </div>
          <div style="max-height:340px;overflow-y:auto;">
            @forelse($lowStockChart['categories'] as $i => $item)
            <div class="low-stock-item">
              <div class="low-stock-name">{{ $item }}</div>
              @php $qty = $lowStockChart['series'][0]['data'][$i]; @endphp
              <span class="low-stock-qty {{ $qty == 0 ? 'danger' : 'warning' }}">{{ $qty }} left</span>
            </div>
            @empty
            <div style="text-align:center;padding:2rem;color:var(--text-muted);">
              <i class="fas fa-check-circle" style="font-size:2rem;color:#10b981;margin-bottom:8px;display:block;"></i>
              All products are in stock
            </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
// ===================== SALES CHART =====================
const salesData = @json($salesChartStats);
let salesChart;

function renderSalesChart(type='daily') {
  const d = salesData[type];
  const isMobile = window.innerWidth < 576;
  const opts = {
    chart: { type:'bar', height: isMobile ? 260 : 340, toolbar:{show:!isMobile,tools:{download:true,zoom:false,pan:false}}, foreColor:'#94a3b8', background:'transparent', animations:{enabled:true,easing:'easeinout',speed:800} },
    series: d.series,
    xaxis: { categories:d.categories, labels:{rotate: isMobile ? -45 : 0, style:{colors:'#94a3b8',fontSize: isMobile ? '9px' : '11px',fontWeight:600}}, axisBorder:{show:false}, axisTicks:{show:false} },
    yaxis: { labels:{style:{colors:'#94a3b8',fontSize: isMobile ? '10px' : '12px',fontWeight:600}, formatter:v=>'Rs '+v.toLocaleString()} },
    dataLabels:{enabled:false},
    plotOptions:{ bar:{horizontal:false,columnWidth: isMobile ? '65%' : '45%',borderRadius: isMobile ? 4 : 8} },
    fill:{ type:'gradient', gradient:{shade:'light',type:'vertical',gradientToColors:['#818cf8'],stops:[0,100]} },
    colors:['#4f46e5'],
    tooltip:{ theme:'light', y:{formatter:v=>'Rs '+v.toLocaleString()} },
    grid:{ borderColor:'#f1f5f9', strokeDashArray:4, xaxis:{lines:{show:false}} },
    responsive: [{
      breakpoint: 576,
      options: {
        chart: { height: 260 },
        plotOptions: { bar: { columnWidth: '70%', borderRadius: 4 } }
      }
    }]
  };
  if(salesChart) salesChart.destroy();
  salesChart = new ApexCharts(document.querySelector('#salesChart'), opts);
  salesChart.render();
}
renderSalesChart();
document.getElementById('salesFilter')?.addEventListener('change', function(){ renderSalesChart(this.value); });

// ===================== PURCHASE CHART =====================
const purchaseData = @json($purchaseChartStats);
let purchaseChart;

function renderPurchaseChart(type='daily') {
  const d = purchaseData[type];
  const isMobile = window.innerWidth < 576;
  const opts = {
    chart: { type:'bar', height: isMobile ? 260 : 340, toolbar:{show:!isMobile,tools:{download:true,zoom:false,pan:false}}, foreColor:'#94a3b8', background:'transparent', animations:{enabled:true,easing:'easeinout',speed:800} },
    series: d.series,
    xaxis: { categories:d.categories, labels:{rotate: isMobile ? -45 : 0, style:{colors:'#94a3b8',fontSize: isMobile ? '9px' : '11px',fontWeight:600}}, axisBorder:{show:false}, axisTicks:{show:false} },
    yaxis: { labels:{style:{colors:'#94a3b8',fontSize: isMobile ? '10px' : '12px',fontWeight:600}, formatter:v=>'Rs '+v.toLocaleString()} },
    dataLabels:{enabled:false},
    plotOptions:{ bar:{horizontal:false,columnWidth: isMobile ? '65%' : '45%',borderRadius: isMobile ? 4 : 8} },
    fill:{ type:'gradient', gradient:{shade:'light',type:'vertical',gradientToColors:['#34d399'],stops:[0,100]} },
    colors:['#10b981'],
    tooltip:{ theme:'light', y:{formatter:v=>'Rs '+v.toLocaleString()} },
    grid:{ borderColor:'#f1f5f9', strokeDashArray:4, xaxis:{lines:{show:false}} },
    responsive: [{
      breakpoint: 576,
      options: {
        chart: { height: 260 },
        plotOptions: { bar: { columnWidth: '70%', borderRadius: 4 } }
      }
    }]
  };
  if(purchaseChart) purchaseChart.destroy();
  purchaseChart = new ApexCharts(document.querySelector('#purchaseChart'), opts);
  purchaseChart.render();
}
renderPurchaseChart();
document.getElementById('purchaseFilter')?.addEventListener('change', function(){ renderPurchaseChart(this.value); });

// ===================== CATEGORY CHART =====================
const catData = @json($categoryProductChart);
document.addEventListener('DOMContentLoaded', function(){
  const isMobile = window.innerWidth < 576;
  const catCount = catData.categories.length;
  // On mobile: horizontal bars so labels don't overlap
  const mobileHeight = isMobile ? Math.max(280, catCount * 32 + 40) : 380;
  const mobileHorizontal = isMobile && catCount > 6;
  new ApexCharts(document.querySelector('#categoryChart'), {
    chart: {
      type: 'bar',
      height: mobileHeight,
      toolbar: { show: false },
      foreColor: '#94a3b8',
      background: 'transparent',
      animations: { enabled: true, easing: 'easeinout', speed: 800 }
    },
    series: catData.series,
    plotOptions: {
      bar: {
        horizontal: mobileHorizontal,
        columnWidth: isMobile ? '65%' : '50%',
        borderRadius: isMobile ? 4 : 8,
        distributed: true
      }
    },
    xaxis: {
      categories: catData.categories,
      labels: {
        rotate: -45,
        rotateAlways: true,
        hideOverlappingLabels: true,
        trim: true,
        maxHeight: 100,
        style: { colors: '#64748b', fontSize: isMobile ? '9.5px' : '11px', fontWeight: 600 }
      },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: {
      labels: {
        style: { colors: '#94a3b8', fontSize: isMobile ? '10px' : '12px', fontWeight: 600 },
        formatter: v => v.toLocaleString()
      }
    },
    colors: ['#4f46e5','#0ea5e9','#ec4899','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#6366f1'],
    legend: { show: !isMobile, position: 'bottom', fontSize: '10px', markers: { size: 8 } },
    dataLabels: { enabled: false },
    tooltip: { theme: 'light', y: { formatter: v => v.toLocaleString() + ' Products' } },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4, xaxis: { lines: { show: false } } },
    responsive: [{
      breakpoint: 576,
      options: {
        chart: { height: mobileHeight },
        plotOptions: { bar: { columnWidth: '70%', borderRadius: 4 } }
      }
    }]
  }).render();
});
</script>
@endsection
