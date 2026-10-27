<style>
/* ══════════ RESPONSIVE HEADER STYLING ══════════ */
@media (max-width: 991.98px) {
    .top_nav .container, .top_nav .container-fluid {
        padding-left: 6px !important;
        padding-right: 6px !important;
        flex-wrap: nowrap !important;
    }
    .rt_logo img {
        max-height: 28px !important;
        width: auto !important;
    }
    .navbar-nav-right {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 4px !important;
        margin-left: auto !important;
        margin-right: 0 !important;
    }
    .nav-bell-link {
        width: 30px !important;
        height: 30px !important;
        font-size: 0.85rem !important;
        padding: 2px !important;
        border-radius: 8px !important;
    }
    .branch-switch-container {
        max-width: 100px !important;
        height: 30px !important;
    }
    .branch-switch-container select {
        font-size: 0.7rem !important;
        padding-left: 3px !important;
        padding-right: 14px !important;
        height: 30px !important;
        max-width: 70px !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }
    .branch-switch-container .input-group-text {
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 0.7rem !important;
        height: 30px !important;
    }
    .nav-profile {
        margin-left: 0 !important;
    }
    .user-avatar-circle {
        width: 28px !important;
        height: 28px !important;
        font-size: 0.7rem !important;
    }
    #mobileNavToggle {
        padding: 2px 4px !important;
        margin-left: 2px !important;
    }
}
</style>

<div class="container-scroller">

    <nav class="rt_nav_header horizontal-layout col-lg-12 col-12 p-0">
        <div class="top_nav flex-grow-1">
            <div class="container-fluid px-3 px-md-4 d-flex flex-row h-100 align-items-center">
                <div class="text-center rt_nav_wrapper d-flex align-items-center">
                    <a class="nav_logo rt_logo rt_logo_badge text-decoration-none me-3" href="{{ url('/home') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="{{ shop_name() }}" />
                    </a>
                    <div class="d-none d-md-block text-start">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 text-white fw-bold" style="font-size: 0.95rem; letter-spacing: -0.2px;">{{ shop_name() }} ERP</h6>
                            <span class="badge rounded-pill d-none d-lg-inline-flex align-items-center gap-1" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(147, 197, 253, 0.38); color: #93c5fd; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; letter-spacing: 0.2px;">
                                <i class="fas fa-code text-info" style="font-size: 0.65rem;"></i> By {{ developer_name() }}
                            </span>
                        </div>
                        <small class="text-white-50" style="font-size: 0.72rem;">{{ shop_tagline() }}</small>
                    </div>
                </div>
                    <ul class="navbar-nav navbar-nav-right mr-0 ml-auto align-items-center flex-row gap-2">
                        <li class="nav-item me-1 d-flex align-items-center gap-2">

                            @php
                                $headerPendingTransfers = 0;
                                $headerPendingDistPayments = 0;
                                $headerPendingDistOrders = 0;
                                if (auth()->check()) {
                                    try {
                                        $headerPendingTransfers = \App\Models\StockTransfer::where('status', 'pending')
                                            ->when(!is_all_branches(), function($q) {
                                                $q->where('to_branch_id', active_branch_id());
                                            })->count();
                                    } catch (\Throwable $e) {}

                                    try {
                                        $headerPendingDistPayments = \App\Models\DistributorPayment::where('status', 'pending')->count();
                                    } catch (\Throwable $e) {}

                                    try {
                                        $headerPendingDistOrders = \App\Models\DistributorOrder::where('status', 'pending')->count();
                                    } catch (\Throwable $e) {}
                                }
                                $headerPendingCount = $headerPendingTransfers + $headerPendingDistPayments + $headerPendingDistOrders;
                            @endphp
                            <div class="dropdown d-inline-block">
                                <a href="#" 
                                   class="nav-bell-link position-relative d-inline-flex align-items-center justify-content-center text-decoration-none shadow-sm" 
                                   data-bs-toggle="dropdown" aria-expanded="false"
                                   id="headerNotificationsDropdown"
                                   title="Pending Notifications & Approvals"
                                   style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); color: #ffffff; transition: all 0.2s ease; cursor: pointer;">
                                    <i class="fas fa-bell" style="font-size: 0.9rem;"></i>
                                    @if($headerPendingCount > 0)
                                    <span class="badge rounded-pill bg-danger shadow position-absolute" 
                                          id="navTransferBadge" 
                                          style="font-size: 0.6rem; top: -3px; right: -3px; padding: 2px 5px; border: 1.5px solid #090d16; box-shadow: 0 0 8px rgba(239, 68, 68, 0.7);">
                                        {{ $headerPendingCount }}
                                    </span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 mt-2" aria-labelledby="headerNotificationsDropdown"
                                     style="border-radius: 14px; min-width: 290px; background: #ffffff;">
                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark small">
                                            <i class="fas fa-bell me-1 text-primary"></i> Pending Approvals
                                        </span>
                                        @if($headerPendingCount > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $headerPendingCount }} New</span>
                                        @else
                                        <span class="badge bg-success rounded-pill">All Clear</span>
                                        @endif
                                    </div>

                                    <div class="py-1">
                                        <!-- 1. Distributor Payments -->
                                        <a href="{{ route('admin.distributors.payments', ['status' => 'pending']) }}" 
                                           class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center justify-content-between {{ $headerPendingDistPayments > 0 ? 'bg-light' : '' }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; background: #fee2e2; color: #dc2626;">
                                                    <i class="fas fa-money-check-alt" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <div>
                                                    <div class="small fw-bold text-dark">Distributor Payments</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Verification requests</div>
                                                </div>
                                            </div>
                                            @if($headerPendingDistPayments > 0)
                                            <span class="badge bg-danger rounded-pill">{{ $headerPendingDistPayments }}</span>
                                            @else
                                            <span class="badge bg-light text-muted rounded-pill">0</span>
                                            @endif
                                        </a>

                                        <!-- 2. Distributor Demand Orders -->
                                        <a href="{{ route('admin.distributors.orders') }}" 
                                           class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center justify-content-between {{ $headerPendingDistOrders > 0 ? 'bg-light' : '' }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; background: #fef3c7; color: #d97706;">
                                                    <i class="fas fa-cart-arrow-down" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <div>
                                                    <div class="small fw-bold text-dark">Demand Orders</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Stock requests</div>
                                                </div>
                                            </div>
                                            @if($headerPendingDistOrders > 0)
                                            <span class="badge bg-warning text-dark rounded-pill">{{ $headerPendingDistOrders }}</span>
                                            @else
                                            <span class="badge bg-light text-muted rounded-pill">0</span>
                                            @endif
                                        </a>

                                        <!-- 3. Stock Transfers -->
                                        <a href="{{ route('stock_transfers.index') }}" 
                                           class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center justify-content-between {{ $headerPendingTransfers > 0 ? 'bg-light' : '' }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; background: #e0f2fe; color: #0284c7;">
                                                    <i class="fas fa-exchange-alt" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <div>
                                                    <div class="small fw-bold text-dark">Stock Transfers</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Inter-branch transfers</div>
                                                </div>
                                            </div>
                                            @if($headerPendingTransfers > 0)
                                            <span class="badge bg-info text-white rounded-pill">{{ $headerPendingTransfers }}</span>
                                            @else
                                            <span class="badge bg-light text-muted rounded-pill">0</span>
                                            @endif
                                        </a>
                                    </div>

                                    @if($headerPendingDistPayments > 0)
                                    <div class="border-top pt-2 mt-1 text-center">
                                        <a href="{{ route('admin.distributors.payments', ['status' => 'pending']) }}" class="small fw-semibold text-danger text-decoration-none">
                                            <i class="fas fa-arrow-right me-1"></i> Review {{ $headerPendingDistPayments }} Pending Payment{{ $headerPendingDistPayments == 1 ? '' : 's' }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <form action="{{ route('branch.switch') }}" method="POST" id="branchSwitchForm" class="d-flex align-items-center mb-0">
                                @csrf
                                <div class="input-group input-group-sm shadow-sm branch-switch-container" 
                                     style="border-radius: 24px; overflow: hidden; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.16); transition: all 0.2s ease;">
                                    <span class="input-group-text border-0 px-2.5" style="background: rgba(255, 255, 255, 0.1); color: #38bdf8;">
                                        <i class="fas fa-store" style="font-size: 0.82rem;"></i>
                                    </span>
                                    <select name="branch_id" onchange="document.getElementById('branchSwitchForm').submit()" 
                                            class="form-select form-select-sm border-0 fw-bold" 
                                            style="background: transparent; color: #ffffff; cursor: pointer; padding-left: 8px; padding-right: 28px; height: 32px; font-size: 0.8rem;">
                                        @php
                                            $canSwitchBranch = auth()->check() && (auth()->user()->can('Switch Branch') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->email === 'admin@admin.com');
                                        @endphp
                                        @if($canSwitchBranch)
                                            <option value="all" {{ is_all_branches() ? 'selected' : '' }} style="color: #0f172a; background: #ffffff;">🏢 All Branches</option>
                                            @foreach(\App\Models\Branch::all() as $branch)
                                                <option value="{{ $branch->id }}" {{ (!is_all_branches() && active_branch_id() == $branch->id) ? 'selected' : '' }} style="color: #0f172a; background: #ffffff;">
                                                    📍 {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            @php
                                                $uBranchId = active_branch_id();
                                                $uBranch = \App\Models\Branch::find($uBranchId);
                                            @endphp
                                            <option value="{{ $uBranchId }}" selected style="color: #0f172a; background: #ffffff;">
                                                📍 {{ $uBranch->name ?? 'Main Branch' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </form>
                        </li>
                        @can('Settings')
                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}" 
                               class="nav-link px-2.5 py-1 d-flex align-items-center text-decoration-none shadow-sm"
                               title="System Settings"
                               style="background: {{ request()->is('settings*') ? 'rgba(15, 118, 110, 0.45)' : 'rgba(255, 255, 255, 0.08)' }}; border-radius: 24px; border: 1px solid {{ request()->is('settings*') ? 'rgba(20, 184, 166, 0.6)' : 'rgba(255, 255, 255, 0.16)' }}; backdrop-filter: blur(8px); height: 32px; color: #ffffff; transition: all 0.2s ease;">
                                <i class="fas fa-sliders-h me-1.5" style="font-size: 0.82rem; color: {{ request()->is('settings*') ? '#5eead4' : 'rgba(255,255,255,0.7)' }} !important;"></i>
                                <span class="fw-bold d-none d-sm-inline-block text-white" style="font-size: 0.8rem;">Settings</span>
                            </a>
                        </li>
                        @endcan
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link dropdown-toggle p-1 d-flex align-items-center text-decoration-none" href="#" data-bs-toggle="dropdown"
                                id="profileDropdown" aria-expanded="false" 
                                style="background: rgba(255, 255, 255, 0.08); border-radius: 24px; padding-right: 10px !important; border: 1px solid rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px);">
                                <div class="user-avatar-circle d-inline-flex align-items-center justify-content-center me-2" 
                                     style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #fff; font-weight: 700; font-size: 0.76rem; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="profile_name text-white fw-bold me-1 d-none d-md-inline-block" style="font-size: 0.82rem;">
                                    {{ Auth::user()->name }}
                                </span>
                                <i class="feather ft-chevron-down text-white-50" style="font-size: 0.75rem;"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end navbar-dropdown p-2 shadow-lg border-0"
                                aria-labelledby="profileDropdown" style="border-radius: 14px; min-width: 210px; margin-top: 10px;">
                                <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-3">
                                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.86rem;">{{ Auth::user()->name }}</p>
                                    <p class="mb-0 text-muted small" style="font-size: 0.75rem;">{{ Auth::user()->email }}</p>
                                </div>
                                @can('Settings')
                                <a class="dropdown-item rounded-2 py-2 d-flex align-items-center text-secondary" href="{{ route('settings.index') }}">
                                    <i class="fas fa-sliders-h me-2 text-teal" style="color: #0f766e;"></i> System Settings
                                </a>
                                @endcan
                                <a class="dropdown-item rounded-2 py-2 d-flex align-items-center text-secondary" href="{{ route('profile.edit') }}">
                                    <i class="feather ft-user me-2 text-primary"></i> Profile Settings
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 py-2 d-flex align-items-center text-danger fw-semibold">
                                        <i class="feather ft-power me-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>


                </div>
            </div>
        <div class="nav-bottom">
            <div class="container-fluid px-3 px-md-4">
                <ul class="nav page-navigation">
                    @can('Dashboard')
                    <li class="nav-item {{ request()->is('home*') ? 'active' : '' }}">
                        <a href="{{ url('/home') }}" class="nav-link">
                            <i class="menu_icon feather ft-home"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    @endcan

                    @canany(['Sales'])
                    <li class="nav-item {{ (request()->is('sale/create*') || request()->is('sale-screen*')) ? 'active' : '' }}">
                        <a href="{{ route('sale.screen') }}" class="nav-link">
                            <i class="menu_icon fas fa-cash-register"></i>
                            <span class="menu-title">Sale Screen</span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['Products','Category','Sub Category','Brands','Purchase','Purchase Return','Vendor','List Warehouse','Warehouse Stock','Stock Transfer','Sales','Sale Return','Bookings','Customer','Production','Raw Materials','Stock Adjustment'])
                    @php
                        $isMgmtActive = request()->is('product*') || request()->is('category*') || request()->is('subcategory*') || request()->is('Brand*') || request()->is('Purchase*') || request()->is('add/Purchase*') || request()->is('production*') || request()->is('raw-materials*') || request()->is('stock-adjustment*') || request()->is('vendors*') || request()->is('warehouse*') || request()->is('stock_transfers*') || request()->is('sale') || request()->is('sale-returns*') || request()->is('bookings*') || request()->is('customers*') || request()->is('table*') || request()->is('sale/pos*');
                    @endphp
                    <li class="nav-item mega-menu {{ $isMgmtActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-th-large"></i>
                            <span class="menu-title">Management</span>
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>
                        <div class="submenu">
                            <div class="col-group-wrapper row">
                                @canany(['Products','Category','Sub Category','Brands'])
                                <!-- Products & Categories -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading"><i class="fas fa-boxes me-1"></i> Products & Categories</p>
                                    <ul class="submenu-item">
                                        @can('Products')
                                        <li><a href="{{ route('product') }}"><i class="fas fa-box"></i> Products</a></li>
                                        @endcan
                                        @can('Category')
                                        <li><a href="{{ route('Category.home') }}"><i class="fas fa-tags"></i> Category</a></li>
                                        @endcan
                                        @can('Sub Category')
                                        <li><a href="{{ route('subcategory.home') }}"><i class="fas fa-sitemap"></i> Sub Category</a></li>
                                        @endcan
                                        @can('Brands')
                                        <li><a href="{{ route('Brand.home') }}"><i class="fas fa-copyright"></i> Brands</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @canany(['Purchase','Purchase Return','Vendor','Production','Raw Materials','Stock Adjustment'])
                                <!-- Purchase & Inventory -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading"><i class="fas fa-shopping-bag me-1"></i> Purchase & Factory</p>
                                    <ul class="submenu-item">
                                        @can('Purchase')
                                        <li><a href="{{ route('add_purchase') }}"><i class="fas fa-cart-plus"></i> Create Purchase</a></li>
                                        <li><a href="{{ route('Purchase.home') }}"><i class="fas fa-shopping-cart"></i> Purchase Invoices</a></li>
                                        @endcan
                                        @canany(['Production','Purchase'])
                                        <li><a href="{{ route('production.index') }}"><i class="fas fa-industry"></i> Own Production</a></li>
                                        @endcanany
                                        @canany(['Raw Materials','Purchase'])
                                        <li><a href="{{ route('raw-materials.index') }}"><i class="fas fa-cubes"></i> Raw Materials</a></li>
                                        @endcanany
                                        @canany(['Stock Adjustment','Purchase'])
                                        <li><a href="{{ route('stock-adjustment.index') }}"><i class="fas fa-sliders-h"></i> Stock Adjustment</a></li>
                                        @endcanany
                                        @can('Purchase Return')
                                        <li><a href="{{ route('purchase.return.index') }}"><i class="fas fa-undo"></i> Purchase Return</a></li>
                                        @endcan
                                        @can('Vendor')
                                        <li><a href="{{ route('vendors') }}"><i class="fas fa-truck-loading"></i> Vendors</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @canany(['List Warehouse','Warehouse Stock','Stock Transfer'])
                                <!-- Inventory -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading"><i class="fas fa-warehouse me-1"></i> Warehouse & Transfer</p>
                                    <ul class="submenu-item">
                                        @can('List Warehouse')
                                        <li><a href="{{ url('warehouse') }}"><i class="fas fa-building"></i> Warehouses List</a></li>
                                        @endcan
                                        @can('Warehouse Stock')
                                        <li><a href="{{ url('warehouse_stocks') }}"><i class="fas fa-layer-group"></i> Warehouse Stock</a></li>
                                        @endcan
                                        @can('Stock Transfer')
                                        <li><a href="{{ url('stock_transfers') }}"><i class="fas fa-exchange-alt"></i> Stock Transfers</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @canany(['Sales','Sale Return','Bookings','Customer'])
                                <!-- Sales & Customers -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading"><i class="fas fa-chart-line me-1"></i> Sales & Customers</p>
                                    <ul class="submenu-item">
                                        @can('Sales')
                                        <li><a href="{{ route('sale.pos') }}"><i class="fas fa-desktop"></i> Sale (POS)</a></li>
                                        <li><a href="{{ url('sale') }}"><i class="fas fa-file-invoice-dollar"></i> Sales Register</a></li>
                                        @endcan
                                        @can('Sale Return')
                                        <li><a href="{{ url('sale-returns') }}"><i class="fas fa-hand-holding-usd"></i> Sale Returns</a></li>
                                        @endcan
                                        @can('Bookings')
                                        <li><a href="{{ route('bookings.index') }}"><i class="fas fa-bookmark"></i> Advance Bookings</a></li>
                                        @endcan
                                        @can('Customer')
                                        <li><a href="{{ url('customers') }}"><i class="fas fa-users"></i> Customer Directory</a></li>
                                        @endcan
                                        @if(config('app.enable_restaurant_tables', false))
                                        <li><a href="{{ route('table.index') }}"><i class="fas fa-utensils"></i> Restaurant Tables</a></li>
                                        @endif
                                    </ul>
                                </div>
                                @endcanany

                            </div>
                        </div>
                    </li>
                    @endcanany

                    @canany(['Char Of Accounts','Expense Voucher','Receipts Voucher','Payment Voucher','Narrations'])
                    @php
                        $isVouchersActive = request()->is('view_all*') || request()->is('all-expense-vochers*') || request()->is('all-recepit-vochers*') || request()->is('all-Payment-vochers*') || request()->is('narrations*');
                    @endphp
                    <!-- Vouchers Menu -->
                    <li class="nav-item {{ $isVouchersActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-file-invoice-dollar"></i>
                            <span class="menu-title">Vouchers</span>
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>

                        <div class="submenu">
                            <ul class="submenu-item">
                                @can('Char Of Accounts')
                                <li>
                                    <a href="{{ route('view_all') }}">
                                        <i class="fas fa-project-diagram"></i>
                                        <span>Chart Of Accounts</span>
                                    </a>
                                </li>
                                @endcan

                                @if(auth()->check() && (auth()->user()->can('Payment Voucher') || auth()->user()->email === 'admin@admin.com' || auth()->user()->hasRole('Super Admin')))
                                <li>
                                    <a href="{{ route('all-Payment-vochers') }}">
                                        <i class="fas fa-wallet"></i>
                                        <span>Payment Vouchers (Vendor)</span>
                                    </a>
                                </li>
                                @endif

                                @if(auth()->check() && (auth()->user()->can('Receipts Voucher') || auth()->user()->email === 'admin@admin.com' || auth()->user()->hasRole('Super Admin')))
                                <li>
                                    <a href="{{ route('all-recepit-vochers') }}">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        <span>Receipt Vouchers (Customer)</span>
                                    </a>
                                </li>
                                @endif

                                @can('Expense Voucher')
                                <li>
                                    <a href="{{ route('all-expense-vochers') }}">
                                        <i class="fas fa-receipt"></i>
                                        <span>Expense Vouchers</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany

                    @php
                        $isDistActive = request()->is('admin/distributors*');
                        $pendingOrdersNavCount = 0;
                        if (\Illuminate\Support\Facades\Schema::hasTable('distributor_orders')) {
                            $pendingOrdersNavCount = \App\Models\DistributorOrder::where('status', 'pending')->count();
                        }
                    @endphp
                                        @php
                        $isAssetsActive = request()->is('fixed-assets*');
                    @endphp
                    <!-- Assets Menu (Machinery & Furniture) -->
                    <li class="nav-item {{ $isAssetsActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-boxes-stacked"></i>
                            <span class="menu-title">Assets</span>
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>
                        <div class="submenu">
                            <ul class="submenu-item">
                                <li>
                                    <a href="{{ route('assets.index') }}">
                                        <i class="fas fa-list"></i>
                                        <span>Assets Directory</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('assets.create') }}">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Register Asset</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('assets.categories.index') }}">
                                        <i class="fas fa-tags"></i>
                                        <span>Asset Categories</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('assets.report') }}">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        <span>Valuation Report</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Distributors Menu -->
                    <li class="nav-item {{ $isDistActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-truck-loading"></i>
                            <span class="menu-title">Distributors</span>
                            @if($pendingOrdersNavCount > 0)
                            <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.65rem; padding: 2px 6px;">{{ $pendingOrdersNavCount }}</span>
                            @endif
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>
                        <div class="submenu">
                            <ul class="submenu-item">
                                <li>
                                    <a href="{{ route('admin.distributors.index') }}">
                                        <i class="fas fa-users-cog"></i>
                                        <span>Distributors Directory</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distributors.customers') }}">
                                        <i class="fas fa-store"></i>
                                        <span>Distributor Customers</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distributors.sales') }}">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        <span>Distributor Sales Register</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distributors.orders') }}">
                                        <i class="fas fa-clipboard-list"></i>
                                        <span>Demand Orders</span>
                                        @if($pendingOrdersNavCount > 0)
                                        <span class="badge bg-danger ms-auto">{{ $pendingOrdersNavCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distributors.stocks') }}">
                                        <i class="fas fa-boxes"></i>
                                        <span>Custody Stocks</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distributors.secondary_sales') }}">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Secondary Market Sales</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    @canany(['Item Stock Report','Branch Stock Report','Purchase Report','Sale Report','Category Sale Report','Daily Sale Closing Report','Profit Loss Report','Customer Ledger','Vendor Ledger','Expense Report','Expense Audit Report','Stock Adjustment Audit Report','System Reports'])
                    @php
                        $isReportsActive = request()->is('report*') || request()->is('System/Reports*') || request()->is('expense-vocher*') || request()->is('stock-adjustment-report*');
                    @endphp
                    <li class="nav-item {{ $isReportsActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon feather ft-pie-chart"></i>
                            <span class="menu-title">Reports</span>
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>

                        <div class="submenu">
                            <ul class="submenu-item">
                                @canany(['Item Stock Report'])
                                <li><a href="{{ route('report.item_stock') }}"><i class="fas fa-boxes"></i> Item Stock Report</a></li>
                                @endcanany
                                @canany(['Branch Stock Report', 'Item Stock Report'])
                                <li><a href="{{ route('report.branch_stock') }}"><i class="fas fa-store"></i> Branch Stock Matrix</a></li>
                                @endcanany

                                @can('Purchase Report')
                                <li><a href="{{ route('report.purchase') }}"><i class="fas fa-shopping-basket"></i> Purchase Report</a></li>
                                @canany(['Production', 'Purchase', 'Sale Report', 'System Reports'])
                                <li><a href="{{ route('report.production') }}"><i class="fas fa-industry"></i> Production Report</a></li>
                                @endcanany
                                @endcan

                                @can('Sale Report')
                                <li><a href="{{ route('report.sale') }}"><i class="fas fa-chart-bar"></i> Sale Report</a></li>
                                @endcan
                                @canany(['Category Sale Report', 'Sale Report'])
                                <li><a href="{{ route('report.sale.category') }}"><i class="fas fa-chart-pie"></i> Category-wise Sales</a></li>
                                @endcanany
                                @canany(['Daily Sale Closing Report', 'Sale Report'])
                                <li><a href="{{ route('report.sale_closing') }}"><i class="fas fa-file-invoice-dollar"></i> Daily Sale Closing</a></li>
                                @endcanany
                                @canany(['Profit Loss Report', 'Sale Report'])
                                <li><a href="{{ route('report.profit_loss') }}"><i class="fas fa-balance-scale"></i> Profit & Loss Report</a></li>
                                @canany(['Profit Loss Report', 'Sale Report', 'System Reports'])
                                <li><a href="{{ route('report.balance_sheet') }}"><i class="fas fa-file-invoice-dollar"></i> Balance Sheet</a></li>
                                @endcanany
                                @endcanany

                                @can('Customer Ledger')
                                <li><a href="{{ route('report.customer.ledger') }}"><i class="fas fa-user-check"></i> Customer Ledgers</a></li>
                                @endcan

                                @can('Vendor Ledger')
                                <li><a href="{{ route('report.vendor.ledger') }}"><i class="fas fa-truck"></i> Vendor Ledgers</a></li>
                                @endcan

                                @can('System Reports')
                                <li><a href="{{ route('System.Reports') }}"><i class="fas fa-file-alt"></i> System Reports</a></li>
                                @endcan

                                @canany(['Stock Adjustment Audit Report', 'Stock Adjustment'])
                                <li><a href="{{ route('stock-adjustment.report') }}"><i class="fas fa-sliders-h"></i> Stock Adjustment Audit</a></li>
                                @endcanany

                                @canany(['Expense Audit Report', 'Expense Report', 'Expense Voucher'])
                                <li><a href="{{ route('expense.vocher') }}"><i class="fas fa-wallet"></i> Expense Audit Report</a></li>
                                @endcanany
                            </ul>
                        </div>
                    </li>
                    @endcanany

                    <!-- User Management Menu -->
                    @canany(['Users','Roles','Permissions','Branch'])
                    @php
                        $isUserMgmtActive = request()->is('users*') || request()->is('roles*') || request()->is('permissions*') || request()->is('branch*');
                    @endphp
                    <li class="nav-item {{ $isUserMgmtActive ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-users-cog"></i>
                            <span class="menu-title">User Management</span>
                            <i class="menu-arrow feather ft-chevron-down"></i>
                        </a>
                        <div class="submenu">
                            <ul class="submenu-item">
                                @can('Users')
                                <li><a href="{{ route('users.index') }}"><i class="fas fa-user-shield"></i> System Users</a></li>
                                @endcan
                                @can('Roles')
                                <li><a href="{{ route('roles.index') }}"><i class="fas fa-user-tag"></i> Roles & Access</a></li>
                                @endcan
                                @can('Permissions')
                                <li><a href="{{ route('permissions.index') }}"><i class="fas fa-key"></i> Permissions Matrix</a></li>
                                @endcan
                                @can('Branch')
                                <li><a href="{{ route('branch.index') }}"><i class="fas fa-store-alt"></i> Branches Setup</a></li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany

                    @can('Cashbook')
                    <li class="nav-item {{ request()->is('cashbook*') ? 'active' : '' }}">
                        <a href="{{ route('cashbook') }}" class="nav-link">
                            <i class="menu_icon fas fa-book-open"></i>
                            <span class="menu-title">CashBook</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
        </div>
    </nav>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Mobile Hamburger Button Toggle
    var mobileBtn = document.getElementById('mobileNavToggle');
    if (mobileBtn) {
        mobileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var navBottom = document.querySelector('.rt_nav_header.horizontal-layout .nav-bottom');
            if (navBottom) {
                navBottom.classList.toggle('header-toggled');
            }
        });
    }

    // 2. Click-to-toggle dropdown submenus (Management, Vouchers, Reports, User Management)
    var navItems = document.querySelectorAll('.rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item');
    navItems.forEach(function(item) {
        var link = item.querySelector('.nav-link');
        var submenu = item.querySelector('.submenu');
        if (submenu && link) {
            link.addEventListener('click', function(e) {
                // Prevent jumping if link is anchor or has submenus
                var href = link.getAttribute('href');
                if (!href || href === '#' || href === 'javascript:void(0)' || submenu) {
                    e.preventDefault();
                    e.stopPropagation();

                    var isAlreadyOpen = item.classList.contains('show-submenu');

                    // Close all other submenus
                    navItems.forEach(function(other) {
                        if (other !== item) {
                            other.classList.remove('show-submenu');
                        }
                    });

                    // Toggle current submenu
                    if (!isAlreadyOpen) {
                        item.classList.add('show-submenu');
                    } else {
                        item.classList.remove('show-submenu');
                    }
                }
            });
        }
    });

    // Close submenus on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.rt_nav_header')) {
            navItems.forEach(function(item) {
                item.classList.remove('show-submenu');
            });
        }
    });
});
</script>