@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.orders.index') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24;">
                    Edit Demand #{{ $order->order_no }}
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Edit Stock Demand to Head Office</h4>
            <p class="mb-0 text-white-50 small">Modify products or quantities for this pending order</p>
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('distributor.orders.update', $order->id) }}" method="POST" id="demandForm">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-clipboard-check me-2 text-teal" style="color: #0f766e;"></i> Requested Products List
                </h6>
                <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" onclick="addRow()">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                        <tr>
                            <th class="ps-3" style="width: 45%;">Product</th>
                            <th class="text-center" style="width: 20%;">Variant / Size</th>
                            <th class="text-center" style="width: 20%;">Requested Qty</th>
                            <th class="text-center pe-3" style="width: 15%;">Remove</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody" style="font-size: 0.88rem;">
                        @foreach($order->items as $item)
                        <tr class="item-row">
                            <td class="ps-3">
                                <select name="product_id[]" class="form-select product-select" onchange="onProductChange(this)" required>
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ $item->product_id == $prod->id ? 'selected' : '' }} data-variants='@json($prod->variants)' data-unit="{{ $prod->unit->name ?? 'Pc' }}">
                                        {{ $prod->item_name }} ({{ $prod->unit->name ?? 'Pc' }})
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <select name="variant_id[]" class="form-select variant-select">
                                    <option value="">Standard</option>
                                    @if($item->product && $item->product->variants)
                                        @foreach($item->product->variants as $v)
                                        <option value="{{ $v->id }}" {{ $item->variant_id == $v->id ? 'selected' : '' }}>
                                            {{ $v->size_label ?: $v->variant_name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td class="text-center">
                                <input type="number" step="0.01" min="0.01" name="requested_qty[]" value="{{ (float)$item->requested_qty }}" class="form-control text-center fw-bold" placeholder="Qty" required>
                            </td>
                            <td class="text-center pe-3">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeRow(this)" style="width: 32px; height: 32px;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-body p-4 bg-light border-top">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Delivery Instructions / Remarks</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes for Super Admin / warehouse packing...">{{ $order->notes }}</textarea>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end gap-2">
                        <a href="{{ route('distributor.orders.index') }}" class="btn btn-outline-secondary rounded-pill py-2.5 px-3">
                            Cancel
                        </a>
                        <button type="submit" class="btn text-white flex-fill py-2.5 rounded-pill fw-bold shadow-sm"
                                style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;">
                            <i class="fas fa-check-circle me-1"></i> Update Demand
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Row for JS -->
<template id="rowTemplate">
    <tr class="item-row">
        <td class="ps-3">
            <select name="product_id[]" class="form-select product-select" onchange="onProductChange(this)" required>
                <option value="">-- Choose Product --</option>
                @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-variants='@json($prod->variants)' data-unit="{{ $prod->unit->name ?? 'Pc' }}">
                    {{ $prod->item_name }} ({{ $prod->unit->name ?? 'Pc' }})
                </option>
                @endforeach
            </select>
        </td>
        <td class="text-center">
            <select name="variant_id[]" class="form-select variant-select">
                <option value="">Standard</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" step="0.01" min="0.01" name="requested_qty[]" class="form-control text-center fw-bold" placeholder="Qty" required>
        </td>
        <td class="text-center pe-3">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeRow(this)" style="width: 32px; height: 32px;">
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
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#itemsBody .item-row');
        if (rows.length <= 1) {
            alert('At least one item is required in the demand order.');
            return;
        }
        btn.closest('tr').remove();
    }

    function onProductChange(select) {
        const row = select.closest('tr');
        const variantSelect = row.querySelector('.variant-select');
        const opt = select.selectedOptions[0];

        variantSelect.innerHTML = '<option value="">Standard</option>';

        if (opt && opt.dataset.variants) {
            try {
                const variants = JSON.parse(opt.dataset.variants);
                if (variants && variants.length > 0) {
                    variants.forEach(v => {
                        const label = v.size_label || v.variant_name || ('Variant #' + v.id);
                        variantSelect.innerHTML += `<option value="${v.id}">${label}</option>`;
                    });
                }
            } catch (e) {
                console.error(e);
            }
        }
    }
</script>
@endpush
@endsection
