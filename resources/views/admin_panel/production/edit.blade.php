@extends('admin_panel.layout.app')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">
            <div class="row">
                <div class="body-wrapper">
                    <div class="bodywrapper__inner">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="page-title m-0">🏭 Edit Production Entry — {{ $entry->entry_no }}</h2>
                            <a href="{{ route('production.index') }}" class="btn btn-danger">Back</a>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $err)
                                <p class="mb-0">{{ $err }}</p>
                            @endforeach
                        </div>
                        @endif
                        
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('production.update', $entry->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label>Production Date</label>
                                            <input type="date" name="production_date" value="{{ $entry->production_date }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Batch / Entry #</label>
                                            <input type="text" name="entry_no" value="{{ $entry->entry_no }}" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Source (Kitchen/Warehouse)</label>
                                            <select name="source" class="form-control">
                                                <option value="kitchen" {{ $entry->source == 'kitchen' ? 'selected' : '' }}>Main Kitchen</option>
                                                <option value="warehouse" {{ $entry->source == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Notes</label>
                                            <input type="text" name="notes" class="form-control" placeholder="Optional notes for this batch..." value="{{ $entry->notes }}">
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="40%">Product</th>
                                                    <th>Code</th>
                                                    <th>Unit</th>
                                                    <th>Entered Qty (KG/Pc)</th>
                                                    <th>Cost (Rs)</th>
                                                    <th>Note</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productionItems">
                                                @foreach($items as $item)
                                                <tr>
                                                    <td>
                                                        <select name="product_id[]" class="form-control select2 product-select" required>
                                                            <option value="">Search Product...</option>
                                                            @foreach($products as $p)
                                                                <option value="{{ $p->id }}" 
                                                                    data-code="{{ $p->item_code }}"
                                                                    data-unit="{{ $p->unit_type === 'kg' ? 'KG' : ($p->unit->name ?? 'Pc') }}"
                                                                    data-is-gram="{{ $p->unit_type === 'kg' || str_contains(strtolower($p->item_name), 'gram') || str_contains(strtolower($p->unit->name ?? ''), 'gram') ? '1' : '0' }}"
                                                                    {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->item_code }} - {{ $p->item_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        
                                                        <div class="variant-container mt-2" style="display:none;">
                                                            <select name="variant_id[]" class="form-control variant-select">
                                                                <option value="">Select Size (Optional)</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" class="form-control code-display" value="{{ $item->item_code }}" readonly></td>
                                                    <td><input type="text" class="form-control unit-display" value="{{ $item->unit }}" readonly></td>
                                                    <td>
                                                        <input type="number" step="0.001" name="qty[]" class="form-control qty-input" required min="0.001" value="{{ $item->qty_entered }}">
                                                        <small class="text-muted conversion-display"></small>
                                                    </td>
                                                    <td><input type="number" step="0.01" min="0" name="item_cost[]" class="form-control item-cost" value="0" placeholder="0"></td>
                                                    <td><input type="text" name="item_note[]" class="form-control" value="{{ $item->notes }}"></td>
                                                    <td><button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                                                     <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
                                        <span style="font-weight:600;">Total Cost: <strong id="editTotalCost" style="color:var(--pc-accent);">Rs {{ number_format($entry->production_cost ?? 0, 0) }}</strong></span>
                                        <button type="submit" class="btn btn-primary btn-lg px-5">💾 Update Entry</button>
                                    </div>

                                    <hr>
                                    <h5 style="font-weight:700;"><i class="fas fa-boxes"></i> Raw Materials Consumed</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="35%">Raw Material</th>
                                                    <th width="20%">Qty Used</th>
                                                    <th width="20%">Cost/Unit (Rs)</th>
                                                    <th width="20%">Total Cost</th>
                                                    <th style="width:50px;">Action</th>
                                                </tr>
                                            </thead>
                                             <tbody id="rmEditBody">
                                                  @forelse($rawMaterialUsage as $rmu)
                                                  <tr class="rm-row">
                                                      <td>
                                                          <input type="hidden" name="rm_type[]" class="rm-type" value="{{ $rmu->ingredient_product_id ? 'product' : 'rm' }}">
                                                          <select name="rm_id[]" class="form-control">
                                                              <option value="">Select...</option>
                                                              <optgroup label="Raw Materials">
                                                              @foreach($rawMaterials as $rm)
                                                               <option value="{{ $rm->id }}" data-type="rm" data-cost="{{ $rm->lastPurchaseCost() }}" {{ $rmu->raw_material_id == $rm->id ? 'selected' : '' }}>
                                                                   [RM] {{ $rm->name }} ({{ $rm->unit }}) [Stock: {{ $rm->currentStock() }}]
                                                               </option>
                                                               @endforeach
                                                               </optgroup>
                                                               <optgroup label="Semi-Finished / Base Products">
                                                               @foreach($products as $p)
                                                               <option value="{{ $p->id }}" data-type="product" data-cost="{{ $p->price ?? 0 }}" {{ $rmu->ingredient_product_id == $p->id ? 'selected' : '' }}>
                                                                   [Product] {{ $p->item_code }} - {{ $p->item_name }} ({{ strtoupper($p->unit_type ?? 'Piece') }})
                                                               </option>
                                                               @endforeach
                                                               </optgroup>
                                                           </select>
                                                      </td>
                                                      <td><input type="number" step="0.01" name="rm_qty[]" class="form-control rm-qty" value="{{ $rmu->qty_used }}" min="0"></td>
                                                      <td><input type="number" step="0.01" min="0" name="rm_cost[]" class="form-control rm-cost" value="{{ $rmu->cost_per_unit }}" placeholder="0"></td>
                                                      <td><span class="rm-total fw-bold">Rs {{ number_format($rmu->total_cost, 0) }}</span></td>
                                                      <td><button type="button" class="btn btn-danger btn-sm remove-rm-row"><i class="fas fa-times"></i></button></td>
                                                  </tr>
                                                  @empty
                                                  @endforelse
                                              </tbody>
                                          </table>
                                      </div>
                                      <button type="button" class="btn btn-success btn-sm" id="addEditRmRow">+ Add More Raw Material</button>

{{-- Hidden template for manual RM rows --}}
<table style="display:none;" id="editRmTemplateWrap">
  <tr class="rm-row manual-rm-row">
    <td>
      <input type="hidden" name="rm_type[]" class="rm-type" value="rm">
      <select name="rm_id[]" class="form-control">
        <option value="">Select Raw Material...</option>
        <optgroup label="Raw Materials">
        @foreach($rawMaterials as $rm)
        <option value="{{ $rm->id }}" data-type="rm" data-cost="{{ $rm->lastPurchaseCost() }}">[RM] {{ $rm->name }} ({{ $rm->unit }})</option>
        @endforeach
        </optgroup>
        <optgroup label="Semi-Finished / Base Products">
        @foreach($products as $p)
        <option value="{{ $p->id }}" data-type="product" data-cost="{{ $p->price ?? 0 }}">[Product] {{ $p->item_code }} - {{ $p->item_name }} ({{ strtoupper($p->unit_type ?? 'Piece') }})</option>
        @endforeach
        </optgroup>
      </select>
    </td>
    <td><input type="number" step="0.01" name="rm_qty[]" class="form-control rm-qty" value="0" min="0"></td>
    <td><input type="number" step="0.01" min="0" name="rm_cost[]" class="form-control rm-cost" value="0" placeholder="0"></td>
    <td><span class="rm-total fw-bold">0</span></td>
    <td><button type="button" class="btn btn-danger btn-sm remove-rm-row"><i class="fas fa-times"></i></button></td>
  </tr>
</table>
                                </form>
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
    $('.select2').select2({ width: '100%', placeholder: 'Select product...' });

    // Cost calculation
    function calcEditCost() {
        var total = 0;
        $('.item-cost').each(function() { total += parseFloat($(this).val()) || 0; });
        $('#rmEditBody .rm-row').each(function() {
            var qty = parseFloat($(this).find('.rm-qty').val()) || 0;
            var cost = parseFloat($(this).find('.rm-cost').val()) || 0;
            $(this).find('.rm-total').text('Rs ' + (qty * cost).toLocaleString());
            total += qty * cost;
        });
        $('#editTotalCost').text('Rs ' + total.toLocaleString());
    }

    $(document).on('input', '.item-cost', calcEditCost);
    $(document).on('input', '.rm-qty, .rm-cost', calcEditCost);

    // Auto-fill rm_cost & rm_type from selected option
    $(document).on('change', '#rmEditBody select[name="rm_id[]"]', function() {
        var opt = $(this).find(':selected');
        var cost = opt.data('cost') || 0;
        var type = opt.data('type') || 'rm';
        $(this).closest('tr').find('.rm-cost').val(cost);
        $(this).closest('tr').find('.rm-type').val(type);
        calcEditCost();
    });

    // Add raw material row
    $('#addEditRmRow').click(function() {
        var tmpl = document.querySelector('#editRmTemplateWrap .rm-row');
        var newRow = tmpl.cloneNode(true);
        newRow.className = 'rm-row manual-rm-row';
        $('#rmEditBody').append(newRow);
        calcEditCost();
    });

    $(document).on('click', '.remove-rm-row', function() {
        if ($('#rmEditBody tr').length > 1) {
            $(this).closest('tr').remove();
            calcEditCost();
        }
    });

    $(document).on('change', '.product-select', function() {
        let opt = $(this).find(':selected');
        let row = $(this).closest('tr');
        row.find('.code-display').val(opt.data('code'));
        row.find('.unit-display').val(opt.data('unit'));
        updateConversion(row);
        loadEditBom(row);
        
        let productId = $(this).val();
        let variantSelect = row.find('.variant-select');
        let variantContainer = row.find('.variant-container');
        let isGram = opt.data('is-gram') == '1';
        
        if (productId) {
            variantContainer.show();
            variantSelect.html('<option value="">Loading sizes...</option>');
            $.ajax({
                url: '/pos/product-variants/' + productId,
                type: 'GET',
                success: function(res) {
                    variantSelect.html('<option value="">Select Size (Optional)</option>');
                    if (res.variants && res.variants.length > 0) {
                        res.variants.forEach(function(v) {
                            variantSelect.append('<option value="' + v.id + '" data-size-value="' + (v.size_value || 1) + '">' + v.size_label + '</option>');
                        });
                    } else {
                        variantContainer.hide();
                        variantSelect.html('<option value="">No Sizes</option>');
                    }
                },
                error: function() {
                    variantContainer.hide();
                    variantSelect.html('<option value="">Select Size (Optional)</option>');
                }
            });
        } else {
            variantContainer.hide();
            variantSelect.html('<option value="">Select Size (Optional)</option>');
        }
    });

    $(document).on('change', '.variant-select', function() {
        let row = $(this).closest('tr');
        updateConversion(row);
        loadEditBom(row);
    });

    $(document).on('input', '.qty-input', function() {
        updateConversion($(this).closest('tr'));
        loadEditBom($(this).closest('tr'));
    });

    function loadEditBom(row) {
        var productId = row.find('.product-select').val();
        var variantId = row.find('.variant-select').val() || '';
        var qty = parseFloat(row.find('.qty-input').val()) || 1;
        if (!productId) return;

        var selectedVarOpt = row.find('.variant-select option:selected');
        var varSizeVal = parseFloat(selectedVarOpt.data('size-value')) || 0;
        var multiplier = varSizeVal > 0 ? varSizeVal : 1;

        var rowId = row.attr('data-row-id');
        if (!rowId) {
            rowId = 'prow_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
            row.attr('data-row-id', rowId);
        }

        // Remove old BOM rows for THIS specific product row only
        $('#rmEditBody .bom-row[data-parent-row="' + rowId + '"]').remove();

        var url = '/products/' + productId + '/bom-raw-materials';
        if (variantId) {
            url += '?variant_id=' + variantId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(bom) {
                if (!bom || bom.length === 0) return;
                var tbody = document.getElementById('rmEditBody');

                bom.forEach(function(item) {
                    var tr = document.createElement('tr');
                    tr.className = 'rm-row bom-row';
                    tr.setAttribute('data-parent-row', rowId);
                    var isProduct = !!item.ingredient_product_id;
                    var itemId = isProduct ? item.ingredient_product_id : item.raw_material_id;
                    var itemType = isProduct ? 'product' : 'rm';
                    var itemName = isProduct 
                        ? (item.ingredient_product ? item.ingredient_product.item_name + ' [Base Product]' : 'Product #' + itemId)
                        : (item.raw_material ? item.raw_material.name + ' (' + (item.raw_material.unit || '') + ')' : 'RM #' + itemId);

                    if (item.is_custom_variant_bom) {
                        itemName += ' [Custom Variant Recipe]';
                    }

                    var itemCost = parseFloat(item.unit_cost) || 0;
                    var effectiveQty = item.is_custom_variant_bom ? qty : (qty * multiplier);
                    var requiredQty = (parseFloat(item.qty_per_unit) * effectiveQty);
                    var totalItemCost = requiredQty * itemCost;

                    tr.innerHTML = '<td>' +
                        '<input type="hidden" name="rm_type[]" class="rm-type" value="' + itemType + '">' +
                        '<input type="hidden" name="rm_id[]" value="' + itemId + '">' +
                        '<input type="text" class="form-control fw-bold" value="' + itemName + '" readonly style="background:#f0f0f0;">' +
                        '</td>' +
                        '<td><input type="number" step="0.01" name="rm_qty[]" class="form-control rm-qty" value="' + requiredQty.toFixed(2) + '" min="0" readonly></td>' +
                        '<td><input type="number" step="0.01" min="0" name="rm_cost[]" class="form-control rm-cost" value="' + itemCost.toFixed(2) + '" placeholder="0"></td>' +
                        '<td><span class="rm-total fw-bold">Rs ' + totalItemCost.toLocaleString() + '</span></td>' +
                        '<td><button type="button" class="btn btn-danger btn-sm remove-rm-row"><i class="fas fa-times"></i></button></td>';

                    tbody.insertBefore(tr, tbody.firstChild);
                });
                calcEditCost();
            }
        });
    }

    function updateConversion(row) {
        let qty = parseFloat(row.find('.qty-input').val()) || 0;
        let isGram = row.find('.product-select option:selected').data('is-gram') == '1';
        if (isGram && qty > 0) {
            let selectedVarOpt = row.find('.variant-select option:selected');
            var varSizeVal = parseFloat(selectedVarOpt.data('size-value')) || 0;
            var multiplier = varSizeVal > 0 ? varSizeVal : 1;
            let grams = qty * multiplier * 1000;
            row.find('.conversion-display').text('(' + grams.toLocaleString() + ' grams to stock)');
        } else {
            row.find('.conversion-display').text('');
        }
    }

    $('#productionItems tr').each(function() {
        updateConversion($(this));
    });

    $('#addRow').click(function() {
        let newRow = $('#productionItems tr:first').clone();
        newRow.removeAttr('data-row-id');
        newRow.find('input').val('');
        newRow.find('.conversion-display').text('');
        newRow.find('.variant-container').hide();
        newRow.find('.variant-select').html('<option value="">Select Size (Optional)</option>');
        newRow.find('.select2-container').remove();
        $('#productionItems').append(newRow);
        newRow.find('.select2').select2({ width: '100%' });
        calcEditCost();
    });

    $(document).on('click', '.remove-row', function() {
        if ($('#productionItems tr').length > 1) {
            let row = $(this).closest('tr');
            let rowId = row.attr('data-row-id');
            if (rowId) {
                $('#rmEditBody .bom-row[data-parent-row="' + rowId + '"]').remove();
            }
            row.remove();
        }
        calcEditCost();
    });

    calcEditCost();
});
</script>
@endsection
