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
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 20px;
  padding: .25rem .9rem;
  font-size: .7rem;
  font-weight: 600;
  color: rgba(255,255,255,.65);
  letter-spacing: .4px;
  text-transform: uppercase;
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

/* ═══════ FORM ELEMENTS ═══════ */
.sa-lbl {
  font-size: .77rem;
  font-weight: 600;
  color: var(--a-text-secondary);
  margin-bottom: .35rem;
  letter-spacing: -.1px;
  display: flex;
  align-items: center;
  gap: .3rem;
}

.sa-lbl i { color: var(--a-accent); font-size: .8rem; }

.sa-req { color: var(--a-danger); font-weight: 700; }

.sa-fld {
  border: 1.5px solid var(--a-border);
  border-radius: var(--a-radius-sm);
  padding: .52rem .85rem;
  font-size: .88rem;
  font-weight: 500;
  color: var(--a-text);
  background: var(--a-surface);
  transition: all .25s ease;
  width: 100%;
  outline: none;
}

.sa-fld:focus {
  border-color: var(--a-accent);
  box-shadow: 0 0 0 3px rgba(43,127,255,.1);
}

.sa-fld::placeholder { color: var(--a-text-muted); font-weight: 400; }

select.sa-fld {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238896ab' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 32px;
}

/* ═══════ TYPE SEGMENTED CONTROL ═══════ */
.sa-type {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .6rem;
}

.sa-type .tp-opt {
  position: relative;
  cursor: pointer;
  border: 1.5px solid var(--a-border);
  border-radius: var(--a-radius-sm);
  padding: .8rem .9rem;
  transition: all .2s ease;
  display: flex;
  align-items: center;
  gap: .6rem;
  background: var(--a-surface);
}

.sa-type .tp-opt:hover { border-color: #c8d0dd; }

.sa-type .tp-opt input { display: none; }

.sa-type .tp-opt .tp-ic {
  width: 2.1rem; height: 2.1rem; flex-shrink: 0;
  border-radius: 8px; display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: var(--a-text-secondary);
  background: var(--a-border-light); transition: all .2s ease;
}

.sa-type .tp-opt .tp-tx b { display: block; font-size: .85rem; color: var(--a-text); font-weight: 700; }
.sa-type .tp-opt .tp-tx span { font-size: .72rem; color: var(--a-text-muted); font-weight: 500; }

.sa-type .tp-opt.active {
  border-color: var(--a-success);
  background: var(--a-success-light);
  box-shadow: 0 4px 14px rgba(15,174,107,.12);
}

.sa-type .tp-opt.active .tp-ic {
  background: linear-gradient(135deg, var(--a-success) 0%, #0c9a5e 100%);
  color: #fff;
}

.sa-type .tp-opt.active.minus {
  border-color: var(--a-danger);
  background: var(--a-danger-light);
  box-shadow: 0 4px 14px rgba(229,69,69,.12);
}

.sa-type .tp-opt.active.minus .tp-ic {
  background: linear-gradient(135deg, var(--a-danger) 0%, #c53333 100%);
  color: #fff;
}

/* ═══════ INFO BAR (type alert) ═══════ */
.sa-infobar {
  border-radius: var(--a-radius-sm);
  padding: .8rem 1.1rem;
  font-size: .84rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: .6rem;
  margin-bottom: 1.25rem;
  border-left: 4px solid;
  animation: saFd .25s ease;
}

.sa-infobar.inc {
  background: var(--a-success-light);
  color: #0b7a4d;
  border-left-color: var(--a-success);
}

.sa-infobar.dec {
  background: var(--a-danger-light);
  color: #a52d2d;
  border-left-color: var(--a-danger);
}

.sa-infobar b { font-weight: 700; }

/* ═══════ ITEMS TABLE ═══════ */
.sa-tbl-wrap { overflow-x: auto; }

.sa-tbl {
  width: 100%; border-collapse: separate; border-spacing: 0;
}

.sa-tbl thead th {
  background: #f8fafc;
  font-size: .71rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .6px; color: var(--a-text-muted);
  padding: .7rem .9rem; border-bottom: 2px solid var(--a-border);
  white-space: nowrap;
}

.sa-tbl tbody td {
  padding: .5rem .9rem; border-bottom: 1px solid var(--a-border-light);
  vertical-align: middle; font-size: .86rem; color: var(--a-text);
}

.sa-tbl tbody tr { transition: background .15s ease; }
.sa-tbl tbody tr:hover { background: #fafbfc; }
.sa-tbl tbody tr:last-child td { border-bottom: none; }

.sa-tbl .t-del {
  border: 1.5px solid #f1e0e0; border-radius: 6px;
  background: transparent; color: var(--a-danger);
  padding: .3rem .6rem; font-size: .8rem;
  transition: all .2s ease; cursor: pointer; opacity: .5;
}

.sa-tbl tr:hover .t-del { opacity: 1; }
.sa-tbl .t-del:hover { background: #fdf2f2; border-color: #f5c8c8; }

.sa-tbl .t-qty { width: 110px; text-align: center; font-weight: 600; }

.sa-tbl .t-conv {
  display: block; font-size: .72rem; color: var(--a-accent);
  font-weight: 600; margin-top: .15rem; white-space: nowrap;
}

.sa-tbl .t-note { min-width: 140px; }

/* ═══════ BUTTONS ═══════ */
.sa-btn {
  background: linear-gradient(135deg, var(--a-accent) 0%, var(--a-accent-dark) 100%);
  border: none; border-radius: var(--a-radius-sm);
  padding: .58rem 2.2rem; font-weight: 700; font-size: .88rem;
  color: #fff; transition: all .3s ease; cursor: pointer;
  display: inline-flex; align-items: center; gap: .5rem;
  position: relative; overflow: hidden;
}

.sa-btn::after {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.08) 0%, transparent 60%);
  pointer-events: none;
}

.sa-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(43,127,255,.28); color: #fff; }
.sa-btn:active { transform: translateY(0); }
.sa-btn:disabled { opacity: .65; cursor: not-allowed; transform: none; box-shadow: none; }

.sa-btn-add {
  background: var(--a-surface);
  border: 1.5px dashed var(--a-border);
  color: var(--a-accent);
  border-radius: var(--a-radius-sm);
  padding: .5rem 1.4rem; font-weight: 600; font-size: .84rem;
  transition: all .2s ease; cursor: pointer;
  display: inline-flex; align-items: center; gap: .45rem;
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

/* ═══════ MOBILE PREMIUM — stacked item cards, no horizontal scroll ═══════ */
@media (max-width: 767.98px) {
  .sa { overflow-x: hidden; }
  .sa * { box-sizing: border-box; }
  .sa .container-fluid { overflow: hidden; }

  .sa-header { padding: 1.15rem; gap: .6rem; }
  .sa-header h2 { font-size: 1.02rem; }
  .sa-header h2 i { font-size: 1.15rem; }
  .sa-header .hdr-badge { font-size: .62rem; padding: .2rem .7rem; }
  .sa-back { width: 100%; justify-content: center; padding: .55rem .6rem; font-size: .8rem; }

  .sa-card-hdr { padding: .8rem 1rem; font-size: .8rem; }
  .sa-card-bd { padding: .95rem; }
  .sa-fld { font-size: .86rem; }

  .sa-type { gap: .55rem; }
  .sa-type .tp-opt { padding: .65rem .7rem; }
  .sa-type .tp-opt .tp-tx span { display: none; }
  .sa-type .tp-opt .tp-ic { width: 1.9rem; height: 1.9rem; font-size: .9rem; }

  .sa-infobar { font-size: .78rem; padding: .7rem .85rem; align-items: flex-start; }

  /* Items: table → stacked cards */
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

  /* Field labels above each stacked input */
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

  /* Qty + unit side by side */
  .sa-tbl tbody td:nth-child(3),
  .sa-tbl tbody td:nth-child(4) { display: inline-block; width: calc(50% - .3rem) !important; vertical-align: bottom; }
  .sa-tbl tbody td:nth-child(3) { margin-right: .6rem; }

  .sa-tbl .t-conv { white-space: normal; }

  /* select2 full-width on mobile */
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
        <h2><i class="bi bi-arrow-left-right"></i>Stock Adjustment</h2>
        <span class="hdr-badge d-none d-sm-inline">New Entry</span>
      </div>
      <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
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

    <form action="{{ route('stock-adjustment.store') }}" method="POST" id="adjForm">
      @csrf

      {{-- ═══ ADJUSTMENT INFO ═══ --}}
      <div class="sa-card">
        <div class="sa-card-hdr"><i class="bi bi-clipboard-data"></i>Adjustment Information</div>
        <div class="sa-card-bd">
          <div class="row g-3">

            <div class="col-md-3">
              <label class="sa-lbl"><i class="bi bi-calendar3"></i>Date <span class="sa-req">*</span></label>
              <input type="date" name="adjustment_date" value="{{ date('Y-m-d') }}" class="sa-fld" required>
            </div>

            <div class="col-md-3">
              <label class="sa-lbl"><i class="bi bi-collection"></i>Reason <span class="sa-req">*</span></label>
              <select name="reason" class="sa-fld" required>
                <option value="">-- Select Reason --</option>
                <option value="Wastage">Wastage / Kharab Maal</option>
                <option value="Mix Sale Adjustment">Mix Sale Adjustment</option>
                <option value="Physical Count Correction">Physical Count Correction</option>
                <option value="Damaged/Expired">Damaged / Expired</option>
                <option value="Bonus/Free">Bonus / Free Stock Added</option>
                <option value="Opening Stock">Opening Stock</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="sa-lbl"><i class="bi bi-chat-text"></i>Internal Notes</label>
              <input type="text" name="notes" class="sa-fld" placeholder="Optional internal reference...">
            </div>
          </div>

          {{-- Type (segmented) --}}
          <label class="sa-lbl mt-4"><i class="bi bi-arrow-left-right"></i>Adjustment Type <span class="sa-req">*</span></label>
          <div class="sa-type" id="saTypeWrap">
            <label class="tp-opt" data-t="increase">
              <input type="radio" name="type" value="increase" required>
              <span class="tp-ic"><i class="bi bi-plus-lg"></i></span>
              <span class="tp-tx"><b>Increase Stock</b><span>Add stock — opening, bonus, correction</span></span>
            </label>
            <label class="tp-opt minus" data-t="decrease">
              <input type="radio" name="type" value="decrease" required>
              <span class="tp-ic"><i class="bi bi-dash-lg"></i></span>
              <span class="tp-tx"><b>Decrease Stock</b><span>Remove stock — wastage, damaged, correction</span></span>
            </label>
          </div>

          <div id="infoBar" class="sa-infobar d-none"></div>
        </div>
      </div>

      {{-- ═══ ITEMS ═══ --}}
      <div class="sa-card">
        <div class="sa-card-hdr">
          <i class="bi bi-box-seam"></i>Items
          <span class="sa-count ms-auto"><i class="bi bi-grid"></i><span id="rowCount">1</span></span>
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
              </tbody>
            </table>
          </div>

          <button type="button" id="addRow" class="sa-btn-add mt-3"><i class="bi bi-plus-lg"></i>Add Row</button>
        </div>
      </div>

      {{-- ═══ SAVE ═══ --}}
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <small class="text-muted" style="font-size:.78rem"><i class="bi bi-info-circle me-1"></i>All changes are applied to stock immediately on save.</small>
        <button type="button" id="saveBtn" class="sa-btn">
          <i class="bi bi-check2-circle"></i><span id="saveTxt">Save Adjustment</span>
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

  initSelect2($('#itemsBody tr:first'));

  // ═══ TYPE SEGMENTED ═══
  $('.sa-type .tp-opt').on('click', function () {
    $('.sa-type .tp-opt').removeClass('active');
    $(this).addClass('active');
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
    newRow.find('select').trigger('change');
    newRow.find('.remove-row').click(function () {
      if ($('#itemsBody tr').length > 1) { $(this).closest('tr').remove(); rowCount(); }
    });
    rowCount();
  });

  // ═══ REMOVE ROW ═══
  $(document).on('click', '.remove-row', function () {
    if ($('#itemsBody tr').length > 1) { $(this).closest('tr').remove(); rowCount(); }
  });

  // ═══ PRODUCT CHANGE ═══
  $(document).on('change', '.select2-prod', function () {
    var opt  = $(this).find(':selected');
    var row  = $(this).closest('tr');
    var vars = opt.data('vars') || [];

    row.find('.unit-disp').val(opt.data('unit') || '');

    var vSelect = row.find('.select2-var');
    vSelect.html('<option value="">-- Main --</option>');
    vars.forEach(function (v) {
      var label = v.size_label || v.variant_name || (v.size_value + ' ' + v.size_unit);
      vSelect.append('<option value="' + v.id + '" data-size="' + v.size_value + '" data-vunit="' + v.size_unit + '">' + label + '</option>');
    });
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
    b.prop('disabled', true); s.removeClass('d-none'); t.text('Saving...');
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
