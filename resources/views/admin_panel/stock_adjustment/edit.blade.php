@extends('admin_panel.layout.app')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --a-bg: #f1f4f9;
  --a-surface: #ffffff;
  --a-border: #e9edf2;
  --a-border-light: #f1f4f9;
  --a-text: #0b1a33;
  --a-text-secondary: #54657e;
  --a-text-muted: #8896ab;
  --a-primary: #1a4d8c;
  --a-primary-light: #e8f0fe;
  --a-primary-dark: #0d3b6e;
  --a-accent: #2b7fff;
  --a-accent-dark: #1a6ae8;
  --a-success: #0fae6b;
  --a-success-light: #e9f9f2;
  --a-danger: #e54545;
  --a-danger-light: #fdf0f0;
  --a-warning: #f5a623;
  --a-radius: 14px;
  --a-radius-sm: 9px;
  --a-shadow-sm: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --a-shadow: 0 2px 8px rgba(0,0,0,.04), 0 1px 4px rgba(0,0,0,.06);
  --a-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --a-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --a-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.sa * { font-family: var(--a-font); }

.sa {
  background: var(--a-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

.sa .container-fluid { overflow: visible; }

/* ═══════ HEADER ═══════ */
.sa-header {
  position: relative;
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--a-radius);
  padding: 1.5rem 2rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--a-shadow-xl);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
}

.sa-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.15) 0%, transparent 100%),
    radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.08) 0%, transparent 100%);
  pointer-events: none;
}

.sa-header::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: .5;
  pointer-events: none;
}

.sa-header > * { position: relative; z-index: 1; }

.sa-header h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.4px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: .65rem;
}

.sa-header h2 i { font-size: 1.4rem; color: #60a5fa; }

.sa-header .hdr-badge {
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  border-radius: 20px;
  padding: .25rem .9rem;
  font-size: .75rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: .4px;
}

.sa-back {
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: var(--a-radius-sm);
  padding: .45rem 1.1rem;
  font-size: .82rem;
  font-weight: 600;
  color: rgba(255,255,255,.7);
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .45rem;
}

.sa-back:hover { background: rgba(255,255,255,.12); color: #fff; text-decoration: none; }

/* ═══════ CARDS ═══════ */
.sa-card {
  background: var(--a-surface);
  border: 1px solid var(--a-border);
  border-radius: var(--a-radius);
  box-shadow: var(--a-shadow-sm);
  margin-bottom: 1.25rem;
  transition: box-shadow .35s ease;
}

.sa-card:hover { box-shadow: var(--a-shadow); }

.sa-card-hdr {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--a-border-light);
  font-size: .82rem;
  font-weight: 700;
  color: var(--a-text);
  letter-spacing: -.1px;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.sa-card-hdr i { font-size: 1rem; color: var(--a-accent); }

.sa-card-bd { padding: 1.5rem; }

/* ═══════ FORM CONTROLS ═══════ */
.sa-lbl {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: .7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--a-text-secondary);
  margin-bottom: .4rem;
}

.sa-lbl i { font-size: .85rem; color: var(--a-accent); }
.sa-req { color: var(--a-danger); font-size: .8rem; }

.sa-fld {
  width: 100%;
  border: 1.5px solid var(--a-border);
  border-radius: var(--a-radius-sm);
  padding: .5rem .85rem;
  font-size: .88rem;
  font-weight: 500;
  color: var(--a-text);
  background: var(--a-surface);
  transition: all .2s ease;
  outline: none;
}

.sa-fld:focus {
  border-color: var(--a-accent);
  box-shadow: 0 0 0 3px rgba(43,127,255,.1);
}

select.sa-fld {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238896ab' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right .85rem center;
  padding-right: 2.2rem;
}

/* ═══════ ADJUSTMENT TYPE (SEGMENTED) ═══════ */
.sa-type {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .75rem;
  margin-top: .4rem;
}

.sa-type .tp-opt {
  position: relative;
  border: 1.5px solid var(--a-border);
  border-radius: var(--a-radius-sm);
  padding: .75rem 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: .75rem;
  transition: all .2s ease;
  background: var(--a-surface);
}

.sa-type .tp-opt:hover {
  border-color: var(--a-accent);
  background: #f8fafc;
}

.sa-type .tp-opt.active {
  border-color: var(--a-success);
  background: var(--a-success-light);
  box-shadow: 0 0 0 3px rgba(15,174,107,.12);
}

.sa-type .tp-opt.minus.active {
  border-color: var(--a-danger);
  background: var(--a-danger-light);
  box-shadow: 0 0 0 3px rgba(229,69,69,.12);
}

.sa-type .tp-opt input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.sa-type .tp-ic {
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #eef4ff;
  color: var(--a-accent);
  font-size: 1.1rem;
  flex: 0 0 auto;
  transition: all .2s ease;
}

.sa-type .tp-opt.active .tp-ic {
  background: var(--a-success);
  color: #fff;
}

.sa-type .tp-opt.minus.active .tp-ic {
  background: var(--a-danger);
  color: #fff;
}

.sa-type .tp-tx b {
  display: block;
  font-size: .88rem;
  color: var(--a-text);
  font-weight: 700;
  margin-bottom: 2px;
}

.sa-type .tp-tx span {
  font-size: .74rem;
  color: var(--a-text-secondary);
}

.sa-infobar {
  margin-top: .75rem;
  border-radius: var(--a-radius-sm);
  padding: .65rem 1rem;
  font-size: .82rem;
  display: flex;
  align-items: center;
  gap: .65rem;
  animation: saFd .2s ease;
}

.sa-infobar.inc {
  background: var(--a-success-light);
  color: #0b7a4d;
  border: 1px solid #b9ecd2;
}

.sa-infobar.dec {
  background: var(--a-danger-light);
  color: #a72d2d;
  border: 1px solid #f3c9c9;
}

/* ═══════ ITEMS TABLE ═══════ */
.sa-tbl-wrap {
  border: 1.5px solid var(--a-border);
  border-radius: var(--a-radius-sm);
  overflow: visible;
}

.sa-tbl {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: .84rem;
}

.sa-tbl thead th {
  background: #f8fafc;
  font-size: .68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--a-text-muted);
  padding: .65rem .85rem;
  border-bottom: 1.5px solid var(--a-border);
  text-align: left;
  white-space: nowrap;
}

.sa-tbl tbody td {
  padding: .55rem .65rem;
  border-bottom: 1px solid var(--a-border-light);
  vertical-align: middle;
}

.sa-tbl tbody tr:last-child td { border-bottom: none; }
.sa-tbl tbody tr:hover td { background: #fafbfc; }

.sa-tbl .t-qty { width: 110px; text-align: right; }
.sa-tbl .t-note { min-width: 140px; }

.sa-tbl .t-del {
  background: transparent;
  border: none;
  color: var(--a-danger);
  padding: .35rem .55rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all .2s;
  font-size: .95rem;
}

.sa-tbl .t-del:hover { background: var(--a-danger-light); }

.sa-tbl .t-conv {
  font-size: .7rem;
  color: var(--a-text-muted);
  display: block;
  margin-top: 2px;
  font-weight: 500;
}

/* ═══════ BUTTONS ═══════ */
.sa-btn {
  background: linear-gradient(135deg, var(--a-accent) 0%, var(--a-accent-dark) 100%);
  color: #fff;
  border: none;
  border-radius: var(--a-radius-sm);
  padding: .6rem 1.6rem;
  font-size: .88rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  cursor: pointer;
  transition: all .25s ease;
  box-shadow: 0 4px 12px rgba(43,127,255,.25);
  text-decoration: none;
}

.sa-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(43,127,255,.35);
  color: #fff;
  text-decoration: none;
}

.sa-btn-add {
  background: var(--a-surface);
  border: 1.5px dashed var(--a-border);
  border-radius: var(--a-radius-sm);
  padding: .55rem 1.1rem;
  font-size: .82rem;
  font-weight: 700;
  color: var(--a-accent);
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  cursor: pointer;
  transition: all .2s;
}

.sa-btn-add:hover { border-color: var(--a-accent); background: var(--a-primary-light); }

.sa-count {
  display: inline-flex; align-items: center; gap: 5px;
  background: var(--a-primary-light); color: var(--a-primary);
  border-radius: 20px; padding: .25rem .85rem;
  font-size: .75rem; font-weight: 700;
}

/* ═══════ SELECT2 ═══════ */
.sa .select2-container--default .select2-selection--single {
  height: 42px !important;
  border: 1.5px solid var(--a-border) !important;
  border-radius: var(--a-radius-sm) !important;
  padding: .2rem 0;
}

.sa .select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 38px !important; color: var(--a-text) !important; font-size: .88rem;
}

.sa .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px !important; }

.sa .select2-dropdown {
  border: 1.5px solid var(--a-border) !important;
  border-radius: var(--a-radius-sm) !important;
  box-shadow: var(--a-shadow);
}

/* ═══════ ANIMATIONS ═══════ */
@keyframes saFd {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

.sa-tbl tbody tr.sa-new { animation: saFd .25s ease; }

/* ═══════ RESPONSIVE ═══════ */
@media (max-width: 768px) {
  .sa-header { padding: 1.1rem 1.25rem; }
  .sa-header h2 { font-size: 1.05rem; }
  .sa-card-bd { padding: 1rem; }
  .sa .container-fluid { padding-left: .75rem !important; padding-right: .75rem !important; }
}

/* ═══════ MOBILE PREMIUM — stacked item cards ═══════ */
@media (max-width: 767.98px) {
  .sa { overflow-x: hidden; }
  .sa * { box-sizing: border-box; }
  .sa .container-fluid { overflow: hidden; }

  .sa-header { padding: 1.15rem; gap: .6rem; }
  .sa-header h2 { font-size: 1.02rem; }
  .sa-header h2 i { font-size: 1.15rem; }
  .sa-header .hdr-badge { font-size: .7rem; padding: .2rem .7rem; }
  .sa-back { width: 100%; justify-content: center; padding: .55rem .6rem; font-size: .8rem; }

  .sa-card-hdr { padding: .8rem 1rem; font-size: .8rem; }
  .sa-card-bd { padding: .95rem; }
  .sa-fld { font-size: .86rem; }

  .sa-type { gap: .55rem; }
  .sa-type .tp-opt { padding: .65rem .7rem; }
  .sa-type .tp-opt .tp-tx span { display: none; }
  .sa-type .tp-opt .tp-ic { width: 1.9rem; height: 1.9rem; font-size: .9rem; }

  .sa-infobar { font-size: .78rem; padding: .7rem .85rem; align-items: flex-start; }

  .sa-tbl-wrap { overflow: visible !important; }

  .sa-tbl,
  .sa-tbl tbody,
  .sa-tbl tr,
  .sa-tbl td {
    display: block;
    width: 100% !important;
  }

  .sa-tbl thead { display: none; }

  .sa-tbl tbody tr {
    position: relative;
    border: 1.5px solid var(--a-border);
    border-radius: var(--a-radius);
    margin-bottom: .9rem;
    padding: .7rem .8rem .8rem;
    background: var(--a-surface);
    box-shadow: var(--a-shadow-sm);
  }

  .sa-tbl tbody tr:last-child { margin-bottom: 0; }
  .sa-tbl tbody tr.sa-new { animation: saFd .25s ease; }

  .sa-tbl tbody td {
    padding: 0 0 .55rem;
    border: none !important;
    vertical-align: top;
  }

  .sa-tbl tbody td:last-child { padding-bottom: 0; }

  .sa-tbl tbody td::before {
    content: attr(data-label);
    display: block;
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--a-text-muted);
    margin-bottom: .3rem;
  }

  .sa-tbl tbody td[data-label=""]::before,
  .sa-tbl tbody td.del-cell::before { display: none; }

  .sa-tbl tbody td.del-cell { position: absolute; top: .55rem; right: .65rem; }

  .sa-tbl .t-del {
    width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
    padding: 0; opacity: 1; border-radius: 9px;
    border: 1.5px solid #f3c9c9; background: #fdf0f0;
  }

  .sa-tbl .t-qty { width: 100% !important; text-align: left; height: 44px; }
  .sa-tbl .t-note { width: 100% !important; min-width: 0 !important; }

  .sa-tbl tbody td:nth-child(3),
  .sa-tbl tbody td:nth-child(4) { display: inline-block; width: calc(50% - .3rem) !important; vertical-align: bottom; }
  .sa-tbl tbody td:nth-child(3) { margin-right: .6rem; }

  .sa-tbl .t-conv { white-space: normal; }

  .sa .select2-container--default .select2-selection--single {
    height: 46px !important; border-radius: var(--a-radius-sm) !important;
  }
  .sa .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important; font-size: .86rem;
  }
  .sa .select2-container--default .select2-selection--single .select2-selection__arrow { height: 44px !important; }

  .sa-card-bd .sa-btn-add { width: 100%; justify-content: center; padding: .7rem .6rem; font-size: .86rem; }

  .sa .text-muted { font-size: .72rem !important; }
  .sa-btn { width: 100%; justify-content: center; padding: .72rem .6rem; font-size: .9rem; }
}
</style>

@section('content')
<div class="sa">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══ HEADER ═══ --}}
    <div class="sa-header">
      <div class="d-flex align-items-center gap-3">
        <h2><i class="bi bi-pencil-square"></i>Edit Stock Adjustment</h2>
        <span class="hdr-badge"><i class="bi bi-hash me-1"></i>{{ $adjustment->ref_no }}</span>
      </div>
      <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
        <a href="{{ route('stock-adjustment.show', $adjustment->id) }}" class="sa-back"><i class="bi bi-eye"></i>View Detail</a>
        <a href="{{ route('stock-adjustment.index') }}" class="sa-back"><i class="bi bi-arrow-left"></i>Back to Adjustments</a>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show sa-alrt mb-4">
      <strong><i class="bi bi-check-circle me-1"></i></strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show sa-alrt mb-4">
      <strong><i class="bi bi-exclamation-triangle me-1"></i></strong>
      @foreach($errors->all() as $e)<span>{{ $e }}</span> @endforeach
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('stock-adjustment.update', $adjustment->id) }}" method="POST" id="adjForm">
      @csrf
      @method('PUT')

      {{-- ═══ ADJUSTMENT INFO ═══ --}}
      <div class="sa-card">
        <div class="sa-card-hdr"><i class="bi bi-clipboard-data"></i>Adjustment Information</div>
        <div class="sa-card-bd">
          <div class="row g-3">

            <div class="col-md-3">
              <label class="sa-lbl"><i class="bi bi-calendar3"></i>Date <span class="sa-req">*</span></label>
              <input type="date" name="adjustment_date" value="{{ old('adjustment_date', $adjustment->adjustment_date) }}" class="sa-fld" required>
            </div>

            <div class="col-md-3">
              <label class="sa-lbl"><i class="bi bi-building"></i>Warehouse</label>
              <select name="warehouse_id" class="sa-fld">
                <option value="">-- Main Stock / None --</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ (string)old('warehouse_id', $adjustment->warehouse_id) === (string)$w->id ? 'selected' : '' }}>
                  {{ $w->warehouse_name }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-3">
              @php
                $currentReason = old('reason', $adjustment->reason);
                $reasons = [
                  'Wastage'                    => 'Wastage / Kharab Maal',
                  'Mix Sale Adjustment'        => 'Mix Sale Adjustment',
                  'Physical Count Correction'  => 'Physical Count Correction',
                  'Damaged/Expired'            => 'Damaged / Expired',
                  'Bonus/Free'                 => 'Bonus / Free Stock Added',
                  'Opening Stock'              => 'Opening Stock',
                  'Other'                      => 'Other',
                ];
              @endphp
              <label class="sa-lbl"><i class="bi bi-collection"></i>Reason <span class="sa-req">*</span></label>
              <select name="reason" class="sa-fld" required>
                <option value="">-- Select Reason --</option>
                @foreach($reasons as $val => $lbl)
                  <option value="{{ $val }}" {{ $currentReason === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
                @if(!empty($currentReason) && !array_key_exists($currentReason, $reasons))
                  <option value="{{ $currentReason }}" selected>{{ $currentReason }}</option>
                @endif
              </select>
            </div>

            <div class="col-md-3">
              <label class="sa-lbl"><i class="bi bi-chat-text"></i>Internal Notes</label>
              <input type="text" name="notes" value="{{ old('notes', $adjustment->notes) }}" class="sa-fld" placeholder="Optional internal reference...">
            </div>
          </div>

          {{-- Type (segmented) --}}
          @php
            $currentType = old('type', $adjustment->type);
          @endphp
          <label class="sa-lbl mt-4"><i class="bi bi-arrow-left-right"></i>Adjustment Type <span class="sa-req">*</span></label>
          <div class="sa-type" id="saTypeWrap">
            <label class="tp-opt {{ $currentType === 'increase' ? 'active' : '' }}" data-t="increase">
              <input type="radio" name="type" value="increase" {{ $currentType === 'increase' ? 'checked' : '' }} required>
              <span class="tp-ic"><i class="bi bi-plus-lg"></i></span>
              <span class="tp-tx"><b>Increase Stock</b><span>Add stock — opening, bonus, correction</span></span>
            </label>
            <label class="tp-opt minus {{ $currentType === 'decrease' ? 'active' : '' }}" data-t="decrease">
              <input type="radio" name="type" value="decrease" {{ $currentType === 'decrease' ? 'checked' : '' }} required>
              <span class="tp-ic"><i class="bi bi-dash-lg"></i></span>
              <span class="tp-tx"><b>Decrease Stock</b><span>Remove stock — wastage, damaged, correction</span></span>
            </label>
          </div>

          <div id="infoBar" class="sa-infobar {{ $currentType ? ($currentType === 'increase' ? 'inc' : 'dec') : 'd-none' }}">
            @if($currentType === 'increase')
              <i class="bi bi-plus-circle-fill"></i><div><b>Increase:</b> stock will be <b>ADDED</b>. Use for opening stock, bonus / free items, and correction of undercounts.</div>
            @elseif($currentType === 'decrease')
              <i class="bi bi-dash-circle-fill"></i><div><b>Decrease:</b> stock will be <b>REMOVED</b>. Use for wastage, mix sale adjustment, damaged / expired, and correction of overcounts.</div>
            @endif
          </div>
        </div>
      </div>

      {{-- ═══ ITEMS ═══ --}}
      <div class="sa-card">
        <div class="sa-card-hdr">
          <i class="bi bi-box-seam"></i>Items
          <span class="sa-count ms-auto"><i class="bi bi-grid"></i><span id="rowCount">{{ count($adjustment->items) ?: 1 }}</span></span>
        </div>
        <div class="sa-card-bd">
          <div class="sa-tbl-wrap">
            <table class="sa-tbl" id="itemsTable">
              <thead>
                <tr>
                  <th width="30%">Product</th>
                  <th width="14%">Variant</th>
                  <th width="8%">Unit</th>
                  <th width="14%">Qty (KG / Pcs)</th>
                  <th>Item Note</th>
                  <th width="56px"></th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                @forelse($adjustment->items as $item)
                @php
                  $selectedProd = $item->product;
                @endphp
                <tr>
                  <td data-label="Product">
                    <select name="product_id[]" class="form-select sa-fld select2-prod" required>
                      <option value="">Search product...</option>
                      @foreach($products as $p)
                      <option value="{{ $p->id }}"
                        data-unit="{{ $p->unit_type === 'kg' ? 'KG' : ($p->unit->name ?? 'Pc') }}"
                        data-iskg="{{ $p->unit_type === 'kg' ? '1' : '0' }}"
                        data-vars="{{ json_encode($p->variants) }}"
                        {{ $item->product_id == $p->id ? 'selected' : '' }}>
                        {{ $p->item_code }} - {{ $p->item_name }}
                      </option>
                      @endforeach
                    </select>
                  </td>
                  <td data-label="Variant">
                    <select name="variant_id[]" class="form-select sa-fld select2-var">
                      <option value="">-- Main --</option>
                      @if($selectedProd && $selectedProd->variants)
                        @foreach($selectedProd->variants as $v)
                          @php
                            $vLabel = $v->size_label ?: ($v->variant_name ?: ($v->size_value . ' ' . $v->size_unit));
                          @endphp
                          <option value="{{ $v->id }}"
                            data-size="{{ $v->size_value }}"
                            data-vunit="{{ $v->size_unit }}"
                            {{ $item->variant_id == $v->id ? 'selected' : '' }}>
                            {{ $vLabel }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                  </td>
                  <td data-label="Unit">
                    <input type="text" class="form-control sa-fld unit-disp" value="{{ $item->unit }}" readonly style="text-align:center">
                  </td>
                  <td data-label="Qty (KG / Pcs)">
                    <input type="number" name="qty[]" class="form-control sa-fld t-qty" value="{{ $item->qty }}" step="0.001" min="0.001" required>
                    <small class="conv-disp"></small>
                  </td>
                  <td data-label="Item Note">
                    <input type="text" name="item_note[]" value="{{ $item->notes }}" class="form-control sa-fld t-note" placeholder="e.g. shelf found extra">
                  </td>
                  <td data-label="" class="del-cell">
                    <button type="button" class="t-del remove-row" title="Remove"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
                @empty
                <tr>
                  <td data-label="Product">
                    <select name="product_id[]" class="form-select sa-fld select2-prod" required>
                      <option value="">Search product...</option>
                      @foreach($products as $p)
                      <option value="{{ $p->id }}"
                        data-unit="{{ $p->unit_type === 'kg' ? 'KG' : ($p->unit->name ?? 'Pc') }}"
                        data-iskg="{{ $p->unit_type === 'kg' ? '1' : '0' }}"
                        data-vars="{{ json_encode($p->variants) }}">
                        {{ $p->item_code }} - {{ $p->item_name }}
                      </option>
                      @endforeach
                    </select>
                  </td>
                  <td data-label="Variant">
                    <select name="variant_id[]" class="form-select sa-fld select2-var">
                      <option value="">-- Main --</option>
                    </select>
                  </td>
                  <td data-label="Unit"><input type="text" class="form-control sa-fld unit-disp" readonly style="text-align:center"></td>
                  <td data-label="Qty (KG / Pcs)">
                    <input type="number" name="qty[]" class="form-control sa-fld t-qty" step="0.001" min="0.001" required>
                    <small class="conv-disp"></small>
                  </td>
                  <td data-label="Item Note"><input type="text" name="item_note[]" class="form-control sa-fld t-note" placeholder="e.g. shelf found extra"></td>
                  <td data-label="" class="del-cell"><button type="button" class="t-del remove-row" title="Remove"><i class="bi bi-trash3"></i></button></td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <button type="button" id="addRow" class="sa-btn-add mt-3"><i class="bi bi-plus-lg"></i>Add Row</button>
        </div>
      </div>

      {{-- ═══ SAVE ═══ --}}
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <small class="text-muted" style="font-size:.78rem">
          <i class="bi bi-info-circle me-1"></i>Previous stock effect of this adjustment will be reversed and updated immediately.
        </small>
        <button type="button" id="saveBtn" class="sa-btn" style="background: linear-gradient(135deg, #0fae6b 0%, #0b7a4d 100%);">
          <i class="bi bi-check2-circle"></i><span id="saveTxt">Update Adjustment</span>
          <span class="spinner-border spinner-border-sm d-none" id="saveSpin"></span>
        </button>
      </div>

    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

  // Initialize select2 & conversions on all initial rows
  $('#itemsBody tr').each(function () {
    initSelect2($(this));
    updateConv($(this));
  });
  rowCount();

  // ═══ TYPE SEGMENTED ═══
  $('.sa-type .tp-opt').on('click', function () {
    $('.sa-type .tp-opt').removeClass('active');
    $(this).addClass('active');
    $(this).find('input[type="radio"]').prop('checked', true);
    var t = $(this).data('t');
    var bar = $('#infoBar');
    if (t === 'increase') {
      bar.removeClass('d-none dec').addClass('inc').html('<i class="bi bi-plus-circle-fill"></i><div><b>Increase:</b> stock will be <b>ADDED</b>. Use for opening stock, bonus / free items, and correction of undercounts.</div>');
    } else {
      bar.removeClass('d-none inc').addClass('dec').html('<i class="bi bi-dash-circle-fill"></i><div><b>Decrease:</b> stock will be <b>REMOVED</b>. Use for wastage, mix sale adjustment, damaged / expired, and correction of overcounts.</div>');
    }
  });

  // ═══ ADD ROW ═══
  $('#addRow').click(function () {
    var first = $('#itemsBody tr:first');
    var newRow = first.clone(false);
    newRow.find('input').val('');
    newRow.find('.unit-disp').val('');
    newRow.find('.conv-disp').text('');
    newRow.find('.select2-container').remove();
    newRow.find('select').val('');
    newRow.find('.select2-var').html('<option value="">-- Main --</option>');
    newRow.addClass('sa-new');
    $('#itemsBody').append(newRow);
    initSelect2(newRow);
    rowCount();
  });

  // ═══ REMOVE ROW ═══
  $(document).on('click', '.remove-row', function () {
    if ($('#itemsBody tr').length > 1) {
      $(this).closest('tr').remove();
      rowCount();
    } else {
      Swal.fire({
        icon: 'info',
        title: 'At least one item required',
        text: 'You cannot remove the only row.',
        timer: 1800,
        showConfirmButton: false
      });
    }
  });

  // ═══ PRODUCT CHANGE ═══
  $(document).on('change', '.select2-prod', function () {
    var opt  = $(this).find(':selected');
    var row  = $(this).closest('tr');
    var vars = opt.data('vars') || [];

    row.find('.unit-disp').val(opt.data('unit') || '');

    var vSelect = row.find('.select2-var');
    vSelect.html('<option value="">-- Main --</option>');
    if (vars && vars.length > 0) {
      vars.forEach(function (v) {
        var label = v.size_label || v.variant_name || (v.size_value + ' ' + v.size_unit);
        vSelect.append('<option value="' + v.id + '" data-size="' + v.size_value + '" data-vunit="' + v.size_unit + '">' + label + '</option>');
      });
    }
    vSelect.trigger('change');
    updateConv(row);
  });

  // ═══ VARIANT CHANGE ═══
  $(document).on('change', '.select2-var', function () {
    updateConv($(this).closest('tr'));
  });

  // ═══ QTY CHANGE ═══
  $(document).on('input', 'input[name="qty[]"]', function () {
    updateConv($(this).closest('tr'));
  });

  function updateConv(row) {
    var opt  = row.find('.select2-prod option:selected');
    var isKg = opt.data('iskg') == '1';
    var qty  = parseFloat(row.find('input[name="qty[]"]').val()) || 0;

    if (isKg && qty > 0) {
      var vOpt = row.find('.select2-var option:selected');
      var grams = 0;
      if (vOpt.val()) {
        var size = parseFloat(vOpt.data('size')) || 0;
        var vUnit = vOpt.data('vunit');
        if (vUnit === 'kg') grams = (size * qty * 1000);
        else grams = (size * qty);
      } else {
        grams = (qty * 1000);
      }
      row.find('.conv-disp').text('= ' + Math.round(grams).toLocaleString() + ' grams');
    } else {
      row.find('.conv-disp').text('');
    }
  }

  function initSelect2(row) {
    row.find('.select2-prod').select2({ width: '100%', placeholder: 'Search product...' });
    row.find('.select2-var').select2({ width: '100%' });
  }

  function rowCount() {
    $('#rowCount').text($('#itemsBody tr').length);
  }

  // ═══ SAVE ═══
  $('#saveBtn').on('click', function () {
    if (!$('.sa-type .tp-opt.active').length) {
      Swal.fire({ icon: 'warning', title: 'Type Required', text: 'Select Increase or Decrease', timer: 1800, showConfirmButton: false }); return;
    }
    if (!$('select[name="reason"]').val()) {
      Swal.fire({ icon: 'warning', title: 'Reason Required', text: 'Select a reason for this adjustment', timer: 1800, showConfirmButton: false }); return;
    }
    var ok = false;
    $('select[name="product_id[]"]').each(function () { if ($(this).val()) ok = true; });
    if (!ok) {
      Swal.fire({ icon: 'warning', title: 'Empty', text: 'Add at least one product', timer: 1800, showConfirmButton: false }); return;
    }

    var b = $('#saveBtn'), s = $('#saveSpin'), t = $('#saveTxt');
    b.prop('disabled', true); s.removeClass('d-none'); t.text('Updating...');
    $('#adjForm').submit();
  });

  @if(session('success'))
  Swal.fire({ icon: 'success', title: 'Done!', text: @json(session('success')), confirmButtonColor: '#0fae6b' });
  @endif

  @if($errors->any())
  Swal.fire({ icon: 'error', title: 'Error', html: @json(implode('<br>', $errors->all())), confirmButtonColor: '#e54545' });
  @endif

});
</script>
@endsection
