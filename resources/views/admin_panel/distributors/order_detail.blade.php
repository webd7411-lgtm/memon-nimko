@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2">
    <!-- Top Action Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-check" style="color: #0f766e;"></i>
                Demand Review: <span class="font-monospace text-dark">{{ $order->order_no }}</span>
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">Order Fulfillment</span>
            </h5>
            <span class="text-muted small">Distributor: {{ $order->distributor->name }} ({{ $order->distributor->code }}) • Created: {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.distributors.orders') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5">
                <i class="fas fa-arrow-left me-1"></i> Back to Demands
            </a>
            @if($order->status === 'dispatched')
            <span class="badge bg-success-subtle text-success border border-success px-3 py-1.5 rounded-pill fw-bold">
                <i class="fas fa-check-circle me-1"></i> Dispatched
            </span>
            @endif
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.distributors.orders.dispatch', $order->id) }}" method="POST" id="dispatchOrderForm">
        @csrf
        <div class="row g-4 mb-4">
            <!-- Left Column: Item Breakdown -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-boxes me-2 text-teal" style="color: #0f766e;"></i> Requested Stock Items
                        </h6>
                        <span class="badge bg-light text-dark border px-2.5 py-1">
                            {{ $order->items->count() }} Products
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                                <tr>
                                    <th class="ps-3">Item Details</th>
                                    <th class="text-center">Avail. Stock</th>
                                    <th class="text-center">Requested</th>
                                    <th class="text-center" style="width: 130px;">Approved Qty</th>
                                    <th class="text-end" style="width: 120px;">Unit Price (Rs)</th>
                                    <th class="text-end pe-3" style="width: 120px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.88rem;">
                                @foreach($order->items as $item)
                                @php
                                    $availStock = $stockMap[$item->id] ?? 0;
                                    $isDispatched = in_array($order->status, ['dispatched', 'delivered']);
                                    $defaultApproved = $isDispatched ? (float)$item->approved_qty : (float)$item->requested_qty;
                                    $defaultPrice = (float)($item->unit_price > 0 ? $item->unit_price : ($item->product->wholesale_price ?? $item->product->price ?? 0));
                                @endphp
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
                                        <span class="badge rounded-pill {{ $availStock > 0 ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }} px-2.5 py-1">
                                            {{ $availStock }} in stock
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark fs-6">{{ (float)$item->requested_qty }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(!$isDispatched)
                                        <input type="number" step="0.01" min="0" max="{{ max($availStock, (float)$item->requested_qty) }}"
                                               name="approved_qty[{{ $item->id }}]"
                                               value="{{ $defaultApproved }}"
                                               class="form-control form-control-sm text-center fw-bold row-qty"
                                               data-id="{{ $item->id }}"
                                               oninput="calcSubtotal({{ $item->id }})" required>
                                        @else
                                        <span class="fw-bold text-success fs-6">{{ (float)$item->approved_qty }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$isDispatched)
                                        <input type="number" step="0.01" min="0"
                                               name="unit_price[{{ $item->id }}]"
                                               value="{{ $defaultPrice }}"
                                               class="form-control form-control-sm text-end fw-bold row-price"
                                               data-id="{{ $item->id }}"
                                               oninput="calcSubtotal({{ $item->id }})" required>
                                        @else
                                        <span>Rs. {{ number_format($item->unit_price, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="fw-bold text-dark row-subtotal" id="subtotal_{{ $item->id }}">
                                            Rs. {{ number_format(($defaultApproved * $defaultPrice), 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f8fafc;">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold text-dark py-3">Total Dispatched Order Value:</td>
                                    <td class="text-end pe-3 fw-bold text-success fs-6 py-3" id="grandTotalDisplay">
                                        Rs. {{ number_format($order->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Dispatch Action & Metadata -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-truck me-2 text-teal" style="color: #0f766e;"></i> Dispatch Parameters
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Dispatching Warehouse *</label>
                            @if(!in_array($order->status, ['dispatched', 'delivered']))
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} (Warehouse)</option>
                                @endforeach
                                <option value="shop">🏢 Main Shop / Counter Stock</option>
                            </select>
                            @else
                            <div class="form-control bg-light">
                                {{ $order->warehouse->name ?? 'Main Shop Stock' }}
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Order Notes from Booker</label>
                            <div class="p-2.5 rounded-3 bg-light text-dark small" style="min-height: 48px;">
                                {{ $order->notes ?: 'No special instructions provided by distributor.' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Head Office Dispatch Remarks</label>
                            @if(!in_array($order->status, ['dispatched', 'delivered']))
                            <textarea name="admin_remarks" rows="3" class="form-control" placeholder="Optional dispatch notes / vehicle / driver details...">{{ $order->admin_remarks }}</textarea>
                            @else
                            <div class="p-2.5 rounded-3 bg-light text-dark small">
                                {{ $order->admin_remarks ?: 'None' }}
                            </div>
                            @endif
                        </div>

                        <!-- Distributor Current Financials -->
                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Distributor Code:</span>
                                <span class="fw-bold text-dark">{{ $order->distributor->code }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Contact Number:</span>
                                <span class="fw-bold text-dark">{{ $order->distributor->phone }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Current Outstanding Balance:</span>
                                <span class="fw-bold text-danger">Rs. {{ number_format($order->distributor->current_balance, 2) }}</span>
                            </div>
                        </div>

                        @if(!in_array($order->status, ['dispatched', 'delivered']))
                        @can('Dispatch Distributor Order')
                        <button type="submit" class="btn text-white w-100 py-2.5 rounded-pill shadow-sm fw-bold"
                                style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;"
                                onclick="return confirm('Confirm dispatch of this stock to distributor? Stock will be deducted from warehouse and added to distributor custody.');">
                            <i class="fas fa-check-circle me-1"></i> Approve & Dispatch Stock
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary w-100 py-2.5 rounded-pill shadow-sm fw-bold disabled" disabled>
                            <i class="fas fa-ban me-1"></i> Dispatch Not Permitted
                        </button>
                        @endcan
                        @else
                        <div class="text-center text-muted small py-2">
                            <i class="fas fa-lock me-1"></i> Order Dispatched on {{ \Carbon\Carbon::parse($order->approved_at)->format('d M, Y h:i A') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function calcSubtotal(itemId) {
    const qtyInput = document.querySelector(`input[name="approved_qty[${itemId}]"]`);
    const priceInput = document.querySelector(`input[name="unit_price[${itemId}]"]`);
    const subtotalEl = document.getElementById(`subtotal_${itemId}`);

    if (qtyInput && priceInput && subtotalEl) {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const sub = qty * price;
        subtotalEl.innerText = 'Rs. ' + sub.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    calcGrandTotal();
}

function calcGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.row-qty').forEach(qtyInput => {
        const id = qtyInput.getAttribute('data-id');
        const priceInput = document.querySelector(`.row-price[data-id="${id}"]`);
        if (priceInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            grand += (qty * price);
        }
    });
    const gtEl = document.getElementById('grandTotalDisplay');
    if (gtEl) {
        gtEl.innerText = 'Rs. ' + grand.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}
</script>
@endsection
