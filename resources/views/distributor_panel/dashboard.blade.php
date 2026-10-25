@extends('distributor_panel.layout.app')

@section('content')
<style>
    .dash-kpi-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dash-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    .kpi-icon-bubble {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .touch-btn {
        padding: 12px 14px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>

<div class="container-fluid px-3 px-md-4">
    <!-- Welcome Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3.5 p-md-4 mb-3 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(15, 118, 110, 0.45); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4; font-size: 0.75rem;">
                    <i class="fas fa-truck-moving me-1"></i> {{ $distributor->code }}
                </span>
                <span class="text-white-50 small">{{ $distributor->area ?: 'Karachi Market' }}</span>
            </div>
            <h4 class="mb-0 fw-bold text-white">{{ $distributor->name }}</h4>
            <p class="mb-0 text-white-50 small">Distributor & Order Booker Portal</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('distributor.payments.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 shadow-sm">
                <i class="fas fa-money-bill-wave me-1"></i> Pay Company
            </a>
            <a href="{{ route('distributor.orders.create') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 shadow-sm">
                <i class="fas fa-cart-arrow-down me-1"></i> Demand Stock
            </a>
            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm text-white rounded-pill px-3.5 py-2 shadow-sm fw-bold"
               style="background: #14b8a6; border: 1px solid #2dd4bf;">
                <i class="fas fa-cash-register me-1"></i> + Make Sale
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 py-2.5 mb-3" role="alert">
        <i class="fas fa-check-circle"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 1: COMPANY HISAB (Mall & Payments) -->
    <!-- ========================================== -->
    <div class="mb-3">
        <div class="section-title">
            <i class="fas fa-building text-teal" style="color: #0f766e;"></i> Company Hisab Kitab (Company Ledger Overview)
        </div>
        <div class="row g-2.5 g-md-3">
            <!-- Total Mall Aaya (Goods Received) -->
            <div class="col-12 col-md-4">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #0f766e !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Company Se Mall Aaya</span>
                        <div class="kpi-icon-bubble" style="background: rgba(15, 118, 110, 0.1); color: #0f766e;">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">Rs. {{ number_format($totalGoodsFromCompany, 2) }}</h4>
                    <span class="text-muted" style="font-size: 0.75rem;">Total goods billed from Head Office</span>
                </div>
            </div>

            <!-- Total Paid to Company -->
            <div class="col-12 col-md-4">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Company ko Paise Diye</span>
                        <div class="kpi-icon-bubble" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-0">Rs. {{ number_format($totalPaidToCompany, 2) }}</h4>
                    <span class="text-muted" style="font-size: 0.75rem;">Total payments deposited to company</span>
                </div>
            </div>

            <!-- Payable to Company (Net Due) -->
            <div class="col-12 col-md-4">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #ef4444 !important; background: #fffdfd;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-danger small fw-bold text-uppercase">Company ko Mazeed Dene Hain</span>
                        <div class="kpi-icon-bubble" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-0">Rs. {{ number_format($payableBalanceToCompany, 2) }}</h4>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted" style="font-size: 0.75rem;">Net Outstanding to Company</span>
                        <a href="{{ route('distributor.company_ledger.index') }}" class="small fw-semibold text-decoration-none text-danger" style="font-size: 0.75rem;">
                            View Ledger <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 2: MARKET SALES & RECOVERY OVERVIEW -->
    <!-- ========================================== -->
    <div class="mb-3">
        <div class="section-title">
            <i class="fas fa-store text-teal" style="color: #0f766e;"></i> Market Sales & Customer Recoveries
        </div>
        <div class="row g-2.5 g-md-3">
            <!-- Total Customer Recovery Collected -->
            <div class="col-6 col-lg-3">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Recovery Wasool Hui</span>
                        <i class="fas fa-cash-register text-success fs-5"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-0">Rs. {{ number_format($totalCustomerRecovery, 2) }}</h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Cash collected from retailers</span>
                </div>
            </div>

            <!-- Market Udhaar / Due from Retailers -->
            <div class="col-6 col-lg-3">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #f59e0b !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Market Udhaar (Due)</span>
                        <i class="fas fa-receipt text-warning fs-5"></i>
                    </div>
                    <h4 class="fw-bold text-warning mb-0">Rs. {{ number_format($totalMarketDue, 2) }}</h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Pending from retail stores</span>
                </div>
            </div>

            <!-- Today's Sales -->
            <div class="col-6 col-lg-3">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #3b82f6 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Today's Sales</span>
                        <i class="fas fa-chart-line text-primary fs-5"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">Rs. {{ number_format($todaySales, 2) }}</h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Month: Rs. {{ number_format($monthSales, 2) }}</span>
                </div>
            </div>

            <!-- Stock in Van Custody -->
            <div class="col-6 col-lg-3">
                <div class="dash-kpi-card p-3 h-100" style="border-left: 4px solid #0f766e !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Van Stock Units</span>
                        <i class="fas fa-truck text-teal fs-5" style="color: #0f766e;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">{{ number_format($totalStockUnits, 0) }} <span class="fs-6 fw-normal text-muted">pcs</span></h4>
                    <a href="{{ route('distributor.stock.index') }}" class="small fw-semibold text-decoration-none" style="font-size: 0.72rem; color: #0f766e;">
                        My Stock <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 3: MOBILE TOUCH LAUNCHPAD          -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3 bg-white">
        <h6 class="fw-bold text-dark mb-2.5 small text-uppercase" style="letter-spacing: 0.5px;">
            <i class="fas fa-bolt me-1 text-teal" style="color: #0f766e;"></i> Quick Action Shortcuts
        </h6>
        <div class="row g-2">
            <div class="col-6 col-md">
                <a href="{{ route('distributor.sales.create') }}" class="touch-btn text-white w-100 shadow-sm" style="background: linear-gradient(135deg, #0f766e, #0d9488);">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    <div>
                        <div class="lh-1">Make Sale</div>
                        <span class="text-white-50" style="font-size: 0.68rem;">Sell to Retailer</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md">
                <a href="{{ route('distributor.customers.index') }}" class="touch-btn text-white w-100 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-hand-holding-usd fs-5"></i>
                    <div>
                        <div class="lh-1">Collect Cash</div>
                        <span class="text-white-50" style="font-size: 0.68rem;">Customer Recovery</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md">
                <a href="{{ route('distributor.payments.index') }}" class="touch-btn text-white w-100 shadow-sm" style="background: linear-gradient(135deg, #090d16, #1e293b); border: 1px solid rgba(255,255,255,0.15);">
                    <i class="fas fa-money-bill-wave text-warning fs-5"></i>
                    <div>
                        <div class="lh-1">Pay Company</div>
                        <span class="text-white-50" style="font-size: 0.68rem;">Deposit & Slips</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md">
                <a href="{{ route('distributor.orders.create') }}" class="touch-btn btn-outline-secondary w-100 border text-dark">
                    <i class="fas fa-plus-circle text-teal fs-5" style="color: #0f766e;"></i>
                    <div>
                        <div class="lh-1">Demand Stock</div>
                        <span class="text-muted" style="font-size: 0.68rem;">Order from Company</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md">
                <a href="{{ route('distributor.company_ledger.index') }}" class="touch-btn btn-outline-secondary w-100 border text-dark">
                    <i class="fas fa-file-invoice text-primary fs-5"></i>
                    <div>
                        <div class="lh-1">Company Ledger</div>
                        <span class="text-muted" style="font-size: 0.68rem;">Statement & Dues</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 4: KIS KIS CUSTOMER SE KITNI RECOVERY MILI (RECOVERIES)-->
    <!-- ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Customer Recovery History -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                <div class="card-header bg-white py-2.5 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark small text-uppercase">
                        <i class="fas fa-hand-holding-usd me-1.5 text-success"></i> Customer Recoveries Received (Wasooli)
                    </h6>
                    <a href="{{ route('distributor.customers.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.78rem;">
                        View All Shops <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; color: #475569;">
                            <tr>
                                <th class="ps-3">Customer / Retail Shop</th>
                                <th>Date</th>
                                <th>Receipt / Ref</th>
                                <th class="text-end pe-3">Amount Received</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.85rem;">
                            @forelse($recentRecoveries as $rec)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $rec->customer->shop_name ?? 'Retail Shop' }}</div>
                                    <span class="text-muted" style="font-size: 0.72rem;">{{ $rec->customer->owner_name ?? '' }} • {{ $rec->customer->area ?? '' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($rec->date)->format('d M, Y') }}</span>
                                </td>
                                <td>
                                    <span class="font-monospace text-muted" style="font-size: 0.75rem;">{{ $rec->reference_no ?: '—' }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1 fs-6 fw-bold">
                                        + Rs. {{ number_format($rec->credit, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">
                                    <i class="fas fa-hand-holding-usd fa-2x mb-2 text-secondary opacity-50"></i>
                                    <div>No customer recoveries logged yet.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Retail Sales -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                <div class="card-header bg-white py-2.5 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark small text-uppercase">
                        <i class="fas fa-receipt me-1.5 text-teal" style="color: #0f766e;"></i> Recent Sales
                    </h6>
                    <a href="{{ route('distributor.sales.index') }}" class="small fw-semibold text-decoration-none" style="color: #0f766e; font-size: 0.78rem;">
                        All Sales <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; color: #475569;">
                            <tr>
                                <th class="ps-3">Invoice</th>
                                <th>Shop</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center pe-2">Bill</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.85rem;">
                            @forelse($recentSales as $sale)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold font-monospace text-dark" style="font-size: 0.78rem;">{{ $sale->invoice_no }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 130px;">{{ $sale->customer->shop_name ?? 'Walk-in' }}</div>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rs. {{ number_format($sale->grand_total, 2) }}
                                </td>
                                <td class="text-center pe-2">
                                    <a href="{{ route('distributor.sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">No sales made yet today.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
