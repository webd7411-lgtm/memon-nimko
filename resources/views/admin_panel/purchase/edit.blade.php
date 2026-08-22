@extends('admin_panel.layout.app')

@section('content')
<style>
:root {
    --pe-bg: #f1f4f9;
    --pe-surface: #ffffff;
    --pe-border: #e9edf2;
    --pe-text: #0b1a33;
    --pe-sec: #54657e;
    --pe-muted: #8896ab;
    --pe-accent: #0d9488;
    --pe-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
}
.pe-desk { overflow-x: auto; }
.pe-tbl { table-layout: fixed; width: 100% !important; margin: 0; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
.pe-tbl thead th { background: #f8fafc; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; color: var(--pe-muted); padding: .5rem .6rem; border-bottom: 2px solid var(--pe-border); white-space: normal; overflow-wrap: break-word; }
.pe-tbl tbody td { padding: .4rem .5rem; border-bottom: 1px solid #f1f4f9; vertical-align: middle; }
.pe-tbl .form-control { font-size: .78rem; padding: .3rem .45rem; }

.pec-cards { display: none; }
.pec-card { background: var(--pe-surface); border: 1.5px solid var(--pe-border); border-left: 4px solid var(--pe-accent); border-radius: 10px; box-shadow: var(--pe-shadow); padding: .7rem .8rem; margin-bottom: .65rem; }
.pec-head { display: flex; align-items: center; gap: .5rem; margin-bottom: .55rem; padding-bottom: .5rem; border-bottom: 1px dashed var(--pe-border); }
.pec-head .pec-prod { font-weight: 700; color: var(--pe-text); background: #f8fafc; }
.pec-head .remove-row { flex: 0 0 auto; }
.pec-search-wrap { flex: 1 1 auto; min-width: 0; }
.pec-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .5rem .7rem; }
.pec-f { min-width: 0; }
.pec-lbl { display: block; font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--pe-muted); margin-bottom: .15rem; }
.pec-f .form-control { font-size: .85rem; min-height: 42px; }

@media (max-width: 991.98px) {
    .pe-desk { display: none; }
    .pec-cards { display: block; }
}
</style>
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">
            <div class="row">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
                    rel="stylesheet">

                <div class="body-wrapper">
                    <div class="bodywrapper__inner">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-nowrap overflow-auto">
                            <div class="flex-grow-1">
                                <h2 class="page-title m-0">Edit Purchase</h2>
                            </div>
                            <div class="d-flex gap-4 justify-content-end flex-wrap">
                                <a href="{{ route('Purchase.home') }}" class="btn btn-danger">Back</a>
                            </div>
                        </div>

                        <div class="row gy-3">
                            <div class="col-lg-12 col-md-12 mb-30">
                                <div class="card">
                                    <div class="card-body">

                                        <form action="{{ route('purchase.update', $purchase->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row mb-3 g-3 mt-4">
                                                <div class="col-xl-3 col-sm-6 mt-3">
                                                    <label><i class="bi bi-calendar-date text-primary me-1"></i>
                                                        Current Date</label>
                                                    <input name="purchase_date" type="date"
                                                        value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : '' }}"
                                                        class="form-control">
                                                </div>

                                                <div class="col-xl-3 col-sm-6 mt-3">
                                                    <label><i class="bi bi-receipt text-primary me-1"></i>
                                                        Companies/Vendors</label>
                                                    <select name="vendor_id" class="form-control">
                                                        <option disabled>Select One</option>
                                                        @foreach ($Vendor as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ $item->id == $purchase->vendor_id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-xl-3 col-sm-6 mt-3">
                                                    <label><i class="bi bi-file-earmark-text text-primary me-1"></i>
                                                        Company Inv #</label>
                                                    <input name="invoice_no" type="text"
                                                        value="{{ $purchase->invoice_no }}" class="form-control">
                                                </div>

                                                <div class="col-xl-3 col-sm-6 mt-3">
                                                    <label><i class="bi bi-building text-primary me-1"></i>
                                                        Warehouse</label>
                                                    <select name="warehouse_id" class="form-control">
                                                        <option disabled>Select One</option>
                                                        @foreach ($Warehouse as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ $item->id == $purchase->warehouse_id ? 'selected' : '' }}>
                                                            {{ $item->warehouse_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-xl-6 col-sm-6 mt-3">
                                                    <label><i class="bi bi-card-text text-primary me-1"></i>
                                                        Note</label>
                                                    <input name="note" type="text" value="{{ $purchase->note }}"
                                                        class="form-control">
                                                </div>
                                                 <div class="col-xl-6 col-sm-6 mt-3">
                                                    <label><i class="bi bi-card-text text-primary me-1"></i>
                                                        Transport Name</label>
                                                    <input name="job_description" type="text"
                                                        class="form-control" value="{{ $purchase->job_description }}">
                                                </div>
                                            </div>

                                            <!-- Items (Desktop table) -->
                                            <div class="pe-desk" style="max-height: 320px; overflow-y: auto;">
                                                <table class="table mt-3 table-bordered pe-tbl">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Product</th>
                                                            <th>Item Code</th>
                                                            <th>Brand</th>
                                                            <th>Unit</th>
                                                            <th>Price</th>
                                                            <th>Discount</th>
                                                            <th>Qty</th>
                                                            <th>Total</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchaseItems">
                                                        @foreach($purchase->items as $item)
                                                        <tr class="pec-row" data-rid="{{ $loop->index }}">
                                                            <!-- Product -->
                                                            <td>
                                                                <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $item->product->item_name ?? 'NULL' }}" readonly>
                                                            </td>

                                                            <!-- Item Code -->
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $item->product->item_code ?? 'NULL' }}" readonly>
                                                            </td>

                                                            <!-- Brand -->
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $item->product->brand->name ?? 'NULL' }}" readonly>
                                                            </td>

                                                            <!-- Unit -->
                                                            <td>
                                                                <input type="text" name="unit[]" class="form-control"
                                                                    value="{{ $item->unit ?? 'NULL' }}">
                                                            </td>

                                                            <!-- Price -->
                                                            <td>
                                                                <input type="number" step="0.01" name="price[]" class="form-control price"
                                                                    value="{{ $item->price ?? 0 }}">
                                                            </td>

                                                            <!-- Discount -->
                                                            <td>
                                                                <input type="number" step="0.01" name="item_disc[]" class="form-control item_disc"
                                                                    value="{{ $item->item_discount ?? 0 }}">
                                                            </td>

                                                            <!-- Qty -->
                                                            <td>
                                                                <input type="number" name="qty[]" class="form-control quantity"
                                                                    value="{{ $item->qty ?? 0 }}">
                                                            </td>

                                                            <!-- Total -->
                                                            <td>
                                                                <input type="text" name="line_total[]" class="form-control row-total"
                                                                    value="{{ $item->line_total ?? 0 }}" readonly>
                                                            </td>

                                                            <!-- Action -->
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Items (Mobile cards) -->
                                            <div class="pec-cards">
                                                @foreach($purchase->items as $item)
                                                <div class="pec-card pec-row" data-rid="{{ $loop->index }}">
                                                    <div class="pec-head">
                                                        <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                                        <input type="text" class="form-control pec-prod"
                                                            value="{{ $item->product->item_name ?? 'NULL' }}" readonly>
                                                        <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                                                    </div>
                                                    <div class="pec-grid">
                                                        <div class="pec-f"><span class="pec-lbl">Item Code</span><div class="item_code"><input type="text" class="form-control" value="{{ $item->product->item_code ?? 'NULL' }}" readonly></div></div>
                                                        <div class="pec-f"><span class="pec-lbl">Brand</span><div class="uom"><input type="text" class="form-control" value="{{ $item->product->brand->name ?? 'NULL' }}" readonly></div></div>
                                                        <div class="pec-f"><span class="pec-lbl">Unit</span><div class="unit"><input type="text" name="unit[]" class="form-control" value="{{ $item->unit ?? 'NULL' }}"></div></div>
                                                        <div class="pec-f"><span class="pec-lbl">Price</span><input type="number" step="0.01" name="price[]" class="form-control price" value="{{ $item->price ?? 0 }}"></div>
                                                        <div class="pec-f"><span class="pec-lbl">Discount</span><input type="number" step="0.01" name="item_disc[]" class="form-control item_disc" value="{{ $item->item_discount ?? 0 }}"></div>
                                                        <div class="pec-f"><span class="pec-lbl">Qty</span><input type="number" name="qty[]" class="form-control quantity" value="{{ $item->qty ?? 0 }}"></div>
                                                        <div class="pec-f"><span class="pec-lbl">Total</span><input type="text" name="line_total[]" class="form-control row-total" value="{{ $item->line_total ?? 0 }}" readonly></div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div class="row g-3 mt-3">
                                                <div class="col-md-3">
                                                    <label>Subtotal</label>
                                                    <input type="text" class="form-control" id="subtotal"
                                                        value="{{ $purchase->subtotal }}" name="subtotal" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Discount</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="discount" id="overallDiscount" value="{{ $purchase->discount }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Extra Cost</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="extra_cost" id="extraCost" value="{{ $purchase->extra_cost }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Net Amount</label>
                                                    <input type="text" name="net_amount" id="netAmount"
                                                        class="form-control fw-bold"
                                                        value="{{ $purchase->net_amount }}" readonly>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-success w-100 mt-4">Update
                                                Purchase</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {

        // ---------- Helpers ----------
        function num(n) {
            return isNaN(parseFloat(n)) ? 0 : parseFloat(n);
        }

        function recalcRow($row) {
            if (!$row || !$row.length) return;
            const qty = num($row.find('.quantity').val());
            const price = num($row.find('.price').val());
            const disc = num($row.find('.item_disc').val());
            let total = (qty * price) - (qty * disc);
            if (total < 0) total = 0;
            $row.find('.row-total').val(total.toFixed(2));
        }

        function recalcSummary() {
            let sub = 0;
            $('.row-total:visible').each(function() {
                sub += num($(this).val());
            });
            $('#subtotal').val(sub.toFixed(2));

            const oDisc = num($('#overallDiscount').val());
            const xCost = num($('#extraCost').val());
            const net = (sub - oDisc + xCost);
            $('#netAmount').val(net.toFixed(2));
        }

        $('#overallDiscount, #extraCost').on('input', function() {
            recalcSummary();
        });

        // ---------- Mobile/Desktop view mode: serialize ONLY the visible field set ----------
        function toggleFormSets() {
            var mobile = window.matchMedia('(max-width: 991.98px)').matches;
            $('#purchaseItems input, #purchaseItems select, #purchaseItems textarea, #purchaseItems button').prop('disabled', mobile);
            $('.pec-cards input, .pec-cards select, .pec-cards textarea, .pec-cards button').prop('disabled', !mobile);
        }
        var _resizeT;
        $(window).on('resize', function() {
            clearTimeout(_resizeT);
            _resizeT = setTimeout(toggleFormSets, 150);
        });

        // ---------- Add row: create BOTH table row + mobile card (same data-rid) ----------
        function appendBlankRow() {
            const rid = $('#purchaseItems tr').length;

            const trHtml = `
        <tr class="pec-row" data-rid="${rid}">
            <td>
                <input type="hidden" name="product_id[]" class="product_id">
                <input type="text" class="form-control productSearch" placeholder="Enter product name..." autocomplete="off">
                <ul class="searchResults list-group mt-1"></ul>
            </td>
            <td class="item_code border"><input type="text" name="item_code[]" class="form-control" readonly></td>
            <td class="uom border"><input type="text" name="uom[]" class="form-control" readonly></td>
            <td class="unit border"><input type="text" name="unit[]" class="form-control" readonly></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="1"></td>
            <td><input type="number" step="0.01" name="item_disc[]" class="form-control item_disc" value=""></td>
            <td class="qty"><input type="number" name="qty[]" class="form-control quantity" value="" min="1"></td>
            <td class="total border"><input type="text" name="line_total[]" class="form-control row-total" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
        </tr>`;
            $('#purchaseItems').append(trHtml);

            const cardHtml = `
        <div class="pec-card pec-row" data-rid="${rid}">
            <div class="pec-head">
                <div class="pec-search-wrap">
                    <input type="hidden" name="product_id[]" class="product_id">
                    <input type="text" class="form-control productSearch" placeholder="Enter product name..." autocomplete="off">
                    <ul class="searchResults list-group mt-1"></ul>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
            </div>
            <div class="pec-grid">
                <div class="pec-f"><span class="pec-lbl">Item Code</span><div class="item_code"><input type="text" name="item_code[]" class="form-control" readonly></div></div>
                <div class="pec-f"><span class="pec-lbl">Brand</span><div class="uom"><input type="text" name="uom[]" class="form-control" readonly></div></div>
                <div class="pec-f"><span class="pec-lbl">Unit</span><div class="unit"><input type="text" name="unit[]" class="form-control" readonly></div></div>
                <div class="pec-f"><span class="pec-lbl">Price</span><input type="number" step="0.01" name="price[]" class="form-control price" value="1"></div>
                <div class="pec-f"><span class="pec-lbl">Discount</span><input type="number" step="0.01" name="item_disc[]" class="form-control item_disc" value=""></div>
                <div class="pec-f"><span class="pec-lbl">Qty</span><input type="number" name="qty[]" class="form-control quantity" value="" min="1"></div>
                <div class="pec-f"><span class="pec-lbl">Total</span><input type="text" name="line_total[]" class="form-control row-total" readonly></div>
            </div>
        </div>`;
            $('.pec-cards').append(cardHtml);
            toggleFormSets();
        }

        // Edit form me bhi ek extra blank row ho, taake user naya product search kare
        appendBlankRow();

        // ---------- Product Search (AJAX) ----------
        $(document).on('keydown', '.productSearch', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        $(document).on('keyup', '.productSearch', function(e) {
            const $input = $(this);
            const q = $input.val().trim();
            const $row = $input.closest('.pec-row');
            const $box = $row.find('.searchResults');

            const isNavKey = ['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key);
            if (isNavKey && $box.children('.search-result-item').length) {
                const $items = $box.children('.search-result-item');
                let idx = $items.index($items.filter('.active'));
                if (e.key === 'ArrowDown') {
                    idx = (idx + 1) % $items.length;
                    $items.removeClass('active');
                    $items.eq(idx).addClass('active');
                    e.preventDefault();
                    return;
                }
                if (e.key === 'ArrowUp') {
                    idx = (idx <= 0 ? $items.length - 1 : idx - 1);
                    $items.removeClass('active');
                    $items.eq(idx).addClass('active');
                    e.preventDefault();
                    return;
                }
                if (e.key === 'Enter') {
                    if (idx >= 0) {
                        $items.eq(idx).trigger('click');
                    } else if ($items.length === 1) {
                        $items.eq(0).trigger('click');
                    }
                    e.preventDefault();
                    return;
                }
            }

            if (q.length === 0) {
                $box.empty();
                return;
            }

            $.ajax({
                url: "{{ route('search-products') }}",
                type: 'GET',
                data: { q },
                success: function(data) {
                    let html = '';
                    (data || []).forEach(p => {
                        const brand = (p.brand && p.brand.name) ? p.brand.name : '';
                        const unit = (p.unit_id ?? '');
                        const price = (p.wholesale_price ?? 0);
                        const code = (p.item_code ?? '');
                        const name = (p.item_name ?? '');
                        const id = (p.id ?? '');
                        html += `
                        <li class="list-group-item search-result-item"
                            tabindex="0"
                            data-product-id="${id}"
                            data-product-name="${name}"
                            data-product-uom="${brand}"
                            data-product-unit="${unit}"
                            data-product-code="${code}"
                            data-price="${price}">
                            ${name} - ${code} - Rs. ${price}
                        </li>`;
                    });
                    $box.html(html);

                    $box.children('.search-result-item').first().addClass('active');
                },
                error: function() {
                    $box.empty();
                }
            });
        });

        // Click/Enter on suggestion
        $(document).on('click', '.search-result-item', function() {
            const $li = $(this);
            const $row = $li.closest('.pec-row');

            $row.find('.productSearch').val($li.data('product-name'));
            $row.find('.item_code input').val($li.data('product-code'));
            $row.find('.uom input').val($li.data('product-uom'));
            $row.find('.unit input').val($li.data('product-unit'));
            $row.find('.price').val($li.data('price'));

            $row.find('.product_id').val($li.data('product-id'));

            $row.find('.quantity').val(1);
            $row.find('.item_disc').val(0);

            recalcRow($row);
            recalcSummary();

            $row.find('.searchResults').empty();

            appendBlankRow();
            $('.pec-row:visible .productSearch').last().focus();
        });

        $(document).on('keydown', '.searchResults .search-result-item', function(e) {
            if (e.key === 'Enter') {
                $(this).trigger('click');
            }
        });

        // Row calculations
        $(document).on('input', '.quantity, .price, .item_disc', function() {
            const $row = $(this).closest('.pec-row');
            recalcRow($row);
            recalcSummary();
        });

        // Remove row (removes matching desktop row + mobile card)
        $(document).on('click', '.remove-row', function() {
            const rid = $(this).closest('.pec-row').data('rid');
            $('.pec-row[data-rid="' + rid + '"]').remove();
            recalcSummary();
        });

        // init values
        recalcRow($('#purchaseItems tr:first'));
        recalcSummary();
        toggleFormSets();
    });
</script>

@endsection