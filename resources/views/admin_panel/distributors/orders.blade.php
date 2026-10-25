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
                <i class="fas fa-clipboard-list" style="color: #0f766e;"></i>
                Distributor Demand Requests (Purchase Requisitions)
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Stock Indents</span>
            </h5>
            <span class="text-muted small">Review incoming stock requisitions from distributors, approve dispatches, and fulfill orders</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.orders') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending ({{ $pendingCount }})</option>
                        <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>🚚 Dispatched</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
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
                    @if(request()->hasAny(['status', 'distributor_id', 'start_date', 'end_date']))
                    <a href="{{ route('admin.distributors.orders') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Distributor</th>
                        <th>Date</th>
                        <th class="text-center">Items & Total Qty</th>
                        <th class="text-end">Estimated Value</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark font-monospace">{{ $order->order_no }}</span>
                            @if($order->notes)
                            <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $order->notes }}">
                                <i class="fas fa-comment-dots text-muted me-1"></i> {{ $order->notes }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->distributor->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $order->distributor->code ?? '' }} • {{ $order->distributor->phone ?? '' }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                {{ $order->total_items }} products • {{ (float)$order->total_qty }} pcs
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">Rs. {{ number_format($order->total_amount, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @if($order->status === 'pending')
                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-2.5 py-1">
                                ⏳ Pending Approval
                            </span>
                            @elseif($order->status === 'dispatched')
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary px-2.5 py-1">
                                🚚 Dispatched
                            </span>
                            @elseif($order->status === 'delivered')
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success px-2.5 py-1">
                                ✅ Delivered
                            </span>
                            @else
                            <span class="badge rounded-pill bg-secondary text-white px-2.5 py-1">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.distributors.orders.show', $order->id) }}"
                               class="btn btn-sm rounded-pill px-3 {{ $order->status === 'pending' ? 'text-white' : 'btn-outline-secondary' }}"
                               style="{{ $order->status === 'pending' ? 'background: #0f766e; border: 1px solid #14b8a6;' : '' }}">
                                <i class="fas {{ $order->status === 'pending' ? 'fa-box-open' : 'fa-eye' }} me-1"></i>
                                {{ $order->status === 'pending' ? 'Review & Dispatch' : 'View Details' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-check fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Demand Orders Found</p>
                            <span class="small">When distributors request stock from their portal, orders will appear here for your approval.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
