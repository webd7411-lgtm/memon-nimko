@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --cs-bg: #f8fafc;
  --cs-surface: #ffffff;
  --cs-border: #e2e8f0;
  --cs-border-lt: #f1f5f9;
  --cs-text: #0f172a;
  --cs-text-sec: #475569;
  --cs-text-muted: #64748b;
  --cs-primary: #2563eb;
  --cs-radius: 16px;
  --cs-radius-sm: 10px;
  --cs-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --cs-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --cs-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.cs-page * {
  font-family: var(--cs-font);
}

.cs-page {
  background-color: var(--cs-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.cs-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--cs-radius);
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

.cs-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.cs-hero > * {
  position: relative;
  z-index: 1;
}

.cs-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.cs-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.cs-hero-icon {
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

.cs-hero-badge {
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

.cs-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.cs-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--cs-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.cs-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.cs-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.cs-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.cs-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.cs-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .cs-kpi-grid { grid-template-columns: 1fr; }
}

.cs-kpi-card {
  background: var(--cs-surface);
  border: 1px solid var(--cs-border);
  border-radius: var(--cs-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--cs-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.cs-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--cs-shadow-lg);
}

.cs-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--cs-text-muted);
}
.cs-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--cs-text);
  letter-spacing: -0.02em;
}

.cs-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.cs-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.cs-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.cs-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.cs-table-card {
  background: var(--cs-surface);
  border: 1px solid var(--cs-border);
  border-radius: var(--cs-radius);
  box-shadow: var(--cs-shadow-lg);
  overflow: hidden;
}

.cs-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--cs-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.cs-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--cs-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cs-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.cs-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.cs-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--cs-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--cs-border);
  border-top: none;
  white-space: nowrap;
}

.cs-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--cs-border-lt);
  font-size: 0.88rem;
  color: var(--cs-text-sec);
}

.cs-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* ═══════ FULL MOBILE RESPONSIVE — CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .cs-page { overflow-x: hidden !important; }

  .cs-hero { padding: 1rem 1rem !important; gap: .5rem; }
  .cs-hero-title h2 { font-size: 1.05rem !important; }
  .cs-hero-actions { width: 100%; display: grid !important; grid-template-columns: 1fr 1fr; gap: .4rem; }
  .cs-hero-actions .cs-btn { justify-content: center; width: 100%; min-height: 36px; font-size: .75rem; padding: .45rem .6rem; }
  .cs-table-header { padding: .9rem 1rem; }
  .cs-table-title { font-size: .95rem; }

  /* Kill scroll wrappers — higher specificity */
  .cs-page .cs-table-wrapper,
  .cs-page .dataTables_wrapper,
  .cs-page .dataTables_scrollBody,
  .cs-page .dataTables_scrollHead,
  .cs-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    padding: 0 !important;
  }

  .cs-page .cs-table,
  .cs-page .cs-table.dataTable {
    display: block !important;
    width: 100% !important;
    font-size: .72rem !important;
  }
  .cs-page .cs-table thead { display: none !important; }
  .cs-page .cs-table tbody { display: block !important; }
  .cs-page .cs-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .55rem;
    align-items: start;
    background: var(--cs-surface);
    border: 1px solid var(--cs-border) !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05), 0 2px 8px rgba(15,23,42,.05);
    padding: .55rem .65rem !important;
    margin-bottom: .55rem;
    overflow: hidden !important;
  }
  .cs-page .cs-table tbody tr:hover td { background: transparent !important; }
  .cs-page .cs-table tbody td {
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
    font-size: .72rem;
    line-height: 1.35;
    text-align: left !important;
  }
  .cs-page .cs-table tbody td::before {
    content: attr(data-label) ":";
    font-size: .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--cs-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .cs-page .cs-table tbody td:nth-child(1) { order: 0; justify-self: start; }
  .cs-page .cs-table tbody td:nth-child(1)::before { content: none; }
  .cs-page .cs-table tbody td:nth-child(1) {
    background: #eff6ff; color: #2563eb !important; border-radius: 6px;
    padding: .05rem .4rem !important; font-weight: 700 !important; font-size: .62rem;
  }
  .cs-page .cs-table tbody td:nth-child(4) { order: 1; grid-column: 1 / -1; }
  .cs-page .cs-table tbody td:nth-child(4)::before { content: none; }
  .cs-page .cs-table tbody td:nth-child(4) { font-weight: 600; font-size: .8rem; padding-top: 2px; }
  .cs-page .cs-table tbody td:nth-child(2) { order: 2; }
  .cs-page .cs-table tbody td:nth-child(3) { order: 3; }
  .cs-page .cs-table tbody td:nth-child(5) { order: 4; }
  .cs-page .cs-table tbody td:nth-child(6) { order: 5; grid-column: 1 / -1; }
  .cs-page .cs-table tbody td:nth-child(7) { order: 6; }
  .cs-page .cs-table tbody td:nth-child(8) { order: 7; grid-column: 1 / -1; font-size: .8rem !important; }
  .cs-page .cs-table tbody td:nth-child(9) { order: 8; }
  .cs-page .cs-table tbody td:nth-child(10) { order: 9; grid-column: 1 / -1; }
  .cs-page .cs-table tbody td:nth-child(10)::before { content: none; }
  .cs-page .cs-table tbody td:nth-child(10) .d-flex { display: grid !important; grid-template-columns: repeat(3, 1fr); gap: .4rem; width: 100%; }
  .cs-page .cs-table tbody td:nth-child(10) .btn { width: 100%; min-height: 34px; display: inline-flex; align-items: center; justify-content: center; }

  .cs-page .dataTables_filter, .cs-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .cs-page .dataTables_filter input, .cs-page .dataTables_length select {
    width: 100% !important; min-height: 36px; font-size: .78rem; border-radius: 8px;
    padding: .3rem .6rem; border: 1.5px solid var(--cs-border); margin-top: 4px;
  }
  .cs-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .cs-page .dataTables_paginate { text-align: center !important; padding: .5rem 0; }
  .cs-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 28px !important; height: 28px !important;
    padding: 0 .35rem !important; font-size: .68rem !important; margin: 0 1px !important;
    border-radius: 6px !important;
  }
}
</style>

<div class="cs-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="cs-hero">
      <div class="cs-hero-title">
        <div class="cs-hero-icon">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <h2>Customer Directory</h2>
          <span class="cs-hero-badge">Accounts & Receivables Ledger</span>
        </div>
      </div>

      <div class="cs-hero-actions">
        <a href="{{ route('customers.create') }}" class="cs-btn cs-btn-primary">
          <i class="bi bi-person-plus-fill me-1"></i> Add Customer
        </a>

        <a href="{{ route('customers.ledger') }}" class="cs-btn cs-btn-glass">
          <i class="bi bi-journal-text me-1"></i> Ledger
        </a>

        <a href="{{ route('customer.payments') }}" class="cs-btn cs-btn-glass">
          <i class="bi bi-cash-stack me-1 text-success"></i> Payments
        </a>

        <a href="{{ route('customers.inactive') }}" class="cs-btn cs-btn-glass">
          <i class="bi bi-person-slash me-1"></i> Inactive
        </a>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalCustomersCount = count($customers);
      $activeCustomersCount = $customers->where('status', 'active')->count();
    @endphp

    <div class="cs-kpi-grid">
      <div class="cs-kpi-card">
        <div class="cs-kpi-info">
          <p>Total Customers</p>
          <h3>{{ number_format($totalCustomersCount) }}</h3>
        </div>
        <div class="cs-kpi-icon blue">
          <i class="bi bi-people"></i>
        </div>
      </div>

      <div class="cs-kpi-card">
        <div class="cs-kpi-info">
          <p>Active Accounts</p>
          <h3>{{ number_format($activeCustomersCount) }}</h3>
        </div>
        <div class="cs-kpi-icon green">
          <i class="bi bi-person-check-fill"></i>
        </div>
      </div>

      <div class="cs-kpi-card">
        <div class="cs-kpi-info">
          <p>Total Receivables</p>
          <h3>Rs. {{ number_format($totalClosingBalance ?? 0, 2) }}</h3>
        </div>
        <div class="cs-kpi-icon amber">
          <i class="bi bi-wallet2"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="cs-table-card">
      <div class="cs-table-header">
        <h3 class="cs-table-title">
          <i class="bi bi-person-badge text-primary me-1"></i> Customer Accounts
        </h3>
      </div>

      <div class="cs-table-wrapper">
        <table id="customerTable" class="table cs-table align-middle mb-0" style="width:100%">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="10%">Date</th>
              <th width="8%">ID</th>
              <th width="20%">Customer Name</th>
              <th width="10%">Type</th>
              <th width="12%">Category</th>
              <th width="12%">Mobile</th>
              <th width="13%" class="text-end">Closing Balance</th>
              <th width="5%" class="text-center">Status</th>
              <th width="5%" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($customers as $customer)
            <tr>
              <td class="fw-bold" data-label="#">#{{ $loop->iteration }}</td>
              <td class="text-muted" data-label="Date">{{ $customer->created_at->format('d M, Y') }}</td>
              <td data-label="ID"><span class="badge bg-light text-dark border">{{ $customer->customer_id }}</span></td>
              <td data-label="Customer">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width:32px; height:32px; font-size:0.8rem;">
                    {{ strtoupper(substr($customer->customer_name, 0, 1)) }}
                  </div>
                  <span class="fw-bold text-dark">{{ $customer->customer_name }}</span>
                </div>
              </td>
              <td data-label="Type"><span class="badge bg-secondary-subtle text-secondary border">{{ $customer->customer_type }}</span></td>
              <td data-label="Category">
                <span class="badge {{ $customer->customer_category == 'Wholesaler' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} fw-semibold">
                  {{ $customer->customer_category ?? '-' }}
                </span>
              </td>
              <td data-label="Mobile">
                @if($customer->mobile)
                  <i class="bi bi-telephone me-1 text-secondary"></i>{{ $customer->mobile }}
                @else
                  —
                @endif
              </td>
              <td class="text-end fw-bold {{ $customer->closing_balance > 0 ? 'text-success' : 'text-danger' }}" data-label="Balance">
                Rs. {{ number_format($customer->closing_balance, 2) }}
              </td>
              <td class="text-center" data-label="Status">
                @if($customer->status === 'active')
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                @else
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                @endif
              </td>
              <td class="text-center" data-label="Actions">
                <div class="d-flex align-items-center justify-content-center gap-1">
                  <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="{{ route('customers.toggleStatus', $customer->id) }}" class="btn btn-sm btn-outline-warning rounded-2" title="Toggle Status">
                    <i class="bi {{ $customer->status === 'active' ? 'bi-toggle-on text-success fs-6' : 'bi-toggle-off text-muted fs-6' }}"></i>
                  </a>
                  <a href="{{ route('customers.destroy', $customer->id) }}" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Are you sure?')" title="Delete">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </td>
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
<script>
$(document).ready(function() {
  if ($.fn.DataTable.isDataTable('#customerTable')) {
    $('#customerTable').DataTable().destroy();
  }
  $('#customerTable').DataTable({
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    order: [],
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search customers..."
    }
  });
});
</script>
@endsection