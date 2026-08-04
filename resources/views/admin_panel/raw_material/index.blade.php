@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
:root {
  --bg: #f0f2f5; --card: #fff; --border: #e9edf2;
  --text: #1e293b; --text-sec: #64748b; --text-muted: #94a3b8;
  --accent: #2b7fff; --accent-drk: #1a6ae8;
  --success: #10b981; --danger: #ef4444; --warning: #f59e0b;
  --radius: 14px; --radius-sm: 9px;
  --shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --shadow-lg: 0 8px 30px rgba(0,0,0,.07);
}
.rm-page * { font-family: 'Inter', sans-serif; }
.rm-page { background: var(--bg); min-height: 100vh; padding-bottom: 2.5rem; }

.rm-hdr {
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--radius); padding: 1.2rem 1.8rem; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: space-between;
  box-shadow: 0 8px 30px rgba(0,0,0,.1);
}
.rm-hdr h2 { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; display: flex; align-items: center; gap: .6rem; }
.rm-hdr h2 i { color: #60a5fa; font-size: 1.3rem; }

.rm-tabs { display: flex; gap: 4px; background: #fff; border-radius: var(--radius); padding: 4px; margin-bottom: 1.5rem; box-shadow: var(--shadow); }
.rm-tab { padding: .5rem 1.2rem; border-radius: 10px; font-size: .82rem; font-weight: 600; color: var(--text-sec); cursor: pointer; transition: all .2s; border: none; background: transparent; }
.rm-tab:hover { background: #f1f5f9; }
.rm-tab.active { background: var(--accent); color: #fff; box-shadow: 0 4px 12px rgba(43,127,255,.2); }

.rm-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem; }
.rm-card-body { padding: 1.25rem; }

.lbl { font-size: .78rem; font-weight: 600; color: var(--text-sec); margin-bottom: .3rem; display: flex; align-items: center; gap: .3rem; }
.lbl i { color: var(--accent); }
.fld { border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: .5rem .8rem; font-size: .85rem; font-weight: 500; width: 100%; outline: none; transition: all .2s; }
.fld:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }

.rm-btn { display: inline-flex; align-items: center; gap: .4rem; border-radius: var(--radius-sm); font-weight: 600; font-size: .82rem; transition: all .25s; cursor: pointer; border: none; padding: .45rem 1.1rem; text-decoration: none; }
.rm-btn-primary { background: linear-gradient(135deg, var(--accent), var(--accent-drk)); color: #fff; }
.rm-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }
.rm-btn-success { background: linear-gradient(135deg, var(--success), #059669); color: #fff; }
.rm-btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,.25); color: #fff; }
.rm-btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.rm-btn-danger:hover { background: #fee2e2; }
.rm-btn-sm { padding: .3rem .7rem; font-size: .75rem; }

.rm-tbl { width: 100%; border-collapse: collapse; font-size: .84rem; }
.rm-tbl th { background: #f8fafc; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted); padding: .6rem .7rem; border-bottom: 2px solid var(--border); text-align: left; }
.rm-tbl td { padding: .5rem .7rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

.stock-badge { padding: 2px 10px; border-radius: 12px; font-size: .75rem; font-weight: 700; }
.stock-ok { background: #ecfdf5; color: #059669; }
.stock-low { background: #fef2f2; color: #dc2626; }

.add-row-btn { background: #f1f5f9; border: 2px dashed #d1d5db; border-radius: var(--radius-sm); padding: .6rem; text-align: center; cursor: pointer; font-weight: 600; font-size: .82rem; color: var(--text-sec); transition: all .2s; }
.add-row-btn:hover { background: #e2e8f0; border-color: var(--accent); color: var(--accent); }
</style>

<div class="rm-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    <div class="rm-hdr">
      <h2><i class="fas fa-database"></i> Raw Materials & Formula Recipes</h2>
      <a href="{{ route('production.index') }}" class="rm-btn rm-btn-primary" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25);">
        <i class="fas fa-industry me-1"></i> Production Batches
      </a>
    </div>

    {{-- KPI STAT CARDS --}}
    @php
      $lowStockCount = $materials->filter(fn($m) => $m->currentStock() <= $m->alert_qty)->count();
    @endphp
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--accent);margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#eff6ff;color:var(--accent);width:45px;height:45px;">
            <i class="fas fa-cubes fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Total Materials</span>
            <strong class="fs-5 text-dark">{{ count($materials) }} Items</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid {{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:{{ $lowStockCount > 0 ? '#fef2f2' : '#ecfdf5' }};color:{{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};width:45px;height:45px;">
            <i class="fas fa-exclamation-triangle fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Low Stock Alerts</span>
            <strong class="fs-5 text-dark" style="color:{{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--text)' }}">{{ $lowStockCount }} Items</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--success);margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#ecfdf5;color:var(--success);width:45px;height:45px;">
            <i class="fas fa-shopping-cart fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Purchases Count</span>
            <strong class="fs-5 text-dark">{{ count($purchases) }} Entries</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #8b5cf6;margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#f3e8ff;color:#8b5cf6;width:45px;height:45px;">
            <i class="fas fa-scroll fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Product Recipes</span>
            <strong class="fs-5 text-dark">{{ isset($productsWithBom) ? count($productsWithBom) : 0 }} Recipes</strong>
          </div>
        </div>
      </div>
    </div>

    {{-- TABS --}}
    <div class="rm-tabs">
      <button class="rm-tab active" data-tab="tab-materials"><i class="fas fa-list me-1"></i> Materials List</button>
      <button class="rm-tab" data-tab="tab-purchase"><i class="fas fa-cart-plus me-1"></i> New Purchase</button>
      <button class="rm-tab" data-tab="tab-history"><i class="fas fa-history me-1"></i> Purchase History</button>
      <button class="rm-tab" data-tab="tab-stock"><i class="fas fa-warehouse me-1"></i> Current Stock</button>
      <button class="rm-tab" data-tab="tab-recipes"><i class="fas fa-utensils me-1"></i> Product Recipes (BOM)</button>
    </div>

    {{-- TAB: MATERIALS --}}
    <div id="tab-materials" class="tab-content">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="font-weight:700;font-size:1rem;margin:0;"><i class="fas fa-cubes me-2" style="color:var(--accent);"></i>Raw Materials List</h5>
            <button class="rm-btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#materialModal" id="resetMaterial"><i class="fas fa-plus"></i> Add Material</button>
          </div>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="materialTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Urdu Name</th>
                  <th>Unit</th>
                  <th>Stock</th>
                  <th>Alert Qty</th>
                  <th style="width:120px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($materials as $m)
                <tr>
                  <td class="mid">{{ $m->id }}</td>
                  <td class="mname">{{ $m->name }}</td>
                  <td class="murduname">{{ $m->urdu_name }}</td>
                  <td class="munit">{{ $m->unit }}</td>
                  <td>
                    @php $sqty = $m->currentStock(); @endphp
                    <span class="stock-badge {{ $sqty > $m->alert_qty ? 'stock-ok' : 'stock-low' }}">{{ $sqty }}</span>
                  </td>
                  <td class="malert">{{ $m->alert_qty }}</td>
                  <td>
                    <button class="rm-btn rm-btn-success rm-btn-sm editMaterial"
                      data-id="{{ $m->id }}" data-name="{{ $m->name }}"
                      data-urdu="{{ $m->urdu_name }}" data-unit="{{ $m->unit }}"
                      data-alert="{{ $m->alert_qty }}" data-notes="{{ $m->notes }}">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.delete', $m->id) }}')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">No raw materials added yet</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB: NEW PURCHASE --}}
    <div id="tab-purchase" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <h5 style="font-weight:700;font-size:1rem;margin-bottom:1.25rem;"><i class="fas fa-cart-plus me-2" style="color:var(--accent);"></i>New Raw Material Purchase</h5>
          <form class="purchase-form" action="{{ route('raw-materials.purchase.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="lbl"><i class="fas fa-calendar"></i> Date</label>
                <input type="date" name="date" class="fld" value="{{ date('Y-m-d') }}" required />
              </div>
              <div class="col-md-4">
                <label class="lbl"><i class="fas fa-user"></i> Vendor</label>
                <select name="vendor_name" class="fld" required>
                  <option value="">Select Vendor...</option>
                  @foreach($vendors as $v)
                  <option value="{{ $v->name }}">{{ $v->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="lbl"><i class="fas fa-sticky-note"></i> Notes</label>
                <input type="text" name="notes" class="fld" placeholder="Optional" />
              </div>
            </div>

            <div class="table-responsive mb-3">
              <table class="rm-tbl" id="purchaseItemsTable">
                <thead>
                  <tr>
                    <th style="width:35%;">Raw Material</th>
                    <th style="width:20%;">Qty</th>
                    <th style="width:20%;">Price/Unit</th>
                    <th style="width:20%;">Total</th>
                    <th style="width:50px;"></th>
                  </tr>
                </thead>
                <tbody id="purchaseItemsBody">
                  <tr class="pitem-row">
                    <td>
                      <select name="raw_material_id[]" class="fld" required>
                        <option value="">Select...</option>
                        @foreach($materials as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" step="0.01" name="qty[]" class="fld pqty" placeholder="0" required /></td>
                    <td><input type="number" step="0.01" name="price_per_unit[]" class="fld pprice" placeholder="0" required /></td>
                    <td><span class="ptotal fw-bold" style="font-size:.9rem;">0</span></td>
                    <td style="white-space:nowrap;">
                      <button type="button" class="rm-btn rm-btn-success rm-btn-sm add-row-btn" id="addPurchaseRow"><i class="fas fa-plus"></i></button>
                      <button type="button" class="rm-btn rm-btn-danger rm-btn-sm remove-row" style="display:none;"><i class="fas fa-times"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div><strong>Total Cost: </strong><span id="purchaseTotal" class="fs-5 fw-bold" style="color:var(--accent);">Rs 0</span></div>
              <button type="submit" class="rm-btn rm-btn-success"><i class="fas fa-save"></i> Save Purchase</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- TAB: PURCHASE HISTORY --}}
    <div id="tab-history" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <h5 style="font-weight:700;font-size:1rem;margin-bottom:1.25rem;"><i class="fas fa-history me-2" style="color:var(--accent);"></i>Purchase History</h5>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="purchaseTable">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Items</th>
                  <th>Total Cost</th>
                  <th>Created By</th>
                  <th style="width:80px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($purchases as $p)
                <tr>
                  <td style="font-weight:600;">{{ $p->invoice_no }}</td>
                  <td>{{ $p->date }}</td>
                  <td>{{ $p->vendor_name ?? '-' }}</td>
                  <td>
                    @foreach($p->items as $item)
                    <div style="font-size:.78rem;">{{ $item->rawMaterial->name ?? '-' }}: {{ $item->qty }} x Rs {{ number_format($item->price_per_unit, 0) }}</div>
                    @endforeach
                  </td>
                  <td style="font-weight:700;">Rs {{ number_format($p->total_cost, 0) }}</td>
                  <td>{{ $p->creator->name ?? '-' }}</td>
                  <td>
                    <button class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.purchase.delete', $p->id) }}')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">No purchases yet</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB: STOCK --}}
    <div id="tab-stock" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <h5 style="font-weight:700;font-size:1rem;margin-bottom:1.25rem;"><i class="fas fa-warehouse me-2" style="color:var(--accent);"></i>Current Stock Levels</h5>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="stockTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Material Name</th>
                  <th>Unit</th>
                  <th>Current Stock</th>
                  <th>Alert Qty</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($materials as $m)
                @php $sqty = $m->currentStock(); @endphp
                <tr>
                  <td>{{ $m->id }}</td>
                  <td style="font-weight:600;">{{ $m->name }}</td>
                  <td>{{ $m->unit }}</td>
                  <td style="font-weight:700;">{{ $sqty }}</td>
                  <td>{{ $m->alert_qty }}</td>
                  <td>
                    <span class="stock-badge {{ $sqty > $m->alert_qty ? 'stock-ok' : 'stock-low' }}">
                      {{ $sqty > $m->alert_qty ? 'In Stock' : ($sqty > 0 ? 'Low Stock' : 'Out of Stock') }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted);">No materials</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB: PRODUCT RECIPES (BOM) --}}
    <div id="tab-recipes" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="font-weight:700;font-size:1rem;margin:0;"><i class="fas fa-utensils me-2" style="color:var(--accent);"></i>Configured Product Recipes (Bill of Materials)</h5>
            <a href="{{ route('store') }}" class="rm-btn rm-btn-primary" style="font-size:.78rem;"><i class="fas fa-plus me-1"></i> Add Product & Recipe</a>
          </div>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="recipesTable">
              <thead>
                <tr>
                  <th>Product Code</th>
                  <th>Product Name</th>
                  <th>Unit Type</th>
                  <th>Recipe Raw Materials (Qty per Unit)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($productsWithBom as $p)
                <tr>
                  <td style="font-weight:700;">{{ $p->item_code }}</td>
                  <td style="font-weight:600;color:var(--text);">{{ $p->item_name }}</td>
                  <td><span class="badge bg-secondary" style="font-size:.72rem;">{{ strtoupper($p->unit_type ?? 'Piece') }}</span></td>
                  <td>
                    @foreach($p->bom as $bomItem)
                    <div style="font-size:.8rem;margin-bottom:2px;">
                      <i class="fas fa-dot-circle text-primary me-1" style="font-size:.6rem;"></i>
                      <strong>{{ $bomItem->rawMaterial->name ?? 'Material' }}:</strong> {{ (float)$bomItem->qty_per_unit }} {{ $bomItem->rawMaterial->unit ?? '' }}
                    </div>
                    @endforeach
                  </td>
                  <td>
                    <a href="{{ route('products.edit', $p->id) }}" class="rm-btn rm-btn-success rm-btn-sm">
                      <i class="fas fa-edit me-1"></i> Edit Recipe
                    </a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted);">No product recipes configured yet. Edit products to add Bill of Materials (BOM).</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- MATERIAL MODAL --}}
<div class="modal fade" id="materialModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.1);">
      <div class="modal-header" style="background:linear-gradient(135deg,#0b1a33,#162d50);color:#fff;border-radius:var(--radius) var(--radius) 0 0;border:none;">
        <h5 class="modal-title" id="materialModalLabel" style="font-weight:700;"><i class="fas fa-cube me-1"></i> Raw Material</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="material-form" action="{{ route('raw-materials.store') }}" method="POST">
        @csrf
        <input type="hidden" name="edit_id" id="materialEditId" />
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="lbl"><i class="fas fa-tag"></i> Name</label>
              <input type="text" name="name" id="materialName" class="fld" required placeholder="e.g. Dough" />
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="fas fa-ruler"></i> Unit</label>
              <select name="unit" id="materialUnit" class="fld" required>
                <option value="">Select...</option>
                <option value="kg">kg</option>
                <option value="piece">piece</option>
                <option value="litre">litre</option>
                <option value="gram">gram</option>
                <option value="dozen">dozen</option>
                <option value="pack">pack</option>
                <option value="bottle">bottle</option>
                <option value="bag">bag</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="fas fa-exclamation-triangle"></i> Alert Qty</label>
              <input type="number" step="0.01" min="0" name="alert_qty" id="materialAlert" class="fld" value="0" />
            </div>
            <div class="col-md-4">
              <label class="lbl"><i class="fas fa-sticky-note"></i> Notes</label>
              <input type="text" name="notes" id="materialNotes" class="fld" placeholder="optional" />
            </div>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
          <button type="button" class="rm-btn" style="background:#f1f5f9;color:var(--text-sec);" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="rm-btn rm-btn-primary"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Tab switching
document.querySelectorAll('.rm-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.rm-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
    this.classList.add('active');
    document.getElementById(this.dataset.tab).style.display = 'block';
  });
});

// Material form submit
$(document).on('submit', '.material-form', function(e) {
  e.preventDefault();
  var fd = new FormData(this);
  var url = $(this).attr('action');
  $(this).find(':submit').attr('disabled', true);
  myAjax(url, fd, 'post');
});

// Edit material
$(document).on('click', '.editMaterial', function() {
  $('#materialEditId').val($(this).data('id'));
  $('#materialName').val($(this).data('name'));
  $('#materialUnit').val($(this).data('unit'));
  $('#materialAlert').val($(this).data('alert'));
  $('#materialNotes').val($(this).data('notes'));
  $('#materialModalLabel').html('<i class="fas fa-edit me-1"></i> Edit Raw Material');
  $('#materialModal').modal('show');
});

// Reset material modal
$(document).on('click', '#resetMaterial', function() {
  $('#materialEditId').val('');
  $('#materialName').val('');
  $('#materialUnit').val('');
  $('#materialAlert').val('0');
  $('#materialNotes').val('');
  $('#materialModalLabel').html('<i class="fas fa-cube me-1"></i> Add Raw Material');
});

// Purchase form - add row
$(document).on('click', '#addPurchaseRow', function() {
  var tbody = document.getElementById('purchaseItemsBody');
  var firstRow = tbody.querySelector('.pitem-row');
  var newRow = firstRow.cloneNode(true);
  newRow.querySelectorAll('input').forEach(inp => inp.value = '');
  newRow.querySelector('select').value = '';
  newRow.querySelector('.ptotal').textContent = '0';
  var addBtn = newRow.querySelector('.add-row-btn');
  if (addBtn) addBtn.style.display = 'none';
  var rmBtn = newRow.querySelector('.remove-row');
  if (rmBtn) rmBtn.style.display = '';
  newRow.querySelectorAll('.pqty, .pprice').forEach(inp => inp.addEventListener('input', function() { calculateRowTotal(this); calculatePurchaseTotal(); }));
  tbody.appendChild(newRow);
});

// Remove row
$(document).on('click', '.remove-row', function() {
  $(this).closest('tr').remove();
  calculatePurchaseTotal();
});

// Calculate row total
function calculateRowTotal(el) {
  var row = el.closest('tr');
  var qty = parseFloat(row.querySelector('.pqty').value) || 0;
  var price = parseFloat(row.querySelector('.pprice').value) || 0;
  row.querySelector('.ptotal').textContent = (qty * price).toLocaleString();
  calculatePurchaseTotal();
}

document.querySelectorAll('.pqty, .pprice').forEach(inp => inp.addEventListener('input', function() { calculateRowTotal(this); }));

function calculatePurchaseTotal() {
  var total = 0;
  document.querySelectorAll('.pitem-row').forEach(row => {
    var qty = parseFloat(row.querySelector('.pqty').value) || 0;
    var price = parseFloat(row.querySelector('.pprice').value) || 0;
    total += qty * price;
  });
  document.getElementById('purchaseTotal').textContent = 'Rs ' + total.toLocaleString();
}

// Purchase form submit
$(document).on('submit', '.purchase-form', function(e) {
  e.preventDefault();
  var fd = new FormData(this);
  var url = $(this).attr('action');
  $(this).find(':submit').attr('disabled', true);
  myAjax(url, fd, 'post');
});

// Confirm delete
function confirmDelete(url) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This action cannot be undone',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    dangerMode: true,
  }).then((result) => {
    if (result.isConfirmed) {
      yourFunction(url, 'get');
    }
  });
}

// DataTables
$(document).ready(function() {
  $('#materialTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: 'Search:', emptyTable: 'No materials' } });
  $('#purchaseTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: 'Search:', emptyTable: 'No purchases' } });
  $('#stockTable').DataTable({ pageLength: 10, order: [[0, 'asc']], language: { search: 'Search:', emptyTable: 'No stock' } });
  $('#recipesTable').DataTable({ pageLength: 10, order: [[0, 'asc']], language: { search: 'Search:', emptyTable: 'No recipes' } });
});
</script>
@endsection
