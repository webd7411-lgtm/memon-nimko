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
    --fcc-purple: #7c3aed;
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

/* Quick Action Buttons in Hero */
.fcc-hero-btn {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff !important;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.fcc-hero-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-1px);
}

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
    font-size: 1.55rem;
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
.bs-row-badge {
    font-size: 0.72rem;
    padding: 0.18rem 0.52rem;
    border-radius: 6px;
    background: #f1f5f9;
    color: #334155;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.15s ease-in-out;
    border: 1px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
}
.bs-row-badge:hover {
    background: #0f766e;
    color: #ffffff;
    border-color: #0f766e;
}
.bs-row-badge i {
    font-size: 0.68rem;
}
.modal-header .modal-title {
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    letter-spacing: 0.3px;
}
.modal-header .btn-close {
    filter: brightness(0) invert(1) !important;
    opacity: 0.85;
}
.modal-header .btn-close:hover {
    opacity: 1;
}
.fcc-modal-table th {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #0f172a !important;
    font-weight: 700 !important;
    background: #f1f5f9 !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 0.65rem 0.75rem;
}
.fcc-modal-table td {
    font-size: 0.85rem;
    vertical-align: middle;
    padding: 0.6rem 0.75rem;
    color: #0f172a !important;
    font-weight: 500;
}
.fcc-modal-table .text-muted {
    color: #334155 !important;
}
.fcc-search-box {
    border-radius: 8px;
    padding: 0.45rem 0.85rem;
    border: 1px solid #94a3b8;
    color: #0f172a;
    font-weight: 500;
}
.fcc-search-box:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
}
.fcc-row-val { font-weight: 700; color: #0f172a; }

/* Print styling */
@media print {
    .fcc-filter-card, .fcc-hero button, .fcc-hero .fcc-hero-btn, .rt_nav_header, .navbar { display: none !important; }
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
                <span class="text-white-50 small">• Live Financial Pulse & Net Worth</span>
            </div>
            <h3>Financial Command Center (One-Window Control)</h3>
            <p>Track Where You Were Yesterday vs Where You Stand Today • Assets • Liabilities • P&L • Outsource Capital & Drawings</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Shortcuts to create vouchers -->
            <a href="{{ route('recepit-vochers') }}" target="_blank" class="fcc-hero-btn" title="Add Outsource Investment via Receipt Voucher">
                <i class="fas fa-plus-circle text-success"></i> + Inward Investment (Receipt)
            </a>
            <a href="{{ route('Payment-vochers') }}" target="_blank" class="fcc-hero-btn" title="Record Personal Drawings / Capital Withdrawal via Payment Voucher">
                <i class="fas fa-minus-circle text-warning"></i> - Personal Drawings (Payment)
            </a>
            <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 fw-semibold shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Snapshot
            </button>
            <button type="button" class="btn text-white rounded-pill px-3 py-1.5 fw-semibold shadow-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);" onclick="loadFinancialSnapshot()">
                <i class="fas fa-sync-alt me-1" id="refreshIcon"></i> Refresh
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
                        <label class="form-label small fw-bold text-muted mb-0" style="font-size:0.75rem;">Current Start</label>
                        <input type="date" name="curr_start_date" id="curr_start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-0" style="font-size:0.75rem;">Current End</label>
                        <input type="date" name="curr_end_date" id="curr_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6 mt-1">
                        <label class="form-label small fw-bold text-muted mb-0" style="font-size:0.75rem;">Baseline Start</label>
                        <input type="date" name="base_start_date" id="base_start_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    </div>
                    <div class="col-6 mt-1">
                        <label class="form-label small fw-bold text-muted mb-0" style="font-size:0.75rem;">Baseline End</label>
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

    <!-- Top 5 Master Executive KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Net Worth (Owner's Equity) -->
        <div class="col-12 col-md-4 col-xl-2.4" style="flex: 1 0 200px;">
            <div class="fcc-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div class="fcc-kpi-title">True Net Worth</div>
                <div class="fcc-kpi-value" id="kpiNetWorth">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaNetWorth"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Assets - Liabilities</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Period Sales -->
        <div class="col-12 col-md-4 col-xl-2.4" style="flex: 1 0 200px;">
            <div class="fcc-kpi-card" style="border-left: 4px solid #3b82f6 !important;">
                <div class="fcc-kpi-title">Period Net Revenue</div>
                <div class="fcc-kpi-value text-primary" id="kpiSales">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaSales"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Gross Sales - Returns</span>
                </div>
            </div>
        </div>

        <!-- 3. Operating Expenses -->
        <div class="col-12 col-md-4 col-xl-2.4" style="flex: 1 0 200px;">
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
        <div class="col-12 col-md-4 col-xl-2.4" style="flex: 1 0 200px;">
            <div class="fcc-kpi-card" style="border-left: 4px solid #10b981 !important;">
                <div class="fcc-kpi-title">Period Net Profit</div>
                <div class="fcc-kpi-value text-success" id="kpiNetProfit">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="fcc-delta-pill" id="deltaNetProfit"><i class="fas fa-minus"></i> 0%</span>
                    <span class="text-muted" style="font-size: 0.72rem;">P&L Bottom Line</span>
                </div>
            </div>
        </div>

        <!-- 5. Net Capital Movement (Injections - Drawings) -->
        <div class="col-12 col-md-4 col-xl-2.4" style="flex: 1 0 200px;">
            <div class="fcc-kpi-card" style="border-left: 4px solid #7c3aed !important;">
                <div class="fcc-kpi-title">Outsource Capital vs Drawings</div>
                <div class="fcc-kpi-value text-purple" style="color: #7c3aed;" id="kpiNetCapital">Rs. 0.00</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="badge bg-light text-dark border px-2 py-0.5" id="capitalSummaryBadge" style="font-size: 0.72rem;">In: Rs 0 | Out: Rs 0</span>
                    <span class="text-muted" style="font-size: 0.72rem;">Owner Funds</span>
                </div>
            </div>
        </div>
    </div>

    <!-- OWNER'S CAPITAL & DRAWINGS BANNER -->
    <div class="fcc-panel" style="border: 1px solid #ddd6fe; background: #faf5ff;">
        <div class="fcc-panel-hdr border-bottom-0 pb-0 mb-3">
            <div>
                <h5 class="fcc-panel-title" style="color: #6b21a8;">
                    <i class="fas fa-hand-holding-usd text-purple" style="color: #7c3aed;"></i>
                    Owner's Capital Injections & Drawings (Outsource Fund Tracking)
                </h5>
                <span class="text-muted small">Bahir se kitni investment aayi (+ Inflow) aur business se kitna personal nikaala (- Drawings/Outflow)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('recepit-vochers') }}" target="_blank" class="btn btn-sm btn-outline-purple bg-white rounded-pill px-3 fw-semibold border" style="color: #7c3aed;">
                    <i class="fas fa-plus-circle me-1"></i> New Capital Inflow (RV)
                </a>
                <a href="{{ route('Payment-vochers') }}" target="_blank" class="btn btn-sm btn-outline-danger bg-white rounded-pill px-3 fw-semibold border" style="color: #dc2626;">
                    <i class="fas fa-minus-circle me-1"></i> New Drawing / Outflow (PV)
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 bg-white rounded-4 border shadow-sm">
                    <span class="text-muted small fw-bold text-uppercase d-block">Period Capital Injected (+ Inflow)</span>
                    <h4 class="fw-bold mb-1 text-success mt-1" id="equityInflowVal">Rs. 0.00</h4>
                    <span class="text-muted small">Outsource investment added via Receipt Vouchers</span>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 bg-white rounded-4 border shadow-sm">
                    <span class="text-muted small fw-bold text-uppercase d-block">Period Personal Drawings (- Outflow)</span>
                    <h4 class="fw-bold mb-1 text-danger mt-1" id="equityOutflowVal">Rs. 0.00</h4>
                    <span class="text-muted small">Business se nikaala gaya personal fund via Payment Vouchers</span>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 bg-white rounded-4 border shadow-sm">
                    <span class="text-muted small fw-bold text-uppercase d-block">All-Time Net Invested Capital</span>
                    <h4 class="fw-bold mb-1 text-purple mt-1" style="color: #7c3aed;" id="equityCumulativeCapitalVal">Rs. 0.00</h4>
                    <span class="text-muted small">Cumulative (Total Injected - Total Withdrawn)</span>
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

                <!-- Sub-Asset Rows with Detail Badges -->
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-money-bill-wave text-success me-2"></i> Cash in Hand (Cash Registers & Drawers)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#cashDetailModal"><i class="fas fa-eye"></i> Details</span>
                    </span>
                    <span class="fcc-row-val" id="rowCashInHand">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-university text-primary me-2"></i> Bank Account Balances
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#bankDetailModal"><i class="fas fa-university"></i> View Banks</span>
                    </span>
                    <span class="fcc-row-val" id="rowBankBalances">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-box text-warning me-2"></i> Finished Goods Stock Value
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#stockModal"><i class="fas fa-boxes"></i> Stock Value</span>
                    </span>
                    <span class="fcc-row-val" id="rowFinishedGoods">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-seedling text-info me-2"></i> Raw Materials Live Stock Value
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#rawStockModal"><i class="fas fa-leaf"></i> RM Stock</span>
                    </span>
                    <span class="fcc-row-val" id="rowRawMaterials">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-users text-primary me-2"></i> Direct Customers Receivable
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#customersModal"><i class="fas fa-user-friends"></i> <span id="badgeCustCount">0</span> Customers</span>
                    </span>
                    <span class="fcc-row-val" id="rowCustomerReceivable">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-user-tie text-teal me-2" style="color: #0f766e;"></i> Distributor Receivable (Owed to Company)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#distributorsModal"><i class="fas fa-truck-loading"></i> <span id="badgeDistCount">0</span> Distributors</span>
                    </span>
                    <span class="fcc-row-val" id="rowDistributorReceivable">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-store text-info me-2"></i> Market / Retail Shop Receivables (Field)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#retailShopsModal"><i class="fas fa-store-alt"></i> <span id="badgeShopsCount">0</span> Shops</span>
                    </span>
                    <span class="fcc-row-val" id="rowMarketReceivable">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-building text-secondary me-2"></i> Fixed Assets (Plant, Vehicles, Equipment)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#assetsModal"><i class="fas fa-clipboard-list"></i> Asset Register</span>
                    </span>
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
                    <span class="fcc-row-label">
                        <i class="fas fa-truck text-danger me-2"></i> Vendor / Supplier Payables (Accounts Payable)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#vendorsModal"><i class="fas fa-file-invoice-dollar"></i> <span id="badgeVendCount">0</span> Vendors</span>
                    </span>
                    <span class="fcc-row-val text-danger" id="rowVendorPayables">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-hand-holding-heart text-warning me-2"></i> Distributor Credit Advances / Credit Notes
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#distAdvancesModal"><i class="fas fa-handshake"></i> <span id="badgeAdvCount">0</span> Advances</span>
                    </span>
                    <span class="fcc-row-val text-danger" id="rowDistributorAdvances">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label">
                        <i class="fas fa-receipt text-warning me-2"></i> Pending Claims (Unapproved Claims)
                        <span class="bs-row-badge ms-2" data-bs-toggle="modal" data-bs-target="#claimsModal"><i class="fas fa-exclamation-circle"></i> <span id="badgeClaimsCount">0</span> Claims</span>
                    </span>
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
                <div class="fcc-row-item">
                    <span class="fcc-row-label text-muted" style="font-size:0.83rem;">Distributor Market Sales (Retailers)</span>
                    <span class="fcc-row-val text-muted" style="font-size:0.83rem;" id="plSecondarySales">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item text-danger">
                    <span class="fcc-row-label text-danger">Less: Sales Returns & Refunds</span>
                    <span class="fcc-row-val text-danger" id="plSalesReturns">- Rs. 0.00</span>
                </div>
                <div class="fcc-row-item bg-light rounded px-2">
                    <span class="fcc-row-label fw-bold text-dark">Net Sales Turnover</span>
                    <span class="fcc-row-val fw-bold text-dark" id="plTotalRevenue">Rs. 0.00</span>
                </div>
                <div class="fcc-row-item text-danger">
                    <span class="fcc-row-label text-danger">Less: Cost of Goods Sold (COGS)</span>
                    <span class="fcc-row-val text-danger" id="plCOGS">- Rs. 0.00</span>
                </div>
                <div class="fcc-row-item">
                    <span class="fcc-row-label fw-semibold text-dark">Gross Margin / Profit</span>
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
                <div class="mb-3">
                    <span class="text-white-50 small d-block">Net Worth / Total Owner's Equity</span>
                    <h3 class="fw-bold mb-0 text-white" id="eqNetWorth">Rs. 0.00</h3>
                </div>
                <div class="p-2 rounded" style="background: rgba(255, 255, 255, 0.1);">
                    <div class="d-flex justify-content-between small text-white-50">
                        <span>• Net Capital Invested:</span>
                        <strong class="text-white" id="eqNetInvested">Rs. 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-white-50 mt-1">
                        <span>• Accumulated Earnings / Surplus:</span>
                        <strong class="text-white" id="eqRetainedEarnings">Rs. 0.00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DETAIL MODALS (360 EXECUTIVE DRILL-DOWN) -->

<!-- 1. Cash in Hand Modal -->
<div class="modal fade" id="cashDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-money-bill-wave text-success me-2"></i> Cash in Hand (Registers & Cash Accounts)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3 mb-4">
          <div class="col-12">
            <div class="p-3 rounded-3 border bg-light d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small d-block">Chart of Accounts — Cash in Hand</span>
                <small class="text-muted">Total verified cash balance across Cash Accounts</small>
              </div>
              <h3 class="fw-bold text-success mb-0" id="modalAccountCash">Rs. 0.00</h3>
            </div>
          </div>
        </div>
        <h6 class="fw-bold text-dark mb-3">Cash Accounts Breakdown</h6>
        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Account Title</th>
                <th>Account Code</th>
                <th class="text-end">Balance</th>
              </tr>
            </thead>
            <tbody id="cashTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted">Total Cash in Hand: <strong class="text-dark" id="modalTotalCashFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 2. Bank Accounts Modal -->
<div class="modal fade" id="bankDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-university text-primary me-2"></i> Bank Account Balances
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Account Title</th>
                <th>Bank Name</th>
                <th>Code</th>
                <th class="text-end">Opening</th>
                <th class="text-end text-success">+ Inflow</th>
                <th class="text-end text-danger">- Outflow</th>
                <th class="text-end">Current Balance</th>
              </tr>
            </thead>
            <tbody id="bankTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted">Total Bank Funds: <strong class="text-primary" id="modalTotalBankFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 3. Finished Goods Stock Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-boxes text-warning me-2"></i> Finished Goods Stock Valuation
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccStockSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search finished product name, variant or code...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Product & Variant</th>
                <th>Code</th>
                <th>Category</th>
                <th class="text-end">Stock Qty</th>
                <th>Unit</th>
                <th class="text-end">Valuation Cost</th>
                <th class="text-end">Total Valuation</th>
              </tr>
            </thead>
            <tbody id="stockTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalStockItemsCount">0</span> items in stock | Total Valuation: <strong class="text-warning-emphasis" id="modalTotalStockFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 4. Raw Materials Live Stock Modal -->
<div class="modal fade" id="rawStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-seedling text-info me-2"></i> Raw Materials Live Stock Valuation
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccRawStockSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search raw material name or code...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Material Name</th>
                <th>Material Code</th>
                <th class="text-end">Stock Qty</th>
                <th>Unit</th>
                <th class="text-end">Last Purchase Cost</th>
                <th class="text-end">Total Valuation</th>
              </tr>
            </thead>
            <tbody id="rawStockTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalRawItemsCount">0</span> materials | Total RM Value: <strong class="text-info" id="modalTotalRawFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 5. Direct Customers Receivable Modal -->
<div class="modal fade" id="customersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-users text-primary me-2"></i> Direct Customers Receivable (Sundry Debtors)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccCustSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search customer name or phone...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th class="text-end">Receivable Balance</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="customersTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalCustCountFooter">0</span> Customers | Total: <strong class="text-primary" id="modalTotalCustFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 6. Distributor Receivable Modal -->
<div class="modal fade" id="distributorsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-user-tie text-teal me-2" style="color: #0f766e;"></i> Distributor Receivable (Owed to Company)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccDistSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search distributor name, code or area...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Code</th>
                <th>Distributor Name</th>
                <th>Area</th>
                <th>Phone</th>
                <th class="text-end">Receivable Balance</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="distributorsTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalDistCountFooter">0</span> Distributors | Total: <strong class="text-teal" style="color: #0f766e;" id="modalTotalDistFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 7. Market / Retail Shop Receivables Modal -->
<div class="modal fade" id="retailShopsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-store text-info me-2"></i> Market / Retail Shop Receivables (Field Outlets)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccShopSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search shop name, owner or distributor...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Shop / Outlet Name</th>
                <th>Owner Name</th>
                <th>Distributor</th>
                <th>Area</th>
                <th>Phone</th>
                <th class="text-end">Balance Due</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="retailShopsTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalShopsCountFooter">0</span> Retail Outlets | Total: <strong class="text-info" id="modalTotalShopsFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 8. Fixed Assets Register Modal -->
<div class="modal fade" id="assetsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-building text-secondary me-2"></i> Fixed Assets Register
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccAssetSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search asset name or code...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Asset Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th class="text-end">Purchase Cost</th>
                <th class="text-end">Current Book Value</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody id="assetsTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalAssetsCountFooter">0</span> Assets | Total Valuation: <strong class="text-secondary" id="modalTotalAssetsFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 9. Vendor / Supplier Payables Modal -->
<div class="modal fade" id="vendorsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-truck text-danger me-2"></i> Vendor / Supplier Payables (Accounts Payable)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccVendSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search vendor name or phone...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Vendor / Supplier Name</th>
                <th>Phone</th>
                <th class="text-end">Payable Amount</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="vendorsTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalVendCountFooter">0</span> Vendors | Total Obligation: <strong class="text-danger" id="modalTotalVendFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 10. Distributor Advances Modal -->
<div class="modal fade" id="distAdvancesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-hand-holding-heart text-warning me-2"></i> Distributor Credit Advances / Credit Notes
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccAdvSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search distributor...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Code</th>
                <th>Distributor Name</th>
                <th>Area</th>
                <th>Phone</th>
                <th class="text-end">Advance Balance</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="distAdvancesTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalAdvCountFooter">0</span> Distributors | Total Advance: <strong class="text-warning-emphasis" id="modalTotalAdvFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- 11. Pending Claims Modal -->
<div class="modal fade" id="claimsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold text-white mb-0">
          <i class="fas fa-receipt text-warning me-2"></i> Pending Distributor Claims (Unapproved)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <input type="text" id="fccClaimSearch" class="form-control form-control-sm mb-3 fcc-search-box" placeholder="Quick search claim no or distributor...">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
          <table class="table table-sm table-hover align-middle fcc-modal-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Claim No</th>
                <th>Distributor</th>
                <th>Claim Date</th>
                <th>Type</th>
                <th class="text-end">Amount</th>
              </tr>
            </thead>
            <tbody id="claimsTableBody">
              <!-- Populated via JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted"><span id="modalClaimsCountFooter">0</span> Claims | Total: <strong class="text-warning-emphasis" id="modalTotalClaimsFooter">Rs. 0.00</strong></span>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
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
            document.getElementById('kpiSales').innerText = formatCurrency(res.curr_pl.net_revenue ?? res.curr_pl.total_sales);
            document.getElementById('kpiExpenses').innerText = formatCurrency(res.curr_pl.total_expenses);
            document.getElementById('kpiNetProfit').innerText = formatCurrency(res.curr_pl.net_profit);

            // Capital Movement Card
            const netCap = res.curr_equity.net_movement;
            document.getElementById('kpiNetCapital').innerText = (netCap >= 0 ? '+ ' : '') + formatCurrency(netCap);
            document.getElementById('capitalSummaryBadge').innerText = 'In: ' + formatCurrency(res.curr_equity.injections) + ' | Out: ' + formatCurrency(res.curr_equity.drawings);

            // Delta Badges
            updateDeltaBadge('deltaNetWorth', res.comparison.net_worth);
            updateDeltaBadge('deltaSales', res.comparison.sales);
            updateDeltaBadge('deltaExpenses', res.comparison.expenses);
            updateDeltaBadge('deltaNetProfit', res.comparison.net_profit);

            // Outsource Capital Tracking Section
            document.getElementById('equityInflowVal').innerText = '+ ' + formatCurrency(res.curr_equity.injections);
            document.getElementById('equityOutflowVal').innerText = '- ' + formatCurrency(res.curr_equity.drawings);
            document.getElementById('equityCumulativeCapitalVal').innerText = formatCurrency(res.cumulative_equity.net_invested_capital);

            // Asset Details
            document.getElementById('totalAssetsVal').innerText = formatCurrency(res.curr_bs.total_assets);
            document.getElementById('rowCashInHand').innerText = formatCurrency(res.curr_bs.cash_in_hand);
            document.getElementById('rowBankBalances').innerText = formatCurrency(res.curr_bs.bank_balances);
            document.getElementById('rowFinishedGoods').innerText = formatCurrency(res.curr_bs.inventory.finished_goods);
            document.getElementById('rowRawMaterials').innerText = formatCurrency(res.curr_bs.inventory.raw_materials);
            document.getElementById('rowCustomerReceivable').innerText = formatCurrency(res.curr_bs.receivables.direct_customers);
            document.getElementById('rowDistributorReceivable').innerText = formatCurrency(res.curr_bs.receivables.distributors);
            if (document.getElementById('rowMarketReceivable')) {
                document.getElementById('rowMarketReceivable').innerText = formatCurrency(res.curr_bs.receivables.market_shops);
            }
            document.getElementById('rowFixedAssets').innerText = formatCurrency(res.curr_bs.fixed_assets);

            // Liabilities Details
            document.getElementById('totalLiabilitiesVal').innerText = formatCurrency(res.curr_bs.total_liabilities);
            document.getElementById('rowVendorPayables').innerText = formatCurrency(res.curr_bs.payables.vendors);
            if (document.getElementById('rowDistributorAdvances')) {
                document.getElementById('rowDistributorAdvances').innerText = formatCurrency(res.curr_bs.payables.distributor_advances);
            }
            document.getElementById('rowPendingClaims').innerText = formatCurrency(res.curr_bs.payables.claims);

            // P&L Breakdown
            document.getElementById('plDirectSales').innerText = formatCurrency(res.curr_pl.direct_sales);
            document.getElementById('plDistSales').innerText = formatCurrency(res.curr_pl.dist_sales);
            if (document.getElementById('plSecondarySales')) {
                document.getElementById('plSecondarySales').innerText = formatCurrency(res.curr_pl.secondary_market_sales);
            }
            if (document.getElementById('plSalesReturns')) {
                document.getElementById('plSalesReturns').innerText = '- ' + formatCurrency(res.curr_pl.total_returns);
            }
            document.getElementById('plTotalRevenue').innerText = formatCurrency(res.curr_pl.net_revenue ?? res.curr_pl.total_sales);
            document.getElementById('plCOGS').innerText = '- ' + formatCurrency(res.curr_pl.cogs);
            document.getElementById('plGrossProfit').innerText = formatCurrency(res.curr_pl.gross_profit);
            document.getElementById('plExpenses').innerText = '- ' + formatCurrency(res.curr_pl.total_expenses);
            document.getElementById('plNetProfit').innerText = formatCurrency(res.curr_pl.net_profit);

            // Net Worth Equation
            document.getElementById('eqAssets').innerText = formatCurrency(res.curr_bs.total_assets);
            document.getElementById('eqLiabilities').innerText = '- ' + formatCurrency(res.curr_bs.total_liabilities);
            document.getElementById('eqNetWorth').innerText = formatCurrency(res.curr_bs.net_worth);
            document.getElementById('eqNetInvested').innerText = formatCurrency(res.curr_bs.equity_breakdown.net_invested_capital);
            document.getElementById('eqRetainedEarnings').innerText = formatCurrency(res.curr_bs.equity_breakdown.retained_earnings);

            // ==========================================
            // POPULATE DETAIL DRILL-DOWN MODALS & BADGES
            // ==========================================
            var bs = res.curr_bs || {};
            var inv = bs.inventory || {};
            var rec = bs.receivables || {};
            var pay = bs.payables || {};

            // 1. Badge counters
            if (document.getElementById('badgeCustCount')) {
                document.getElementById('badgeCustCount').innerText = (rec.customers_list || []).length;
            }
            if (document.getElementById('badgeDistCount')) {
                document.getElementById('badgeDistCount').innerText = (rec.distributors_list || []).length;
            }
            if (document.getElementById('badgeShopsCount')) {
                document.getElementById('badgeShopsCount').innerText = (rec.market_shops_list || []).length;
            }
            if (document.getElementById('badgeVendCount')) {
                document.getElementById('badgeVendCount').innerText = (pay.vendors_list || []).length;
            }
            if (document.getElementById('badgeAdvCount')) {
                document.getElementById('badgeAdvCount').innerText = (pay.advances_list || []).length;
            }
            if (document.getElementById('badgeClaimsCount')) {
                document.getElementById('badgeClaimsCount').innerText = (pay.claims_list || []).length;
            }

            // 2. Cash Modal
            var cashDet = bs.cash_details || {};
            if (document.getElementById('modalCounterCash')) {
                document.getElementById('modalCounterCash').innerText = formatCurrency(cashDet.counter_cash || 0);
            }
            if (document.getElementById('modalAccountCash')) {
                var accCashSum = (cashDet.accounts || []).reduce((sum, a) => sum + (parseFloat(a.balance) || 0), 0);
                document.getElementById('modalAccountCash').innerText = formatCurrency(accCashSum);
            }
            if (document.getElementById('modalTotalCashFooter')) {
                document.getElementById('modalTotalCashFooter').innerText = formatCurrency(bs.cash_in_hand || 0);
            }
            var cashHtml = '';
            (cashDet.accounts || []).forEach(function(ca, idx) {
                cashHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${ca.title || '-'}</td>
                    <td><span class="badge bg-light text-muted border">${ca.code || '-'}</span></td>
                    <td class="text-end fw-bold text-success">${formatCurrency(ca.balance)}</td>
                </tr>`;
            });
            if (!cashHtml) cashHtml = '<tr><td colspan="4" class="text-center text-muted py-3">No cash accounts found</td></tr>';
            if (document.getElementById('cashTableBody')) document.getElementById('cashTableBody').innerHTML = cashHtml;

            // 3. Bank Modal
            var bankHtml = '';
            (bs.bank_details || []).forEach(function(ba, idx) {
                bankHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${ba.title || '-'}</td>
                    <td>${ba.bank_name || '-'}</td>
                    <td><span class="badge bg-light text-muted border">${ba.code || '-'}</span></td>
                    <td class="text-end text-muted">${formatCurrency(ba.opening_balance)}</td>
                    <td class="text-end text-success">+ ${formatCurrency(ba.inflow)}</td>
                    <td class="text-end text-danger">- ${formatCurrency(ba.outflow)}</td>
                    <td class="text-end fw-bold text-primary">${formatCurrency(ba.current_balance)}</td>
                </tr>`;
            });
            if (!bankHtml) bankHtml = '<tr><td colspan="8" class="text-center text-muted py-3">No bank accounts configured</td></tr>';
            if (document.getElementById('bankTableBody')) document.getElementById('bankTableBody').innerHTML = bankHtml;
            if (document.getElementById('modalTotalBankFooter')) document.getElementById('modalTotalBankFooter').innerText = formatCurrency(bs.bank_balances || 0);

            // 4. Finished Goods Stock Modal
            var stockItems = inv.finished_items || [];
            var stockHtml = '';
            stockItems.forEach(function(it, idx) {
                stockHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${it.name}</td>
                    <td><span class="badge bg-light text-muted border">${it.code || '-'}</span></td>
                    <td><span class="badge bg-secondary-subtle text-secondary">${it.category || 'General'}</span></td>
                    <td class="text-end fw-bold">${it.qty}</td>
                    <td>${it.unit}</td>
                    <td class="text-end text-muted">${formatCurrency(it.unit_cost)}</td>
                    <td class="text-end fw-bold text-warning-emphasis">${formatCurrency(it.total_val)}</td>
                </tr>`;
            });
            if (!stockHtml) stockHtml = '<tr><td colspan="8" class="text-center text-muted py-3">No physical finished stock recorded</td></tr>';
            if (document.getElementById('stockTableBody')) document.getElementById('stockTableBody').innerHTML = stockHtml;
            if (document.getElementById('modalStockItemsCount')) document.getElementById('modalStockItemsCount').innerText = stockItems.length;
            if (document.getElementById('modalTotalStockFooter')) document.getElementById('modalTotalStockFooter').innerText = formatCurrency(inv.finished_goods || 0);

            // 5. Raw Materials Stock Modal
            var rawItems = inv.raw_items || [];
            var rawHtml = '';
            rawItems.forEach(function(rm, idx) {
                rawHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${rm.name}</td>
                    <td><span class="badge bg-light text-muted border">${rm.code || '-'}</span></td>
                    <td class="text-end fw-bold">${rm.qty}</td>
                    <td>${rm.unit}</td>
                    <td class="text-end text-muted">${formatCurrency(rm.unit_cost)}</td>
                    <td class="text-end fw-bold text-info">${formatCurrency(rm.total_val)}</td>
                </tr>`;
            });
            if (!rawHtml) rawHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No raw materials in stock</td></tr>';
            if (document.getElementById('rawStockTableBody')) document.getElementById('rawStockTableBody').innerHTML = rawHtml;
            if (document.getElementById('modalRawItemsCount')) document.getElementById('modalRawItemsCount').innerText = rawItems.length;
            if (document.getElementById('modalTotalRawFooter')) document.getElementById('modalTotalRawFooter').innerText = formatCurrency(inv.raw_materials || 0);

            // 6. Direct Customers Modal
            var custList = rec.customers_list || [];
            var custHtml = '';
            custList.forEach(function(c, idx) {
                custHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${c.name}</td>
                    <td>${c.phone || '-'}</td>
                    <td class="text-end fw-bold text-primary">${formatCurrency(c.balance)}</td>
                    <td class="text-center">
                        <a href="${c.ledger_url}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:0.75rem;">
                            <i class="fas fa-book me-1"></i> Ledger
                        </a>
                    </td>
                </tr>`;
            });
            if (!custHtml) custHtml = '<tr><td colspan="5" class="text-center text-muted py-3">No direct customer balances pending</td></tr>';
            if (document.getElementById('customersTableBody')) document.getElementById('customersTableBody').innerHTML = custHtml;
            if (document.getElementById('modalCustCountFooter')) document.getElementById('modalCustCountFooter').innerText = custList.length;
            if (document.getElementById('modalTotalCustFooter')) document.getElementById('modalTotalCustFooter').innerText = formatCurrency(rec.direct_customers || 0);

            // 7. Distributors Modal
            var distList = rec.distributors_list || [];
            var distHtml = '';
            distList.forEach(function(d, idx) {
                distHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td><span class="badge bg-light text-muted border">${d.code || '-'}</span></td>
                    <td class="fw-semibold text-dark">${d.name}</td>
                    <td>${d.area || '-'}</td>
                    <td>${d.phone || '-'}</td>
                    <td class="text-end fw-bold text-teal" style="color: #0f766e;">${formatCurrency(d.balance)}</td>
                    <td class="text-center">
                        <a href="${d.ledger_url}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size:0.75rem;">
                            <i class="fas fa-book-open me-1"></i> Ledger
                        </a>
                    </td>
                </tr>`;
            });
            if (!distHtml) distHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No distributor balances pending</td></tr>';
            if (document.getElementById('distributorsTableBody')) document.getElementById('distributorsTableBody').innerHTML = distHtml;
            if (document.getElementById('modalDistCountFooter')) document.getElementById('modalDistCountFooter').innerText = distList.length;
            if (document.getElementById('modalTotalDistFooter')) document.getElementById('modalTotalDistFooter').innerText = formatCurrency(rec.distributors || 0);

            // 8. Market / Retail Shops Modal
            var shopList = rec.market_shops_list || [];
            var shopHtml = '';
            shopList.forEach(function(s, idx) {
                shopHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${s.shop_name}</td>
                    <td>${s.owner_name || '-'}</td>
                    <td><span class="badge bg-light text-dark border">${s.distributor_name || 'Direct'}</span></td>
                    <td>${s.area || '-'}</td>
                    <td>${s.phone || '-'}</td>
                    <td class="text-end fw-bold text-info">${formatCurrency(s.balance)}</td>
                    <td class="text-center">
                        <a href="${s.ledger_url}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size:0.75rem;">
                            <i class="fas fa-book me-1"></i> Ledger
                        </a>
                    </td>
                </tr>`;
            });
            if (!shopHtml) shopHtml = '<tr><td colspan="8" class="text-center text-muted py-3">No retail outlet receivables</td></tr>';
            if (document.getElementById('retailShopsTableBody')) document.getElementById('retailShopsTableBody').innerHTML = shopHtml;
            if (document.getElementById('modalShopsCountFooter')) document.getElementById('modalShopsCountFooter').innerText = shopList.length;
            if (document.getElementById('modalTotalShopsFooter')) document.getElementById('modalTotalShopsFooter').innerText = formatCurrency(rec.market_shops || 0);

            // 9. Fixed Assets Modal
            var assetList = bs.fixed_assets_list || [];
            var assetHtml = '';
            assetList.forEach(function(a, idx) {
                assetHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td><span class="badge bg-light text-muted border">${a.code || '-'}</span></td>
                    <td class="fw-semibold text-dark">${a.name}</td>
                    <td>${a.category || '-'}</td>
                    <td class="text-end text-muted">${formatCurrency(a.purchase_cost)}</td>
                    <td class="text-end fw-bold text-dark">${formatCurrency(a.current_value)}</td>
                    <td class="text-center"><span class="badge bg-success-subtle text-success">${a.status || 'Active'}</span></td>
                </tr>`;
            });
            if (!assetHtml) assetHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No fixed assets registered</td></tr>';
            if (document.getElementById('assetsTableBody')) document.getElementById('assetsTableBody').innerHTML = assetHtml;
            if (document.getElementById('modalAssetsCountFooter')) document.getElementById('modalAssetsCountFooter').innerText = assetList.length;
            if (document.getElementById('modalTotalAssetsFooter')) document.getElementById('modalTotalAssetsFooter').innerText = formatCurrency(bs.fixed_assets || 0);

            // 10. Vendors Modal
            var vendList = pay.vendors_list || [];
            var vendHtml = '';
            vendList.forEach(function(v, idx) {
                vendHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td class="fw-semibold text-dark">${v.name}</td>
                    <td>${v.phone || '-'}</td>
                    <td class="text-end fw-bold text-danger">${formatCurrency(v.balance)}</td>
                    <td class="text-center">
                        <a href="${v.ledger_url}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:0.75rem;">
                            <i class="fas fa-book me-1"></i> Ledger
                        </a>
                    </td>
                </tr>`;
            });
            if (!vendHtml) vendHtml = '<tr><td colspan="5" class="text-center text-muted py-3">No supplier payables outstanding</td></tr>';
            if (document.getElementById('vendorsTableBody')) document.getElementById('vendorsTableBody').innerHTML = vendHtml;
            if (document.getElementById('modalVendCountFooter')) document.getElementById('modalVendCountFooter').innerText = vendList.length;
            if (document.getElementById('modalTotalVendFooter')) document.getElementById('modalTotalVendFooter').innerText = formatCurrency(pay.vendors || 0);

            // 11. Distributor Advances Modal
            var advList = pay.advances_list || [];
            var advHtml = '';
            advList.forEach(function(ad, idx) {
                advHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td><span class="badge bg-light text-muted border">${ad.code || '-'}</span></td>
                    <td class="fw-semibold text-dark">${ad.name}</td>
                    <td>${ad.area || '-'}</td>
                    <td>${ad.phone || '-'}</td>
                    <td class="text-end fw-bold text-warning-emphasis">${formatCurrency(ad.balance)}</td>
                    <td class="text-center">
                        <a href="${ad.ledger_url}" target="_blank" class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size:0.75rem;">
                            <i class="fas fa-book me-1"></i> Ledger
                        </a>
                    </td>
                </tr>`;
            });
            if (!advHtml) advHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No distributor advance credits</td></tr>';
            if (document.getElementById('distAdvancesTableBody')) document.getElementById('distAdvancesTableBody').innerHTML = advHtml;
            if (document.getElementById('modalAdvCountFooter')) document.getElementById('modalAdvCountFooter').innerText = advList.length;
            if (document.getElementById('modalTotalAdvFooter')) document.getElementById('modalTotalAdvFooter').innerText = formatCurrency(pay.distributor_advances || 0);

            // 12. Claims Modal
            var claimsList = pay.claims_list || [];
            var claimsHtml = '';
            claimsList.forEach(function(clm, idx) {
                claimsHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">${clm.claim_no || '-'}</span></td>
                    <td class="fw-semibold">${clm.distributor_name}</td>
                    <td>${clm.claim_date}</td>
                    <td><span class="badge bg-warning-subtle text-warning-emphasis">${clm.type_label}</span></td>
                    <td class="text-end fw-bold text-danger">${formatCurrency(clm.amount)}</td>
                </tr>`;
            });
            if (!claimsHtml) claimsHtml = '<tr><td colspan="6" class="text-center text-muted py-3">No pending claims</td></tr>';
            if (document.getElementById('claimsTableBody')) document.getElementById('claimsTableBody').innerHTML = claimsHtml;
            if (document.getElementById('modalClaimsCountFooter')) document.getElementById('modalClaimsCountFooter').innerText = claimsList.length;
            if (document.getElementById('modalTotalClaimsFooter')) document.getElementById('modalTotalClaimsFooter').innerText = formatCurrency(pay.claims || 0);
        })
        .catch(err => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            console.error('Error loading snapshot:', err);
        });
}

// Live search in drill-down modals
function setupModalSearch(inputId, tableBodyId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('keyup', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#' + tableBodyId + ' tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setupModalSearch('fccStockSearch', 'stockTableBody');
    setupModalSearch('fccRawStockSearch', 'rawStockTableBody');
    setupModalSearch('fccCustSearch', 'customersTableBody');
    setupModalSearch('fccDistSearch', 'distributorsTableBody');
    setupModalSearch('fccShopSearch', 'retailShopsTableBody');
    setupModalSearch('fccAssetSearch', 'assetsTableBody');
    setupModalSearch('fccVendSearch', 'vendorsTableBody');
    setupModalSearch('fccAdvSearch', 'distAdvancesTableBody');
    setupModalSearch('fccClaimSearch', 'claimsTableBody');
    loadFinancialSnapshot();
});
</script>
@endsection
