@php
    $currentRoute = Route::currentRouteName();
    $currentType = request('type');
    $pendingCount = isset($pendingOrdersCount) ? $pendingOrdersCount : \App\Models\DistributorOrder::where('status', 'pending')->count();
    $pendingPaymentsCount = \App\Models\DistributorPayment::where('status', 'pending')->count();
    $pendingClaimsCount = \App\Models\DistributorClaim::where('status', 'pending')->count();
@endphp

<style>
    .dist-nav-container {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 0.85rem;
        border-radius: 12px;
        overflow: hidden;
    }
    .dist-nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.35rem 0.75rem;
        width: 100%;
        box-sizing: border-box;
    }
    .dist-nav-tabs-scroller {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
        min-width: 0;
        flex: 1 1 auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: -1px;
    }
    .dist-nav-tabs-scroller::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    .dist-nav-tab {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 12px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #626d80;
        text-decoration: none !important;
        transition: color 0.15s ease-in-out;
        border-bottom: 3px solid transparent;
        letter-spacing: -0.01em;
        white-space: nowrap !important;
        flex-shrink: 0;
    }
    .dist-nav-tab:hover {
        color: #1e293b;
    }
    .dist-nav-tab.active {
        color: #5861f5 !important;
        font-weight: 700;
        border-bottom-color: #5861f5;
    }
    .dist-nav-tab .badge-count {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
        border-radius: 50rem;
    }
    .dist-nav-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .dist-btn-action {
        font-size: 0.75rem !important;
        padding: 6px 12px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px;
        line-height: 1.35;
        white-space: nowrap !important;
        min-height: 34px !important;
        text-decoration: none !important;
        flex-shrink: 0;
    }

    /* Tablet & Mobile Breakpoint (Below 992px) */
    @media (max-width: 991.98px) {
        .dist-nav-container {
            padding: 0.45rem !important;
            margin-bottom: 0.75rem;
        }
        .dist-nav-inner {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.55rem !important;
            padding: 0 !important;
        }
        .dist-nav-tabs-scroller {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 auto !important;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 0.35rem;
        }
        .dist-nav-tab {
            padding: 6px 10px;
            font-size: 0.76rem;
        }
        .dist-nav-actions {
            width: 100% !important;
            max-width: 100% !important;
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.45rem !important;
        }
        .dist-nav-actions > *:nth-child(3):last-child {
            grid-column: 1 / -1 !important;
        }
        .dist-btn-action {
            width: 100% !important;
            min-height: 36px !important;
            font-size: 0.74rem !important;
            padding: 5px 8px !important;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<div class="dist-nav-container rounded-3 shadow-none">
    <div class="dist-nav-inner">
        <!-- Horizontal Tabs as per design -->
        <nav class="dist-nav-tabs-scroller">
            @canany(['Distributors', 'Distributor Management'])
            <!-- 1. Distributors -->
            <a href="{{ route('admin.distributors.index') }}" 
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.index' && empty($currentType) ? 'active' : '' }}">
                Distributors
            </a>

            <!-- 2. Order Bookers -->
            <a href="{{ route('admin.distributors.index', ['type' => 'booker']) }}" 
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.index' && $currentType === 'booker' ? 'active' : '' }}">
                Order Bookers
            </a>
            @endcanany

            @can('Distributor Customers')
            <!-- 3. Distributor Customers -->
            <a href="{{ route('admin.distributors.customers') }}" 
               class="dist-nav-tab {{ in_array($currentRoute, ['admin.distributors.customers', 'admin.distributors.customers.ledger']) ? 'active' : '' }}">
                Distributor Customers
            </a>
            @endcan

            @can('Distributor Sales')
            <!-- 4. Sales Register -->
            <a href="{{ route('admin.distributors.sales') }}" 
               class="dist-nav-tab {{ in_array($currentRoute, ['admin.distributors.sales', 'admin.distributors.sale_invoice']) ? 'active' : '' }}">
                Sales Register
            </a>
            @endcan

            @can('Distributor Orders')
            <!-- 5. Demand Requests -->
            <a href="{{ route('admin.distributors.orders') }}" 
               class="dist-nav-tab {{ in_array($currentRoute, ['admin.distributors.orders', 'admin.distributors.orders.show']) ? 'active' : '' }}">
                Demand Requests
                @if($pendingCount > 0)
                    <span class="badge bg-danger badge-count">{{ $pendingCount }}</span>
                @endif
            </a>
            @endcan

            @can('Distributor Custody Stocks')
            <!-- 6. Stock Transfers (Custody) -->
            <a href="{{ route('admin.distributors.stocks') }}" 
               class="dist-nav-tab {{ in_array($currentRoute, ['admin.distributors.stocks', 'admin.distributors.opening_stock']) ? 'active' : '' }}">
                Stock Transfers
            </a>
            @endcan

            @can('Distributor Secondary Sales')
            <!-- 7. Reports (Market Sales Audit) -->
            <a href="{{ route('admin.distributors.secondary_sales') }}" 
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.secondary_sales' ? 'active' : '' }}">
                Reports
            </a>
            @endcan

            <!-- Recovery & Hierarchy Ledger Report -->
            <a href="{{ route('report.distribution_recovery_ledger') }}" 
               class="dist-nav-tab {{ $currentRoute === 'report.distribution_recovery_ledger' ? 'active' : '' }}" style="color: #dc2626 !important; font-weight: 600;">
                <i class="fas fa-file-invoice-dollar me-1"></i> Recovery Ledger
            </a>

            @can('Distributor Payments')
            <!-- 8. Payments & Approvals -->
            <a href="{{ route('admin.distributors.payments') }}" 
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.payments' ? 'active' : '' }}">
                Payments
                @if($pendingPaymentsCount > 0)
                    <span class="badge rounded-pill badge-count shadow-sm" style="background: #ef4444 !important; color: #ffffff !important; font-weight: 700; box-shadow: 0 1px 4px rgba(239, 68, 68, 0.4);">{{ $pendingPaymentsCount }}</span>
                @endif
            </a>
            @endcan

            @can('Distributor Claims')
            <!-- 9. Claims (Trade Discounts, Damage, Freight) -->
            <a href="{{ route('admin.distributors.claims') }}" 
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.claims' ? 'active' : '' }}">
                Claims & Discounts
                @if($pendingClaimsCount > 0)
                    <span class="badge rounded-pill badge-count shadow-sm" style="background: #ef4444 !important; color: #ffffff !important; font-weight: 700; box-shadow: 0 1px 4px rgba(239, 68, 68, 0.4);">{{ $pendingClaimsCount }}</span>
                @endif
            </a>
            @endcan
            <!-- 9. Shop Bookings (Order Booker Market Demands) -->
            <a href="{{ route('distributor.shop_bookings.index') }}" 
               class="dist-nav-tab" style="color: #d97706 !important; font-weight: 700;">
                <i class="fas fa-boxes-packing text-warning me-1"></i> Shop Bookings
            </a>
        </nav>

        <!-- Right Side Quick Actions (Small, Compact Buttons) -->
        <div class="dist-nav-actions">
            <a href="{{ route('distributor.booker.create') }}" class="btn btn-outline-success dist-btn-action shadow-sm" title="Book Order for Shop">
                <i class="fas fa-plus-circle" style="font-size: 0.7rem;"></i> + Book Order
            </a>
            <a href="{{ route('distributor.dashboard') }}" class="btn text-white dist-btn-action shadow-sm" style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;" title="Open Distributor & Booker Portal">
                <i class="fas fa-truck-moving" style="font-size: 0.7rem;"></i> Distributor Portal
            </a>
            @if($currentRoute === 'admin.distributors.index')
            @can('Create Distributor')
            <button type="button" class="btn btn-outline-dark dist-btn-action shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#addDistributorModal">
                <i class="fas fa-user-plus" style="font-size: 0.65rem;"></i> Add {{ request('type') === 'booker' ? 'Booker' : 'Distributor' }}
            </button>
            @endcan
            @endif
            @if($currentRoute === 'admin.distributors.customers')
            @can('Create Distributor Customer')
            <button type="button" class="btn btn-outline-dark dist-btn-action shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#addRetailCustomerModal">
                <i class="fas fa-store" style="font-size: 0.65rem;"></i> + Add Retailer
            </button>
            @endcan
            @endif
        </div>
    </div>
</div>
