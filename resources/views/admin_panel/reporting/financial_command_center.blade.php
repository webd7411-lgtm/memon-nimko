@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --fcc-bg: #f8fafc;
    --fcc-surface: #ffffff;
    --fcc-border: #e2e8f0;
    --fcc-primary: #0f766e;
    --fcc-primary-dark: #115e59;
    --fcc-success: #10b981;
    --fcc-danger: #ef4444;
    --fcc-warning: #f59e0b;
    --fcc-info: #0284c7;
    --fcc-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.fcc-container * { font-family: var(--fcc-font); }
.fcc-container { background-color: var(--fcc-bg); min-height: 100vh; padding: 1.25rem 1.5rem 3rem; }

/* Hero Banner */
.fcc-hero {
    background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
    border-radius: 18px;
    padding: 1.6rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 18px 30px -10px rgba(15, 23, 42, 0.28);
    color: #ffffff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
}
.fcc-hero h3 { font-weight: 800; margin: 0; font-size: 1.45rem; letter-spacing: -0.5px; }
.fcc-hero p { margin: 0; font-size: 0.85rem; color: rgba(255, 255, 255, 0.8); }

/* Control & Filter Strip */
.fcc-filter-card {
    background: #ffffff;
    border: 1px solid var(--fcc-border);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
}

/* KPI Snapshot Cards */
.fcc-kpi-card {
    background: #ffffff;
    border: 1px solid var(--fcc-border);
    border-radius: 16px;
    padding: 1.25rem 1.4rem;
    height: 100%;
    position: relative;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.fcc-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
}
.fcc-kpi-title {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    margin-bottom: 0.35rem;
}
.fcc-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 0.4rem;
}
.fcc-delta-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 700;
}
.fcc-delta-pill.good { background: #dcfce7; color: #15803d; }
.fcc-delta-pill.bad { background: #fee2e2; color: #b91c1c; }
.fcc-delta-pill.neutral { background: #f1f5f9; color: #64748b; }

/* Comparison Banner (Kal vs Aaj) */
.fcc-compare-strip {
    background: #ffffff;
    border: 1px solid #e0e7ff;
    border-left: 5px solid #6366f1;
    border-radius: 14px;
    padding: 0.9rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}

/* Financial Panels */
.fcc-panel {
    background: #ffffff;
    border: 1px solid var(--fcc-border);
    border-radius: 16px;
    padding: 1.4rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.fcc-panel-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.9rem;
    margin-bottom: 1.2rem;
}
.fcc-panel-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

/* Metric Row */
.fcc-row-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.5rem;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 0.88rem;
}
.fcc-row-item:last-child { border-bottom: none; }
.fcc-row-label { color: #475569; font-weight: 500; }
.fcc-row-val { font-weight: 700; color: #0f172a; }

/* Print styling */
@media print {
    .fcc-filter-card, .fcc-hero button, .rt_nav_header, .navbar { display: none !important; }
    .fcc-container { padding: 0 !important; background: #ffffff !important; }
    .fcc-hero { background: #0f766e !important; color: #fff !important; }
}
</style>

<div class="container-fluid fcc-container">
    <!-- Top Hero Header -->
    <div class="fcc-hero">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white text-dark fw-bold px-2 py-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">EXECUTIVE 360°</span>
                <span class="text-white-50 small">• Real-time Financial Pulse</span>
            </div>
            <h3>Financial Command Center (One-Window Net Worth)</h3>
            <p>Overall live visibility of Where You Were Yesterday vs Where You Stand Today • Assets • Liabilities • P&L</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 fw-semibold shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Snapshot
            </button>
            <button type="button" class="btn text-white rounded-pill px-3 py-1.5 fw-semibold shadow-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);" onclick="loadFinancialSnapshot()">
                <i class="fas fa-sync-alt me-1" id="refreshIcon"></i> Refresh Live Data
            </button>
        </div>
    </div>

    <!-- Filter & Comparison Selector Bar -->
    <div class="fcc-filter-card">
        <form id="fccFilterForm" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-history me-1"></i> Time Comparison Horizon</label>
                <select name="comparison_type" id="comparison_type" class="form-select form-select-sm fw-semibold" onchange="toggleCustomDates(); loadFinancialSnapshot();">
                    <option value="today_vs_yesterday" selected>📅 Today vs Yesterday (Kal vs Aaj)</option>
                    <option value="this_week_vs_last_week">📆 This Week vs Last Week</option>
                    <option value="this_month_vs_last_month">📊 This Month vs Last Month</option>
                    <option value="this_year_vs_last_year">📈 This Year vs Last Year</option>
                    <option value="custom">⚙️ Custom Comparison Date Ranges</option>
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-code-branch me-1"></i> Branch / Channel</label>
                <select name="branch_id" id="branch_id" class="form-select form-select-sm fw-semibold" onchange="loadFinancialSnapshot();">
                    <option value="all">All Branches & Channels Combined</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Custom date range inputs (Hidden by default) -->
            <div class="col-12 col-md-5 custom-dates-row d-none">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Current End Date</label>
                        <input type="date" name="curr_end_date" id="curr_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Baseline End Date</label>
                        <input type="date" name="base_end_date" id="base_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-2 ms-auto text-end custom-dates-btn d-none">
                <label class="form-label d-block small mb-1">&nbsp;</label>
                <button type="button" class="btn btn-sm text-white rounded-pill px-4 fw-bold w-100" style="background: var(--fcc-primary);" onclick="loadFinancialSnapshot()">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Active Horizon Info Strip -->
    <div class="fcc-compare-strip">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-arrows-alt-h text-primary fs-5"></i>
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Comparing Active Periods:</span>
                <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                    <span class="text-success" id="currLabel">Today</span>
                    <span class="text-muted mx-1">compared against</span>
                    <span class="text-primary" id="baseLabel">Yesterday</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark border px-3 py-1.5 fw-semibold" id="solvencyBadge">
                <i class="fas fa-shield-alt text-success me-1"></i> Checking Health...
            </span>
            <span class="text-muted small">Liquidity Ratio: <strong class="text-dark" id="currentRatioVal">—</strong></span>
        </div>
    </div>

    <!-- Top 4 Master Executive KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Net Worth (Owner's Equity) -->
        <div class="col-12 col-md-3">
            <div class="fcc-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div class="fcc-kpi-title">True Net Worth (Assets - Liabilities)</div>
                <div class="fcc-kpi-value" id="kpiNetWorth">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaNetWorth"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">vs baseline</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Period Sales -->
        <div class="col-12 col-md-3">
            <div class="fcc-kpi-card" style="border-left: 4px solid #3b82f6 !important;">
                <div class="fcc-kpi-title">Period Sales Revenue</div>
                <div class="fcc-kpi-value text-primary" id="kpiSales">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaSales"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Direct + Dist Sales</span>
                </div>
            </div>
        </div>

        <!-- 3. Operating Expenses -->
        <div class="col-12 col-md-3">
            <div class="fcc-kpi-card" style="border-left: 4px solid #f59e0b !important;">
                <div class="fcc-kpi-title">Operating Expenses</div>
                <div class="fcc-kpi-value text-warning" id="kpiExpenses">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaExpenses"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Vouchers & Overheads</span>
                </div>
            </div>
        </div>

        <!-- 4. Net Profit / Loss -->
        <div class="col-12 col-md-3">
            <div class="fcc-kpi-card" style="border-left: 4px solid #10b981 !important;">
                <div class="fcc-kpi-title">Period Net Profit</div>
                <div class="fcc-kpi-value text-success" id="kpiNetProfit">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaNetProfit"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">After COGS & Expenses</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Column Comprehensive Breakdown -->
    <div class="row g-4">
        <!-- LEFT COLUMN: BALANCE SHEET (Assets & Liabilities) -->
        <div class="col-12 col-lg-7">
            <!-- ASSETS SECTION -->
            <div class="fcc-panel">
                <div class="fcc-panel-hdr">
                    <h5 class="fcc-panel-title">
                        <i class="fas fa-cubes text-teal" style="color: #0f766e;"></i>
                        Total Business Assets
                    </h5>
                    <div class="text-end">
                        <span class="text-muted small">Cumulative Total:</span>
                        <h5 class="fw-bold text-dark mb-0" id="totalAssetsVal">Rs. 0.00</h5>
                    </div>
                </div>

                <!-- Sub-Asset Rows -->
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-money-bill-wave text-success me-2"></i> Cash in Hand (Cash Registers & Drawers)</span>
                    <span class="fcc-row-val" id="rowCashInHand">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-university text-primary me-2"></i> Bank Account Balances</span>
                    <span class="fcc-row-val" id="rowBankBalances">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-box text-warning me-2"></i> Finished Goods Stock Value</span>
                    <span class="fcc-row-val" id="rowFinishedGoods">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-seedling text-info me-2"></i> Raw Materials Live Stock Value</span>
                    <span class="fcc-row-val" id="rowRawMaterials">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-truck-loading text-secondary me-2"></i> Field / Van Custody Stock (Distributors)</span>
                    <span class="fcc-row-val" id="rowFieldCustody">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-users text-primary me-2"></i> Direct Customers Receivable</span>
                    <span class="fcc-row-val" id="rowCustomerReceivable">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-user-tie text-teal me-2" style="color: #0f766e;"></i> Distributor Receivable (Owed to Company)</span>
                    <span class="fcc-row-val" id="rowDistributorReceivable">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-building text-secondary me-2"></i> Fixed Assets (Plant, Vehicles, Equipment)</span>
                    <span class="fcc-row-val" id="rowFixedAssets">Rs. 0.00</span>
                </div>
            </div>

            <!-- LIABILITIES SECTION -->
            <div class="fcc-panel">
                <div class="fcc-panel-hdr">
                    <h5 class="fcc-panel-title">
                        <i class="fas fa-hand-holding-usd text-danger"></i>
                        Total Business Liabilities (Den-daari)
                    </h5>
                    <div class="text-end">
                        <span class="text-muted small">Cumulative Obligations:</span>
                        <h5 class="fw-bold text-danger mb-0" id="totalLiabilitiesVal">Rs. 0.00</h5>
                    </div>
                </div>

                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-truck text-danger me-2"></i> Vendor / Supplier Payables (Accounts Payable)</span>
                    <span class="fcc-row-val text-danger" id="rowVendorPayables">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label"><i class="fas fa-receipt text-warning me-2"></i> Pending Claims / Customer Credit Notes</span>
                    <span class="fcc-row-val text-danger" id="rowPendingClaims">Rs. 0.00</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: PROFIT & LOSS BREAKDOWN + NET WORTH EQUATION -->
        <div class="col-12 col-lg-5">
            <!-- P&L Period Summary -->
            <div class="fcc-panel">
                <div class="fcc-panel-hdr">
                    <h5 class="fcc-panel-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Income Statement (P&L Breakdown)
                    </h5>
                    <span class="badge bg-light text-muted border" id="plPeriodBadge">Selected Period</span>
                </div>

                <div class="fcc-row-item">
                    <span class="fcc-row-label">Direct Sales Invoices</span>
                    <span class="fcc-row-val text-dark" id="plDirectSales">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">Distributor Dispatched Sales</span>
                    <span class="fcc-row-val text-dark" id="plDistSales">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item bg-light rounded px-2">
                    <span class="fcc-row-label fw-bold text-dark">Gross Revenue</span>
                    <span class="fcc-row-val fw-bold text-dark" id="plTotalRevenue">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item text-danger">
                    <span class="fcc-row-label text-danger">Less: Cost of Goods Sold (COGS)</span>
                    <span class="fcc-row-val text-danger" id="plCOGS">- Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label fw-semibold text-dark">Gross Margin</span>
                    <span class="fcc-row-val fw-bold text-success" id="plGrossProfit">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item text-danger">
                    <span class="fcc-row-label text-danger">Less: Operating Expenses</span>
                    <span class="fcc-row-val text-danger" id="plExpenses">- Rs. 0.00</span>
                </div>
                <div class="fcc-row-item p-2 rounded mt-2" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <span class="fcc-row-label fw-bold text-success fs-6">Net Profit for Period</span>
                    <span class="fcc-row-val fw-bold text-success fs-5" id="plNetProfit">Rs. 0.00</span>
                </div>
            </div>

            <!-- NET WORTH EQUATION CARD -->
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-uppercase small fw-bold text-white-50 letter-spacing-1">Financial Equation</span>
                    <i class="fas fa-balance-scale text-teal-light fs-4" style="color: #14b8a6;"></i>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 small d-block">Total Assets</span>
                    <h5 class="fw-bold mb-0 text-white" id="eqAssets">Rs. 0.00</h5>
                </div>
                <div class="mb-3">
                    <span class="text-white-50 small d-block">Minus Total Liabilities</span>
                    <h5 class="fw-bold mb-0 text-warning" id="eqLiabilities">- Rs. 0.00</h5>
                </div>
                <hr class="border-white opacity-20 my-3">
                <div>
                    <span class="text-white-50 small d-block">Net Worth / Owner's Equity</span>
                    <h3 class="fw-bold mb-0 text-white" id="eqNetWorth">Rs. 0.00</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCustomDates() {
    const isCustom = document.getElementById('comparison_type').value === 'custom';
    document.querySelectorAll('.custom-dates-row').forEach(el => el.classList.toggle('d-none', !isCustom));
    document.querySelectorAll('.custom-dates-btn').forEach(el => el.classList.toggle('d-none', !isCustom));
}

function formatCurrency(val) {
    const num = parseFloat(val) || 0;
    return 'Rs. ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateDeltaBadge(elementId, delta) {
    const el = document.getElementById(elementId);
    if (!el || !delta) return;

    let icon = '<i class="fas fa-minus"></i>';
    if (delta.direction === 'up') icon = '<i class="fas fa-arrow-up"></i>';
    if (delta.direction === 'down') icon = '<i class="fas fa-arrow-down"></i>';

    el.className = 'fcc-delta-pill ' + (delta.status || 'neutral');
    el.innerHTML = `${icon} ${delta.pct}% (${formatCurrency(delta.difference)})`;
}

function loadFinancialSnapshot() {
    const refreshIcon = document.getElementById('refreshIcon');
    if (refreshIcon) refreshIcon.classList.add('fa-spin');

    const form = document.getElementById('fccFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    fetch("{{ route('report.financial_command_center.fetch') }}?" + params)
        .then(res => res.json())
        .then(res => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            if (!res.status) {
                alert('Could not load financial snapshot');
                return;
            }

            // Update Labels
            document.getElementById('currLabel').innerText = res.curr_label;
            document.getElementById('baseLabel').innerText = res.base_label;
            document.getElementById('plPeriodBadge').innerText = res.curr_label;

            // Health & Liquidity
            document.getElementById('currentRatioVal').innerText = res.health.current_ratio + ':1';
            const solvencyBadge = document.getElementById('solvencyBadge');
            if (res.health.is_profitable && res.curr_bs.net_worth >= 0) {
                solvencyBadge.className = 'badge bg-success-subtle text-success border border-success px-3 py-1.5 fw-semibold';
                solvencyBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Solvent & Profitable';
            } else {
                solvencyBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-1.5 fw-semibold';
                solvencyBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> ' + res.health.solvency_status;
            }

            // Top KPI Cards
            document.getElementById('kpiNetWorth').innerText = formatCurrency(res.curr_bs.net_worth);
            document.getElementById('kpiSales').innerText = formatCurrency(res.curr_pl.total_sales);
            document.getElementById('kpiExpenses').innerText = formatCurrency(res.curr_pl.total_expenses);
            document.getElementById('kpiNetProfit').innerText = formatCurrency(res.curr_pl.net_profit);

            // Delta Badges
            updateDeltaBadge('deltaNetWorth', res.comparison.net_worth);
            updateDeltaBadge('deltaSales', res.comparison.sales);
            updateDeltaBadge('deltaExpenses', res.comparison.expenses);
            updateDeltaBadge('deltaNetProfit', res.comparison.net_profit);

            // Asset Details
            document.getElementById('totalAssetsVal').innerText = formatCurrency(res.curr_bs.total_assets);
            document.getElementById('rowCashInHand').innerText = formatCurrency(res.curr_bs.cash_in_hand);
            document.getElementById('rowBankBalances').innerText = formatCurrency(res.curr_bs.bank_balances);
            document.getElementById('rowFinishedGoods').innerText = formatCurrency(res.curr_bs.inventory.finished_goods);
            document.getElementById('rowRawMaterials').innerText = formatCurrency(res.curr_bs.inventory.raw_materials);
            document.getElementById('rowFieldCustody').innerText = formatCurrency(res.curr_bs.inventory.field_custody);
            document.getElementById('rowCustomerReceivable').innerText = formatCurrency(res.curr_bs.receivables.direct_customers);
            document.getElementById('rowDistributorReceivable').innerText = formatCurrency(res.curr_bs.receivables.distributors);
            document.getElementById('rowFixedAssets').innerText = formatCurrency(res.curr_bs.fixed_assets);

            // Liabilities Details
            document.getElementById('totalLiabilitiesVal').innerText = formatCurrency(res.curr_bs.total_liabilities);
            document.getElementById('rowVendorPayables').innerText = formatCurrency(res.curr_bs.payables.vendors);
            document.getElementById('rowPendingClaims').innerText = formatCurrency(res.curr_bs.payables.claims);

            // P&L Breakdown
            document.getElementById('plDirectSales').innerText = formatCurrency(res.curr_pl.direct_sales);
            document.getElementById('plDistSales').innerText = formatCurrency(res.curr_pl.dist_sales);
            document.getElementById('plTotalRevenue').innerText = formatCurrency(res.curr_pl.total_sales);
            document.getElementById('plCOGS').innerText = '- ' + formatCurrency(res.curr_pl.cogs);
            document.getElementById('plGrossProfit').innerText = formatCurrency(res.curr_pl.gross_profit);
            document.getElementById('plExpenses').innerText = '- ' + formatCurrency(res.curr_pl.total_expenses);
            document.getElementById('plNetProfit').innerText = formatCurrency(res.curr_pl.net_profit);

            // Net Worth Equation
            document.getElementById('eqAssets').innerText = formatCurrency(res.curr_bs.total_assets);
            document.getElementById('eqLiabilities').innerText = '- ' + formatCurrency(res.curr_bs.total_liabilities);
            document.getElementById('eqNetWorth').innerText = formatCurrency(res.curr_bs.net_worth);
        })
        .catch(err => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            console.error('Error loading snapshot:', err);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    loadFinancialSnapshot();
});
</script>
@endsection
