@php
    $currentRoute = Route::currentRouteName();
    $currentType = request('type');
    $pendingCount = isset($pendingOrdersCount) ? $pendingOrdersCount : \App\Models\DistributorOrder::where('status', 'pending')->count();
    $pendingPaymentsCount = \App\Models\DistributorPayment::where('status', 'pending')->count();
@endphp

<style>
    .dist-nav-container {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 0.85rem;
    }
    .dist-nav-tabs-scroller {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
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
        padding: 9px 13px;
        font-size: 0.84rem;
        font-weight: 500;
        color: #626d80;
        text-decoration: none !important;
        transition: color 0.15s ease-in-out;
        border-bottom: 3px solid transparent;
        letter-spacing: -0.01em;
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
    .dist-btn-action {
        font-size: 0.74rem !important;
        padding: 4px 11px !important;
        border-radius: 50rem !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        line-height: 1.4;
    }
</style>

<div class="dist-nav-container rounded-3 shadow-none">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-2.5 px-md-3 pt-1">
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
               class="dist-nav-tab {{ $currentRoute === 'admin.distributors.stocks' ? 'active' : '' }}">
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
        </nav>

        <!-- Right Side Quick Actions (Small, Compact Buttons) -->
        <div class="d-flex align-items-center gap-1.5 py-1">
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
