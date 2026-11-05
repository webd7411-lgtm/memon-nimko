@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-white-50">&bull;</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Distributor Claims & Schemes
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Retail Shop Discount Claims</h4>
            <p class="mb-0 text-white-50 small">
                Claim company-approved discounts provided to retail shops. Approved amounts are credited directly to your Company Ledger.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.company_ledger.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                <i class="fas fa-file-invoice-dollar me-1"></i> View Company Ledger
            </a>
            <button type="button" class="btn btn-teal btn-sm rounded-pill px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                <i class="fas fa-plus-circle me-1"></i> Submit New Discount Claim
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Outstanding Due -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">My Ledger Due</span>
                        <h4 class="fw-bold mb-0 {{ ($distributor->current_balance ?? 0) > 0 ? 'text-danger' : 'text-success' }} mt-1">
                            Rs. {{ number_format($distributor->current_balance ?? 0, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Payable to company</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-danger">
                        <i class="fas fa-balance-scale fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Market Discount Given -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Shop Discounts Given</span>
                        <h4 class="fw-bold mb-0 text-warning-emphasis mt-1">
                            Rs. {{ number_format($totalDiscountGiven ?? 0, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Provided to retail shops</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-warning">
                        <i class="fas fa-tags fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Unclaimed Discounts (Ready to Claim) -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #0f766e !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Unclaimed (Ready to File)</span>
                        <h4 class="fw-bold mb-0 text-teal mt-1" style="color: #0f766e;">
                            Rs. {{ number_format($unclaimedDiscount ?? 0, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Eligible for date-range claim</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-teal" style="color: #0f766e;">
                        <i class="fas fa-hand-holding-usd fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Total Approved & Credited -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Approved Credits</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            Rs. {{ number_format($approvedAmount, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Credited to your company ledger</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-success">
                        <i class="fas fa-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.claims.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Verification</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved & Credited</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Claim Date From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Claim Date To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.claims.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset">
                        <i class="fas fa-rotate"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Claims Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white py-3 px-3 d-flex align-items-center justify-content-between border-bottom">
            <div class="fw-bold text-dark">
                <i class="fas fa-clipboard-list text-teal me-1" style="color: #0f766e;"></i> My Submitted Discount Claims
            </div>
            <span class="badge bg-light text-secondary border">Showing {{ $claims->count() }} of {{ $claims->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.74rem;">
                    <tr>
                        <th class="ps-3">Claim ID & Date</th>
                        <th>Discount Sales Period</th>
                        <th>Shop Bills</th>
                        <th class="text-end">Claimed Discount</th>
                        <th class="text-end">Approved Credit</th>
                        <th>Settlement Mode</th>
                        <th>Status</th>
                        <th>Proof Receipt</th>
                        <th class="text-center pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold font-monospace text-teal" style="color: #0f766e;">{{ $claim->claim_no }}</div>
                            <div class="text-muted small" style="font-size: 0.73rem;">
                                Filed: {{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}
                            </div>
                        </td>

                        <td>
                            @if($claim->from_date && $claim->to_date)
                                <div class="fw-semibold text-dark">
                                    <i class="far fa-calendar-alt text-teal me-1" style="color: #0f766e;"></i>
                                    {{ $claim->from_date->format('d M Y') }} &rarr; {{ $claim->to_date->format('d M Y') }}
                                </div>
                            @else
                                <span class="text-muted small">Standard Claim</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-store me-1 text-secondary"></i> {{ $claim->sales->count() > 0 ? $claim->sales->count() . ' Bills' : ($claim->items->count() > 0 ? $claim->items->count() . ' Items' : '1 Entry') }}
                            </span>
                        </td>

                        <td class="text-end">
                            <div class="fw-bold text-dark">Rs. {{ number_format($claim->total_amount, 2) }}</div>
                        </td>

                        <td class="text-end">
                            @if($claim->status === 'approved')
                                <div class="fw-bold text-success">Rs. {{ number_format($claim->approved_amount, 2) }}</div>
                            @elseif($claim->status === 'rejected')
                                <span class="text-danger small">Rejected</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                <i class="fas fa-file-invoice-dollar me-1"></i> Credit Note
                            </span>
                        </td>

                        <td>
                            <span class="badge {{ $claim->status_badge_class }} px-2 py-1">
                                {{ strtoupper($claim->status) }}
                            </span>
                        </td>

                        <td>
                            @if($claim->attachment)
                                <a href="{{ asset($claim->attachment) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0.5 px-2" style="font-size: 0.72rem;">
                                    <i class="fas fa-paperclip me-1"></i> View
                                </a>
                            @else
                                <span class="text-muted small" style="font-size: 0.72rem;">None</span>
                            @endif
                        </td>

                        <td class="text-center pe-3">
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill py-0.5 px-2.5" style="font-size: 0.75rem;"
                                    data-bs-toggle="modal" data-bs-target="#distClaimViewModal{{ $claim->id }}">
                                <i class="fas fa-eye me-1"></i> Details
                            </button>
                        </td>
                    </tr>

                    <!-- Details Modal -->
                    <div class="modal fade" id="distClaimViewModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom py-3" style="background: linear-gradient(135deg, #090d16, #0f766e); color: #fff;">
                                    <div>
                                        <h5 class="modal-title fw-bold text-white mb-0">Claim #{{ $claim->claim_no }}</h5>
                                        <small class="text-white-50">Retail Shop Discount Reimbursement</small>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-3 p-3 bg-light rounded-3 border">
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Claim Date</div>
                                            <div class="fw-bold">{{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}</div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Discount Period</div>
                                            <div class="fw-bold text-dark">
                                                {{ $claim->from_date ? $claim->from_date->format('d M') : '-' }} &rarr; {{ $claim->to_date ? $claim->to_date->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Claimed Amount</div>
                                            <div class="fw-bold text-dark">Rs. {{ number_format($claim->total_amount, 2) }}</div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Status</div>
                                            <span class="badge {{ $claim->status_badge_class }}">{{ strtoupper($claim->status) }}</span>
                                        </div>
                                    </div>

                                    @if($claim->status === 'approved')
                                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                                        <i class="fas fa-check-circle fs-5"></i>
                                        <div>
                                            Approved for <strong>Rs. {{ number_format($claim->approved_amount, 2) }}</strong> &mdash; Credited to Company Ledger via Credit Note on {{ $claim->approved_at ? $claim->approved_at->format('d M Y') : '' }}.
                                        </div>
                                    </div>
                                    @endif

                                    @if($claim->description)
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Submitted Explanation / Notes:</label>
                                        <div class="p-2.5 rounded-3 bg-white border small text-muted">
                                            {{ $claim->description }}
                                        </div>
                                    </div>
                                    @endif

                                    @if($claim->sales && $claim->sales->count() > 0)
                                    <h6 class="fw-bold small text-uppercase text-muted mb-2">
                                        <i class="fas fa-store me-1"></i> Retail Invoices Included in this Claim ({{ $claim->sales->count() }})
                                    </h6>
                                    <div class="table-responsive border rounded-3 mb-3">
                                        <table class="table table-sm align-middle mb-0" style="font-size: 0.8rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th>Shop Name & Owner</th>
                                                    <th>Bill Date</th>
                                                    <th class="text-end">Gross Bill</th>
                                                    <th class="text-end text-danger">Discount Given</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($claim->sales as $s)
                                                <tr>
                                                    <td class="fw-bold font-monospace text-teal">{{ $s->invoice_no }}</td>
                                                    <td>
                                                        <strong>{{ $s->customer->shop_name ?? 'Shop' }}</strong>
                                                        <small class="text-muted d-block">{{ $s->customer->owner_name ?? '' }} {{ $s->customer->phone ? '• ' . $s->customer->phone : '' }}</small>
                                                    </td>
                                                    <td>{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-' }}</td>
                                                    <td class="text-end">Rs. {{ number_format($s->subtotal, 2) }}</td>
                                                    <td class="text-end fw-bold text-danger">Rs. {{ number_format($s->discount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @elseif($claim->items && $claim->items->count() > 0)
                                    <h6 class="fw-bold small text-uppercase text-muted mb-2">Item Breakdown</h6>
                                    <div class="table-responsive border rounded-3 mb-3">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Claim Rate</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($claim->items as $it)
                                                <tr>
                                                    <td class="fw-semibold">{{ $it->product->item_name ?? 'Custom Item' }}</td>
                                                    <td class="text-center">{{ (float)$it->qty }}</td>
                                                    <td class="text-end">Rs. {{ number_format($it->unit_rate, 2) }}</td>
                                                    <td class="text-end fw-bold">Rs. {{ number_format($it->subtotal, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif

                                    @if($claim->attachment)
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Attached Document:</label>
                                        <div>
                                            <a href="{{ asset($claim->attachment) }}" target="_blank" class="btn btn-sm btn-outline-teal rounded-pill">
                                                <i class="fas fa-file-download me-1"></i> Open Attachment
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    @if($claim->admin_notes)
                                    <div class="alert alert-secondary mb-0 p-2.5 rounded-3 small">
                                        <strong>Admin Remarks:</strong> {{ $claim->admin_notes }}
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer bg-light border-top py-2 px-4">
                                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="fas fa-receipt fs-1 opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-secondary">No Claims Found</h6>
                            <p class="text-muted small mb-3">You have not submitted any discount claims matching these filters.</p>
                            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                                + Submit New Discount Claim
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
        <div class="card-footer bg-white border-top py-2 px-3">
            {{ $claims->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: SUBMIT NEW RETAIL DISCOUNT CLAIM                        -->
<!-- ============================================================== -->
<div class="modal fade" id="newClaimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('distributor.claims.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <!-- Hidden inputs -->
            <input type="hidden" name="claim_type" value="trade_discount">
            <input type="hidden" name="settlement_type" value="credit_note">

            <div class="modal-header border-bottom py-3" style="background: linear-gradient(135deg, #090d16, #0f766e); color: #fff;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fas fa-tags me-1"></i> Submit Retail Discount Claim to Head Office
                    </h5>
                    <small class="text-white-50">Select sales date range to auto-calculate all shop discounts given</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Date Range Selection Card -->
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <label class="form-label fw-bold text-dark small mb-2">
                        <i class="far fa-calendar-alt text-teal me-1" style="color: #0f766e;"></i> Select Discount Sales Period:
                    </label>
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-5">
                            <label class="form-label text-muted small mb-1">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" id="claimFromDate" class="form-control form-control-sm"
                                   value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="form-label text-muted small mb-1">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" id="claimToDate" class="form-control form-control-sm"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-2 mt-md-4">
                            <button type="button" class="btn btn-sm btn-dark w-100" id="btnRefreshCalculation" title="Re-calculate discounts">
                                <i class="fas fa-sync-alt me-1"></i> Calculate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Auto-Calculation Result Banner -->
                <div class="card border-0 rounded-3 mb-3 text-center p-3" id="calcResultCard" style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1); border: 1px solid #14b8a6 !important;">
                    <div id="calcLoading" style="display: none;" class="py-2">
                        <div class="spinner-border spinner-border-sm text-teal" role="status"></div>
                        <span class="ms-2 small text-muted">Calculating unclaimed shop discounts in date range...</span>
                    </div>

                    <div id="calcContent">
                        <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                            Total Auto-Calculated Shop Discount
                        </span>
                        <h2 class="fw-bolder mb-0 mt-1" style="color: #0f766e;" id="calcTotalDisplay">Rs. 0.00</h2>
                        <div class="small fw-semibold mt-1" id="calcInvoicesCount" style="color: #115e59;">
                            0 retail invoices eligible for claim
                        </div>
                    </div>
                </div>

                <!-- Scrollable Table of Eligible Invoices -->
                <div id="previewInvoicesWrap" style="display: none;" class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">
                            <i class="fas fa-list-check me-1 text-teal"></i> Eligible Retail Invoices Breakdown:
                        </label>
                        <span class="badge bg-light text-muted border" id="previewInvoicesBadge">0 Bills</span>
                    </div>
                    <div class="table-responsive border rounded-3" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Shop Name</th>
                                    <th>Date</th>
                                    <th class="text-end">Gross Bill</th>
                                    <th class="text-end text-danger fw-bold">Discount Given</th>
                                </tr>
                            </thead>
                            <tbody id="previewInvoicesBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Settlement Mode Fixed Badge -->
                <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border mb-3">
                    <div>
                        <span class="small fw-bold text-dark d-block">Settlement Mode:</span>
                        <span class="small text-muted" style="font-size: 0.73rem;">Once approved, this amount is directly credited to your Company Ledger.</span>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Credit Note (Ledger Credit)
                    </span>
                </div>

                <!-- Attachment & Description -->
                <div class="row g-2 mb-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Optional Proof / Summary Attachment</label>
                        <input type="file" name="attachment" class="form-control form-control-sm" accept="image/*,.pdf">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Remarks / Note (Optional)</label>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="e.g. November retail scheme claim">
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-teal rounded-pill px-4" id="btnSubmitClaim" disabled>
                    <i class="fas fa-paper-plane me-1"></i> Submit Claim to Head Office
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fromDateInp = document.getElementById('claimFromDate');
        const toDateInp = document.getElementById('claimToDate');
        const btnRefresh = document.getElementById('btnRefreshCalculation');
        const btnSubmit = document.getElementById('btnSubmitClaim');
        const calcLoading = document.getElementById('calcLoading');
        const calcContent = document.getElementById('calcContent');
        const calcTotalDisplay = document.getElementById('calcTotalDisplay');
        const calcInvoicesCount = document.getElementById('calcInvoicesCount');
        const previewWrap = document.getElementById('previewInvoicesWrap');
        const previewBody = document.getElementById('previewInvoicesBody');
        const previewBadge = document.getElementById('previewInvoicesBadge');

        const calculateUrl = "{{ route('distributor.claims.calculate_discount') }}";

        function fetchDiscountCalculation() {
            const fromDate = fromDateInp.value;
            const toDate = toDateInp.value;

            if (!fromDate || !toDate) {
                return;
            }

            calcLoading.style.display = 'block';
            calcContent.style.opacity = '0.3';
            btnSubmit.disabled = true;

            fetch(`${calculateUrl}?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                calcLoading.style.display = 'none';
                calcContent.style.opacity = '1';

                if (data.status) {
                    const totalDisc = parseFloat(data.total_discount || 0);
                    const count = parseInt(data.count || 0);

                    calcTotalDisplay.textContent = 'Rs. ' + totalDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    calcInvoicesCount.innerHTML = `<strong>${count}</strong> retail invoice(s) eligible for claim`;

                    if (totalDisc > 0 && count > 0) {
                        btnSubmit.disabled = false;
                        previewWrap.style.display = 'block';
                        previewBadge.textContent = `${count} Bills`;

                        let rowsHtml = '';
                        (data.sales || []).forEach(s => {
                            rowsHtml += `
                                <tr>
                                    <td class="fw-bold font-monospace text-teal">${s.invoice_no}</td>
                                    <td>
                                        <strong>${s.shop_name}</strong>
                                        ${s.owner_name ? `<small class="text-muted d-block">${s.owner_name}</small>` : ''}
                                    </td>
                                    <td>${s.sale_date}</td>
                                    <td class="text-end">Rs. ${parseFloat(s.subtotal).toFixed(2)}</td>
                                    <td class="text-end fw-bold text-danger">Rs. ${parseFloat(s.discount).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                        previewBody.innerHTML = rowsHtml;
                    } else {
                        btnSubmit.disabled = true;
                        previewWrap.style.display = 'none';
                        previewBody.innerHTML = '';
                        calcInvoicesCount.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> No unclaimed retail discounts found in this date range.</span>`;
                    }
                }
            })
            .catch(err => {
                calcLoading.style.display = 'none';
                calcContent.style.opacity = '1';
                calcInvoicesCount.innerHTML = `<span class="text-danger">Error calculating discount. Please try again.</span>`;
            });
        }

        if (fromDateInp && toDateInp) {
            fromDateInp.addEventListener('change', fetchDiscountCalculation);
            toDateInp.addEventListener('change', fetchDiscountCalculation);
        }

        if (btnRefresh) {
            btnRefresh.addEventListener('click', fetchDiscountCalculation);
        }

        // Trigger on initial modal open
        const newClaimModalEl = document.getElementById('newClaimModal');
        if (newClaimModalEl) {
            newClaimModalEl.addEventListener('shown.bs.modal', function () {
                fetchDiscountCalculation();
            });
        }
    });
</script>
@endsection
