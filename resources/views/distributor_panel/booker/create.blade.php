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
    --bk-radius: 16px;
}

/* Prevent any accidental horizontal scroll on the entire page */
html, body {
    overflow-x: hidden !important;
    max-width: 100vw;
}

.bk-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 12px 14px 40px 14px;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    overflow-x: hidden;
    width: 100%;
    box-sizing: border-box;
}

/* Card Styling */
.bk-card {
    background: var(--bk-card-bg);
    border: 1px solid var(--bk-border);
    border-radius: var(--bk-radius);
    padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 16px;
    box-sizing: border-box;
    width: 100%;
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
    border-radius: 14px;
    padding: 14px 18px;
    margin-top: 14px;
    transition: all 0.3s ease;
    word-break: break-word;
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

/* Desktop Products Table */
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
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

/* Select2 Enhancements & Mobile Guard */
.select2-container {
    width: 100% !important;
    max-width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 44px !important;
    border: 1.5px solid var(--bk-border) !important;
    border-radius: 10px !important;
    padding: 7px 12px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}

.select2-dropdown {
    max-width: 95vw !important;
    border-radius: 12px !important;
    border: 1.5px solid var(--bk-primary) !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;
    z-index: 99999 !important;
}

.select2-search--dropdown .select2-search__field {
    border-radius: 8px !important;
    padding: 8px 12px !important;
}

/* Sleek Buttons */
.btn-premium-add {
    background-color: #f0fdfa;
    color: #0f766e;
    border: 1.5px solid #99f6e4;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 8px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
}

.btn-premium-add:hover {
    background-color: #ccfbf1;
    color: #115e59;
    border-color: #5eead4;
}

/* Premium Compact Submit Button */
.btn-premium-submit {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 11px 20px;
    border-radius: 10px;
    border: none;
    letter-spacing: 0.2px;
    box-shadow: 0 3px 10px rgba(15, 118, 110, 0.22);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    width: 100%;
}

.btn-premium-submit:hover {
    background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
    box-shadow: 0 4px 14px rgba(15, 118, 110, 0.32);
    transform: translateY(-1px);
    color: #ffffff;
}

.btn-premium-submit:active {
    transform: translateY(0);
    box-shadow: 0 2px 5px rgba(15, 118, 110, 0.2);
}

/* Hide mobile-specific elements on desktop */
.bk-mobile-row-header,
.bk-mobile-subtotal-strip,
.item-mobile-label {
    display: none !important;
}

/* =========================================================================
   MOBILE NATIVE APP EXPERIENCE (< 768px)
   Balanced, Symmetrical 2-Column Boxes with Zero Horizontal Scroll
   ========================================================================= */
@media (max-width: 767.98px) {
    .bk-container {
        padding: 8px 10px 40px 10px !important;
    }

    .bk-card {
        padding: 14px 14px !important;
        border-radius: 16px !important;
        margin-bottom: 12px !important;
    }

    .bk-card-title {
        font-size: 0.96rem !important;
        margin-bottom: 10px !important;
        padding-bottom: 8px !important;
    }

    .balance-amount-display {
        font-size: 1.35rem !important;
    }

    /* Disable horizontal scroll on table wrapper */
    .table-responsive {
        overflow-x: visible !important;
    }

    /* Convert Table elements to Block / Grid for App-like Box rendering */
    #bookingTable {
        display: block !important;
        width: 100% !important;
        border: none !important;
        margin-bottom: 0 !important;
    }

    #bookingTable thead {
        display: none !important;
    }

    #bookingTable tbody {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        width: 100% !important;
    }

    /* EACH PRODUCT ROW AS AN ELEVATED, BALANCED CARD / BOX */
    #bookingTable tr.bk-row {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 10px 10px !important;
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 14px !important;
        padding: 12px !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        box-sizing: border-box !important;
        width: 100% !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    #bookingTable tr.bk-row:focus-within {
        border-color: #0f766e !important;
        box-shadow: 0 4px 14px rgba(15, 118, 110, 0.12) !important;
    }

    /* Grid Cell 1: Product & Variant spans full width (Row 1) */
    .bk-td-product {
        grid-column: 1 / -1 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
        width: 100% !important;
    }

    /* Grid Cell 2 & 3: Booking Qty (Left) & Unit Price (Right) (Row 2) */
    .bk-td-qty {
        grid-column: 1 / 2 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
    }

    .bk-td-price {
        grid-column: 2 / 3 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
    }

    /* Grid Cell 4 & 5: Trade Offer (Left) & Van Stock (Right) (Row 3) */
    .bk-td-to {
        grid-column: 1 / 2 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
    }

    .bk-td-stock {
        grid-column: 2 / 3 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
    }

    /* Grid Cell 6: Subtotal Box spans full width (Row 4) */
    .bk-td-subtotal {
        grid-column: 1 / -1 !important;
        display: block !important;
        padding: 0 !important;
        border: none !important;
    }

    .bk-td-action {
        display: none !important;
    }

    /* Mobile Row Header */
    .bk-mobile-row-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding-bottom: 8px !important;
        margin-bottom: 10px !important;
        border-bottom: 1px dashed #e2e8f0 !important;
    }

    .bk-stock-box-mobile {
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 8px;
        width: 100%;
        box-sizing: border-box;
    }

    .bk-mobile-subtotal-strip {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%) !important;
        border: 1px solid #ccfbf1 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-top: 2px !important;
    }

    .item-mobile-label {
        display: block !important;
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bk-item-badge {
        font-size: 0.8rem;
        font-weight: 800;
        color: #0f766e;
        background: #f0fdfa;
        border: 1px solid #ccfbf1;
        padding: 3px 9px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
    }

    .row-qty, .row-price, .row-trade-offer {
        font-size: 0.92rem !important;
        border-radius: 8px !important;
        height: 38px !important;
    }
}
</style>

<div class="bk-container">

    {{-- Top Notification Banner --}}
    <div class="alert alert-info border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 mb-3 rounded-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0fdfa 100%); border-left: 5px solid #0f766e !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-clipboard-check fs-4 text-teal" style="color: #0f766e;"></i>
            <div>
                <strong class="text-dark d-block">Order Booking Mode</strong>
                <small class="text-secondary">Demand booking for retail shops. Stock and ledgers will not be affected until dispatch.</small>
            </div>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge bg-dark font-monospace px-3 py-1.5 fs-6">{{ $bookingNo }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
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

            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold text-secondary small mb-1">
                        Retail Shop / Customer <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2-shop" id="distributor_customer_id" name="distributor_customer_id" required>
                        <option value="">-- Choose Retail Shop --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-balance="{{ (float)$c->current_balance }}"
                                    data-approved-discount="{{ (float)$c->effective_discount }}" 
                                    data-to-buy="{{ (float)$c->effective_trade_offer_buy_qty }}" 
                                    data-to-free="{{ (float)$c->effective_trade_offer_free_qty }}" 
                                    data-tier="{{ $c->tier?->name ?? 'Standard Retailer' }}" 
                                    data-scheme-label="{{ $c->scheme_label }}"
                                    data-phone="{{ $c->phone ?: 'N/A' }}"
                                    data-owner="{{ $c->owner_name ?: 'N/A' }}"
                                    data-area="{{ $c->area ?: 'N/A' }}"
                                    data-address="{{ $c->address ?: 'N/A' }}">
                                {{ $c->shop_name }} &bull; {{ $c->area ?: 'General' }} ({{ $c->owner_name ?: $c->phone }}) [Disc: {{ (float)($c->approved_discount ?? 0) }}%]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-bold text-secondary small mb-1">
                        <i class="fas fa-hand-holding-dollar text-success me-1"></i> Order Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select form-select-sm h-auto py-2 fw-bold" id="orderPaymentType" name="payment_type" required>
                        <option value="cash" selected>Order Cash</option>
                        <option value="credit">Order Credit</option>
                    </select>
                </div>

                <div class="col-6 col-sm-3 col-md-2">
                    <label class="form-label fw-bold text-secondary small mb-1">Booking Date</label>
                    <input type="date" class="form-control form-control-sm h-auto py-2 fw-bold" name="booking_date" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-6 col-sm-3 col-md-2">
                    <label class="form-label fw-bold text-secondary small mb-1">Booker</label>
                    <input type="text" class="form-control form-control-sm h-auto py-2 bg-light fw-bold text-muted" value="{{ Auth::user()->name ?? 'Booker' }}" readonly>
                </div>
            </div>

            {{-- 🎯 LIVE PREVIOUS BALANCE BANNER (AUTO-UPDATES ON SHOP SELECT) --}}
            <div id="shopBalanceBanner" class="shop-balance-banner">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-dark font-monospace" id="lblShopArea">-</span>
                            <h5 class="mb-0 fw-bolder text-dark" id="lblShopName">Selected Shop</h5>
                        </div>
                        <div class="small text-muted mb-1">
                            <i class="fas fa-user me-1"></i> <span id="lblShopOwner">-</span> &bull; 
                            <i class="fas fa-phone me-1"></i> <span id="lblShopPhone">-</span>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" id="badgeApprovedDiscount">
                                <i class="fas fa-percentage me-1"></i> Approved Discount Limit: <strong id="lblApprovedDiscount">0</strong>%
                            </span>
                        </div>
                    </div>

                    <div class="text-md-end text-start mt-2 mt-md-0">
                        <span class="badge px-3 py-1 mb-1 fw-bold" id="badgeDueStatus">Status</span>
                        <div class="balance-amount-display" id="valPreviousBalance">Rs 0.00</div>
                        <small class="text-muted d-block" id="lblBalanceHelp">Outstanding Balance Due</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════ STEP 2: PRODUCT SELECTION & BOOKING ITEMS (APP BOXES) ════════════ --}}
        <div class="bk-card">
            <div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-2 mb-3">
                <div class="bk-card-title mb-0 border-0 p-0">
                    <i class="fas fa-boxes-stacked text-teal" style="color: #0f766e;"></i>
                    <span>Step 2: Order Booking Items</span>
                </div>
                <button type="button" class="btn btn-premium-add shadow-xs" id="btnAddRow">
                    <i class="fas fa-plus me-1"></i> Add Another Item
                </button>
            </div>

            @php
                $productOptionsHtml = '<option value="">-- Choose Product / Variant --</option>';
                foreach ($products as $p) {
                    $unitName = $p->unit->name ?? 'pc';
                    if ($p->variants && $p->variants->count() > 0) {
                        foreach ($p->variants as $v) {
                            $stockKey = $p->id . '_' . $v->id;
                            $vStock = isset($stockMap[$stockKey]) ? (float)$stockMap[$stockKey]->qty : 0;
                            $vPrice = (float)($v->price ?: $p->price);
                            $vName = $v->size_label ?: $v->variant_name;
                            $formattedStock = ($vStock == (int)$vStock) ? (int)$vStock : $vStock;
                            $label = $p->item_name . ' - ' . $vName . ' (Rs ' . number_format($vPrice, 0) . ') [' . $formattedStock . ' ' . $unitName . ']';
                            $productOptionsHtml .= '<option value="' . $p->id . '" data-variant-id="' . $v->id . '" data-price="' . $vPrice . '" data-stock="' . $vStock . '" data-unit="' . e($unitName) . '">' . e($label) . '</option>';
                        }
                    } else {
                        $stockKey = $p->id . '_0';
                        $pStock = isset($stockMap[$stockKey]) ? (float)$stockMap[$stockKey]->qty : 0;
                        $pPrice = (float)$p->price;
                        $formattedStock = ($pStock == (int)$pStock) ? (int)$pStock : $pStock;
                        $label = $p->item_name . ' (Rs ' . number_format($pPrice, 0) . ') [' . $formattedStock . ' ' . $unitName . ']';
                        $productOptionsHtml .= '<option value="' . $p->id . '" data-variant-id="" data-price="' . $pPrice . '" data-stock="' . $pStock . '" data-unit="' . e($unitName) . '">' . e($label) . '</option>';
                    }
                }
            @endphp

            <div class="table-responsive">
                <table class="table bk-table align-middle" id="bookingTable">
                    <thead>
                        <tr>
                            <th style="width: 32%;">Product &amp; Variant</th>
                            <th style="width: 14%;" class="text-center">Available Stock</th>
                            <th style="width: 12%;" class="text-center">Booking Qty</th>
                            <th style="width: 13%;" class="text-end">Unit Price</th>
                            <th style="width: 13%;" class="text-center"><i class="fas fa-gift text-success me-1"></i> Trade Offer (Free)</th>
                            <th style="width: 12%;" class="text-end">Subtotal</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        <tr class="bk-row" data-row="0">
                            {{-- Cell 1: Product & Variant (Spans Full Width on Mobile) --}}
                            <td class="bk-td-product">
                                <div class="bk-mobile-row-header">
                                    <span class="bk-item-badge">
                                        <i class="fas fa-box-open me-1"></i> Item #<span class="item-index-num">1</span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 btn-remove-row">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </div>

                                <label class="item-mobile-label">Product &amp; Variant <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm select2-product product-selector" name="product_id[]" required>
                                    {!! $productOptionsHtml !!}
                                </select>
                                <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
                            </td>

                            {{-- Cell 2: Van Stock (Paired with Trade Offer on Row 3 on Mobile) --}}
                            <td class="bk-td-stock text-center">
                                <label class="item-mobile-label"><i class="fas fa-warehouse text-secondary me-1"></i> Van Stock</label>
                                <div class="bk-stock-box-mobile">
                                    <span class="stock-tag bg-light text-secondary border stock-display">-</span>
                                </div>
                            </td>

                            {{-- Cell 3: Booking Qty (Left Column on Row 2 on Mobile) --}}
                            <td class="bk-td-qty">
                                <label class="item-mobile-label">Booking Qty <span class="text-danger">*</span></label>
                                <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center fw-bold row-qty py-2" name="qty[]" value="1" required>
                            </td>

                            {{-- Cell 4: Unit Price (Right Column on Row 2 on Mobile) --}}
                            <td class="bk-td-price">
                                <label class="item-mobile-label">Unit Price (Rs) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text d-md-none small">Rs</span>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-bold row-price py-2" name="unit_price[]" value="0" required>
                                </div>
                            </td>

                            {{-- Cell 5: Trade Offer (Left Column on Row 3 on Mobile) --}}
                            <td class="bk-td-to">
                                <label class="item-mobile-label">
                                    <i class="fas fa-gift text-success me-1"></i> Trade Offer (Free)
                                </label>
                                <input type="number" step="any" min="0" readonly class="form-control form-control-sm text-center fw-bold row-trade-offer bg-light text-primary py-2" name="trade_offer[]" value="0" placeholder="0" title="Trade Offer Scheme: Auto-calculated &amp; Locked by Company">
                            </td>

                            {{-- Cell 6: Subtotal Strip (Spans Full Width on Row 4 on Mobile) --}}
                            <td class="bk-td-subtotal text-end">
                                <div class="bk-mobile-subtotal-strip">
                                    <span class="small text-secondary fw-bold"><i class="fas fa-receipt text-teal me-1"></i> Item Subtotal:</span>
                                    <span class="fw-bolder fs-6 row-subtotal text-teal" style="color: #0f766e;">Rs 0.00</span>
                                </div>
                                <span class="fw-bold row-subtotal text-dark d-none d-md-inline">Rs 0.00</span>
                            </td>

                            {{-- Cell 7: Action (Desktop Table Only) --}}
                            <td class="bk-td-action text-center d-none d-md-table-cell">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Remove item">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-start">
                <button type="button" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3 py-2 w-100 w-sm-auto shadow-xs" id="btnAddRowBottom">
                    <i class="fas fa-plus-circle text-success me-1"></i> + Add Another Item Box
                </button>
            </div>
        </div>

        {{-- ════════════ STEP 3: BILL SUMMARY & CONFIRMATION ════════════ --}}
        <div class="row g-3">
            <div class="col-12 col-md-5 order-1 order-md-2">
                <div class="bk-card h-100">
                    <div class="bk-card-title">
                        <i class="fas fa-calculator text-teal" style="color: #0f766e;"></i>
                        <span>Booking Total Summary</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary small">Total Products:</span>
                        <strong id="lblTotalItems">0</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary small">Total Quantity:</span>
                        <strong id="lblTotalQty">0</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary small"><i class="fas fa-gift text-success me-1"></i> Trade Offer (Free):</span>
                        <strong id="lblTotalTradeOffer" class="text-success">0</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary small">Subtotal:</span>
                        <strong id="lblSubtotal">Rs 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary small">Order Type:</span>
                        <span id="lblSummaryOrderType" class="badge bg-success text-white px-2.5 py-1 fw-bold font-monospace">
                            <i class="fas fa-money-bill-wave me-1"></i> Order Cash
                        </span>
                    </div>

                    <div class="py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <span class="text-secondary fw-semibold small">Special Discount:</span>
                                <small class="text-danger d-block" id="lblDiscountCapNote" style="font-size: 0.72rem;">Max Allowed: 0% (Rs 0.00)</small>
                            </div>
                            <div style="width: 140px; max-width: 50%;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" step="any" min="0" class="form-control text-end fw-bold py-1.5" id="txtDiscount" name="discount" value="0">
                                </div>
                            </div>
                        </div>
                        <div id="discountLimitAlert" class="alert alert-warning py-1.5 px-2 my-2 small" style="display: none; font-size: 0.75rem;"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom mt-2">
                        <span class="fs-6 fw-bold text-dark">Net Order Value:</span>
                        <span class="fs-4 fw-bolder text-teal" id="lblGrandTotal" style="color: #0f766e;">Rs 0.00</span>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-premium-submit" id="btnSubmitBooking">
                            <i class="fas fa-check-circle me-1.5"></i> Submit Order Booking
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-7 order-2 order-md-1">
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

    let currentShopApprovedDiscountPct = 0;
    let currentShopToBuy = 0;
    let currentShopToFree = 0;

    // 1. Initialize Shop Select2
    $('.select2-shop').select2({
        placeholder: "-- Search & Choose Retail Shop --",
        allowClear: true,
        width: '100%'
    });

    // 2. Initialize Product Select2
    function formatProductOption(state) {
        if (!state.id) return state.text;
        const text = state.text;
        const lastBracketIndex = text.lastIndexOf(' [');
        if (lastBracketIndex !== -1 && text.endsWith(']')) {
            const mainText = text.substring(0, lastBracketIndex);
            const stockPart = text.substring(lastBracketIndex + 1);
            return $(`
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span class="fw-semibold text-dark">${mainText}</span>
                    <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-2 py-1 ms-2 font-monospace" style="font-size: 0.78rem;">${stockPart}</span>
                </div>
            `);
        }
        return state.text;
    }

    function initProductSelect2(element) {
        element.select2({
            placeholder: "-- Choose Product / Variant --",
            allowClear: true,
            width: '100%',
            templateResult: formatProductOption
        });
    }
    initProductSelect2($('.select2-product'));

    // Dynamic Order Type change feedback
    $('#orderPaymentType').on('change', function() {
        const val = $(this).val();
        if (val === 'credit') {
            $('#lblSummaryOrderType')
                .removeClass('bg-success')
                .addClass('bg-primary')
                .html('<i class="fas fa-file-invoice-dollar me-1"></i> Order Credit');
        } else {
            $('#lblSummaryOrderType')
                .removeClass('bg-primary')
                .addClass('bg-success')
                .html('<i class="fas fa-money-bill-wave me-1"></i> Order Cash');
        }
    });

    // Update Item # labels on rows
    function updateRowNumbers() {
        $('#bookingTableBody tr.bk-row').each(function(idx) {
            $(this).find('.item-index-num').text(idx + 1);
        });
    }

    // LIVE PREVIOUS BALANCE & APPROVED DISCOUNT ON SHOP SELECTION
    $('#distributor_customer_id').on('change', function() {
        const customerId = $(this).val();
        const banner = $('#shopBalanceBanner');

        if (!customerId) {
            currentShopApprovedDiscountPct = 0;
            currentShopToBuy = 0;
            currentShopToFree = 0;
            banner.slideUp();
            calculateGrandTotal();
            return;
        }

        const selectedOpt = $(this).find('option:selected');
        const rawText = selectedOpt.text();
        const shopName = rawText.includes('•') ? rawText.split('•')[0].trim() : (rawText.split('[')[0].trim());
        const shopOwner = selectedOpt.data('owner') || '-';
        const shopPhone = selectedOpt.data('phone') || '-';
        const shopArea  = selectedOpt.data('area')  || 'General';
        currentShopApprovedDiscountPct = parseFloat(selectedOpt.data('approved-discount')) || 0;
        currentShopToBuy = parseFloat(selectedOpt.data('to-buy')) || 0;
        currentShopToFree = parseFloat(selectedOpt.data('to-free')) || 0;

        $('#lblShopName').text(shopName);
        $('#lblShopOwner').text(shopOwner);
        $('#lblShopPhone').text(shopPhone);
        $('#lblShopArea').text(shopArea);
        $('#lblApprovedDiscount').text(currentShopApprovedDiscountPct);

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

                    if (res.approved_discount !== undefined) {
                        currentShopApprovedDiscountPct = parseFloat(res.approved_discount) || 0;
                    }
                    $('#lblApprovedDiscount').text(currentShopApprovedDiscountPct);

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
                    calculateGrandTotal();
                }
            },
            error: function() {
                const bal = parseFloat(selectedOpt.data('balance') || 0);
                $('#valPreviousBalance').text('Rs ' + Math.abs(bal).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#lblApprovedDiscount').text(currentShopApprovedDiscountPct);
                banner.slideDown();
                calculateGrandTotal();
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
        let totalTradeOffer = 0;
        let subtotal = 0;

        $('#bookingTableBody tr').each(function() {
            const pId = $(this).find('.product-selector').val();
            if (pId) {
                totalItems++;
                const q = parseFloat($(this).find('.row-qty').val()) || 0;
                const to = parseFloat($(this).find('.row-trade-offer').val()) || 0;
                const p = parseFloat($(this).find('.row-price').val()) || 0;
                totalQty += q;
                totalTradeOffer += to;
                subtotal += (q * p);
            }
        });

        const approvedPct = currentShopApprovedDiscountPct || 0;
        const maxDiscountAllowed = Math.round((subtotal * (approvedPct / 100)) * 100) / 100;

        $('#lblDiscountCapNote').text('Max Allowed: ' + approvedPct + '% (Rs ' + maxDiscountAllowed.toFixed(2) + ')');

        let discount = parseFloat($('#txtDiscount').val()) || 0;
        if (discount > (maxDiscountAllowed + 0.001)) {
            discount = maxDiscountAllowed;
            $('#txtDiscount').val(maxDiscountAllowed > 0 ? maxDiscountAllowed.toFixed(2) : 0);
            $('#discountLimitAlert')
                .html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Discount Limit:</strong> Ye shop sirf ' + approvedPct + '% discount ke liye approved hai (Max Rs ' + maxDiscountAllowed.toFixed(2) + '). Order Booker is se zyada discount nahi de sakta.')
                .slideDown();
        } else {
            $('#discountLimitAlert').slideUp();
        }

        const grandTotal = Math.max(0, subtotal - discount);

        $('#lblTotalItems').text(totalItems);
        $('#lblTotalQty').text(totalQty.toFixed(1));
        $('#lblTotalTradeOffer').text(totalTradeOffer > 0 ? ('+' + totalTradeOffer.toFixed(1) + ' Free') : '0');
        $('#lblSubtotal').text('Rs ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#lblGrandTotal').text('Rs ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
    }

    $(document).on('input', '.row-qty, .row-price, .row-trade-offer', function() {
        calculateRow($(this).closest('tr'));
    });

    $('#txtDiscount').on('input', function() {
        calculateGrandTotal();
    });

    // 6. Add Row (Box Template for Mobile & Desktop)
    function addTableRow() {
        const rowCount = $('#bookingTableBody tr').length;
        const template = `
            <tr class="bk-row" data-row="${rowCount}">
                <td class="bk-td-product">
                    <div class="bk-mobile-row-header">
                        <span class="bk-item-badge">
                            <i class="fas fa-box-open me-1"></i> Item #<span class="item-index-num">${rowCount + 1}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 btn-remove-row">
                            <i class="fas fa-trash-alt me-1"></i> Remove
                        </button>
                    </div>
                    <label class="item-mobile-label">Product &amp; Variant <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm select2-product product-selector" name="product_id[]" required>
                        {!! addslashes($productOptionsHtml) !!}
                    </select>
                    <input type="hidden" name="variant_id[]" class="row-variant-id" value="">
                </td>
                <td class="bk-td-stock text-center">
                    <label class="item-mobile-label"><i class="fas fa-warehouse text-secondary me-1"></i> Van Stock</label>
                    <div class="bk-stock-box-mobile">
                        <span class="stock-tag bg-light text-secondary border stock-display">-</span>
                    </div>
                </td>
                <td class="bk-td-qty">
                    <label class="item-mobile-label">Booking Qty <span class="text-danger">*</span></label>
                    <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center fw-bold row-qty py-2" name="qty[]" value="1" required>
                </td>
                <td class="bk-td-price">
                    <label class="item-mobile-label">Unit Price (Rs) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text d-md-none small">Rs</span>
                        <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-bold row-price py-2" name="unit_price[]" value="0" required>
                    </div>
                </td>
                <td class="bk-td-to">
                    <label class="item-mobile-label">
                        <i class="fas fa-gift text-success me-1"></i> Trade Offer (Free)
                    </label>
                    <input type="number" step="any" min="0" readonly class="form-control form-control-sm text-center fw-bold row-trade-offer bg-light text-primary py-2" name="trade_offer[]" value="0" placeholder="0" title="Trade Offer Scheme: Auto-calculated &amp; Locked by Company">
                </td>
                <td class="bk-td-subtotal text-end">
                    <div class="bk-mobile-subtotal-strip">
                        <span class="small text-secondary fw-bold"><i class="fas fa-receipt text-teal me-1"></i> Item Subtotal:</span>
                        <span class="fw-bolder fs-6 row-subtotal text-teal" style="color: #0f766e;">Rs 0.00</span>
                    </div>
                    <span class="fw-bold row-subtotal text-dark d-none d-md-inline">Rs 0.00</span>
                </td>
                <td class="bk-td-action text-center d-none d-md-table-cell">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Remove item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        const newRow = $(template);
        $('#bookingTableBody').append(newRow);
        initProductSelect2(newRow.find('.select2-product'));
        updateRowNumbers();
    }

    $('#btnAddRow, #btnAddRowBottom').on('click', function() {
        addTableRow();
    });

    // 7. Remove Row
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#bookingTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateGrandTotal();
            updateRowNumbers();
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

        let totalSubtotal = 0;
        $('#bookingTableBody tr').each(function() {
            const q = parseFloat($(this).find('.row-qty').val()) || 0;
            const p = parseFloat($(this).find('.row-price').val()) || 0;
            totalSubtotal += (q * p);
        });

        const approvedPct = currentShopApprovedDiscountPct || 0;
        const maxDiscountAllowed = Math.round((totalSubtotal * (approvedPct / 100)) * 100) / 100;
        const enteredDiscount = parseFloat($('#txtDiscount').val()) || 0;

        if (enteredDiscount > (maxDiscountAllowed + 0.05)) {
            e.preventDefault();
            alert('Discount Error: Is shop ke liye maximum ' + approvedPct + '% discount (Rs ' + maxDiscountAllowed.toFixed(2) + ') allowed hai. Booker is se zyada discount nahi de sakta.');
            $('#txtDiscount').val(maxDiscountAllowed.toFixed(2)).focus();
            calculateGrandTotal();
            return false;
        }

        $('#btnSubmitBooking').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Submitting Order...');
    });

});
</script>
@endsection
