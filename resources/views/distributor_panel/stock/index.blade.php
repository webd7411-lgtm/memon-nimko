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
                    Physical Inventory
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Stock Inventory</h4>
            <p class="mb-0 text-white-50 small">Goods issued from company warehouse available for retail market sales</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.orders.create') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2">
                <i class="fas fa-plus me-1"></i> Demand More Stock
            </a>
            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-teal rounded-pill px-3 py-2">
                <i class="fas fa-cart-plus me-1"></i> Make Sale
            </a>
        </div>
    </div>

    <!-- Quick Stat Pill -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center justify-content-between"
                 style="border-left: 4px solid #0f766e !important;">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Quantity in Custody</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($totalQty, 0) }} <span class="fs-6 fw-normal text-muted">units</span></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 1.2rem;">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-alt me-2 text-teal" style="color: #0f766e;"></i> Available Products List
            </h6>
            <form method="GET" action="{{ route('distributor.stock.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-start-pill" placeholder="Search product name...">
                    <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Product Name</th>
                        <th>Variant / Size</th>
                        <th class="text-center">Available Qty</th>
                        <th class="text-end">Issue Rate (Rs)</th>
                        <th class="text-end">Total Value</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($stocks as $stock)
                    <tr>
                        <td class="ps-4">
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
                            <span class="badge rounded-pill {{ $stock->qty > 0 ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger' }} px-3 py-1.5 fs-6 fw-bold">
                                {{ (float)$stock->qty }} {{ $stock->product->unit->name ?? 'pcs' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="text-dark">Rs. {{ number_format($stock->avg_price, 2) }}</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">Rs. {{ number_format(((float)$stock->qty * (float)$stock->avg_price), 2) }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                <i class="fas fa-cash-register me-1"></i> Sell
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">You currently have no stock in custody.</p>
                            <span class="small">Click "Demand More Stock" to place a purchase requisition to Head Office.</span>
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
