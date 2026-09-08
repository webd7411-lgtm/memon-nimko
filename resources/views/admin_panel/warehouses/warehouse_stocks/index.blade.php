@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --ws-bg: #f8fafc;
  --ws-surface: #ffffff;
  --ws-border: #e2e8f0;
  --ws-border-lt: #f1f5f9;
  --ws-text: #0f172a;
  --ws-text-sec: #475569;
  --ws-text-muted: #64748b;
  --ws-primary: #2563eb;
  --ws-radius: 16px;
  --ws-radius-sm: 10px;
  --ws-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --ws-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --ws-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.ws-page * {
  font-family: var(--ws-font);
}

.ws-page {
  background-color: var(--ws-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.ws-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--ws-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.3);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.ws-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.ws-hero > * {
  position: relative;
  z-index: 1;
}

.ws-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.ws-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.ws-hero-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  font-size: 1.4rem;
}

.ws-hero-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #93c5fd;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.ws-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.ws-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--ws-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.ws-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.ws-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.ws-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.ws-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.ws-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .ws-kpi-grid { grid-template-columns: 1fr; }
}

.ws-kpi-card {
  background: var(--ws-surface);
  border: 1px solid var(--ws-border);
  border-radius: var(--ws-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--ws-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.ws-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--ws-shadow-lg);
}

.ws-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--ws-text-muted);
}
.ws-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--ws-text);
  letter-spacing: -0.02em;
}

.ws-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.ws-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.ws-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.ws-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }

/* ═══════ FILTER CARD ═══════ */
.ws-filter-card {
  background: var(--ws-surface);
  border: 1px solid var(--ws-border);
  border-radius: var(--ws-radius);
  padding: 1.15rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--ws-shadow);
}

.ws-input {
  border: 1px solid var(--ws-border);
  border-radius: var(--ws-radius-sm);
  padding: 0.5rem 0.85rem;
  font-size: 0.88rem;
  color: var(--ws-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.ws-input:focus {
  border-color: var(--ws-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.ws-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--ws-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.ws-table-card {
  background: var(--ws-surface);
  border: 1px solid var(--ws-border);
  border-radius: var(--ws-radius);
  box-shadow: var(--ws-shadow-lg);
  overflow: hidden;
}

.ws-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--ws-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.ws-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--ws-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ws-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.ws-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.ws-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ws-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--ws-border);
  border-top: none;
  white-space: nowrap;
}

.ws-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--ws-border-lt);
  font-size: 0.88rem;
  color: var(--ws-text-sec);
}

.ws-table tbody tr {
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.ws-table tbody tr:hover td {
  background-color: #f8fafc;
}

.ws-table tbody tr.row-selected td {
  background-color: #eff6ff !important;
}

/* ═══════ FULL MOBILE RESPONSIVE — CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .ws-page { overflow-x: hidden !important; }

  .ws-hero { padding: 1rem 1rem !important; gap: .5rem; }
  .ws-hero-title h2 { font-size: 1.05rem !important; }
  .ws-hero-actions { width: 100%; display: grid !important; grid-template-columns: 1fr 1fr; gap: .4rem; }
  .ws-hero-actions .ws-btn { justify-content: center; width: 100%; min-height: 36px; font-size: .75rem; padding: .45rem .6rem; }
  .ws-table-header { padding: .9rem 1rem; }
  .ws-table-title { font-size: .95rem; }
  .ws-filter-card { padding: 1rem; }

  /* Kill scroll wrappers — higher specificity */
  .ws-page .ws-table-wrapper,
  .ws-page .dataTables_wrapper,
  .ws-page .dataTables_scrollBody,
  .ws-page .dataTables_scrollHead,
  .ws-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    padding: 0 !important;
  }

  .ws-page .ws-table,
  .ws-page .ws-table.dataTable {
    display: block !important;
    width: 100% !important;
    font-size: .75rem !important;
  }
  .ws-page .ws-table thead { display: none !important; }
  .ws-page .ws-table tbody { display: block !important; }
  .ws-page .ws-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .55rem;
    align-items: start;
    background: var(--ws-surface);
    border: 1px solid var(--ws-border) !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05), 0 2px 8px rgba(15,23,42,.05);
    padding: .55rem .65rem !important;
    margin-bottom: .55rem;
    overflow: hidden !important;
    cursor: pointer;
  }
  .ws-page .ws-table tbody tr:hover td { background: transparent !important; }
  .ws-page .ws-table tbody tr.row-selected td { background-color: #eff6ff !important; }
  .ws-page .ws-table tbody td {
    display: flex !important;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 2px 4px;
    border: none !important;
    padding: 0 !important;
    min-width: 0;
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: anywhere;
    font-size: .75rem;
    line-height: 1.35;
    text-align: left !important;
  }
  .ws-page .ws-table tbody td::before {
    content: attr(data-label) ":";
    font-size: .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--ws-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .ws-page .ws-table tbody td:nth-child(1) { order: 0; justify-self: start; }
  .ws-page .ws-table tbody td:nth-child(1)::before { content: none; }
  .ws-page .ws-table tbody td:nth-child(1) {
    background: #eff6ff; color: #2563eb !important; border-radius: 6px;
    padding: .05rem .4rem !important; font-weight: 700 !important; font-size: .62rem;
  }
  .ws-page .ws-table tbody td:nth-child(4) { order: 1; grid-column: 1 / -1; }
  .ws-page .ws-table tbody td:nth-child(4)::before { content: none; }
  .ws-page .ws-table tbody td:nth-child(4) { font-weight: 600; font-size: .8rem; padding-top: 2px; }
  .ws-page .ws-table tbody td:nth-child(2) { order: 2; }
  .ws-page .ws-table tbody td:nth-child(3) { order: 3; grid-column: 1 / -1; }
  .ws-page .ws-table tbody td:nth-child(5) { order: 4; }
  .ws-page .ws-table tbody td:nth-child(6) { order: 5; }
  .ws-page .ws-table tbody td:nth-child(7) { order: 6; }
  .ws-page .ws-table tbody td:nth-child(8) { order: 7; }
  .ws-page .ws-table tbody td:nth-child(9) { order: 8; }
  .ws-page .ws-table tbody td:nth-child(10) { order: 9; grid-column: 1 / -1; font-size: .82rem !important; }
  .ws-page .ws-table tbody td:nth-child(11) { order: 10; grid-column: 1 / -1; }

  .ws-page .dataTables_filter, .ws-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .ws-page .dataTables_filter input, .ws-page .dataTables_length select {
    width: 100% !important; min-height: 36px; font-size: .78rem; border-radius: 8px;
    padding: .3rem .6rem; border: 1.5px solid var(--ws-border); margin-top: 4px;
  }
  .ws-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .ws-page .dataTables_paginate { text-align: center !important; padding: .5rem 0; }
  .ws-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 28px !important; height: 28px !important;
    padding: 0 .35rem !important; font-size: .68rem !important; margin: 0 1px !important;
    border-radius: 6px !important;
  }
}
</style>

<div class="ws-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="ws-hero">
      <div class="ws-hero-title">
        <div class="ws-hero-icon">
          <i class="bi bi-boxes"></i>
        </div>
        <div>
          <h2>Stock Status Ledger</h2>
          <span class="ws-hero-badge">Inventory Quantities & Valuation</span>
        </div>
      </div>

      <div class="ws-hero-actions">
        <a href="{{ route('warehouse_stocks.create') }}" class="ws-btn ws-btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Add Stock
        </a>

        <a id="exportStockAllBtn" href="javascript:void(0)" class="ws-btn ws-btn-glass">
          <i class="bi bi-file-earmark-excel me-1 text-success"></i> Export All
        </a>

        <button id="exportStockSelectedBtn" type="button" class="ws-btn ws-btn-glass">
          <i class="bi bi-check2-square me-1"></i> Export Selected
        </button>

        <a href="{{ url()->previous() }}" class="ws-btn ws-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalStockEntries = count($stocks);
      $totalShopQty = $stocks->sum(fn($s) => $s->shop_stock ?? 0);
      $totalWhQty = $stocks->sum(fn($s) => $s->warehouse_stock ?? ($s->quantity ?? 0));
    @endphp

    <div class="ws-kpi-grid">
      <div class="ws-kpi-card">
        <div class="ws-kpi-info">
          <p>Stock Entries</p>
          <h3>{{ number_format($totalStockEntries) }}</h3>
        </div>
        <div class="ws-kpi-icon blue">
          <i class="bi bi-journal-bookmark"></i>
        </div>
      </div>

      <div class="ws-kpi-card">
        <div class="ws-kpi-info">
          <p>Shop Stock Total</p>
          <h3>{{ number_format($totalShopQty, 2) }}</h3>
        </div>
        <div class="ws-kpi-icon green">
          <i class="bi bi-shop"></i>
        </div>
      </div>

      <div class="ws-kpi-card">
        <div class="ws-kpi-info">
          <p>Warehouse Stock Total</p>
          <h3>{{ number_format($totalWhQty, 2) }}</h3>
        </div>
        <div class="ws-kpi-icon amber">
          <i class="bi bi-building"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="ws-filter-card">
      <form method="GET" action="{{ route('warehouse_stocks.index') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label class="ws-label"><i class="bi bi-funnel me-1 text-primary"></i> Stock Type</label>
          <select name="stock_type" class="ws-input">
            <option value="all" {{ request('stock_type') == 'all' ? 'selected' : '' }}>All Locations</option>
            <option value="shop" {{ request('stock_type') == 'shop' ? 'selected' : '' }}>Shop Stock Only</option>
            <option value="warehouse" {{ request('stock_type') == 'warehouse' ? 'selected' : '' }}>Warehouse Stock Only</option>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="ws-label"><i class="bi bi-building me-1 text-primary"></i> Specific Warehouse</label>
          <select name="warehouse_id" class="ws-input">
            <option value="">All Warehouses</option>
            @foreach(\App\Models\Warehouse::all() as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="ws-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
          <input type="date" name="start_date" class="ws-input" value="{{ request('start_date') }}">
        </div>

        <div class="col-12 col-md-3">
          <label class="ws-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
          <input type="date" name="end_date" class="ws-input" value="{{ request('end_date') }}">
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
          <button type="submit" class="ws-btn ws-btn-primary w-100">
            <i class="bi bi-filter me-1"></i> Filter
          </button>
          <a href="{{ route('warehouse_stocks.index') }}" class="ws-btn ws-btn-glass text-dark border w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>

    @if(request('start_date') && request('end_date'))
    <div class="alert alert-info py-2 rounded-3 shadow-sm border-0 mb-3">
      <i class="bi bi-info-circle-fill me-2"></i> Showing stock activity from <strong>{{ request('start_date') }}</strong> to <strong>{{ request('end_date') }}</strong>
    </div>
    @endif

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="ws-table-card">
      <div class="ws-table-header">
        <h3 class="ws-table-title">
          <i class="bi bi-card-checklist text-primary me-1"></i> Current Stock Balances
        </h3>
        <span class="text-muted small"><i class="bi bi-mouse2 me-1"></i> Click rows to select for Export</span>
      </div>

      <div class="ws-table-wrapper">
        <table class="table ws-table" id="stockTable">
          <thead>
            <tr>
              <th width="4%">#</th>
              <th width="10%">Date</th>
              <th width="16%">Location</th>
              <th width="20%">Product</th>
              <th width="8%">Unit</th>
              <th width="10%">Brand</th>
              <th width="10%">Price</th>
              <th width="10%" class="text-center">Shop Stock</th>
              <th width="10%" class="text-center">Warehouse Stock</th>
              <th width="10%" class="text-center">Total Stock</th>
              <th width="12%">Remarks</th>
            </tr>
          </thead>
          <tbody>
            @foreach($stocks as $stock)
            <tr>
              <td class="fw-bold" data-label="#">#{{ $loop->iteration }}</td>
              <td data-label="Date">
                <span class="badge bg-light text-dark border fw-normal">
                  {{ $stock->created_at->format('d M Y') }}
                </span>
              </td>
              <td data-label="Location">
                @if($stock->warehouse)
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    <i class="bi bi-building me-1"></i> {{ $stock->warehouse->warehouse_name }}
                  </span>
                @else
                  <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="bi bi-shop me-1"></i> Shop
                  </span>
                @endif
              </td>
              <td class="fw-bold text-dark" data-label="Product">{{ $stock->product?->item_name }}</td>
              <td data-label="Unit"><span class="badge bg-secondary-subtle text-secondary">{{ $stock->product?->unit?->name ?? $stock->product?->unit_id }}</span></td>
              <td data-label="Brand">{{ $stock->product?->brand?->name ?? 'N/A' }}</td>
              <td class="fw-semibold" data-label="Price">PKR {{ number_format((float)($stock->product?->price ?? 0), 2) }}</td>
              <td class="text-center fw-bold text-success" data-label="Shop Stock">{{ number_format($stock->shop_stock ?? 0, 2) }}</td>
              <td class="text-center fw-bold text-primary" data-label="Whs Stock">{{ number_format($stock->warehouse_stock ?? ($stock->quantity ?? 0), 2) }}</td>
              <td class="text-center fw-bold text-dark" data-label="Total Stock">{{ number_format($stock->total_stock ?? ($stock->quantity ?? 0), 2) }}</td>
              <td data-label="Remarks"><small class="text-muted">{{ $stock->remarks ?? '—' }}</small></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#stockTable')) {
        $('#stockTable').DataTable().destroy();
    }
    $('#stockTable').DataTable({
        paging: true,
        pageLength: 25,
        searching: true,
        ordering: false,
        scrollX: false,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search stock entries..."
        }
    });
});
</script>

<script>
$(function() {
    $('#stockTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).is('a,button,input,select,textarea')) return;
        $(this).toggleClass('row-selected');
    });

    function toNumber(txt) {
        if (txt === null || txt === undefined) return '';
        var s = String(txt).trim();
        s = s.replace(/,/g, '').replace(/PKR/ig, '').replace(/[^\d\.\-]/g, '');
        if (s === '' || s === '-') return '';
        var n = Number(s);
        return isNaN(n) ? txt : n;
    }

    function parseStockRow(tr) {
        var $tds = $(tr).find('td');
        var date = $tds.eq(1).text().trim();
        var warehouse = $tds.eq(2).text().trim();
        var product = $tds.eq(3).text().trim();
        var shopStock = toNumber($tds.eq(7).text());
        var warehouseStock = toNumber($tds.eq(8).text());
        var totalStock = toNumber($tds.eq(9).text());
        var remarks = $tds.eq(10).text().trim();
        return [date, warehouse, product, shopStock, warehouseStock, totalStock, remarks];
    }

    function buildAndDownload(rowsArray, filename) {
        var header = ['Date', 'Warehouse', 'Product', 'Shop Stock', 'Warehouse Stock', 'Total Stock', 'Remarks'];
        var aoa = [header].concat(rowsArray);
        var ws = XLSX.utils.aoa_to_sheet(aoa);
        ws['!cols'] = [
            { wpx: 80 },
            { wpx: 140 },
            { wpx: 200 },
            { wpx: 80 },
            { wpx: 100 },
            { wpx: 100 },
            { wpx: 180 }
        ];
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'WarehouseStock');
        XLSX.writeFile(wb, filename);
    }

    $('#exportStockAllBtn').on('click', function() {
        var rows = [];
        $('#stockTable tbody tr').each(function() {
            if ($(this).is(':hidden')) return;
            rows.push(parseStockRow(this));
        });
        if (rows.length === 0) {
            alert('No rows to export.');
            return;
        }
        var ts = new Date().toISOString().replace(/[:\-T]/g, '').slice(0, 14);
        buildAndDownload(rows, 'warehouse_stock_all_' + ts + '.xlsx');
    });

    $('#exportStockSelectedBtn').on('click', function() {
        var sel = [];
        $('#stockTable tbody tr.row-selected').each(function() {
            sel.push(parseStockRow(this));
        });
        if (sel.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Select rows by clicking them, then click Export Selected.'
                });
            } else {
                alert('Select rows by clicking them, then click Export Selected.');
            }
            return;
        }
        var ts = new Date().toISOString().replace(/[:\-T]/g, '').slice(0, 14);
        buildAndDownload(sel, 'warehouse_stock_selected_' + ts + '.xlsx');
    });
});
</script>
@endsection