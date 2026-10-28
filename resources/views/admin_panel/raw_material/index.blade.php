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

/* ═══════ SELECT2 SEARCHABLE DROPDOWN (PURCHASE TAB) ═══════ */
#purchaseItemsTable .select2-container,
.purchase-form .select2-container {
  width: 100% !important;
}
#purchaseItemsTable .select2-container--default .select2-selection--single,
.purchase-form .select2-container--default .select2-selection--single {
  height: 42px !important;
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-sm) !important;
  display: flex !important;
  align-items: center;
  background: #fff;
  transition: all .2s;
}
#purchaseItemsTable .select2-container--default .select2-selection--single .select2-selection__rendered,
.purchase-form .select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 40px !important;
  color: var(--text) !important;
  font-size: .88rem;
  font-weight: 500;
  padding-left: .85rem !important;
  padding-right: 2rem !important;
}
#purchaseItemsTable .select2-container--default .select2-selection--single .select2-selection__arrow,
.purchase-form .select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 40px !important;
  right: 8px !important;
}
#purchaseItemsTable .select2-container--default.select2-container--focus .select2-selection--single,
#purchaseItemsTable .select2-container--default.select2-container--open .select2-selection--single,
.purchase-form .select2-container--default.select2-container--focus .select2-selection--single,
.purchase-form .select2-container--default.select2-container--open .select2-selection--single {
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px rgba(37,99,235,.12) !important;
}
.select2-dropdown {
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-sm) !important;
  box-shadow: var(--shadow-lg) !important;
  z-index: 9999 !important;
}
.select2-search--dropdown .select2-search__field {
  border: 1px solid var(--border) !important;
  border-radius: 8px !important;
  padding: .45rem .65rem !important;
  font-size: .85rem !important;
  outline: none !important;
}
.select2-search--dropdown .select2-search__field:focus {
  border-color: var(--accent) !important;
}
.select2-results__option--highlighted[aria-selected] {
  background-color: var(--accent) !important;
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
      $lowStockCount = $materials->filter(function($m) {
        $factor = (float)($m->conversion_factor ?? 1);
        $purQty = $factor > 0 ? ($m->currentStock() / $factor) : $m->currentStock();
        return $purQty <= (float)($m->alert_qty ?? 0);
      })->count();
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
    @php
      $activeTab = request('tab', request()->has('start_date') ? 'tab-stock' : 'tab-materials');
    @endphp
    <div class="rm-tabs">
      <button class="rm-tab {{ $activeTab === 'tab-materials' ? 'active' : '' }}" data-tab="tab-materials"><i class="bi bi-list-ul"></i> Materials List</button>
      <button class="rm-tab {{ $activeTab === 'tab-purchase' ? 'active' : '' }}" data-tab="tab-purchase"><i class="bi bi-cart-plus-fill"></i> New Purchase</button>
      <button class="rm-tab {{ $activeTab === 'tab-history' ? 'active' : '' }}" data-tab="tab-history"><i class="bi bi-clock-history"></i> Purchase History</button>
      <button class="rm-tab {{ $activeTab === 'tab-returns' ? 'active' : '' }}" data-tab="tab-returns"><i class="bi bi-arrow-return-left"></i> Purchase Returns</button>
      <button class="rm-tab {{ $activeTab === 'tab-stock' ? 'active' : '' }}" data-tab="tab-stock"><i class="bi bi-shop"></i> Current Branch Stock</button>
      <button class="rm-tab {{ $activeTab === 'tab-warehouse-stock' ? 'active' : '' }}" data-tab="tab-warehouse-stock"><i class="bi bi-building"></i> Warehouse Stock</button>
      <button class="rm-tab {{ $activeTab === 'tab-recipes' ? 'active' : '' }}" data-tab="tab-recipes"><i class="bi bi-egg-fried"></i> Product Recipes (BOM)</button>
    </div>

    {{-- TAB: MATERIALS --}}
    <div id="tab-materials" class="tab-content" style="display: {{ $activeTab === 'tab-materials' ? 'block' : 'none' }};">
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
                  <th>Branch Stock</th>
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
                  <td data-label="Branch Stock">
                    @php 
                      $sqty = $m->currentStock(); 
                      $factor = (float)($m->conversion_factor ?? 1);
                      $purQty = $factor > 0 ? round($sqty / $factor, 2) : $sqty;
                      $cUnit = $m->consumption_unit ?? $m->unit;
                      $whQty = $m->warehouseStock();
                      $whPurQty = $factor > 0 ? round($whQty / $factor, 2) : $whQty;
                    @endphp
                    <span class="stock-badge {{ $purQty > (float)$m->alert_qty ? 'stock-ok' : 'stock-low' }}">
                      {{ number_format($purQty, 2) }} {{ $m->unit }}
                    </span>
                    @if($factor > 1)
                      <div style="font-size:.72rem;" class="text-muted fw-semibold mt-1">({{ number_format($sqty, 2) }} {{ $cUnit }})</div>
                    @endif
                    @if($whQty > 0)
                      <div class="mt-1">
                        <span class="badge bg-primary-subtle text-primary border" style="cursor:pointer;font-size:.7rem;" onclick="switchTab('tab-warehouse-stock')" title="Click to view warehouse stock">
                          <i class="bi bi-building me-1"></i>WH: {{ number_format($whPurQty, 2) }} {{ $m->unit }}
                        </span>
                      </div>
                    @endif
                  </td>
                  <td class="malert fw-semibold" data-label="Alert Qty">
                    {{ number_format((float)($m->alert_qty ?? 0), 2) }} {{ $m->unit }}
                  </td>
                  <td class="text-center" data-label="Actions">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <button type="button" class="rm-btn rm-btn-sm viewMaterial"
                        style="background:#eff6ff;color:var(--accent);border:1px solid #bfdbfe;"
                        data-id="{{ $m->id }}"
                        data-name="{{ $m->name }}"
                        data-urdu="{{ $m->urdu_name ?? '' }}"
                        data-unit="{{ $m->unit }}"
                        data-cunit="{{ $m->consumption_unit ?? $m->unit }}"
                        data-factor="{{ (float)($m->conversion_factor ?? 1) }}"
                        data-price="{{ (float)$lastCost }}"
                        data-purqty="{{ number_format($purQty, 2) }}"
                        data-sqty="{{ number_format($sqty, 2) }}"
                        data-whqty="{{ number_format($whPurQty, 2) }}"
                        data-alert="{{ (float)($m->alert_qty ?? 0) }}"
                        data-notes="{{ $m->notes ?? '' }}"
                        title="View Details">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button type="button" class="rm-btn rm-btn-success rm-btn-sm editMaterial"
                        data-id="{{ $m->id }}"
                        data-name="{{ $m->name }}"
                        data-urdu="{{ $m->urdu_name ?? '' }}"
                        data-unit="{{ strtolower($m->unit) }}"
                        data-cunit="{{ strtolower($m->consumption_unit ?? $m->unit) }}"
                        data-factor="{{ (float)($m->conversion_factor ?? 1) }}"
                        data-price="{{ (float)$lastCost }}"
                        data-opening-stock="{{ (float)$purQty }}"
                        data-alert="{{ (float)($m->alert_qty ?? 0) }}"
                        data-notes="{{ $m->notes ?? '' }}"
                        title="Edit">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button type="button" class="rm-btn rm-btn-danger rm-btn-sm"
                        onclick="confirmDelete('{{ route('raw-materials.delete', $m->id) }}')"
                        title="Delete">
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
                <select name="vendor_id" class="fld" required onchange="document.getElementById('raw_material_vendor_name').value = this.options[this.selectedIndex].getAttribute('data-name') || '';">
                  <option value="">Select Vendor...</option>
                  @foreach($vendors as $v)
                  <option value="{{ $v->id }}" data-name="{{ $v->name }}">{{ $v->name }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="vendor_name" id="raw_material_vendor_name">
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
                      <select name="raw_material_id[]" class="fld select2-rm" required>
                        <option value="">Select Material...</option>
                        @foreach($materials as $m)
                        <option value="{{ $m->id }}" data-price="{{ $m->lastPurchaseCost() > 0 ? $m->lastPurchaseCost() : ($m->initial_price ?? 0) }}">{{ $m->name }} ({{ $m->unit }})</option>
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
                  <th style="width:145px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($purchases as $p)
                <tr>
                  <td style="font-weight:700;" data-label="Invoice">
                    <span class="badge bg-light text-dark border font-monospace">{{ $p->invoice_no }}</span>
                    @if(!empty($p->is_fully_returned))
                      <span class="badge bg-danger-subtle text-danger border ms-1" style="font-size:.65rem;" title="All items in this purchase returned">Fully Returned</span>
                    @elseif(!empty($p->is_partially_returned))
                      <span class="badge bg-warning-subtle text-warning-emphasis border ms-1" style="font-size:.65rem;" title="Refunded Rs {{ number_format($p->total_returned_amount ?? 0, 0) }}">Partially Returned</span>
                    @endif
                  </td>
                  <td data-label="Date">{{ $p->date }}</td>
                  <td class="fw-bold text-dark" data-label="Vendor">{{ $p->vendor_name ?? '-' }}</td>
                  <td data-label="Warehouse"><span class="badge bg-primary-subtle text-primary border fw-semibold">{{ $p->warehouse->warehouse_name ?? $p->warehouse->name ?? 'Main Stock' }}</span></td>
                  <td data-label="Items Breakdown">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge bg-secondary-subtle text-secondary border fw-semibold">
                        <i class="bi bi-box-seam me-1"></i>{{ count($p->items) }} {{ count($p->items) == 1 ? 'Item' : 'Items' }}
                      </span>
                      <button type="button" class="rm-btn rm-btn-sm viewPurchaseItemsBtn"
                        style="font-size:.75rem;padding:2px 8px;min-height:26px;background:#eff6ff;color:var(--accent);border:1px solid #bfdbfe;"
                        data-invoice="{{ $p->invoice_no }}"
                        data-date="{{ $p->date }}"
                        data-vendor="{{ $p->vendor_name ?? '-' }}"
                        data-warehouse="{{ $p->warehouse->warehouse_name ?? $p->warehouse->name ?? 'Main Stock' }}"
                        data-total="Rs {{ number_format($p->total_cost, 0) }}"
                        data-items="{{ json_encode($p->items->map(function($item) {
                          return [
                            'name' => $item->rawMaterial->name ?? '-',
                            'qty' => (float)$item->qty,
                            'unit' => $item->rawMaterial->unit ?? '',
                            'price' => number_format((float)$item->price_per_unit, 2),
                            'total' => number_format((float)$item->total, 2)
                          ];
                        })) }}"
                        title="View Items Details">
                        <i class="bi bi-eye me-1"></i> View
                      </button>
                    </div>
                  </td>
                  <td style="font-weight:700;" class="text-success" data-label="Total Cost">Rs {{ number_format($p->total_cost, 0) }}</td>
                  <td data-label="Created By"><span class="badge bg-secondary-subtle text-secondary">{{ $p->creator->name ?? '-' }}</span></td>
                  <td class="text-center" data-label="Actions">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      {{-- Return Purchase Button --}}
                      @if(!empty($p->is_fully_returned))
                        <button type="button" class="rm-btn rm-btn-sm" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;opacity:0.6;cursor:not-allowed;" title="Already Fully Returned" disabled>
                          <i class="bi bi-arrow-return-left"></i>
                        </button>
                      @else
                        <button type="button" class="rm-btn rm-btn-sm openReturnModalBtn"
                          style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;"
                          data-id="{{ $p->id }}"
                          data-invoice="{{ $p->invoice_no }}"
                          title="Return Raw Material">
                          <i class="bi bi-arrow-return-left"></i>
                        </button>
                      @endif
                      <button type="button" class="rm-btn rm-btn-success rm-btn-sm editPurchaseBtn"
                        data-id="{{ $p->id }}"
                        data-invoice="{{ $p->invoice_no }}"
                        data-date="{{ $p->date }}"
                        data-vendor-id="{{ $p->vendor_id }}"
                        data-vendor-name="{{ $p->vendor_name }}"
                        data-warehouse-id="{{ $p->warehouse_id }}"
                        data-notes="{{ $p->notes ?? '' }}"
                        data-items="{{ json_encode($p->items->map(function($item) {
                          return [
                            'raw_material_id' => $item->raw_material_id,
                            'qty' => (float)$item->qty,
                            'price_per_unit' => (float)$item->price_per_unit,
                            'total' => (float)$item->total,
                          ];
                        })) }}"
                        title="Edit Purchase">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <a href="{{ route('raw-materials.purchase.print', $p->id) }}" target="_blank" class="rm-btn rm-btn-primary rm-btn-sm" title="Print Invoice">
                        <i class="bi bi-printer"></i>
                      </a>
                      <button type="button" class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.purchase.delete', $p->id) }}')" title="Delete Purchase">
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

    {{-- TAB: PURCHASE RETURNS --}}
    <div id="tab-returns" class="tab-content" style="display: {{ $activeTab === 'tab-returns' ? 'block' : 'none' }};">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-arrow-return-left me-2" style="color:#ea580c;"></i>Raw Material Purchase Returns</h5>
              <span class="badge bg-warning-subtle text-warning-emphasis border fw-semibold">{{ isset($purchaseReturns) ? count($purchaseReturns) : 0 }} Returns</span>
            </div>
            <button class="rm-btn" style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;" onclick="printReportSection('returnsTable', 'Raw Material Purchase Returns')"><i class="bi bi-printer me-1"></i> Print Returns History</button>
          </div>
          <div style="overflow-x:auto;" id="returnsPrintArea">
            <table class="rm-tbl" id="returnsTable">
              <thead>
                <tr>
                  <th>Return Invoice</th>
                  <th>Date</th>
                  <th>Original Purchase</th>
                  <th>Vendor</th>
                  <th>Storage Location</th>
                  <th>Items Returned</th>
                  <th>Total Refund</th>
                  <th>Reason / Notes</th>
                  <th>Returned By</th>
                  <th style="width:110px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @if(isset($purchaseReturns))
                @forelse($purchaseReturns as $r)
                <tr>
                  <td style="font-weight:700;" data-label="Return Invoice">
                    <span class="badge bg-light text-danger border font-monospace">{{ $r->return_invoice }}</span>
                  </td>
                  <td data-label="Date">{{ $r->return_date }}</td>
                  <td data-label="Original Purchase">
                    @if($r->purchase)
                      <span class="badge bg-primary-subtle text-primary border font-monospace">{{ $r->purchase->invoice_no }}</span>
                    @else
                      <span class="text-muted small">-</span>
                    @endif
                  </td>
                  <td class="fw-bold text-dark" data-label="Vendor">{{ $r->vendor_name ?? ($r->vendor->name ?? '-') }}</td>
                  <td data-label="Storage Location">
                    <span class="badge bg-secondary-subtle text-secondary border fw-semibold">{{ $r->warehouse->warehouse_name ?? $r->warehouse->name ?? 'Main Stock' }}</span>
                  </td>
                  <td data-label="Items Returned">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge bg-secondary-subtle text-secondary border fw-semibold">
                        <i class="bi bi-box-seam me-1"></i>{{ count($r->items) }} {{ count($r->items) == 1 ? 'Item' : 'Items' }}
                      </span>
                      <button type="button" class="rm-btn rm-btn-sm viewReturnItemsBtn"
                        style="font-size:.75rem;padding:2px 8px;min-height:26px;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;"
                        data-invoice="{{ $r->return_invoice }}"
                        data-original="{{ $r->purchase->invoice_no ?? '-' }}"
                        data-date="{{ $r->return_date }}"
                        data-vendor="{{ $r->vendor_name ?? ($r->vendor->name ?? '-') }}"
                        data-warehouse="{{ $r->warehouse->warehouse_name ?? $r->warehouse->name ?? 'Main Stock' }}"
                        data-total="Rs {{ number_format($r->total_cost, 0) }}"
                        data-items="{{ json_encode($r->items->map(function($item) {
                          return [
                            'name' => $item->rawMaterial->name ?? '-',
                            'qty' => (float)$item->qty,
                            'unit' => $item->rawMaterial->unit ?? '',
                            'price' => number_format((float)$item->price_per_unit, 2),
                            'total' => number_format((float)$item->total, 2)
                          ];
                        })) }}"
                        title="View Returned Items">
                        <i class="bi bi-eye me-1"></i> View
                      </button>
                    </div>
                  </td>
                  <td style="font-weight:700;" class="text-danger" data-label="Total Refund">Rs {{ number_format($r->total_cost, 0) }}</td>
                  <td data-label="Reason / Notes"><small class="text-muted">{{ $r->remarks ?? '-' }}</small></td>
                  <td data-label="Returned By"><span class="badge bg-secondary-subtle text-secondary">{{ $r->creator->name ?? '-' }}</span></td>
                  <td class="text-center" data-label="Actions">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <a href="{{ route('raw-materials.purchase.return.print', $r->id) }}" target="_blank" class="rm-btn rm-btn-sm" style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;" title="Print Return Voucher">
                        <i class="bi bi-printer"></i>
                      </a>
                      <button type="button" class="rm-btn rm-btn-danger rm-btn-sm" onclick="confirmDelete('{{ route('raw-materials.purchase.return.delete', $r->id) }}')" title="Delete Return & Restore Stock">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;padding:2rem;color:var(--text-muted);">No raw material purchase returns recorded yet</td></tr>
                @endforelse
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB: CURRENT BRANCH STOCK --}}
    <div id="tab-stock" class="tab-content" style="display: {{ $activeTab === 'tab-stock' ? 'block' : 'none' }};">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
              <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-shop me-2" style="color:var(--accent);"></i>Current Branch Stock & Movement Report</h5>
              <div class="text-muted small mt-1" style="font-size:.78rem;">Shop floor inventory for active branch with opening stock, purchases, recipe usages, and current closing balance</div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button type="button" class="rm-btn rm-btn-sm" style="background:#eff6ff;color:var(--accent);border:1px solid #bfdbfe;" onclick="switchTab('tab-warehouse-stock')">
                <i class="bi bi-building me-1"></i> View Warehouse Stock
              </button>
              <button class="rm-btn rm-btn-primary" onclick="printReportSection('stockTable', 'Raw Material Branch Stock Report')"><i class="bi bi-printer me-1"></i> Print Stock Report</button>
            </div>
          </div>

          {{-- DATE & TIME FILTERS --}}
          <form method="GET" action="{{ route('raw-materials.index') }}" id="stockFilterForm" class="p-3 bg-white rounded-3 border mb-3 shadow-sm">
            <input type="hidden" name="tab" value="tab-stock">
            <div class="row g-2 align-items-end">
              <div class="col-6 col-md-2">
                <label class="lbl"><i class="bi bi-calendar-date"></i> From Date</label>
                <input type="date" name="start_date" id="stockStartDate" class="fld" value="{{ $startDate }}" required>
              </div>
              <div class="col-6 col-md-2">
                <label class="lbl"><i class="bi bi-clock"></i> From Time</label>
                <input type="time" name="start_time" id="stockStartTime" class="fld" value="{{ substr($startTime, 0, 5) }}" required>
              </div>
              <div class="col-6 col-md-2">
                <label class="lbl"><i class="bi bi-calendar-date"></i> To Date</label>
                <input type="date" name="end_date" id="stockEndDate" class="fld" value="{{ $endDate }}" required>
              </div>
              <div class="col-6 col-md-2">
                <label class="lbl"><i class="bi bi-clock"></i> To Time</label>
                <input type="time" name="end_time" id="stockEndTime" class="fld" value="{{ substr($endTime, 0, 5) }}" required>
              </div>
              <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="rm-btn rm-btn-primary flex-grow-1 justify-content-center">
                  <i class="bi bi-funnel-fill"></i> Filter Stock
                </button>
                <a href="{{ route('raw-materials.index', ['tab' => 'tab-stock']) }}" class="rm-btn" style="background:#f1f5f9;color:var(--text-sec);border:1px solid var(--border);" title="Reset to Today">
                  <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
              </div>
            </div>

            {{-- Quick Filter Pills --}}
            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top flex-wrap">
              <span class="text-muted small fw-bold" style="font-size:.74rem;text-transform:uppercase;">Quick Range:</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-range-btn" data-range="today" style="font-size:.76rem;border-radius:20px;">Today</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-range-btn" data-range="yesterday" style="font-size:.76rem;border-radius:20px;">Yesterday</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-range-btn" data-range="this_week" style="font-size:.76rem;border-radius:20px;">This Week</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-range-btn" data-range="this_month" style="font-size:.76rem;border-radius:20px;">This Month</button>
              <span class="ms-auto badge bg-light text-secondary border font-monospace" style="font-size:.75rem;">
                <i class="bi bi-clock-history me-1"></i>Active Period: {{ date('d-M-Y', strtotime($startDate)) }} {{ substr($startTime, 0, 5) }} &rarr; {{ date('d-M-Y', strtotime($endDate)) }} {{ substr($endTime, 0, 5) }}
              </span>
            </div>
          </form>

          {{-- SEARCH & STATUS FILTERS --}}
          <div class="row g-2 mb-3 p-3 bg-light rounded-3 border">
            <div class="col-md-6">
              <label class="lbl"><i class="bi bi-search"></i> Search Material</label>
              <input type="text" id="stockSearchInput" class="fld" placeholder="Search by material name..." />
            </div>
            <div class="col-md-6">
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
                  <th style="color:var(--accent);"><i class="bi bi-sunrise me-1"></i>Opening Stock</th>
                  <th style="color:var(--success);"><i class="bi bi-cart-plus me-1"></i>Purchase Qty</th>
                  <th style="color:var(--danger);"><i class="bi bi-egg-fried me-1"></i>Used Qty</th>
                  <th style="color:#0284c7;"><i class="bi bi-boxes me-1"></i>Current Stock</th>
                  <th>Alert Qty</th>
                  <th>Status</th>
                  <th>Warehouse Stock</th>
                  <th class="text-center" style="width:105px;">Usage Detail</th>
                </tr>
              </thead>
              <tbody>
                @forelse($materials as $m)
                  @php 
                    $factor = (float)($m->conversion_factor ?? 1);
                    if ($factor <= 0) $factor = 1;
                    $cUnit = $m->consumption_unit ?? $m->unit;

                    $openPur = (float)($m->calc_opening_stock_purchase ?? 0);
                    $openCons = (float)($m->calc_opening_stock_consumption ?? 0);

                    $purPur = (float)($m->calc_purchase_qty_purchase ?? 0);
                    $purCons = (float)($m->calc_purchase_qty_consumption ?? 0);

                    $usedPur = (float)($m->calc_used_qty_purchase ?? 0);
                    $usedCons = (float)($m->calc_used_qty_consumption ?? 0);

                    $currPur = (float)($m->calc_current_stock_purchase ?? 0);
                    $currCons = (float)($m->calc_current_stock_consumption ?? 0);

                    $statusStr = $m->calc_status ?? 'In Stock';
                    $whQty = $m->warehouseStock();
                    $whPurQty = $factor > 0 ? round($whQty / $factor, 2) : $whQty;
                  @endphp
                <tr class="stock-row" data-status="{{ strtolower($statusStr) }}">
                  <td class="fw-bold" data-label="#">{{ $m->id }}</td>
                  <td style="font-weight:700;" class="text-dark material-name-cell" data-label="Material">
                    {{ $m->name }}
                    @if($m->urdu_name)
                      <span class="d-block small text-muted font-normal">{{ $m->urdu_name }}</span>
                    @endif
                  </td>
                  <td data-label="Unit">
                    <span class="badge bg-light text-dark border">{{ strtoupper($m->unit) }}</span>
                    @if($factor > 1)
                      <div style="font-size:.7rem;" class="text-muted">1 {{ $m->unit }} = {{ number_format($factor, 0) }} {{ $cUnit }}</div>
                    @endif
                  </td>
                  <td style="font-weight:700;" class="open-stock-cell" data-label="Opening Stock">
                    <span class="text-dark">{{ number_format($openPur, 2) }} {{ $m->unit }}</span>
                    @if($factor > 1)
                      <div style="font-size:.72rem;" class="text-muted fw-semibold">({{ number_format($openCons, 2) }} {{ $cUnit }})</div>
                    @endif
                  </td>
                  <td style="font-weight:700;" class="purchase-qty-cell" data-label="Purchase Qty">
                    @if($purCons > 0)
                      <span class="badge bg-success-subtle text-success border fw-bold" style="font-size:.82rem;">
                        +{{ number_format($purPur, 2) }} {{ $m->unit }}
                      </span>
                      @if($factor > 1)
                        <div style="font-size:.72rem;" class="text-muted fw-semibold">({{ number_format($purCons, 2) }} {{ $cUnit }})</div>
                      @endif
                    @else
                      <span class="text-muted small">0.00</span>
                    @endif
                  </td>
                  <td style="font-weight:700;" class="used-qty-cell" data-label="Used Qty">
                    @if($usedCons > 0)
                      <span class="badge bg-danger-subtle text-danger border fw-bold" style="font-size:.82rem;">
                        -{{ number_format($usedCons, 2) }} {{ $cUnit }}
                      </span>
                      @if($factor > 1)
                        <div style="font-size:.72rem;" class="text-muted fw-semibold">({{ number_format($usedPur, 2) }} {{ $m->unit }})</div>
                      @endif
                    @else
                      <span class="text-muted small">0.00</span>
                    @endif
                  </td>
                  <td style="font-weight:800;" class="fs-6 current-stock-cell" data-label="Current Stock">
                    <span class="stock-badge {{ $statusStr === 'In Stock' ? 'stock-ok' : 'stock-low' }}">
                      {{ number_format($currPur, 2) }} {{ $m->unit }}
                    </span>
                    @if($factor > 1)
                      <div style="font-size:.72rem;" class="text-muted fw-semibold mt-1">({{ number_format($currCons, 2) }} {{ $cUnit }})</div>
                    @endif
                  </td>
                  <td data-label="Alert Qty">
                    {{ number_format((float)($m->alert_qty ?? 0), 2) }} {{ $m->unit }}
                  </td>
                  <td data-label="Status">
                    <span class="stock-badge {{ $statusStr === 'In Stock' ? 'stock-ok' : 'stock-low' }}">
                      {{ $statusStr }}
                    </span>
                  </td>
                  <td data-label="Warehouse Stock">
                    @if($whQty > 0)
                      <span class="badge bg-primary-subtle text-primary border" style="cursor:pointer;font-size:.78rem;" onclick="switchTab('tab-warehouse-stock')" title="Click to view warehouse breakdown">
                        <i class="bi bi-building me-1"></i>{{ number_format($whPurQty, 2) }} {{ $m->unit }}
                      </span>
                    @else
                      <span class="text-muted small" style="font-size:.75rem;">None</span>
                    @endif
                  </td>
                  <td class="text-center" data-label="Usage Detail">
                    @if($usedCons > 0)
                      <button type="button" class="rm-btn rm-btn-primary rm-btn-sm btn-view-usage" 
                        data-id="{{ $m->id }}" 
                        data-name="{{ $m->name }}" 
                        data-unit="{{ $m->unit }}" 
                        data-cunit="{{ $cUnit }}" 
                        data-used="{{ number_format($usedCons, 2) }}" 
                        data-used-pur="{{ number_format($usedPur, 2) }}">
                        <i class="bi bi-info-circle me-1"></i> Detail
                      </button>
                    @else
                      <button type="button" class="rm-btn rm-btn-sm" style="background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;cursor:not-allowed;" title="No recipe usage recorded in selected period" disabled>
                        <i class="bi bi-dash"></i> Detail
                      </button>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:2rem;color:var(--text-muted);">No materials available</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB: WAREHOUSE STOCK --}}
    <div id="tab-warehouse-stock" class="tab-content" style="display:none;">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
              <h5 style="font-weight:700;font-size:1.05rem;margin:0;"><i class="bi bi-building me-2" style="color:var(--accent);"></i>Raw Material Warehouse Stock</h5>
              <div class="text-muted small mt-1" style="font-size:.78rem;">Detailed stock levels stored across warehouses (independent from branch shop stock)</div>
            </div>
            <button class="rm-btn rm-btn-primary" onclick="printReportSection('warehouseStockTable', 'Raw Material Warehouse Stock Report')"><i class="bi bi-printer me-1"></i> Print Warehouse Report</button>
          </div>

          {{-- WAREHOUSE KPI MINI CARDS --}}
          @php
            $whTotalItems = $warehouseStocks->where('qty', '>', 0)->count();
            $whUniqueWarehouses = $warehouseStocks->pluck('warehouse_id')->unique()->filter()->count();
            $whTotalEstValue = 0;
            foreach ($warehouseStocks as $ws) {
              if ($ws->rawMaterial) {
                $f = (float)($ws->rawMaterial->conversion_factor ?? 1);
                $pQty = $f > 0 ? ((float)$ws->qty / $f) : (float)$ws->qty;
                $whTotalEstValue += $pQty * (float)$ws->rawMaterial->lastPurchaseCost();
              }
            }
          @endphp
          <div class="row g-2 mb-3">
            <div class="col-6 col-md-4">
              <div class="p-2 px-3 rounded-3 border bg-white d-flex align-items-center justify-content-between shadow-sm">
                <div>
                  <span class="text-muted d-block small" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Stocked Materials</span>
                  <strong class="fs-6 text-dark">{{ $whTotalItems }} Items in Stock</strong>
                </div>
                <div class="rounded-circle p-2 bg-primary-subtle text-primary"><i class="bi bi-boxes fs-5"></i></div>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="p-2 px-3 rounded-3 border bg-white d-flex align-items-center justify-content-between shadow-sm">
                <div>
                  <span class="text-muted d-block small" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Active Warehouses</span>
                  <strong class="fs-6 text-dark">{{ $whUniqueWarehouses }} Warehouses</strong>
                </div>
                <div class="rounded-circle p-2 bg-info-subtle text-info"><i class="bi bi-building fs-5"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="p-2 px-3 rounded-3 border bg-white d-flex align-items-center justify-content-between shadow-sm">
                <div>
                  <span class="text-muted d-block small" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Est. Warehouse Value</span>
                  <strong class="fs-6 text-success">Rs {{ number_format($whTotalEstValue, 2) }}</strong>
                </div>
                <div class="rounded-circle p-2 bg-success-subtle text-success"><i class="bi bi-cash-stack fs-5"></i></div>
              </div>
            </div>
          </div>

          {{-- WAREHOUSE FILTERS --}}
          <div class="row g-2 mb-3 p-3 bg-light rounded-3 border">
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-search"></i> Search Material</label>
              <input type="text" id="whStockSearchInput" class="fld" placeholder="Search by material name..." />
            </div>
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-building"></i> Filter by Warehouse</label>
              <select id="whStockWarehouseFilter" class="fld">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                <option value="{{ strtolower($w->warehouse_name ?? $w->name) }}">{{ $w->warehouse_name ?? $w->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="lbl"><i class="bi bi-funnel"></i> Filter by Stock Status</label>
              <select id="whStockStatusFilter" class="fld">
                <option value="">All Statuses</option>
                <option value="in stock">In Stock</option>
                <option value="low stock">Low Stock</option>
                <option value="out of stock">Out of Stock</option>
              </select>
            </div>
          </div>

          <div style="overflow-x:auto;" id="warehouseStockPrintArea">
            <table class="rm-tbl" id="warehouseStockTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Material Name</th>
                  <th>Warehouse</th>
                  <th>Warehouse Stock</th>
                  <th>Recipe Qty</th>
                  <th>Purchase Price</th>
                  <th>Est. Total Value</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($warehouseStocks as $ws)
                  @php
                    $m = $ws->rawMaterial;
                    if (!$m) continue;
                    $factor = (float)($m->conversion_factor ?? 1);
                    $rawQty = (float)$ws->qty;
                    $purQty = $factor > 0 ? round($rawQty / $factor, 2) : $rawQty;
                    $alertInConsumption = (float)($m->alert_qty ?? 0) * ($factor > 0 ? $factor : 1);
                    $statusStr = $rawQty > $alertInConsumption ? 'In Stock' : ($rawQty > 0 ? 'Low Stock' : 'Out of Stock');
                    $lastCost = $m->lastPurchaseCost();
                    $estValue = $purQty * $lastCost;
                    $whName = $ws->warehouse->warehouse_name ?? $ws->warehouse->name ?? 'Warehouse #' . $ws->warehouse_id;
                  @endphp
                  <tr class="wh-stock-row" data-status="{{ strtolower($statusStr) }}" data-warehouse="{{ strtolower($whName) }}">
                    <td class="fw-bold" data-label="#">{{ $m->id }}</td>
                    <td style="font-weight:700;" class="text-dark wh-material-name-cell" data-label="Material">{{ $m->name }}</td>
                    <td class="wh-name-cell" data-label="Warehouse">
                      <span class="badge bg-primary-subtle text-primary border font-semibold px-2 py-1">
                        <i class="bi bi-building me-1"></i>{{ $whName }}
                      </span>
                    </td>
                    <td style="font-weight:800;" class="fs-6" data-label="Warehouse Stock">
                      <span class="stock-badge {{ $rawQty > $alertInConsumption ? 'stock-ok' : 'stock-low' }}">
                        {{ number_format($purQty, 2) }} {{ $m->unit }}
                      </span>
                    </td>
                    <td data-label="Recipe Qty">
                      <span class="badge bg-light text-secondary border font-monospace">
                        {{ number_format($rawQty, 2) }} {{ $m->consumption_unit ?? $m->unit }}
                      </span>
                    </td>
                    <td data-label="Purchase Price">
                      <span class="text-muted fw-semibold" style="font-size:.84rem;">Rs {{ number_format($lastCost, 2) }} / {{ $m->unit }}</span>
                    </td>
                    <td style="font-weight:700;" class="text-success" data-label="Est. Total Value">
                      Rs {{ number_format($estValue, 2) }}
                    </td>
                    <td data-label="Status">
                      <span class="stock-badge {{ $rawQty > $alertInConsumption ? 'stock-ok' : 'stock-low' }}">
                        {{ $statusStr }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:var(--text-muted);"><i class="bi bi-building-slash fs-3 d-block mb-2 text-secondary"></i>No warehouse stock recorded yet. When creating purchases, select a warehouse to store materials there.</td></tr>
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
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge bg-primary-subtle text-primary border fw-semibold">
                        <i class="bi bi-layers-fill me-1"></i>{{ count($p->bom) }} Ingredients
                      </span>
                      <button type="button" class="rm-btn rm-btn-sm viewRecipeBtn"
                        style="font-size:.78rem;padding:3px 10px;min-height:28px;background:#eff6ff;color:var(--accent);border:1px solid #bfdbfe;"
                        data-product="{{ json_encode([
                          'id' => $p->id,
                          'code' => $p->item_code,
                          'name' => $p->item_name,
                          'unit' => strtoupper($p->unit_type ?? 'Piece'),
                          'edit_url' => route('products.edit', $p->id),
                          'items' => $p->bom->map(function($b) {
                            return [
                              'material' => $b->rawMaterial->name ?? 'Material',
                              'urdu_name' => $b->rawMaterial->urdu_name ?? '',
                              'qty' => (float)$b->qty_per_unit,
                              'unit' => $b->rawMaterial->consumption_unit ?? $b->rawMaterial->unit ?? '',
                              'purchase_unit' => $b->rawMaterial->unit ?? ''
                            ];
                          })
                        ]) }}"
                        title="View Recipe Details">
                        <i class="bi bi-eye me-1"></i> View Details
                      </button>
                    </div>
                  </td>
                  <td class="text-center" data-label="Action">
                    <a href="{{ route('products.edit', $p->id) }}" class="rm-btn rm-btn-success rm-btn-sm" title="Edit Recipe">
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
              <label class="lbl"><i class="bi bi-fonts"></i> Urdu Name (Optional)</label>
              <input type="text" name="urdu_name" id="materialUrduName" class="fld" placeholder="اردو نام" />
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
              <label class="lbl"><i class="bi bi-box-seam-fill"></i> Current Stock (Purchase Unit)</label>
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
              <label class="lbl"><i class="bi bi-exclamation-triangle-fill"></i> Alert Qty (Purchase Unit)</label>
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
          <button type="submit" id="materialModalSubmitBtn" class="rm-btn rm-btn-primary"><i class="bi bi-save me-1"></i> Save Material</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- VIEW RECIPE DETAIL MODAL --}}
<div class="modal fade" id="viewRecipeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-egg-fried me-2 text-warning"></i> Product Recipe Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="p-3 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <span class="text-muted small d-block">Product:</span>
            <strong id="vrModalProductName" class="fs-5 text-dark"></strong>
            <span id="vrModalProductCode" class="badge bg-light text-dark border ms-2 font-monospace"></span>
          </div>
          <div>
            <span class="badge bg-primary-subtle text-primary border px-2 py-1" id="vrModalUnit"></span>
          </div>
        </div>
        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-list-check me-1 text-primary"></i> Required Bill of Materials (BOM)</h6>
        <div class="table-responsive border rounded-3 overflow-hidden">
          <table class="table table-sm table-striped mb-0" style="font-size:.85rem;">
            <thead class="table-light">
              <tr>
                <th style="width:45px;">#</th>
                <th>Raw Material</th>
                <th>Required Qty</th>
                <th>Usage Unit</th>
                <th>Purchase / Base Unit</th>
              </tr>
            </thead>
            <tbody id="vrModalItemsBody"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3 justify-content-between">
        <span class="text-muted small" id="vrModalItemCount"></span>
        <div>
          <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Close</button>
          <a href="#" id="vrModalEditLink" class="rm-btn rm-btn-success"><i class="bi bi-pencil-square me-1"></i> Edit Recipe</a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- VIEW PURCHASE ITEMS DETAIL MODAL --}}
<div class="modal fade" id="viewPurchaseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-cart-check me-2 text-info"></i> Purchase Invoice Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-2 p-3 bg-light rounded-3 border mb-3">
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Invoice No:</span>
            <strong id="vpModalInvoice" class="font-monospace text-primary"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Date:</span>
            <strong id="vpModalDate" class="text-dark"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Vendor:</span>
            <strong id="vpModalVendor" class="text-dark"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Warehouse:</span>
            <strong id="vpModalWarehouse" class="text-dark"></strong>
          </div>
        </div>
        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-box-seam me-1 text-primary"></i> Purchased Raw Materials</h6>
        <div class="table-responsive border rounded-3 overflow-hidden">
          <table class="table table-sm table-striped mb-0" style="font-size:.85rem;">
            <thead class="table-light">
              <tr>
                <th style="width:45px;">#</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
              </tr>
            </thead>
            <tbody id="vpModalItemsBody"></tbody>
            <tfoot class="table-light fw-bold">
              <tr>
                <td colspan="4" class="text-end">Grand Total:</td>
                <td id="vpModalTotal" class="text-success fs-6"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- VIEW MATERIAL DETAIL MODAL --}}
<div class="modal fade" id="viewMaterialModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-info-circle-fill me-2 text-info"></i> Raw Material Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center pb-3 border-bottom mb-3">
          <h4 id="vmName" class="fw-bold text-dark mb-1"></h4>
          <span id="vmUrdu" class="text-muted d-block" style="font-size:.9rem;"></span>
          <span id="vmUnitBadge" class="badge bg-primary-subtle text-primary border mt-2"></span>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Branch Stock:</span>
              <strong id="vmBranchStock" class="text-success fs-6"></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Warehouse Stock:</span>
              <strong id="vmWhStock" class="text-primary fs-6"></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Recipe Usage Stock:</span>
              <strong id="vmRecipeStock" class="text-secondary"></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Last Purchase Price:</span>
              <strong id="vmPrice" class="text-success"></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Alert Threshold:</span>
              <strong id="vmAlert" class="text-danger"></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Unit Conversion:</span>
              <strong id="vmConversion" class="text-dark small"></strong>
            </div>
          </div>
          <div class="col-12" id="vmNotesContainer" style="display:none;">
            <div class="p-2 border rounded bg-light">
              <span class="text-muted d-block small">Notes:</span>
              <span id="vmNotes" class="text-dark small"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- RECIPE USAGE DETAIL MODAL --}}
<div class="modal fade" id="usageDetailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <div>
          <h5 class="modal-title fw-bold" id="usageDetailModalLabel">
            <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Recipe Usage Breakdown
          </h5>
          <div class="text-white-50 small mt-1">Product-wise consumption details for selected time period</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3 p-md-4">
        {{-- Summary Banner --}}
        <div class="p-3 rounded-3 border bg-light mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <span class="text-muted d-block small text-uppercase fw-bold" style="font-size:.72rem;">Raw Material</span>
            <strong class="fs-5 text-dark" id="modalMaterialName">-</strong>
          </div>
          <div>
            <span class="text-muted d-block small text-uppercase fw-bold" style="font-size:.72rem;">Filtered Period</span>
            <span class="badge bg-white text-dark border px-2 py-1 font-monospace" id="modalFilterPeriod" style="font-size:.8rem;">-</span>
          </div>
          <div class="text-md-end">
            <span class="text-muted d-block small text-uppercase fw-bold" style="font-size:.72rem;">Total Used Qty</span>
            <strong class="fs-5 text-danger" id="modalTotalUsed">-</strong>
          </div>
        </div>

        {{-- Loading Spinner --}}
        <div id="usageDetailLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="text-muted small mt-2">Loading recipe usage breakdown...</p>
        </div>

        {{-- Table Container --}}
        <div id="usageDetailContent" style="display:none; overflow-x:auto;">
          <table class="rm-tbl" id="usageDetailTable">
            <thead>
              <tr>
                <th>Date & Time</th>
                <th>Batch #</th>
                <th>Product Used In</th>
                <th>Batch Output</th>
                <th style="color:var(--danger);">Material Used</th>
                <th>Recipe / Unit</th>
                <th>Created By</th>
              </tr>
            </thead>
            <tbody id="usageDetailTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
            <tfoot>
              <tr style="background:#f8fafc; font-weight:700;">
                <td colspan="4" class="text-end">Total Material Used:</td>
                <td style="color:var(--danger);" id="modalFooterTotalUsed">-</td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>

        {{-- Empty State --}}
        <div id="usageDetailEmpty" class="text-center py-4" style="display:none;">
          <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
          <p class="text-muted fw-semibold">No product recipe usage found for this material in the selected date & time period.</p>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-2 d-flex justify-content-between">
        <button type="button" class="rm-btn rm-btn-sm" style="background:#fff;border:1px solid var(--border);" onclick="printModalSection('usageDetailContent')">
          <i class="bi bi-printer me-1"></i> Print Breakdown
        </button>
        <button type="button" class="rm-btn rm-btn-primary rm-btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{{-- EDIT PURCHASE MODAL --}}
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-pencil-square me-2 text-warning"></i> Edit Raw Material Purchase (<span id="epModalInvoice" class="font-monospace text-info"></span>)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="edit-purchase-form" action="{{ route('raw-materials.purchase.store') }}" method="POST">
        @csrf
        <input type="hidden" name="edit_id" id="epEditId" />
        <input type="hidden" name="vendor_name" id="epVendorName" />
        <div class="modal-body p-4">
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-calendar-date"></i> Date</label>
              <input type="date" name="date" id="epDate" class="fld" required />
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-person-fill"></i> Vendor</label>
              <select name="vendor_id" id="epVendorId" class="fld" required onchange="document.getElementById('epVendorName').value = this.options[this.selectedIndex].getAttribute('data-name') || '';">
                <option value="">Select Vendor...</option>
                @foreach($vendors as $v)
                <option value="{{ $v->id }}" data-name="{{ $v->name }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-houses-fill"></i> Warehouse</label>
              <select name="warehouse_id" id="epWarehouseId" class="fld">
                <option value="">Main Stock (No Warehouse)</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->warehouse_name ?? $w->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-sticky-fill"></i> Notes</label>
              <input type="text" name="notes" id="epNotes" class="fld" placeholder="Optional notes" />
            </div>
          </div>

          <div class="table-responsive mb-3 border rounded-3 overflow-hidden">
            <table class="rm-tbl" id="epItemsTable">
              <thead>
                <tr>
                  <th style="width:38%;">Raw Material</th>
                  <th style="width:20%;">Qty</th>
                  <th style="width:20%;">Price/Unit</th>
                  <th style="width:17%;">Total</th>
                  <th style="width:5%;"></th>
                </tr>
              </thead>
              <tbody id="epItemsBody">
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border">
            <div>
              <button type="button" class="rm-btn rm-btn-sm rm-btn-primary" id="epAddRowBtn"><i class="bi bi-plus-lg me-1"></i> Add Another Item</button>
            </div>
            <div>
              <strong>Total Cost: </strong><span id="epTotalCost" class="fs-5 fw-bold" style="color:var(--accent);">Rs 0</span>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
          <button type="button" class="rm-btn" style="background:#fff;border:1px solid var(--border);" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="epSubmitBtn" class="rm-btn rm-btn-success"><i class="bi bi-save me-1"></i> Update Purchase</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- RAW MATERIAL PURCHASE RETURN MODAL --}}
<div class="modal fade" id="returnPurchaseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#7c2d12,#ea580c);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-arrow-return-left me-2"></i> Raw Material Purchase Return (<span id="retModalInvoice" class="font-monospace text-warning"></span>)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="purchaseReturnForm" action="{{ route('raw-materials.purchase.return.store') }}" method="POST">
        @csrf
        <input type="hidden" name="purchase_id" id="retPurchaseId" />
        <div class="modal-body p-4">
          <div class="row g-3 mb-3 p-3 bg-light rounded-3 border">
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-calendar-check"></i> Return Date</label>
              <input type="date" name="return_date" id="retDate" class="fld" value="{{ date('Y-m-d') }}" required />
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-person-fill"></i> Vendor</label>
              <input type="text" id="retVendorName" class="fld" readonly style="background:#f1f5f9;" />
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-houses-fill"></i> Storage Location</label>
              <input type="text" id="retWarehouseName" class="fld" readonly style="background:#f1f5f9;" />
            </div>
            <div class="col-md-3">
              <label class="lbl"><i class="bi bi-chat-left-text"></i> Return Reason / Remarks</label>
              <input type="text" name="notes" id="retNotes" class="fld" placeholder="e.g. Damaged, Excess, Quality Issue..." />
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-1" style="color:#ea580c;"></i> Enter Quantities to Return</h6>
            <span class="text-muted small">Specify return quantity for each item (leave 0 or empty for items not returning)</span>
          </div>

          <div class="table-responsive border rounded-3 overflow-hidden mb-3">
            <table class="table table-sm align-middle mb-0" id="retItemsTable" style="font-size:.88rem;">
              <thead class="table-light">
                <tr>
                  <th>Raw Material</th>
                  <th class="text-center" style="width:110px;">Purchased</th>
                  <th class="text-center" style="width:110px;">Already Ret.</th>
                  <th class="text-center" style="width:110px;">Available</th>
                  <th class="text-center" style="width:110px;">Current Stock</th>
                  <th class="text-end" style="width:120px;">Price / Unit</th>
                  <th style="width:150px;">Return Qty</th>
                  <th class="text-end" style="width:130px;">Return Total</th>
                </tr>
              </thead>
              <tbody id="retItemsBody"></tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="7" class="text-end fw-bold fs-6">Total Return Amount:</td>
                  <td class="text-end fw-bold fs-6 text-danger" id="retTotalCost">Rs 0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
          <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="rm-btn" id="retSubmitBtn" style="background:#ea580c;color:#fff;"><i class="bi bi-check2-circle me-1"></i> Submit Purchase Return</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- VIEW RETURN ITEMS DETAIL MODAL --}}
<div class="modal fade" id="viewReturnModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:none;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#7c2d12,#ea580c);color:#fff;border:none;">
        <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-arrow-return-left me-2 text-warning"></i> Purchase Return Details (<span id="vrModalInvoice" class="font-monospace text-warning"></span>)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-2 p-3 bg-light rounded-3 border mb-3">
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Return Invoice:</span>
            <strong id="vrModalInvoice2" class="font-monospace text-danger"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Original Purchase:</span>
            <strong id="vrModalOriginal" class="font-monospace text-primary"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Return Date:</span>
            <strong id="vrModalDate" class="text-dark"></strong>
          </div>
          <div class="col-6 col-md-3">
            <span class="text-muted small d-block">Vendor:</span>
            <strong id="vrModalVendor" class="text-dark"></strong>
          </div>
        </div>
        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-box-seam me-1 text-danger"></i> Returned Raw Materials</h6>
        <div class="table-responsive border rounded-3 overflow-hidden">
          <table class="table table-sm table-striped mb-0" style="font-size:.85rem;">
            <thead class="table-light">
              <tr>
                <th style="width:45px;">#</th>
                <th>Material</th>
                <th>Returned Quantity</th>
                <th>Unit Price</th>
                <th>Refund Total</th>
              </tr>
            </thead>
            <tbody id="vrModalItemsBody"></tbody>
            <tfoot class="table-light fw-bold">
              <tr>
                <td colspan="4" class="text-end">Total Refund:</td>
                <td id="vrModalTotal" class="text-danger fs-6"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="rm-btn" style="background:#fff;color:var(--text-sec);border:1px solid var(--border);" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<template id="materialOptionsTemplate">
  <option value="">Select Material...</option>
  @foreach($materials as $m)
  <option value="{{ $m->id }}" data-price="{{ $m->lastPurchaseCost() > 0 ? $m->lastPurchaseCost() : ($m->initial_price ?? 0) }}">{{ $m->name }} ({{ $m->unit }})</option>
  @endforeach
</template>
@endsection

@section('scripts')
<script>
// Initialize Select2 on Material Dropdowns
function initMaterialSelect2($elements) {
  if (typeof $.fn.select2 === 'undefined') return;
  var $targets = $elements ? $($elements) : $('#purchaseItemsBody .select2-rm');
  $targets.each(function() {
    var $el = $(this);
    if ($el.hasClass('select2-hidden-accessible')) {
      $el.select2('destroy');
    }
    $el.select2({
      width: '100%',
      placeholder: 'Select Material...',
      allowClear: true
    });
  });
}

// Tab switching
window.switchTab = function(tabId) {
  document.querySelectorAll('.rm-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
  var targetTab = document.querySelector('.rm-tab[data-tab="' + tabId + '"]');
  if (targetTab) targetTab.classList.add('active');
  var targetContent = document.getElementById(tabId);
  if (targetContent) {
    targetContent.style.display = 'block';
    if (tabId === 'tab-purchase') {
      setTimeout(function() {
        initMaterialSelect2();
      }, 50);
    }
  }
};

document.querySelectorAll('.rm-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    window.switchTab(this.dataset.tab);
  });
});

// Material form submit
$(document).on('submit', '.material-form', function(e) {
  e.preventDefault();
  var form = $(this);
  var fd = new FormData(this);
  var url = form.attr('action');
  var $btn = form.find(':submit');
  var btnText = $btn.html();
  $btn.attr('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Saving...');

  $.ajax({
    url: url,
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(data) {
      $btn.attr('disabled', false).html(btnText);
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: data.success,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          if (data.reload) window.location.reload();
        });
      } else if (data.errors) {
        var errHtml = '';
        $.each(data.errors, function(k, v) {
          errHtml += '<div class="text-danger small">' + (Array.isArray(v) ? v.join(', ') : v) + '</div>';
        });
        Swal.fire({ icon: 'error', title: 'Validation Errors', html: errHtml });
      } else if (data.error) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.error });
      }
    },
    error: function(xhr) {
      $btn.attr('disabled', false).html(btnText);
      var msg = 'Failed to save raw material.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        msg = xhr.responseJSON.message;
      }
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }
  });
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
  var id = $(this).data('id');
  var name = $(this).data('name');
  var urdu = $(this).data('urdu') || '';
  var unit = ($(this).data('unit') || '').toString().toLowerCase();
  var cunit = ($(this).data('cunit') || unit).toString().toLowerCase();
  var factor = $(this).data('factor') || 1;
  var price = $(this).data('price') || 0;
  var alert = $(this).data('alert') || 0;
  var openingStock = $(this).data('opening-stock') || 0;
  var notes = $(this).data('notes') || '';

  $('#materialEditId').val(id);
  $('#materialName').val(name);
  $('#materialUrduName').val(urdu);

  if (unit && $('#materialUnit option[value="' + unit + '"]').length === 0) {
    $('#materialUnit').append(new Option(unit, unit, true, true));
  }
  $('#materialUnit').val(unit);

  if (cunit && $('#materialConsumptionUnit option[value="' + cunit + '"]').length === 0) {
    $('#materialConsumptionUnit').append(new Option(cunit, cunit, true, true));
  }
  $('#materialConsumptionUnit').val(cunit);

  $('#materialConversionFactor').val(factor);
  $('#materialPrice').val(price);
  $('#materialAlert').val(alert);
  $('#materialOpeningStock').val(openingStock);
  $('#materialNotes').val(notes);

  $('#materialModalLabel').html('<i class="bi bi-pencil-square me-1 text-primary"></i> Edit Raw Material');
  $('#materialModalSubmitBtn').html('<i class="bi bi-check-circle me-1"></i> Update Material');
  $('#materialModal').modal('show');
});

// Reset material modal
$(document).on('click', '#resetMaterial', function() {
  $('#materialEditId').val('');
  $('#materialName').val('');
  $('#materialUrduName').val('');
  $('#materialUnit').val('');
  $('#materialConsumptionUnit').val('');
  $('#materialConversionFactor').val('1');
  $('#materialPrice').val('0');
  $('#materialAlert').val('0');
  $('#materialOpeningStock').val('0');
  $('#materialNotes').val('');
  $('#materialModalLabel').html('<i class="bi bi-box-seam me-1 text-primary"></i> Add Raw Material');
  $('#materialModalSubmitBtn').html('<i class="bi bi-save me-1"></i> Save Material');
});

// Purchase form - add row
$(document).on('click', '#addPurchaseRow', function() {
  var tbody = document.getElementById('purchaseItemsBody');
  var firstRow = tbody.querySelector('.pitem-row');
  var newRow = firstRow.cloneNode(true);
  
  // Clean select2 container and attributes from cloned element
  $(newRow).find('.select2-container').remove();
  var $sel = $(newRow).find('.select2-rm');
  $sel.removeClass('select2-hidden-accessible');
  $sel.removeAttr('data-select2-id tabindex aria-hidden');
  $sel.find('option').removeAttr('data-select2-id');
  $sel.val('');

  newRow.querySelectorAll('input').forEach(inp => inp.value = '');
  newRow.querySelector('.ptotal').textContent = '0';
  var addBtn = newRow.querySelector('.add-row-btn');
  if (addBtn) addBtn.style.display = 'none';
  var rmBtn = newRow.querySelector('.remove-row');
  if (rmBtn) rmBtn.style.display = '';
  newRow.querySelectorAll('.pqty, .pprice').forEach(inp => inp.addEventListener('input', function() { calculateRowTotal(this); calculatePurchaseTotal(); }));
  tbody.appendChild(newRow);

  // Initialize Select2 on the new row
  initMaterialSelect2($(newRow).find('.select2-rm'));
});

// Remove row
$(document).on('click', '.remove-row', function() {
  var row = $(this).closest('tr');
  if (row.find('.select2-rm').hasClass('select2-hidden-accessible')) {
    row.find('.select2-rm').select2('destroy');
  }
  row.remove();
  calculatePurchaseTotal();
});

// Auto-fill price on material selection
$(document).on('change', '.select2-rm', function() {
  var row = $(this).closest('tr');
  var price = $(this).find(':selected').data('price');
  var priceInput = row.find('.pprice');
  if (price !== undefined && price !== null && price > 0 && (priceInput.val() === '' || priceInput.val() == 0)) {
    priceInput.val(price);
  }
  calculateRowTotal(priceInput[0] || this);
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
  var isWarehouseStock = tableId === 'warehouseStockTable';
  var isPurchase = tableId === 'purchaseTable';
  var isReturns = tableId === 'returnsTable';

  if (isStock) {
    var sD = $('#stockStartDate').val() || '';
    var sT = $('#stockStartTime').val() || '';
    var eD = $('#stockEndDate').val() || '';
    var eT = $('#stockEndTime').val() || '';
    if (sD && eD) {
      title += ' (' + sD + ' ' + sT + ' to ' + eD + ' ' + eT + ')';
    }
    var headers = '<tr>' +
      '<th style="width:20%;">Material & Unit</th>' +
      '<th style="width:16%;">Opening Stock</th>' +
      '<th style="width:16%;">Purchase Qty</th>' +
      '<th style="width:16%;">Used Qty</th>' +
      '<th style="width:16%;">Current Stock</th>' +
      '<th style="width:16%;">Status / Alert</th>' +
      '</tr>';
    
    $('#stockTable tbody tr.stock-row').each(function() {
      if ($(this).css('display') !== 'none' && $(this).find('td').length >= 8) {
        var id = $(this).find('td:eq(0)').text().trim();
        var name = $(this).find('.material-name-cell').text().trim();
        var unit = $(this).find('td:eq(2)').text().trim();
        var openStock = $(this).find('.open-stock-cell').text().trim();
        var purQty = $(this).find('.purchase-qty-cell').text().trim();
        var usedQty = $(this).find('.used-qty-cell').text().trim();
        var currStock = $(this).find('.current-stock-cell').text().trim();
        var alert = $(this).find('td:eq(7)').text().trim();
        var status = $(this).find('td:eq(8)').text().trim();

        rowsHtml += '<tr>';
        rowsHtml += '<td><strong>' + name + '</strong><br><span style="font-size:8.5px;color:#444;">ID: ' + id + ' (' + unit + ')</span></td>';
        rowsHtml += '<td><strong>' + openStock + '</strong></td>';
        rowsHtml += '<td>' + purQty + '</td>';
        rowsHtml += '<td><span style="color:#b91c1c;">' + usedQty + '</span></td>';
        rowsHtml += '<td><strong>' + currStock + '</strong></td>';
        rowsHtml += '<td>' + status + '<br><span style="font-size:8.5px;color:#444;">Alert: ' + alert + '</span></td>';
        rowsHtml += '</tr>';
      }
    });

    var tableHtml = '<table><thead>' + headers + '</thead><tbody>' + rowsHtml + '</tbody></table>';
  } else if (isWarehouseStock) {
    var headers = '<tr><th style="width:25%;">Material Name</th><th style="width:20%;">Warehouse</th><th style="width:20%;">Warehouse Stock</th><th style="width:20%;">Recipe Qty</th><th style="width:15%;">Est. Value</th></tr>';

    $('#warehouseStockTable tbody tr.wh-stock-row').each(function() {
      if ($(this).css('display') !== 'none' && $(this).find('td').length >= 7) {
        var id = $(this).find('td:eq(0)').text().trim();
        var name = $(this).find('.wh-material-name-cell').text().trim();
        var wh = $(this).find('.wh-name-cell').text().trim();
        var stock = $(this).find('td:eq(3)').text().trim();
        var recipe = $(this).find('td:eq(4)').text().trim();
        var val = $(this).find('td:eq(6)').text().trim();
        var status = $(this).find('td:eq(7)').text().trim();

        rowsHtml += '<tr>';
        rowsHtml += '<td><strong>' + name + '</strong><br><span style="font-size:8.5px;color:#444;">ID: ' + id + '</span></td>';
        rowsHtml += '<td><strong>' + wh + '</strong></td>';
        rowsHtml += '<td><strong>' + stock + '</strong><br><span style="font-size:8.5px;color:#444;">' + status + '</span></td>';
        rowsHtml += '<td>' + recipe + '</td>';
        rowsHtml += '<td><strong>' + val + '</strong></td>';
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
  } else if (isReturns) {
    var headers = '<tr><th style="width:24%;">Return Invoice & Date</th><th style="width:24%;">Original & Vendor</th><th style="width:34%;">Items Returned</th><th style="width:18%;">Refund & By</th></tr>';

    $('#returnsTable tbody tr').each(function() {
      if ($(this).css('display') !== 'none' && $(this).find('td').length >= 8) {
        var retInv = $(this).find('td:eq(0)').text().trim();
        var date = $(this).find('td:eq(1)').text().trim();
        var origInv = $(this).find('td:eq(2)').text().trim();
        var vendor = $(this).find('td:eq(3)').text().trim();
        var itemsHtml = $(this).find('td:eq(5)').html().trim();
        var total = $(this).find('td:eq(6)').text().trim();
        var creator = $(this).find('td:eq(8)').text().trim();

        rowsHtml += '<tr>';
        rowsHtml += '<td><strong>' + retInv + '</strong><br><span style="font-size:8.5px;color:#444;">' + date + '</span></td>';
        rowsHtml += '<td><strong>' + vendor + '</strong><br><span style="font-size:8.5px;color:#444;">Orig: ' + origInv + '</span></td>';
        rowsHtml += '<td>' + itemsHtml + '</td>';
        rowsHtml += '<td><strong style="color:#b91c1c;">' + total + '</strong><br><span style="font-size:8.5px;color:#444;">' + creator + '</span></td>';
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
  printWin.document.write('<div class="header"><h2>{{ shop_name() }}</h2><h4>' + title + '</h4><p>Date: ' + new Date().toLocaleString() + ' | Developed by {{ developer_name() }}</p></div>');
  printWin.document.write(tableHtml);
  printWin.document.write('</body></html>');
  printWin.document.close();
  printWin.focus();
  setTimeout(function() {
    printWin.print();
    printWin.close();
  }, 400);
}

// Live Current Branch Stock Filtering
$(document).on('keyup change', '#stockSearchInput, #stockStatusFilter', function() {
  var searchVal = $('#stockSearchInput').val().toLowerCase().trim();
  var statusVal = $('#stockStatusFilter').val().toLowerCase().trim();

  $('#stockTable tbody tr.stock-row').each(function() {
    var nameText = $(this).find('.material-name-cell').text().toLowerCase();
    var statusAttr = ($(this).data('status') || '').toLowerCase();

    var matchSearch = !searchVal || nameText.indexOf(searchVal) !== -1;
    var matchStatus = !statusVal || statusAttr === statusVal;

    if (matchSearch && matchStatus) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
});

// Live Warehouse Stock Filtering
$(document).on('keyup change', '#whStockSearchInput, #whStockWarehouseFilter, #whStockStatusFilter', function() {
  var searchVal = $('#whStockSearchInput').val().toLowerCase().trim();
  var whVal = $('#whStockWarehouseFilter').val().toLowerCase().trim();
  var statusVal = $('#whStockStatusFilter').val().toLowerCase().trim();

  $('#warehouseStockTable tbody tr.wh-stock-row').each(function() {
    var nameText = $(this).find('.wh-material-name-cell').text().toLowerCase();
    var whText = ($(this).data('warehouse') || $(this).find('.wh-name-cell').text()).toLowerCase();
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
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Yes, Delete',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Deleting...',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: response.success,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else if (response.error) {
            Swal.fire('Error!', response.error, 'error');
          } else {
            window.location.reload();
          }
        },
        error: function(xhr) {
          var msg = 'An error occurred while deleting.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.error) {
            msg = xhr.responseJSON.error;
          }
          Swal.fire('Error!', msg, 'error');
        }
      });
    }
  });
}

// View Recipe Details Modal
$(document).on('click', '.viewRecipeBtn', function() {
  var p = $(this).data('product');
  if (typeof p === 'string') {
    try { p = JSON.parse(p); } catch (e) { p = {}; }
  }
  $('#vrModalProductName').text(p.name || '-');
  $('#vrModalProductCode').text(p.code || '-');
  $('#vrModalUnit').text('Unit: ' + (p.unit || 'Piece'));
  $('#vrModalEditLink').attr('href', p.edit_url || '#');

  var tbody = '';
  var items = p.items || [];
  if (items.length > 0) {
    items.forEach(function(item, idx) {
      tbody += '<tr>' +
        '<td>' + (idx + 1) + '</td>' +
        '<td><strong>' + (item.material || '-') + '</strong>' + (item.urdu_name ? ' <span class="text-muted small">(' + item.urdu_name + ')</span>' : '') + '</td>' +
        '<td class="fw-bold text-primary">' + item.qty + '</td>' +
        '<td><span class="badge bg-light text-dark border">' + (item.unit || '-') + '</span></td>' +
        '<td><span class="badge bg-secondary-subtle text-secondary">' + (item.purchase_unit || '-') + '</span></td>' +
      '</tr>';
    });
  } else {
    tbody = '<tr><td colspan="5" class="text-center text-muted py-3">No BOM recipe items configured</td></tr>';
  }
  $('#vrModalItemsBody').html(tbody);
  $('#vrModalItemCount').text('Total: ' + items.length + ' raw materials in recipe');
  $('#viewRecipeModal').modal('show');
});

// View Purchase Items Modal
$(document).on('click', '.viewPurchaseItemsBtn', function() {
  var inv = $(this).data('invoice');
  var dt = $(this).data('date');
  var ven = $(this).data('vendor');
  var wh = $(this).data('warehouse');
  var tot = $(this).data('total');
  var items = $(this).data('items');
  if (typeof items === 'string') {
    try { items = JSON.parse(items); } catch (e) { items = []; }
  }

  $('#vpModalInvoice').text(inv || '-');
  $('#vpModalDate').text(dt || '-');
  $('#vpModalVendor').text(ven || '-');
  $('#vpModalWarehouse').text(wh || '-');
  $('#vpModalTotal').text(tot || 'Rs 0');

  var tbody = '';
  if (items && items.length > 0) {
    items.forEach(function(item, idx) {
      tbody += '<tr>' +
        '<td>' + (idx + 1) + '</td>' +
        '<td><strong>' + item.name + '</strong></td>' +
        '<td class="fw-bold">' + item.qty + ' ' + (item.unit || '') + '</td>' +
        '<td>Rs ' + item.price + '</td>' +
        '<td class="text-success fw-bold">Rs ' + item.total + '</td>' +
      '</tr>';
    });
  } else {
    tbody = '<tr><td colspan="5" class="text-center text-muted py-3">No items in this purchase</td></tr>';
  }
  $('#vpModalItemsBody').html(tbody);
  $('#viewPurchaseModal').modal('show');
});

// View Material Details Modal
$(document).on('click', '.viewMaterial', function() {
  var name = $(this).data('name');
  var urdu = $(this).data('urdu') || '';
  var unit = $(this).data('unit');
  var cunit = $(this).data('cunit');
  var factor = $(this).data('factor') || 1;
  var price = $(this).data('price') || 0;
  var purqty = $(this).data('purqty') || 0;
  var sqty = $(this).data('sqty') || 0;
  var whqty = $(this).data('whqty') || 0;
  var alert = $(this).data('alert') || 0;
  var notes = $(this).data('notes') || '';

  $('#vmName').text(name);
  $('#vmUrdu').text(urdu);
  $('#vmUnitBadge').text(unit.toUpperCase() + ' (Vendor Unit)');
  $('#vmBranchStock').text(purqty + ' ' + unit);
  $('#vmWhStock').text(whqty + ' ' + unit);
  $('#vmRecipeStock').text(sqty + ' ' + cunit);
  $('#vmPrice').text('Rs ' + Number(price).toFixed(2) + ' / ' + unit);
  $('#vmAlert').text(alert + ' ' + unit);
  $('#vmConversion').text('1 ' + unit + ' = ' + factor + ' ' + cunit);

  if (notes) {
    $('#vmNotes').text(notes);
    $('#vmNotesContainer').show();
  } else {
    $('#vmNotesContainer').hide();
  }

  $('#viewMaterialModal').modal('show');
});

// Quick Range Filter Buttons
$(document).on('click', '.quick-range-btn', function() {
  var range = $(this).data('range');
  var now = new Date();
  var pad = function(n) { return (n < 10 ? '0' : '') + n; };
  var formatDate = function(d) {
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
  };

  var startD = new Date(now);
  var endD = new Date(now);

  if (range === 'today') {
    // start today, end today
  } else if (range === 'yesterday') {
    startD.setDate(startD.getDate() - 1);
    endD.setDate(endD.getDate() - 1);
  } else if (range === 'this_week') {
    var day = startD.getDay() || 7;
    startD.setDate(startD.getDate() - day + 1);
  } else if (range === 'this_month') {
    startD.setDate(1);
  }

  $('#stockStartDate').val(formatDate(startD));
  $('#stockStartTime').val('00:00');
  $('#stockEndDate').val(formatDate(endD));
  $('#stockEndTime').val('23:59');

  $('#stockFilterForm').submit();
});

// AJAX: Recipe Usage Detail Breakdown
$(document).on('click', '.btn-view-usage', function() {
  var rmId = $(this).data('id');
  var rmName = $(this).data('name');
  var rmUnit = $(this).data('unit');
  var rmCUnit = $(this).data('cunit');
  var totalUsedText = $(this).data('used') + ' ' + rmCUnit;

  $('#modalMaterialName').text(rmName);
  $('#modalTotalUsed').text(totalUsedText);
  $('#usageDetailLoading').show();
  $('#usageDetailContent').hide();
  $('#usageDetailEmpty').hide();
  $('#usageDetailTableBody').empty();

  var sDate = $('#stockStartDate').val();
  var sTime = $('#stockStartTime').val();
  var eDate = $('#stockEndDate').val();
  var eTime = $('#stockEndTime').val();

  $('#modalFilterPeriod').text(sDate + ' ' + sTime + ' to ' + eDate + ' ' + eTime);
  $('#usageDetailModal').modal('show');

  $.ajax({
    url: '{{ url("/raw-materials/usage-detail") }}/' + rmId,
    type: 'GET',
    data: {
      start_date: sDate,
      start_time: sTime,
      end_date: eDate,
      end_time: eTime
    },
    success: function(res) {
      $('#usageDetailLoading').hide();
      if (!res.success || !res.records || res.records.length === 0) {
        $('#usageDetailEmpty').show();
        return;
      }

      $('#modalTotalUsed').text(res.total_used_consumption_formatted + (res.material.conversion_factor > 1 ? ' (' + res.total_used_purchase_formatted + ')' : ''));
      $('#modalFooterTotalUsed').text(res.total_used_consumption_formatted);

      var rows = '';
      res.records.forEach(function(rec) {
        if (rec.products && rec.products.length > 0) {
          rec.products.forEach(function(prod, pIdx) {
            rows += '<tr>';
            if (pIdx === 0) {
              rows += '<td rowspan="' + rec.products.length + '" style="vertical-align:middle;font-size:.82rem;">' + rec.datetime + '</td>';
              rows += '<td rowspan="' + rec.products.length + '" style="vertical-align:middle;"><span class="badge bg-light text-dark border font-monospace">' + rec.entry_no + '</span></td>';
            }
            rows += '<td class="fw-bold text-dark" style="font-size:.84rem;"><i class="bi bi-egg-fried text-primary me-1"></i>' + prod.product_name + '</td>';
            rows += '<td style="font-size:.82rem;"><span class="badge bg-secondary-subtle text-secondary">' + prod.produced_qty + '</span></td>';
            rows += '<td class="fw-bold text-danger" style="font-size:.86rem;">' + prod.rm_qty_used + (prod.rm_qty_used_purchase !== prod.rm_qty_used ? '<br><small class="text-muted font-normal">(' + prod.rm_qty_used_purchase + ')</small>' : '') + '</td>';
            rows += '<td style="font-size:.78rem;" class="text-muted">' + prod.recipe_per_unit + '</td>';
            if (pIdx === 0) {
              rows += '<td rowspan="' + rec.products.length + '" style="vertical-align:middle;font-size:.8rem;"><span class="badge bg-light text-secondary border">' + rec.creator + '</span></td>';
            }
            rows += '</tr>';
          });
        }
      });

      $('#usageDetailTableBody').html(rows);
      $('#usageDetailContent').show();
    },
    error: function() {
      $('#usageDetailLoading').hide();
      $('#usageDetailEmpty').html('<p class="text-danger fw-semibold">Failed to fetch usage details. Please try again.</p>').show();
    }
  });
});

// Print Modal Section Helper
function printModalSection(containerId) {
  var content = document.getElementById(containerId);
  if (!content) return;
  var matName = $('#modalMaterialName').text();
  var period = $('#modalFilterPeriod').text();
  var total = $('#modalTotalUsed').text();

  var printWin = window.open('', '', 'width=850,height=700');
  printWin.document.write('<!DOCTYPE html><html><head><title>Recipe Usage Breakdown - ' + matName + '</title>');
  printWin.document.write('<style>');
  printWin.document.write('* { box-sizing: border-box; font-family: "Arial Narrow", Arial, sans-serif; }');
  printWin.document.write('body { margin: 0; padding: 10px; color: #000; background: #fff; font-size: 10px; line-height: 1.3; }');
  printWin.document.write('.header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 6px; }');
  printWin.document.write('.header h2 { margin: 0 0 2px; font-size: 15px; font-weight: 800; text-transform: uppercase; }');
  printWin.document.write('.header h4 { margin: 0 0 3px; font-size: 12px; font-weight: 700; color: #111; }');
  printWin.document.write('.header p { margin: 0; font-size: 9px; color: #444; }');
  printWin.document.write('.summary { margin-bottom: 10px; padding: 6px; border: 1px solid #ccc; font-size: 10px; }');
  printWin.document.write('table { width: 100% !important; border-collapse: collapse !important; margin-top: 4px; font-size: 9.5px; }');
  printWin.document.write('th, td { border: 1px solid #000 !important; padding: 5px 6px !important; text-align: left; vertical-align: middle; }');
  printWin.document.write('th { background: #e2e8f0 !important; font-weight: 700; text-transform: uppercase; font-size: 9px; }');
  printWin.document.write('.badge { border: none !important; font-weight: 700; font-size: 9px !important; }');
  printWin.document.write('@media print { @page { size: auto; margin: 5mm; } body { padding: 0; } tr { page-break-inside: avoid; } }');
  printWin.document.write('</style></head><body>');
  printWin.document.write('<div class="header"><h2>{{ shop_name() }}</h2><h4>Recipe Usage Breakdown: ' + matName + '</h4><p>Period: ' + period + ' | Total Used: ' + total + '</p></div>');
  printWin.document.write(content.innerHTML);
  printWin.document.write('</body></html>');
  printWin.document.close();
  printWin.focus();
  setTimeout(function() {
    printWin.print();
    printWin.close();
  }, 400);
}

// Calculate row and grand total for Edit Purchase modal
function calculateEpTotals() {
  var grandTotal = 0;
  $('#epItemsBody tr').each(function() {
    var qty = parseFloat($(this).find('.ep-qty').val()) || 0;
    var price = parseFloat($(this).find('.ep-price').val()) || 0;
    var rowTotal = qty * price;
    $(this).find('.ep-row-total').text(rowTotal.toFixed(2));
    grandTotal += rowTotal;
  });
  $('#epTotalCost').text('Rs ' + grandTotal.toLocaleString());
}

// Edit Purchase Button Click
$(document).on('click', '.editPurchaseBtn', function() {
  var id = $(this).data('id');
  var inv = $(this).data('invoice');
  var dt = $(this).data('date');
  var vendorId = $(this).data('vendor-id');
  var vendorName = $(this).data('vendor-name') || '';
  var whId = $(this).data('warehouse-id') || '';
  var notes = $(this).data('notes') || '';
  var items = $(this).data('items');

  if (typeof items === 'string') {
    try { items = JSON.parse(items); } catch(e) { items = []; }
  }

  $('#epEditId').val(id);
  $('#epModalInvoice').text(inv || ('#' + id));
  $('#epDate').val(dt);
  $('#epVendorId').val(vendorId);
  $('#epVendorName').val(vendorName);
  $('#epWarehouseId').val(whId);
  $('#epNotes').val(notes);

  var optionsHtml = $('#materialOptionsTemplate').html();
  var tbody = $('#epItemsBody');
  tbody.empty();

  if (items && items.length > 0) {
    items.forEach(function(item) {
      var row = $(
        '<tr class="ep-row">' +
          '<td data-label="Raw Material"><select name="raw_material_id[]" class="fld ep-mat-select" required>' + optionsHtml + '</select></td>' +
          '<td data-label="Qty"><input type="number" step="0.01" name="qty[]" class="fld ep-qty" value="' + item.qty + '" placeholder="0" required /></td>' +
          '<td data-label="Price/Unit"><input type="number" step="0.01" name="price_per_unit[]" class="fld ep-price" value="' + item.price_per_unit + '" placeholder="0" required /></td>' +
          '<td data-label="Total"><span class="ep-row-total fw-bold" style="font-size:.9rem;">' + (item.qty * item.price_per_unit).toFixed(2) + '</span></td>' +
          '<td style="white-space:nowrap;" data-label="Action"><button type="button" class="rm-btn rm-btn-danger rm-btn-sm ep-remove-row"><i class="bi bi-x-lg"></i></button></td>' +
        '</tr>'
      );
      row.find('.ep-mat-select').val(item.raw_material_id);
      tbody.append(row);
    });
  } else {
    $('#epAddRowBtn').trigger('click');
  }

  calculateEpTotals();
  $('#editPurchaseModal').modal('show');
});

// Add Row in Edit Purchase modal
$(document).on('click', '#epAddRowBtn', function() {
  var optionsHtml = $('#materialOptionsTemplate').html();
  var row = $(
    '<tr class="ep-row">' +
      '<td data-label="Raw Material"><select name="raw_material_id[]" class="fld ep-mat-select" required>' + optionsHtml + '</select></td>' +
      '<td data-label="Qty"><input type="number" step="0.01" name="qty[]" class="fld ep-qty" placeholder="0" required /></td>' +
      '<td data-label="Price/Unit"><input type="number" step="0.01" name="price_per_unit[]" class="fld ep-price" placeholder="0" required /></td>' +
      '<td data-label="Total"><span class="ep-row-total fw-bold" style="font-size:.9rem;">0</span></td>' +
      '<td style="white-space:nowrap;" data-label="Action"><button type="button" class="rm-btn rm-btn-danger rm-btn-sm ep-remove-row"><i class="bi bi-x-lg"></i></button></td>' +
    '</tr>'
  );
  $('#epItemsBody').append(row);
});

// Remove Row in Edit Purchase modal
$(document).on('click', '.ep-remove-row', function() {
  if ($('#epItemsBody tr').length > 1) {
    $(this).closest('tr').remove();
    calculateEpTotals();
  } else {
    Swal.fire({ icon: 'warning', title: 'Attention', text: 'At least one item is required in a purchase.' });
  }
});

// Recalculate on input in Edit Purchase modal
$(document).on('input', '.ep-qty, .ep-price', function() {
  calculateEpTotals();
});

// Auto-fill price on material selection in Edit Purchase modal
$(document).on('change', '.ep-mat-select', function() {
  var row = $(this).closest('tr');
  var price = $(this).find(':selected').data('price');
  var priceInput = row.find('.ep-price');
  if (price !== undefined && price !== null && price > 0 && (priceInput.val() === '' || priceInput.val() == 0)) {
    priceInput.val(price);
  }
  calculateEpTotals();
});

// Submit Edit Purchase form
$(document).on('submit', '.edit-purchase-form', function(e) {
  e.preventDefault();
  var form = $(this);
  var fd = new FormData(this);
  var url = form.attr('action');
  var $btn = $('#epSubmitBtn');
  var btnText = $btn.html();
  $btn.attr('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Updating...');

  $.ajax({
    url: url,
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(data) {
      $btn.attr('disabled', false).html(btnText);
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: data.success,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          if (data.reload) window.location.reload();
        });
      } else if (data.errors) {
        var errHtml = '';
        $.each(data.errors, function(k, v) {
          errHtml += '<div class="text-danger small">' + (Array.isArray(v) ? v.join(', ') : v) + '</div>';
        });
        Swal.fire({ icon: 'error', title: 'Validation Errors', html: errHtml });
      } else if (data.error) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.error });
      }
    },
    error: function(xhr) {
      $btn.attr('disabled', false).html(btnText);
      var msg = 'Failed to update purchase.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        msg = xhr.responseJSON.message;
      }
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }
  });
});

// Calculate Return Totals
function calculateRetTotals() {
  var grandTotal = 0;
  $('#retItemsBody tr').each(function() {
    var qty = parseFloat($(this).find('.ret-qty').val()) || 0;
    var price = parseFloat($(this).find('.ret-price').val()) || 0;
    var rowTotal = qty * price;
    $(this).find('.ret-row-total').text('Rs ' + rowTotal.toFixed(2));
    grandTotal += rowTotal;
  });
  $('#retTotalCost').text('Rs ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

// Open Return Purchase Modal
$(document).on('click', '.openReturnModalBtn', function() {
  var purchaseId = $(this).data('id');
  var btn = $(this);
  var origHtml = btn.html();
  btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

  $.ajax({
    url: '{{ url("/raw-materials/purchase") }}/' + purchaseId + '/return-data',
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      btn.prop('disabled', false).html(origHtml);
      if (!res.success) {
        Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'Failed to fetch purchase details.' });
        return;
      }
      var p = res.purchase;
      $('#retPurchaseId').val(p.id);
      $('#retModalInvoice').text(p.invoice_no);
      $('#retVendorName').val(p.vendor_name || 'N/A');
      $('#retWarehouseName').val(p.warehouse_name || 'Main Stock');
      $('#retNotes').val('');

      var tbody = $('#retItemsBody');
      tbody.empty();

      if (p.items && p.items.length > 0) {
        p.items.forEach(function(item) {
          var isFullyRet = item.available_qty <= 0.0001;
          var row = $(
            '<tr class="ret-row ' + (isFullyRet ? 'table-light opacity-75' : '') + '">' +
              '<td>' +
                '<strong>' + item.name + '</strong>' +
                '<input type="hidden" name="raw_material_id[]" value="' + item.raw_material_id + '" />' +
                '<input type="hidden" name="price_per_unit[]" class="ret-price" value="' + item.price_per_unit + '" />' +
              '</td>' +
              '<td class="text-center font-monospace">' + item.purchased_qty.toFixed(2) + ' ' + item.unit + '</td>' +
              '<td class="text-center font-monospace text-muted">' + item.returned_qty.toFixed(2) + ' ' + item.unit + '</td>' +
              '<td class="text-center"><span class="badge ' + (isFullyRet ? 'bg-secondary' : 'bg-success-subtle text-success border') + ' font-monospace">' + item.available_qty.toFixed(2) + ' ' + item.unit + '</span></td>' +
              '<td class="text-center font-monospace text-primary">' + item.current_stock.toFixed(2) + ' ' + item.unit + '</td>' +
              '<td class="text-end font-monospace">Rs ' + item.price_per_unit.toFixed(2) + '</td>' +
              '<td>' +
                (isFullyRet
                  ? '<span class="badge bg-danger-subtle text-danger border">Fully Returned</span><input type="hidden" name="qty[]" value="0" class="ret-qty">'
                  : '<div class="input-group input-group-sm">' +
                      '<input type="number" step="0.01" min="0" max="' + item.available_qty + '" name="qty[]" class="fld ret-qty py-1 px-2" placeholder="0" value="0" style="font-weight:700;height:auto;" data-max="' + item.available_qty + '" data-stock="' + item.current_stock + '">' +
                      '<button type="button" class="btn btn-outline-secondary btn-sm max-ret-btn" title="Return Max Available" style="font-size:.7rem;padding:2px 6px;">MAX</button>' +
                    '</div>'
                ) +
              '</td>' +
              '<td class="text-end font-monospace fw-bold ret-row-total text-danger">Rs 0.00</td>' +
            '</tr>'
          );
          tbody.append(row);
        });
      }

      calculateRetTotals();
      $('#returnPurchaseModal').modal('show');
    },
    error: function(xhr) {
      btn.prop('disabled', false).html(origHtml);
      var msg = 'Failed to load purchase return data.';
      if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }
  });
});

// Click MAX available quantity
$(document).on('click', '.max-ret-btn', function() {
  var input = $(this).closest('.input-group').find('.ret-qty');
  var max = parseFloat(input.data('max')) || 0;
  input.val(max.toFixed(2)).trigger('input');
});

// Recalculate on return qty input
$(document).on('input', '.ret-qty', function() {
  var max = parseFloat($(this).data('max')) || 0;
  var val = parseFloat($(this).val()) || 0;
  if (val > max) {
    $(this).val(max);
    Swal.fire({ icon: 'info', title: 'Max Limit', text: 'You cannot return more than available quantity (' + max + ').', timer: 1500, showConfirmButton: false });
  }
  calculateRetTotals();
});

// Submit Purchase Return Form
$(document).on('submit', '#purchaseReturnForm', function(e) {
  e.preventDefault();
  var form = $(this);

  var totalQty = 0;
  $('.ret-qty').each(function() {
    totalQty += parseFloat($(this).val()) || 0;
  });

  if (totalQty <= 0) {
    Swal.fire({ icon: 'warning', title: 'No Return Quantity', text: 'Please enter a return quantity greater than 0 for at least one item.' });
    return;
  }

  var fd = new FormData(this);
  var url = form.attr('action');
  var $btn = $('#retSubmitBtn');
  var btnText = $btn.html();
  $btn.attr('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Processing Return...');

  $.ajax({
    url: url,
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(data) {
      $btn.attr('disabled', false).html(btnText);
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Return Completed!',
          text: data.success,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          if (data.reload) {
            window.location.href = data.reload;
          } else {
            window.location.reload();
          }
        });
      } else if (data.errors) {
        var errHtml = '';
        $.each(data.errors, function(k, v) {
          errHtml += '<div class="text-danger small">' + (Array.isArray(v) ? v.join(', ') : v) + '</div>';
        });
        Swal.fire({ icon: 'error', title: 'Validation Errors', html: errHtml });
      } else if (data.error) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.error });
      }
    },
    error: function(xhr) {
      $btn.attr('disabled', false).html(btnText);
      var msg = 'Failed to process purchase return.';
      if (xhr.responseJSON && xhr.responseJSON.error) {
        msg = xhr.responseJSON.error;
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
        msg = xhr.responseJSON.message;
      }
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }
  });
});

// View Return Items Details Modal
$(document).on('click', '.viewReturnItemsBtn', function() {
  var inv = $(this).data('invoice');
  var orig = $(this).data('original');
  var dt = $(this).data('date');
  var ven = $(this).data('vendor');
  var tot = $(this).data('total');
  var items = $(this).data('items');
  if (typeof items === 'string') {
    try { items = JSON.parse(items); } catch (e) { items = []; }
  }

  $('#vrModalInvoice').text(inv || '-');
  $('#vrModalInvoice2').text(inv || '-');
  $('#vrModalOriginal').text(orig || '-');
  $('#vrModalDate').text(dt || '-');
  $('#vrModalVendor').text(ven || '-');
  $('#vrModalTotal').text(tot || 'Rs 0');

  var tbody = '';
  if (items && items.length > 0) {
    items.forEach(function(item, idx) {
      tbody += '<tr>' +
        '<td>' + (idx + 1) + '</td>' +
        '<td><strong>' + item.name + '</strong></td>' +
        '<td class="fw-bold text-danger">' + item.qty + ' ' + (item.unit || '') + '</td>' +
        '<td>Rs ' + item.price + '</td>' +
        '<td class="text-danger fw-bold">Rs ' + item.total + '</td>' +
      '</tr>';
    });
  } else {
    tbody = '<tr><td colspan="5" class="text-center text-muted">No items recorded</td></tr>';
  }
  $('#vrModalItemsBody').html(tbody);
  $('#viewReturnModal').modal('show');
});

// DataTables & Initializations
$(document).ready(function() {
  initMaterialSelect2();
  if ($.fn.DataTable.isDataTable('#materialTable')) $('#materialTable').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#purchaseTable')) $('#purchaseTable').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#recipesTable')) $('#recipesTable').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#returnsTable')) $('#returnsTable').DataTable().destroy();
  $('#materialTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: '_INPUT_', searchPlaceholder: 'Search materials...' } });
  $('#purchaseTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: '_INPUT_', searchPlaceholder: 'Search purchases...' } });
  $('#recipesTable').DataTable({ pageLength: 10, order: [[0, 'asc']], language: { search: '_INPUT_', searchPlaceholder: 'Search recipes...' } });
  $('#returnsTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: '_INPUT_', searchPlaceholder: 'Search returns...' } });
});
</script>
@endsection
