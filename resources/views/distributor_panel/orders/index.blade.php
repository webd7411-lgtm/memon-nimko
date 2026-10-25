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
                    Purchase Demands
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Stock Demands to Company</h4>
            <p class="mb-0 text-white-50 small">Track your purchase orders and requisitions submitted to Head Office</p>
        </div>
        <div>
            <a href="{{ route('distributor.orders.create') }}" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold">
                <i class="fas fa-plus me-1"></i> + Create New Demand
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-check-circle"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Date</th>
                        <th class="text-center">Total Items</th>
                        <th class="text-center">Total Quantity</th>
                        <th class="text-end">Est. Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-dark">{{ $order->order_no }}</span>
                            @if($order->notes)
                            <div class="text-muted small text-truncate" style="max-width: 250px;">
                                <i class="fas fa-comment-dots text-muted me-1"></i> {{ $order->notes }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2.5 py-1">
                                {{ $order->total_items }} products
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark">{{ (float)$order->total_qty }} pcs</span>
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
                            <span class="badge bg-secondary text-white px-2.5 py-1">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('distributor.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-cart-plus fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Stock Demands Placed Yet</p>
                            <span class="small">Click "+ Create New Demand" to request products from Head Office.</span>
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
