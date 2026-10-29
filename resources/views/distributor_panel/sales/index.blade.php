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
                    Sales Register
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Retail Sales History</h4>
            <p class="mb-0 text-white-50 small">Record of all stock sold to retail stores and market shops</p>
        </div>
        <div>
            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold">
                <i class="fas fa-cart-plus me-1"></i> + Make New Sale
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.sales.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <select name="customer_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Retail Stores</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->shop_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-sm text-white rounded-pill w-100" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Retail Customer</th>
                        <th>Date</th>
                        <th class="text-center">Mode</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Cash Paid</th>
                        <th class="text-end">Due Balance</th>
                        <th class="text-end pe-4">Receipt</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($sales as $sale)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-dark">{{ $sale->invoice_no }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sale->customer->shop_name ?? 'Walk-in' }}</div>
                            <span class="text-muted small">{{ $sale->customer->phone ?? '' }}</span>
                        </td>
                        <td>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            @if($sale->payment_type === 'cash')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5">Cash</span>
                            @elseif($sale->payment_type === 'credit')
                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0.5">Credit</span>
                            @else
                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-0.5">Partial</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($sale->grand_total, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">
                            Rs. {{ number_format($sale->paid_amount, 2) }}
                        </td>
                        <td class="text-end fw-bold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                            Rs. {{ number_format($sale->due_amount, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('distributor.sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                <i class="fas fa-print me-1"></i> Print
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Sales Invoices Found</p>
                            <span class="small">Click "+ Make New Sale" above to bill your retail customers.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
