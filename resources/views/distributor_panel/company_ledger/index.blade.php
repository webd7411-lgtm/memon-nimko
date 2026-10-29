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
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Financial Statement
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">My Company Account Statement</h4>
            <p class="mb-0 text-white-50 small">Track all goods received from Head Office, payments deposited, and current outstanding balance</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.payments.index') }}" class="btn btn-teal btn-sm rounded-pill px-3 py-2 shadow-sm">
                <i class="fas fa-money-bill-wave me-1"></i> Make Payment to Company
            </a>
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Statement
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Goods Received (Debit)</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($totalGoodsReceived, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Payments Deposited (Credit)</span>
                <h4 class="fw-bold mb-0 text-success mt-1">Rs. {{ number_format($totalPaidToCompany, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #ef4444 !important;">
                <span class="text-muted small fw-semibold text-uppercase">My Current Balance to Company</span>
                <h4 class="fw-bold mb-0 {{ $currentBalance > 0 ? 'text-danger' : 'text-success' }} mt-1">
                    Rs. {{ number_format($currentBalance, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Statement Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-alt me-2 text-teal" style="color: #0f766e;"></i> Company Ledger History
            </h6>
            <form method="GET" action="{{ route('distributor.company_ledger.index') }}" class="d-flex align-items-center gap-2">
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
                        <th>Description / Items Summary</th>
                        <th class="text-end">Goods Received (Debit)</th>
                        <th class="text-end">Payment Deposited (Credit)</th>
                        <th class="text-end pe-4">My Balance</th>
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
                            <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-0.5">🚚 Stock Dispatched</span>
                            @elseif($entry->type === 'payment_received')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5">💰 Payment Received</span>
                            @elseif($entry->type === 'claim_adjustment')
                            <span class="badge bg-info-subtle text-info-emphasis border border-info px-2 py-0.5">🏷️ Claim / Scheme Credit</span>
                            @else
                            <span class="badge bg-light text-secondary border px-2 py-0.5">{{ ucfirst(str_replace('_', ' ', $entry->type)) }}</span>
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
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Transactions with Company Yet</p>
                            <span class="small">When Head Office dispatches goods or logs your payments, they will show here.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
