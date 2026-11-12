@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --al-bg: #f8fafc;
  --al-surface: #ffffff;
  --al-border: #e2e8f0;
  --al-border-lt: #f1f5f9;
  --al-text: #0f172a;
  --al-text-sec: #475569;
  --al-text-muted: #64748b;
  --al-primary: #0f766e;
  --al-radius: 16px;
  --al-radius-sm: 10px;
  --al-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --al-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.al-page * {
  font-family: var(--al-font);
}

.al-page {
  background-color: var(--al-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

.al-hero {
  position: relative;
  background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
  border-radius: var(--al-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.25);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.al-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 12% 88%, rgba(16, 185, 129, 0.2) 0%, transparent 55%),
    radial-gradient(circle at 88% 12%, rgba(59, 130, 246, 0.18) 0%, transparent 50%);
  pointer-events: none;
}

.al-hero > * {
  position: relative;
  z-index: 1;
}

.al-hero-left {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}

.al-hero-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #5eead4;
  font-size: 1.35rem;
}

.al-hero-title h2 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.al-hero-sub {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.3rem;
}

.al-pill {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #e2e8f0;
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.al-pill-live {
  background: rgba(16, 185, 129, 0.2);
  border-color: rgba(16, 185, 129, 0.45);
  color: #6ee7b7;
}

.al-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  border-radius: var(--al-radius-sm);
  font-size: 0.82rem;
  font-weight: 600;
  border: 1px solid transparent;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.18s ease;
}

.al-btn-glass {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.24);
  color: #ffffff !important;
}

.al-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
}

.al-btn-primary {
  background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
  color: #ffffff !important;
}

.al-btn-primary:hover {
  filter: brightness(1.06);
}

.al-card {
  background: var(--al-surface);
  border: 1px solid var(--al-border);
  border-radius: var(--al-radius);
  box-shadow: var(--al-shadow);
}

.al-filter-card {
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;
}

.al-label {
  display: block;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--al-text-muted);
  margin-bottom: 0.3rem;
}

.al-input {
  width: 100%;
  border: 1px solid var(--al-border);
  border-radius: 9px;
  padding: 0.48rem 0.75rem;
  font-size: 0.84rem;
  color: var(--al-text);
  background: #fff;
}

.al-input:focus {
  outline: none;
  border-color: #0d9488;
  box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}

.al-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.25rem;
}

@media (max-width: 991px) {
  .al-kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.65rem;
  }
}

.al-kpi {
  background: var(--al-surface);
  border: 1px solid var(--al-border);
  border-radius: 14px;
  padding: 1rem 1.2rem;
  box-shadow: var(--al-shadow);
  position: relative;
  overflow: hidden;
}

.al-kpi::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
}

.al-kpi.opening::before { background: #3b82f6; }
.al-kpi.in::before      { background: #10b981; }
.al-kpi.out::before     { background: #ef4444; }
.al-kpi.closing::before { background: #0f766e; }

.al-kpi-lbl {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--al-text-muted);
  margin-bottom: 0.35rem;
}

.al-kpi-val {
  font-size: 1.35rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.al-kpi-sub {
  font-size: 0.74rem;
  color: var(--al-text-muted);
  margin-top: 0.2rem;
}

.al-table-card {
  overflow: hidden;
}

.al-table-head {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--al-border);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

.al-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.84rem;
}

.al-table thead th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 0.75rem 0.9rem;
  border-bottom: 1px solid var(--al-border);
  white-space: nowrap;
}

.al-table tbody td {
  padding: 0.72rem 0.9rem;
  border-bottom: 1px solid var(--al-border-lt);
  vertical-align: middle;
}

.al-table tbody tr:hover {
  background: #f8fafc;
}

.al-table tfoot td {
  background: #f1f5f9;
  font-weight: 800;
  padding: 0.8rem 0.9rem;
  border-top: 2px solid var(--al-border);
}

.badge-in {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
  font-weight: 700;
  font-size: 0.75rem;
  padding: 0.22rem 0.55rem;
  border-radius: 6px;
}

.badge-out {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
  font-weight: 700;
  font-size: 0.75rem;
  padding: 0.22rem 0.55rem;
  border-radius: 6px;
}

.badge-ref {
  background: #f1f5f9;
  color: #0f172a;
  border: 1px solid #e2e8f0;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 0.73rem;
  font-weight: 700;
  padding: 0.18rem 0.48rem;
  border-radius: 5px;
}

.al-print-header {
  display: none;
}

@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .al-page { overflow-x: hidden !important; max-width: 100% !important; padding: 0 !important; }
  .al-page .container-fluid { padding: 0.6rem 0.65rem 1.25rem !important; }

  .al-hero {
    padding: 0.85rem !important;
    border-radius: 14px !important;
    margin-bottom: 0.75rem !important;
    flex-direction: column;
    align-items: stretch !important;
    gap: 0.65rem !important;
  }
  .al-hero-left { gap: 0.6rem !important; align-items: flex-start !important; }
  .al-hero-icon { width: 38px !important; height: 38px !important; font-size: 1.05rem !important; border-radius: 10px !important; flex-shrink: 0; }
  .al-hero-title h2 { font-size: 1.02rem !important; margin-bottom: 0.25rem !important; }
  .al-hero-sub { display: flex; flex-wrap: wrap; gap: 0.3rem; }
  .al-pill { font-size: 0.66rem !important; padding: 0.16rem 0.45rem !important; }

  .al-hero .al-no-print {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.45rem !important;
    width: 100%;
  }
  .al-hero .al-no-print .al-btn {
    width: 100%;
    min-height: 34px !important;
    padding: 0.32rem 0.5rem !important;
    font-size: 0.74rem !important;
    justify-content: center;
    border-radius: 9px !important;
  }
  .al-hero .al-no-print .al-btn:last-child:nth-child(odd) {
    grid-column: 1 / -1;
  }

  .al-filter-card { padding: 0.7rem 0.75rem !important; border-radius: 12px !important; margin-bottom: 0.75rem !important; }
  .al-label { font-size: 0.62rem !important; margin-bottom: 0.2rem !important; }
  .al-input { min-height: 36px !important; height: 36px !important; font-size: 0.78rem !important; padding: 0.3rem 0.55rem !important; border-radius: 8px !important; }
  .al-filter-card .al-btn { min-height: 36px !important; height: 36px !important; padding: 0.3rem 0.55rem !important; font-size: 0.76rem !important; border-radius: 8px !important; }

  .al-kpi-grid { gap: 0.5rem !important; margin-bottom: 0.75rem !important; }
  .al-kpi { padding: 0.65rem 0.75rem !important; border-radius: 11px !important; }
  .al-kpi-lbl { font-size: 0.58rem !important; margin-bottom: 0.2rem !important; }
  .al-kpi-val { font-size: 0.94rem !important; word-break: break-word; }
  .al-kpi-sub { font-size: 0.62rem !important; }

  .al-table-card { border-radius: 12px !important; background: transparent !important; border: none !important; box-shadow: none !important; }
  .al-table-head {
    background: var(--al-surface);
    border: 1px solid var(--al-border);
    border-radius: 12px;
    padding: 0.65rem 0.8rem !important;
    margin-bottom: 0.55rem;
  }
  .al-table-head h5, .al-table-head h6 { font-size: 0.88rem !important; margin: 0 !important; }
  .al-table-card .table-responsive { overflow: visible !important; }

  .al-table, .al-table thead, .al-table tbody, .al-table tfoot { display: block !important; width: 100% !important; }
  .al-table thead { display: none !important; }

  .al-table tbody tr {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.35rem 0.55rem;
    background: var(--al-surface);
    border: 1px solid var(--al-border);
    border-radius: 12px;
    padding: 0.65rem 0.75rem;
    margin-bottom: 0.55rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }
  .al-table tbody td {
    display: flex !important;
    flex-direction: column;
    border: none !important;
    padding: 0 !important;
    text-align: left !important;
    white-space: normal !important;
    word-break: break-word;
    min-width: 0;
    font-size: 0.74rem;
  }
  .al-table tbody td:nth-child(1) {
    order: 0; grid-column: 1; justify-self: start; align-self: center;
    background: #eef4ff; color: #2b5bb3 !important; border-radius: 6px;
    padding: 0.1rem 0.45rem !important; font-size: 0.64rem; width: fit-content;
    flex-direction: row;
  }
  .al-table tbody td:nth-child(1)::before { content: "#"; font-weight: 800; }
  .al-table tbody td:nth-child(2) {
    order: 0; grid-column: 2; justify-self: end; text-align: right !important; align-items: flex-end; font-size: 0.7rem;
  }
  .al-table tbody td:nth-child(3) { order: 1; grid-column: 1 / -1; }
  .al-table tbody td:nth-child(4) { order: 2; grid-column: 1 / -1; background: #f8fafc; padding: 0.3rem 0.45rem !important; border-radius: 7px; font-size: 0.73rem; }
  .al-table tbody td:nth-child(5) { order: 3; }
  .al-table tbody td:nth-child(5)::before { content: "Before"; font-size: 0.56rem; font-weight: 700; text-transform: uppercase; color: var(--al-text-muted); }
  .al-table tbody td:nth-child(8) { order: 4; align-items: flex-end; text-align: right !important; }
  .al-table tbody td:nth-child(8)::before { content: "After Balance"; font-size: 0.56rem; font-weight: 700; text-transform: uppercase; color: var(--al-text-muted); }
  .al-table tbody td:nth-child(6) { order: 5; border-top: 1px dashed #e2e8f0 !important; padding-top: 0.35rem !important; }
  .al-table tbody td:nth-child(6)::before { content: "In (+)"; font-size: 0.56rem; font-weight: 700; text-transform: uppercase; color: var(--al-text-muted); }
  .al-table tbody td:nth-child(7) { order: 6; align-items: flex-end; text-align: right !important; border-top: 1px dashed #e2e8f0 !important; padding-top: 0.35rem !important; }
  .al-table tbody td:nth-child(7)::before { content: "Out (-)"; font-size: 0.56rem; font-weight: 700; text-transform: uppercase; color: var(--al-text-muted); }

  .al-table tbody td[colspan] {
    grid-column: 1 / -1 !important;
    align-items: center !important;
    text-align: center !important;
    padding: 1.5rem 0.5rem !important;
  }

  .al-table tfoot tr {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.35rem 0.55rem;
    background: #f1f5f9;
    border: 1px solid var(--al-border);
    border-radius: 12px;
    padding: 0.65rem 0.75rem;
  }
  .al-table tfoot td {
    display: flex !important;
    flex-direction: column;
    border: none !important;
    padding: 0 !important;
    text-align: left !important;
    font-size: 0.76rem;
    font-weight: 700;
  }
  .al-table tfoot td:nth-child(1) { grid-column: 1 / -1; font-size: 0.78rem; border-bottom: 1px solid #cbd5e1 !important; padding-bottom: 0.3rem !important; }
  .al-table tfoot td:nth-child(2)::before { content: "Opening"; font-size: 0.56rem; color: var(--al-text-muted); text-transform: uppercase; }
  .al-table tfoot td:nth-child(3)::before { content: "Total In"; font-size: 0.56rem; color: var(--al-text-muted); text-transform: uppercase; }
  .al-table tfoot td:nth-child(4)::before { content: "Total Out"; font-size: 0.56rem; color: var(--al-text-muted); text-transform: uppercase; }
  .al-table tfoot td:nth-child(5)::before { content: "Closing"; font-size: 0.56rem; color: var(--al-text-muted); text-transform: uppercase; }
}

@media print {
  .al-no-print,
  .navbar,
  .horizontal-menu,
  .sidebar,
  footer {
    display: none !important;
  }
  .al-page {
    background: #ffffff !important;
    padding: 0 !important;
  }
  .al-hero {
    background: #0f172a !important;
    color: #fff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .al-print-header {
    display: block;
    margin-bottom: 12px;
  }
  .al-card {
    box-shadow: none !important;
  }
}
</style>

<div class="main-content al-page">
  <div class="main-content-inner">
    <div class="container-fluid py-3">

      {{-- Hero Header --}}
      <div class="al-hero">
        <div class="al-hero-left">
          <div class="al-hero-icon">
            <i class="bi bi-journal-check"></i>
          </div>
          <div class="al-hero-title">
            <h2>Account Ledger / Statement</h2>
            @if($account)
              <div class="al-hero-sub">
                <span class="al-pill">
                  <i class="bi bi-bank me-1"></i>{{ $account->title }}
                </span>
                <span class="al-pill font-monospace">{{ $account->account_code }}</span>
                <span class="al-pill">{{ $account->head->name ?? 'N/A' }}</span>
                <span class="al-pill al-pill-live">
                  Live Balance: PKR {{ number_format((float)($account->opening_balance ?? 0), 2) }}
                </span>
              </div>
            @endif
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap al-no-print">
          <button type="button" onclick="window.print()" class="al-btn al-btn-glass">
            <i class="bi bi-printer"></i> Print / PDF
          </button>
          <a href="{{ route('cashbook') }}" class="al-btn al-btn-glass">
            <i class="bi bi-book"></i> CashBook
          </a>
          <a href="{{ route('view_all') }}" class="al-btn al-btn-glass">
            <i class="bi bi-arrow-left"></i> Chart of Accounts
          </a>
        </div>
      </div>

      {{-- Filters --}}
      <div class="al-card al-filter-card al-no-print">
        <form method="GET" action="{{ route('coa.account.ledger') }}" id="accountLedgerFilterForm" class="row g-2 align-items-end">
          <div class="col-md-4 col-lg-4">
            <label class="al-label"><i class="bi bi-wallet2 me-1"></i>Select Account</label>
            <select name="account_id" class="al-input" onchange="document.getElementById('accountLedgerFilterForm').submit()">
              @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ ($account && (int)$account->id === (int)$acc->id) ? 'selected' : '' }}>
                  {{ $acc->title }} ({{ $acc->account_code }}) — PKR {{ number_format((float)($acc->opening_balance ?? 0), 2) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="al-label">From Date</label>
            <input type="date" name="start_date" class="al-input" value="{{ $startDate }}">
          </div>

          <div class="col-6 col-md-2">
            <label class="al-label">To Date</label>
            <input type="date" name="end_date" class="al-input" value="{{ $endDate }}">
          </div>

          <div class="col-6 col-md-2">
            <label class="al-label">Flow Type</label>
            <select name="flow" class="al-input">
              <option value="all" {{ $flow === 'all' ? 'selected' : '' }}>All (In & Out)</option>
              <option value="in" {{ $flow === 'in' ? 'selected' : '' }}>Money In (+) Only</option>
              <option value="out" {{ $flow === 'out' ? 'selected' : '' }}>Money Out (-) Only</option>
            </select>
          </div>

          <div class="col-6 col-md-2 d-flex gap-2">
            <button type="submit" class="al-btn al-btn-primary w-100 justify-content-center">
              <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('coa.account.ledger', $account ? $account->id : null) }}" class="al-btn btn btn-outline-secondary" title="Reset Filters">
              <i class="bi bi-arrow-counterclockwise"></i>
            </a>
          </div>

          <div class="col-md-4 mt-2">
            <input type="text" name="search" class="al-input" placeholder="Search by Invoice / Voucher # or description..." value="{{ $search }}">
          </div>
        </form>
      </div>

      {{-- KPI Summary Cards --}}
      <div class="al-kpi-grid">
        <div class="al-kpi opening">
          <div class="al-kpi-lbl"><i class="bi bi-door-open text-primary me-1"></i> Opening Balance (Pehle)</div>
          <div class="al-kpi-val text-primary">PKR {{ number_format($periodOpeningBalance, 2) }}</div>
          <div class="al-kpi-sub">{{ $startDate ? 'Balance before ' . $startDate : 'Starting from 0.00' }}</div>
        </div>

        <div class="al-kpi in">
          <div class="al-kpi-lbl"><i class="bi bi-arrow-down-circle text-success me-1"></i> Total Payment In (+)</div>
          <div class="al-kpi-val text-success">+ PKR {{ number_format($totalIn, 2) }}</div>
          <div class="al-kpi-sub">Total credited / received</div>
        </div>

        <div class="al-kpi out">
          <div class="al-kpi-lbl"><i class="bi bi-arrow-up-circle text-danger me-1"></i> Total Payment Out (-)</div>
          <div class="al-kpi-val text-danger">- PKR {{ number_format($totalOut, 2) }}</div>
          <div class="al-kpi-sub">Total debited / paid</div>
        </div>

        <div class="al-kpi closing">
          <div class="al-kpi-lbl"><i class="bi bi-safe2 text-teal me-1"></i> Closing Balance (Baad)</div>
          <div class="al-kpi-val" style="color: {{ $periodClosingBalance >= 0 ? '#0f766e' : '#dc2626' }};">
            PKR {{ number_format($periodClosingBalance, 2) }}
          </div>
          <div class="al-kpi-sub">{{ $endDate ? 'Balance up to ' . $endDate : 'Current live closing balance' }}</div>
        </div>
      </div>

      {{-- Ledger Table --}}
      <div class="al-card al-table-card">
        <div class="al-table-head">
          <div>
            <h5 class="mb-0 fw-bold text-dark">
              <i class="bi bi-list-columns-reverse me-1 text-teal"></i>
              Transaction History & Running Balance
            </h5>
            <small class="text-muted">
              Har transaction par pehle balance, payment in/out aur naya balance step-by-step show ho raha hai
            </small>
          </div>
          <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
            {{ $entries->count() }} Transaction(s)
          </span>
        </div>

        <div class="table-responsive">
          <table class="al-table">
            <thead>
              <tr>
                <th width="4%">#</th>
                <th width="14%">Date & Time</th>
                <th width="15%">Type & Ref #</th>
                <th width="25%">Description / Remarks</th>
                <th width="11%" class="text-end">Before (Pehle)</th>
                <th width="10%" class="text-end text-success">Payment In (+)</th>
                <th width="10%" class="text-end text-danger">Payment Out (-)</th>
                <th width="11%" class="text-end">After Balance</th>
              </tr>
            </thead>
            <tbody>
              @forelse($entries as $idx => $row)
                <tr>
                  <td class="text-muted fw-semibold">{{ $idx + 1 }}</td>
                  <td class="text-nowrap">
                    <div class="fw-semibold text-dark">{{ $row->transaction_date ? $row->transaction_date->format('d M Y') : '-' }}</div>
                    <small class="text-muted">{{ $row->transaction_date ? $row->transaction_date->format('h:i A') : '' }}</small>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark" style="font-size:0.8rem;">{{ $row->reference_type ?? 'Transaction' }}</div>
                    @if($row->reference_no)
                      <span class="badge-ref">{{ $row->reference_no }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="text-dark">{{ $row->description ?? '-' }}</div>
                    @if($row->user)
                      <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $row->user->name }}</small>
                    @endif
                  </td>
                  <td class="text-end text-secondary fw-semibold text-nowrap">
                    PKR {{ number_format((float)$row->previous_balance, 2) }}
                  </td>
                  <td class="text-end text-nowrap">
                    @if((float)$row->credit > 0)
                      <span class="badge-in">+ {{ number_format((float)$row->credit, 2) }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-end text-nowrap">
                    @if((float)$row->debit > 0)
                      <span class="badge-out">- {{ number_format((float)$row->debit, 2) }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-end fw-bold text-nowrap" style="color: {{ (float)$row->balance >= 0 ? '#0f766e' : '#dc2626' }};">
                    PKR {{ number_format((float)$row->balance, 2) }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Is account ya selected date filter mein abhi koi ledger entry mojood nahi hai.
                  </td>
                </tr>
              @endforelse
            </tbody>
            @if($entries->count() > 0)
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end">Period Summary:</td>
                  <td class="text-end text-secondary">PKR {{ number_format($periodOpeningBalance, 2) }}</td>
                  <td class="text-end text-success">+ PKR {{ number_format($totalIn, 2) }}</td>
                  <td class="text-end text-danger">- PKR {{ number_format($totalOut, 2) }}</td>
                  <td class="text-end" style="color:#0f766e;">PKR {{ number_format($periodClosingBalance, 2) }}</td>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
