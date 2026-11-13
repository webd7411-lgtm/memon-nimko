@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --rp-bg: #f8fafc;
  --rp-surface: #ffffff;
  --rp-border: #e2e8f0;
  --rp-border-lt: #f1f5f9;
  --rp-text: #0f172a;
  --rp-text-sec: #475569;
  --rp-text-muted: #64748b;
  --rp-primary: #0e8349;
  --rp-primary-dark: #0b6b3b;
  --rp-radius: 14px;
  --rp-radius-sm: 8px;
  --rp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --rp-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --rp-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.rp-page * {
  font-family: var(--rp-font);
  box-sizing: border-box;
}

.rp-page {
  background-color: var(--rp-bg);
  min-height: 100vh;
  padding: 1.25rem;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.rp-hdr {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #064e3b 100%);
  border-radius: var(--rp-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.rp-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(14, 131, 73, 0.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(59, 130, 246, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.rp-hdr > * {
  position: relative;
  z-index: 1;
}

.rp-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.rp-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.rp-hdr-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #34d399;
  font-size: 1.4rem;
}

.rp-hdr-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #a7f3d0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.rp-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--rp-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.rp-btn-primary {
  background: linear-gradient(135deg, #0e8349 0%, #0b6b3b 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(14, 131, 73, 0.35);
}
.rp-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(14, 131, 73, 0.45);
}

.rp-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.rp-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ FILTER CARD ═══════ */
.rp-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.rp-card-body { padding: 1.25rem; }

.rp-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--rp-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.rp-input {
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--rp-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.rp-input:focus {
  border-color: var(--rp-primary);
  box-shadow: 0 0 0 3px rgba(14, 131, 73, 0.15);
  outline: none;
}

/* ═══════ KPI STATS ═══════ */
.rp-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.rp-kpi-card {
  background: #ffffff;
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  padding: 1.15rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: var(--rp-shadow);
}

.rp-kpi-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--rp-text-muted);
  margin: 0;
}

.rp-kpi-val {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--rp-text);
  margin: 0.25rem 0 0 0;
}

.rp-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.rp-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.rp-kpi-icon.green { background: #ecfdf5; color: #059669; }
.rp-kpi-icon.amber { background: #fffbeb; color: #d97706; }
.rp-kpi-icon.purple { background: #f5f3ff; color: #7c3aed; }

/* ═══════ DATA TABLE ═══════ */
.rp-table-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow-lg);
  overflow: hidden;
}

.rp-table-header {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--rp-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.rp-table-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--rp-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rp-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
}

.rp-table {
  width: 100% !important;
  border-collapse: separate;
  border-spacing: 0;
}

.rp-table thead th {
  background: #f1f5f9;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #334155;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--rp-border);
  white-space: nowrap;
}

.rp-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--rp-border-lt);
  font-size: 0.88rem;
  color: var(--rp-text);
}

.rp-table tbody tr:hover td {
  background-color: #f8fafc;
}

.rp-badge-source {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
}
.rp-badge-branch { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.rp-badge-wh { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

.item-chip {
  display: inline-block;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 2px 8px;
  font-size: 0.8rem;
  font-weight: 600;
  margin: 2px;
}

.rm-breakdown-box {
  background: #fafaf9;
  border: 1px dashed #d6d3d1;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 0.8rem;
  margin-top: 4px;
}

.rm-item-line {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 2px 0;
  border-bottom: 1px dotted #e7e5e4;
}
.rm-item-line:last-child { border-bottom: none; }

.rp-loader { display: none; text-align: center; padding: 2rem 0; }
.rp-loader .spinner-border { color: var(--rp-primary); width: 2.5rem; height: 2.5rem; }

/* PRINT STYLING */
@media print {
  .no-print, .rp-hdr-actions, .rp-card, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate {
    display: none !important;
  }
  .rp-page { padding: 0 !important; background: #fff !important; }
  .rp-hdr { background: none !important; color: #000 !important; box-shadow: none !important; padding: 0.5rem 0 !important; border-bottom: 2px solid #000; }
  .rp-hdr h2 { color: #000 !important; }
  .rp-hdr-icon, .rp-hdr-badge { display: none !important; }
  .rp-table thead th { background: #eee !important; color: #000 !important; }
  .rp-table td { color: #000 !important; }
}

@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .rp-page, .rp-page * { max-width: 100%; box-sizing: border-box; }
  .rp-page { overflow-x: hidden !important; padding: 0.65rem 0.65rem 1.25rem !important; }

  .rp-hdr { padding: 1rem 0.85rem !important; gap: 0.6rem; flex-direction: column; align-items: stretch !important; border-radius: 12px; }
  .rp-hdr-title { gap: 0.6rem; }
  .rp-hdr-title h2 { font-size: 1.05rem !important; }
  .rp-hdr-icon { width: 38px; height: 38px; font-size: 1.1rem; }
  .rp-hdr-badge { font-size: 0.62rem; padding: 0.2rem 0.6rem; }
  .rp-hdr-actions { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 0.45rem; width: 100%; }
  .rp-btn { width: 100%; justify-content: center; min-height: 34px !important; font-size: 0.76rem !important; padding: 0.35rem 0.65rem; border-radius: 8px; }

  .rp-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 0.5rem; margin-bottom: 0.75rem; }
  .rp-kpi-card { padding: 0.7rem 0.8rem; border-radius: 12px; }
  .rp-kpi-card .val { font-size: 1.1rem; }
  .rp-kpi-card .lbl { font-size: 0.62rem; }

  .rp-card { border-radius: 12px; margin-bottom: 0.75rem; }
  .rp-card-body { padding: 0.75rem !important; }
  .rp-card-body form.row { margin: 0 !important; }
  .rp-card-body form.row > [class*="col-"] { padding: 0 !important; margin-bottom: 0.5rem; }
  .rp-label { font-size: 0.65rem !important; margin-bottom: 0.2rem; }
  .rp-input { min-height: 36px !important; height: 36px !important; font-size: 0.8rem !important; padding: 0.3rem 0.55rem; border-radius: 8px; }

  /* Table → Mobile Cards */
  .rp-table-wrapper { overflow: visible !important; border: none !important; padding: 0 !important; }
  .rp-table { display: block !important; width: 100% !important; border: none !important; }
  .rp-table thead { display: none !important; }
  .rp-table tbody { display: flex !important; flex-direction: column !important; gap: 0.6rem !important; width: 100% !important; }
  .rp-table tbody tr {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.4rem 0.55rem !important;
    background: #ffffff;
    border: 1px solid var(--rp-border) !important;
    border-radius: 12px;
    padding: 0.7rem 0.8rem !important;
    box-shadow: 0 1px 3px rgba(15,23,42,.04);
  }
  .rp-table tbody td {
    display: flex !important;
    flex-direction: column;
    align-items: flex-start;
    border: none !important;
    padding: 0 !important;
    min-width: 0 !important;
    text-align: left !important;
    font-size: 0.76rem;
  }
  .rp-table tbody td::before {
    font-size: 0.56rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.15rem;
  }
  .rp-table tbody td:nth-child(1) { grid-column: 1 / 2; }
  .rp-table tbody td:nth-child(1)::before { content: "Batch / Date"; }
  .rp-table tbody td:nth-child(2) { grid-column: 2 / 3; align-items: flex-end; }
  .rp-table tbody td:nth-child(2)::before { content: "Location"; }
  .rp-table tbody td:nth-child(3) { grid-column: 1 / -1; background: #f8fafc; padding: 0.4rem 0.55rem !important; border-radius: 8px; }
  .rp-table tbody td:nth-child(3)::before { content: "Finished Goods Produced"; }
  .rp-table tbody td:nth-child(4) { grid-column: 1 / -1; background: #fffbeb; padding: 0.4rem 0.55rem !important; border-radius: 8px; border: 1px solid #fef3c7; }
  .rp-table tbody td:nth-child(4)::before { content: "Raw Materials Used"; }
  .rp-table tbody td:nth-child(5) { grid-column: 1 / 2; }
  .rp-table tbody td:nth-child(5)::before { content: "Production Cost"; }
  .rp-table tbody td:nth-child(6) { grid-column: 2 / 3; align-items: flex-end; }
  .rp-table tbody td:nth-child(6)::before { content: "Avg Cost / Unit"; }
  .rp-table tbody td:nth-child(7) {
    grid-column: 1 / -1;
    border-top: 1px dashed #e2e8f0 !important;
    padding-top: 0.45rem !important;
    margin-top: 0.15rem;
    align-items: stretch !important;
  }
  .rp-table tbody td:nth-child(7)::before { display: none !important; }
  .rp-table tbody td:nth-child(7) a { width: 100%; justify-content: center; min-height: 34px; font-size: 0.78rem; display: inline-flex; align-items: center; border-radius: 8px; }

  .rp-table tbody tr td[colspan] {
    grid-column: 1 / -1 !important;
    align-items: center !important;
    text-align: center !important;
    padding: 1.5rem 0.5rem !important;
  }
  .rp-table tbody tr td[colspan]::before { display: none !important; }

  .rp-table tfoot { display: block !important; width: 100% !important; margin-top: 0.5rem; }
  .rp-table tfoot tr {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.4rem 0.55rem !important;
    border-radius: 12px;
    padding: 0.65rem 0.75rem !important;
  }
  .rp-table tfoot td { display: flex !important; flex-direction: column !important; border: none !important; padding: 0 !important; font-size: 0.76rem !important; }
  .rp-table tfoot td:nth-child(1) { grid-column: 1 / -1 !important; font-size: 0.82rem !important; }
  .rp-table tfoot td:nth-child(2)::before { content: "Total Produced Qty"; font-size: 0.56rem; color: #94a3b8; }
  .rp-table tfoot td:nth-child(3)::before { content: "Total RM Used"; font-size: 0.56rem; color: #94a3b8; }
  .rp-table tfoot td:nth-child(4)::before { content: "Total Cost"; font-size: 0.56rem; color: #94a3b8; }
  .rp-table tfoot td:nth-child(5)::before { content: "Avg Unit Cost"; font-size: 0.56rem; color: #94a3b8; }
  .rp-table tfoot td:nth-child(6) { display: none !important; }
}

@media (max-width: 575.98px) {
  .rp-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}
</style>

<div class="rp-page">

  {{-- ═══════ HERO HEADER ═══════ --}}
  <div class="rp-hdr">
    <div class="rp-hdr-title">
      <div class="rp-hdr-icon">
        <i class="fas fa-industry"></i>
      </div>
      <div>
        <h2>Production Cost & Quantity Report</h2>
        <span class="rp-hdr-badge">Batch Costing & Consumption Analytics</span>
      </div>
    </div>

    <div class="rp-hdr-actions d-flex align-items-center gap-2">
      <a href="{{ route('production.index') }}" class="rp-btn rp-btn-glass">
        <i class="fas fa-arrow-left"></i> Batches List
      </a>
      <a href="{{ route('production.create') }}" class="rp-btn rp-btn-glass">
        <i class="fas fa-plus"></i> New Entry
      </a>
      <button type="button" class="rp-btn rp-btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i> Print Report
      </button>
    </div>
  </div>

  {{-- ═══════ FILTER CARD ═══════ --}}
  <div class="rp-card no-print">
    <div class="rp-card-body">
      <form id="filterForm" class="row g-3 align-items-end">
        <div class="col-12 col-sm-6 col-md-3">
          <label class="rp-label"><i class="fas fa-calendar-alt text-success me-1"></i> From Date</label>
          <input type="date" id="startDate" name="start_date" class="rp-input" value="{{ date('Y-m-01') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <label class="rp-label"><i class="fas fa-calendar-alt text-success me-1"></i> To Date</label>
          <input type="date" id="endDate" name="end_date" class="rp-input" value="{{ date('Y-m-d') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-2">
          <label class="rp-label"><i class="fas fa-store text-success me-1"></i> Branch</label>
          <select id="branchId" name="branch_id" class="rp-input">
            <option value="all">All Branches</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-sm-6 col-md-2">
          <label class="rp-label"><i class="fas fa-warehouse text-success me-1"></i> Warehouse</label>
          <select id="warehouseId" name="warehouse_id" class="rp-input">
            <option value="all">All Warehouses</option>
            @foreach($warehouses as $w)
            <option value="{{ $w->id }}">{{ $w->warehouse_name ?? ($w->name ?? 'Warehouse #'.$w->id) }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-sm-6 col-md-2">
          <label class="rp-label"><i class="fas fa-boxes text-success me-1"></i> Product</label>
          <select id="productId" name="product_id" class="rp-input select2">
            <option value="all">All Products</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->item_code }} - {{ $p->item_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
          <button type="button" id="resetBtn" class="btn btn-outline-secondary btn-sm px-3 fw-bold">
            <i class="fas fa-redo me-1"></i> Reset
          </button>
          <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background:#0e8349;">
            <i class="fas fa-filter me-1"></i> Apply Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════ KPI STATS ═══════ --}}
  <div class="rp-kpi-grid">
    <div class="rp-kpi-card">
      <div>
        <p class="rp-kpi-label">Total Batches</p>
        <h3 class="rp-kpi-val" id="kpiBatches">0</h3>
      </div>
      <div class="rp-kpi-icon blue">
        <i class="fas fa-layer-group"></i>
      </div>
    </div>

    <div class="rp-kpi-card">
      <div>
        <p class="rp-kpi-label">Total Finished Quantity</p>
        <h3 class="rp-kpi-val text-primary" id="kpiQty">0</h3>
      </div>
      <div class="rp-kpi-icon amber">
        <i class="fas fa-box-open"></i>
      </div>
    </div>

    <div class="rp-kpi-card">
      <div>
        <p class="rp-kpi-label">Total Production Cost</p>
        <h3 class="rp-kpi-val text-success" id="kpiCost">Rs 0</h3>
      </div>
      <div class="rp-kpi-icon green">
        <i class="fas fa-money-bill-wave"></i>
      </div>
    </div>

    <div class="rp-kpi-card">
      <div>
        <p class="rp-kpi-label">Raw Material Ingredients</p>
        <h3 class="rp-kpi-val text-purple" id="kpiRm">0 Used</h3>
      </div>
      <div class="rp-kpi-icon purple">
        <i class="fas fa-seedling"></i>
      </div>
    </div>
  </div>

  {{-- ═══════ DATA TABLE CARD ═══════ --}}
  <div class="rp-table-card">
    <div class="rp-table-header">
      <h3 class="rp-table-title">
        <i class="fas fa-clipboard-list text-success"></i> Detailed Production & Cost Breakdown
      </h3>
      <span class="text-muted small" id="recordCountText">Showing 0 records</span>
    </div>

    <div class="rp-loader" id="tableLoader">
      <div class="spinner-border" role="status"></div>
      <div class="mt-2 text-muted fw-bold">Loading Production Data & Costs...</div>
    </div>

    <div class="rp-table-wrapper" id="tableContainer">
      <table class="rp-table" id="productionReportTable">
        <thead>
          <tr>
            <th>Batch # / Date</th>
            <th>Location</th>
            <th style="min-width: 220px;">Finished Goods Produced (Qty)</th>
            <th style="min-width: 280px;">Raw Materials / Ingredients Used</th>
            <th class="text-end">Production Cost (Rs)</th>
            <th class="text-end">Avg Cost / Unit</th>
            <th class="text-center" style="width: 100px;">Action</th>
          </tr>
        </thead>
        <tbody id="reportTableBody">
          {{-- Populated via AJAX --}}
        </tbody>
        <tfoot id="reportTableFoot" style="display:none;">
          <tr style="background:#0f172a; color:#ffffff; font-weight:800;">
            <td colspan="2" class="text-white">TOTALS:</td>
            <td id="footTotalQty" class="text-white">0</td>
            <td id="footTotalRm" class="text-white">-</td>
            <td id="footTotalCost" class="text-end text-success fw-bold fs-6">Rs 0</td>
            <td id="footAvgCost" class="text-end text-white">-</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  $('.select2').select2({ width: '100%' });

  let dtInstance = null;

  function loadReport() {
    $('#tableLoader').show();
    $('#tableContainer').css('opacity', '0.4');

    $.ajax({
      url: "{{ route('report.production.fetch') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        branch_id: $('#branchId').val(),
        warehouse_id: $('#warehouseId').val(),
        product_id: $('#productId').val()
      },
      success: function(res) {
        $('#tableLoader').hide();
        $('#tableContainer').css('opacity', '1');

        if (dtInstance) {
          dtInstance.destroy();
          dtInstance = null;
        }

        let tbody = $('#reportTableBody');
        tbody.empty();

        if (!res.rows || res.rows.length === 0) {
          tbody.html('<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x d-block mb-2"></i>No production records found for the selected criteria.</td></tr>');
          $('#kpiBatches').text(0);
          $('#kpiQty').text(0);
          $('#kpiCost').text('Rs 0');
          $('#kpiRm').text('0 Used');
          $('#reportTableFoot').hide();
          $('#recordCountText').text('Showing 0 records');
          return;
        }

        // Update KPIs
        $('#kpiBatches').text((res.summary.total_batches || 0).toLocaleString());
        $('#kpiQty').text((res.summary.total_finished_qty || 0).toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 3}));
        $('#kpiCost').text('Rs ' + (res.summary.total_production_cost || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#kpiRm').text((res.summary.total_rm_records || 0) + ' Used');
        $('#recordCountText').text('Showing ' + res.rows.length + ' batch(es)');

        // Populate Table Rows
        let grandCost = 0;
        let grandQty = 0;

        res.rows.forEach(function(r) {
          grandCost += parseFloat(r.production_cost) || 0;
          grandQty += parseFloat(r.total_qty) || 0;

          // Finished Products HTML
          let finishedHtml = '<div class="d-flex flex-column gap-1">';
          if (r.finished_items && r.finished_items.length > 0) {
            r.finished_items.forEach(function(fi) {
              let qtyDisp = parseFloat(fi.qty_entered).toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 3});
              let variantDisp = fi.variant ? ' (' + fi.variant + ')' : '';
              finishedHtml += '<div class="item-chip">' +
                '<strong>' + fi.item_name + '</strong>' + variantDisp +
                ' — <span class="text-success fw-bold">' + qtyDisp + ' ' + fi.unit + '</span>' +
                '</div>';
            });
          } else {
            finishedHtml += '<span class="text-muted">No items</span>';
          }
          finishedHtml += '</div>';

          // Raw Materials Consumed HTML
          let rmHtml = '';
          if (r.raw_materials && r.raw_materials.length > 0) {
            rmHtml += '<div class="rm-breakdown-box">';
            r.raw_materials.forEach(function(rm) {
              let qtyFormatted = parseFloat(rm.qty_used).toFixed(2);
              let costFormatted = parseFloat(rm.cost_per_unit).toFixed(2);
              let totalFormatted = parseFloat(rm.total_cost).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
              rmHtml += '<div class="rm-item-line">' +
                '<span><strong>' + rm.name + '</strong> (' + qtyFormatted + ' ' + rm.unit + ' @ Rs ' + costFormatted + ')</span>' +
                '<span class="fw-bold text-success">Rs ' + totalFormatted + '</span>' +
                '</div>';
            });
            rmHtml += '</div>';
          } else {
            rmHtml = '<span class="text-muted small">None recorded</span>';
          }

          let dateDisp = r.production_date ? new Date(r.production_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
          let costDisp = (parseFloat(r.production_cost) || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
          let unitCostDisp = (parseFloat(r.unit_cost) || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

          let tr = '<tr>' +
            '<td>' +
              '<div class="fw-bold text-dark">#' + r.entry_no + '</div>' +
              '<div class="small text-muted font-monospace"><i class="fas fa-calendar-day me-1"></i>' + dateDisp + '</div>' +
              '<div class="small text-secondary"><i class="fas fa-user me-1"></i>' + r.user_name + '</div>' +
            '</td>' +
            '<td>' +
              '<div class="mb-1"><span class="rp-badge-source rp-badge-branch"><i class="fas fa-store me-1"></i>' + r.branch_name + '</span></div>' +
              (r.warehouse_name !== '-' ? '<div><span class="rp-badge-source rp-badge-wh"><i class="fas fa-warehouse me-1"></i>' + r.warehouse_name + '</span></div>' : '') +
            '</td>' +
            '<td>' + finishedHtml + '</td>' +
            '<td>' + rmHtml + '</td>' +
            '<td class="text-end fw-bold text-success fs-6">Rs ' + costDisp + '</td>' +
            '<td class="text-end fw-semibold text-dark">Rs ' + unitCostDisp + '</td>' +
            '<td class="text-center">' +
              '<a href="' + r.gatepass_url + '" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Print Gatepass">' +
                '<i class="fas fa-print me-1"></i> Pass' +
              '</a>' +
            '</td>' +
          '</tr>';

          tbody.append(tr);
        });

        // Footers
        $('#footTotalQty').text(grandQty.toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 3}) + ' Units/KG');
        $('#footTotalCost').text('Rs ' + grandCost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        let overallAvg = grandQty > 0 ? (grandCost / grandQty) : 0;
        $('#footAvgCost').text('Rs ' + overallAvg.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / unit');
        $('#reportTableFoot').show();

        // Initialize DataTable
        dtInstance = $('#productionReportTable').DataTable({
          pageLength: 25,
          lengthMenu: [10, 25, 50, 100],
          order: [[0, 'desc']],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search report entries..."
          }
        });
      },
      error: function(err) {
        $('#tableLoader').hide();
        $('#tableContainer').css('opacity', '1');
        alert('Failed to load production report. Please try again.');
      }
    });
  }

  // Initial load
  loadReport();

  $('#filterForm').submit(function(e) {
    e.preventDefault();
    loadReport();
  });

  $('#resetBtn').click(function() {
    $('#startDate').val("{{ date('Y-m-01') }}");
    $('#endDate').val("{{ date('Y-m-d') }}");
    $('#branchId').val('all');
    $('#warehouseId').val('all');
    $('#productId').val('all').trigger('change');
    loadReport();
  });
});
</script>
@endsection
