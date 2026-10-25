@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Select2 Theme Styling */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 5px 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        color: #0f172a !important;
        font-weight: 500 !important;
        font-size: 0.88rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border: 1px solid #0f766e !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #0f766e !important;
    }
</style>

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

    <!-- Compact Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.distributors.sales') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="Back to Sales Register">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-truck-loading" style="color: #0f766e;"></i>
                    Direct Stock Issue / Sale to Distributor
                    <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.7rem; font-weight: 600;">Primary Sale</span>
                </h5>
            </div>
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.distributors.direct_sale.store') }}" method="POST" id="directSaleForm">
        @csrf
        <!-- Hidden Default Warehouse (user requested warehouse field removed) -->
        <input type="hidden" name="warehouse_id" value="{{ $warehouses->first()->id ?? 'shop' }}">

        <div class="row g-3 mb-4">
            <!-- Left Side: Products Table -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-2.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-boxes me-1" style="color: #0f766e;"></i> Products to Issue
                            </h6>
                            <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.72rem;">
                                <i class="fas fa-keyboard me-1 text-teal"></i> Press [Enter] on Rate to add next row
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background: #0f766e;" onclick="addRowAndFocus()">
                            <i class="fas fa-plus me-1"></i> Add Product
                        </button>
                    </div>

                    <div class="table-responsive" style="min-height: 250px;">
                        <table class="table align-middle mb-0" id="directSaleTable">
                            <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                                <tr>
                                    <th class="ps-3" style="width: 40%;">Product (Searchable)</th>
                                    <th class="text-center" style="width: 20%;">Variant / Size</th>
                                    <th class="text-center" style="width: 13%;">Issue Qty</th>
                                    <th class="text-end" style="width: 14%;">Rate (Rs)</th>
                                    <th class="text-end pe-3" style="width: 13%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" style="font-size: 0.88rem;">
                                <tr class="item-row">
                                    <td class="ps-3">
                                        <select name="product_id[]" class="form-select select2-prod" required>
                                            <option value="">-- Search & Choose Product --</option>
                                            @foreach($products as $prod)
                                            @php
                                                $prodWholesale = (float)($prod->wholesale_price ?? 0);
                                                $prodRetail = (float)($prod->price ?? 0);
                                                $effectivePrice = $prodWholesale > 0 ? $prodWholesale : $prodRetail;
                                            @endphp
                                            <option value="{{ $prod->id }}"
                                                    data-variants='@json($prod->variants)'
                                                    data-price="{{ $effectivePrice }}"
                                                    data-unit="{{ $prod->unit->name ?? 'Pc' }}">
                                                {{ $prod->item_name }} ({{ $prod->unit->name ?? 'Pc' }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <select name="variant_id[]" class="form-select form-select-sm variant-select" onchange="onVariantChange(this)">
                                            <option value="" data-price="0">Standard</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.01" min="0.01" name="quantity[]" class="form-control form-control-sm text-center fw-bold row-qty"
                                               placeholder="Qty" oninput="calculateRow(this)" required>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control form-control-sm text-end fw-bold row-price"
                                               placeholder="0.00" oninput="calculateRow(this)" required>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="fw-bold text-dark row-subtotal">Rs. 0.00</span>
                                    </td>
                                    <td class="text-center pe-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0" onclick="removeRow(this)" style="width: 28px; height: 28px; line-height: 26px;">
                                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot style="background: #f8fafc;">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-dark py-2.5">Total Invoice Value:</td>
                                    <td class="text-end pe-3 fw-bold text-success fs-6 py-2.5" id="grandTotalDisplay">Rs. 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Parameters -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-truck-loading me-2" style="color: #0f766e;"></i> Sale Parameters</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Select Target Distributor *</label>
                        <select name="distributor_id" id="distributorSelect" class="form-select select2-dist" onchange="onDistributorSelect(this)" required>
                            <option value="">-- Choose Distributor --</option>
                            @foreach($distributors as $d)
                            <option value="{{ $d->id }}" data-balance="{{ (float)$d->current_balance }}" data-phone="{{ $d->phone }}" data-code="{{ $d->code }}">
                                {{ $d->name }} ({{ $d->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Distributor Balance preview -->
                    <div class="p-2.5 rounded-3 mb-3 bg-light border">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Current Balance:</span>
                            <span class="fw-bold text-danger" id="distBalanceDisplay">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>Phone:</span>
                            <span class="fw-semibold text-dark" id="distPhoneDisplay">—</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Issue / Sale Date *</label>
                        <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Dispatch Notes / Remarks</label>
                        <textarea name="remarks" rows="2" class="form-control" placeholder="Optional notes, vehicle no, etc..."></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 rounded-pill fw-bold shadow-sm"
                            style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;"
                            onclick="return confirm('Confirm issue of stock to distributor? Stock will be deducted and debited to distributor ledger.');">
                        <i class="fas fa-check-circle me-1"></i> Issue & Dispatch Stock
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Row for JS -->
<template id="rowTemplate">
    <tr class="item-row">
        <td class="ps-3">
            <select name="product_id[]" class="form-select select2-prod" required>
                <option value="">-- Search & Choose Product --</option>
                @foreach($products as $prod)
                @php
                    $prodWholesale = (float)($prod->wholesale_price ?? 0);
                    $prodRetail = (float)($prod->price ?? 0);
                    $effectivePrice = $prodWholesale > 0 ? $prodWholesale : $prodRetail;
                @endphp
                <option value="{{ $prod->id }}"
                        data-variants='@json($prod->variants)'
                        data-price="{{ $effectivePrice }}"
                        data-unit="{{ $prod->unit->name ?? 'Pc' }}">
                    {{ $prod->item_name }} ({{ $prod->unit->name ?? 'Pc' }})
                </option>
                @endforeach
            </select>
        </td>
        <td class="text-center">
            <select name="variant_id[]" class="form-select form-select-sm variant-select" onchange="onVariantChange(this)">
                <option value="" data-price="0">Standard</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" step="0.01" min="0.01" name="quantity[]" class="form-control form-control-sm text-center fw-bold row-qty"
                   placeholder="Qty" oninput="calculateRow(this)" required>
        </td>
        <td class="text-end">
            <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control form-control-sm text-end fw-bold row-price"
                   placeholder="0.00" oninput="calculateRow(this)" required>
        </td>
        <td class="text-end pe-3">
            <span class="fw-bold text-dark row-subtotal">Rs. 0.00</span>
        </td>
        <td class="text-center pe-2">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0" onclick="removeRow(this)" style="width: 28px; height: 28px; line-height: 26px;">
                <i class="fas fa-times" style="font-size: 0.75rem;"></i>
            </button>
        </td>
    </tr>
</template>

<script>
// Initialize Select2 on target element
function initProductSelect2(element) {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $(element).select2({
            width: '100%',
            placeholder: '-- Search & Choose Product --',
            allowClear: true
        });
    }
}

// Handle product change and auto fetch rate
function onProductChange(select) {
    const row = select.closest('tr');
    const variantSelect = row.querySelector('.variant-select');
    const priceInput = row.querySelector('.row-price');
    const qtyInput = row.querySelector('.row-qty');
    const selectedOpt = select.options[select.selectedIndex];
    
    variantSelect.innerHTML = '';
    
    if (selectedOpt && selectedOpt.value) {
        let parentPrice = parseFloat(selectedOpt.dataset.price) || 0;
        let chosenPrice = parentPrice;

        if (selectedOpt.dataset.variants) {
            try {
                const variants = JSON.parse(selectedOpt.dataset.variants);
                if (variants && variants.length > 0) {
                    variants.forEach((v, idx) => {
                        const vWholesale = parseFloat(v.wholesale_price) || 0;
                        const vRetail = parseFloat(v.price) || 0;
                        const vEffective = vWholesale > 0 ? vWholesale : (vRetail > 0 ? vRetail : parentPrice);
                        const label = (v.size_label || v.variant_name || ('Variant ' + v.id)) + (vEffective > 0 ? ' (Rs ' + vEffective + ')' : '');

                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.innerText = label;
                        opt.dataset.price = vEffective;

                        // Default select first variant
                        if (idx === 0) {
                            opt.selected = true;
                            chosenPrice = vEffective;
                        }
                        variantSelect.appendChild(opt);
                    });
                } else {
                    variantSelect.innerHTML = '<option value="" data-price="' + parentPrice + '">Standard</option>';
                }
            } catch(e) {
                variantSelect.innerHTML = '<option value="" data-price="' + parentPrice + '">Standard</option>';
            }
        } else {
            variantSelect.innerHTML = '<option value="" data-price="' + parentPrice + '">Standard</option>';
        }

        // Set fetched rate immediately
        priceInput.value = chosenPrice.toFixed(2);

        // Auto focus Qty input
        setTimeout(() => {
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        }, 80);
    } else {
        variantSelect.innerHTML = '<option value="" data-price="0">Standard</option>';
        priceInput.value = '0.00';
    }
    calculateRow(select);
}

// When variant is changed, update rate
function onVariantChange(variantSelect) {
    const row = variantSelect.closest('tr');
    const priceInput = row.querySelector('.row-price');
    const selectedOpt = variantSelect.options[variantSelect.selectedIndex];
    if (selectedOpt && selectedOpt.dataset.price) {
        const p = parseFloat(selectedOpt.dataset.price) || 0;
        if (p > 0) {
            priceInput.value = p.toFixed(2);
        }
    }
    calculateRow(variantSelect);
}

function calculateRow(el) {
    const row = el.closest('tr');
    const qtyInput = row.querySelector('.row-qty');
    const priceInput = row.querySelector('.row-price');
    const subtotalEl = row.querySelector('.row-subtotal');

    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const sub = qty * price;
    subtotalEl.innerText = 'Rs. ' + sub.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    calcGrandTotal();
}

function calcGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const price = parseFloat(row.querySelector('.row-price').value) || 0;
        grand += (qty * price);
    });
    document.getElementById('grandTotalDisplay').innerText = 'Rs. ' + grand.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function addRow() {
    const tpl = document.getElementById('rowTemplate');
    const clone = tpl.content.cloneNode(true);
    const tbody = document.getElementById('itemsBody');
    tbody.appendChild(clone);
    const newRow = tbody.lastElementChild;
    const select = newRow.querySelector('.select2-prod');
    initProductSelect2(select);
    return newRow;
}

function addRowAndFocus() {
    const newRow = addRow();
    if (newRow && typeof $ !== 'undefined') {
        const select = $(newRow).find('.select2-prod');
        setTimeout(() => {
            select.select2('open');
        }, 100);
    }
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        calcGrandTotal();
    } else {
        alert('At least one product is required.');
    }
}

function onDistributorSelect(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const bal = parseFloat(opt.dataset.balance) || 0;
        document.getElementById('distBalanceDisplay').innerText = 'Rs. ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('distPhoneDisplay').innerText = opt.dataset.phone || '—';
    } else {
        document.getElementById('distBalanceDisplay').innerText = 'Rs. 0.00';
        document.getElementById('distPhoneDisplay').innerText = '—';
    }
}

// Keyboard Enter Key Navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const target = e.target;

        // In Qty -> Move to Rate
        if (target.classList.contains('row-qty')) {
            e.preventDefault();
            const row = target.closest('tr');
            const priceInput = row.querySelector('.row-price');
            if (priceInput) {
                priceInput.focus();
                priceInput.select();
            }
            return;
        }

        // In Rate -> Add new row and open product search!
        if (target.classList.contains('row-price')) {
            e.preventDefault();
            const row = target.closest('tr');
            const rows = document.querySelectorAll('.item-row');

            if (row === rows[rows.length - 1]) {
                addRowAndFocus();
            } else {
                const nextRow = row.nextElementSibling;
                if (nextRow && typeof $ !== 'undefined') {
                    $(nextRow).find('.select2-prod').select2('open');
                }
            }
            return;
        }
    }
});

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-prod').each(function() {
            initProductSelect2(this);
        });

        $(document).on('change', '.select2-prod', function() {
            onProductChange(this);
        });
    }
});
</script>
@endsection
