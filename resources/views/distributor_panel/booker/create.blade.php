@extends('distributor_panel.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<style>
:root {
    --bk-primary: #0f766e;
    --bk-primary-hover: #0d9488;
    --bk-accent: #0284c7;
    --bk-success: #16a34a;
    --bk-danger: #ef4444;
    --bk-warning: #f59e0b;
    --bk-border: #cbd5e1;
    --bk-card-bg: #ffffff;
    --bk-radius: 12px;
}

.bk-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 12px 14px 40px 14px;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
}

/* Card Styling */
.bk-card {
    background: var(--bk-card-bg);
    border: 1px solid var(--bk-border);
    border-radius: var(--bk-radius);
    padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 16px;
}

.bk-card-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    border-bottom: 1.5px solid #f1f5f9;
    padding-bottom: 10px;
}

/* Live Previous Balance Highlight Box */
.shop-balance-banner {
    display: none;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 14px 18px;
    margin-top: 14px;
    transition: all 0.3s ease;
}

.shop-balance-banner.has-due {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border-color: #ef4444;
}

.shop-balance-banner.is-clear {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-color: #10b981;
}

.shop-balance-banner.is-advance {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-color: #0284c7;
}

.balance-amount-display {
    font-size: 1.6rem;
    font-weight: 900;
    letter-spacing: -0.5px;
}

/* Products Table */
.bk-table th {
    background-color: #0f172a;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border: none;
}

.bk-table td {
    padding: 8px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
}

.bk-row:hover {
    background-color: #f8fafc;
}

.stock-tag {
    font-size: 0.72rem;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 600;
}

/* Select2 Enhancements */
.select2-container .select2-selection--single {
    height: 42px !important;
    border: 1.5px solid var(--bk-border) !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}

/* Responsive tweaks */
@media (max-width: 768px) {
    .bk-container { padding: 8px; }
    .balance-amount-display { font-size: 1.35rem; }
    .bk-table { font-size: 0.85rem; }
}
</style>

<div class="bk-container">

    {{-- Top Notification Banner --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-3 rounded-3" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0fdfa 100%); border-left: 5px solid #0f766e !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-clipboard-check fs-4 text-teal" style="color: #0f766e;"></i>
            <div>
                <strong class="text-dark d-block">Order Booking Mode</strong>
                <small class="text-secondary">Demand order booking mode. Stock and customer ledgers will not be affected until the distributor approves delivery.</small>
            </div>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-dark font-monospace px-3 py-2 fs-6">{{ $bookingNo }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('distributor.booker.store') }}" method="POST" id="bookingOrderForm">
        @csrf

        {{-- ════════════ STEP 1: SHOP SELECTION & PREVIOUS BALANCE ════════════ --}}
        <div class="bk-card">
            <div class="bk-card-title">
                <i class="fas fa-store text-teal" style="color: #0f766e;"></i>
                <span>Step 1: Select Retail Shop</span>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold text-secondary small mb-1">
                        Retail Shop / Customer <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2-shop" id="distributor_customer_id" name="distributor_customer_id" required>
                        <option value="">-- Choose Retail Shop --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-balance="{{ (float)$c->current_balance }}"
                                    data-phone="{{ $c->phone ?: 'N/A' }}"
                                    data-owner="{{ $c->owner_name ?: 'N/A' }}"
                                    data-area="{{ $c->area ?: 'N/A' }}"
                                    data-address="{{ $c->address ?: 'N/A' }}">
                                {{ $c->shop_name }} &bull; {{ $c->area ?: 'General' }} ({{ $c->owner_name ?: $c->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-secondary small mb-1">Booking Date</label>
                    <input type="date" class="form-control form-control-sm h-auto py-2 fw-bold" name="booking_date" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-secondary small mb-1">Booker</label>
                    <input type="text" class="form-control form-control-sm h-auto py-2 bg-light fw-bold text-muted" value="{{ Auth::user()->name ?? 'Booker' }}" readonly>
                </div>
            </div>

            {{-- 🎯 LIVE PREVIOUS BALANCE BANNER (AUTO-UPDATES ON SHOP SELECT) --}}
            <div id="shopBalanceBanner" class="shop-balance-banner">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-dark font-monospace" id="lblShopArea">-</span>
                            <h5 class="mb-0 fw-bolder text-dark" id="lblShopName">Selected Shop</h5>
                        </div>
                        <div class="small text-muted">
                            <i class="fas fa-user me-1"></i> <span id="lblShopOwner">-</span> &bull; 
                            <i class="fas fa-phone me-1"></i> <span id="lblShopPhone">-</span>
                        </div>
                    </div>

                    <div class="text-md-end text-start">
                        <span class="badge px-3 py-1 mb-1 fw-bold" id="badgeDueStatus">Status</span>
                        <div class="balance-amount-display" id="valPreviousBalance">Rs 0.00</div>
                        <small class="text-muted d-block" id="lblBalanceHelp">Outstanding Balance Due</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════ STEP 2: PRODUCT SELECTION & BOOKING ITEMS ════════════ --}}
        <div class="bk-card">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                <div class="bk-card-title mb-0 border-0 p-0">
                    <i class="fas fa-boxes-stacked text-teal" style="color: #0f766e;"></i>
                    <span>Step 2: Order Booking Items</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3" id="btnAddRow">
                    <i class="fas fa-plus me-1"></i> Add Another Item
                </button>
            </div>

            @php
                $productOptionsHtml = '<option value="">-- Choose Product / Variant --</option>';
                foreach ($products as $p) {
                    if ($p->variants && $p->variants->count() > 0) {
                        foreach ($p->variants as $v) {
                            $stockKey = $p->id . '_' . $v->id;
                            $vStock = isset($stockMap[$stockKey]) ? (float)$stockMap[$stockKey]->qty : 0;
                            $vPrice = (float)($v->price ?: $p->price);
                            $vName = $v->size_label ?: $v->variant_name;
                            $unitName = $p->unit->name ?? 'Pcs';
                            $label = $p->item_name . ' - ' . $vName . ' (Rs ' . number_format($vPrice, 0) . ')';
                            $productOptionsHtml .= '<option value="' . $p->id . '" data-variant-id="' . $v->id . '" data-price="' . $vPrice . '" data-stock="' . $vStock . '" data-unit="' . e($unitName) . '">' . e($label) . '</option>';
                        }
                    } else {
                        $stockKey = $p->id . '_0';
                        $pStock = isset($stockMap[$stockKey]) ? (float)$stockMap[$stockKey]->qty : 0;
                        $pPrice = (float)$p->price;
                        $unitName = $p->unit->name ?? 'Pcs';
                        $label = $p->item_name . ' (Rs ' . number_format($pPrice, 0) . ')';
                        $productOptionsHtml .= '<option value="' . $p->id . '" data-variant-id="" data-price="' . $pPrice . '" data-stock="' . $pStock . '" data-unit="' . e($unitName) . '">' . e($label) . '</option>';
                    }
                }
            @endphp

            <div class="table-responsive">
                <table class="table bk-table align-middle" id="bookingTable">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Product &amp; Variant</th>
                            <th style="width: 15%;" class="text-center">Available Stock</th>
                            <th style="width: 15%;" class="text-center">Booking Qty</th>
                            <th style="width: 12%;" class="text-end">Unit Price</th>
                            <th style="width: 13%;" class="text-end">Subtotal</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        <tr class="bk-row" data-row="0">
                            <td>
                                <select class="form-select form-select-sm select2-product product-selector" name="product_id[]" required>
                                    {!! $productOptionsHtml !!}
                                </select>
                                <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
                            </td>
                            <td class="text-center">
                                <span class="stock-tag bg-light text-secondary border stock-display">-</span>
                            </td>
                            <td>
                                <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center fw-bold row-qty" name="qty[]" value="1" required>
                            </td>
                            <td>
                                <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-bold row-price" name="unit_price[]" value="0" required>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold row-subtotal text-dark">Rs 0.00</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Remove item">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-2 text-start">
                <button type="button" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3" id="btnAddRowBottom">
                    <i class="fas fa-plus-circle text-success me-1"></i> Add Another Row
                </button>
            </div>
        </div>

        {{-- ════════════ STEP 3: BILL SUMMARY & CONFIRMATION ════════════ --}}
        <div class="row g-3">
            <div class="col-12 col-md-7">
                <div class="bk-card h-100">
                    <div class="bk-card-title">
                        <i class="fas fa-comment-dots text-teal" style="color: #0f766e;"></i>
                        <span>Order Notes / Customer Remarks</span>
                    </div>
                    <textarea class="form-control" name="notes" rows="4" placeholder="e.g. Deliver before 11 AM, carton packing requested..."></textarea>
                    
                    <div class="p-3 bg-light rounded-3 mt-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span>This order will be submitted directly to the distributor for dispatch and delivery.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="bk-card h-100">
                    <div class="bk-card-title">
                        <i class="fas fa-calculator text-teal" style="color: #0f766e;"></i>
                        <span>Booking Total Summary</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Total Products:</span>
                        <strong id="lblTotalItems">0</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Total Quantity:</span>
                        <strong id="lblTotalQty">0</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Subtotal:</span>
                        <strong id="lblSubtotal">Rs 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary">Special Discount:</span>
                        <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-bold w-50" id="txtDiscount" name="discount" value="0">
                    </div>

                    <div class="d-flex justify-content-between py-3 border-bottom mt-2">
                        <span class="fs-5 fw-bold text-dark">Net Order Value:</span>
                        <span class="fs-4 fw-bolder text-teal" id="lblGrandTotal" style="color: #0f766e;">Rs 0.00</span>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm fs-5" id="btnSubmitBooking" style="background: linear-gradient(135deg, #10b981 0%, #0f766e 100%); border: none;">
                            <i class="fas fa-check-circle me-1"></i> Submit Order Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- Pass products and variants dictionary to JS --}}
<script>
    const allProductsData = @json($products);
    const distributorStockMap = @json($stockMap);
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // 1. Initialize Shop Select2
    $('.select2-shop').select2({
        placeholder: "-- Search & Choose Retail Shop --",
        allowClear: true,
        width: '100%'
    });

    // 2. Initialize Product Select2
    function initProductSelect2(element) {
        element.select2({
            placeholder: "-- Choose Product / Variant --",
            allowClear: true,
            width: '100%'
        });
    }
    initProductSelect2($('.select2-product'));

    // 🎯 LIVE PREVIOUS BALANCE UPDATE ON SHOP SELECTION
    $('#distributor_customer_id').on('change', function() {
        const customerId = $(this).val();
        const banner = $('#shopBalanceBanner');

        if (!customerId) {
            banner.slideUp();
            return;
        }

        const selectedOpt = $(this).find('option:selected');
        const shopName  = selectedOpt.text().split('•')[0].trim();
        const shopOwner = selectedOpt.data('owner') || '-';
        const shopPhone = selectedOpt.data('phone') || '-';
        const shopArea  = selectedOpt.data('area')  || 'General';

        $('#lblShopName').text(shopName);
        $('#lblShopOwner').text(shopOwner);
        $('#lblShopPhone').text(shopPhone);
        $('#lblShopArea').text(shopArea);

        // Fetch Live Credit / Previous Balance from backend
        $.ajax({
            url: "{{ url('/distributor-portal/booker/shop-info') }}/" + customerId,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $('#valPreviousBalance').html('<i class="fas fa-spinner fa-spin fs-5"></i>');
            },
            success: function(res) {
                if (res.success) {
                    banner.removeClass('has-due is-clear is-advance');

                    $('#valPreviousBalance').text(res.formatted_due);
                    $('#badgeDueStatus').text(res.due_status)
                                       .removeClass('bg-danger bg-success bg-info')
                                       .addClass(res.badge_class);

                    if (res.current_balance > 0) {
                        banner.addClass('has-due');
                        $('#valPreviousBalance').css('color', '#dc2626');
                        $('#lblBalanceHelp').text('Outstanding Balance Due');
                    } else if (res.current_balance < 0) {
                        banner.addClass('is-advance');
                        $('#valPreviousBalance').css('color', '#0284c7');
                        $('#lblBalanceHelp').text('Advance Balance Available');
                    } else {
                        banner.addClass('is-clear');
                        $('#valPreviousBalance').css('color', '#16a34a');
                        $('#lblBalanceHelp').text('Account Clear / Zero Balance');
                    }

                    banner.slideDown();
                }
            },
            error: function() {
                const bal = parseFloat(selectedOpt.data('balance') || 0);
                $('#valPreviousBalance').text('Rs ' + Math.abs(bal).toLocaleString('en-US', {minimumFractionDigits: 2}));
                banner.slideDown();
            }
        });
    });

    // 3. Product & Variant Selection Setup
    $(document).on('change', '.product-selector', function() {
        const row = $(this).closest('tr');
        const selectedOpt = $(this).find('option:selected');
        const pId = $(this).val();

        if (!pId) {
            row.find('.row-variant-id').val('');
            row.find('.stock-display').text('-').removeClass('bg-success bg-danger text-white').addClass('bg-light text-secondary');
            row.find('.row-price').val(0);
            calculateRow(row);
            return;
        }

        const vId = selectedOpt.data('variant-id') !== undefined ? selectedOpt.data('variant-id') : '';
        const price = parseFloat(selectedOpt.data('price')) || 0;
        const stock = parseFloat(selectedOpt.data('stock')) || 0;

        row.find('.row-variant-id').val(vId);
        row.find('.row-price').val(price);
        updateStockBadge(row, stock);
        calculateRow(row);
    });

    function updateStockBadge(row, availQty) {
        const badge = row.find('.stock-display');
        if (availQty > 0) {
            badge.text(availQty + ' in Van')
                 .removeClass('bg-danger bg-light text-secondary text-dark')
                 .addClass('bg-success-subtle text-success border border-success-subtle');
        } else {
            badge.text('0 (Out of Stock)')
                 .removeClass('bg-success bg-light text-secondary')
                 .addClass('bg-warning-subtle text-dark border border-warning-subtle');
        }
    }

    // 5. Calculations
    function calculateRow(row) {
        const qty = parseFloat(row.find('.row-qty').val()) || 0;
        const price = parseFloat(row.find('.row-price').val()) || 0;
        const subtotal = qty * price;
        row.find('.row-subtotal').text('Rs ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let totalItems = 0;
        let totalQty = 0;
        let subtotal = 0;

        $('#bookingTableBody tr').each(function() {
            const pId = $(this).find('.product-selector').val();
            if (pId) {
                totalItems++;
                const q = parseFloat($(this).find('.row-qty').val()) || 0;
                const p = parseFloat($(this).find('.row-price').val()) || 0;
                totalQty += q;
                subtotal += (q * p);
            }
        });

        const discount = parseFloat($('#txtDiscount').val()) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        $('#lblTotalItems').text(totalItems);
        $('#lblTotalQty').text(totalQty.toFixed(1));
        $('#lblSubtotal').text('Rs ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#lblGrandTotal').text('Rs ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
    }

    $(document).on('input', '.row-qty, .row-price', function() {
        calculateRow($(this).closest('tr'));
    });

    $('#txtDiscount').on('input', function() {
        calculateGrandTotal();
    });

    // 6. Add Row
    function addTableRow() {
        const rowCount = $('#bookingTableBody tr').length;
        const template = `
            <tr class="bk-row" data-row="${rowCount}">
                <td>
                    <select class="form-select form-select-sm select2-product product-selector" name="product_id[]" required>
                        {!! addslashes($productOptionsHtml) !!}
                    </select>
                    <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
                </td>
                <td class="text-center">
                    <span class="stock-tag bg-light text-secondary border stock-display">-</span>
                </td>
                <td>
                    <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center fw-bold row-qty" name="qty[]" value="1" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-bold row-price" name="unit_price[]" value="0" required>
                </td>
                <td class="text-end">
                    <span class="fw-bold row-subtotal text-dark">Rs 0.00</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Remove item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        const newRow = $(template);
        $('#bookingTableBody').append(newRow);
        initProductSelect2(newRow.find('.select2-product'));
    }

    $('#btnAddRow, #btnAddRowBottom').on('click', function() {
        addTableRow();
    });

    // 7. Remove Row
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#bookingTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert('At least one product row is required in the booking.');
        }
    });

    // 8. Form Validation
    $('#bookingOrderForm').on('submit', function(e) {
        if (!$('#distributor_customer_id').val()) {
            e.preventDefault();
            alert('Please select a retail shop first!');
            $('#distributor_customer_id').focus();
            return false;
        }

        let hasValidItems = false;
        $('.product-selector').each(function() {
            if ($(this).val()) hasValidItems = true;
        });

        if (!hasValidItems) {
            e.preventDefault();
            alert('Please select at least one product to book the order.');
            return false;
        }

        $('#btnSubmitBooking').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Submitting Order...');
    });

});
</script>
@endsection
