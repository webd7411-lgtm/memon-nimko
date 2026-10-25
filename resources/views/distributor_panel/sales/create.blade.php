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
                    Secondary Sale POS
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Sell to Retail Customer / Shop</h4>
            <p class="mb-0 text-white-50 small">Issue stock from your van custody directly to retail shops with instant billing</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.sales.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                <i class="fas fa-history me-1"></i> Sales History
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

    @if($stocks->isEmpty())
    <div class="alert alert-warning rounded-4 shadow-sm p-4 text-center">
        <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
        <h5 class="fw-bold text-dark">No Available Stock in Van / Custody</h5>
        <p class="text-muted small mb-3">You do not have any items in stock to sell. Please submit a demand order to Head Office first.</p>
        <a href="{{ route('distributor.orders.create') }}" class="btn btn-teal rounded-pill px-4">
            <i class="fas fa-cart-arrow-down me-1"></i> Create Demand Order
        </a>
    </div>
    @else
    <form action="{{ route('distributor.sales.store') }}" method="POST" id="saleForm">
        @csrf
        <div class="row g-4 mb-4">
            <!-- Left Side: Items Selection Table -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-cart-arrow-down me-2 text-teal" style="color: #0f766e;"></i> Cart Items (From Van Stock)
                            </h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" onclick="addSaleRow()">
                            <i class="fas fa-plus me-1"></i> Add Row
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="saleItemsTable">
                            <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                                <tr>
                                    <th class="ps-3" style="width: 42%;">Product In Custody</th>
                                    <th class="text-center" style="width: 18%;">Available</th>
                                    <th class="text-center" style="width: 15%;">Sale Qty</th>
                                    <th class="text-end" style="width: 15%;">Rate (Rs)</th>
                                    <th class="text-center pe-3" style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody id="saleItemsBody" style="font-size: 0.88rem;">
                                <!-- First Row Default -->
                                <tr class="sale-row">
                                    <td class="ps-3">
                                        <select name="product_id[]" class="form-select stock-select" onchange="onStockItemSelect(this)" required>
                                            <option value="">-- Choose Stock Item --</option>
                                            @foreach($stocks as $stk)
                                            <option value="{{ $stk->product_id }}"
                                                    data-variant="{{ $stk->variant_id }}"
                                                    data-avail="{{ (float)$stk->qty }}"
                                                    data-unit="{{ $stk->product->unit->name ?? 'Pc' }}"
                                                    data-price="{{ (float)($stk->product->price ?? $stk->avg_price) }}">
                                                {{ $stk->product->item_name }}
                                                @if($stk->variant)
                                                ({{ $stk->variant->size_label ?: $stk->variant->variant_name }})
                                                @endif
                                                — [{{ (float)$stk->qty }} in van]
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="variant_id[]" class="variant-hidden" value="">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-success border avail-badge px-2.5 py-1">0 pcs</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.01" min="0.01" name="qty[]" class="form-control text-center fw-bold sale-qty"
                                               placeholder="0" oninput="calculateSale()" required>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control text-end fw-bold sale-price"
                                               placeholder="0.00" oninput="calculateSale()" required>
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeSaleRow(this)" style="width: 32px; height: 32px;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Retail Customer & Payment Summary -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-store me-2 text-teal" style="color: #0f766e;"></i> Retail Customer</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Select Retail Store *</label>
                        <select name="distributor_customer_id" id="customerSelect" class="form-select form-select-lg" onchange="onCustomerSelect(this)" required>
                            <option value="">-- Choose Retailer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-balance="{{ (float)$c->current_balance }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->shop_name }} ({{ $c->area ?: 'Market' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Current Balance -->
                    <div class="p-3 rounded-3 mb-3 bg-light border">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Previous Outstanding:</span>
                            <span class="fw-bold text-danger" id="prevBalanceDisplay">Rs. 0.00</span>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-calculator me-2 text-teal" style="color: #0f766e;"></i> Billing Summary</h6>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal:</span>
                        <span class="fw-bold text-dark fs-6" id="subtotalDisplay">Rs. 0.00</span>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Special Discount:</span>
                            <div class="input-group input-group-sm" style="max-width: 130px;">
                                <span class="input-group-text">Rs</span>
                                <input type="number" step="0.01" min="0" name="discount" id="discountInput" value="0.00" class="form-control text-end" oninput="calculateSale()">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-2">
                        <span class="fw-bold text-dark">Grand Total:</span>
                        <span class="fw-bold fs-5" style="color: #0f766e;" id="grandTotalDisplay">Rs. 0.00</span>
                    </div>

                    <!-- Payment Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Payment Mode *</label>
                        <select name="payment_type" id="paymentTypeSelect" class="form-select" onchange="onPaymentTypeChange(this)" required>
                            <option value="cash">💵 Cash Received</option>
                            <option value="credit">📝 Credit (Udhaar)</option>
                            <option value="partial">⚖️ Partial Payment</option>
                        </select>
                    </div>

                    <div class="mb-3" id="paidAmountContainer">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Cash Collected Now (Rs)</label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmountInput" value="0.00" class="form-control form-control-lg fw-bold text-success" oninput="calculateSale()">
                    </div>

                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span>Remaining Due Balance:</span>
                        <span class="fw-bold text-danger fs-6" id="dueAmountDisplay">Rs. 0.00</span>
                    </div>

                    <div class="mb-3">
                        <textarea name="notes" rows="2" class="form-control small" placeholder="Optional sale notes..."></textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-3 rounded-pill fw-bold shadow-sm"
                            style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;">
                        <i class="fas fa-check-circle me-1"></i> Confirm Sale & Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- Template Row for JS -->
<template id="saleRowTemplate">
    <tr class="sale-row">
        <td class="ps-3">
            <select name="product_id[]" class="form-select stock-select" onchange="onStockItemSelect(this)" required>
                <option value="">-- Choose Stock Item --</option>
                @foreach($stocks as $stk)
                <option value="{{ $stk->product_id }}"
                        data-variant="{{ $stk->variant_id }}"
                        data-avail="{{ (float)$stk->qty }}"
                        data-unit="{{ $stk->product->unit->name ?? 'Pc' }}"
                        data-price="{{ (float)($stk->product->price ?? $stk->avg_price) }}">
                    {{ $stk->product->item_name }}
                    @if($stk->variant)
                    ({{ $stk->variant->size_label ?: $stk->variant->variant_name }})
                    @endif
                    — [{{ (float)$stk->qty }} in van]
                </option>
                @endforeach
            </select>
            <input type="hidden" name="variant_id[]" class="variant-hidden" value="">
        </td>
        <td class="text-center">
            <span class="badge bg-light text-success border avail-badge px-2.5 py-1">0 pcs</span>
        </td>
        <td class="text-center">
            <input type="number" step="0.01" min="0.01" name="qty[]" class="form-control text-center fw-bold sale-qty"
                   placeholder="0" oninput="calculateSale()" required>
        </td>
        <td class="text-end">
            <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control text-end fw-bold sale-price"
                   placeholder="0.00" oninput="calculateSale()" required>
        </td>
        <td class="text-center pe-3">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeSaleRow(this)" style="width: 32px; height: 32px;">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<script>
function onStockItemSelect(select) {
    const row = select.closest('tr');
    const selectedOpt = select.options[select.selectedIndex];
    const availBadge = row.querySelector('.avail-badge');
    const priceInput = row.querySelector('.sale-price');
    const qtyInput = row.querySelector('.sale-qty');
    const variantHidden = row.querySelector('.variant-hidden');

    if (selectedOpt && selectedOpt.value) {
        const avail = parseFloat(selectedOpt.dataset.avail) || 0;
        const unit = selectedOpt.dataset.unit || 'pcs';
        const price = parseFloat(selectedOpt.dataset.price) || 0;
        const variant = selectedOpt.dataset.variant || '';

        availBadge.innerText = avail + ' ' + unit;
        priceInput.value = price.toFixed(2);
        variantHidden.value = variant;
        qtyInput.max = avail;
        if (!qtyInput.value) qtyInput.value = 1;
    } else {
        availBadge.innerText = '0 pcs';
        variantHidden.value = '';
    }
    calculateSale();
}

function addSaleRow() {
    const tpl = document.getElementById('saleRowTemplate');
    const clone = tpl.content.cloneNode(true);
    document.getElementById('saleItemsBody').appendChild(clone);
}

function addSaleRowAndFocus() {
    addSaleRow();
    const rows = document.querySelectorAll('.sale-row');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        const select = lastRow.querySelector('.stock-select');
        if (select) select.focus();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const target = e.target;
        if (target.classList.contains('sale-qty')) {
            e.preventDefault();
            const row = target.closest('tr');
            const price = row.querySelector('.sale-price');
            if (price) {
                price.focus();
                price.select();
            }
            return;
        }

        if (target.classList.contains('sale-price')) {
            e.preventDefault();
            const row = target.closest('tr');
            const rows = document.querySelectorAll('.sale-row');
            if (row === rows[rows.length - 1]) {
                addSaleRowAndFocus();
            } else {
                const nextRow = row.nextElementSibling;
                if (nextRow) {
                    const nextSelect = nextRow.querySelector('.stock-select');
                    if (nextSelect) nextSelect.focus();
                }
            }
            return;
        }
    }
});

function removeSaleRow(btn) {
    const rows = document.querySelectorAll('.sale-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        calculateSale();
    } else {
        alert('Sale must contain at least one item.');
    }
}

function onCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const bal = opt ? (parseFloat(opt.dataset.balance) || 0) : 0;
    document.getElementById('prevBalanceDisplay').innerText = 'Rs. ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function onPaymentTypeChange(select) {
    const type = select.value;
    const paidInput = document.getElementById('paidAmountInput');
    const grandTotal = getGrandTotal();

    if (type === 'cash') {
        paidInput.value = grandTotal.toFixed(2);
    } else if (type === 'credit') {
        paidInput.value = '0.00';
    }
    calculateSale();
}

function getGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.sale-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.sale-qty').value) || 0;
        const price = parseFloat(row.querySelector('.sale-price').value) || 0;
        subtotal += (qty * price);
    });
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    return Math.max(0, subtotal - discount);
}

function calculateSale() {
    let subtotal = 0;
    document.querySelectorAll('.sale-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.sale-qty').value) || 0;
        const price = parseFloat(row.querySelector('.sale-price').value) || 0;
        subtotal += (qty * price);
    });

    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const grandTotal = Math.max(0, subtotal - discount);

    const paymentType = document.getElementById('paymentTypeSelect').value;
    const paidInput = document.getElementById('paidAmountInput');

    if (paymentType === 'cash') {
        paidInput.value = grandTotal.toFixed(2);
    }

    const paid = parseFloat(paidInput.value) || 0;
    const due = Math.max(0, grandTotal - paid);

    document.getElementById('subtotalDisplay').innerText = 'Rs. ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('grandTotalDisplay').innerText = 'Rs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dueAmountDisplay').innerText = 'Rs. ' + due.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const custSelect = document.getElementById('customerSelect');
    if (custSelect && custSelect.value) {
        onCustomerSelect(custSelect);
    }
    calculateSale();
});
</script>
@endsection
