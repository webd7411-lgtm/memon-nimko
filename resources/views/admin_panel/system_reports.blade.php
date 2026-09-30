@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary: #4f46e5;
        --primary-light: #818cf8;
        --primary-soft: #eef2ff;
        --accent-1: #0ea5e9;
        --accent-2: #8b5cf6;
        --accent-3: #ec4899;
        --bg-page: #f8fafc;
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --card-shadow: 0 1px 4px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03);
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
    }

    body, html {
        overflow-x: hidden !important;
        max-width: 100vw;
    }

    .reports-page * {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .reports-page {
        background: var(--bg-page);
        min-height: 100vh;
        padding: 16px 20px;
        position: relative;
        overflow-x: hidden !important;
        width: 100%;
        max-width: 100%;
    }

    .glass-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 14px;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s;
    }
    .glass-card:hover {
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
        transform: translateY(-1.5px);
    }

    /* ═══════ SMALL BEAUTIFUL ATTRACTIVE STAT CARDS ═══════ */
    .report-section-hdr {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #475569;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-card {
        padding: 10px 14px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        border-radius: 12px !important;
        height: 100%;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 3.5px;
        height: 100%;
        border-radius: 0 4px 4px 0;
    }
    .stat-card .stat-icon {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px !important;
        flex-shrink: 0;
    }
    .stat-card .stat-label {
        font-size: 10px !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 1px;
        line-height: 1.2;
    }
    .stat-card .stat-value {
        font-size: 17px !important;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
        overflow-wrap: anywhere;
    }
    .stat-card .stat-sub {
        font-size: 10.5px !important;
        color: var(--text-muted);
        margin-top: 1px;
        font-weight: 500;
        line-height: 1.1;
    }

    .stat-card.border-accent-1::after { background: linear-gradient(180deg, #4f46e5, #818cf8); }
    .stat-card.border-accent-2::after { background: linear-gradient(180deg, #0ea5e9, #38bdf8); }
    .stat-card.border-accent-3::after { background: linear-gradient(180deg, #ef4444, #f87171); }
    .stat-card.border-accent-4::after { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
    .stat-card.border-accent-5::after { background: linear-gradient(180deg, #10b981, #34d399); }
    .stat-card.border-accent-6::after { background: linear-gradient(180deg, #f97316, #fb923c); }
    .stat-card.border-accent-7::after { background: linear-gradient(180deg, #8b5cf6, #a78bfa); }
    .stat-card.border-accent-8::after { background: linear-gradient(180deg, #ec4899, #f472b6); }

    /* ═══════ COMPACT FILTER HEADER CARD ═══════ */
    .filter-card {
        padding: 12px 18px;
        margin-bottom: 14px;
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
    }
    .filter-card label {
        color: var(--text-secondary);
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .filter-card .form-control, .filter-card .form-select {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: var(--text-primary);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        height: 36px;
        transition: all 0.2s;
    }
    .filter-card .form-control:focus, .filter-card .form-select:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        outline: none;
    }
    .filter-card .btn-filter {
        background: linear-gradient(135deg, var(--primary), var(--accent-2));
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 700;
        font-size: 12.5px;
        color: #fff;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(79,70,229,0.25);
    }
    .filter-card .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(79,70,229,0.35);
        color: #fff;
    }
    .filter-card .btn-export-pdf {
        background: #dc2626;
        border: none;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 12px;
        color: #fff !important;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .filter-card .btn-export-pdf:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }
    .filter-card .btn-export-bw {
        background: #334155;
        border: none;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 12px;
        color: #fff !important;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .filter-card .btn-export-bw:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }
    .filter-card .btn-reset {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 12px;
        color: var(--text-secondary);
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .filter-card .btn-reset:hover {
        background: #e2e8f0;
        color: var(--text-primary);
    }

    /* ═══════ SMALL BEAUTIFUL CHARTS ═══════ */
    .chart-container {
        padding: 14px 16px !important;
        border-radius: 14px !important;
        margin-bottom: 12px;
    }
    .chart-container .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 6px;
    }
    .chart-container .chart-title {
        font-size: 13.5px !important;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
    }
    .chart-container .chart-sub {
        font-size: 10.5px !important;
        color: var(--text-muted);
        font-weight: 500;
    }
    .chart-container .chart-filter-select {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: var(--text-primary);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        height: 30px;
        transition: all 0.2s;
    }
    .chart-container .chart-filter-select:focus {
        border-color: var(--primary);
        outline: none;
    }
    .chart-wrapper {
        border-radius: 10px;
        overflow: hidden;
        background: #fafbfc;
        border: 1px solid rgba(0,0,0,0.03);
    }

    /* ═══════ MODAL ═══════ */
    .modal-custom {
        background: rgba(15,23,42,0.6);
        backdrop-filter: blur(8px);
    }
    .modal-custom .modal-content {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        color: var(--text-primary);
    }
    .modal-custom .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 18px;
    }
    .modal-custom .modal-body { padding: 16px; }
    .modal-custom .modal-title { font-weight: 800; font-size: 15px; color: var(--text-primary); }

    /* ═══════ RESPONSIVE MOBILE POLISH & NO HORIZONTAL SCROLL ═══════ */
    @media (max-width: 767.98px) {
        .reports-page { padding: 10px; }
        .filter-card { padding: 12px 14px; }
        .filter-card .form-control, .filter-card .form-select { height: 38px; font-size: 13.5px; }
        .filter-card .btn-filter,
        .filter-card .btn-export-pdf,
        .filter-card .btn-export-bw,
        .filter-card .btn-reset {
            flex: 1 1 calc(50% - 4px);
            min-height: 36px;
            font-size: 11.5px;
        }

        .stat-card { padding: 9px 11px !important; }
        .stat-card .stat-icon { width: 32px !important; height: 32px !important; font-size: 13px !important; }
        .stat-card .stat-label { font-size: 9px !important; }
        .stat-card .stat-value { font-size: 15px !important; }
        .stat-card .stat-sub { font-size: 9.5px !important; }

        .chart-container { padding: 12px 10px !important; }
        .chart-container .chart-title { font-size: 12.5px !important; }
        .chart-container .chart-sub { font-size: 10px !important; }
        .apexcharts-toolbar { display: none !important; }
        .apexcharts-legend { font-size: 10px !important; flex-wrap: wrap !important; }
    }

    @media (max-width: 575.98px) {
        .reports-page { padding: 8px; }
        .stat-card { padding: 8px 10px !important; }
        .stat-card .stat-icon { width: 28px !important; height: 28px !important; font-size: 12px !important; border-radius: 8px !important; }
        .stat-card .stat-label { font-size: 8.5px !important; }
        .stat-card .stat-value { font-size: 14px !important; }
        .chart-container { padding: 10px 8px !important; }
    }
</style>

<div class="reports-page">
    
    {{-- ═══════ COMPACT FILTER HEADER CARD ═══════ --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('System.Reports') }}" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label><i class="fas fa-calendar-alt me-1 text-primary"></i>Start Month</label>
                <input type="month" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-6 col-md-3">
                <label><i class="fas fa-calendar-alt me-1 text-primary"></i>End Month</label>
                <input type="month" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-12 col-md-6 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-filter flex-grow-1">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <a href="{{ route('System.Reports.PDF', request()->all()) }}" class="btn btn-export-pdf" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('System.Reports.PDF.BW', request()->all()) }}" class="btn btn-export-bw" target="_blank">
                    <i class="fas fa-file-alt"></i> B&amp;W
                </a>
                <a href="{{ route('System.Reports') }}" class="btn btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ═══════ SECTION 1: INVENTORY & ENTITIES (SMALL ATTRACTIVE CARDS) ═══════ --}}
    <div class="report-section-hdr">
        <i class="fas fa-cubes text-primary"></i> Inventory & Master Entities
    </div>
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-1">
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
            <div class="glass-card stat-card border-accent-2">
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
            <div class="glass-card stat-card border-accent-3">
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
            <div class="glass-card stat-card border-accent-4">
                <div>
                    <div class="stat-label">Customers</div>
                    <div class="stat-value">{{ $customerscount }}</div>
                    <div class="stat-sub">Registered accounts</div>
                </div>
                <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ SECTION 2: TRADE & TURNOVER ═══════ --}}
    <div class="report-section-hdr">
        <i class="fas fa-arrows-split-up-and-left text-success"></i> Trade & Turnover Summary
    </div>
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-5">
                <div>
                    <div class="stat-label">Total Purchases</div>
                    <div class="stat-value">Rs {{ number_format($totalPurchases, 0) }}</div>
                    <div class="stat-sub">Procurement value</div>
                </div>
                <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-3">
                <div>
                    <div class="stat-label">Purchase Returns</div>
                    <div class="stat-value">Rs {{ number_format($totalPurchaseReturns, 0) }}</div>
                    <div class="stat-sub">Returned to vendors</div>
                </div>
                <div class="stat-icon" style="background:#fef2f2;color:#ef4444;">
                    <i class="fas fa-undo-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-5">
                <div>
                    <div class="stat-label">Total Sales</div>
                    <div class="stat-value">Rs {{ number_format($totalSales, 0) }}</div>
                    <div class="stat-sub">Gross sales value</div>
                </div>
                <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-6">
                <div>
                    <div class="stat-label">Sales Returns</div>
                    <div class="stat-value">Rs {{ number_format($totalSalesReturns, 0) }}</div>
                    <div class="stat-sub">Customer returns</div>
                </div>
                <div class="stat-icon" style="background:#fff7ed;color:#f97316;">
                    <i class="fas fa-undo"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ SECTION 3: PROFIT & LOSS METRICS ═══════ --}}
    <div class="report-section-hdr">
        <i class="fas fa-coins text-warning"></i> Profit & Loss Highlights
    </div>
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-5">
                <div>
                    <div class="stat-label">Net Sales</div>
                    <div class="stat-value">Rs {{ number_format($netSales, 0) }}</div>
                    <div class="stat-sub">Sales - Returns</div>
                </div>
                <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-1">
                <div>
                    <div class="stat-label">Net Purchases</div>
                    <div class="stat-value">Rs {{ number_format($netPurchases, 0) }}</div>
                    <div class="stat-sub">Purchases - Returns</div>
                </div>
                <div class="stat-icon" style="background:#eef2ff;color:#4f46e5;">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card border-accent-3">
                <div>
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value">Rs {{ number_format($totalExpenses, 0) }}</div>
                    <div class="stat-sub">Operating costs</div>
                </div>
                <div class="stat-icon" style="background:#fef2f2;color:#ef4444;">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card stat-card" style="{{ $grossProfit >= 0 ? 'border-left:3.5px solid #10b981;' : 'border-left:3.5px solid #ef4444;' }}">
                <div>
                    <div class="stat-label">{{ $grossProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
                    <div class="stat-value" style="{{ $grossProfit >= 0 ? 'color:#10b981;' : 'color:#ef4444;' }}">
                        Rs {{ number_format(abs($grossProfit), 0) }}
                    </div>
                    <div class="stat-sub">{{ $grossProfit >= 0 ? 'Net Sales - Pur - Exp' : 'Loss incurred' }}</div>
                </div>
                <div class="stat-icon" style="background:{{ $grossProfit >= 0 ? '#ecfdf5' : '#fef2f2' }};color:{{ $grossProfit >= 0 ? '#10b981' : '#ef4444' }};">
                    <i class="fas fa-{{ $grossProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ SMALL BEAUTIFUL CHARTS (ROW 1: SALES & PURCHASE) ═══════ --}}
    <div class="row g-2 g-md-3 mb-2">
        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-chart-line me-2" style="color:var(--primary);"></i>Sales Trend</div>
                        <div class="chart-sub">Revenue for selected period</div>
                    </div>
                    <select id="salesFilter" class="chart-filter-select">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="chart-wrapper">
                    <div id="salesReportChart" style="height:230px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-chart-bar me-2" style="color:#10b981;"></i>Purchase Trend</div>
                        <div class="chart-sub">Procurement spending overview</div>
                    </div>
                    <select id="purchaseFilter" class="chart-filter-select">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="chart-wrapper">
                    <div id="purchaseReportChart" style="height:230px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ SMALL BEAUTIFUL CHARTS (ROW 2: P&L BREAKDOWN & SUMMARY) ═══════ --}}
    <div class="row g-2 g-md-3 mb-2">
        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-chart-pie me-2" style="color:#8b5cf6;"></i>Profit / Loss Ratio</div>
                        <div class="chart-sub">Revenue vs Cost vs Expense</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <div id="profitLossChart" style="height:230px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-scale-balanced me-2" style="color:#f59e0b;"></i>Financial Comparison</div>
                        <div class="chart-sub">Summary overview</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <div id="summaryChart" style="height:220px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ SMALL BEAUTIFUL CHARTS (ROW 3: CATEGORY STOCK & EXPENSE) ═══════ --}}
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-cubes me-2" style="color:var(--accent-2);"></i>Category Wise Products</div>
                        <div class="chart-sub">Tap bar to view products</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <div id="categoryStockChart" style="height:240px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card chart-container">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-file-invoice me-2" style="color:#f59e0b;"></i>Account Wise Expense</div>
                        <div class="chart-sub">Distribution by head</div>
                    </div>
                </div>
                <div class="mb-2">
                    <select id="expenseHeadSelect" class="chart-filter-select w-100">
                        <option value="">Select Account Head</option>
                        @foreach($expenseChartData as $hid => $head)
                        <option value="{{ $hid }}">{{ $head['head_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="chart-wrapper">
                    <div id="expenseAccountChart" style="height:220px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CATEGORY PRODUCTS MODAL --}}
<div class="modal fade" id="categoryProductsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="productSearch" class="form-control" placeholder="Search product..." onkeyup="searchProducts()">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Product Name</th>
                                <th style="width:120px;" class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody"></tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ===================== SALES CHART =====================
    const salesChartStats = @json($salesChartStats);
    let salesChart;

    function getSalesChartOpts(type = 'daily') {
        const data = salesChartStats[type];
        return {
            chart: {
                type: 'bar',
                height: 230,
                toolbar: { show: false },
                foreColor: '#94a3b8',
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            responsive: [{
                breakpoint: 768,
                options: { chart: { height: 190, toolbar: { show: false } }, plotOptions: { bar: { columnWidth: '50%', borderRadius: 5 } } }
            }],
            series: data.series,
            xaxis: {
                categories: data.categories,
                labels: { style: { colors: '#94a3b8', fontSize: '10.5px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 },
                    formatter: val => 'Rs ' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val)
                }
            },
            dataLabels: { enabled: false },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '38%',
                    borderRadius: 6,
                    distributed: false
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    gradientToColors: ['#818cf8'],
                    stops: [0, 100]
                }
            },
            colors: ['#4f46e5'],
            tooltip: {
                theme: 'light',
                y: { formatter: val => 'Rs ' + val.toLocaleString() }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            }
        };
    }

    function renderSalesChart(type = 'daily') {
        if (salesChart) salesChart.destroy();
        salesChart = new ApexCharts(document.querySelector("#salesReportChart"), getSalesChartOpts(type));
        salesChart.render();
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderSalesChart();
        document.getElementById('salesFilter')?.addEventListener('change', function() {
            renderSalesChart(this.value);
        });
    });

    // ===================== PURCHASE CHART =====================
    const purchaseChartStats = @json($purchaseChartStats);
    let purchaseChart;

    function getPurchaseChartOpts(type = 'daily') {
        const data = purchaseChartStats[type];
        return {
            chart: {
                type: 'bar',
                height: 230,
                toolbar: { show: false },
                foreColor: '#94a3b8',
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            responsive: [{
                breakpoint: 768,
                options: { chart: { height: 190, toolbar: { show: false } }, plotOptions: { bar: { columnWidth: '50%', borderRadius: 5 } } }
            }],
            series: data.series,
            xaxis: {
                categories: data.categories,
                labels: { style: { colors: '#94a3b8', fontSize: '10.5px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 },
                    formatter: val => 'Rs ' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val)
                }
            },
            dataLabels: { enabled: false },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '38%',
                    borderRadius: 6
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    gradientToColors: ['#34d399'],
                    stops: [0, 100]
                }
            },
            colors: ['#10b981'],
            tooltip: {
                theme: 'light',
                y: { formatter: val => 'Rs ' + val.toLocaleString() }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            }
        };
    }

    function renderPurchaseChart(type = 'daily') {
        if (purchaseChart) purchaseChart.destroy();
        purchaseChart = new ApexCharts(document.querySelector("#purchaseReportChart"), getPurchaseChartOpts(type));
        purchaseChart.render();
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderPurchaseChart();
        document.getElementById('purchaseFilter')?.addEventListener('change', function() {
            renderPurchaseChart(this.value);
        });
    });

    // ===================== CATEGORY STOCK CHART =====================
    const categoryStockData = @json($categoryProductChart);

    document.addEventListener('DOMContentLoaded', function() {
        const options = {
            chart: {
                type: 'bar',
                height: 240,
                toolbar: { show: false },
                foreColor: '#94a3b8',
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 800 },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const categoryId = categoryStockData.category_ids[config.dataPointIndex];
                        const categoryName = categoryStockData.categories[config.dataPointIndex];
                        loadCategoryProducts(categoryId, categoryName);
                    }
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 210 },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            rotateAlways: true,
                            style: { colors: '#64748b', fontSize: '9.5px', fontWeight: 600 },
                            offsetY: 3
                        }
                    },
                    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '9.5px', fontWeight: 600 } } },
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
                    tooltip: { theme: 'light', y: { formatter: val => val.toLocaleString() + ' Products' } }
                }
            }],
            series: categoryStockData.series,
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '42%',
                    borderRadius: 6,
                    distributed: true
                }
            },
            xaxis: {
                categories: categoryStockData.categories,
                labels: {
                    rotate: -35,
                    rotateAlways: true,
                    hideOverlappingLabels: true,
                    trim: true,
                    maxHeight: 70,
                    style: { colors: '#64748b', fontSize: '10.5px', fontWeight: 600 }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 },
                    formatter: val => val.toLocaleString()
                }
            },
            colors: ['#4f46e5', '#0ea5e9', '#ec4899', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#6366f1'],
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light',
                y: { formatter: val => val.toLocaleString() + ' Products' }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            }
        };

        new ApexCharts(document.querySelector("#categoryStockChart"), options).render();
    });

    // ===================== CATEGORY PRODUCTS MODAL =====================
    let activeCategoryId = null;
    let activeCategoryName = '';

    function loadCategoryProducts(categoryId, categoryName, page = 1) {
        activeCategoryId = categoryId;
        activeCategoryName = categoryName;
        $('#modalTitle').text(categoryName + ' – Products');
        $('#categoryProductsModal').modal('show');
        let search = $('#productSearch').val();

        $.get(`/category-products/${categoryId}`, { page, search }, function(res) {
            let rows = '';
            res.data.forEach((item, index) => {
                rows += `<tr>
                    <td>${((res.current_page - 1) * 100) + index + 1}</td>
                    <td>${item.item_name}</td>
                    <td class="text-end fw-bold">${item.stock}</td>
                </tr>`;
            });
            $('#productsTableBody').html(rows);
            let pagination = '';
            for (let i = 1; i <= res.last_page; i++) {
                pagination += `<li class="page-item ${i === res.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadCategoryProducts(${categoryId}, '${categoryName}', ${i})">${i}</a>
                </li>`;
            }
            $('#pagination').html(pagination);
        });
    }

    function searchProducts() {
        loadCategoryProducts(activeCategoryId, activeCategoryName, 1);
    }

    // ===================== EXPENSE CHART =====================
    let expenseChart;
    const expenseData = @json($expenseChartData);

    document.getElementById('expenseHeadSelect')?.addEventListener('change', function() {
        const headId = this.value;
        if (!headId) return;
        const data = expenseData[headId];
        const options = {
            chart: {
                type: 'bar',
                height: 220,
                toolbar: { show: false },
                foreColor: '#94a3b8',
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            responsive: [{
                breakpoint: 768,
                options: { chart: { height: 190, toolbar: { show: false } } }
            }],
            series: data.series,
            xaxis: {
                categories: data.categories,
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 },
                    formatter: val => 'Rs ' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val)
                }
            },
            dataLabels: { enabled: false },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 5,
                    barHeight: '45%'
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'horizontal',
                    gradientToColors: ['#fbbf24'],
                    stops: [0, 100]
                }
            },
            colors: ['#f59e0b'],
            tooltip: {
                theme: 'light',
                y: { formatter: val => 'Rs ' + val.toLocaleString() }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            }
        };
        if (expenseChart) expenseChart.destroy();
        expenseChart = new ApexCharts(document.querySelector("#expenseAccountChart"), options);
        expenseChart.render();
    });

    // ===================== PROFIT / LOSS PIE CHART =====================
    document.addEventListener('DOMContentLoaded', function() {
        const netSales = {{ $netSales }};
        const netPurchases = {{ $netPurchases }};
        const totalExpenses = {{ $totalExpenses }};

        new ApexCharts(document.querySelector('#profitLossChart'), {
            chart: { type: 'donut', height: 230, background: 'transparent', animations: { enabled: true, easing: 'easeinout', speed: 800 } },
            responsive: [{
                breakpoint: 768,
                options: { chart: { height: 200 }, legend: { position: 'bottom', fontSize: '10px' } }
            }],
            series: [netSales, netPurchases, totalExpenses],
            labels: ['Net Sales', 'Net Purchases', 'Total Expenses'],
            colors: ['#10b981', '#4f46e5', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '12px', fontWeight: 700 },
                            value: { show: true, fontSize: '14px', fontWeight: 800, formatter: val => 'Rs ' + parseInt(val).toLocaleString() }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11.5px', fontWeight: 600, labels: { colors: '#64748b' } },
            tooltip: { theme: 'light', y: { formatter: val => 'Rs ' + val.toLocaleString() } }
        }).render();
    });

    // ===================== SUMMARY BAR CHART =====================
    document.addEventListener('DOMContentLoaded', function() {
        const summaryData = [
            { name: 'Net Sales', data: [{{ $netSales }}] },
            { name: 'Net Purchases', data: [{{ $netPurchases }}] },
            { name: 'Total Expenses', data: [{{ $totalExpenses }}] },
            { name: '{{ $grossProfit >= 0 ? "Net Profit" : "Net Loss" }}', data: [{{ abs($grossProfit) }}] }
        ];

        new ApexCharts(document.querySelector('#summaryChart'), {
            chart: { type: 'bar', height: 220, toolbar: { show: false }, foreColor: '#94a3b8', background: 'transparent', animations: { enabled: true, easing: 'easeinout', speed: 800 } },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 190 },
                    dataLabels: { enabled: false },
                    legend: { position: 'bottom', fontSize: '10px', markers: { size: 3 } },
                    xaxis: { labels: { style: { colors: '#64748b', fontSize: '10px', fontWeight: 600 } } },
                    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 600 } } },
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 5 } }
                }
            }],
            series: summaryData,
            xaxis: {
                categories: ['Period'],
                labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontWeight: 600 }, formatter: val => 'Rs ' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val) }
            },
            dataLabels: { enabled: false },
            plotOptions: { bar: { horizontal: false, columnWidth: '42%', borderRadius: 6, distributed: true } },
            colors: ['#10b981', '#4f46e5', '#ef4444', '{{ $grossProfit >= 0 ? "#f59e0b" : "#ef4444" }}'],
            legend: { show: true, position: 'bottom', fontSize: '11.5px', fontWeight: 600, labels: { colors: '#64748b' } },
            tooltip: { theme: 'light', y: { formatter: val => 'Rs ' + val.toLocaleString() } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4, xaxis: { lines: { show: false } } }
        }).render();
    });
</script>
@endsection