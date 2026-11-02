@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #b91c1c 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5;">
                    <i class="fas fa-undo-alt me-1"></i> Customer Sales Returns
                </span>
                <span class="text-white-50">•</span>
                <span class="text-white-50 small">Retailer Returns Register</span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Retail Customer Sales Returns</h4>
            <p class="mb-0 text-white-50 small">Manage and audit goods returned by shops, refund cash, or adjust their ledger balance</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.sales.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2">
                <i class="fas fa-cash-register me-1"></i> Sales Register
            </a>
            <a href="{{ route('distributor.returns.create') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 py-2 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> + New Sale Return
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('distributor.returns.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <select name="customer_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Retail Customers</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->shop_name }} ({{ $c->owner_name ?? 'Owner' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" placeholder="Start Date">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" placeholder="End Date">
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill flex-fill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['customer_id', 'start_date', 'end_date']))
                    <a href="{{ route('distributor.returns.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Return #</th>
                        <th>Retail Customer</th>
                        <th>Original Sale #</th>
                        <th>Date</th>
                        <th class="text-center">Mode</th>
                        <th class="text-end">Return Value</th>
                        <th class="text-end">Ledger Credit</th>
                        <th class="text-end">Cash Refunded</th>
                        <th class="text-end pe-4">Receipt</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($returns as $ret)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-danger">{{ $ret->return_no }}</span>
                            @if($ret->reason)
                            <div class="text-muted small text-truncate" style="max-width: 180px;" title="{{ $ret->reason }}">
                                {{ $ret->reason }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $ret->customer->shop_name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $ret->customer->phone ?? '' }}</span>
                        </td>
                        <td>
                            @if($ret->sale)
                            <a href="{{ route('distributor.sales.receipt', $ret->sale->id) }}" class="badge bg-light text-primary border text-decoration-none">
                                {{ $ret->sale->invoice_no }}
                            </a>
                            @else
                            <span class="badge bg-light text-muted border">Direct Return</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($ret->return_date)->format('d M, Y') }}</span>
                            @if($ret->created_at)
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $ret->created_at->timezone(config('app.timezone', 'Asia/Karachi'))->format('h:i A') }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ret->refund_mode === 'cash')
                            <span class="badge bg-warning-subtle text-warning border border-warning px-2.5 py-1">
                                Cash Refund
                            </span>
                            @else
                            <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1">
                                Balance Deducted
                            </span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($ret->net_return_amount, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">
                            Rs. {{ number_format($ret->credit_adjusted, 2) }}
                        </td>
                        <td class="text-end text-danger fw-semibold">
                            Rs. {{ number_format($ret->refund_amount, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('distributor.sales.receipt_return', $ret->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                <i class="fas fa-print me-1"></i> Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-undo-alt fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Customer Returns Processed Yet</p>
                            <span class="small">Click "+ New Sale Return" to receive returned items from retail shops.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $returns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
