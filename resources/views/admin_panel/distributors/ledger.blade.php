@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2">
    <!-- Top Action Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-file-invoice" style="color: #0f766e;"></i>
                {{ $distributor->name }} — Company Ledger
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Statement</span>
            </h5>
            <span class="text-muted small">Code: {{ $distributor->code }} • Phone: {{ $distributor->phone }} • City: {{ $distributor->city ?? 'Karachi' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.distributors.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5">
                <i class="fas fa-arrow-left me-1"></i> Distributors
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Statement
            </button>
            <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-bold shadow-sm"
                    style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;"
                    data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="fas fa-hand-holding-usd me-1"></i> Receive Payment
            </button>
        </div>
    </div>

    <!-- Balance Summary Card -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Goods Dispatched (Debit)</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($totalDebit, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Payments Received (Credit)</span>
                <h4 class="fw-bold mb-0 text-success mt-1">Rs. {{ number_format($totalCredit, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #ef4444 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Net Current Balance (Receivable)</span>
                <h4 class="fw-bold mb-0 {{ $distributor->current_balance > 0 ? 'text-danger' : 'text-success' }} mt-1">
                    Rs. {{ number_format($distributor->current_balance, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-alt me-2 text-teal" style="color: #0f766e;"></i> Transaction Ledger Entries
            </h5>
            <form method="GET" action="{{ route('admin.distributors.ledger', $distributor->id) }}" class="d-flex align-items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Type</th>
                        <th>Ref #</th>
                        <th>Description / Details</th>
                        <th class="text-end">Debit (Goods Shipped)</th>
                        <th class="text-end">Credit (Payment Given)</th>
                        <th class="text-end pe-4">Running Balance</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($entries as $entry)
                    <tr>
                        <td class="ps-4">
                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($entry->date)->format('d M, Y') }}</span>
                        </td>
                        <td>
                            @if($entry->type === 'dispatch')
                            <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-0.5">🚚 Dispatch</span>
                            @elseif($entry->type === 'payment_received')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5">💰 Payment</span>
                            @elseif($entry->type === 'opening')
                            <span class="badge bg-light text-secondary border px-2 py-0.5">🏁 Opening</span>
                            @else
                            <span class="badge bg-secondary text-white px-2 py-0.5">{{ ucfirst($entry->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace text-muted small">{{ $entry->reference_no ?: '—' }}</span>
                        </td>
                        <td>
                            <div class="text-dark">{{ $entry->description }}</div>
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
                            <i class="fas fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Ledger Entries Recorded</p>
                            <span class="small">Transactions will appear automatically when you dispatch orders or record payments.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.distributors.payment.store', $distributor->id) }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <h5 class="modal-title fw-bold"><i class="fas fa-hand-holding-usd me-2"></i> Receive Payment from Distributor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3 bg-light border">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Distributor:</span>
                            <span class="fw-bold text-dark">{{ $distributor->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Outstanding Balance:</span>
                            <span class="fw-bold text-danger">Rs. {{ number_format($distributor->current_balance, 2) }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (Rs) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-lg fw-bold" placeholder="0.00" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Date *</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Mode *</label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer / Online</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bank / Slip / Cheque Reference #</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Optional transaction ID">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes / Remarks</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Optional payment remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background: #0f766e; border: 1px solid #14b8a6;">
                        <i class="fas fa-check me-1"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
