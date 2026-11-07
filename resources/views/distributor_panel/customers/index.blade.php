@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-white-50">&bull;</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Retail Network
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">My Retail Shops & Customers</h4>
            <p class="mb-0 text-white-50 small">Manage your shop accounts, customer ledgers, and credit limits</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold"
                    data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="fas fa-plus me-1"></i> + Add New Retailer
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-check-circle"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Key Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Retail Accounts</span>
                <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalCustomers }}</h3>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #f59e0b !important;">
                <span class="text-muted small fw-semibold text-uppercase">Market Credit Due from Retailers</span>
                <h3 class="fw-bold mb-0 text-danger mt-1">Rs. {{ number_format($totalReceivable, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-store me-2 text-teal" style="color: #0f766e;"></i> Registered Retail Stores
            </h6>
            <form method="GET" action="{{ route('distributor.customers.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-start-pill" placeholder="Search shop, phone, area...">
                    <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Shop Name & Owner</th>
                        <th>Phone / Contact</th>
                        <th>Area / Market</th>
                        <th class="text-end">Balance (Receivable)</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($customers as $cust)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark fs-6">{{ $cust->shop_name }}</div>
                            <span class="text-muted small">Prop: {{ $cust->owner_name ?: 'N/A' }}</span>
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                @if($cust->tier)
                                <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 0.7rem;">
                                    <i class="fas fa-layer-group me-1"></i> Tier: {{ $cust->tier->name }}
                                </span>
                                @endif
                                <span class="badge rounded-pill {{ (float)$cust->effective_discount > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }} px-2 py-0.5" style="font-size: 0.7rem;">
                                    <i class="fas fa-percentage me-1"></i> Discount Limit: {{ (float)$cust->effective_discount }}%
                                </span>
                                @if($cust->effective_trade_offer_buy_qty > 0 && $cust->effective_trade_offer_free_qty > 0)
                                <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-0.5" style="font-size: 0.7rem;">
                                    <i class="fas fa-gift me-1"></i> Trade Offer: {{ $cust->scheme_label }}
                                </span>
                                @else
                                <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5" style="font-size: 0.7rem;">
                                    <i class="fas fa-gift me-1"></i> Trade Offer: No Scheme
                                </span>
                                @endif
                                @if($cust->booker)
                                <span class="badge rounded-pill border px-2 py-0.5" style="background: rgba(15, 118, 110, 0.1); color: #0f766e; border-color: rgba(15, 118, 110, 0.25) !important; font-size: 0.7rem;">
                                    <i class="fas fa-user-tie me-1"></i> Booker: {{ $cust->booker->name }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-dark"><i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.75rem;"></i> {{ $cust->phone ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2.5 py-1">
                                <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $cust->area ?: 'Local Market' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $cust->current_balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 0.95rem;">
                                Rs. {{ number_format($cust->current_balance, 2) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('distributor.sales.create', ['customer_id' => $cust->id]) }}" class="btn btn-outline-primary rounded-pill px-2.5 py-1" title="Sell to Shop">
                                    <i class="fas fa-cash-register me-1"></i> Sell
                                </a>
                                <a href="{{ route('distributor.customers.ledger', $cust->id) }}" class="btn btn-outline-secondary rounded-pill px-2.5 py-1 ms-1" title="View Shop Ledger">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Ledger
                                </a>
                                <button type="button" class="btn btn-outline-dark rounded-pill px-2.5 py-1 ms-1" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $cust->id }}" title="Edit Shop Details & Discount Limit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-store-alt-slash fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Retail Stores Added Yet</p>
                            <span class="small">Click "+ Add New Retailer" above to register shops in your route or area.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('distributor.customers.store') }}" method="POST">
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
                        <!-- Left Column: Shop Identity & Booker -->
                        <div class="col-12 col-md-6 border-end-md pe-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-store text-teal me-1.5" style="color: #0f766e;"></i> SHOP IDENTITY & BOOKER
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-user-tie text-teal me-1" style="color: #0f766e;"></i> Assign Order Booker *
                                </label>
                                <select name="booker_user_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Order Booker --</option>
                                    @if(isset($bookers))
                                        @foreach($bookers as $bkr)
                                        <option value="{{ $bkr->id }}">{{ $bkr->name }} ({{ $bkr->email }})</option>
                                        @endforeach
                                    @endif
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
                                    <label class="form-label fw-semibold small text-dark mb-1">Mobile Number</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="0300-1234567">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Route, Discount & Opening Balance -->
                        <div class="col-12 col-md-6 ps-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-sliders-h text-teal me-1.5" style="color: #0f766e;"></i> ROUTE, DISCOUNT & CREDIT
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Area / Market Route</label>
                                <input type="text" name="area" class="form-control form-control-sm" placeholder="e.g. Main Market, Malir">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Full Shop Address</label>
                                <input type="text" name="address" class="form-control form-control-sm" placeholder="Optional complete shop address...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-lock text-muted me-1"></i> Company Approved Discount (%)
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control bg-light text-muted fw-bold" value="0.00%" readonly disabled>
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Company Head Office assigns and approves discount for this shop.</div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark mb-1">Initial Opening Balance (Previous Balance)</label>
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
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4 fw-semibold shadow-sm" style="background: #0f766e; border: 1px solid #14b8a6;">
                        <i class="fas fa-check me-1"></i> Save Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modals -->
@foreach($customers as $cust)
<div class="modal fade" id="editCustomerModal{{ $cust->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('distributor.customers.update', $cust->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2.5 px-3.5 text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit fs-5"></i>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Edit Retailer: {{ $cust->shop_name }}</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5 p-md-4">
                    <div class="row g-3 g-md-4">
                        <!-- Left Column: Shop Identity & Booker -->
                        <div class="col-12 col-md-6 border-end-md pe-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-store text-teal me-1.5" style="color: #0f766e;"></i> SHOP IDENTITY & BOOKER
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-user-tie text-teal me-1" style="color: #0f766e;"></i> Assign Order Booker *
                                </label>
                                <select name="booker_user_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Order Booker --</option>
                                    @if(isset($bookers))
                                        @foreach($bookers as $bkr)
                                        <option value="{{ $bkr->id }}" {{ $cust->booker_user_id == $bkr->id ? 'selected' : '' }}>
                                            {{ $bkr->name }} ({{ $bkr->email }})
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Shop / Store Name *</label>
                                <input type="text" name="shop_name" class="form-control form-control-sm" value="{{ $cust->shop_name }}" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Owner / Proprietor</label>
                                    <input type="text" name="owner_name" class="form-control form-control-sm" value="{{ $cust->owner_name }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Mobile Number</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ $cust->phone }}">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Route & Discount -->
                        <div class="col-12 col-md-6 ps-md-4">
                            <h6 class="fw-bold text-dark pb-1 border-bottom mb-3" style="font-size: 0.84rem; letter-spacing: 0.3px;">
                                <i class="fas fa-sliders-h text-teal me-1.5" style="color: #0f766e;"></i> ROUTE & DISCOUNT LIMIT
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Area / Market Route</label>
                                <input type="text" name="area" class="form-control form-control-sm" value="{{ $cust->area }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Full Shop Address</label>
                                <input type="text" name="address" class="form-control form-control-sm" value="{{ $cust->address }}" placeholder="Optional address">
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark mb-1">
                                    <i class="fas fa-lock text-warning me-1"></i> Company Approved Discount (%)
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ (float)($cust->approved_discount ?? 0) }}%" readonly disabled>
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Strictly locked: Only Company Head Office can modify the approved discount for this shop.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3.5 d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4 fw-semibold shadow-sm" style="background: #0f766e; border: 1px solid #14b8a6;">
                        <i class="fas fa-save me-1"></i> Update Shop Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
@media (min-width: 768px) {
    .border-end-md {
        border-right: 1px solid #e2e8f0 !important;
    }
}
</style>
@endsection
