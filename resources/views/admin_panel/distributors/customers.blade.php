@extends('admin_panel.layout.app')

@section('content')
<style>
    .dist-module-container {
        padding-left: 18px !important;
        padding-right: 18px !important;
        padding-top: 10px !important;
        padding-bottom: 30px !important;
    }
    @media (min-width: 992px) {
        .dist-module-container {
            padding-left: 28px !important;
            padding-right: 28px !important;
            padding-top: 16px !important;
        }
    }
    .dist-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 14px 18px !important;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dist-mobile-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef2f6 !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
        @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #e2e8f0 !important;
        }
    }
    .dist-btn-action {
        font-size: 0.74rem !important;
        padding: 4px 11px !important;
        border-radius: 50rem !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-tier-policy {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1.5px solid #cbd5e1 !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    .btn-tier-policy:hover, .btn-tier-policy:focus {
        background: #0f766e !important;
        color: #ffffff !important;
        border-color: #0f766e !important;
    }
    .btn-tier-policy:hover i, .btn-tier-policy:focus i {
        color: #ffffff !important;
    }

</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 mt-1">
        <div>
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                <i class="fas fa-store text-teal" style="color: #0f766e;"></i>
                Distributor Retail Customers & Ledgers
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.68rem; padding: 3px 8px;">Retail Network</span>
            </h5>
            <span class="text-muted" style="font-size: 0.78rem;">
                Browse retail shops registered under distributors and bookers, audit outstanding balances and view detailed transaction ledgers.
            </span>
        </div>
        <div>
            <button type="button" class="btn btn-tier-policy dist-btn-action shadow-sm me-2"
                    style="padding: 6px 14px !important;"
                    data-bs-toggle="modal" data-bs-target="#manageTiersModal">
                <i class="fas fa-layer-group text-primary" style="font-size: 0.72rem;"></i> Customer Tiers & Policy Schemes
            </button>
            <button type="button" class="btn text-white dist-btn-action shadow-sm"
                    style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6; padding: 6px 14px !important;"
                    data-bs-toggle="modal" data-bs-target="#addRetailCustomerModal">
                <i class="fas fa-plus" style="font-size: 0.7rem;"></i> + Add New Retail Shop
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="dist-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Retail Shops</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.35rem;">{{ $totalCustomers }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 1.15rem;">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="dist-kpi-card" style="border-left: 4px solid #f59e0b !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Shops with Due Balance</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.35rem;">{{ $customersWithDueCount }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 1.15rem;">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="dist-kpi-card" style="border-left: 4px solid #ef4444 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Market Due (Udhaar)</span>
                    <h3 class="fw-bold mb-0 text-danger mt-1" style="font-size: 1.3rem;">Rs. {{ number_format($totalMarketDue, 2) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.15rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="dist-kpi-card" style="border-left: 4px solid #6366f1 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Active Distributors</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.35rem;">{{ count($distributors) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(99, 102, 241, 0.1); color: #6366f1; font-size: 1.15rem;">
                    <i class="fas fa-truck-loading"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.customers') }}" class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="form-control border-start-0" 
                               placeholder="Search shop, proprietor, phone, area...">
                    </div>
                </div>

                <!-- Distributor Filter -->
                <div class="col-12 col-sm-6 col-md-3">
                    <select name="distributor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- All Distributors --</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Booker Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="booker_user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- All Bookers --</option>
                        @foreach($distributors as $d)
                            @if($d->bookers && $d->bookers->count() > 0)
                                <optgroup label="{{ $d->name }}">
                                    @foreach($d->bookers as $bkr)
                                    <option value="{{ $bkr->id }}" {{ request('booker_user_id') == $bkr->id ? 'selected' : '' }}>
                                        {{ $bkr->name }}
                                    </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Balance Status Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="balance_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- All Accounts (Any Balance) --</option>
                        <option value="due" {{ request('balance_status') === 'due' ? 'selected' : '' }}>Outstanding Due Only (&gt; 0)</option>
                        <option value="zero" {{ request('balance_status') === 'zero' ? 'selected' : '' }}>Zero / Cleared Balance Only</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 col-md-2 d-flex gap-1 justify-content-md-end">
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-3 fw-semibold shadow-sm w-100" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'distributor_id', 'balance_status']))
                    <a href="{{ route('admin.distributors.customers') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="Clear Filters">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-none d-md-block">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-ul me-2 text-teal" style="color: #0f766e;"></i> Retail Customers Directory
                @if(request('distributor_id'))
                    @php $currentDist = $distributors->firstWhere('id', request('distributor_id')); @endphp
                    @if($currentDist)
                        <span class="badge bg-light text-dark border ms-2">Distributor: {{ $currentDist->name }}</span>
                    @endif
                @endif
            </h6>
            <span class="text-muted small">Showing {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Shop / Store Name</th>
                        <th>Assigned Distributor</th>
                        <th>Phone / Contact</th>
                        <th>Area / Market</th>
                        <th class="text-center">Transactions</th>
                        <th class="text-end">Current Balance (Due)</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($customers as $c)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                     style="width: 36px; height: 36px; background: linear-gradient(135deg, #0f766e, #14b8a6); font-size: 0.82rem;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.distributors.customers.ledger', $c->id) }}" class="fw-bold text-dark text-decoration-none hover-underline">
                                        {{ $c->shop_name }}
                                    </a>
                                    <div class="text-muted small">Prop: {{ $c->owner_name ?: 'N/A' }}</div>
                                    <div class="mt-1">
                                        <span class="badge rounded-pill {{ ($c->approved_discount ?? 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }} px-2 py-0.5" style="font-size: 0.7rem;">
                                            <i class="fas fa-percentage me-1"></i> Discount Limit: {{ (float)($c->approved_discount ?? 0) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->distributor)
                            <a href="{{ route('admin.distributors.customers', ['distributor_id' => $c->distributor->id]) }}" class="text-decoration-none">
                                <div class="fw-bold text-dark">{{ $c->distributor->name }}</div>
                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ $c->distributor->code }} &bull; {{ ucfirst($c->distributor->type ?? 'distributor') }}
                                </span>
                            </a>
                            @else
                            <span class="text-muted small">Unassigned</span>
                            @endif

                            @if($c->booker)
                            <div class="mt-1">
                                <span class="badge rounded-pill border px-2 py-0.5" style="background: rgba(15, 118, 110, 0.1); color: #0f766e; border-color: rgba(15, 118, 110, 0.25) !important; font-size: 0.7rem;">
                                    <i class="fas fa-user-tie me-1"></i> Booker: {{ $c->booker->name }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">
                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.72rem;"></i>
                                {{ $c->phone ?: 'â€”' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $c->area ?: 'General Market' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1" style="font-size: 0.75rem;">
                                <i class="fas fa-receipt me-1 text-muted"></i> {{ $c->ledgers_count }} entries
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $c->current_balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 0.95rem;">
                                Rs. {{ number_format($c->current_balance, 2) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                @can('Distributor Customer Ledger')
                                <!-- View Customer Ledger -->
                                <a href="{{ route('admin.distributors.customers.ledger', $c->id) }}" 
                                   class="btn btn-outline-primary rounded-pill px-2.5 py-1" 
                                   title="View Customer Ledger & Statement">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Ledger
                                </a>
                                @endcan

                                @can('Create Distributor Payment')
                                <!-- Collect Payment Modal Trigger -->
                                <button type="button" class="btn btn-outline-success rounded-pill px-2 py-1 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#receivePaymentModal{{ $c->id }}"
                                        title="Record Payment Collection">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </button>
                                @endcan

                                @can('Edit Distributor Customer')
                                <!-- Edit Customer Modal Trigger -->
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-2 py-1 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $c->id }}"
                                        title="Edit Shop Details">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan

                                @can('Delete Distributor Customer')
                                <!-- Delete Customer Modal Trigger -->
                                <button type="button" class="btn btn-outline-danger rounded-pill px-2 py-1 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#deleteCustomerModal{{ $c->id }}"
                                        title="Delete Customer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-store-slash fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Retail Customers Found</p>
                            <span class="small">No customer matches the selected filters. Click "+ Add New Retail Shop" to register a shop under a distributor.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }}</span>
            {{ $customers->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile View Cards -->
    <div class="d-block d-md-none mb-4">
        @forelse($customers as $c)
        <div class="dist-mobile-card">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2 border-bottom">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $c->shop_name }}</h6>
                    <span class="text-muted small">Prop: {{ $c->owner_name ?: 'N/A' }}</span>
                </div>
                <span class="badge {{ $c->current_balance > 0 ? 'bg-danger-subtle text-danger border border-danger' : 'bg-success-subtle text-success border border-success' }} px-2 py-1">
                    Rs. {{ number_format($c->current_balance, 2) }}
                </span>
            </div>

            <div class="small mb-2">
                <div class="text-muted"><i class="fas fa-truck-loading me-1"></i> Distributor: <strong class="text-dark">{{ $c->distributor->name ?? 'N/A' }}</strong></div>
                @if($c->booker)
                <div class="text-muted"><i class="fas fa-user-tie me-1" style="color: #0f766e;"></i> Booker: <strong class="text-dark">{{ $c->booker->name }}</strong></div>
                @endif
                <div class="text-muted"><i class="fas fa-phone-alt me-1"></i> Phone: <span class="text-dark">{{ $c->phone ?: 'â€”' }}</span></div>
                <div class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> Area: <span class="text-dark">{{ $c->area ?: 'General' }}</span></div>
                <div class="mt-1.5 d-flex flex-wrap gap-1">
                    <span class="badge bg-primary-subtle text-primary border px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $c->tier?->name ?? 'Standard' }}</span>
                    <span class="badge bg-success-subtle text-success border px-1.5 py-0.5" style="font-size: 0.68rem;">{{ (float)$c->effective_discount }}% Disc</span>
                    @if($c->effective_trade_offer_buy_qty > 0)
                    <span class="badge bg-info-subtle text-info-emphasis border px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $c->scheme_label }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2">
                <span class="badge bg-light text-dark border px-2 py-1 small">
                    {{ $c->ledgers_count }} records
                </span>
                <div class="btn-group btn-group-sm">
                    @can('Distributor Customer Ledger')
                    <a href="{{ route('admin.distributors.customers.ledger', $c->id) }}" class="btn btn-outline-primary rounded-pill px-3 py-1">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Ledger
                    </a>
                    @endcan
                    @can('Create Distributor Payment')
                    <button type="button" class="btn btn-outline-success rounded-pill px-2.5 py-1 ms-1"
                            data-bs-toggle="modal" data-bs-target="#receivePaymentModal{{ $c->id }}">
                        <i class="fas fa-hand-holding-usd"></i>
                    </button>
                    @endcan
                    @can('Edit Distributor Customer')
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-2.5 py-1 ms-1"
                            data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $c->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
            <i class="fas fa-store-slash fa-2x mb-2 text-secondary opacity-50"></i>
            <p class="mb-0">No Retail Customers Found</p>
        </div>
        @endforelse

        @if($customers->hasPages())
        <div class="mt-3">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: ADD NEW RETAIL CUSTOMER                 -->
<!-- ============================================== -->
<div class="modal fade" id="addRetailCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.store') }}" method="POST">
                @csrf
                <div class="modal-header py-2.5 px-3.5 text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-store-alt fs-5"></i>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Register New Retail Customer</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5 p-md-4">
                    <div class="row g-3 g-md-4">
                        <!-- Left Column: Distributor & Booker Mapping & Shop Info -->
                        <div class="col-12 col-md-6 border-end-md pe-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-network-wired text-teal me-1.5" style="color: #0f766e;"></i> ASSIGNMENT & SHOP IDENTITY
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Assign to Distributor *</label>
                                <select name="distributor_id" id="addCustomerDistributorSelect" class="form-select form-select-sm" required>
                                    <option value="">-- Select Distributor --</option>
                                    @foreach($distributors as $d)
                                    <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }} ({{ $d->code }} - {{ ucfirst($d->type ?? 'distributor') }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-user-tie text-teal me-1" style="color: #0f766e;"></i> Assign Order Booker *
                                </label>
                                <select name="booker_user_id" id="addCustomerBookerSelect" class="form-select form-select-sm" required>
                                    <option value="">-- Select Order Booker --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Shop / Store Name *</label>
                                <input type="text" name="shop_name" class="form-control form-control-sm" placeholder="e.g. Madina Super Store" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Owner / Proprietor</label>
                                    <input type="text" name="owner_name" class="form-control form-control-sm" placeholder="Owner Name">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Mobile / Phone</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="0300-1234567">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Location, Discount & Opening Balance -->
                        <div class="col-12 col-md-6 ps-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-sliders-h text-teal me-1.5" style="color: #0f766e;"></i> ROUTE, DISCOUNT & OPENING CREDIT
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Area / Market Route</label>
                                <input type="text" name="area" class="form-control form-control-sm" placeholder="e.g. Malir Halt Market, Korangi #4">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Full Shop Address</label>
                                <input type="text" name="address" class="form-control form-control-sm" placeholder="Optional physical location address">
                            </div>

                            <!-- Customer Tier & Schemes (Plan 1) -->
                            <div class="mb-3 p-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                                <div class="mb-2">
                                    <label class="form-label fw-bold small text-dark mb-1">
                                        <i class="fas fa-layer-group text-primary me-1"></i> Customer Category / Pricing Tier <span class="text-danger">*</span>
                                    </label>
                                    <select name="tier_id" id="add_tier_id" class="form-select form-select-sm customer-tier-select" data-prefix="add" style="border: 1.5px solid #0f766e; font-weight: 600;">
                                        <option value="">-- Direct / Custom Assignment --</option>
                                        @foreach($tiers ?? [] as $t)
                                        <option value="{{ $t->id }}"
                                                data-discount="{{ (float)$t->discount_percentage }}"
                                                data-to-buy="{{ (float)$t->trade_offer_buy_qty }}"
                                                data-to-free="{{ (float)$t->trade_offer_free_qty }}">
                                            {{ $t->name }} &bull; ({{ (float)$t->discount_percentage }}% Disc | {{ $t->scheme_label }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted" style="font-size: 0.71rem;">
                                        Tier select karne se Company Discount % aur Trade Offer khud bakhud fill ho jayenge.
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-dark mb-1">Company Approved Discount (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0" max="100" name="approved_discount" id="add_approved_discount" class="form-control fw-bold text-success" value="0.00" placeholder="e.g. 5">
                                            <span class="input-group-text fw-bold">%</span>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.7rem;">Distributor na is se zyada de sakta he na kam</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Scheme: Buy Qty</label>
                                        <input type="number" step="0.01" min="0" name="trade_offer_buy_qty" id="add_to_buy" class="form-control form-control-sm fw-bold" value="0" placeholder="e.g. 12">
                                        <span class="text-muted" style="font-size: 0.7rem;">Khareedari Qty</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Free Qty (T.O)</label>
                                        <input type="number" step="0.01" min="0" name="trade_offer_free_qty" id="add_to_free" class="form-control form-control-sm fw-bold text-primary" value="0" placeholder="e.g. 1">
                                        <span class="text-muted" style="font-size: 0.7rem;">Muft item Qty</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark mb-1">Initial Opening Balance (Previous Udhaar / Debt)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3.5 d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4 fw-semibold shadow-sm" style="background: #0f766e;">
                        <i class="fas fa-check me-1"></i> Save Shop Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- LOOP MODALS (Edit, Payment, Delete)            -->
<!-- ============================================== -->
@foreach($customers as $c)
<!-- 1. Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.update', $c->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2.5 px-3.5 text-white" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit fs-5"></i>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Edit Retailer: {{ $c->shop_name }}</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5 p-md-4">
                    <div class="row g-3 g-md-4">
                        <!-- Left Column: Distributor & Booker Mapping & Shop Info -->
                        <div class="col-12 col-md-6 border-end-md pe-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-network-wired text-primary me-1.5"></i> ASSIGNMENT & SHOP IDENTITY
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Assigned Distributor *</label>
                                <select name="distributor_id" class="form-select form-select-sm edit-dist-select" data-cust-id="{{ $c->id }}" required>
                                    @foreach($distributors as $d)
                                    <option value="{{ $d->id }}" {{ $c->distributor_id == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }} ({{ $d->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-user-tie text-teal me-1" style="color: #0f766e;"></i> Assign Order Booker *
                                </label>
                                <select name="booker_user_id" id="editBookerSelect{{ $c->id }}" class="form-select form-select-sm" data-selected="{{ $c->booker_user_id }}" required>
                                    <option value="">-- Select Order Booker --</option>
                                    @php
                                        $parentDist = $distributors->firstWhere('id', $c->distributor_id);
                                    @endphp
                                    @if($parentDist && $parentDist->bookers)
                                        @foreach($parentDist->bookers as $bkr)
                                        <option value="{{ $bkr->id }}" {{ $c->booker_user_id == $bkr->id ? 'selected' : '' }}>
                                            {{ $bkr->name }} ({{ $bkr->email }})
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Shop / Store Name *</label>
                                <input type="text" name="shop_name" class="form-control form-control-sm" value="{{ $c->shop_name }}" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Owner / Proprietor</label>
                                    <input type="text" name="owner_name" class="form-control form-control-sm" value="{{ $c->owner_name }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Mobile / Phone</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ $c->phone }}">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Location, Discount & Status -->
                        <div class="col-12 col-md-6 ps-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-sliders-h text-primary me-1.5"></i> ROUTE, DISCOUNT & STATUS
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Area / Market Route</label>
                                <input type="text" name="area" class="form-control form-control-sm" value="{{ $c->area }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Full Address</label>
                                <input type="text" name="address" class="form-control form-control-sm" value="{{ $c->address }}" placeholder="Optional address">
                            </div>

                            <!-- Customer Tier & Schemes (Plan 1) -->
                            <div class="mb-3 p-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                                <div class="mb-2">
                                    <label class="form-label fw-bold small text-dark mb-1">
                                        <i class="fas fa-layer-group text-primary me-1"></i> Customer Category / Pricing Tier <span class="text-danger">*</span>
                                    </label>
                                    <select name="tier_id" id="edit_{{ $c->id }}_tier_id" class="form-select form-select-sm customer-tier-select" data-prefix="edit_{{ $c->id }}" style="border: 1.5px solid #0f766e; font-weight: 600;">
                                        <option value="">-- Direct / Custom Assignment --</option>
                                        @foreach($tiers ?? [] as $t)
                                        <option value="{{ $t->id }}" {{ ($c->tier_id == $t->id) ? 'selected' : '' }}
                                                data-discount="{{ (float)$t->discount_percentage }}"
                                                data-to-buy="{{ (float)$t->trade_offer_buy_qty }}"
                                                data-to-free="{{ (float)$t->trade_offer_free_qty }}">
                                            {{ $t->name }} &bull; ({{ (float)$t->discount_percentage }}% Disc | {{ $t->scheme_label }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted" style="font-size: 0.71rem;">
                                        Tier change karne se Discount aur Scheme policy auto update hogi.
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-dark mb-1">Company Approved Discount (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0" max="100" name="approved_discount" id="edit_{{ $c->id }}_approved_discount" class="form-control fw-bold text-success" value="{{ (float)$c->effective_discount }}" placeholder="e.g. 5">
                                            <span class="input-group-text fw-bold">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Scheme: Buy Qty</label>
                                        <input type="number" step="0.01" min="0" name="trade_offer_buy_qty" id="edit_{{ $c->id }}_to_buy" class="form-control form-control-sm fw-bold" value="{{ (float)$c->effective_trade_offer_buy_qty }}" placeholder="e.g. 12">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Free Qty (T.O)</label>
                                        <input type="number" step="0.01" min="0" name="trade_offer_free_qty" id="edit_{{ $c->id }}_to_free" class="form-control form-control-sm fw-bold text-primary" value="{{ (float)$c->effective_trade_offer_free_qty }}" placeholder="e.g. 1">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark mb-1">Account Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="active" {{ $c->status === 'active' ? 'selected' : '' }}>Active (Can Place Orders)</option>
                                    <option value="inactive" {{ $c->status === 'inactive' ? 'selected' : '' }}>Inactive (Blocked)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3.5 d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Receive Payment Modal with Bank & Cash Management (Compact No-Scroll) -->
<div class="modal fade" id="receivePaymentModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.payment.store', $c->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header py-2 px-3 text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-white fs-6"></i>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" style="font-size: 0.95rem;">Receive Direct Customer Payment</h6>
                            <span class="text-white-50" style="font-size: 0.7rem;">Deposit into bank, clear customer due & update distributor ledger</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Compact Info Banner -->
                    <div class="p-2 px-3 mb-2 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $c->shop_name }}</span>
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $c->distributor->name ?? 'Distributor' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small me-1">Current Due:</span>
                            <span class="fw-bold text-danger" style="font-size: 0.95rem;">Rs. {{ number_format($c->current_balance, 2) }}</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <!-- Left Column -->
                        <div class="col-12 col-md-6">
                            <!-- Receiving Account -->
                            <div class="mb-2">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.76rem;">
                                    <i class="fas fa-university text-teal me-1" style="color: #0f766e;"></i> Receiving Bank / Cash Account *
                                </label>
                                <select name="account_id" class="form-select form-select-sm" required style="font-size: 0.8rem;">
                                    <option value="">-- Select Bank / Cash Account --</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $acc->title }} ({{ $acc->account_code }}) [Bal: Rs. {{ number_format($acc->opening_balance, 2) }}]
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount Received -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.76rem;">Amount Received (Rs.) *</label>
                                    @if($c->current_balance > 0)
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" style="font-size: 0.72rem; color: #0f766e;"
                                            onclick="document.getElementById('payAmount_{{ $c->id }}').value = '{{ $c->current_balance }}'">
                                        <i class="fas fa-bolt text-warning me-0.5"></i> Full Due: Rs. {{ number_format($c->current_balance, 2) }}
                                    </button>
                                    @endif
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text fw-bold">Rs.</span>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="payAmount_{{ $c->id }}" class="form-control fw-bold" placeholder="0.00" required>
                                </div>
                            </div>

                            <!-- Payment Date & Method -->
                            <div class="row g-1.5 mb-1">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Date *</label>
                                    <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required style="font-size: 0.8rem;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Method</label>
                                    <select name="payment_method" class="form-select form-select-sm" style="font-size: 0.8rem;">
                                        <option value="cash">Cash Counter</option>
                                        <option value="bank_transfer">Bank Transfer / Online</option>
                                        <option value="raast">Raast / QR</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="pay_order">Pay Order</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-12 col-md-6">
                            <input type="hidden" name="adjust_distributor" value="1">

                            <!-- Ref & Slip Image -->
                            <div class="row g-1.5 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Trx Ref #</label>
                                    <input type="text" name="transaction_reference" class="form-control form-control-sm" placeholder="e.g. Raast ID, IBFT Ref" style="font-size: 0.8rem;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Slip / Proof</label>
                                    <input type="file" name="slip_image" class="form-control form-control-sm" accept="image/*" style="font-size: 0.75rem;">
                                </div>
                            </div>

                            <!-- Notes / Description -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Notes / Description</label>
                                <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Optional notes, recovery details..." style="font-size: 0.8rem;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small" style="font-size: 0.72rem;">* Bank balance & customer ledger will be instantly credited</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.78rem;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white rounded-pill px-3.5 fw-semibold shadow-sm" style="background: #0f766e; font-size: 0.78rem;">
                            <i class="fas fa-check me-1"></i> Receive & Credit to Bank
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Delete Customer Modal -->
<div class="modal fade" id="deleteCustomerModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.destroy', $c->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-danger"
                         style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.1);">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Delete Retail Shop?</h6>
                    <p class="text-muted small mb-0">Are you sure you want to delete <strong>{{ $c->shop_name }}</strong>? This action cannot be undone if no transactions exist.</p>
                </div>
                <div class="modal-footer bg-light py-2 d-flex justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


@php
    $distributorsJson = $distributors->map(function($d) {
        return [
            'id' => $d->id,
            'name' => $d->name,
            'bookers' => $d->bookers ? $d->bookers->map(function($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'email' => $b->email,
                ];
            })->values() : []
        ];
    });
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function () {

    // Handle Customer Tier Selection change (auto fill discount & trade offer)
    $(document).on('change', '.customer-tier-select', function() {
        const prefix = $(this).data('prefix');
        const selected = $(this).find(':selected');
        if (!selected || !selected.val()) return;

        const disc = selected.data('discount');
        const toBuy = selected.data('to-buy');
        const toFree = selected.data('to-free');

        if (disc !== undefined && disc !== null) {
            $('#' + prefix + '_approved_discount').val(parseFloat(disc).toFixed(2));
        }
        if (toBuy !== undefined && toBuy !== null) {
            $('#' + prefix + '_to_buy').val(parseFloat(toBuy));
        }
        if (toFree !== undefined && toFree !== null) {
            $('#' + prefix + '_to_free').val(parseFloat(toFree));
        }
    });

        const distributorsData = @json($distributorsJson);

        function populateBookers(distId, selectElem, selectedBookerId = null) {
            if (!selectElem) return;
            selectElem.innerHTML = '<option value="">-- Select Order Booker --</option>';

            if (!distId) return;

            const dist = distributorsData.find(d => String(d.id) === String(distId));
            if (dist && dist.bookers && dist.bookers.length > 0) {
                dist.bookers.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.name + (b.email ? ' (' + b.email + ')' : '');
                    if (selectedBookerId && String(selectedBookerId) === String(b.id)) {
                        opt.selected = true;
                    }
                    selectElem.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "-- No Order Bookers Registered under this Distributor --";
                opt.disabled = true;
                selectElem.appendChild(opt);
            }
        }

        // Add Customer Modal
        const addDistSelect = document.getElementById('addCustomerDistributorSelect');
        const addBookerSelect = document.getElementById('addCustomerBookerSelect');
        if (addDistSelect && addBookerSelect) {
            addDistSelect.addEventListener('change', function () {
                populateBookers(this.value, addBookerSelect);
            });
            if (addDistSelect.value) {
                populateBookers(addDistSelect.value, addBookerSelect);
            }
        }

        // Edit Customer Modals
        document.querySelectorAll('.edit-dist-select').forEach(function (select) {
            const custId = select.getAttribute('data-cust-id');
            const bookerSelect = document.getElementById('editBookerSelect' + custId);
            if (!bookerSelect) return;

            select.addEventListener('change', function () {
                populateBookers(this.value, bookerSelect);
            });
        });
    });
</script>


<!-- ==========================================
     MANAGE CUSTOMER TIERS & SCHEMES MODAL
=========================================== -->
<div class="modal fade" id="manageTiersModal" tabindex="-1" aria-labelledby="manageTiersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-layer-group text-primary"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="manageTiersModalLabel" style="font-size: 0.96rem;">
                            Customer Pricing Tiers & Company Schemes (Plan 1)
                        </h6>
                        <span class="text-white-50 small" style="font-size: 0.72rem;">
                            Company assigns fixed discount % and trade offer ratio. Distributor cannot give more or less.
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Existing Tiers List -->
                <h6 class="fw-bold text-dark mb-2" style="font-size: 0.84rem;">
                    <i class="fas fa-list me-1 text-primary"></i> Active Company Tiers
                </h6>
                <div class="table-responsive bg-white rounded-3 border mb-4">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Tier Name</th>
                                <th>Fixed Discount %</th>
                                <th>Trade Offer (Scheme)</th>
                                <th>Description</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tiers ?? [] as $tier)
                            <tr>
                                <td class="fw-bold text-dark">
                                    <i class="fas fa-tag text-primary me-1"></i> {{ $tier->name }}
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border px-2 py-1">
                                        {{ (float)$tier->discount_percentage }}%
                                    </span>
                                </td>
                                <td>
                                    @if($tier->trade_offer_buy_qty > 0 && $tier->trade_offer_free_qty > 0)
                                    <span class="badge bg-info-subtle text-info-emphasis border px-2 py-1">
                                        Buy {{ (float)$tier->trade_offer_buy_qty }} Get {{ (float)$tier->trade_offer_free_qty }} Free
                                    </span>
                                    @else
                                    <span class="text-muted small">No Free Item</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $tier->description ?: 'N/A' }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5"
                                            data-bs-toggle="collapse" data-bs-target="#editTierCollapse{{ $tier->id }}">
                                        <i class="fas fa-pen me-1"></i> Edit Policy
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="editTierCollapse{{ $tier->id }}">
                                <td colspan="5" class="bg-light p-3 border-top">
                                    <form action="{{ route('admin.distributors.customer_tiers.update', $tier->id) }}" method="POST">
                                        @csrf
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Tier Name</label>
                                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $tier->name }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold mb-1">Discount %</label>
                                                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control form-control-sm" value="{{ (float)$tier->discount_percentage }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold mb-1">Buy Qty</label>
                                                <input type="number" step="0.01" min="0" name="trade_offer_buy_qty" class="form-control form-control-sm" value="{{ (float)$tier->trade_offer_buy_qty }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold mb-1">Free Qty</label>
                                                <input type="number" step="0.01" min="0" name="trade_offer_free_qty" class="form-control form-control-sm" value="{{ (float)$tier->trade_offer_free_qty }}">
                                            </div>
                                            <div class="col-md-3 d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">
                                                    <i class="fas fa-save me-1"></i> Update
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No pricing tiers defined.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Add New Tier Card -->
                <div class="card border rounded-3 p-3 bg-white">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.84rem;">
                        <i class="fas fa-plus-circle text-success me-1"></i> Add New Customer Pricing Tier
                    </h6>
                    <form action="{{ route('admin.distributors.customer_tiers.store') }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">Tier Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Mart & Bakers" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold mb-1">Discount % *</label>
                                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control form-control-sm" placeholder="e.g. 4.5" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold mb-1">T.O: Buy Qty</label>
                                <input type="number" step="0.01" min="0" name="trade_offer_buy_qty" class="form-control form-control-sm" placeholder="e.g. 12" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold mb-1">Free Qty (T.O)</label>
                                <input type="number" step="0.01" min="0" name="trade_offer_free_qty" class="form-control form-control-sm" placeholder="e.g. 1" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Optional notes">
                            </div>
                            <div class="col-12 text-end mt-2">
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                    <i class="fas fa-plus me-1"></i> Save New Pricing Tier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
