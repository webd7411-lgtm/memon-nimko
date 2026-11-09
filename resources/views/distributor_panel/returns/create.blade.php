@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #b91c1c 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.returns.index') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Returns
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5;">
                    Return #{{ $returnNo }}
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Process Customer Sale Return</h4>
            <p class="mb-0 text-white-50 small">Take back products from retailer, update custody stock and adjust customer ledger balance</p>
        </div>
    </div>

    <!-- Error/Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('distributor.returns.store') }}" method="POST" id="returnForm">
        @csrf
        <div class="row g-4 mb-4">
            <!-- Left: Items Selection -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-boxes me-2 text-danger"></i> Returned Products List
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="addRow()">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="returnItemsTable">
                            <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                                <tr>
                                    <th class="ps-3" style="width: 38%;">Product</th>
                                    <th style="width: 18%;">Variant / Size</th>
                                    <th class="text-center" style="width: 14%;">Return Qty</th>
                                    <th class="text-end" style="width: 15%;">Unit Rate (Rs.)</th>
                                    <th class="text-end" style="width: 15%;">Subtotal (Rs.)</th>
                                    <th class="text-center pe-3" style="width: 10%;">Condition</th>
                                    <th class="text-center" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsBody" style="font-size: 0.88rem;">
                                @if($selectedSale && $selectedSale->items->count() > 0)
                                    @foreach($selectedSale->items as $sItem)
                                    <tr class="return-row">
                                        <td class="ps-3">
                                            <input type="hidden" name="sale_item_id[]" value="{{ $sItem->id }}">
                                            <input type="hidden" name="product_id[]" value="{{ $sItem->product_id }}">
                                            <div class="fw-bold text-dark">{{ $sItem->product->item_name ?? 'Item' }}</div>
                                            <small class="text-muted">Sold Qty: {{ (float)$sItem->qty }}</small>
                                        </td>
                                        <td>
                                            <input type="hidden" name="variant_id[]" value="{{ $sItem->variant_id }}">
                                            <span class="badge bg-light text-secondary border">
                                                {{ $sItem->variant ? ($sItem->variant->size_label ?: $sItem->variant->variant_name) : 'Standard' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" min="0" max="{{ (float)$sItem->qty }}" name="return_qty[]" value="0"
                                                   class="form-control form-control-sm text-center fw-bold row-qty" oninput="calculateTotals()">
                                        </td>
                                        <td class="text-end">
                                            <input type="number" step="0.01" name="unit_price[]" value="{{ (float)$sItem->unit_price }}"
                                                   class="form-control form-control-sm text-end row-price" oninput="calculateTotals()">
                                        </td>
                                        <td class="text-end fw-bold text-danger row-subtotal">
                                            0.00
                                        </td>
                                        <td class="text-center pe-3">
                                            <select name="item_condition[]" class="form-select form-select-sm">
                                                <option value="restockable">Good (Restock)</option>
                                                <option value="damaged">Damaged / Expired</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="return-row">
                                        <td class="ps-3">
                                            <select name="product_id[]" class="form-select form-select-sm product-select" onchange="onProductSelect(this)" required>
                                                <option value="">-- Choose Product --</option>
                                                @foreach($stocks as $st)
                                                <option value="{{ $st->product_id }}" data-price="{{ $st->avg_price ?: ($st->product->wholesale_price ?? 0) }}" data-variant-id="{{ $st->variant_id }}" data-variant-name="{{ $st->variant ? ($st->variant->size_label ?: $st->variant->variant_name) : 'Standard' }}">
                                                    {{ $st->product->item_name ?? 'Product' }} @if($st->variant) ({{ $st->variant->size_label ?: $st->variant->variant_name }}) @endif
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
                                            <span class="badge bg-light text-secondary border row-variant-badge">Standard</span>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" min="0" name="return_qty[]" value="1"
                                                   class="form-control form-control-sm text-center fw-bold row-qty" oninput="calculateTotals()" required>
                                        </td>
                                        <td class="text-end">
                                            <input type="number" step="0.01" name="unit_price[]" value="0.00"
                                                   class="form-control form-control-sm text-end row-price" oninput="calculateTotals()" required>
                                        </td>
                                        <td class="text-end fw-bold text-danger row-subtotal">
                                            0.00
                                        </td>
                                        <td class="text-center pe-3">
                                            <select name="item_condition[]" class="form-select form-select-sm">
                                                <option value="restockable">Good (Restock)</option>
                                                <option value="damaged">Damaged / Expired</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot style="background: #f8fafc;">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-dark py-2">Total Return Value:</td>
                                    <td class="text-end fw-bold text-danger fs-6 py-2" id="displayTotal">Rs. 0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Customer & Settlement Info -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-store me-2 text-danger"></i> Customer & Settlement
                    </h6>

                    @if($selectedSale)
                    <input type="hidden" name="distributor_sale_id" value="{{ $selectedSale->id }}">
                    <div class="p-3 rounded-3 mb-3" style="background: #fef2f2; border: 1px solid #fee2e2;">
                        <span class="small text-danger fw-bold text-uppercase">Sale Invoice Link</span>
                        <div class="fw-bold text-dark fs-6">{{ $selectedSale->invoice_no }}</div>
                        <span class="small text-muted">Billed on {{ \Carbon\Carbon::parse($selectedSale->sale_date)->format('d M, Y') }}</span>
                    </div>
                    @endif

                    <!-- Customer Select -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase">Retail Customer *</label>
                        <select name="distributor_customer_id" id="customerSelect" class="form-select" onchange="updateCustomerInfo(this)" required>
                            <option value="">-- Choose Retail Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ ($selectedSale && $selectedSale->distributor_customer_id == $c->id) ? 'selected' : '' }} data-balance="{{ (float)$c->current_balance }}">
                                {{ $c->shop_name }} (Due: Rs. {{ number_format($c->current_balance, 0) }})
                            </option>
                            @endforeach
                        </select>
                        <div class="mt-1 small" id="customerDueBadge">
                            @if($selectedSale && $selectedSale->customer)
                            <span class="badge bg-warning-subtle text-dark border">
                                Outstanding Balance: Rs. {{ number_format($selectedSale->customer->current_balance, 2) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Return Date -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase">Return Date *</label>
                        <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <!-- Settlement / Refund Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase">Settlement Mode *</label>
                        <select name="refund_mode" id="refundModeSelect" class="form-select" onchange="toggleRefundFields()">
                            <option value="credit">Deduct from Customer Balance (Credit Udhaar)</option>
                            <option value="cash">Pay Cash Refund to Customer</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            "Deduct from Balance" directly lowers what the shop owes you.
                        </small>
                    </div>

                    <!-- Cash Refund Fields (Hidden by default) -->
                    <div id="cashRefundDiv" style="display: none;" class="p-3 rounded-3 mb-3 bg-light border border-danger-subtle">
                        <div class="mb-2">
                            <label class="form-label fw-semibold small text-danger">Cash Refund Amount (Rs.) *</label>
                            <input type="number" step="0.01" min="0" name="refund_amount" id="refundAmountInput" class="form-control fw-bold text-danger" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label fw-semibold small text-dark">Deduct Refund from Account *</label>
                            <select name="account_id" id="distributorRefundAccountSelect" class="form-select form-select-sm fw-semibold">
                                @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ ($acc->head_id == 2 || stripos($acc->title, 'cash') !== false) ? '💵' : '🏦' }} {{ $acc->title }} ({{ $acc->account_code }}) - Bal: Rs. {{ number_format($acc->opening_balance, 2) }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Refund ki raqam foran is account ke balance se minus ho jayegi.</small>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-uppercase">Reason / Return Notes</label>
                        <textarea name="reason" rows="2" class="form-control" placeholder="Reason for return, batch rejection, shop adjustment..."></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-3 rounded-pill fw-bold shadow-sm"
                            style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: 1px solid #ef4444;">
                        <i class="fas fa-undo-alt me-2"></i> Submit & Process Return
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Row for JS Addition -->
<template id="rowTemplate">
    <tr class="return-row">
        <td class="ps-3">
            <select name="product_id[]" class="form-select form-select-sm product-select" onchange="onProductSelect(this)" required>
                <option value="">-- Choose Product --</option>
                @foreach($stocks as $st)
                <option value="{{ $st->product_id }}" data-price="{{ $st->avg_price ?: ($st->product->wholesale_price ?? 0) }}" data-variant-id="{{ $st->variant_id }}" data-variant-name="{{ $st->variant ? ($st->variant->size_label ?: $st->variant->variant_name) : 'Standard' }}">
                    {{ $st->product->item_name ?? 'Product' }} @if($st->variant) ({{ $st->variant->size_label ?: $st->variant->variant_name }}) @endif
                </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
            <span class="badge bg-light text-secondary border row-variant-badge">Standard</span>
        </td>
        <td class="text-center">
            <input type="number" step="0.01" min="0" name="return_qty[]" value="1"
                   class="form-control form-control-sm text-center fw-bold row-qty" oninput="calculateTotals()" required>
        </td>
        <td class="text-end">
            <input type="number" step="0.01" name="unit_price[]" value="0.00"
                   class="form-control form-control-sm text-end row-price" oninput="calculateTotals()" required>
        </td>
        <td class="text-end fw-bold text-danger row-subtotal">
            0.00
        </td>
        <td class="text-center pe-3">
            <select name="item_condition[]" class="form-select form-select-sm">
                <option value="restockable">Good (Restock)</option>
                <option value="damaged">Damaged / Expired</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

@section('scripts')
<script>
    function addRow() {
        const tpl = document.getElementById('rowTemplate');
        const clone = tpl.content.cloneNode(true);
        document.getElementById('returnItemsBody').appendChild(clone);
        calculateTotals();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#returnItemsBody .return-row');
        if (rows.length <= 1) {
            alert('At least one item row is required.');
            return;
        }
        btn.closest('tr').remove();
        calculateTotals();
    }

    function onProductSelect(select) {
        const row = select.closest('tr');
        const opt = select.selectedOptions[0];
        if (!opt) return;

        const price = parseFloat(opt.dataset.price || 0);
        const variantId = opt.dataset.variantId || '';
        const variantName = opt.dataset.variantName || 'Standard';

        const priceInput = row.querySelector('.row-price');
        if (priceInput) {
            priceInput.value = price.toFixed(2);
        }
        const variantIdInput = row.querySelector('.row-variant-id');
        if (variantIdInput) {
            variantIdInput.value = variantId;
        }
        const variantBadge = row.querySelector('.row-variant-badge');
        if (variantBadge) {
            variantBadge.textContent = variantName;
        }

        calculateTotals();
    }

    function calculateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('#returnItemsBody .return-row').forEach(row => {
            const qtyInput = row.querySelector('.row-qty');
            const priceInput = row.querySelector('.row-price');
            const subtotalElem = row.querySelector('.row-subtotal');

            const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
            const subtotal = qty * price;

            if (subtotalElem) {
                subtotalElem.textContent = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            grandTotal += subtotal;
        });

        const displayTotalElem = document.getElementById('displayTotal');
        if (displayTotalElem) {
            displayTotalElem.textContent = 'Rs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const refundInput = document.getElementById('refundAmountInput');
        if (refundInput && (!refundInput.value || parseFloat(refundInput.value) > grandTotal)) {
            refundInput.value = grandTotal.toFixed(2);
        }
    }

    function toggleRefundFields() {
        const mode = document.getElementById('refundModeSelect').value;
        const div = document.getElementById('cashRefundDiv');
        if (div) {
            div.style.display = (mode === 'cash') ? 'block' : 'none';
        }
    }

    function updateCustomerInfo(select) {
        const opt = select.selectedOptions[0];
        const badge = document.getElementById('customerDueBadge');
        if (!badge) return;
        if (opt && opt.dataset.balance) {
            const bal = parseFloat(opt.dataset.balance || 0);
            badge.innerHTML = `<span class="badge bg-warning-subtle text-dark border">Outstanding Balance: Rs. ${bal.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>`;
        } else {
            badge.innerHTML = '';
        }
    }

    // Event delegation on table body so any typing or change immediately recalculates
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.getElementById('returnItemsBody');
        if (tableBody) {
            tableBody.addEventListener('input', (e) => {
                if (e.target.classList.contains('row-qty') || e.target.classList.contains('row-price')) {
                    calculateTotals();
                }
            });
            tableBody.addEventListener('change', (e) => {
                if (e.target.classList.contains('row-qty') || e.target.classList.contains('row-price')) {
                    calculateTotals();
                }
            });
        }
        calculateTotals();
    });
</script>
@endsection
