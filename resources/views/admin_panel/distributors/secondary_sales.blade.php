@extends('admin_panel.layout.app')

@section('content')
<style>
    .dist-module-container {
        padding-left: 14px !important;
        padding-right: 14px !important;
        padding-top: 6px !important;
        padding-bottom: 30px !important;
    }
    @media (min-width: 768px) {
        .dist-module-container {
            padding-left: 20px !important;
            padding-right: 20px !important;
            padding-top: 12px !important;
        }
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-chart-line" style="color: #0f766e;"></i>
                Distributors Market Sales Audit & Reports
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Secondary Sales</span>
            </h5>
            <span class="text-muted small">Audit all sales executed by distributors and order bookers to retail shops in the market</span>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Market Sales</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($totalMarketSales, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Market Cash Collected</span>
                <h4 class="fw-bold mb-0 text-success mt-1">Rs. {{ number_format($totalMarketCollected, 2) }}</h4>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #f59e0b !important;">
                <span class="text-muted small fw-semibold text-uppercase">Retailer Market Credit / Due</span>
                <h4 class="fw-bold mb-0 text-warning mt-1">Rs. {{ number_format($totalMarketDue, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.secondary_sales') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <select name="distributor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
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
                        <th>Distributor</th>
                        <th>Retail Customer / Shop</th>
                        <th>Date</th>
                        <th class="text-center">Items Sold</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid (Cash)</th>
                        <th class="text-end pe-4">Due Balance</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($sales as $sale)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-dark">{{ $sale->invoice_no }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sale->distributor->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $sale->distributor->code ?? '' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sale->customer->shop_name ?? 'Walk-in' }}</div>
                            <span class="text-muted small">{{ $sale->customer->phone ?? '' }} {{ $sale->customer->area ? "({$sale->customer->area})" : '' }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                {{ $sale->items->count() }} items ({{ $sale->items->sum('qty') }} pcs)
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($sale->grand_total, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">
                            Rs. {{ number_format($sale->paid_amount, 2) }}
                        </td>
                        <td class="text-end pe-4 fw-bold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                            Rs. {{ number_format($sale->due_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-basket fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Market Sales Recorded Yet</p>
                            <span class="small">When distributors sell to retail shops from their custody, sales records will appear here.</span>
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
