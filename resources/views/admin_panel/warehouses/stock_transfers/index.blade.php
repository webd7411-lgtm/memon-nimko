@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --st-bg: #f8fafc;
  --st-surface: #ffffff;
  --st-border: #e2e8f0;
  --st-border-lt: #f1f5f9;
  --st-text: #0f172a;
  --st-text-sec: #475569;
  --st-text-muted: #64748b;
  --st-primary: #2563eb;
  --st-primary-dark: #1d4ed8;
  --st-success: #10b981;
  --st-warning: #f59e0b;
  --st-danger: #ef4444;
  --st-radius: 16px;
  --st-radius-sm: 10px;
  --st-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --st-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --st-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.st-page * {
  font-family: var(--st-font);
}

.st-page {
  background-color: var(--st-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.st-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--st-radius);
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

.st-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.st-hero > * {
  position: relative;
  z-index: 1;
}

.st-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.st-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.st-hero-icon {
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

.st-hero-badge {
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

.st-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.st-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--st-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.st-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.st-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.st-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.st-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.st-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
  .st-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
  .st-kpi-grid { grid-template-columns: 1fr; }
}

.st-kpi-card {
  background: var(--st-surface);
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--st-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.st-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--st-shadow-lg);
}

.st-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--st-text-muted);
}
.st-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--st-text);
  letter-spacing: -0.02em;
}

.st-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.st-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.st-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.st-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
.st-kpi-icon.purple { background: #f5f3ff; color: #8b5cf6; }

/* ═══════ FILTER BAR ═══════ */
.st-filter-card {
  background: var(--st-surface);
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius);
  padding: 1.15rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--st-shadow);
}

.st-form-control {
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius-sm);
  padding: 0.5rem 0.85rem;
  font-size: 0.88rem;
  color: var(--st-text);
  background: #ffffff;
  transition: all 0.2s ease;
}
.st-form-control:focus {
  border-color: var(--st-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.st-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--st-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.st-table-card {
  background: var(--st-surface);
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius);
  box-shadow: var(--st-shadow-lg);
  overflow: hidden;
}

.st-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--st-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.st-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--st-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.st-table-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.st-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.st-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--st-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--st-border);
  border-top: none;
  white-space: nowrap;
}

.st-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--st-border-lt);
  font-size: 0.88rem;
  color: var(--st-text-sec);
}

.st-table tbody tr:hover td {
  background-color: #f8fafc;
}

.st-table tbody tr.selected td {
  background-color: #eff6ff !important;
}

/* ═══════ BADGES & CHIPS ═══════ */
.st-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}
.st-badge-shop {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
}
.st-badge-wh {
  background: #eff6ff;
  color: #1e40af;
  border: 1px solid #bfdbfe;
}
.st-badge-date {
  background: #f8fafc;
  color: #475569;
  border: 1px solid #e2e8f0;
  font-family: monospace;
}

.st-item-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 0.25rem 0.65rem;
  font-size: 0.78rem;
  margin: 0.15rem;
  font-weight: 600;
  color: var(--st-text);
}
.st-item-chip .qty-tag {
  background: var(--st-primary);
  color: #ffffff;
  border-radius: 10px;
  padding: 0.1rem 0.45rem;
  font-size: 0.72rem;
  font-weight: 800;
}

.st-btn-action {
  padding: 0.35rem 0.75rem;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid var(--st-border);
  background: #ffffff;
  color: var(--st-primary);
  transition: all 0.15s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}
.st-btn-action:hover {
  background: var(--st-primary);
  color: #ffffff;
  border-color: var(--st-primary);
}

@media (max-width: 768px) {
  .st-hero { padding: 1.2rem 1.25rem; }
  .st-hero-title h2 { font-size: 1.2rem; }
  .st-table-header { padding: 1rem; }
  .st-filter-card { padding: 1rem; }
}

/* ═══════════════════════════════════════════════════
   MOBILE PREMIUM — transfer cards, no horizontal scroll
═══════════════════════════════════════════════════ */
.st-mlist { display: none; }

.st-mcard {
  background: var(--st-surface);
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius);
  box-shadow: var(--st-shadow);
  margin-bottom: .9rem;
  overflow: hidden;
}

.st-mcard-top {
  display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem;
  padding: 1rem 1.05rem .85rem;
}
.st-mcard-ref { display: flex; align-items: center; gap: .7rem; min-width: 0; }
.st-mcard-ic {
  width: 2.6rem; height: 2.6rem; flex: 0 0 auto; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  color: var(--st-primary); font-size: 1.15rem; border: 1px solid #dbeafe;
}
.st-mcard-id { font-weight: 800; font-size: .98rem; color: var(--st-text); letter-spacing: -.2px; word-break: break-word; }
.st-mcard-date { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: var(--st-text-muted); font-weight: 500; margin-top: 2px; }
.st-mcard-date i { font-size: .78rem; }

.st-mcard-route {
  display: flex; align-items: center; gap: .6rem;
  padding: .5rem .8rem .85rem;
}
.st-mcard-loc { flex: 1 1 0; min-width: 0; background: #f8fafc; border: 1px solid var(--st-border-lt); border-radius: 12px; padding: .6rem .7rem; display: flex; gap: .6rem; align-items: flex-start; }
.st-mcard-loc-ic {
  width: 2rem; height: 2rem; flex: 0 0 auto; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: .9rem;
}
.st-mcard-loc-ic.from { background: #eff6ff; color: var(--st-primary); }
.st-mcard-loc-ic.to { background: #ecfdf5; color: var(--st-success); }
.st-mcard-loc-lb { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--st-text-muted); margin-bottom: 2px; }
.st-mcard-loc-val { font-size: .82rem; font-weight: 700; color: var(--st-text); word-break: break-word; line-height: 1.3; }
.st-mcard-arrow { flex: 0 0 auto; color: var(--st-text-muted); font-size: 1rem; }

.st-mcard-items { margin: 0 .8rem .9rem; border: 1px solid var(--st-border-lt); border-radius: 12px; overflow: hidden; }
.st-mcard-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .55rem .75rem; background: #f8fafc; border-bottom: 1px solid var(--st-border-lt);
  font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--st-text-sec);
}
.st-mcard-items-hd i { color: var(--st-primary); font-size: .82rem; }
.st-mcard-items-hd b { color: var(--st-primary); background: #eff6ff; border-radius: 20px; padding: .05rem .5rem; font-size: .68rem; }
.st-mcard-item {
  display: flex; align-items: center; justify-content: space-between; gap: .7rem;
  padding: .55rem .75rem; background: #fff; border-bottom: 1px solid var(--st-border-lt);
}
.st-mcard-item:last-child { border-bottom: none; }
.st-mcard-item-nm { font-size: .84rem; font-weight: 600; color: var(--st-text); line-height: 1.3; word-break: break-word; min-width: 0; flex: 1 1 auto; }
.st-mcard-item-qty {
  flex: 0 0 auto; font-size: .76rem; font-weight: 800; color: var(--st-primary-dark);
  background: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: .25rem .6rem; white-space: nowrap;
}

.st-mcard-foot { padding: 0 .8rem .85rem; }
.st-mcard-receipt {
  display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff !important; text-decoration: none; border-radius: 11px; padding: .68rem .9rem;
  font-size: .82rem; font-weight: 700; box-shadow: 0 6px 16px rgba(37, 99, 235, .2);
}
.st-mcard-receipt:hover { color: #fff !important; text-decoration: none; box-shadow: 0 10px 24px rgba(37, 99, 235, .32); }
.st-mcard-receipt .bi-arrow-right { margin-left: auto; }

.st-mempty {
  background: var(--st-surface); border: 1px dashed var(--st-border); border-radius: var(--st-radius);
  text-align: center; padding: 2.5rem 1rem; color: var(--st-text-muted); min-height: 220px;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .4rem;
}
.st-mempty i { font-size: 2.2rem; color: #cbd5e1; }

@media (max-width: 991.98px) {
  .st-page { overflow-x: hidden; }
  .st-page, .st-page .container-fluid { max-width: 100%; }

  .st-table-card { display: none !important; }
  .st-mlist { display: block; }

  .st-hero { padding: 1.1rem; flex-direction: column; align-items: stretch; gap: .75rem; }
  .st-hero-title h2 { font-size: 1.12rem; }
  .st-hero-title { gap: .7rem; }
  .st-hero-icon { width: 42px; height: 42px; font-size: 1.2rem; }
  .st-hero-actions { width: 100%; gap: .5rem; }
  .st-hero-actions .st-btn { flex: 1 1 auto; justify-content: center; padding: .62rem .4rem; font-size: .76rem; }
  .st-hero-actions #btnExportSelected { display: none; }

  .st-filter-card { padding: 1rem; }
  .st-filter-card .st-form-control { font-size: .86rem; }

  .st-kpi-grid { grid-template-columns: 1fr 1fr; gap: .6rem; }
  .st-kpi-card { padding: .9rem .85rem; }
  .st-kpi-info p { font-size: .66rem; }
  .st-kpi-info h3 { font-size: 1.15rem; }
  .st-kpi-icon { width: 38px; height: 38px; font-size: 1.05rem; }
}

@media (max-width: 575.98px) {
  .st-kpi-grid { grid-template-columns: 1fr; }
}
</style>

<div class="st-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="st-hero">
      <div class="st-hero-title">
        <div class="st-hero-icon">
          <i class="bi bi-arrow-left-right"></i>
        </div>
        <div>
          <h2>Stock Transfers</h2>
          <span class="st-hero-badge">Inventory Movement Ledger</span>
        </div>
      </div>

      <div class="st-hero-actions">
        <a href="{{ route('warehouses.index') }}" class="st-btn st-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>

        <a href="{{ route('stock_transfers.create') }}" class="st-btn st-btn-primary">
          <i class="bi bi-plus-lg me-1"></i> + New Transfer
        </a>

        <button type="button" class="st-btn st-btn-glass" id="btnExportAll">
          <i class="bi bi-file-earmark-excel me-1 text-success"></i> Export All
        </button>

        <button type="button" class="st-btn st-btn-glass" id="btnExportSelected">
          <i class="bi bi-check2-square me-1"></i> Export Selected
        </button>
      </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalTransfersCount = count($transfers);
      $shopTransfersCount = 0;
      $whTransfersCount = 0;
      $todayTransfersCount = 0;
      $todayDate = \Carbon\Carbon::today()->format('Y-m-d');

      foreach($transfers as $t) {
        if ($t->transfer_to === 'shop') {
          $shopTransfersCount++;
        } else {
          $whTransfersCount++;
        }
        $tDate = \Carbon\Carbon::parse($t->created_at)->format('Y-m-d');
        if ($tDate === $todayDate) {
          $todayTransfersCount++;
        }
      }
    @endphp

    <div class="st-kpi-grid">
      <div class="st-kpi-card">
        <div class="st-kpi-info">
          <p>Total Transfers</p>
          <h3>{{ number_format($totalTransfersCount) }}</h3>
        </div>
        <div class="st-kpi-icon blue">
          <i class="bi bi-arrow-left-right"></i>
        </div>
      </div>

      <div class="st-kpi-card">
        <div class="st-kpi-info">
          <p>Shop Dispatches</p>
          <h3>{{ number_format($shopTransfersCount) }}</h3>
        </div>
        <div class="st-kpi-icon green">
          <i class="bi bi-shop"></i>
        </div>
      </div>

      <div class="st-kpi-card">
        <div class="st-kpi-info">
          <p>Warehouse Dispatches</p>
          <h3>{{ number_format($whTransfersCount) }}</h3>
        </div>
        <div class="st-kpi-icon amber">
          <i class="bi bi-building"></i>
        </div>
      </div>

      <div class="st-kpi-card">
        <div class="st-kpi-info">
          <p>Today's Activity</p>
          <h3>{{ number_format($todayTransfersCount) }}</h3>
        </div>
        <div class="st-kpi-icon purple">
          <i class="bi bi-calendar2-check"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ FILTER BAR ═══════ --}}
    <div class="st-filter-card">
      <form id="filterForm" class="row g-3 align-items-end">
        <div class="col-12 col-sm-5 col-md-4 col-lg-3">
          <label class="st-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
          <input type="date" id="startDate" class="st-form-control w-100" value="{{ request('start_date') }}">
        </div>

        <div class="col-12 col-sm-5 col-md-4 col-lg-3">
          <label class="st-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
          <input type="date" id="endDate" class="st-form-control w-100" value="{{ request('end_date') }}">
        </div>

        <div class="col-12 col-sm-2 col-md-4 col-lg-3 d-flex gap-2">
          <button type="button" class="st-btn st-btn-primary w-100" id="btnFilter">
            <i class="bi bi-funnel-fill me-1"></i> Filter
          </button>

          <button type="button" class="st-btn st-btn-glass text-dark border w-100" id="btnReset">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </button>
        </div>
      </form>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="st-table-card">
      <div class="st-table-header">
        <h3 class="st-table-title">
          <i class="bi bi-info-circle text-primary"></i> Recent Transfer Movements
        </h3>
      </div>

      <div class="st-table-wrapper">
        <table class="table st-table" id="stockTransferTable">
          <thead>
            <tr>
              <th width="4%" class="text-center">
                <input type="checkbox" id="selectAll" class="form-check-input">
              </th>
              <th width="8%">ID</th>
              <th width="12%">Date</th>
              <th width="18%">From Location</th>
              <th width="18%">To Location</th>
              <th width="28%">Transferred Items</th>
              <th width="12%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($transfers as $transfer)
            <tr>
              <td class="text-center">
                <input type="checkbox" class="row-checkbox form-check-input" value="{{ $transfer->id }}">
              </td>

              <td>
                <span class="fw-bold text-dark">#{{ $transfer->id }}</span>
              </td>

              <td>
                <span class="st-badge st-badge-date">
                  {{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y') }}
                </span>
              </td>

              <td>
                @if($transfer->fromWarehouse)
                  <span class="st-badge st-badge-wh">
                    <i class="bi bi-building me-1"></i> {{ $transfer->fromWarehouse->warehouse_name }}
                  </span>
                @else
                  <span class="st-badge st-badge-shop">
                    <i class="bi bi-shop me-1"></i> Shop Stock
                  </span>
                @endif
              </td>

              <td>
                @if ($transfer->transfer_to === 'shop')
                  <span class="st-badge st-badge-shop">
                    <i class="bi bi-shop me-1"></i> {{ $transfer->shop_name ?? 'Shop' }}
                  </span>
                @else
                  <span class="st-badge st-badge-wh">
                    <i class="bi bi-building me-1"></i> {{ $transfer->toWarehouse->warehouse_name ?? 'Warehouse' }}
                  </span>
                @endif
              </td>

              <td>
                @if (!empty($transfer->items))
                  @foreach ($transfer->items as $item)
                    <div class="st-item-chip">
                      <span>{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</span>
                      <span class="qty-tag">{{ $item['qty'] }} {{ $item['unit'] }}</span>
                    </div>
                  @endforeach
                @else
                  <span class="text-muted small">No items listed</span>
                @endif
              </td>

              <td class="text-center">
                <a href="{{ route('stock_transfers.receipt', $transfer->id) }}" class="st-btn-action" target="_blank">
                  <i class="bi bi-receipt me-1"></i> Receipt
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                No stock transfers recorded yet.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ═══════ MOBILE CARD LIST ═══════ --}}
    <div class="st-mlist">
      @forelse ($transfers as $transfer)
      <div class="st-mcard">
        <div class="st-mcard-top">
          <div class="st-mcard-ref">
            <span class="st-mcard-ic"><i class="bi bi-arrow-left-right"></i></span>
            <div>
              <div class="st-mcard-id">Transfer #{{ $transfer->id }}</div>
              <div class="st-mcard-date"><i class="bi bi-calendar3"></i>{{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y') }}</div>
            </div>
          </div>
        </div>

        <div class="st-mcard-route">
          <div class="st-mcard-loc">
            <span class="st-mcard-loc-ic from"><i class="bi bi-box-arrow-up-right"></i></span>
            <div class="st-mcard-loc-box w-100">
              <div class="st-mcard-loc-lb">From</div>
              <div class="st-mcard-loc-val">
                @if($transfer->fromWarehouse)
                  {{ $transfer->fromWarehouse->warehouse_name }}
                @else
                  Shop Stock
                @endif
              </div>
            </div>
          </div>
          <span class="st-mcard-arrow"><i class="bi bi-arrow-right"></i></span>
          <div class="st-mcard-loc">
            <span class="st-mcard-loc-ic to"><i class="bi bi-box-arrow-in-down-left"></i></span>
            <div class="st-mcard-loc-box w-100">
              <div class="st-mcard-loc-lb">To</div>
              <div class="st-mcard-loc-val">
                @if ($transfer->transfer_to === 'shop')
                  {{ $transfer->shop_name ?? 'Shop' }}
                @else
                  {{ $transfer->toWarehouse->warehouse_name ?? 'Warehouse' }}
                @endif
              </div>
            </div>
          </div>
        </div>

        @if (!empty($transfer->items))
        <div class="st-mcard-items">
          <div class="st-mcard-items-hd">
            <span><i class="bi bi-box-seam me-1"></i>Items</span>
            <b>{{ count($transfer->items) }}</b>
          </div>
          @foreach ($transfer->items as $item)
          <div class="st-mcard-item">
            <div class="st-mcard-item-nm">{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</div>
            <span class="st-mcard-item-qty">{{ $item['qty'] }} {{ $item['unit'] }}</span>
          </div>
          @endforeach
        </div>
        @else
        <div class="st-mcard-items"><div class="st-mcard-item"><span class="text-muted">No items listed</span></div></div>
        @endif

        <div class="st-mcard-foot">
          <a href="{{ route('stock_transfers.receipt', $transfer->id) }}" class="st-mcard-receipt" target="_blank">
            <i class="bi bi-receipt"></i> View Receipt <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
      @empty
      <div class="st-mempty"><i class="bi bi-inbox"></i><span>No stock transfers recorded yet.</span></div>
      @endforelse
    </div>

  </div>
</div>
@endsection

@section('scripts')
<!-- DataTables & SheetJS -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#stockTransferTable').DataTable({
        "order": [[ 1, "desc" ]],
        "pageLength": 25,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search transfers..."
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 5, 6] }
        ]
    });

    // Select All
    $('#selectAll').on('click', function() {
        var checked = this.checked;
        $('.row-checkbox').prop('checked', checked);
        toggleRowHighlight();
    });

    $(document).on('change', '.row-checkbox', function() {
        toggleRowHighlight();
    });

    function toggleRowHighlight() {
        $('.row-checkbox').each(function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('selected');
            } else {
                $(this).closest('tr').removeClass('selected');
            }
        });
    }

    // Filter Logic
    $('#btnFilter').on('click', function() {
        var start = $('#startDate').val();
        var end = $('#endDate').val();
        var url = new URL(window.location.href);

        if (start) url.searchParams.set('start_date', start);
        else url.searchParams.delete('start_date');

        if (end) url.searchParams.set('end_date', end);
        else url.searchParams.delete('end_date');

        window.location.href = url.toString();
    });

    $('#btnReset').on('click', function() {
        var url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        window.location.href = url.pathname;
    });

    // Export All to Excel
    $('#btnExportAll').on('click', function() {
        var data = [];
        table.rows({ search: 'applied' }).every(function() {
            var rowData = this.data();
            var $row = $(this.node());
            
            var id = $row.find('td:eq(1)').text().trim();
            var date = $row.find('td:eq(2)').text().trim();
            var fromLoc = $row.find('td:eq(3)').text().trim();
            var toLoc = $row.find('td:eq(4)').text().trim();
            var items = $row.find('td:eq(5)').text().trim().replace(/\s+/g, ' ');

            data.push({
                "Transfer ID": id,
                "Date": date,
                "From Location": fromLoc,
                "To Location": toLoc,
                "Items Transferred": items
            });
        });

        if (data.length === 0) {
            alert('No data available to export.');
            return;
        }

        var ws = XLSX.utils.json_to_sheet(data);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Stock Transfers");
        XLSX.writeFile(wb, "Stock_Transfers_Ledger.xlsx");
    });

    // Export Selected
    $('#btnExportSelected').on('click', function() {
        var data = [];
        $('.row-checkbox:checked').each(function() {
            var $row = $(this).closest('tr');
            var id = $row.find('td:eq(1)').text().trim();
            var date = $row.find('td:eq(2)').text().trim();
            var fromLoc = $row.find('td:eq(3)').text().trim();
            var toLoc = $row.find('td:eq(4)').text().trim();
            var items = $row.find('td:eq(5)').text().trim().replace(/\s+/g, ' ');

            data.push({
                "Transfer ID": id,
                "Date": date,
                "From Location": fromLoc,
                "To Location": toLoc,
                "Items Transferred": items
            });
        });

        if (data.length === 0) {
            alert('Please select at least one transfer record using the checkboxes.');
            return;
        }

        var ws = XLSX.utils.json_to_sheet(data);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Selected Transfers");
        XLSX.writeFile(wb, "Selected_Stock_Transfers.xlsx");
    });
});
</script>
@endsection