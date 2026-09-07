@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-green: #0e8349;
  --pc-green-hover: #0b6b3b;
  --pc-bg: #f8fafc;
  --pc-card-bg: #ffffff;
  --pc-border: #e2e8f0;
  --pc-text-dark: #0f172a;
  --pc-text-muted: #64748b;
  --pc-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.pc-container * {
  font-family: var(--pc-font);
  box-sizing: border-box;
}

.pc-container {
  background-color: var(--pc-bg);
  min-height: 100vh;
  padding: 1.25rem;
  overflow-x: hidden !important;
}

/* ══════════ TOP HEADER BAR ══════════ */
.pc-header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid var(--pc-border);
  border-radius: 8px;
  padding: 0.85rem 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.pc-header-title {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.pc-header-title h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--pc-text-dark);
  margin: 0;
}

.pc-header-title .pc-icon {
  font-size: 1.4rem;
  color: var(--pc-green);
  line-height: 1;
}

.pc-badge-pill {
  background: #e0e7ff;
  color: #4338ca;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 12px;
  padding: 3px 10px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: inline-block;
}

.pc-btn-back {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #475569;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.45rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: all 0.2s ease;
}

.pc-btn-back:hover {
  background: #f1f5f9;
  color: #0f172a;
}

/* ══════════ CARDS ══════════ */
.pc-card {
  background: var(--pc-card-bg);
  border: 1px solid var(--pc-border);
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

.pc-card-header {
  font-size: 0.98rem;
  font-weight: 700;
  color: var(--pc-text-dark);
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
  margin-bottom: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.pc-card-header i {
  color: var(--pc-green);
  font-size: 1.15rem;
  margin-right: 0.4rem;
}

.pc-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.4rem;
  display: block;
}

.pc-input, .pc-select {
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 0.55rem 0.75rem;
  font-size: 0.88rem;
  color: #0f172a;
  background-color: #ffffff;
  width: 100%;
  transition: border-color 0.2s;
}

.pc-input:focus, .pc-select:focus {
  border-color: var(--pc-green);
  outline: none;
  box-shadow: 0 0 0 3px rgba(14, 131, 73, 0.12);
}

.pc-input[readonly] {
  background-color: #f1f5f9;
  color: #475569;
  font-weight: 600;
}

/* ══════════ TABLE STYLING ══════════ */
.pc-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #e2e8f0;
}

.pc-table th {
  background-color: #ebf5f0 !important;
  color: #1e293b;
  font-size: 0.82rem;
  font-weight: 700;
  padding: 0.75rem 0.85rem;
  border-bottom: 1px solid #cbd5e1;
  border-right: 1px solid #e2e8f0;
  text-align: center;
}

.pc-table td {
  padding: 0.65rem 0.85rem;
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  font-size: 0.88rem;
  color: #0f172a;
  vertical-align: middle;
}

.pc-table td:last-child, .pc-table th:last-child {
  border-right: none;
}

/* Select2 overrides for table */
.pc-container .select2-container--default .select2-selection--single {
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  height: 36px !important;
  padding: 3px 6px !important;
}

.pc-container .select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #0f172a !important;
  font-size: 0.85rem !important;
  font-weight: 600 !important;
  line-height: 28px !important;
}

.pc-container .select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 34px !important;
}

/* MOBILE RESPONSIVE */
.mobile-lbl { display: none; }

@media (max-width: 767.98px) {
  .pc-container {
    padding: 0.6rem 0.4rem !important;
  }

  .pc-header-bar {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 0.75rem !important;
  }

  .pc-table, .pc-table tbody, .pc-table tr, .pc-table td {
    display: block !important;
    width: 100% !important;
  }

  .pc-table thead { display: none !important; }

  .pc-table tbody tr {
    background: #ffffff !important;
    border: 1px solid var(--pc-border) !important;
    border-radius: 8px !important;
    padding: 0.85rem !important;
    margin-bottom: 0.85rem !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    position: relative !important;
  }

  .pc-table td {
    padding: 0.35rem 0 !important;
    border: none !important;
    text-align: left !important;
  }

  .mobile-lbl {
    display: block !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    color: #64748b !important;
    margin-bottom: 0.2rem !important;
    text-transform: uppercase !important;
  }
}
</style>

<div class="pc-container">

  {{-- ══════════ TOP HEADER BAR ══════════ --}}
  <div class="pc-header-bar">
    <div class="pc-header-title">
      <i class="bi bi-gear-wide-connected pc-icon"></i>
      <h2>Own Production Entry</h2>
      <span class="pc-badge-pill">New Batch Form</span>
    </div>

    <a href="{{ route('production.index') }}" class="pc-btn-back">
      <i class="bi bi-arrow-left"></i> Back to Batches
    </a>
  </div>

  <form action="{{ route('production.store') }}" method="POST">
    @csrf

    {{-- FLASH MESSAGES --}}
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:10px;">
      <strong><i class="bi bi-check-circle me-1"></i>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border:none;border-radius:10px;">
      <strong><i class="bi bi-exclamation-triangle me-1"></i>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border:none;border-radius:10px;">
      <strong><i class="bi bi-exclamation-triangle me-1"></i>Error!</strong>
      <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ══════════ MAIN CARD: BATCH DETAILS ══════════ --}}
    <div class="pc-card">
      <div class="pc-card-header">
        <div><i class="bi bi-clipboard-data"></i> Production Batch Details</div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-sm-6 col-md-3">
          <label class="pc-label"><i class="bi bi-calendar-event me-1 text-success"></i> Production Date</label>
          <input type="date" name="production_date" value="{{ date('Y-m-d') }}" class="pc-input" required>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <label class="pc-label"><i class="bi bi-hash me-1 text-success"></i> Batch / Entry #</label>
          <input type="text" name="entry_no" value="PROD-{{ date('Ymd-His') }}" class="pc-input" readonly>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <label class="pc-label"><i class="bi bi-geo-alt me-1 text-success"></i> Source</label>
          <select name="source" class="pc-select">
            <option value="branch">Branch: {{ active_branch_name() }}</option>
            <option value="warehouse">Warehouse Production</option>
          </select>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <label class="pc-label"><i class="bi bi-building me-1 text-success"></i> Target Warehouse</label>
          <select name="warehouse_id" class="pc-select">
            <option value="">Select Warehouse (Optional for Branch)...</option>
            @foreach(\App\Models\Warehouse::orderBy('warehouse_name')->get() as $w)
            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
              {{ $w->warehouse_name ?? ($w->name ?? 'Warehouse #'.$w->id) }}
            </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <label class="pc-label"><i class="bi bi-card-text me-1 text-success"></i> Notes</label>
          <input type="text" name="notes" class="pc-input" placeholder="Optional notes for batch...">
        </div>
      </div>
    </div>

    {{-- ══════════ FINISHED PRODUCTS PRODUCED ══════════ --}}
    <div class="pc-card">
      <div class="pc-card-header">
        <div><i class="bi bi-box-seam-fill"></i> Finished Products Produced</div>
      </div>

      <div class="table-responsive">
        <table class="pc-table">
          <thead>
            <tr>
              <th class="text-start" style="width:30%;">Product</th>
              <th style="width:12%;">Code</th>
              <th style="width:10%;">Unit</th>
              <th style="width:15%;">Entered Qty (KG/Pc)</th>
              <th style="width:15%;">Cost (Rs)</th>
              <th style="width:13%;">Note</th>
              <th style="width:50px;">Action</th>
            </tr>
          </thead>
          <tbody id="productionItems">
            <tr>
              <td>
                <span class="mobile-lbl">Product</span>
                <select name="product_id[]" class="pc-select select2 product-select" required style="width:100%;">
                  <option value="">Search Product...</option>
                  @foreach($products as $p)
                  <option value="{{ $p->id }}"
                    data-code="{{ $p->item_code }}"
                    data-unit="{{ $p->unit_type === 'kg' ? 'KG' : ($p->unit->name ?? 'Pc') }}"
                    data-is-gram="{{ $p->unit_type === 'kg' || str_contains(strtolower($p->item_name), 'gram') || str_contains(strtolower($p->unit->name ?? ''), 'gram') ? '1' : '0' }}">
                    {{ $p->item_code }} - {{ $p->item_name }}
                  </option>
                  @endforeach
                </select>
                <div class="variant-container mt-1" style="display:none;">
                  <select name="variant_id[]" class="pc-select variant-select" style="width:100%;">
                    <option value="">Select Size (Optional)</option>
                  </select>
                </div>
              </td>
              <td>
                <span class="mobile-lbl">Code</span>
                <input type="text" class="pc-input code-display text-center" readonly>
              </td>
              <td>
                <span class="mobile-lbl">Unit</span>
                <input type="text" class="pc-input unit-display text-center fw-bold" readonly>
              </td>
              <td>
                <span class="mobile-lbl">Entered Qty</span>
                <input type="number" step="0.001" name="qty[]" class="pc-input qty-input text-center fw-bold" required min="0.001" placeholder="0.000">
                <small class="text-muted d-block conversion-display" style="font-size:0.75rem;"></small>
              </td>
              <td>
                <span class="mobile-lbl">Cost (Rs)</span>
                <input type="number" step="0.01" min="0" name="item_cost[]" class="pc-input item-cost text-center fw-bold" value="0" placeholder="0.00">
              </td>
              <td>
                <span class="mobile-lbl">Note</span>
                <input type="text" name="item_note[]" class="pc-input" placeholder="Item note...">
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row" style="background:#ef4444; border:none; border-radius:6px; width:30px; height:30px;" title="Remove row">
                  <i class="bi bi-trash-fill text-white"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <button type="button" class="btn btn-outline-success btn-sm fw-bold px-3 py-2" id="addRow" style="border-radius:6px;">
          <i class="bi bi-plus-lg me-1"></i> Add More Product Row
        </button>
      </div>
    </div>

    {{-- ══════════ RAW MATERIALS CONSUMED ══════════ --}}
    <div class="pc-card">
      <div class="pc-card-header">
        <div><i class="bi bi-box-seam"></i> Raw Materials Consumed</div>
      </div>

      <div class="table-responsive">
        <table class="pc-table">
          <thead>
            <tr>
              <th class="text-start" style="width:35%;">Raw Material</th>
              <th style="width:20%;">Qty Used</th>
              <th style="width:20%;">Cost/Unit (Rs)</th>
              <th style="width:20%;">Total Cost</th>
              <th style="width:50px;">Action</th>
            </tr>
          </thead>
          <tbody id="rmUsageBody">
            <tr class="rm-row manual-rm-row" id="rmManualTemplate" style="display:none;">
              <td>
                <span class="mobile-lbl">Raw Material / Product Ingredient</span>
                <input type="hidden" name="rm_type[]" class="rm-type" value="rm">
                <select name="rm_id[]" class="pc-select" style="width:100%;">
                  <option value="">Select Ingredient / Raw Material...</option>
                  <optgroup label="Raw Materials">
                    @foreach($rawMaterials as $rm)
                    <option value="{{ $rm->id }}" data-type="rm" data-stock="{{ $rm->currentStock() }}" data-cost="{{ $rm->lastPurchaseCost() }}">
                      [Raw Material] {{ $rm->name }} (Purchase: {{ $rm->unit }}, Recipe: {{ $rm->consumption_unit ?? $rm->unit }}) [Stock: {{ $rm->currentStock() }}]
                    </option>
                    @endforeach
                  </optgroup>
                  <optgroup label="Semi-Finished / Base Products">
                    @foreach($products as $pItem)
                    <option value="{{ $pItem->id }}" data-type="product" data-stock="{{ $pItem->stock->qty ?? 0 }}" data-cost="{{ $pItem->price }}">
                      [Product] {{ $pItem->item_code }} - {{ $pItem->item_name }}
                    </option>
                    @endforeach
                  </optgroup>
                </select>
              </td>
              <td>
                <span class="mobile-lbl">Qty Used</span>
                <input type="number" step="0.01" name="rm_qty[]" class="pc-input rm-qty text-center" value="0" min="0">
              </td>
              <td>
                <span class="mobile-lbl">Cost/Unit</span>
                <input type="number" step="0.01" min="0" name="rm_cost[]" class="pc-input rm-cost text-center" value="0" placeholder="0">
              </td>
              <td class="text-center">
                <span class="mobile-lbl">Total Cost</span>
                <span class="rm-total fw-bold text-success" style="font-size:.9rem;">0</span>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-rm-row" style="background:#ef4444; border:none; border-radius:6px; width:30px; height:30px;" title="Remove">
                  <i class="bi bi-trash-fill text-white"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <button type="button" class="btn btn-outline-success btn-sm fw-bold px-3 py-2" id="addRmRow" style="border-radius:6px;">
          <i class="bi bi-plus-lg me-1"></i> Add More Raw Material / Ingredient
        </button>

        <div class="d-flex align-items-center gap-3 ms-auto">
          <span class="fw-bold fs-6 text-dark">Total Batch Cost: <strong id="totalProdCost" class="text-success fs-5">Rs 0</strong></span>
          <button type="submit" class="btn text-white fw-bold px-4 py-2" style="background-color:#0e8349; border-radius:6px; box-shadow: 0 4px 10px rgba(14, 131, 73, 0.2);">
            <i class="bi bi-floppy-fill me-1"></i> Save Production Batch
          </button>
        </div>
      </div>
    </div>

  </form>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  $('.select2').select2({ width: '100%', placeholder: 'Select product...' });

  function calcTotalCost() {
    var total = 0;
    $('.item-cost').each(function() { total += parseFloat($(this).val()) || 0; });
    $('.rm-row').each(function() {
      var qty = parseFloat($(this).find('.rm-qty').val()) || 0;
      var cost = parseFloat($(this).find('.rm-cost').val()) || 0;
      $(this).find('.rm-total').text((qty * cost).toLocaleString());
      total += qty * cost;
    });
    $('#totalProdCost').text('Rs ' + total.toLocaleString());
  }

  $(document).on('input', '.item-cost', calcTotalCost);
  $(document).on('input', '.rm-qty, .rm-cost', function() {
    calcTotalCost();
  });

  $(document).on('change', '#rmUsageBody select[name="rm_id[]"]', function() {
    var opt = $(this).find(':selected');
    var cost = opt.data('cost') || 0;
    var type = opt.data('type') || 'rm';
    var row = $(this).closest('tr');
    row.find('.rm-cost').val(cost);
    row.find('.rm-type').val(type);
    calcTotalCost();
  });

  $('#addRmRow').click(function() {
    var tmpl = document.getElementById('rmManualTemplate');
    var newRow = tmpl.cloneNode(true);
    newRow.style.display = '';
    newRow.className = 'rm-row manual-rm-row';
    newRow.id = '';
    newRow.querySelectorAll('input').forEach(function(el) { el.value = ''; el.readOnly = false; });
    newRow.querySelector('select').disabled = false;
    newRow.querySelector('.rm-type').value = 'rm';
    newRow.querySelector('.rm-total').textContent = '0';
    document.getElementById('rmUsageBody').appendChild(newRow);
  });

  $(document).on('click', '.remove-rm-row', function() {
    if ($('#rmUsageBody tr').length > 1) {
      $(this).closest('tr').remove();
      calcTotalCost();
    }
  });

  $(document).on('change', '.product-select', function() {
    let opt = $(this).find(':selected');
    let row = $(this).closest('tr');
    row.find('.code-display').val(opt.data('code'));
    row.find('.unit-display').val(opt.data('unit'));
    updateConversion(row);
    loadBomForRow(row);

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
    loadBomForRow(row);
  });

  var pendingBomAjax = {};

  function loadBomForRow(row) {
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

    // Abort previous AJAX for this product row to prevent duplicate BOM rows
    if (pendingBomAjax[rowId]) {
      pendingBomAjax[rowId].abort();
    }

    // Remove old BOM rows for THIS specific product row only
    $('#rmUsageBody .bom-row[data-parent-row="' + rowId + '"]').remove();

    var url = '/products/' + productId + '/bom-raw-materials';
    if (variantId) {
      url += '?variant_id=' + variantId;
    }

    pendingBomAjax[rowId] = $.ajax({
      url: url,
      type: 'GET',
      success: function(bom) {
        if (!bom || bom.length === 0) return;
        var tbody = document.getElementById('rmUsageBody');

        bom.forEach(function(item) {
          var tr = document.createElement('tr');
          tr.className = 'rm-row bom-row';
          tr.setAttribute('data-parent-row', rowId);
          var isProduct = !!item.ingredient_product_id;
          var itemId = isProduct ? item.ingredient_product_id : item.raw_material_id;
          var itemType = isProduct ? 'product' : 'rm';
          var itemName = isProduct 
            ? (item.ingredient_product ? item.ingredient_product.item_name + ' [Base Product]' : 'Product #' + itemId)
            : (item.raw_material ? item.raw_material.name + ' (' + (item.raw_material.consumption_unit || item.raw_material.unit || '') + ')' : 'RM #' + itemId);

          if (item.is_custom_variant_bom) {
            itemName += ' [Custom Variant Recipe]';
          }

          var itemCost = parseFloat(item.unit_cost) || 0;
          var effectiveQty = item.is_custom_variant_bom ? qty : (qty * multiplier);
          var requiredQty = (parseFloat(item.qty_per_unit) * effectiveQty);

          tr.innerHTML = '<td>' +
            '<input type="hidden" name="rm_type[]" class="rm-type" value="' + itemType + '">' +
            '<input type="hidden" name="rm_id[]" value="' + itemId + '">' +
            '<input type="text" class="pc-input fw-bold" value="' + itemName + '" readonly style="background:#f1f5f9; color:#0f172a;">' +
            '</td>' +
            '<td><input type="number" step="0.01" name="rm_qty[]" class="pc-input rm-qty text-center" value="' + requiredQty.toFixed(2) + '" min="0" readonly></td>' +
            '<td><input type="number" step="0.01" min="0" name="rm_cost[]" class="pc-input rm-cost text-center" value="' + itemCost.toFixed(2) + '" placeholder="0"></td>' +
            '<td class="text-center"><span class="rm-total fw-bold text-success" style="font-size:.9rem;">0</span></td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-rm-row" style="background:#ef4444; border:none; border-radius:6px; width:30px; height:30px;" title="Remove"><i class="bi bi-trash-fill text-white"></i></button></td>';

          tbody.insertBefore(tr, tbody.firstChild);
        });
        calcTotalCost();
      },
      complete: function() {
        pendingBomAjax[rowId] = null;
      }
    });
  }

  $(document).on('input', '.qty-input', function() {
    updateConversion($(this).closest('tr'));
    loadBomForRow($(this).closest('tr'));
  });

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
    calcTotalCost();
  });

  $(document).on('click', '.remove-row', function() {
    if ($('#productionItems tr').length > 1) {
      let row = $(this).closest('tr');
      let rowId = row.attr('data-row-id');
      if (rowId) {
        $('#rmUsageBody .bom-row[data-parent-row="' + rowId + '"]').remove();
      }
      row.remove();
    }
    calcTotalCost();
  });
});
</script>
@endsection
