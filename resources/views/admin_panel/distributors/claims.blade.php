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
    .btn-teal {
        background: linear-gradient(135deg, #0f766e, #0d9488);
        border: 1px solid #14b8a6;
        color: #ffffff;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-teal:hover {
        background: linear-gradient(135deg, #0d9488, #115e59);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.35);
    }
    .btn-outline-teal {
        border: 1px solid #0f766e;
        color: #0f766e;
        font-weight: 600;
    }
    .btn-outline-teal:hover {
        background: #0f766e;
        color: #ffffff;
    }
    .table-claims th {
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 14px;
    }
    .table-claims td {
        font-size: 0.84rem;
        padding: 11px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Flash Messages -->
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
    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-info-circle fs-5"></i>
        <div>{{ session('info') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header & Action Row -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-primary"></i> Distributor Claims & Reimbursements
            </h4>
            <p class="text-muted small mb-0">
                Manage trade discounts, secondary market schemes, damage stock, and freight expense reimbursements.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('Create Distributor Claim')
            <button type="button" class="btn btn-teal rounded-pill px-3 py-1.5 shadow-sm small" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                <i class="fas fa-plus-circle me-1"></i> + File New Claim
            </button>
            @endcan
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Pending Verification -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dist-kpi-card border-start border-4 border-warning">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Audit</div>
                    <h3 class="fw-bold text-dark my-1">{{ $pendingClaimsCount }}</h3>
                    <div class="text-warning-emphasis fw-bold" style="font-size: 0.75rem;">
                        Rs. {{ number_format($pendingAmount, 2) }}
                    </div>
                </div>
                <div class="rounded-circle p-3 bg-warning-subtle text-warning">
                    <i class="fas fa-clock fs-4"></i>
                </div>
            </div>
        </div>

        <!-- 2. Approved & Settled -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dist-kpi-card border-start border-4 border-success">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Approved Expense</div>
                    <h3 class="fw-bold text-success my-1">Rs. {{ number_format($approvedAmount, 0) }}</h3>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Credited to distributors
                    </div>
                </div>
                <div class="rounded-circle p-3 bg-success-subtle text-success">
                    <i class="fas fa-check-double fs-4"></i>
                </div>
            </div>
        </div>

        <!-- 3. Total Claims -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dist-kpi-card border-start border-4 border-primary">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Claims Filed</div>
                    <h3 class="fw-bold text-primary my-1">{{ $totalClaimsCount }}</h3>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        All categories included
                    </div>
                </div>
                <div class="rounded-circle p-3 bg-primary-subtle text-primary">
                    <i class="fas fa-file-invoice-dollar fs-4"></i>
                </div>
            </div>
        </div>

        <!-- 4. Accounting Explanation Pill -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dist-kpi-card border-start border-4 border-info bg-light">
                <div>
                    <div class="text-info fw-bold small text-uppercase" style="font-size: 0.72rem;">
                        <i class="fas fa-lightbulb me-1"></i> Auto Accounting
                    </div>
                    <div class="text-dark small mt-1" style="font-size: 0.76rem; line-height: 1.35;">
                        Approval auto-credits the <strong>Distributor Ledger</strong> (reducing their liability) & debits <strong>Company Expense</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.claims') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Distributor</label>
                    <select name="distributor_id" class="form-select form-select-sm">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Claim Type</label>
                    <select name="claim_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="trade_discount" {{ request('claim_type') === 'trade_discount' ? 'selected' : '' }}>Trade Schemes / Shop Discounts</option>
                        <option value="damage_stock" {{ request('claim_type') === 'damage_stock' ? 'selected' : '' }}>Damaged / Expired Goods</option>
                        <option value="freight" {{ request('claim_type') === 'freight' ? 'selected' : '' }}>Freight / Bilty Reimbursement</option>
                        <option value="target_incentive" {{ request('claim_type') === 'target_incentive' ? 'selected' : '' }}>Sales Target Incentive</option>
                        <option value="other" {{ request('claim_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100 rounded-3">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.distributors.claims') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filters">
                        <i class="fas fa-rotate"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Claims Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-3 d-flex align-items-center justify-content-between border-bottom">
            <div class="fw-bold text-dark">
                <i class="fas fa-list-check text-teal me-1" style="color: #0f766e;"></i> Claims Register
            </div>
            <span class="badge bg-light text-secondary border">Showing {{ $claims->count() }} of {{ $claims->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-claims mb-0">
                <thead>
                    <tr>
                        <th>Claim Details</th>
                        <th>Distributor</th>
                        <th>Claim Type</th>
                        <th class="text-end">Claimed Amount</th>
                        <th class="text-end">Approved Amount</th>
                        <th>Settlement</th>
                        <th>Status</th>
                        <th>Receipt / Proof</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $claim->claim_no }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                <i class="far fa-calendar-alt me-1"></i> {{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}
                            </div>
                            @if($claim->items->count() > 0)
                            <span class="badge bg-light text-dark border mt-1" style="font-size: 0.65rem;">
                                {{ $claim->items->count() }} item(s) listed
                            </span>
                            @endif
                        </td>

                        <td>
                            <div class="fw-bold text-dark">{{ $claim->distributor->name ?? 'N/A' }}</div>
                            <div class="text-muted small" style="font-size: 0.73rem;">
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $claim->distributor->code ?? '' }}</span>
                                {{ $claim->distributor->city ?? '' }}
                            </div>
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $claim->type_badge_class }} px-2.5 py-1">
                                {{ $claim->type_label }}
                            </span>
                        </td>

                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($claim->total_amount, 2) }}
                        </td>

                        <td class="text-end">
                            @if($claim->status === 'approved')
                                <span class="fw-bold text-success">Rs. {{ number_format($claim->approved_amount, 2) }}</span>
                                @if($claim->expenseAccount)
                                <div class="text-muted" style="font-size: 0.68rem;" title="Account Head">
                                    {{ Str::limit($claim->expenseAccount->title, 20) }}
                                </div>
                                @endif
                            @elseif($claim->status === 'rejected')
                                <span class="text-muted text-decoration-line-through">Rs. 0.00</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis">Pending Audit</span>
                            @endif
                        </td>

                        <td>
                            @if($claim->settlement_type === 'credit_note')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" title="Adjusted against Company Ledger balance">
                                    <i class="fas fa-file-invoice me-1"></i> Credit Note
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Free Replacement items dispatched">
                                    <i class="fas fa-boxes-packing me-1"></i> Stock Replenish
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $claim->status_badge_class }} px-2.5 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                {{ $claim->status }}
                            </span>
                            @if($claim->approved_at)
                            <div class="text-muted mt-0.5" style="font-size: 0.68rem;">
                                {{ $claim->approved_at->format('d/m/y H:i') }}
                            </div>
                            @endif
                        </td>

                        <td>
                            @if($claim->attachment)
                                <a href="{{ asset($claim->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="fas fa-paperclip me-1"></i> View Proof
                                </a>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- View Details Modal Trigger -->
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewClaimModal{{ $claim->id }}" title="View Claim Details & Breakdown">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if($claim->status === 'pending')
                                    @can('Approve Distributor Claim')
                                    <!-- Approve Trigger -->
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveClaimModal{{ $claim->id }}" title="Approve & Post to Ledger">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endcan

                                    @can('Reject Distributor Claim')
                                    <!-- Reject Trigger -->
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectClaimModal{{ $claim->id }}" title="Reject Claim">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endcan

                                    @can('Delete Distributor Claim')
                                    <!-- Delete Trigger -->
                                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#deleteClaimModal{{ $claim->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="fas fa-receipt fs-1 opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-secondary">No Claims Found</h6>
                            <p class="text-muted small mb-3">No claims match your selected filters.</p>
                            @can('Create Distributor Claim')
                            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                                + File First Claim
                            </button>
                            @endcan
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
<!-- CLAIM MODALS (View, Approve, Reject, Delete)                    -->
<!-- ============================================================== -->
@foreach($claims as $claim)
    <!-- ========================================== -->
    <!-- MODAL 1: VIEW CLAIM DETAILS & BREAKDOWN -->
    <!-- ========================================== -->
    <div class="modal fade" id="viewClaimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Claim Details &bull; {{ $claim->claim_no }}
                        </h5>
                        <span class="badge {{ $claim->type_badge_class }} mt-1">{{ $claim->type_label }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3 p-3 bg-light rounded-3 border">
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Distributor</div>
                            <div class="fw-bold">{{ $claim->distributor->name ?? '-' }}</div>
                            <div class="small text-muted">{{ $claim->distributor->code ?? '' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Claim Date</div>
                            <div class="fw-bold">{{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Total Claimed</div>
                            <div class="fw-bold text-dark fs-6">Rs. {{ number_format($claim->total_amount, 2) }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Status</div>
                            <span class="badge {{ $claim->status_badge_class }}">{{ strtoupper($claim->status) }}</span>
                        </div>
                    </div>

                    @if($claim->description)
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Distributor's Description / Note:</label>
                        <div class="p-2.5 rounded-3 bg-white border small text-muted">
                            {{ $claim->description }}
                        </div>
                    </div>
                    @endif

                    <!-- Retail Shop Invoices Included in this Claim -->
                    @if($claim->sales && $claim->sales->count() > 0)
                    <h6 class="fw-bold small text-uppercase text-muted mb-2">
                        <i class="fas fa-store me-1 text-teal"></i> Retail Shop Invoices Breakdown ({{ $claim->sales->count() }} Bills)
                        @if($claim->from_date && $claim->to_date)
                            &bull; Period: {{ $claim->from_date->format('d M Y') }} to {{ $claim->to_date->format('d M Y') }}
                        @endif
                    </h6>
                    <div class="table-responsive border rounded-3 mb-3" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Shop Name & Owner</th>
                                    <th>Bill Date</th>
                                    <th class="text-end">Gross Bill</th>
                                    <th class="text-end text-danger fw-bold">Approved Discount</th>
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
                    @endif

                    <!-- Itemized Table if available (Legacy Claims) -->
                    @if($claim->items->count() > 0)
                    <h6 class="fw-bold small text-uppercase text-muted mb-2">Itemized Breakdown</h6>
                    <div class="table-responsive border rounded-3 mb-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product / Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Claim Rate</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Reason / Scheme Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($claim->items as $it)
                                <tr>
                                    <td class="fw-semibold">{{ $it->product->item_name ?? 'Custom Item' }}</td>
                                    <td class="text-center">{{ number_format($it->qty, 0) }}</td>
                                    <td class="text-end">Rs. {{ number_format($it->unit_rate, 2) }}</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($it->subtotal, 2) }}</td>
                                    <td class="small text-muted">{{ $it->reason ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($claim->attachment)
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Attached Document / Photo:</label>
                        <div>
                            <a href="{{ asset($claim->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Open Attachment in New Tab
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
                <div class="modal-footer bg-light border-top py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: APPROVE & SETTLE CLAIM (ACCOUNTING) -->
    <!-- ========================================== -->
    @if($claim->status === 'pending')
    <div class="modal fade" id="approveClaimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.distributors.claims.approve', $claim->id) }}" class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                @csrf
                <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #0f766e, #0d9488);">
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-check-circle me-2"></i> Approve & Settle Claim
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1.5">
                            <span class="text-muted small">Claim No:</span>
                            <span class="fw-bold font-monospace">{{ $claim->claim_no }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1.5">
                            <span class="text-muted small">Distributor:</span>
                            <span class="fw-bold text-dark">{{ $claim->distributor->name ?? '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Claimed Amount:</span>
                            <span class="fw-bold text-primary fs-6">Rs. {{ number_format($claim->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Approved Amount Field -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Approved Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Rs.</span>
                            <input type="number" step="0.01" name="approved_amount" class="form-control fw-bold fs-6 text-success" value="{{ $claim->total_amount }}" required>
                        </div>
                        <div class="form-text small">You can adjust this amount if audit found discrepancy.</div>
                    </div>

                    <!-- Settlement Method -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Settlement Mode <span class="text-danger">*</span></label>
                        <input type="hidden" name="settlement_type" value="credit_note">
                        <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success fs-5"></i>
                            <div>
                                <strong class="small d-block text-dark">Credit Note (Company Ledger Credit)</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Minus from Distributor Ledger debt & Book under Company Expense Voucher</small>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Account Dropdown -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Book Under Company Expense Account</label>
                        <select name="expense_account_id" class="form-select">
                            <option value="">-- Auto Match Default Head --</option>
                            @foreach($expenseAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ (str_contains(strtolower($acc->title), 'trade') || str_contains(strtolower($acc->title), 'claim')) ? 'selected' : '' }}>
                                {{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Creates an automatic Expense entry in Profit & Loss.</div>
                    </div>

                    <!-- Admin Remarks -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Approval Remarks / Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="e.g. Verified from shop bills and approved for credit."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 text-white fw-semibold" style="background: linear-gradient(135deg, #0f766e, #0d9488); border: none;">
                        <i class="fas fa-check me-1"></i> Confirm & Post to Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: REJECT CLAIM -->
    <!-- ========================================== -->
    <div class="modal fade" id="rejectClaimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.distributors.claims.reject', $claim->id) }}" class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                @csrf
                <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-times-circle me-1"></i> Reject Claim
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark small mb-3">
                        Are you sure you want to reject Claim <strong>#{{ $claim->claim_no }}</strong> of <strong>Rs. {{ number_format($claim->total_amount, 2) }}</strong> from <strong>{{ $claim->distributor->name ?? '' }}</strong>?
                    </p>
                    <div>
                        <label class="form-label small fw-bold text-dark">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Explain why this claim was rejected (e.g. invalid bill proof, expired policy)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                        <i class="fas fa-times me-1"></i> Reject Claim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 4: DELETE CLAIM -->
    <!-- ========================================== -->
    <div class="modal fade" id="deleteClaimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.distributors.claims.destroy', $claim->id) }}" class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                @csrf
                @method('DELETE')
                <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #090d16, #1e293b);">
                    <h5 class="modal-title fw-bold mb-0">Delete Claim Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-trash-alt text-danger fs-1 mb-3"></i>
                    <h5>Delete Claim #{{ $claim->claim_no }}?</h5>
                    <p class="text-muted small mb-0">This pending claim will be permanently removed.</p>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Delete</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

<!-- ============================================================== -->
<!-- MODAL: FILE NEW CLAIM (ADMIN DIRECT ENTRY) -->
<!-- ============================================================== -->
<div class="modal fade" id="createClaimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('admin.distributors.claims.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="claim_type" value="trade_discount">
            <input type="hidden" name="settlement_type" value="credit_note">

            <div class="modal-header border-bottom py-3" style="background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> File Retail Discount Claim for Distributor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Distributor <span class="text-danger">*</span></label>
                        <select name="distributor_id" id="adminDistId" class="form-select" required>
                            <option value="">-- Select Distributor --</option>
                            @foreach($distributors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }}) &bull; Due: Rs. {{ number_format($d->current_balance, 0) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Claim Date <span class="text-danger">*</span></label>
                        <input type="date" name="claim_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Sales Period: From Date <span class="text-danger">*</span></label>
                        <input type="date" name="from_date" id="adminFromDate" class="form-control" value="{{ date('Y-m-01') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Sales Period: To Date <span class="text-danger">*</span></label>
                        <input type="date" name="to_date" id="adminToDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Live Auto-Calculation Box -->
                <div class="p-3 rounded-3 text-center mb-3 border" style="background: #f0fdfa; border-color: #14b8a6 !important;">
                    <span class="text-muted small fw-bold text-uppercase">Auto-Calculated Unclaimed Shop Discount</span>
                    <h3 class="fw-bolder mb-0 text-teal mt-1" id="adminCalcDisplay" style="color: #0f766e;">Rs. 0.00</h3>
                    <div class="small fw-semibold mt-1" id="adminCalcNotice" style="color: #115e59;">Select distributor and dates to fetch unclaimed discounts</div>
                    <input type="hidden" name="total_amount" id="adminTotalClaimAmount" value="0.00">
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Description & Reason</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g. November retail shop scheme discount reimbursement">
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-teal rounded-pill px-4" id="adminBtnSubmitClaim" disabled>
                    <i class="fas fa-check-circle me-1"></i> Record & Submit Claim
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('addClaimRowBtn');
        const body = document.getElementById('claimItemsBody');
        const totalInput = document.getElementById('adminTotalClaimAmount');

        const products = @json($products);

        let rowIndex = 0;

        function recalcTotal() {
            let sum = 0;
            const subtotals = document.querySelectorAll('.claim-subtotal');
            subtotals.forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                sum += val;
            });
            if (sum > 0) {
                totalInput.value = sum.toFixed(2);
            }
        }

        if (addBtn && body) {
            addBtn.addEventListener('click', function () {
                let optionsHtml = '<option value="">-- Select Product --</option>';
                products.forEach(p => {
                    optionsHtml += `<option value="${p.id}">${p.item_name} (Rs. ${p.price || 0})</option>`;
                });

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm">
                            ${optionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="1" name="items[${rowIndex}][qty]" class="form-control form-control-sm claim-qty" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][unit_rate]" class="form-control form-control-sm claim-rate" value="0.00">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][subtotal]" class="form-control form-control-sm claim-subtotal" value="0.00" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger p-1 remove-row-btn" style="line-height: 1;">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;

                body.appendChild(tr);

                const qtyInp = tr.querySelector('.claim-qty');
                const rateInp = tr.querySelector('.claim-rate');
                const subInp = tr.querySelector('.claim-subtotal');
                const removeBtn = tr.querySelector('.remove-row-btn');

                function updateRow() {
                    const q = parseFloat(qtyInp.value) || 0;
                    const r = parseFloat(rateInp.value) || 0;
                    subInp.value = (q * r).toFixed(2);
                    recalcTotal();
                }

                qtyInp.addEventListener('input', updateRow);
                rateInp.addEventListener('input', updateRow);

                removeBtn.addEventListener('click', function () {
                    tr.remove();
                    recalcTotal();
                });

                rowIndex++;
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const distSelect = document.getElementById('adminDistId');
        const fromDateInp = document.getElementById('adminFromDate');
        const toDateInp = document.getElementById('adminToDate');
        const calcDisplay = document.getElementById('adminCalcDisplay');
        const calcNotice = document.getElementById('adminCalcNotice');
        const totalAmountInp = document.getElementById('adminTotalClaimAmount');
        const submitBtn = document.getElementById('adminBtnSubmitClaim');

        const calculateUrl = "{{ route('admin.distributors.claims.calculate_discount') }}";

        function fetchAdminDiscount() {
            const distId = distSelect ? distSelect.value : '';
            const fromDate = fromDateInp ? fromDateInp.value : '';
            const toDate = toDateInp ? toDateInp.value : '';

            if (!distId || !fromDate || !toDate) {
                if (submitBtn) submitBtn.disabled = true;
                return;
            }

            calcNotice.textContent = "Calculating unclaimed shop discounts...";
            if (submitBtn) submitBtn.disabled = true;

            fetch(`${calculateUrl}?distributor_id=${encodeURIComponent(distId)}&from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    const totalDisc = parseFloat(data.total_discount || 0);
                    const count = parseInt(data.count || 0);

                    calcDisplay.textContent = 'Rs. ' + totalDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    totalAmountInp.value = totalDisc.toFixed(2);

                    if (totalDisc > 0 && count > 0) {
                        calcNotice.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Found <strong>${count}</strong> unclaimed shop invoices (Total: Rs. ${totalDisc.toFixed(2)})</span>`;
                        if (submitBtn) submitBtn.disabled = false;
                    } else {
                        calcNotice.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> No unclaimed discounts found for this distributor in selected date range.</span>`;
                        if (submitBtn) submitBtn.disabled = true;
                    }
                }
            })
            .catch(err => {
                calcNotice.textContent = "Error calculating discount.";
            });
        }

        if (distSelect) distSelect.addEventListener('change', fetchAdminDiscount);
        if (fromDateInp) fromDateInp.addEventListener('change', fetchAdminDiscount);
        if (toDateInp) toDateInp.addEventListener('change', fetchAdminDiscount);
    });
</script>
@endsection
