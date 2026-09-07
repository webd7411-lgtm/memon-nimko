@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --coa-bg: #f8fafc;
  --coa-surface: #ffffff;
  --coa-border: #e2e8f0;
  --coa-border-lt: #f1f5f9;
  --coa-text: #090d16;
  --coa-text-sec: #475569;
  --coa-text-muted: #64748b;
  --coa-primary: #0f766e;
  --coa-radius: 16px;
  --coa-radius-sm: 10px;
  --coa-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --coa-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --coa-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.coa-page * {
  font-family: var(--coa-font);
}

.coa-page {
  background-color: var(--coa-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.coa-hero {
  position: relative;
  background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
  border-radius: var(--coa-radius);
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

.coa-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.coa-hero > * {
  position: relative;
  z-index: 1;
}

.coa-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.coa-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.coa-hero-icon {
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

.coa-hero-badge {
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

.coa-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--coa-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.coa-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.coa-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.coa-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.coa-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.coa-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .coa-kpi-grid { grid-template-columns: 1fr; }
}

.coa-kpi-card {
  background: var(--coa-surface);
  border: 1px solid var(--coa-border);
  border-radius: var(--coa-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--coa-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.coa-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--coa-shadow-lg);
}

.coa-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--coa-text-muted);
}
.coa-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--coa-text);
  letter-spacing: -0.02em;
}

.coa-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.coa-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.coa-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.coa-kpi-icon.purple { background: #faf5ff; color: #a855f7; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.coa-table-card {
  background: var(--coa-surface);
  border: 1px solid var(--coa-border);
  border-radius: var(--coa-radius);
  box-shadow: var(--coa-shadow-lg);
  overflow: hidden;
}

.coa-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--coa-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.coa-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--coa-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.coa-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.coa-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.coa-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--coa-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--coa-border);
  border-top: none;
  white-space: nowrap;
}

.coa-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--coa-border-lt);
  font-size: 0.88rem;
  color: var(--coa-text-sec);
}

.coa-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
.coa-table { table-layout: fixed; width: 100% !important; }
.coa-table thead th { white-space: normal !important; overflow-wrap: break-word; }
.coa-table tbody td { white-space: normal !important; overflow-wrap: break-word; word-break: break-word; }
.coa-table tbody td .d-flex { flex-wrap: wrap; }
.coa-table-wrapper { overflow-x: hidden; }

/* ═══════ MODAL STYLING ═══════ */
.coa-modal .modal-content {
  border: none;
  border-radius: var(--coa-radius);
  box-shadow: var(--coa-shadow-lg);
  overflow: hidden;
}

.coa-modal .modal-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}

.coa-modal .modal-title {
  font-weight: 700;
  font-size: 1.15rem;
}

.coa-input {
  border: 1px solid var(--coa-border);
  border-radius: var(--coa-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--coa-text);
  width: 100%;
}
.coa-input:focus {
  border-color: var(--coa-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

/* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
.coac-cards { display: none; }

.coac-card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
  margin-bottom: .85rem;
  overflow: hidden;
}
.coac-head {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  padding: .8rem .9rem .7rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e9edf2;
}
.coac-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
.coac-title { font-size: .93rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: break-word; }
.coac-sub { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: #64748b; font-weight: 600; word-break: break-word; }
.coac-sub i { color: #94a3b8; font-size: .82rem; }
.coac-status {
  flex: 0 0 auto; display: inline-flex; align-items: center; gap: 4px;
  border-radius: 20px; padding: .28rem .7rem; font-size: .68rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
}
.coac-status.active { background: #e7f9f0; color: #0f7a4d; border: 1px solid #b9ecd2; }
.coac-status.inactive { background: #fef2f2; color: #a72d2d; border: 1px solid #f3c9c9; }

.coac-meta { display: flex; flex-wrap: wrap; gap: .4rem .8rem; padding: .6rem .9rem; }
.coac-meta span { display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; color: #475569; font-weight: 600; word-break: break-word; }
.coac-meta span i { color: #94a3b8; font-size: .82rem; }

.coac-balance {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  margin: 0 .9rem .7rem; padding: .6rem .8rem;
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  border: 1px solid #fecaca; border-radius: 12px;
}
.coac-balance span { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #991b1b; }
.coac-balance b { font-size: 1rem; font-weight: 900; color: #dc2626; word-break: break-word; }

.coac-actions { display: flex; gap: .45rem; flex-wrap: wrap; padding: 0 .9rem .9rem; }
.coac-actions .btn {
  flex: 1 1 40%; min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
  border-radius: 10px !important; font-size: .78rem !important; font-weight: 700; margin: 0 !important; white-space: normal;
  text-decoration: none;
}
.coac-actions .btn:hover { transform: translateY(-1px); }
.coac-actions .btn-outline-primary { background: linear-gradient(135deg, #60a5fa, #2563eb); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(37, 99, 235, .25); }
.coac-actions .btn-outline-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, .25); }

.coac-empty {
  text-align: center; padding: 2.5rem 1rem; color: #64748b;
  display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.coac-empty i { font-size: 2.2rem; color: #cbd5e1; }

/* ═══════ FULL MOBILE RESPONSIVE (no horizontal scroll) ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .coa-page { padding-bottom: 2rem; }

  .coa-hero { padding: 1.1rem 1rem; gap: .75rem; }
  .coa-hero-title { gap: .6rem; align-items: flex-start; }
  .coa-hero-title h2 { font-size: 1.05rem; }
  .coa-hero-icon { width: 40px; height: 40px; font-size: 1.15rem; border-radius: 10px; }
  .coa-hero-badge { font-size: .6rem; padding: .22rem .6rem; }
  .coa-hero .d-flex { width: 100%; flex-direction: column; align-items: stretch; }
  .coa-hero .d-flex .coa-btn { width: 100%; justify-content: center; }

  .coa-kpi-grid { gap: .65rem; margin-bottom: 1rem; }
  .coa-kpi-card { padding: .9rem .85rem; border-radius: 14px; }
  .coa-kpi-info p { font-size: .66rem; }
  .coa-kpi-info h3 { font-size: 1.2rem; }
  .coa-kpi-icon { width: 38px; height: 38px; font-size: 1rem; }

  .coa-table-card { border-radius: 14px; }
  .coa-table-header { padding: .9rem 1rem; }
  .coa-table-title { font-size: .92rem; }

  /* DataTables controls: full width, no float overlap */
  .coa-table-wrapper { overflow: hidden; padding: 0; }
  .coa-table-wrapper > .dataTables_wrapper > .row > div { flex: 0 0 100%; max-width: 100%; }
  .coa-table-wrapper .dataTables_filter,
  .coa-table-wrapper .dataTables_length { float: none !important; text-align: left !important; margin: .35rem 0 0; }
  .coa-table-wrapper .dataTables_length { margin-top: .75rem; }
  .coa-table-wrapper .dataTables_length label { font-size: .78rem; color: var(--coa-text-muted); white-space: normal; }
  .coa-table-wrapper .dataTables_length select,
  .coa-table-wrapper .dataTables_filter input {
    display: inline-block;
    width: auto;
    padding: .45rem .7rem;
    border: 1px solid var(--coa-border);
    border-radius: 10px;
    font-size: .85rem;
    background: #fff;
    color: var(--coa-text);
    margin: 0 0 0 .35rem !important;
  }
  .coa-table-wrapper .dataTables_filter input { width: 100% !important; margin: .35rem 0 0 0 !important; }
  .coa-table-wrapper .dataTables_filter input:focus,
  .coa-table-wrapper .dataTables_length select:focus {
    border-color: var(--coa-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    outline: none;
  }

  /* Hide actual table on mobile, render premium cards instead */
  #productTable { display: none !important; }
  .coac-cards { display: block; padding: .5rem .6rem 1rem; }

  /* Pagination */
  .coa-table-wrapper .dataTables_info { text-align: center; font-size: .72rem; padding: 1rem 0 .35rem; }
  .coa-table-wrapper .pagination { flex-wrap: wrap; justify-content: center; gap: .25rem; }
  .coa-table-wrapper .page-item { margin: 0; }
  .coa-table-wrapper .page-link {
    min-width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 .5rem;
    font-size: .75rem;
    border: 1px solid var(--coa-border);
    border-radius: 8px;
    color: var(--coa-text-sec);
    background: #fff;
  }
  .coa-table-wrapper .page-item.active .page-link {
    background: var(--coa-primary);
    border-color: var(--coa-primary);
    color: #fff;
  }
}

@media (max-width: 399.98px) {
  .coa-hero .d-flex { gap: .4rem; }
  .coa-table tbody td { font-size: .8rem; }
}
</style>

<div class="coa-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="coa-hero">
      <div class="coa-hero-title">
        <div class="coa-hero-icon">
          <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div>
          <h2>Chart of Accounts</h2>
          <span class="coa-hero-badge">Expense Heads & Account Control</span>
        </div>
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('Payment-vochers') }}" class="coa-btn coa-btn-primary" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); border: none;">
          <i class="bi bi-wallet2 me-1"></i> + Vendor Payment Voucher
        </a>

        <a href="{{ route('recepit-vochers') }}" class="coa-btn coa-btn-glass">
          <i class="bi bi-receipt-cutoff me-1"></i> + Customer Receipt
        </a>

        <a href="{{ route('expense-vochers') }}" class="coa-btn coa-btn-glass">
          <i class="bi bi-receipt me-1"></i> + Expense Voucher
        </a>

        <button class="coa-btn coa-btn-glass" data-bs-toggle="modal" data-bs-target="#addAccountModal">
          <i class="bi bi-plus-circle me-1"></i> Add Account
        </button>

        <button class="coa-btn coa-btn-glass" data-bs-toggle="modal" data-bs-target="#addHeadModal">
          <i class="bi bi-folder-plus me-1"></i> Add Head
        </button>

        <a href="{{ url()->previous() }}" class="coa-btn coa-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalAccountsCount = count($accounts);
      $activeAccountsCount = $accounts->where('status', 1)->count();
      $totalHeadsCount = count($heads);
    @endphp

    <div class="coa-kpi-grid">
      <div class="coa-kpi-card">
        <div class="coa-kpi-info">
          <p>Total Accounts</p>
          <h3>{{ number_format($totalAccountsCount) }}</h3>
        </div>
        <div class="coa-kpi-icon" style="background:#f0fdf4; color:#0f766e;">
          <i class="bi bi-book-half"></i>
        </div>
      </div>

      <div class="coa-kpi-card">
        <div class="coa-kpi-info">
          <p>Active Accounts</p>
          <h3>{{ number_format($activeAccountsCount) }}</h3>
        </div>
        <div class="coa-kpi-icon" style="background:#f0fdf4; color:#0f766e;">
          <i class="bi bi-check-circle-fill"></i>
        </div>
      </div>

      <div class="coa-kpi-card">
        <div class="coa-kpi-info">
          <p>Expense Heads</p>
          <h3>{{ number_format($totalHeadsCount) }}</h3>
        </div>
        <div class="coa-kpi-icon" style="background:#f8fafc; color:#334155;">
          <i class="bi bi-diagram-2"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="coa-table-card">
      <div class="coa-table-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h3 class="coa-table-title mb-0">
          <i class="bi bi-journals me-1" style="color:#0f766e;"></i> Account Ledger Directory
        </h3>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="{{ route('Payment-vochers') }}" class="btn btn-sm rounded-pill px-3 py-1 font-weight-bold shadow-sm" style="font-size:0.78rem; background: linear-gradient(135deg, #0f766e, #0d9488); color:#fff; border: none;">
            <i class="bi bi-cash-stack me-1"></i> + Vendor Payment Voucher
          </a>
          <a href="{{ route('all-Payment-vochers') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 font-weight-bold" style="font-size:0.78rem;">
            <i class="bi bi-journal-text me-1"></i> Vendor Payment History
          </a>
          <a href="{{ route('all-recepit-vochers') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 font-weight-bold" style="font-size:0.78rem;">
            <i class="bi bi-receipt me-1"></i> Receipt History
          </a>
        </div>
      </div>

      <div class="coa-table-wrapper">
        <table id="productTable" class="table coa-table align-middle nowrap" style="width:100%">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="12%">Account Code</th>
              <th width="18%">Expense Head</th>
              <th width="22%">Account Title</th>
              <th width="15%">Closing Balance</th>
              <th width="10%" class="text-center">Status</th>
              <th width="18%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($accounts as $key => $account)
            <tr>
              <td class="fw-bold">#{{ $key+1 }}</td>
              <td>
                <span class="badge bg-light text-dark border font-monospace fw-bold">
                  {{ $account->account_code }}
                </span>
              </td>
              <td>
                <span class="badge border fw-semibold" style="background:#f0fdf4; color:#0f766e; border-color:#ccfbf1!important;">
                  <i class="bi bi-diagram-2 me-1"></i>{{ $account->head->name ?? 'N/A' }}
                </span>
              </td>
              <td class="fw-bold text-dark">{{ $account->title }}</td>
              <td class="fw-bold" style="color:#0f766e;">
                PKR {{ number_format((float)($account->opening_balance ?? 0), 2) }}
              </td>
              <td class="text-center">
                @if($account->status)
                  <span class="badge rounded-pill px-2 py-1" style="background:#0f766e; color:#fff;">Active</span>
                @else
                  <span class="badge rounded-pill px-2 py-1" style="background:#334155; color:#fff;">Inactive</span>
                @endif
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                  <a href="{{ route('Payment-vochers') }}" class="btn btn-xs rounded-2 fw-semibold" title="Create Vendor / Account Payment Voucher" style="font-size:0.72rem; padding: 2px 7px; background: #f0fdf4; color: #0f766e; border: 1px solid #ccfbf1; text-decoration: none;">
                    <i class="bi bi-wallet2 me-1"></i> Pay
                  </a>

                  <a href="{{ route('recepit-vochers') }}" class="btn btn-xs rounded-2 fw-semibold" title="Create Customer / Account Receipt Voucher" style="font-size:0.72rem; padding: 2px 7px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; text-decoration: none;">
                    <i class="bi bi-receipt me-1"></i> Rec
                  </a>

                  <button class="btn btn-xs btn-outline-primary rounded-2 btn-edit-account"
                    style="font-size:0.72rem; padding: 2px 7px;"
                    data-bs-toggle="modal"
                    data-bs-target="#addAccountModal"
                    data-id="{{ $account->id }}"
                    data-head="{{ $account->head_id }}"
                    data-code="{{ $account->account_code }}"
                    data-title="{{ $account->title }}"
                    data-balance="{{ $account->opening_balance }}"
                    data-status="{{ $account->status }}">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </button>

                  <button class="btn btn-xs btn-outline-danger rounded-2 btn-delete-account"
                    style="font-size:0.72rem; padding: 2px 7px;"
                    data-id="{{ $account->id }}">
                    <i class="bi bi-trash me-1"></i> Delete
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- ═══════ MOBILE / TABLET PREMIUM CARDS (rendered by JS from DataTable rows) ═══════ --}}
      <div id="coaCards" class="coac-cards"></div>
    </div>

  </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade coa-modal" id="addAccountModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('coa.account.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="accountModalTitle"><i class="bi bi-plus-circle me-1 text-primary"></i> Add New Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <input type="hidden" name="account_id" id="account_id">
      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label fw-bold text-dark small text-uppercase">Select Head</label>
          <select name="head_id" class="coa-input" required>
            <option value="">Select Head</option>
            @foreach($heads as $head)
            <option value="{{ $head->id }}">{{ $head->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold text-dark small text-uppercase">Account Code</label>
          <input type="text" name="account_code" class="coa-input" placeholder="e.g. ACC-101" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold text-dark small text-uppercase">Account Title</label>
          <input type="text" name="title" class="coa-input" placeholder="e.g. Utility Bills" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold text-dark small text-uppercase">Opening Balance</label>
          <input type="number" step="0.01" name="opening_balance" class="coa-input" value="0.00">
        </div>
        <div class="form-check form-switch pt-2">
          <input class="form-check-input" name="status" type="checkbox" value="on" id="statusSwitch" checked>
          <label class="form-check-label fw-bold text-dark" for="statusSwitch">Active Account</label>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="coa-btn coa-btn-primary" id="accountSubmitBtn">
          Add Account
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Add Head Modal -->
<div class="modal fade coa-modal" id="addHeadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('coa.head.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-folder-plus me-1 text-primary"></i> Add Expense Head</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label fw-bold text-dark small text-uppercase">Head Name</label>
          <input type="text" name="name" class="coa-input" placeholder="e.g. Operational Expenses" required>
        </div>
      </div>
      <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="coa-btn coa-btn-primary">Add Head</button>
      </div>
    </form>
  </div>
</div>

<form id="deleteAccountForm" method="POST">
  @csrf
  @method('DELETE')
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-delete-account');
    if (!btn) return;
    var accountId = btn.dataset.id;
    var form = document.getElementById('deleteAccountForm');
    form.action = '/coa/account/' + accountId;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This account will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
<script>
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-edit-account');
    if (!btn) return;
    var modal = document.getElementById('addAccountModal');

    document.getElementById('accountModalTitle').innerText = 'Edit Account';
    document.getElementById('accountSubmitBtn').innerText = 'Update Account';

    document.getElementById('account_id').value = btn.dataset.id;
    modal.querySelector('[name="head_id"]').value = btn.dataset.head;
    modal.querySelector('[name="account_code"]').value = btn.dataset.code;
    modal.querySelector('[name="title"]').value = btn.dataset.title;
    modal.querySelector('[name="opening_balance"]').value = btn.dataset.balance;
    modal.querySelector('[name="status"]').checked = String(btn.dataset.status) === '1';
});

document.addEventListener('hidden.bs.modal', function(e) {
    if (e.target && e.target.id === 'addAccountModal') {
        document.getElementById('accountModalTitle').innerText = 'Add New Account';
        document.getElementById('accountSubmitBtn').innerText = 'Add Account';
        document.getElementById('addAccountModal').querySelector('form').reset();
        document.getElementById('account_id').value = '';
    }
});

$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#productTable')) {
        $('#productTable').DataTable().destroy();
    }
    var table = $('#productTable').DataTable({
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        order: [
            [1, 'desc']
        ],
        columnDefs: [{
            targets: 0,
            orderable: false
        }],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search accounts..."
        },
        drawCallback: function () {
            if (window.matchMedia('(max-width: 991.98px)').matches) renderCoaCards(this);
        }
    });

    window.setTimeout(function () {
        if (window.matchMedia('(max-width: 991.98px)').matches) renderCoaCards(table);
    }, 0);

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    function renderCoaCards(dt) {
        if (!dt || !dt.rows) return;
        var rows = dt.rows({ page: 'current' }).nodes().toArray();
        var $wrap = $('#coaCards');
        if (!rows.length) {
            $wrap.html('<div class="coac-empty"><i class="bi bi-inbox"></i><span>No accounts found.</span></div>');
            return;
        }
        var html = '';
        rows.forEach(function (tr) {
            var tds = $(tr).children('td');
            var sr = $(tds[0]).text().trim();
            var code = $(tds[1]).text().trim();
            var head = $(tds[2]).text().trim();
            var title = $(tds[3]).text().trim();
            var bal = $(tds[4]).text().trim();
            var statusText = $(tds[5]).text().trim();
            var isActive = /active/i.test(statusText);
            var actions = $(tds[6]).html() || '';

            html += '<div class="coac-card">'
                + '<div class="coac-head">'
                + '<div class="coac-head-l">'
                + '<span class="coac-title">' + esc(title) + '</span>'
                + '<span class="coac-sub"><i class="bi bi-hash"></i>' + esc(code) + '</span>'
                + '</div>'
                + '<span class="coac-status ' + (isActive ? 'active' : 'inactive') + '"><i class="bi bi-' + (isActive ? 'check-circle' : 'x-circle') + '"></i>' + esc(statusText) + '</span>'
                + '</div>'
                + '<div class="coac-meta">'
                + '<span><i class="bi bi-diagram-2"></i>' + esc(head) + '</span>'
                + '<span><i class="bi bi-hash"></i>#' + esc(sr) + '</span>'
                + '</div>'
                + '<div class="coac-balance"><span>Closing Balance</span><b>' + esc(bal) + '</b></div>'
                + '<div class="coac-actions">' + actions + '</div>'
                + '</div>';
        });
        $wrap.html(html);
    }

    $(window).on('resize.coac', function () {
        if (window.matchMedia('(max-width: 991.98px)').matches) renderCoaCards(table);
    });

    // Prevent aria-hidden on focused element — move focus before modal closes
    $('.modal').on('hide.bs.modal', function() {
        $(document.body).focus();
    });
});
</script>
@endsection