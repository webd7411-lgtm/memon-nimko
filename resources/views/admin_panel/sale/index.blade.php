@extends('admin_panel.layout.app')

@section('content')
@php
    $fmt = function($val) {
        $val = (float)$val;
        return ($val == (int)$val) ? number_format($val, 0) : number_format($val, 2);
    };
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --sl-bg: #f8fafc;
  --sl-surface: #ffffff;
  --sl-border: #e2e8f0;
  --sl-border-lt: #f1f5f9;
  --sl-text: #0f172a;
  --sl-text-sec: #475569;
  --sl-text-muted: #64748b;
  --sl-primary: #2563eb;
  --sl-radius: 16px;
  --sl-radius-sm: 10px;
  --sl-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --sl-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --sl-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sl-page * {
  font-family: var(--sl-font);
}

.sl-page {
  background-color: var(--sl-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.sl-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--sl-radius);
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

.sl-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.sl-hero > * {
  position: relative;
  z-index: 1;
}

.sl-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.sl-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.sl-hero-icon {
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

.sl-hero-badge {
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

.sl-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.sl-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--sl-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.sl-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.sl-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.sl-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.sl-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ CASHIER SUMMARY BADGES ═══════ */
.cashier-chips {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-top: 0.75rem;
}

.cashier-chip {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 10px;
  padding: 0.4rem 0.85rem;
  color: #ffffff;
  font-size: 0.82rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.cashier-chip strong {
  color: #60a5fa;
}

/* ═══════ FILTER CARD ═══════ */
.sl-filter-card {
  background: var(--sl-surface);
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  padding: 1.15rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--sl-shadow);
}

.sl-input {
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius-sm);
  padding: 0.5rem 0.85rem;
  font-size: 0.88rem;
  color: var(--sl-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.sl-input:focus {
  border-color: var(--sl-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.sl-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--sl-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.sl-table-card {
  background: var(--sl-surface);
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  box-shadow: var(--sl-shadow-lg);
  overflow: hidden;
}

.sl-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--sl-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.sl-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--sl-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sl-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.sl-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.sl-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--sl-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--sl-border);
  border-top: none;
  white-space: nowrap;
}

.sl-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--sl-border-lt);
  font-size: 0.88rem;
  color: var(--sl-text-sec);
}

.sl-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
.sl-table { table-layout: fixed; width: 100% !important; }
.sl-table thead th { white-space: normal; }
.sl-table tbody td { white-space: normal; overflow-wrap: break-word; word-break: break-word; }
.sl-table tbody td .btn-group { display: flex; flex-wrap: wrap; }
.sl-table tbody td .btn-group .btn { white-space: normal; min-width: max-content; }
.sl-table-wrapper { overflow-x: hidden; }

/* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
.sl-cards { display: none; }

.slc-card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
  margin-bottom: .85rem;
  overflow: hidden;
}
.slc-head {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  padding: .8rem .9rem .7rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e9edf2;
}
.slc-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
.slc-inv { font-size: .98rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: break-word; }
.slc-sub { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: #64748b; font-weight: 600; }
.slc-status {
  flex: 0 0 auto; display: inline-flex; align-items: center; gap: 4px;
  border-radius: 20px; padding: .28rem .7rem; font-size: .68rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
}
.slc-status.ok { background: #e7f9f0; color: #0f7a4d; border: 1px solid #b9ecd2; }
.slc-status.ret { background: #fef2f2; color: #a72d2d; border: 1px solid #f3c9c9; }

.slc-meta { display: flex; flex-wrap: wrap; gap: .4rem .8rem; padding: .6rem .9rem; }
.slc-meta span { display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; color: #475569; font-weight: 600; word-break: break-word; }
.slc-meta span i { color: #94a3b8; font-size: .82rem; }

.slc-items { margin: .1rem .9rem .7rem; border: 1px solid #e9edf2; border-radius: 12px; overflow: hidden; }
.slc-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .5rem .7rem; background: #f8fafc; border-bottom: 1px solid #e9edf2;
  font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #475569;
}
.slc-items-hd i { color: #2563eb; font-size: .82rem; }
.slc-items-hd b { color: #2563eb; background: #eff6ff; border-radius: 20px; padding: .03rem .5rem; font-size: .66rem; }
.slc-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .5rem .7rem; background: #fff; border-bottom: 1px solid #f1f5f9;
}
.slc-item:last-child { border-bottom: none; }
.slc-item-nm { flex: 1 1 auto; min-width: 0; font-size: .82rem; font-weight: 600; color: #0f172a; word-break: break-word; line-height: 1.3; }
.slc-item-qty { flex: 0 0 auto; font-size: .74rem; font-weight: 700; color: #475569; background: #f1f5f9; border-radius: 7px; padding: .18rem .5rem; white-space: nowrap; }
.slc-item-total { flex: 0 0 auto; font-size: .78rem; font-weight: 800; color: #0f172a; white-space: nowrap; }

.slc-total {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  margin: 0 .9rem .7rem; padding: .6rem .8rem;
  background: linear-gradient(135deg, #eff6ff 0%, #e0ecff 100%);
  border: 1px solid #dbeafe; border-radius: 12px;
}
.slc-total span { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #1e40af; }
.slc-total b { font-size: 1.02rem; font-weight: 900; color: #1d4ed8; word-break: break-word; }

.slc-actions { display: flex; gap: .45rem; flex-wrap: wrap; padding: 0 .9rem .9rem; }
.slc-actions .btn-group { display: flex; flex-wrap: wrap; gap: .45rem; width: 100%; }
.slc-actions .btn {
  flex: 1 1 40%; min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
  border-radius: 10px !important; font-size: .78rem !important; font-weight: 700; margin: 0 !important; white-space: normal;
  text-decoration: none;
}
.slc-actions .btn:hover { transform: translateY(-1px); }
.slc-actions .btn-dark { background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(15, 23, 42, .25); }
.slc-actions .btn-info { background: linear-gradient(135deg, #38bdf8, #0284c7); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(14, 165, 233, .25); }
.slc-actions .btn-success { background: linear-gradient(135deg, #34d399, #059669); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, .25); }
.slc-actions .btn-primary { background: linear-gradient(135deg, #60a5fa, #2563eb); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(37, 99, 235, .25); }
.slc-actions .btn-warning { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #1f2937 !important; border: none; box-shadow: 0 4px 12px rgba(245, 158, 11, .25); }

.slc-empty {
  text-align: center; padding: 2.5rem 1rem; color: #64748b;
  display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.slc-empty i { font-size: 2.2rem; color: #cbd5e1; }


/* ═══════════════════════════════════════════════════
   MOBILE / TABLET PREMIUM — real cards, no horizontal scroll
═══════════════════════════════════════════════════ */
@media (max-width: 991.98px) {
  .sl-page { overflow-x: hidden; }
  .sl-page, .sl-page .container-fluid { max-width: 100%; }

  .sl-hero { padding: 1.05rem; flex-direction: column; align-items: stretch; gap: .8rem; }
  .sl-hero-title { gap: .7rem; }
  .sl-hero-title h2 { font-size: 1.12rem; }
  .sl-hero-icon { width: 42px; height: 42px; font-size: 1.15rem; }
  .sl-hero-actions { width: 100%; gap: .5rem; }
  .sl-hero-actions .sl-btn { flex: 1 1 auto; justify-content: center; padding: .6rem .45rem; font-size: .78rem; }

  .cashier-chips { gap: .5rem; }
  .cashier-chip { flex: 1 1 auto; justify-content: center; padding: .45rem .55rem; font-size: .78rem; text-align: center; }

  .sl-filter-card { padding: 1rem; }
  .sl-filter-card .sl-input { font-size: .86rem; }

  .sl-table-card { padding-bottom: .5rem; }

  /* Hide actual table on mobile, render premium cards instead */
  #productTable { display: none !important; }
  .sl-cards { display: block; padding: .5rem .6rem 1rem; }
  .sl-table-wrapper, .sl-table-wrapper .table-responsive { overflow: visible !important; padding: 0; }

  /* DataTables controls */
  .sl-table-card .dataTables_wrapper { padding: 0 0 .5rem; }
  .sl-table-card .dataTables_wrapper .row { margin: 0; }
  .sl-table-card .dataTables_filter,
  .sl-table-card .dataTables_length { width: 100%; padding: .4rem .6rem; }
  .sl-table-card .dataTables_filter input { width: 100% !important; min-width: 0; max-width: 100%; }
  .sl-table-card .dataTables_length select { max-width: 100%; }
  .sl-table-card .dataTables_info { padding: .4rem .6rem; font-size: .78rem; white-space: normal; }
  .sl-table-card .dataTables_paginate { padding: .4rem .6rem .6rem; display: flex; flex-wrap: wrap; gap: 3px; justify-content: flex-end; }
  .sl-table-card .dataTables_paginate .paginate_button { padding: .45rem .65rem; font-size: .76rem; margin: 0; }
}
</style>

<div class="sl-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="sl-hero">
      <div>
        <div class="sl-hero-title">
          <div class="sl-hero-icon">
            <i class="bi bi-cart-check-fill"></i>
          </div>
          <div>
            <h2>Sales Register</h2>
            <span class="sl-hero-badge">Transactions & Billing Ledger</span>
          </div>
        </div>

        @if(auth()->user()->roles->filter(function($role) { return stripos($role->name, 'cashier') !== false; })->isNotEmpty())
        <div class="cashier-chips">
          <div class="cashier-chip">
            <i class="bi bi-wallet2 text-warning"></i> Opening Cash: <strong>PKR {{ $fmt($openingBalance ?? 0) }}</strong>
          </div>
          <div class="cashier-chip">
            <i class="bi bi-graph-up-arrow text-success"></i> Today's Sale: <strong>PKR {{ $fmt($todaySales ?? 0) }}</strong>
          </div>
          <div class="cashier-chip">
            <i class="bi bi-dash-circle text-danger"></i> Today's Expense: <strong>PKR {{ $fmt($todayExpense ?? 0) }}</strong>
          </div>
          <div class="cashier-chip">
            <i class="bi bi-currency-dollar text-info"></i> Net Cash: <strong>PKR {{ $fmt($netCash ?? 0) }}</strong>
          </div>
        </div>
        @endif
      </div>

      <div class="sl-hero-actions">
        <a href="{{ route('sale.add') }}" class="sl-btn sl-btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Add Sale
        </a>

        <a href="{{ url('bookings') }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-journal-bookmark me-1"></i> All Bookings
        </a>

        <a href="{{ url('sale-returns') }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Sale Returns
        </a>

        <a href="{{ url()->previous() }}" class="sl-btn sl-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="sl-filter-card">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> From Date</label>
          <input type="date" id="filterFrom" class="sl-input">
        </div>

        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> To Date</label>
          <input type="date" id="filterTo" class="sl-input">
        </div>

        @if(auth()->user()->hasRole('Admin'))
        <div class="col-12 col-md-3">
          <label class="sl-label"><i class="bi bi-person me-1 text-primary"></i> Cashier / User</label>
          <select id="filterUser" class="sl-input">
            <option value="">All Users</option>
            @foreach(\App\Models\User::all() as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="col-12 col-md-3 d-flex gap-2">
          <button id="btnFilter" class="sl-btn sl-btn-primary w-100">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <button id="btnReset" class="sl-btn sl-btn-glass text-dark border w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </button>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="sl-table-card">
      <div class="sl-table-header">
        <h3 class="sl-table-title">
          <i class="bi bi-receipt text-primary me-1"></i> Completed & Pending Sale Transactions
        </h3>
      </div>

      <div class="sl-table-wrapper">
        <table id="productTable" class="table sl-table align-middle" style="width:100%">
          <thead>
            <tr>
              <th width="4%">S.No</th>
              <th width="10%">User</th>
              <th width="10%">Invoice No</th>
              <th width="12%">Customer</th>
              <th width="16%">Products</th>
              <th width="6%">Qty</th>
              <th width="8%">Price</th>
              <th width="8%">Discount</th>
              <th width="8%">Total Price</th>
              <th width="10%">Total Amount</th>
              <th width="10%">Date | Time</th>
              <th width="8%">Status</th>
              <th width="8%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loaded via DataTables Server-Side AJAX -->
          </tbody>
        </table>
      </div>

      {{-- ═══════ MOBILE CARDS (rendered by JS from DataTable data) ═══════ --}}
      <div id="saleCards" class="sl-cards"></div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<style>
div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 220px;
    margin-left: -110px;
    margin-top: -50px;
    text-align: center;
    padding: 15px;
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border-radius: 12px;
    z-index: 1000;
    font-size: 1rem;
    color: #0f172a;
}
.dataTables_filter input {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0.4rem 0.8rem !important;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('sale') }}",
            data: function (d) {
                d.from_date = $('#filterFrom').val();
                d.to_date = $('#filterTo').val();
                d.filter_user = $('#filterUser').val();
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4, orderable: false, searchable: false },
            { data: 5, orderable: false, searchable: false },
            { data: 6, orderable: false, searchable: false },
            { data: 7, orderable: false, searchable: false },
            { data: 8, orderable: false, searchable: false },
            { data: 9 },
            { data: 10 },
            { data: 11 },
            { data: 12, orderable: false, searchable: false }
        ],
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        order: [
            [2, 'desc']
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search Invoice/Customer...",
            processing: '<div class="spinner-border text-primary mb-2" role="status" style="width: 2.5rem; height: 2.5rem;"></div><div class="fw-bold">Loading Sales...</div>'
        },
        drawCallback: function () {
            if (window.matchMedia('(max-width: 991.98px)').matches) renderSaleCards();
        }
    });

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    function stripTags(html) {
        var d = document.createElement('div');
        d.innerHTML = html == null ? '' : String(html);
        return d.textContent;
    }

    function splitBr(html) {
        return String(html || '').split(/<br\s*\/?>/i).map(function (s) { return s.trim(); });
    }

    function renderSaleCards() {
        var data = table.rows({ page: 'current' }).data().toArray();
        var $wrap = $('#saleCards');
        if (!data.length) {
            $wrap.html('<div class="slc-empty"><i class="bi bi-inbox"></i><span>No sales found.</span></div>');
            return;
        }
        var html = '';
        data.forEach(function (r) {
            var user = r[1], invoice = r[2], customer = r[3],
                prods = splitBr(r[4]).filter(Boolean),
                qtys = splitBr(r[5]),
                tot = splitBr(r[8]),
                totalAmt = stripTags(r[9]),
                date = r[10],
                isRet = /Return/i.test(r[11] || '');

            var items = '';
            prods.forEach(function (p, i) {
                items += '<div class="slc-item">'
                    + '<span class="slc-item-nm">' + esc(p) + '</span>'
                    + '<span class="slc-item-qty">Qty ' + esc(qtys[i] || '-') + '</span>'
                    + '<span class="slc-item-total">' + esc(tot[i] || '') + '</span>'
                    + '</div>';
            });

            html += '<div class="slc-card">'
                + '<div class="slc-head">'
                + '<div class="slc-head-l">'
                + '<span class="slc-inv">' + esc(invoice) + '</span>'
                + '<span class="slc-sub"><i class="bi bi-person"></i>' + esc(user) + '</span>'
                + '</div>'
                + '<span class="slc-status ' + (isRet ? 'ret' : 'ok') + '"><i class="bi bi-' + (isRet ? 'arrow-return-left' : 'check2') + '"></i>' + (isRet ? 'Return' : 'Sale') + '</span>'
                + '</div>'
                + '<div class="slc-meta">'
                + '<span><i class="bi bi-calendar3"></i>' + esc(date) + '</span>'
                + '<span><i class="bi bi-person-badge"></i>' + esc(customer) + '</span>'
                + '</div>'
                + (items ? '<div class="slc-items"><div class="slc-items-hd"><span><i class="bi bi-box-seam"></i>Items</span><b>' + prods.length + '</b></div>' + items + '</div>' : '')
                + '<div class="slc-total"><span>Total Amount</span><b>PKR ' + esc(totalAmt) + '</b></div>'
                + '<div class="slc-actions">' + (r[12] || '') + '</div>'
                + '</div>';
        });
        $wrap.html(html);
    }

    $('#btnFilter').on('click', function() {
        table.draw();
    });

    $('#btnReset').on('click', function() {
        $('#filterFrom').val('');
        $('#filterTo').val('');
        $('#filterUser').val('');
        table.draw();
    });

    $(window).on('resize.slc', function () {
        if (window.matchMedia('(max-width: 991.98px)').matches) renderSaleCards();
    });
});
</script>
@endsection