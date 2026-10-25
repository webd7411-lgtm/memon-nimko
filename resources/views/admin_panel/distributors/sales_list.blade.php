@extends('admin_panel.layout.app')

@section('content')
<style>
    .dist-module-container {
        padding-left: 18px !important;
        padding-right: 18px !important;
        padding-top: 10px !important;
        padding-bottom: 30px !important;
    }
    @media (min-width: 992px) {
        .dist-module-container {
            padding-left: 28px !important;
            padding-right: 28px !important;
            padding-top: 16px !important;
        }
    }
    .dist-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 14px 18px !important;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 mt-1">
        <div>
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                <i class="fas fa-file-invoice-dollar text-teal" style="color: #0f766e;"></i>
                Distributor Sales Register (Issued Invoices)
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.68rem; padding: 3px 8px;">Primary</span>
            </h5>
            <span class="text-muted" style="font-size: 0.78rem;">Record of all stock dispatches and billing issued directly to distributors</span>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Sales</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.4rem;">Rs. {{ number_format($totalSalesAmount, 2) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 1.15rem;">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #3b82f6 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Quantity Issued</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.4rem;">{{ number_format($totalItemsDispatched, 0) }} <span class="fs-6 fw-normal text-muted">pcs</span></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 1.15rem;">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #10b981 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Invoices Issued</span>
                    <h3 class="fw-bold mb-0 text-success mt-1" style="font-size: 1.4rem;">{{ $sales->total() }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.15rem;">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-2.5">
            <form method="GET" action="{{ route('admin.distributors.sales') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search invoice # or distributor...">
                </div>
                <div class="col-12 col-md-3">
                    <select name="distributor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}" {{ request('distributor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm text-white rounded-pill flex-fill" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'distributor_id', 'start_date', 'end_date']))
                    <a href="{{ route('admin.distributors.sales') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- DESKTOP TABLE VIEW (Visible on Tablets & PCs)  -->
    <!-- ============================================== -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-none d-md-block bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-3">Invoice #</th>
                        <th>Distributor</th>
                        <th>Date</th>
                        <th class="text-center">Items & Qty</th>
                        <th class="text-end">Invoice Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($sales as $sale)
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold font-monospace text-dark">{{ $sale->order_no }}</span>
                            @if($sale->admin_remarks)
                            <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $sale->admin_remarks }}">
                                <i class="fas fa-comment-alt text-muted me-1"></i> {{ $sale->admin_remarks }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sale->distributor->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $sale->distributor->code ?? '' }} • {{ $sale->distributor->phone ?? '' }}</span>
                        </td>
                        <td>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($sale->order_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                {{ $sale->total_items ?? 0 }} products • {{ (float)($sale->total_qty ?? 0) }} pcs
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            Rs. {{ number_format($sale->total_amount ?? 0, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success px-2.5 py-1">
                                <i class="fas fa-check-circle me-1"></i> Dispatched / Issued
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.distributors.sales.invoice', $sale->id) }}" class="btn btn-outline-secondary rounded-pill px-2.5 py-1" title="Print Invoice">
                                    <i class="fas fa-print me-1"></i> Print
                                </a>
                                <a href="{{ route('admin.distributors.orders.show', $sale->id) }}" class="btn btn-outline-secondary rounded-pill px-2.5 py-1 ms-1" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.distributors.ledger', $sale->distributor_id) }}" class="btn btn-outline-secondary rounded-pill px-2.5 py-1 ms-1" title="View Ledger">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Distributor Sales Invoices Found</p>
                            <span class="small">Click "+ New Distributor Sale" to issue goods to a distributor.</span>
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

    <!-- ============================================== -->
    <!-- MOBILE CARD VIEW (Visible on Mobile Screens)   -->
    <!-- ============================================== -->
    <div class="d-block d-md-none mb-4">
        @forelse($sales as $sale)
        <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white p-3" style="border: 1px solid #edf2f7 !important;">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                <div>
                    <span class="fw-bold font-monospace text-dark d-block" style="font-size: 0.95rem;">{{ $sale->order_no }}</span>
                    <span class="fw-semibold text-dark" style="font-size: 0.88rem;">{{ $sale->distributor->name ?? 'Distributor' }}</span>
                    <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $sale->distributor->code ?? '' }} • {{ \Carbon\Carbon::parse($sale->order_date)->format('d M, Y') }}</span>
                </div>
                <span class="badge rounded-pill px-2.5 py-1 text-success fw-semibold" style="background: #e8f8f0; font-size: 0.75rem;">
                    <i class="fas fa-check-circle me-1" style="font-size: 7px;"></i> Dispatched
                </span>
            </div>

            <div class="row g-2 mb-2.5">
                <div class="col-6">
                    <div class="p-2 rounded-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Items Dispatched</span>
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $sale->total_items ?? 0 }} items ({{ (float)($sale->total_qty ?? 0) }} pcs)</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Invoice Value</span>
                        <span class="fw-bold text-success" style="font-size: 0.85rem;">Rs. {{ number_format($sale->total_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between gap-2 pt-1 border-top">
                <a href="{{ route('admin.distributors.sales.invoice', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 flex-grow-1 text-center">
                    <i class="fas fa-print me-1"></i> Print
                </a>
                <a href="{{ route('admin.distributors.orders.show', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 flex-grow-1 text-center">
                    <i class="fas fa-eye me-1"></i> Details
                </a>
                <a href="{{ route('admin.distributors.ledger', $sale->distributor_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 flex-grow-1 text-center">
                    <i class="fas fa-file-invoice me-1"></i> Ledger
                </a>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted bg-white">
            <i class="fas fa-file-invoice-dollar fa-3x mb-2 text-secondary opacity-50"></i>
            <p class="mb-0 fw-semibold">No Distributor Sales Invoices Found</p>
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
