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
            <h4 class="mb-0 mt-1 fw-bold text-white">Claims & Scheme Reimbursements</h4>
            <p class="mb-0 text-white-50 small">
                Submit claims for retail shop discounts, damage stock, or freight to be credited to your company ledger.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.company_ledger.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                <i class="fas fa-file-invoice-dollar me-1"></i> View Company Ledger
            </a>
            <button type="button" class="btn btn-teal btn-sm rounded-pill px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                <i class="fas fa-plus-circle me-1"></i> Submit New Claim
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
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #ef4444 !important;">
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

        <!-- 2. Pending Claims -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Under Verification</span>
                        <h4 class="fw-bold mb-0 text-warning-emphasis mt-1">
                            Rs. {{ number_format($pendingAmount, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">{{ $pendingCount }} claim(s) pending audit</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-warning">
                        <i class="fas fa-clock fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Approved & Credited -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Approved Credits</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            Rs. {{ number_format($approvedAmount, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Deducted from your ledger debt</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-success">
                        <i class="fas fa-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Notice / How It Works -->
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="border-left: 4px solid #0f766e !important;">
                <div class="small fw-bold text-teal mb-1" style="color: #0f766e;">
                    <i class="fas fa-info-circle me-1"></i> How Claims Work:
                </div>
                <div class="small text-muted" style="font-size: 0.75rem; line-height: 1.35;">
                    Discounts given to shops or damage stock are claimed here. Once approved by Head Office, it is automatically credited to your ledger.
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.claims.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Claim Type</label>
                    <select name="claim_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="trade_discount" {{ request('claim_type') === 'trade_discount' ? 'selected' : '' }}>Trade Schemes / Shop Discounts</option>
                        <option value="damage_stock" {{ request('claim_type') === 'damage_stock' ? 'selected' : '' }}>Damaged / Expired Goods</option>
                        <option value="freight" {{ request('claim_type') === 'freight' ? 'selected' : '' }}>Freight / Bilty Paid</option>
                        <option value="target_incentive" {{ request('claim_type') === 'target_incentive' ? 'selected' : '' }}>Target Incentive</option>
                        <option value="other" {{ request('claim_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
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
                <i class="fas fa-clipboard-list text-teal me-1" style="color: #0f766e;"></i> My Submitted Claims
            </div>
            <span class="badge bg-light text-secondary border">Showing {{ $claims->count() }} of {{ $claims->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.74rem;">
                    <tr>
                        <th>Claim ID & Date</th>
                        <th>Claim Type</th>
                        <th>Description / Details</th>
                        <th class="text-end">Claimed Amount</th>
                        <th class="text-end">Approved Amount</th>
                        <th>Settlement Mode</th>
                        <th>Status</th>
                        <th>Proof Receipt</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $claim->claim_no }}</div>
                            <div class="text-muted small" style="font-size: 0.73rem;">
                                {{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}
                            </div>
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $claim->type_badge_class }} px-2.5 py-1">
                                {{ $claim->type_label }}
                            </span>
                        </td>

                        <td>
                            <div class="text-truncate" style="max-width: 260px;" title="{{ $claim->description }}">
                                {{ $claim->description ?: 'No remarks entered' }}
                            </div>
                            @if($claim->items->count() > 0)
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                {{ $claim->items->count() }} line item(s) included
                            </div>
                            @endif
                        </td>

                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($claim->total_amount, 2) }}
                        </td>

                        <td class="text-end">
                            @if($claim->status === 'approved')
                                <span class="fw-bold text-success">Rs. {{ number_format($claim->approved_amount, 2) }}</span>
                            @elseif($claim->status === 'rejected')
                                <span class="text-muted text-decoration-line-through">Rs. 0.00</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis">Under Audit</span>
                            @endif
                        </td>

                        <td>
                            @if($claim->settlement_type === 'credit_note')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                    <i class="fas fa-file-invoice me-1"></i> Ledger Credit
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="fas fa-boxes-packing me-1"></i> Stock Replenish
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $claim->status_badge_class }} px-2.5 py-1 text-uppercase" style="font-size: 0.68rem;">
                                {{ $claim->status }}
                            </span>
                            @if($claim->admin_notes && $claim->status === 'rejected')
                            <div class="text-danger small mt-0.5" style="font-size: 0.7rem;" title="{{ $claim->admin_notes }}">
                                Reason: {{ Str::limit($claim->admin_notes, 25) }}
                            </div>
                            @endif
                        </td>

                        <td>
                            @if($claim->attachment)
                                <a href="{{ asset($claim->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="fas fa-paperclip me-1"></i> Receipt
                                </a>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <!-- View Breakdown Modal Trigger -->
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#distClaimViewModal{{ $claim->id }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>

                    <!-- Details Modal -->
                    <div class="modal fade" id="distClaimViewModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom py-3">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark">Claim #{{ $claim->claim_no }}</h5>
                                        <span class="badge {{ $claim->type_badge_class }}">{{ $claim->type_label }}</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-3 p-3 bg-light rounded-3">
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Claim Date</div>
                                            <div class="fw-bold">{{ $claim->claim_date ? $claim->claim_date->format('d M, Y') : '-' }}</div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Claimed Amount</div>
                                            <div class="fw-bold text-dark">Rs. {{ number_format($claim->total_amount, 2) }}</div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Approved Amount</div>
                                            <div class="fw-bold text-success">
                                                {{ $claim->status === 'approved' ? 'Rs. ' . number_format($claim->approved_amount, 2) : 'Pending' }}
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-muted small">Status</div>
                                            <span class="badge {{ $claim->status_badge_class }}">{{ strtoupper($claim->status) }}</span>
                                        </div>
                                    </div>

                                    @if($claim->description)
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Submitted Explanation:</label>
                                        <div class="p-2.5 rounded-3 bg-white border small text-muted">
                                            {{ $claim->description }}
                                        </div>
                                    </div>
                                    @endif

                                    @if($claim->items->count() > 0)
                                    <h6 class="fw-bold small text-uppercase text-muted mb-2">Item Breakdown</h6>
                                    <div class="table-responsive border rounded-3 mb-3">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Claim Rate</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th>Reason / Shop Details</th>
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

                                    @if($claim->admin_notes)
                                    <div class="alert alert-secondary p-2.5 rounded-3 small mb-0">
                                        <strong>Head Office Remarks:</strong> {{ $claim->admin_notes }}
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer border-top py-2">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-hand-holding-dollar fs-1 text-muted opacity-25 mb-2"></i>
                            <h6 class="fw-bold text-secondary">No Claims Submitted Yet</h6>
                            <p class="text-muted small mb-3">If you provided promotional discounts to shops or have damage stock, submit a claim below.</p>
                            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                                + Submit My First Claim
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
<!-- MODAL: SUBMIT NEW CLAIM (DISTRIBUTOR) -->
<!-- ============================================================== -->
<div class="modal fade" id="newClaimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('distributor.claims.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3" style="background: linear-gradient(135deg, #090d16, #0f766e); color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-invoice me-1"></i> Submit New Claim to Head Office
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Claim Type <span class="text-danger">*</span></label>
                        <select name="claim_type" class="form-select" required>
                            <option value="trade_discount" selected>Trade Scheme / Discount to Shops (Reimbursement)</option>
                            <option value="damage_stock">Damaged / Expired / Leaked Products</option>
                            <option value="freight">Bilty / Transport Fare Reimbursement</option>
                            <option value="target_incentive">Monthly Sales Target Incentive</option>
                            <option value="other">Other Operational Claim</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Claim Date <span class="text-danger">*</span></label>
                        <input type="date" name="claim_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Total Claim Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Rs.</span>
                            <input type="number" step="0.01" name="total_amount" id="portalTotalAmount" class="form-control fw-bold fs-6" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark">Requested Settlement Mode <span class="text-danger">*</span></label>
                        <select name="settlement_type" class="form-select" required>
                            <option value="credit_note" selected>Credit Note (Credit to Company Ledger)</option>
                            <option value="stock_replacement">Stock Replacement (Send Replacement Mal)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Proof / Bill Receipt / Photo</label>
                        <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                        <div class="form-text small">Attach photos of damage goods or retailer invoice copies showing discounts given.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Description & Reason</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. As per company promo, 5% cash discount was provided to retail shops on 150 cartons of Memon Nimko." required></textarea>
                    </div>
                </div>

                <!-- Itemized Breakdown -->
                <div class="border-top pt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label small fw-bold text-dark mb-0">
                            <i class="fas fa-boxes-stacked me-1"></i> Optional Product Breakdown
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill py-0.5 px-2" id="portalAddRowBtn">
                            + Add Item Row
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="portalItemsTable">
                            <thead class="table-light">
                                <tr style="font-size: 0.74rem;">
                                    <th style="width: 40%;">Product</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 20%;">Rate / Disc</th>
                                    <th style="width: 20%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="portalItemsBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-teal px-4">
                    <i class="fas fa-paper-plane me-1"></i> Submit to Head Office
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('portalAddRowBtn');
        const body = document.getElementById('portalItemsBody');
        const totalInput = document.getElementById('portalTotalAmount');

        const products = @json($products);

        let rowIndex = 0;

        function recalcTotal() {
            let sum = 0;
            const subtotals = document.querySelectorAll('.portal-claim-subtotal');
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
                        <input type="number" step="1" name="items[${rowIndex}][qty]" class="form-control form-control-sm portal-qty" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][unit_rate]" class="form-control form-control-sm portal-rate" value="0.00">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][subtotal]" class="form-control form-control-sm portal-claim-subtotal" value="0.00" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger p-1 remove-row-btn" style="line-height: 1;">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;

                body.appendChild(tr);

                const qtyInp = tr.querySelector('.portal-qty');
                const rateInp = tr.querySelector('.portal-rate');
                const subInp = tr.querySelector('.portal-claim-subtotal');
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
@endsection
