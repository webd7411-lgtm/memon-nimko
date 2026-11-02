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

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list" style="color: #0f766e;"></i>
                Distributor Demand Requests (Purchase Requisitions)
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Stock Indents</span>
            </h5>
            <span class="text-muted small">Review incoming stock requisitions from distributors, approve dispatches, or reject orders</span>
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
                            @elseif($order->status === 'rejected')
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger px-2.5 py-1">
                                ❌ Rejected
                            </span>
                            @if($order->admin_remarks)
                            <div class="text-muted small text-truncate" style="max-width: 140px; margin: 2px auto 0;" title="{{ $order->admin_remarks }}">
                                {{ $order->admin_remarks }}
                            </div>
                            @endif
                            @else
                            <span class="badge rounded-pill bg-secondary text-white px-2.5 py-1">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1 justify-content-end flex-wrap">
                                @if($order->status === 'pending')
                                <a href="{{ route('admin.distributors.orders.show', $order->id) }}"
                                   class="btn btn-sm rounded-pill px-2.5 text-white shadow-sm"
                                   style="background: #0f766e; border: 1px solid #14b8a6;"
                                   title="Review and dispatch items">
                                    <i class="fas fa-box-open me-1"></i> Dispatch
                                </a>
                                <a href="{{ route('admin.distributors.orders.edit', $order->id) }}"
                                   class="btn btn-sm rounded-pill px-2.5 btn-outline-warning shadow-sm fw-semibold"
                                   title="Edit order quantities or products">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-2 shadow-sm"
                                        title="Reject Order"
                                        onclick="openRejectModal({{ $order->id }}, '{{ $order->order_no }}', '{{ addslashes($order->distributor->name ?? 'Distributor') }}')">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </button>
                                @else
                                <a href="{{ route('admin.distributors.orders.show', $order->id) }}"
                                   class="btn btn-sm rounded-pill px-3 btn-outline-secondary">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                @if(in_array($order->status, ['rejected', 'pending']))
                                <a href="{{ route('admin.distributors.orders.edit', $order->id) }}"
                                   class="btn btn-sm rounded-pill px-2.5 btn-outline-warning shadow-sm fw-semibold"
                                   title="Edit order">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                @endif
                                @endif
                            </div>
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

<!-- Reject Order Modal -->
<div class="modal fade" id="rejectOrderModal" tabindex="-1" aria-labelledby="rejectOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="rejectOrderForm" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="rejectOrderModalLabel">
                        <i class="fas fa-times-circle"></i> Reject Demand Order
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3" style="background: #fef2f2; border: 1px dashed #fca5a5;">
                        <div class="text-muted small">Are you sure you want to reject this distributor demand order?</div>
                        <div class="fw-bold text-dark mt-1" id="rejectOrderNo"></div>
                        <div class="text-muted small" id="rejectDistributorName"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Rejection Reason / Remarks (Optional)</label>
                        <textarea name="admin_remarks" class="form-control" rows="3" placeholder="e.g. Out of stock, payment pending, quantity adjustment required..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-times-circle me-1"></i> Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(orderId, orderNo, distName) {
    const form = document.getElementById('rejectOrderForm');
    form.action = "{{ url('admin/distributors/orders') }}/" + orderId + "/reject";
    document.getElementById('rejectOrderNo').innerText = "Order: " + orderNo;
    document.getElementById('rejectDistributorName').innerText = "Distributor: " + distName;
    const modal = new bootstrap.Modal(document.getElementById('rejectOrderModal'));
    modal.show();
}
</script>
@endsection
