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
            <span class="text-muted small">Audit all sales and free trade offer pieces given by distributors and order bookers to retail shops in the market</span>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #0f766e !important;">
                <span class="text-muted small fw-semibold text-uppercase">Total Market Sales</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($totalMarketSales, 2) }}</h4>
                <small class="text-muted fw-semibold">Filtered Net Sales</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Market Cash Collected</span>
                <h4 class="fw-bold mb-0 text-success mt-1">Rs. {{ number_format($totalMarketCollected, 2) }}</h4>
                <small class="text-muted fw-semibold">Cash recovery</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #f59e0b !important;">
                <span class="text-muted small fw-semibold text-uppercase">Retailer Market Credit / Due</span>
                <h4 class="fw-bold mb-0 text-warning mt-1">Rs. {{ number_format($totalMarketDue, 2) }}</h4>
                <small class="text-muted fw-semibold">Pending market udhaar</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #2563eb !important; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small fw-bold text-uppercase">Total Trade Offer Given</span>
                    <span class="badge bg-primary text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem;"><i class="fas fa-gift me-1"></i>Free Pcs</span>
                </div>
                <h4 class="fw-bolder mb-0 text-primary mt-1">{{ number_format($totalTradeOfferPcs ?? 0) }} Pcs Free</h4>
                <small class="text-muted fw-semibold">Total free scheme pieces delivered</small>
            </div>
        </div>
    </div>

    <!-- Filter Bar with Month and Date Range -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.secondary_sales') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Distributor</label>
                    <select name="distributor_id" class="form-select form-select-sm">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Filter by Month</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="form-control form-control-sm" title="Select a month to filter">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white rounded-pill w-100 fw-bold" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter Sales &amp; T.O
                    </button>
                    <a href="{{ route('admin.distributors.secondary_sales') }}" class="btn btn-sm btn-light border rounded-pill w-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-none d-md-block bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Distributor</th>
                        <th>Retail Customer / Shop</th>
                        <th>Date</th>
                        <th class="text-center">Items Sold</th>
                        <th class="text-center" style="background: #eff6ff; color: #1d4ed8;">Trade Offer (Free)</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid (Cash)</th>
                        <th class="text-end">Due Balance</th>
                        <th class="text-center pe-3">Details</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($sales as $sale)
                    @php
                        $saleTO = (float) $sale->items->sum('trade_offer');
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-dark">{{ $sale->invoice_no }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sale->distributor->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $sale->distributor->code ?? '' }}</span>
                        </td>
                        <td>
                            @if($sale->customer)
                            <a href="{{ route('admin.distributors.customers.ledger', $sale->distributor_customer_id) }}" class="fw-bold text-dark text-decoration-none" title="View Customer Ledger">
                                {{ $sale->customer->shop_name }} <i class="fas fa-external-link-alt text-muted ms-0.5" style="font-size: 0.65rem;"></i>
                            </a>
                            <div class="text-muted small">{{ $sale->customer->phone ?? '' }} {{ $sale->customer->area ? "({$sale->customer->area})" : '' }}</div>
                            @else
                            <div class="fw-bold text-dark">Walk-in</div>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                {{ $sale->items->count() }} items ({{ $sale->items->sum('qty') }} pcs)
                            </span>
                        </td>
                        <td class="text-center" style="background: #fafcff;">
                            @if($saleTO > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold" style="font-size: 0.78rem;">
                                    <i class="fas fa-gift me-1"></i> +{{ number_format($saleTO) }} Pcs Free
                                </span>
                            @else
                                <span class="text-muted small">0 Pcs</span>
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
                        <td class="text-center pe-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" data-bs-toggle="collapse" data-bs-target="#saleItemsCollapse_{{ $sale->id }}" aria-expanded="false" title="View Item Details & Free Pcs">
                                <i class="fas fa-list-ul me-1"></i> Items
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse bg-light" id="saleItemsCollapse_{{ $sale->id }}">
                        <td colspan="10" class="p-3">
                            <div class="card border rounded-3 shadow-xs">
                                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-dark"><i class="fas fa-boxes text-primary me-1"></i> Invoice Products &amp; Trade Offer Breakdown for #{{ $sale->invoice_no }}</span>
                                    <span class="badge bg-primary text-white">Total T.O: {{ number_format($saleTO) }} Free Pcs</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Product</th>
                                                <th class="text-center" style="width: 90px;">Sold Qty</th>
                                                <th class="text-center text-primary" style="width: 120px;">Trade Offer (Free)</th>
                                                <th class="text-center fw-bold" style="width: 130px;">Total Physical Qty</th>
                                                <th class="text-end" style="width: 110px;">Price</th>
                                                <th class="text-end" style="width: 110px;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->items as $idx => $it)
                                            @php
                                                $itTO = (float)($it->trade_offer ?? 0);
                                                $itTotalPhys = (float)$it->qty + $itTO;
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <strong>{{ $it->product->item_name ?? 'Item' }}</strong>
                                                    @if($it->variant)
                                                        <span class="badge bg-secondary-subtle text-dark ms-1">{{ $it->variant->name }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">{{ (float)$it->qty }}</td>
                                                <td class="text-center">
                                                    @if($itTO > 0)
                                                        <span class="badge bg-primary text-white fw-bold">+{{ $itTO }} Free</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text-dark">{{ $itTotalPhys }} pcs</td>
                                                <td class="text-end">Rs. {{ number_format($it->unit_price, 2) }}</td>
                                                <td class="text-end fw-bold">Rs. {{ number_format($it->total, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-basket fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Market Sales Recorded Yet</p>
                            <span class="small">When distributors sell to retail shops from their custody, sales records and trade offers will appear here.</span>
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

    <!-- Mobile Secondary Sales Cards (Zero Horizontal Scroll) -->
    <div class="d-block d-md-none mb-4">
        @forelse($sales as $sale)
        @php
            $saleTO = (float) $sale->items->sum('trade_offer');
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white p-3" style="border: 1px solid #edf2f7 !important;">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2 border-bottom">
                <div>
                    <span class="fw-bold font-monospace text-dark d-block" style="font-size: 0.92rem;">{{ $sale->invoice_no }}</span>
                    <span class="text-muted small">{{ CarbonCarbon::parse($sale->sale_date)->format('d M, Y') }}</span>
                </div>
                <span class="badge bg-light text-dark border px-2 py-1 small">
                    {{ $sale->items->count() }} items
                </span>
            </div>

            <div class="mb-2">
                <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                    {{ $sale->customer->shop_name ?? 'Retail Customer' }}
                </div>
                <span class="text-muted small">Distributor: {{ $sale->distributor->name ?? 'Distributor' }}</span>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2 px-2.5 rounded-3 bg-light my-2">
                <div>
                    <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">TOTAL</span>
                    <span class="fw-bold text-dark">Rs. {{ number_format($sale->grand_total, 2) }}</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">PAID</span>
                    <span class="fw-bold text-success">Rs. {{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                <div class="text-end">
                    <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">DUE</span>
                    <span class="fw-bold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-success' }} fs-6">Rs. {{ number_format($sale->due_amount, 2) }}</span>
                </div>
            </div>

            @if($saleTO > 0)
            <div class="small text-primary mb-2 p-1.5 rounded-2 bg-primary-subtle border border-primary-subtle" style="font-size: 0.72rem;">
                <i class="fas fa-gift me-1"></i> Trade Offer Schemes Given: <strong>{{ (float)$saleTO }} pcs free</strong>
            </div>
            @endif

            <div class="pt-2 border-top">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 d-flex align-items-center justify-content-center"
                        style="min-height: 34px; font-size: 0.76rem;"
                        data-bs-toggle="modal" data-bs-target="#saleModal{{ $sale->id }}">
                    <i class="fas fa-eye me-1"></i> View Invoice Breakdown
                </button>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
            <i class="fas fa-chart-line fa-2x mb-2 text-secondary opacity-50"></i>
            <p class="mb-0 small">No Secondary Market Sales Recorded</p>
        </div>
        @endforelse

        @if($sales->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
