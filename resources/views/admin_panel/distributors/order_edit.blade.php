@extends('admin_panel.layout.app')

@section('content')
<style>
    .dist-module-container {
        padding: 1rem;
    }
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .btn-teal {
        background: #0f766e;
        color: #ffffff;
        border: 1px solid #14b8a6;
    }
    .btn-teal:hover {
        background: #0d9488;
        color: #ffffff;
    }
    .remove-row-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

<div class="dist-module-container">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.distributors.orders') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24;">
                    Edit Demand #{{ $order->order_no }}
                </span>
                <span class="badge rounded-pill px-2.5 py-1 text-uppercase" style="background: rgba(255,255,255,0.15); font-size: 0.7rem;">
                    Status: {{ ucfirst($order->status) }}
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Edit Distributor Demand Order</h4>
            <p class="mb-0 text-white-50 small">Modify products, quantities, prices, or remarks for this order</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.distributors.orders.show', $order->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fas fa-eye me-1"></i> View Order Details
            </a>
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.distributors.orders.update', $order->id) }}" method="POST" id="editDemandForm">
        @csrf

        <!-- Order Meta Card -->
        <div class="card card-custom mb-4">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Distributor</label>
                        <select name="distributor_id" class="form-select form-select-sm" required>
                            @foreach($distributors as $d)
                            <option value="{{ $d->id }}" {{ $order->distributor_id == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->code }}) - {{ $d->city ?? 'No City' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Order Date</label>
                        <input type="date" name="order_date" class="form-control form-control-sm" value="{{ $order->order_date ? date('Y-m-d', strtotime($order->order_date)) : date('Y-m-d') }}">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Order Number</label>
                        <input type="text" class="form-control form-control-sm bg-light font-monospace fw-bold" value="{{ $order->order_no }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="card card-custom mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-boxes me-2 text-teal" style="color: #0f766e;"></i> Requested Products List
                </h6>
                <button type="button" class="btn btn-sm btn-teal rounded-pill px-3 shadow-sm" onclick="addRow()">
                    <i class="fas fa-plus me-1"></i> Add Product
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th style="min-width: 280px;">Product Name</th>
                            <th style="width: 140px;">Unit</th>
                            <th style="width: 150px;">Requested Qty</th>
                            <th style="width: 150px;">Rate / Price (₨)</th>
                            <th style="width: 160px;" class="text-end">Subtotal (₨)</th>
                            <th style="width: 60px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody" style="font-size: 0.9rem;">
                        @foreach($order->items as $index => $item)
                        @php
                            $pPrice = (float)($item->unit_price ?: $item->product->wholesale_price ?: $item->product->price ?: 0);
                            $lineSubtotal = (float)$item->requested_qty * $pPrice;
                        @endphp
                        <tr class="item-row">
                            <td class="text-center row-idx text-muted small fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <select name="product_id[]" class="form-select form-select-sm product-select" required onchange="productChanged(this)">
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}"
                                            data-unit="{{ $p->unit->name ?? 'Units' }}"
                                            data-price="{{ $p->wholesale_price ?? $p->price ?? 0 }}"
                                            data-variants="{{ json_encode($p->variants) }}"
                                            {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->item_name }} ({{ $p->item_code }})
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="variant_id[]" class="variant-id" value="{{ $item->variant_id }}">
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border unit-badge px-2 py-1">
                                    {{ $item->product->unit->name ?? 'Units' }}
                                </span>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="requested_qty[]"
                                       class="form-control form-control-sm qty-input text-end fw-bold"
                                       value="{{ (float)$item->requested_qty }}" required oninput="calcSubtotal(this)">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="unit_price[]"
                                       class="form-control form-control-sm price-input text-end"
                                       value="{{ number_format($pPrice, 2, '.', '') }}" oninput="calcSubtotal(this)">
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark subtotal-cell">₨ {{ number_format($lineSubtotal, 2) }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn" onclick="removeRow(this)" title="Remove item">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background: #f8fafc;">
                        <tr>
                            <td colspan="3" class="text-end fw-bold text-muted small text-uppercase">Total Quantity:</td>
                            <td class="text-end fw-bold text-dark" id="displayTotalQty">{{ number_format($order->total_qty, 2) }}</td>
                            <td class="text-end fw-bold text-muted small text-uppercase">Grand Total:</td>
                            <td class="text-end fw-bold text-dark fs-6" id="displayTotalAmount" style="color: #0f766e !important;">₨ {{ number_format($order->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Notes / Remarks Card -->
        <div class="card card-custom mb-4">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Distributor Order Notes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Notes entered by distributor...">{{ $order->notes }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Admin / Fulfillment Remarks</label>
                        <textarea name="admin_remarks" class="form-control form-control-sm" rows="3" placeholder="Remarks or instructions for dispatch...">{{ $order->admin_remarks }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="{{ route('admin.distributors.orders') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-teal rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-save me-1"></i> Save Changes to Order
            </button>
        </div>

    </form>
</div>

<!-- Hidden product template row -->
<template id="rowTemplate">
    <tr class="item-row">
        <td class="text-center row-idx text-muted small fw-bold"></td>
        <td>
            <select name="product_id[]" class="form-select form-select-sm product-select" required onchange="productChanged(this)">
                <option value="">— Select Product —</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}"
                        data-unit="{{ $p->unit->name ?? 'Units' }}"
                        data-price="{{ $p->wholesale_price ?? $p->price ?? 0 }}"
                        data-variants="{{ json_encode($p->variants) }}">
                    {{ $p->item_name }} ({{ $p->item_code }})
                </option>
                @endforeach
            </select>
            <input type="hidden" name="variant_id[]" class="variant-id" value="">
        </td>
        <td>
            <span class="badge bg-light text-secondary border unit-badge px-2 py-1">—</span>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="requested_qty[]"
                   class="form-control form-control-sm qty-input text-end fw-bold"
                   value="1" required oninput="calcSubtotal(this)">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="unit_price[]"
                   class="form-control form-control-sm price-input text-end"
                   value="0.00" oninput="calcSubtotal(this)">
        </td>
        <td class="text-end">
            <span class="fw-bold text-dark subtotal-cell">₨ 0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn" onclick="removeRow(this)" title="Remove item">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    function addRow() {
        const tpl = document.getElementById('rowTemplate');
        const clone = tpl.content.cloneNode(true);
        document.getElementById('itemsBody').appendChild(clone);
        reindexRows();
        recalcTotals();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#itemsBody tr.item-row');
        if (rows.length <= 1) {
            alert('At least one product item is required in the order.');
            return;
        }
        btn.closest('tr').remove();
        reindexRows();
        recalcTotals();
    }

    function reindexRows() {
        document.querySelectorAll('#itemsBody tr.item-row').forEach((row, i) => {
            const idxCell = row.querySelector('.row-idx');
            if (idxCell) idxCell.textContent = (i + 1);
        });
    }

    function productChanged(select) {
        const opt = select.options[select.selectedIndex];
        const row = select.closest('tr');
        if (!opt || !opt.value) {
            row.querySelector('.unit-badge').textContent = '—';
            row.querySelector('.price-input').value = '0.00';
            row.querySelector('.variant-id').value = '';
            calcSubtotal(row.querySelector('.qty-input'));
            return;
        }

        const unit = opt.getAttribute('data-unit') || 'Units';
        const price = parseFloat(opt.getAttribute('data-price') || 0);

        row.querySelector('.unit-badge').textContent = unit;
        row.querySelector('.price-input').value = price.toFixed(2);

        calcSubtotal(row.querySelector('.qty-input'));
    }

    function calcSubtotal(elem) {
        const row = elem.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.subtotal-cell').textContent = '₨ ' + subtotal.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        recalcTotals();
    }

    function recalcTotals() {
        let totalQty = 0;
        let totalAmount = 0;

        document.querySelectorAll('#itemsBody tr.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            totalQty += qty;
            totalAmount += (qty * price);
        });

        document.getElementById('displayTotalQty').textContent = totalQty.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById('displayTotalAmount').textContent = '₨ ' + totalAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        reindexRows();
        recalcTotals();
    });
</script>
@endpush
@endsection
