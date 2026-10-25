@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.orders.index') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> My Orders
                </a>
                <span class="text-white-50">•</span>
                <span class="font-monospace text-white fw-bold">{{ $order->order_no }}</span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Demand Order Summary</h4>
            <p class="mb-0 text-white-50 small">Submitted on {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</p>
        </div>
        <div>
            @if($order->status === 'pending')
            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3 py-2 fs-6">
                ⏳ Waiting for Company Dispatch
            </span>
            @elseif($order->status === 'dispatched')
            <span class="badge rounded-pill bg-primary px-3 py-2 fs-6 shadow-sm">
                🚚 Stock Dispatched by Company
            </span>
            @elseif($order->status === 'delivered')
            <span class="badge rounded-pill bg-success px-3 py-2 fs-6 shadow-sm">
                ✅ Stock Delivered into Custody
            </span>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes me-2 text-teal" style="color: #0f766e;"></i> Items in this Demand</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                            <tr>
                                <th class="ps-3">Item Details</th>
                                <th class="text-center">Requested Qty</th>
                                <th class="text-center">Approved / Dispatched</th>
                                <th class="text-end">Unit Rate</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem;">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $item->product->item_name ?? 'Product' }}</div>
                                    @if($item->variant)
                                    <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.72rem;">
                                        {{ $item->variant->size_label ?: $item->variant->variant_name }}
                                    </span>
                                    @endif
                                    <span class="text-muted small">Unit: {{ $item->product->unit->name ?? 'Pc' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">{{ (float)$item->requested_qty }}</span>
                                </td>
                                <td class="text-center">
                                    @if($order->status === 'dispatched' || $order->status === 'delivered')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success px-2.5 py-1 fw-bold fs-6">
                                        {{ (float)$item->approved_qty }}
                                    </span>
                                    @else
                                    <span class="text-muted small">Pending dispatch</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    Rs. {{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="text-end pe-3 fw-bold text-dark">
                                    Rs. {{ number_format(($item->approved_qty > 0 ? ($item->approved_qty * $item->unit_price) : ($item->requested_qty * $item->unit_price)), 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #f8fafc;">
                            <tr>
                                <td colspan="4" class="text-end fw-bold text-dark py-3">Total Value:</td>
                                <td class="text-end pe-3 fw-bold text-success fs-6 py-3">
                                    Rs. {{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-2 text-teal" style="color: #0f766e;"></i> Order Information</h6>
                
                <div class="mb-3">
                    <span class="text-muted small text-uppercase">Order Notes</span>
                    <p class="mb-0 text-dark small">{{ $order->notes ?: 'None' }}</p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small text-uppercase">Head Office Remarks</span>
                    <p class="mb-0 text-dark small">{{ $order->admin_remarks ?: 'No remarks entered yet.' }}</p>
                </div>

                @if($order->approved_at)
                <div class="mb-3">
                    <span class="text-muted small text-uppercase">Dispatched Date</span>
                    <p class="mb-0 text-dark small fw-semibold">{{ \Carbon\Carbon::parse($order->approved_at)->format('d M, Y h:i A') }}</p>
                </div>
                @endif

                <div class="p-3 rounded-3 mt-auto bg-light border">
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Total Items:</span>
                        <span class="fw-bold text-dark">{{ $order->total_items }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>Status:</span>
                        <span class="fw-bold text-uppercase" style="color: #0f766e;">{{ $order->status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
