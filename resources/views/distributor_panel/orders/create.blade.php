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
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    New Requisition
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Demand Stock from Head Office</h4>
            <p class="mb-0 text-white-50 small">Specify the products and quantities you need dispatched to your custody</p>
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('distributor.orders.store') }}" method="POST" id="demandForm">
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
                        <!-- First Row Default -->
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
                    </tbody>
                </table>
            </div>

            <div class="card-body p-4 bg-light border-top">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Delivery Instructions / Remarks</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes for Super Admin / warehouse packing..."></textarea>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn text-white w-100 py-3 rounded-pill fw-bold shadow-sm"
                                style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;">
                            <i class="fas fa-paper-plane me-2"></i> Submit Demand to Company
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

<script>
function onProductChange(select) {
    const row = select.closest('tr');
    const variantSelect = row.querySelector('.variant-select');
    const selectedOpt = select.options[select.selectedIndex];
    
    variantSelect.innerHTML = '<option value="">Standard</option>';
    if (selectedOpt && selectedOpt.dataset.variants) {
        try {
            const variants = JSON.parse(selectedOpt.dataset.variants);
            if (variants && variants.length > 0) {
                variants.forEach(v => {
                    const label = v.size_label || v.variant_name || ('Size: ' + v.id);
                    const opt = document.createElement('option');
                    opt.value = v.id;
                    opt.innerText = label;
                    variantSelect.appendChild(opt);
                });
            }
        } catch(e) {}
    }
}

function addRow() {
    const tpl = document.getElementById('rowTemplate');
    const clone = tpl.content.cloneNode(true);
    document.getElementById('itemsBody').appendChild(clone);
}

function addRowAndFocus() {
    addRow();
    const rows = document.querySelectorAll('.item-row');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        const prodSelect = lastRow.querySelector('.product-select');
        if (prodSelect) {
            prodSelect.focus();
        }
    }
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('At least one item is required in the demand.');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const target = e.target;
        if (target.name && target.name.includes('requested_qty')) {
            e.preventDefault();
            const row = target.closest('tr');
            const rows = document.querySelectorAll('.item-row');
            if (row === rows[rows.length - 1]) {
                addRowAndFocus();
            } else {
                const nextRow = row.nextElementSibling;
                if (nextRow) {
                    const nextProd = nextRow.querySelector('.product-select');
                    if (nextProd) nextProd.focus();
                }
            }
        }
    }
});
</script>
@endsection
