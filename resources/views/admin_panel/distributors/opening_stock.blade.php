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
        z-index: 9999 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
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

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.distributors.stocks') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="Back to Custody Stocks">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-boxes-stacked" style="color: #0f766e;"></i>
                    Distributor Opening Stock Entry
                    <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.7rem; font-weight: 600;">System Onboarding</span>
                </h5>
                <span class="text-muted small">Load on-ground initial physical inventory for distributors without deducting from company warehouse</span>
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

    <form action="{{ route('admin.distributors.opening_stock.store') }}" method="POST" id="openingStockForm">
        @csrf

        <div class="row g-3 mb-4">
            <!-- Left Side: Products Table -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-2.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-boxes me-1" style="color: #0f766e;"></i> Opening Stock Items
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
                        <table class="table align-middle mb-0" id="openingStockTable">
                            <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                                <tr>
                                    <th class="ps-3" style="width: 50%;">Product / Variant (Searchable)</th>
                                    <th class="text-center" style="width: 15%;">Opening Qty</th>
                                    <th class="text-end" style="width: 15%;">Rate (Rs)</th>
                                    <th class="text-end pe-3" style="width: 15%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" style="font-size: 0.88rem;">
                                <tr class="item-row">
                                    <td class="ps-3">
                                        <select name="product_id[]" class="form-select select2-prod" required>
                                            <option value="">-- Search & Choose Product / Variant --</option>
                                            @foreach($products as $prod)
                                                @php
                                                    $prodWholesale = (float)($prod->wholesale_price ?? 0);
                                                    $prodRetail = (float)($prod->price ?? 0);
                                                    $effectivePrice = $prodWholesale > 0 ? $prodWholesale : $prodRetail;
                                                    $unitName = $prod->unit->name ?? 'Pc';
                                                @endphp
                                                @if($prod->variants && $prod->variants->count() > 0)
                                                    @foreach($prod->variants as $v)
                                                        @php
                                                            $vWholesale = (float)($v->wholesale_price ?? 0);
                                                            $vRetail = (float)($v->retail_price ?? 0);
                                                            $vPrice = $vWholesale > 0 ? $vWholesale : ($vRetail > 0 ? $vRetail : $effectivePrice);
                                                            $vLabel = $v->size_label ?: $v->variant_name;
                                                        @endphp
                                                        <option value="{{ $prod->id }}_{{ $v->id }}"
                                                                data-price="{{ $vPrice }}"
                                                                data-unit="{{ $unitName }}">
                                                            {{ $prod->item_name }} — {{ $vLabel }} ({{ $unitName }})
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ $prod->id }}_0"
                                                            data-price="{{ $effectivePrice }}"
                                                            data-unit="{{ $unitName }}">
                                                        {{ $prod->item_name }} ({{ $unitName }})
                                                    </option>
                                                @endif
                                            @endforeach
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
                                    <td class="fw-bold text-dark ps-3 py-2.5">
                                        Total Lines: <span id="totalItemsDisplay" class="badge bg-secondary">1</span>
                                    </td>
                                    <td class="text-center fw-bold text-dark py-2.5">
                                        Qty: <span id="totalQtyDisplay">0.00</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark py-2.5">Total Value:</td>
                                    <td class="text-end pe-3 fw-bold text-success fs-6 py-2.5" id="grandTotalDisplay">Rs. 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Guidance Info Box -->
                <div class="alert alert-light border rounded-4 d-flex align-items-start gap-3 p-3">
                    <i class="fas fa-info-circle text-teal fs-4 mt-1" style="color: #0f766e;"></i>
                    <div class="small text-muted">
                        <strong class="text-dark">Warehouse Stock Safe:</strong> Yeh stock direct distributor ki custody (van / godown) mein register hota hai. Company ke Main Warehouse se koi stock minus nahi hoga, kyunke yeh maal pehle se distributor ke paas physically mojud hai.
                    </div>
                </div>
            </div>

            <!-- Right Side: Parameters -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2" style="color: #0f766e;"></i> Entry Details</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Target Distributor *</label>
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
                            <span>Current Ledger Balance:</span>
                            <span class="fw-bold text-danger" id="distBalanceDisplay">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>Phone:</span>
                            <span class="fw-semibold text-dark" id="distPhoneDisplay">—</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Opening Date *</label>
                        <input type="date" name="opening_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <!-- Ledger Impact Toggle -->
                    <div class="mb-3 p-3 rounded-3 border" style="background: #fdfdfd;">
                        <label class="form-label fw-bold small text-dark mb-2">
                            <i class="fas fa-wallet text-secondary me-1"></i> Khata / Ledger Impact:
                        </label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="affect_ledger" id="affect_ledger_0" value="0" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="affect_ledger_0">
                                📦 Stock Only (Recommended)
                            </label>
                            <div class="text-muted" style="font-size: 0.76rem;">
                                Sirf physical stock add hoga. Ledger mein koi udhaar add nahi hoga (agar opening balance pehle se daal diya hai).
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="affect_ledger" id="affect_ledger_1" value="1">
                            <label class="form-check-label small fw-semibold text-dark" for="affect_ledger_1">
                                💰 Stock + Ledger Debit
                            </label>
                            <div class="text-muted" style="font-size: 0.76rem;">
                                Is stock ki total raqam distributor ke ledger mein Debit (Receivable) charh jayegi.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Remarks / Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="e.g. Initial stock loaded on software onboarding"></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2.5 rounded-3 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0f766e, #0d9488);">
                        <i class="fas fa-save me-1"></i> Save Opening Stock
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Row for adding products -->
<template id="rowTemplate">
    <tr class="item-row">
        <td class="ps-3">
            <select name="product_id[]" class="form-select select2-prod" required>
                <option value="">-- Search & Choose Product / Variant --</option>
                @foreach($products as $prod)
                    @php
                        $prodWholesale = (float)($prod->wholesale_price ?? 0);
                        $prodRetail = (float)($prod->price ?? 0);
                        $effectivePrice = $prodWholesale > 0 ? $prodWholesale : $prodRetail;
                        $unitName = $prod->unit->name ?? 'Pc';
                    @endphp
                    @if($prod->variants && $prod->variants->count() > 0)
                        @foreach($prod->variants as $v)
                            @php
                                $vWholesale = (float)($v->wholesale_price ?? 0);
                                $vRetail = (float)($v->retail_price ?? 0);
                                $vPrice = $vWholesale > 0 ? $vWholesale : ($vRetail > 0 ? $vRetail : $effectivePrice);
                                $vLabel = $v->size_label ?: $v->variant_name;
                            @endphp
                            <option value="{{ $prod->id }}_{{ $v->id }}"
                                    data-price="{{ $vPrice }}"
                                    data-unit="{{ $unitName }}">
                                {{ $prod->item_name }} — {{ $vLabel }} ({{ $unitName }})
                            </option>
                        @endforeach
                    @else
                        <option value="{{ $prod->id }}_0"
                                data-price="{{ $effectivePrice }}"
                                data-unit="{{ $unitName }}">
                            {{ $prod->item_name }} ({{ $unitName }})
                        </option>
                    @endif
                @endforeach
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
    // Self-executing setup to ensure Select2 initializes immediately
    (function initOpeningStockModule() {
        function setup() {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                setTimeout(setup, 100);
                return;
            }

            $('.select2-dist').select2({
                width: '100%',
                placeholder: '-- Choose Distributor --'
            });

            initSelect2Prod();
            calculateGrandTotal();

            // Keyboard navigation: Press [Enter] on rate input to add next row
            $(document).off('keydown', '.row-price').on('keydown', '.row-price', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addRowAndFocus();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setup);
        } else {
            setup();
        }
    })();

    function initSelect2Prod() {
        $('.select2-prod').not('.select2-hidden-accessible').select2({
            width: '100%',
            placeholder: '-- Search & Choose Product / Variant --',
            dropdownParent: $('body')
        }).on('select2:select', function(e) {
            let row = $(this).closest('tr');
            let selected = $(this).find(':selected');
            let defaultPrice = parseFloat(selected.data('price')) || 0;

            row.find('.row-price').val(defaultPrice.toFixed(2));
            calculateRow(this);

            setTimeout(() => {
                row.find('.row-qty').focus();
            }, 50);
        });
    }

    function onDistributorSelect(select) {
        let selected = $(select).find(':selected');
        let balance = parseFloat(selected.data('balance')) || 0;
        let phone = selected.data('phone') || '—';

        $('#distBalanceDisplay').text('Rs. ' + balance.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#distPhoneDisplay').text(phone);
    }

    function calculateRow(element) {
        let row = $(element).closest('tr');
        let qty = parseFloat(row.find('.row-qty').val()) || 0;
        let price = parseFloat(row.find('.row-price').val()) || 0;
        let subtotal = qty * price;

        row.find('.row-subtotal').text('Rs. ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        let totalQty = 0;
        let count = 0;

        $('#itemsBody tr.item-row').each(function() {
            let qty = parseFloat($(this).find('.row-qty').val()) || 0;
            let price = parseFloat($(this).find('.row-price').val()) || 0;
            if (qty > 0) {
                count++;
                totalQty += qty;
                total += (qty * price);
            }
        });

        $('#totalItemsDisplay').text(count > 0 ? count : $('#itemsBody tr.item-row').length);
        $('#totalQtyDisplay').text(totalQty.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#grandTotalDisplay').text('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    function addRowAndFocus() {
        let template = document.getElementById('rowTemplate');
        let clone = template.content.cloneNode(true);
        $('#itemsBody').append(clone);
        initSelect2Prod();

        let lastRow = $('#itemsBody tr.item-row:last');
        setTimeout(() => {
            lastRow.find('.select2-prod').select2('open');
        }, 50);
    }

    function removeRow(btn) {
        if ($('#itemsBody tr.item-row').length > 1) {
            $(btn).closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert('At least one row is required.');
        }
    }
</script>
@endsection
