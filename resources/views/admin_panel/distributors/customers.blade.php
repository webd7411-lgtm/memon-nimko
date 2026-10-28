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
    .dist-btn-action {
        font-size: 0.74rem !important;
        padding: 4px 11px !important;
        border-radius: 50rem !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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
                <div class="col-12 col-md-4">
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
                        <option value="">-- All Distributors / Bookers --</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Balance Status Filter -->
                <div class="col-12 col-sm-6 col-md-3">
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
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->distributor)
                            <a href="{{ route('admin.distributors.customers', ['distributor_id' => $c->distributor->id]) }}" class="text-decoration-none">
                                <div class="fw-bold text-dark">{{ $c->distributor->name }}</div>
                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ $c->distributor->code }} • {{ ucfirst($c->distributor->type ?? 'distributor') }}
                                </span>
                            </a>
                            @else
                            <span class="text-muted small">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">
                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.72rem;"></i>
                                {{ $c->phone ?: '—' }}
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
                <div class="text-muted"><i class="fas fa-phone-alt me-1"></i> Phone: <span class="text-dark">{{ $c->phone ?: '—' }}</span></div>
                <div class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> Area: <span class="text-dark">{{ $c->area ?: 'General' }}</span></div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem;">
                        <i class="fas fa-store me-2"></i> Register New Retail Customer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Assign to Distributor / Booker *</label>
                        <select name="distributor_id" class="form-select form-select-sm" required>
                            <option value="">-- Select Distributor --</option>
                            @foreach($distributors as $d)
                            <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->code }} - {{ ucfirst($d->type ?? 'distributor') }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Shop / Store Name *</label>
                        <input type="text" name="shop_name" class="form-control form-control-sm" placeholder="e.g. Madina Super Store" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Owner / Proprietor</label>
                            <input type="text" name="owner_name" class="form-control form-control-sm" placeholder="Owner Name">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Mobile / Phone</label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="0300-1234567">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Area / Market Route</label>
                        <input type="text" name="area" class="form-control form-control-sm" placeholder="e.g. Malir Halt Market, Korangi #4">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Full Shop Address</label>
                        <textarea name="address" rows="2" class="form-control form-control-sm" placeholder="Optional physical address..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark">Initial Opening Balance (Previous Credit / Udhaar)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" value="0.00">
                        </div>
                        <span class="text-muted" style="font-size: 0.72rem;">If this retailer has previous debt before entering the system, specify it here.</span>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4" style="background: #0f766e;">
                        <i class="fas fa-check me-1"></i> Save Shop Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- LOOP MODALS (Edit, Payment, Delete)            -->
<!-- ============================================== -->
@foreach($customers as $c)
<!-- 1. Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.update', $c->id) }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <h5 class="modal-title fw-bold" style="font-size: 1.05rem;">
                        <i class="fas fa-edit me-2"></i> Edit Retailer: {{ $c->shop_name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Assigned Distributor *</label>
                        <select name="distributor_id" class="form-select form-select-sm" required>
                            @foreach($distributors as $d)
                            <option value="{{ $d->id }}" {{ $c->distributor_id == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Shop / Store Name *</label>
                        <input type="text" name="shop_name" class="form-control form-control-sm" value="{{ $c->shop_name }}" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Owner / Proprietor</label>
                            <input type="text" name="owner_name" class="form-control form-control-sm" value="{{ $c->owner_name }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Mobile / Phone</label>
                            <input type="text" name="phone" class="form-control form-control-sm" value="{{ $c->phone }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Area / Market Route</label>
                        <input type="text" name="area" class="form-control form-control-sm" value="{{ $c->area }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Full Address</label>
                        <textarea name="address" rows="2" class="form-control form-control-sm">{{ $c->address }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="active" {{ $c->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $c->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
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

@endsection
