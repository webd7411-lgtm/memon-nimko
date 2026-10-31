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
                <i class="fas fa-boxes" style="color: #0f766e;"></i>
                Stock Transfers & Field Custody
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Van / Field Stock</span>
            </h5>
            <span class="text-muted small">Live inventory quantities currently loaded in distributor custody, vehicles, and vans</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.distributors.opening_stock') }}" class="btn btn-sm text-white rounded-pill px-3 shadow-sm" style="background: linear-gradient(135deg, #0f766e, #0d9488); font-weight: 600;">
                <i class="fas fa-plus-circle me-1"></i> Add Opening Stock
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.stocks') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <select name="distributor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->item_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-sm text-white rounded-pill w-100" style="background: #0f766e;">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Distributor</th>
                        <th>Product Name</th>
                        <th>Variant / Size</th>
                        <th class="text-center">Available in Van/Custody</th>
                        <th class="text-end">Avg. Issue Rate</th>
                        <th class="text-end pe-4">Stock Value (Rs)</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($stocks as $stock)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $stock->distributor->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $stock->distributor->code ?? '' }} • {{ $stock->distributor->phone ?? '' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $stock->product->item_name ?? 'Product' }}</div>
                            <span class="text-muted small">Unit: {{ $stock->product->unit->name ?? 'Pc' }}</span>
                        </td>
                        <td>
                            @if($stock->variant)
                            <span class="badge bg-light text-secondary border px-2 py-0.5">
                                {{ $stock->variant->size_label ?: $stock->variant->variant_name }}
                            </span>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3 py-1.5 fs-6 fw-bold">
                                {{ (float)$stock->qty }} {{ $stock->product->unit->name ?? 'pcs' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="text-dark">Rs. {{ number_format($stock->avg_price, 2) }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold text-dark">Rs. {{ number_format(((float)$stock->qty * (float)$stock->avg_price), 2) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-boxes fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Stock Currently in Distributor Custody</p>
                            <span class="small">When you dispatch demand orders to distributors, their stock will be tracked here in real-time.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $stocks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
