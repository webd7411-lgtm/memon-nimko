@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.customers.index') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Retailers
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Shop Ledger
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">{{ $customer->shop_name }}</h4>
            <p class="mb-0 text-white-50 small">Owner: {{ $customer->owner_name ?? 'N/A' }} • Phone: {{ $customer->phone ?? '—' }} • Area: {{ $customer->area ?? 'General' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold"
                    data-bs-toggle="modal" data-bs-target="#receivePaymentModal">
                <i class="fas fa-hand-holding-usd me-1"></i> Collect Payment
            </button>
        </div>
    </div>

    <!-- Balance Summary Card -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Billed to Shop (Debit)</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($totalDebit, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Payments Collected (Credit)</span>
                <h4 class="fw-bold mb-0 text-success mt-1">Rs. {{ number_format($totalCredit, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #ef4444 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Outstanding Due from Shop</span>
                <h4 class="fw-bold mb-0 {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }} mt-1">
                    Rs. {{ number_format($customer->current_balance, 2) }}
                </h4>
            </div>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-history me-2 text-teal" style="color: #0f766e;"></i> Transaction Ledger History
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Type</th>
                        <th>Invoice / Ref #</th>
                        <th>Description</th>
                        <th class="text-end">Sale / Debit</th>
                        <th class="text-end">Payment / Credit</th>
                        <th class="text-end pe-4">Balance</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($entries as $entry)
                    <tr>
                        <td class="ps-4">
                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($entry->date)->format('d M, Y') }}</span>
                        </td>
                        <td>
                            @if($entry->type === 'sale')
                            <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-0.5">🛒 Sale</span>
                            @elseif($entry->type === 'payment_received')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5">💰 Received</span>
                            @else
                            <span class="badge bg-light text-secondary border px-2 py-0.5">{{ ucfirst($entry->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace text-muted small">{{ $entry->reference_no ?: '—' }}</span>
                        </td>
                        <td>
                            <div class="text-dark">{{ $entry->description }}</div>
                            @if($entry->account)
                            <div class="mt-1">
                                <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.72rem;">
                                    <i class="fas fa-wallet me-1" style="color: #0f766e;"></i> {{ $entry->account->title }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($entry->debit > 0)
                            <span class="fw-bold text-danger">Rs. {{ number_format($entry->debit, 2) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($entry->credit > 0)
                            <span class="fw-bold text-success">Rs. {{ number_format($entry->credit, 2) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold {{ $entry->balance > 0 ? 'text-dark' : 'text-success' }}">
                                Rs. {{ number_format($entry->balance, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Transactions on Record</p>
                            <span class="small">Sales and payment collections with this retailer will show here automatically.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Receive Payment Modal -->
<div class="modal fade" id="receivePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('distributor.customers.payment.store', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <h5 class="modal-title fw-bold"><i class="fas fa-hand-holding-usd me-2"></i> Receive Payment from Retailer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3 bg-light border">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Shop Name:</span>
                            <span class="fw-bold text-dark">{{ $customer->shop_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Current Outstanding:</span>
                            <span class="fw-bold text-danger">Rs. {{ number_format($customer->current_balance, 2) }}</span>
                        </div>
                    </div>

                    <!-- Deposit To Distributor Account -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0">Deposit To Account (Distributor Account) <span class="text-danger">*</span></label>
                            <a href="{{ route('distributor.accounts.index') }}" target="_blank" class="small text-teal text-decoration-none fw-semibold" style="color: #0f766e; font-size: 0.75rem;">
                                <i class="fas fa-plus-circle me-1"></i>Manage Accounts
                            </a>
                        </div>
                        <select name="account_id" class="form-select" required>
                            @if(isset($accounts) && $accounts->count() > 1)
                                <option value="">-- Select Receiving Account --</option>
                            @endif
                            @if(isset($accounts))
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ ($loop->first && $accounts->count() === 1) ? 'selected' : '' }}>
                                        {{ $acc->title }}
                                        @if($acc->account_number) (A/C: {{ $acc->account_number }}) @endif
                                        — [Bal: Rs. {{ number_format($acc->opening_balance, 2) }}]
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted d-block mt-1">Collected payment will be credited into your selected Cash/Bank account.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Collected Amount (Rs) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-lg fw-bold" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Collection Date *</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks / Receipt Note</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background: #0f766e; border: 1px solid #14b8a6;">
                        <i class="fas fa-check me-1"></i> Save Collection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
