<style>
/* ══════════ RESPONSIVE HEADER STYLING ══════════ */
@media (max-width: 991.98px) {
    .top_nav .container {
        padding-left: 8px !important;
        padding-right: 8px !important;
        flex-wrap: nowrap !important;
    }
    .rt_logo img {
        max-height: 32px !important;
        width: auto !important;
    }
    .navbar-nav-right {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 6px !important;
        margin-left: auto !important;
        margin-right: 4px !important;
    }
    .nav-bell-link {
        margin-right: 4px !important;
        font-size: 1.05rem !important;
        padding: 3px !important;
    }
    .branch-switch-container {
        max-width: 120px !important;
    }
    .branch-switch-container select {
        font-size: 0.72rem !important;
        padding-left: 4px !important;
        padding-right: 18px !important;
        height: 30px !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    .branch-switch-container .input-group-text {
        padding-left: 6px !important;
        padding-right: 6px !important;
        font-size: 0.72rem !important;
        height: 30px !important;
    }
    .nav-profile {
        margin-left: 2px !important;
    }
    .nav-profile .profile_name {
        max-width: 75px !important;
        display: inline-block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        vertical-align: middle !important;
        font-size: 0.75rem !important;
        color: #ffffff !important;
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
            <div class="container d-flex flex-row h-100 align-items-center">
                <div class="text-center rt_nav_wrapper d-flex align-items-center">
                    <a class="nav_logo rt_logo" href="{{ url('/home') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo" /></a>
                </div>
                    <ul class="navbar-nav navbar-nav-right mr-0 ml-auto align-items-center flex-row">
                        <li class="nav-item me-2 d-flex align-items-center">
                            @php
                                $headerPendingCount = 0;
                                if (auth()->check()) {
                                    $headerPendingCount = \App\Models\StockTransfer::where('status', 'pending')
                                        ->when(!is_all_branches(), function($q) {
                                            $q->where('to_branch_id', active_branch_id());
                                        })->count();
                                }
                            @endphp
                            <a href="{{ route('stock_transfers.index') }}" 
                               class="text-white nav-bell-link position-relative d-inline-flex align-items-center text-decoration-none me-2" 
                               title="Pending Stock Transfers"
                               style="font-size: 1.15rem; line-height: 1; padding: 4px;">
                                <i class="fas fa-bell"></i>
                                <span class="badge rounded-pill bg-danger shadow position-absolute {{ $headerPendingCount > 0 ? '' : 'd-none' }}" 
                                      id="navTransferBadge" 
                                      style="font-size: 0.65rem; top: -4px; right: -6px; padding: 2px 5px; border: 1.5px solid #ffffff;">
                                    {{ $headerPendingCount }}
                                </span>
                            </a>

                            <form action="{{ route('branch.switch') }}" method="POST" id="branchSwitchForm" class="d-flex align-items-center mb-0">
                                @csrf
                                <div class="input-group input-group-sm shadow-sm branch-switch-container" style="border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.2);">
                                    <span class="input-group-text bg-primary text-white border-0 px-2">
                                        <i class="fas fa-store"></i>
                                    </span>
                                    <select name="branch_id" onchange="document.getElementById('branchSwitchForm').submit()" 
                                            class="form-select form-select-sm border-0 font-weight-bold" 
                                            style="background-color: #f8fafc; color: #0f172a; cursor: pointer; padding-left: 8px; padding-right: 25px; height: 32px;">
                                        @php
                                            $isSuperAdminUser = auth()->check() && (auth()->user()->email === 'admin@admin.com' || auth()->user()->hasRole('Super Admin'));
                                        @endphp
                                        @if($isSuperAdminUser)
                                            <option value="all" {{ is_all_branches() ? 'selected' : '' }}>🏢 All Branches</option>
                                            @foreach(\App\Models\Branch::all() as $branch)
                                                <option value="{{ $branch->id }}" {{ (!is_all_branches() && active_branch_id() == $branch->id) ? 'selected' : '' }}>
                                                    📍 {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            @php
                                                $uBranchId = active_branch_id();
                                                $uBranch = \App\Models\Branch::find($uBranchId);
                                            @endphp
                                            <option value="{{ $uBranchId }}" selected>
                                                📍 {{ $uBranch->name ?? 'Main Branch' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </form>
                        </li>
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link dropdown-toggle p-0" href="#" data-bs-toggle="dropdown"
                                id="profileDropdown" aria-expanded="false">
                                <span class="profile_name">{{ Auth::user()->name }} <i
                                        class="feather ft-chevron-down"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown pt-2"
                                aria-labelledby="profileDropdown">
                                <span role="separator" class="divider"></span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="ti-power-off text-dark mr-3"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>

                    <button class="navbar-toggler align-self-center" type="button" id="mobileNavToggle"
                        aria-label="Toggle navigation">
                        <span class="feather ft-menu text-white"></span>
                    </button>

                </div>
            </div>
        <div class="nav-bottom">
            <div class="container">
                <ul class="nav page-navigation">
                    @can('Dashboard')
                    <li class="nav-item">
                        <a href="{{ url('/home') }}" class="nav-link"><i
                                class="menu_icon feather ft-home"></i><span class="menu-title">Dashboard</span></a>
                    </li>
                    @endcan

                    @canany(['Sales'])
                    <li class="nav-item">
                        <a href="{{ url('/sale/create') }}" class="nav-link">
                            <i class="menu_icon fas fa-cash-register"></i>
                            <span class="menu-title">Sale</span></a>
                    </li>
                    @endcanany

                    @canany(['Products','Category','Sub Category','Brands','Purchase','Purchase Return','Vendor','List Warehouse','Warehouse Stock','Stock Transfer','Sales','Sale Return','Bookings','Customer','Production','Raw Materials','Stock Adjustment'])
                    <li class="nav-item mega-menu">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-user-shield"></i>
                            <span class="menu-title">Management</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="submenu">
                            <div class="col-group-wrapper row">
                                @canany(['Products','Category','Sub Category','Brands'])
                                <!-- Products & Categories -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading">Products & Categories</p>
                                    <ul class="submenu-item">
                                        @can('Products')
                                        <li><a href="{{ route('product') }}"><i class="fas fa-box"></i> Products</a></li>
                                        @endcan
                                        @can('Category')
                                        <li><a href="{{ route('Category.home') }}"><i class="fas fa-list"></i> Category</a></li>
                                        @endcan
                                        @can('Sub Category')
                                        <li><a href="{{ route('subcategory.home') }}"><i class="fas fa-th-list"></i> Sub Category</a></li>
                                        @endcan
                                        @can('Brands')
                                        <li><a href="{{ route('Brand.home') }}"><i class="fas fa-trademark"></i> Brands</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany

                                @canany(['Purchase','Purchase Return','Vendor','Production','Raw Materials','Stock Adjustment'])
                                <!-- Purchase & Inventory -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading">Purchase & Inventory</p>
                                    <ul class="submenu-item">
                                        @can('Purchase')
                                        <li><a href="{{ route('Purchase.home') }}"><i class="fas fa-shopping-cart"></i> Purchase</a></li>
                                        @endcan
                                        @canany(['Production','Purchase'])
                                        <li><a href="{{ route('production.index') }}"><i class="fas fa-industry"></i> Own Production</a></li>
                                        @endcanany
                                        @canany(['Raw Materials','Purchase'])
                                        <li><a href="{{ route('raw-materials.index') }}"><i class="fas fa-database"></i> Raw Materials</a></li>
                                        @endcanany
                                        @canany(['Stock Adjustment','Purchase'])
                                        <li><a href="{{ route('stock-adjustment.index') }}"><i class="fas fa-sliders-h"></i> Stock Adjustment</a></li>
                                        @endcanany
                                        @can('Purchase Return')
                                        <li><a href="{{ route('purchase.return.index') }}"><i class="fas fa-undo"></i> Purchase Return</a></li>
                                        @endcan
                                        @can('Vendor')
                                        <li><a href="{{ route('vendors') }}"><i class="fas fa-truck"></i> Vendor</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany


                                @canany(['List Warehouse','Warehouse Stock','Stock Transfer'])
                                <!-- Inventory -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading">Inventory</p>
                                    <ul class="submenu-item">
                                        @can('List Warehouse')
                                        <li><a href="{{ url('warehouse') }}"><i class="fas fa-warehouse"></i> List Warehouse</a></li>
                                        @endcan
                                        @can('Warehouse Stock')
                                        <li><a href="{{ url('warehouse_stocks') }}"><i class="fas fa-boxes"></i> Stock Status</a></li>
                                        @endcan
                                        @can('Stock Transfer')
                                        <li><a href="{{ url('stock_transfers') }}"><i class="fas fa-exchange-alt"></i> Stock Transfer</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany
                                @canany(['Sales','Sale Return','Bookings','Customer'])
                                <!-- Sales & Customers -->
                                <div class="col-group col-md-3">
                                    <p class="category-heading">Sales & Customers</p>
                                    <ul class="submenu-item">
                                        @can('Sales')
                                        <li><a href="{{ url('sale') }}"><i class="fas fa-receipt"></i> Sales</a></li>
                                        @endcan
                                        @can('Sale Return')
                                        <li><a href="{{ url('sale-returns') }}"><i class="fas fa-receipt"></i> Sale Return</a></li>
                                        @endcan
                                        @can('Bookings')
                                        <li><a href="{{ route('bookings.index') }}"><i class="fas fa-receipt"></i> Bookings</a></li>
                                        @endcan
                                        @can('Customer')
                                        <li><a href="{{ url('customers') }}"><i class="fas fa-user"></i> Customer</a></li>
                                        @endcan
                                        <li><a href="{{ route('table.index') }}"><i class="fas fa-table"></i> Tables</a></li>
                                    </ul>
                                </div>
                                @endcanany

                            </div>
                        </div>
                    </li>
                    @endcanany


                    @canany(['Char Of Accounts','Expense Voucher','Receipts Voucher','Payment Voucher','Narrations'])
                    <!-- Vouchers Menu -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-file-invoice-dollar"></i>
                            <span class="menu-title">Vouchers</span>

                            <i class="menu-arrow"></i>
                        </a>

                        <div class="submenu">
                            <ul class="submenu-item">

                                @can('Char Of Accounts')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('view_all') }}">
                                        <i class="fa-solid fa-money-bill-wave mr-2"></i>
                                        <span>Char Of Accounts</span>
                                    </a>
                                </li>
                                @endcan

                                @can('Narrations')
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('narrations.index') }}">
                                        <i class="fa-solid fa-money-bill-wave mr-2"></i>
                                        <span>Narrations</span>
                                    </a>
                                </li> -->
                                @endcan

                                @can('Receipts Voucher')
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('all-recepit-vochers') }}">
                                        <i class="fa-solid fa-wallet mr-2"></i>
                                        <span>Receipts Voucher</span>
                                    </a>
                                </li> -->
                                @endcan

                                @can('Payment Voucher')
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('all-Payment-vochers') }}">
                                        <i class="fa-solid fa-wallet mr-2"></i>
                                        <span>Payment Voucher</span>
                                    </a>
                                </li> -->
                                @endcan

                                @can('Expense Voucher')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('all-expense-vochers') }}">
                                        <i class="fa-solid fa-money-bill-wave mr-2"></i>
                                        <span>Expense Voucher</span>
                                    </a>
                                </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                    @endcanany

                    @canany(['Item Stock Report','Purchase Report','Sale Report','Customer Ledger','Vendor Ledger','System Reports'])
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="menu_icon feather ft-clipboard"></i>
                            <span class="menu-title">Reports</span>
                            <i class="menu-arrow"></i>
                        </a>

                        <div class="submenu">
                            <ul class="submenu-item">

                                @can('Item Stock Report')
                                <li>
                                    <a href="{{ route('report.item_stock') }}">
                                        <i class="fa-solid fa-boxes"></i> Item Stock Report
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('report.branch_stock') }}">
                                        <i class="fa-solid fa-store"></i> Branch Stock Matrix
                                    </a>
                                </li>
                                @endcan

                                @can('Purchase Report')
                                <li>
                                    <a href="{{ route('report.purchase') }}">
                                        <i class="fa-solid fa-users"></i> Purchase Report
                                    </a>
                                </li>
                                @endcan

                                @can('Sale Report')
                                <li>
                                    <a href="{{ route('report.sale') }}">
                                        <i class="fa-solid fa-users"></i> Sale Report
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('report.sale.category') }}">
                                        <i class="fa-solid fa-users"></i> Sale Report Category
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('report.sale_closing') }}">
                                        <i class="fa-solid fa-file-invoice"></i> Sale Closing Report
                                    </a>
                                </li>

                                @endcan



                                @can('Customer Ledger')
                                <li>
                                    <a href="{{ route('report.customer.ledger') }}">
                                        <i class="fa-solid fa-users"></i> Customer Ledger
                                    </a>
                                </li>
                                @endcan

                                @can('Vendor Ledger')
                                <li>
                                    <a href="{{ route('report.vendor.ledger') }}">
                                        <i class="fa-solid fa-users"></i> Vendor Ledger
                                    </a>
                                </li>
                                @endcan

                                @can('System Reports')
                                <li>
                                    <a href="{{ route('System.Reports') }}">
                                        <i class="fa-solid fa-users"></i> System Reports
                                    </a>
                                </li>
                                @endcan

                                <li>
                                    <a href="{{ route('stock-adjustment.report') }}">
                                        <i class="fas fa-sliders-h"></i> Stock Adjustment Report
                                    </a>
                                </li>

                                @if (auth()->user()->email === 'admin@admin.com')
                                <li>
                                    <a href="{{ route('expense.vocher') }}">
                                        <i class="fa-solid fa-users"></i> Expense Report
                                    </a>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                    @endcanany

                    <!-- User Management Menu -->
                    @if (auth()->user()->email === 'admin@admin.com')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="menu_icon fas fa-users-cog"></i>
                            <span class="menu-title">User Management</span>

                            <i class="menu-arrow"></i>
                        </a>
                        <div class="submenu">
                            <ul class="submenu-item">
                                <li><a href="{{ route('users.index') }}"><i class="fa-solid fa-users"></i>
                                        Users</a></li>
                                <li><a href="{{ route('roles.index') }}"><i
                                            class="fa-solid fa-user-lock"></i> Roles</a></li>
                                <li><a href="{{ route('permissions.index') }}"><i
                                            class="fa-solid fa-user-lock"></i> Permissions</a></li>
                                <li><a href="{{ route('branch.index') }}"><i
                                            class="fa-solid fa-store"></i> Branches</a></li>
                            </ul>
                        </div>
                    </li>
                    @endif

                    @if (auth()->user()->email === 'admin@admin.com')
                    <li class="nav-item">
                        <a href="{{ route('cashbook') }}" class="nav-link">
                            <i class="menu_icon fas fa-users-cog"></i>
                            <span class="menu-title">CashBook</span>
                        </a>
                    </li>
                    @endif

                    @if (auth()->user()->email === 'admin@admin.com')
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link">
                            <i class="menu_icon fas fa-cog"></i>
                            <span class="menu-title">Settings</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
    </nav>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>