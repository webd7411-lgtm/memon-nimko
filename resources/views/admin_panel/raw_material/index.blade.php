@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
:root {
  --bg: #f8fafc; --card: #ffffff; --border: #e2e8f0;
  --text: #0f172a; --text-sec: #475569; --text-muted: #64748b;
  --accent: #2563eb; --accent-drk: #1d4ed8;
  --success: #10b981; --danger: #ef4444; --warning: #f59e0b;
  --radius: 16px; --radius-sm: 10px;
  --shadow: 0 4px 6px -1px rgba(0,0,0,.05), 0 2px 4px -1px rgba(0,0,0,.03);
  --shadow-lg: 0 10px 25px -5px rgba(0,0,0,.08), 0 8px 10px -6px rgba(0,0,0,.04);
}
.rm-page * { font-family: 'Inter', sans-serif; }
.rm-page { background: var(--bg); min-height: 100vh; padding-bottom: 2.5rem; }

.rm-hdr {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--radius); padding: 1.25rem 1.75rem; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
  box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.3);
}
.rm-hdr h2 { font-size: 1.35rem; font-weight: 800; color: #fff; margin: 0; display: flex; align-items: center; gap: .65rem; }
.rm-hdr h2 i { color: #60a5fa; font-size: 1.4rem; }

.rm-tabs { display: flex; gap: 6px; background: #fff; border-radius: var(--radius); padding: 6px; margin-bottom: 1.5rem; box-shadow: var(--shadow); border: 1px solid var(--border); overflow-x: auto; }
.rm-tab { padding: .55rem 1.25rem; border-radius: 10px; font-size: .85rem; font-weight: 600; color: var(--text-sec); cursor: pointer; transition: all .2s; border: none; background: transparent; white-space: nowrap; display: flex; align-items: center; gap: .4rem; }
.rm-tab:hover { background: #f1f5f9; color: var(--text); }
.rm-tab.active { background: var(--accent); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,.35); }

.rm-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-lg); margin-bottom: 1.5rem; overflow: hidden; }
.rm-card-body { padding: 1.35rem; }

.lbl { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: var(--text-sec); margin-bottom: .35rem; display: flex; align-items: center; gap: .35rem; }
.lbl i { color: var(--accent); }
.fld { border: 1px solid var(--border); border-radius: var(--radius-sm); padding: .55rem .85rem; font-size: .88rem; font-weight: 500; width: 100%; outline: none; transition: all .2s; background: #fff; color: var(--text); }
.fld:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

.rm-btn { display: inline-flex; align-items: center; gap: .45rem; border-radius: var(--radius-sm); font-weight: 600; font-size: .85rem; transition: all .2s; cursor: pointer; border: none; padding: .55rem 1.15rem; text-decoration: none; }
.rm-btn-primary { background: linear-gradient(135deg, var(--accent), var(--accent-drk)); color: #fff !important; box-shadow: 0 4px 14px rgba(37,99,235,.35); }
.rm-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,.45); }
.rm-btn-success { background: linear-gradient(135deg, var(--success), #059669); color: #fff !important; box-shadow: 0 4px 14px rgba(16,185,129,.35); }
.rm-btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,.45); }
.rm-btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.rm-btn-danger:hover { background: #fee2e2; }
.rm-btn-sm { padding: .35rem .75rem; font-size: .78rem; border-radius: 8px; }

.rm-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .88rem; }
.rm-tbl th { background: #f8fafc; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: .85rem 1rem; border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap; }
.rm-tbl td { padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: var(--text-sec); }
.rm-tbl tbody tr:hover td { background-color: #f8fafc; }

.stock-badge { padding: 4px 12px; border-radius: 20px; font-size: .78rem; font-weight: 700; display: inline-flex; align-items: center; gap: .3rem; }
.stock-ok { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.stock-low { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

.add-row-btn { background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: var(--radius-sm); padding: .65rem; text-align: center; cursor: pointer; font-weight: 600; font-size: .85rem; color: var(--text-sec); transition: all .2s; }
.add-row-btn:hover { background: #e2e8f0; border-color: var(--accent); color: var(--accent); }

/* ═══════ MOBILE PREMIUM CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .rm-page { overflow-x: hidden !important; padding-bottom: 2rem; }

  /* Header */
  .rm-hdr { flex-direction: column !important; align-items: stretch !important; padding: .9rem 1rem !important; gap: .6rem !important; }
  .rm-hdr h2 { font-size: .98rem !important; }
  .rm-hdr h2 i { font-size: 1.05rem; }
  .rm-hdr a.rm-btn { justify-content: center; min-height: 40px; font-size: .76rem; }

  /* KPI stat cards — compact */
  .rm-page .row.g-3 { margin-bottom: .9rem !important; }
  .rm-page .row.g-3 > .col-6 > .rm-card { padding: .55rem .6rem !important; gap: .5rem !important; border-radius: 12px; margin-bottom: 0 !important; }
  .rm-page .row.g-3 .rounded-circle { width: 36px !important; height: 36px !important; padding: .45rem !important; flex-shrink: 0; }
  .rm-page .row.g-3 .fs-5 { font-size: .92rem !important; }
  .rm-page .row.g-3 .small { font-size: .56rem !important; }

  /* Tabs → 2-col grid, no horizontal scroll */
  .rm-tabs { display: grid !important; grid-template-columns: 1fr 1fr; gap: 6px; overflow: visible !important; padding: 4px; }
  .rm-tab { justify-content: center; white-space: normal; font-size: .7rem; padding: .5rem .35rem; min-height: 42px; border-radius: 10px; }
  .rm-tab i { font-size: .8rem; }

  /* Card body */
  .rm-card-body { padding: .7rem !important; overflow: hidden !important; }

  /* Kill ALL scroll wrappers — no horizontal scroll */
  .rm-page [style*="overflow-x"],
  .rm-page .table-responsive,
  .rm-page .dataTables_wrapper,
  .rm-page .dataTables_scrollBody,
  .rm-page .dataTables_scrollHead,
  .rm-page .dataTables_scrollFoot {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
  }

  /* Table → stacked cards */
  .rm-page .rm-tbl,
  .rm-page .rm-tbl.dataTable {
    display: block !important;
    width: 100% !important;
  }
  .rm-page .rm-tbl thead { display: none !important; }
  .rm-page .rm-tbl tbody { display: block !important; }
  .rm-page .rm-tbl tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .5rem .55rem;
    align-items: start;
    background: var(--card);
    border: 1px solid var(--border);
    border-top: 3px solid var(--accent);
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 8px rgba(0,0,0,.05);
    padding: .6rem .7rem;
    margin-bottom: .6rem;
    overflow: hidden !important;
  }
  .rm-page .rm-tbl tbody tr:hover { background: var(--card); }

  .rm-page .rm-tbl tbody td {
    display: flex !important;
    flex-direction: column;
    gap: .15rem;
    border: none !important;
    padding: 0 !important;
    min-width: 0;
    text-align: left;
    word-break: break-word;
    overflow-wrap: anywhere;
  }
  .rm-page .rm-tbl tbody td::before {
    content: attr(data-label);
    font-size: .54rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .45px;
    color: var(--text-muted);
    white-space: nowrap;
  }

  /* ── Purchase form rows (editable) — inputs full width ── */
  #purchaseItemsTable .rm-tbl tbody td { flex-direction: column; gap: .15rem; }
  #purchaseItemsTable .rm-tbl tbody td .fld { width: 100% !important; min-height: 40px; font-size: .82rem; }
  #purchaseItemsTable .rm-tbl tbody td .ptotal { font-size: .78rem; }
  #purchaseItemsTable .rm-tbl tbody td .add-row-btn,
  #purchaseItemsTable .rm-tbl tbody td .remove-row { display: flex !important; align-items: center; justify-content: center; min-height: 40px; }
  #purchaseItemsTable .rm-tbl tbody td:nth-child(1) { grid-column: 1 / -1; }
  #purchaseItemsTable .rm-tbl tbody td:nth-child(5) { flex-direction: row; gap: .4rem; grid-column: 1 / -1; align-items: stretch; }
  #purchaseItemsTable .rm-tbl tbody td:nth-child(5)::before { content: none; }
  #purchaseItemsTable .rm-tbl tbody td:nth-child(5) .rm-btn { flex: 1; }

  /* ── Empty / colspan cells ── */
  .rm-page .rm-tbl tbody td[colspan] {
    grid-column: 1 / -1;
    text-align: center;
    padding: 1.2rem .5rem !important;
    color: var(--text-muted);
  }

  /* ── Form fields (purchase tab + modal) ── */
  .fld { font-size: .88rem !important; min-height: 44px; }
  .lbl { font-size: .72rem; }
  .rm-btn { min-height: 40px; }
  .rm-btn-sm { padding: .4rem .7rem; font-size: .72rem; }
  .row.g-3 > .col-md-3,
  .row.g-3 > .col-md-6,
  .row.g-3 > .col-md-12,
  .row.g-3 > .col-md-4 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }

  /* Modal */
  .modal-dialog { margin: .5rem; }
  .modal-dialog .modal-body { padding: 1rem; }
  .modal-content { border-radius: 14px; }
}
/* Fix DataTables conflict with mobile grid CSS */
@media (max-width: 767.98px) {
  .dataTables_wrapper .rm-tbl tbody tr,
  table.dataTable tbody tr {
    display: table-row !important;
    grid-template-columns: none !important;
  }
  .dataTables_wrapper .rm-tbl tbody td,
  table.dataTable tbody td {
    display: table-cell !important;
    flex-direction: initial !important;
  }
}
</style>

<div class="rm-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- HERO HEADER --}}
    <div class="rm-hdr">
      <h2><i class="bi bi-database-fill"></i> Raw Materials & Formula Recipes</h2>
      <a href="{{ route('production.index') }}" class="rm-btn" style="background:rgba(255,255,255,0.12);color:#fff;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);">
        <i class="bi bi-gear-wide-connected me-1"></i> Production Batches
      </a>
    </div>

    {{-- KPI STAT CARDS --}}
    @php
      $lowStockCount = $materials->filter(fn($m) => $m->currentStock() <= $m->alert_qty)->count();
    @endphp
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--accent);margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#eff6ff;color:var(--accent);width:48px;height:48px;">
            <i class="bi bi-boxes fs-4"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Total Materials</span>
            <strong class="fs-5 text-dark">{{ count($materials) }} Items</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid {{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:{{ $lowStockCount > 0 ? '#fef2f2' : '#ecfdf5' }};color:{{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};width:48px;height:48px;">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Low Stock Alerts</span>
            <strong class="fs-5 text-dark" style="color:{{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--text)' }}">{{ $lowStockCount }} Items</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--success);margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#ecfdf5;color:var(--success);width:48px;height:48px;">
            <i class="bi bi-cart-check-fill fs-4"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.73rem;text-transform:uppercase;">Purchases Count</span>
            <strong class="fs-5 text-dark">{{ count($purchases) }} Entries</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="rm-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #8b5cf6;margin-bottom:0;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#f3e8ff;color:#8b5cf6;width:48px;height:48px;">
            <i class="bi bi-receipt-cutoff fs-4"></i>
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
      <button class="rm-tab active" data-tab="tab-materials"><i class="bi bi-list-ul"></i> Materials List</button>
      <button class="rm-tab" data-tab="tab-purchase"><i class="bi bi-cart-plus-fill"></i> New Purchase</button>
      <button class="rm-tab" data-tab="tab-history"><i class="bi bi-clock-history"></i> Purchase History</button>
      <button class="rm-tab" data-tab="tab-stock"><i class="bi bi-houses-fill"></i> Current Stock</button>
      <button class="rm-tab" data-tab="tab-recipes"><i class="bi bi-egg-fried"></i> Product Recipes (BOM)</button>
    </div>

    {{-- TAB: MATERIALS --}}
    <div id="tab-materials" class="tab-content">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-boxes me-2" style="color:var(--accent);"></i>Raw Materials List</h5>
            <button class="rm-btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#materialModal" id="resetMaterial"><i class="bi bi-plus-lg"></i> Add Material</button>
          </div>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="materialTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Units & Ratio</th>
                  <th>Purchase Price / Unit</th>
                  <th>Total Stock</th>
                  <th>Alert Qty</th>
                  <th style="width:120px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($materials as $m)
                @php $lastCost = $m->lastPurchaseCost(); @endphp
                <tr>
                  <td class="mid fw-bold" data-label="#">{{ $m->id }}</td>
                  <td class="mname fw-bold text-dark" data-label="Name">{{ $m->name }}</td>
                  <td class="munit" data-label="Units">
                    <span class="badge bg-primary-subtle text-primary border">{{ strtoupper($m->unit) }}</span>
                    @if($m->consumption_unit && $m->consumption_unit != $m->unit)
                    <div style="font-size:.72rem;" class="text-muted mt-1">1 {{ $m->unit }} = {{ number_format($m->conversion_factor, 0) }} {{ $m->consumption_unit }}</div>
                    @endif
                  </td>
                  <td class="mprice fw-bold text-success" data-label="Purchase Price">
                    Rs {{ number_format($lastCost, 2) }}
                    <div style="font-size:.72rem;" class="text-muted">per {{ $m->unit }}</div>
                  </td>
                  <td data-label="Stock">
                    @php 
                      $sqty = $m->currentStock(); 
                      $factor = (float)($m->conversion_factor ?? 1);
                      $purQty = $factor > 0 ? round($sqty / $factor, 2) : $sqty;
                      $cUnit = $m->consumption_unit ?? $m->unit;
                    @endphp
                    <span class="stock-badge {{ $sqty > $m->alert_qty ? 'stock-ok' : 'stock-low' }}">
                      {{ number_format($sqty, 2) }} {{ $cUnit }}
                    </span>
                    @if($factor > 1)
                      <div style="font-size:.72rem;" class="text-muted fw-semibold mt-1">({{ $purQty }} {{ $m->unit }})</div>
                    @endif
                  </td>
                  <td class="malert fw-semibold" data-label="Alert Qty">
                    @php
                      $factor = (float)($m->conversion_factor ?? 1);
                      $alertInPurchaseUnit = $factor > 0 ? ($m->alert_qty / $factor) : $m->alert_qty;
                    @endphp
                    {{ number_format($alertInPurchaseUnit, 2) }} {{ $m->unit }}
                  </td>
                  <td class="text-center" data-label="Actions">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <button class="rm-btn rm-btn-success rm-btn-sm editMaterial"
                        data-id="{{ $m->id }}" data-name="{{ $m->name }}"
                        data-urdu="{{ $m->urdu_name }}" data-unit="{{ $m->unit }}"
                        data-cunit="{{ $m->consumption_unit ?? $m->unit }}"
                        data-factor="{{ number_format($m->conversion_factor ?? 1, 4) }}"
                        data-price="{{ $lastCost }}"
                        data-opening-stock="{{ number_format($m->currentStock() / (($m->conversion_factor ?? 1) > 0 ? ($m->conversion_factor ?? 1) : 1), 2) }}"
                        data-alert="{{ $m->alert_qty }}" data-notes="{{ $m->notes }}" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.delete', $m->id) }}')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
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
          <h5 style="font-weight:700;font-size:1.05rem;margin-bottom:1.25rem;"><i class="bi bi-cart-plus-fill me-2" style="color:var(--accent);"></i>New Raw Material Purchase</h5>
          <form class="purchase-form" action="{{ route('raw-materials.purchase.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="lbl"><i class="bi bi-calendar-date"></i> Date</label>
                <input type="date" name="date" class="fld" value="{{ date('Y-m-d') }}" required />
              </div>
              <div class="col-md-3">
                <label class="lbl"><i class="bi bi-person-fill"></i> Vendor</label>
                <select name="vendor_name" class="fld" required>
                  <option value="">Select Vendor...</option>
                  @foreach($vendors as $v)
                  <option value="{{ $v->name }}">{{ $v->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="lbl"><i class="bi bi-houses-fill"></i> Warehouse</label>
                <select name="warehouse_id" class="fld">
                  <option value="">Main Stock (No Warehouse)</option>
                  @foreach($warehouses as $w)
                  <option value="{{ $w->id }}">{{ $w->warehouse_name ?? $w->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="lbl"><i class="bi bi-sticky-fill"></i> Notes</label>
                <input type="text" name="notes" class="fld" placeholder="Optional notes" />
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
                    <td data-label="Raw Material">
                      <select name="raw_material_id[]" class="fld" required>
                        <option value="">Select Material...</option>
                        @foreach($materials as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                        @endforeach
                      </select>
                    </td>
                    <td data-label="Qty"><input type="number" step="0.01" name="qty[]" class="fld pqty" placeholder="0" required /></td>
                    <td data-label="Price/Unit"><input type="number" step="0.01" name="price_per_unit[]" class="fld pprice" placeholder="0" required /></td>
                    <td data-label="Total"><span class="ptotal fw-bold" style="font-size:.9rem;">0</span></td>
                    <td style="white-space:nowrap;" data-label="Action">
                      <button type="button" class="rm-btn rm-btn-success rm-btn-sm add-row-btn" id="addPurchaseRow"><i class="bi bi-plus-lg"></i></button>
                      <button type="button" class="rm-btn rm-btn-danger rm-btn-sm remove-row" style="display:none;"><i class="bi bi-x-lg"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div><strong>Total Cost: </strong><span id="purchaseTotal" class="fs-5 fw-bold" style="color:var(--accent);">Rs 0</span></div>
              <button type="submit" class="rm-btn rm-btn-success"><i class="bi bi-save me-1"></i> Save Purchase</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- TAB: PURCHASE HISTORY --}}
    <div id="tab-history" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-clock-history me-2" style="color:var(--accent);"></i>Purchase History</h5>
            <button class="rm-btn rm-btn-primary" onclick="printReportSection('purchaseTable', 'Raw Material Purchase History')"><i class="bi bi-printer me-1"></i> Print Purchase History</button>
          </div>
          <div style="overflow-x:auto;" id="purchasePrintArea">
            <table class="rm-tbl" id="purchaseTable">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Warehouse</th>
                  <th>Items Breakdown</th>
                  <th>Total Cost</th>
                  <th>Created By</th>
                  <th style="width:110px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($purchases as $p)
                <tr>
                  <td style="font-weight:700;" data-label="Invoice"><span class="badge bg-light text-dark border font-monospace">{{ $p->invoice_no }}</span></td>
                  <td data-label="Date">{{ $p->date }}</td>
                  <td class="fw-bold text-dark" data-label="Vendor">{{ $p->vendor_name ?? '-' }}</td>
                  <td data-label="Warehouse"><span class="badge bg-primary-subtle text-primary border fw-semibold">{{ $p->warehouse->warehouse_name ?? $p->warehouse->name ?? 'Main Stock' }}</span></td>
                  <td data-label="Items Breakdown">
                    @foreach($p->items as $item)
                    <div style="font-size:.8rem;margin-bottom:2px;" class="text-secondary">
                      <i class="bi bi-dot me-1"></i><strong>{{ $item->rawMaterial->name ?? '-' }}:</strong> {{ $item->qty }} x Rs {{ number_format($item->price_per_unit, 0) }}
                    </div>
                    @endforeach
                  </td>
                  <td style="font-weight:700;" class="text-success" data-label="Total Cost">Rs {{ number_format($p->total_cost, 0) }}</td>
                  <td data-label="Created By"><span class="badge bg-secondary-subtle text-secondary">{{ $p->creator->name ?? '-' }}</span></td>
                  <td class="text-center" data-label="Actions">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <a href="{{ route('raw-materials.purchase.print', $p->id) }}" target="_blank" class="rm-btn rm-btn-primary rm-btn-sm" title="Print Invoice">
                        <i class="bi bi-printer"></i>
                      </a>
                      <button class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.purchase.delete', $p->id) }}')" title="Delete Purchase">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted);">No purchases recorded yet</td></tr>
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
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-houses-fill me-2" style="color:var(--accent);"></i>Current Stock Levels</h5>
            <button class="rm-btn rm-btn-primary" onclick="printReportSection('stockTable', 'Raw Material Current Stock Report')"><i class="bi bi-printer me-1"></i> Print Stock Report</button>
          </div>

          {{-- STOCK FILTERS --}}
          <div class="row g-2 mb-3 p-3 bg-light rounded-3 border">
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-search"></i> Search Material</label>
              <input type="text" id="stockSearchInput" class="fld" placeholder="Search by name..." />
            </div>
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-houses"></i> Filter by Warehouse</label>
              <select id="stockWarehouseFilter" class="fld">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                <option value="{{ strtolower($w->warehouse_name ?? $w->name) }}">{{ $w->warehouse_name ?? $w->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-funnel"></i> Filter by Stock Status</label>
              <select id="stockStatusFilter" class="fld">
                <option value="">All Statuses</option>
                <option value="in stock">In Stock</option>
                <option value="low stock">Low Stock</option>
                <option value="out of stock">Out of Stock</option>
              </select>
            </div>
          </div>

          <div style="overflow-x:auto;" id="stockPrintArea">
            <table class="rm-tbl" id="stockTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Material Name</th>
                  <th>Unit</th>
                  <th>Warehouse Breakdown</th>
                  <th>Total Stock</th>
                  <th>Alert Qty</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($materials as $m)
                  @php 
                    $sqty = $m->currentStock(); 
                    $factor = (float)($m->conversion_factor ?? 1);
                    $purQty = $factor > 0 ? round($sqty / $factor, 2) : $sqty;
                    $alertInConsumption = (float)($m->alert_qty ?? 0) * ($factor > 0 ? $factor : 1);
                    $statusStr = $sqty > $alertInConsumption ? 'In Stock' : ($sqty > 0 ? 'Low Stock' : 'Out of Stock');
                  @endphp
                <tr class="stock-row" data-status="{{ strtolower($statusStr) }}">
                  <td class="fw-bold" data-label="#">{{ $m->id }}</td>
                  <td style="font-weight:700;" class="text-dark material-name-cell" data-label="Material">{{ $m->name }}</td>
                  <td data-label="Unit"><span class="badge bg-light text-dark border">{{ $m->unit }}</span></td>
                  <td class="warehouse-cell" data-label="Warehouse Breakdown">
                    @if($m->stocks && $m->stocks->count() > 0)
                    @foreach($m->stocks as $st)
                    @php
                      $stFactor = (float)($m->conversion_factor ?? 1);
                      $stQtyInPurchaseUnit = $stFactor > 0 ? round($st->qty / $stFactor, 2) : $st->qty;
                    @endphp
                    <div style="font-size:.78rem;" class="wh-item">
                      <i class="bi bi-dot me-1"></i><strong>{{ $st->warehouse->warehouse_name ?? $st->warehouse->name ?? 'Main Stock' }}:</strong> {{ $stQtyInPurchaseUnit }} {{ $m->unit }}
                    </div>
                    @endforeach
                    @else
                    <span class="text-muted" style="font-size:.78rem;">Main Stock: {{ $purQty }} {{ $m->unit }}</span>
                    @endif
                  </td>
                  <td style="font-weight:800;" class="fs-6" data-label="Total Stock">{{ $purQty }} {{ $m->unit }}</td>
                  <td data-label="Alert Qty">
                    @php
                      $factor = (float)($m->conversion_factor ?? 1);
                      $alertInPurchaseUnit = $factor > 0 ? ($m->alert_qty / $factor) : $m->alert_qty;
                    @endphp
                    {{ number_format($alertInPurchaseUnit, 2) }} {{ $m->unit }}
                  </td>
                  <td data-label="Status">
                    <span class="stock-badge {{ $sqty > $m->alert_qty ? 'stock-ok' : 'stock-low' }}">
                      {{ $statusStr }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">No materials</td></tr>
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
            <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-egg-fried me-2" style="color:var(--accent);"></i>Configured Product Recipes (Bill of Materials)</h5>
            <a href="{{ route('store') }}" class="rm-btn rm-btn-primary" style="font-size:.78rem;"><i class="bi bi-plus-lg me-1"></i> Add Product & Recipe</a>
          </div>
          <div style="overflow-x:auto;">
            <table class="rm-tbl" id="recipesTable">
              <thead>
                <tr>
                  <th>Product Code</th>
                  <th>Product Name</th>
                  <th>Unit Type</th>
                  <th>Recipe Raw Materials (Qty per Unit)</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($productsWithBom as $p)
                <tr>
                  <td style="font-weight:700;" data-label="Product Code"><span class="badge bg-light text-dark border font-monospace">{{ $p->item_code }}</span></td>
                  <td style="font-weight:700;color:var(--text);" data-label="Product Name">{{ $p->item_name }}</td>
                  <td data-label="Unit Type"><span class="badge bg-secondary-subtle text-secondary" style="font-size:.75rem;">{{ strtoupper($p->unit_type ?? 'Piece') }}</span></td>
                  <td data-label="Recipe Raw Materials">
                    @foreach($p->bom as $bomItem)
                    <div style="font-size:.82rem;margin-bottom:3px;">
                      <i class="bi bi-record-circle-fill text-primary me-1" style="font-size:.7rem;"></i>
                      <strong>{{ $bomItem->rawMaterial->name ?? 'Material' }}:</strong> {{ (float)$bomItem->qty_per_unit }} {{ $bomItem->rawMaterial->consumption_unit ?? $bomItem->rawMaterial->unit ?? '' }}
                    </div>
                    @endforeach
                  </td>
                  <td class="text-center" data-label="Action">
                    <a href="{{ route('products.edit', $p->id) }}" class="rm-btn rm-btn-success rm-btn-sm">
                      <i class="bi bi-pencil-square me-1"></i> Edit Recipe
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
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <h5 class="modal-title" id="materialModalLabel" style="font-weight:700;"><i class="bi bi-box-seam me-1 text-primary"></i> Raw Material</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="material-form" action="{{ route('raw-materials.store') }}" method="POST">
        @csrf
        <input type="hidden" name="edit_id" id="materialEditId" />
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-tag-fill"></i> Material Name</label>
              <input type="text" name="name" id="materialName" class="fld" required placeholder="e.g. Dough, Sugar" />
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-bag-check-fill"></i> Purchase Unit (Vendor)</label>
              <select name="unit" id="materialUnit" class="fld" required>
                <option value="">Select Unit...</option>
                <option value="kg">kg (Kilogram)</option>
                <option value="litre">litre (Litre)</option>
                <option value="dozen">dozen (Dozen)</option>
                <option value="piece">piece (Piece)</option>
                <option value="gram">gram (Gram)</option>
                <option value="bottle">bottle (Bottle)</option>
                <option value="bag">bag (Bag)</option>
                <option value="pack">pack (Pack)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-egg-fried"></i> Recipe / Usage Unit</label>
              <select name="consumption_unit" id="materialConsumptionUnit" class="fld" required>
                <option value="">Select Recipe Unit...</option>
                <option value="gram">gram (Grams)</option>
                <option value="ml">ml (Milliliters)</option>
                <option value="piece">piece (Pieces)</option>
                <option value="kg">kg (Kilograms)</option>
                <option value="litre">litre (Litres)</option>
                <option value="dozen">dozen (Dozens)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-gear-fill"></i> Conversion Ratio</label>
              <input type="number" step="0.0001" min="0.0001" name="conversion_factor" id="materialConversionFactor" class="fld" value="1" required placeholder="e.g. 1000" />
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-box-seam-fill"></i> Opening Stock (Purchase Unit)</label>
              <input type="number" step="0.01" min="0" name="opening_stock" id="materialOpeningStock" class="fld" value="0" placeholder="e.g. 50" />
            </div>
            <div class="col-12 mt-1">
              <div class="p-2 rounded bg-light border text-muted" style="font-size:.74rem;">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                <strong>Rule:</strong> How many Recipe Units exist in 1 Purchase Unit? (e.g. 1 kg = <strong>1000</strong> grams, 1 dozen = <strong>12</strong> pieces).
              </div>
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-cash-stack"></i> Purchase Price / Cost (Rs)</label>
              <input type="number" step="0.01" min="0" name="initial_price" id="materialPrice" class="fld" value="0" placeholder="e.g. 150.00" />
            </div>
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-exclamation-triangle-fill"></i> Alert Qty (Recipe Unit)</label>
              <input type="number" step="0.01" min="0" name="alert_qty" id="materialAlert" class="fld" value="0" />
            </div>
            <div class="col-12">
              <label class="lbl"><i class="bi bi-sticky-fill"></i> Notes</label>
              <input type="text" name="notes" id="materialNotes" class="fld" placeholder="Optional description" />
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="rm-btn rm-btn-primary"><i class="bi bi-save me-1"></i> Save Material</button>
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

// Auto conversion defaults when purchase unit changes
$(document).on('change', '#materialUnit', function() {
  var u = $(this).val().toLowerCase();
  var cUnit = $('#materialConsumptionUnit');
  var factor = $('#materialConversionFactor');

  if (u === 'kg') {
    cUnit.val('gram');
    factor.val('1000');
  } else if (u === 'litre') {
    cUnit.val('ml');
    factor.val('1000');
  } else if (u === 'dozen') {
    cUnit.val('piece');
    factor.val('12');
  } else if (u === 'gram' || u === 'piece' || u === 'ml') {
    cUnit.val(u);
    factor.val('1');
  }
});

// Edit material
$(document).on('click', '.editMaterial', function() {
  $('#materialEditId').val($(this).data('id'));
  $('#materialName').val($(this).data('name'));
  $('#materialUnit').val($(this).data('unit'));
  $('#materialConsumptionUnit').val($(this).data('cunit') || $(this).data('unit'));
  $('#materialConversionFactor').val($(this).data('factor') || 1);
  $('#materialPrice').val($(this).data('price') || 0);
  $('#materialAlert').val($(this).data('alert'));
  $('#materialOpeningStock').val($(this).data('opening-stock') || 0);
  $('#materialNotes').val($(this).data('notes'));
  $('#materialModalLabel').html('<i class="bi bi-pencil-square me-1 text-primary"></i> Edit Raw Material');
  $('#materialModal').modal('show');
});

// Reset material modal
$(document).on('click', '#resetMaterial', function() {
  $('#materialEditId').val('');
  $('#materialName').val('');
  $('#materialUnit').val('');
  $('#materialConsumptionUnit').val('');
  $('#materialConversionFactor').val('1');
  $('#materialPrice').val('0');
  $('#materialAlert').val('0');
  $('#materialNotes').val('');
  $('#materialModalLabel').html('<i class="bi bi-box-seam me-1 text-primary"></i> Add Raw Material');
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

// Print section report helper
function printReportSection(tableId, title) {
  var origTable = document.getElementById(tableId);
  if (!origTable) return;

  var rowsHtml = '';
  var isStock = tableId === 'stockTable';
  var isPurchase = tableId === 'purchaseTable';

  if (isStock) {
    var headers = '<tr><th style="width:25%;">Material & Unit</th><th style="width:38%;">Warehouse Breakdown</th><th style="width:20%;">Total / Alert</th><th style="width:17%;">Status</th></tr>';
    
    $('#stockTable tbody tr.stock-row').each(function() {
      if ($(this).css('display') !== 'none') {
        var id = $(this).find('td:eq(0)').text().trim();
        var name = $(this).find('.material-name-cell').text().trim();
        var unit = $(this).find('td:eq(2)').text().trim();
        var whHtml = $(this).find('.warehouse-cell').html().trim();
        var total = $(this).find('td:eq(4)').text().trim();
        var alert = $(this).find('td:eq(5)').text().trim();
        var status = $(this).find('.stock-badge').text().trim();

        rowsHtml += '<tr>';
        rowsHtml += '<td><strong>' + name + '</strong><br><span style="font-size:8.5px;color:#444;">' + id + ' (' + unit + ')</span></td>';
        rowsHtml += '<td>' + whHtml + '</td>';
        rowsHtml += '<td><strong>' + total + '</strong><br><span style="font-size:8.5px;color:#444;">Alert: ' + alert + '</span></td>';
        rowsHtml += '<td><strong>' + status + '</strong></td>';
        rowsHtml += '</tr>';
      }
    });

    var tableHtml = '<table><thead>' + headers + '</thead><tbody>' + rowsHtml + '</tbody></table>';
  } else if (isPurchase) {
    var headers = '<tr><th style="width:26%;">Invoice & Date</th><th style="width:24%;">Vendor & Storage</th><th style="width:32%;">Items Breakdown</th><th style="width:18%;">Total & By</th></tr>';

    $('#purchaseTable tbody tr').each(function() {
      if ($(this).css('display') !== 'none' && $(this).find('td').length >= 7) {
        var inv = $(this).find('td:eq(0)').text().trim();
        var date = $(this).find('td:eq(1)').text().trim();
        var vendor = $(this).find('td:eq(2)').text().trim();
        var wh = $(this).find('td:eq(3)').text().trim();
        var itemsHtml = $(this).find('td:eq(4)').html().trim();
        var total = $(this).find('td:eq(5)').text().trim();
        var creator = $(this).find('td:eq(6)').text().trim();

        rowsHtml += '<tr>';
        rowsHtml += '<td><strong>' + inv + '</strong><br><span style="font-size:8.5px;color:#444;">' + date + '</span></td>';
        rowsHtml += '<td><strong>' + vendor + '</strong><br><span style="font-size:8.5px;color:#444;">' + wh + '</span></td>';
        rowsHtml += '<td>' + itemsHtml + '</td>';
        rowsHtml += '<td><strong>' + total + '</strong><br><span style="font-size:8.5px;color:#444;">' + creator + '</span></td>';
        rowsHtml += '</tr>';
      }
    });

    var tableHtml = '<table><thead>' + headers + '</thead><tbody>' + rowsHtml + '</tbody></table>';
  } else {
    var clone = origTable.cloneNode(true);
    clone.querySelectorAll('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dt-buttons').forEach(el => el.remove());
    var tableHtml = clone.outerHTML;
  }

  var printWin = window.open('', '', 'width=850,height=700');
  printWin.document.write('<!DOCTYPE html><html><head><title>' + title + '</title>');
  printWin.document.write('<style>');
  printWin.document.write('* { box-sizing: border-box; font-family: "Arial Narrow", Arial, sans-serif; }');
  printWin.document.write('body { margin: 0; padding: 6px; color: #000; background: #fff; font-size: 9.5px; line-height: 1.3; }');
  printWin.document.write('.header { text-align: center; margin-bottom: 8px; border-bottom: 2px solid #000; padding-bottom: 4px; }');
  printWin.document.write('.header h2 { margin: 0 0 2px; font-size: 14px; font-weight: 800; text-transform: uppercase; }');
  printWin.document.write('.header h4 { margin: 0 0 2px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #111; }');
  printWin.document.write('.header p { margin: 0; font-size: 8.5px; color: #444; }');
  printWin.document.write('table { width: 100% !important; border-collapse: collapse !important; margin-top: 4px; font-size: 9.5px; line-height: 1.3; table-layout: fixed; }');
  printWin.document.write('th, td { border: 1px solid #000 !important; padding: 4px 4px !important; text-align: left; vertical-align: top; word-break: normal !important; overflow-wrap: normal !important; word-wrap: normal !important; white-space: normal !important; hyphens: none !important; }');
  printWin.document.write('th { background: #e2e8f0 !important; font-weight: 700; text-transform: uppercase; font-size: 9px; color: #000; }');
  printWin.document.write('td div { display: block !important; margin-bottom: 2px !important; padding-bottom: 2px; border-bottom: 1px dashed #ccc; font-size: 9px; }');
  printWin.document.write('td div:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0; }');
  printWin.document.write('.badge { background: none !important; color: #000 !important; border: none !important; font-weight: 700; padding: 0 !important; font-size: 9px !important; }');
  printWin.document.write('.btn, button, a, i { display: none !important; }');
  printWin.document.write('@media print {');
  printWin.document.write('  @page { size: auto; margin: 3mm; }');
  printWin.document.write('  body { padding: 0; }');
  printWin.document.write('  table { page-break-inside: auto; }');
  printWin.document.write('  tr { page-break-inside: avoid; page-break-after: auto; }');
  printWin.document.write('}');
  printWin.document.write('</style>');
  printWin.document.write('</head><body>');
  printWin.document.write('<div class="header"><h2>Memon Nimko</h2><h4>' + title + '</h4><p>Date: ' + new Date().toLocaleString() + '</p></div>');
  printWin.document.write(tableHtml);
  printWin.document.write('</body></html>');
  printWin.document.close();
  printWin.focus();
  setTimeout(function() {
    printWin.print();
    printWin.close();
  }, 400);
}

// Live Current Stock Filtering
$(document).on('keyup change', '#stockSearchInput, #stockWarehouseFilter, #stockStatusFilter', function() {
  var searchVal = $('#stockSearchInput').val().toLowerCase().trim();
  var whVal = $('#stockWarehouseFilter').val().toLowerCase().trim();
  var statusVal = $('#stockStatusFilter').val().toLowerCase().trim();

  $('#stockTable tbody tr.stock-row').each(function() {
    var nameText = $(this).find('.material-name-cell').text().toLowerCase();
    var whText = $(this).find('.warehouse-cell').text().toLowerCase();
    var statusAttr = ($(this).data('status') || '').toLowerCase();

    var matchSearch = !searchVal || nameText.indexOf(searchVal) !== -1;
    var matchWh = !whVal || whText.indexOf(whVal) !== -1;
    var matchStatus = !statusVal || statusAttr === statusVal;

    if (matchSearch && matchWh && matchStatus) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
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
  if ($.fn.DataTable.isDataTable('#materialTable')) $('#materialTable').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#purchaseTable')) $('#purchaseTable').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#recipesTable')) $('#recipesTable').DataTable().destroy();
  $('#materialTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: '_INPUT_', searchPlaceholder: 'Search materials...' } });
  $('#purchaseTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: '_INPUT_', searchPlaceholder: 'Search purchases...' } });
  $('#recipesTable').DataTable({ pageLength: 10, order: [[0, 'asc']], language: { search: '_INPUT_', searchPlaceholder: 'Search recipes...' } });
});
</script>
@endsection
