@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --bk-bg: #f8fafc;
  --bk-surface: #ffffff;
  --bk-border: #e2e8f0;
  --bk-border-lt: #f1f5f9;
  --bk-text: #0f172a;
  --bk-text-sec: #475569;
  --bk-text-muted: #64748b;
  --bk-primary: #2563eb;
  --bk-radius: 16px;
  --bk-radius-sm: 10px;
  --bk-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --bk-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --bk-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.bk-page * {
  font-family: var(--bk-font);
}

.bk-page {
  background-color: var(--bk-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.bk-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--bk-radius);
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

.bk-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(245, 158, 11, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.bk-hero > * {
  position: relative;
  z-index: 1;
}

.bk-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.bk-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.bk-hero-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #f59e0b;
  font-size: 1.4rem;
}

.bk-hero-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #fde68a;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.bk-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--bk-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.bk-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.bk-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.bk-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .bk-kpi-grid { grid-template-columns: 1fr; }
}

.bk-kpi-card {
  background: var(--bk-surface);
  border: 1px solid var(--bk-border);
  border-radius: var(--bk-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--bk-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bk-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--bk-shadow-lg);
}

.bk-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--bk-text-muted);
}
.bk-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--bk-text);
  letter-spacing: -0.02em;
}

.bk-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.bk-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
.bk-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.bk-kpi-icon.red { background: #fef2f2; color: #ef4444; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.bk-table-card {
  background: var(--bk-surface);
  border: 1px solid var(--bk-border);
  border-radius: var(--bk-radius);
  box-shadow: var(--bk-shadow-lg);
  overflow: hidden;
}

.bk-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--bk-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.bk-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--bk-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.bk-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.bk-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.bk-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--bk-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--bk-border);
  border-top: none;
  white-space: nowrap;
}

.bk-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--bk-border-lt);
  font-size: 0.88rem;
  color: var(--bk-text-sec);
}

.bk-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* ═══════ FULL MOBILE RESPONSIVE — PREMIUM CARD LAYOUT ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .bk-page, .bk-page * { max-width: 100%; box-sizing: border-box; }
  .bk-page { overflow-x: hidden !important; }

  .bk-hero { padding: 1.05rem 1rem !important; gap: .6rem; }
  .bk-hero-title { gap: .6rem; }
  .bk-hero-title h2 { font-size: 1.1rem !important; }
  .bk-hero-icon { width: 40px; height: 40px; font-size: 1.15rem; }
  .bk-hero-badge { font-size: .65rem; padding: .22rem .7rem; }
  .bk-btn { font-size: .8rem; padding: .5rem .9rem; }
  .bk-kpi-grid { grid-template-columns: 1fr; gap: .65rem; }
  .bk-kpi-card { padding: .9rem 1rem; }
  .bk-kpi-info h3 { font-size: 1.3rem; }
  .bk-table-header { padding: .85rem 1rem; }
  .bk-table-title { font-size: .95rem; }

  /* Kill all scroll wrappers — higher specificity */
  .bk-page .bk-table-wrapper,
  .bk-page .dataTables_wrapper,
  .bk-page .dataTables_scrollHead,
  .bk-page .dataTables_scrollBody,
  .bk-page .dataTables_scroll,
  .bk-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .bk-page .bk-table, .bk-page .bk-table.dataTable {
    display: block !important;
    width: 100% !important;
    border-collapse: collapse !important;
  }
  .bk-page .bk-table thead { display: none !important; }
  .bk-page .bk-table tbody { display: block !important; width: 100%; }

  /* ── Each booking becomes a premium card box ── */
  .bk-page .bk-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr 1fr;
    grid-auto-rows: auto;
    column-gap: .5rem;
    row-gap: .4rem;
    align-items: center;
    background: var(--bk-surface);
    border: 1px solid var(--bk-border) !important;
    border-radius: 14px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 7px 20px rgba(15,23,42,.07);
    padding: .85rem .8rem !important;
    margin-bottom: .75rem;
    overflow: hidden !important;
  }
  .bk-page .bk-table tbody tr:hover td { background: transparent !important; }

  .bk-page .bk-table tbody td {
    display: flex !important;
    flex-direction: column;
    align-items: flex-start;
    gap: 1px 5px;
    border: none !important;
    padding: .08rem 0 !important;
    min-width: 0;
    min-height: 0;
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: anywhere;
    font-size: .74rem;
    line-height: 1.4;
    text-align: left !important;
  }
  .bk-page .bk-table tbody td::before {
    content: attr(data-label);
    font-size: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--bk-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  /* Card header band */
  .bk-page .bk-table tbody td:nth-child(1) {
    grid-column: 1; grid-row: 1;
    justify-self: start;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff !important;
    border-radius: 8px;
    padding: .22rem .55rem !important;
    font-weight: 800 !important;
    font-size: .66rem;
    box-shadow: 0 3px 8px rgba(37,99,235,.35);
  }
  .bk-page .bk-table tbody td:nth-child(1)::before { content: none; }

  .bk-page .bk-table tbody td:nth-child(2) {
    grid-column: 2 / -1; grid-row: 1;
    flex-direction: row;
    align-items: center;
    font-weight: 800 !important;
    font-size: .98rem !important;
    color: var(--bk-text);
  }
  .bk-page .bk-table tbody td:nth-child(2)::before { content: none; }

  .bk-page .bk-table tbody td:nth-child(3) { grid-column: 1 / span 2; grid-row: 2; flex-direction: row; align-items: center; }
  .bk-page .bk-table tbody td:nth-child(3)::before { content: none; }
  .bk-page .bk-table tbody td:nth-child(11) { grid-column: 3 / -1; grid-row: 2; flex-direction: row; align-items: center; justify-self: end; }
  .bk-page .bk-table tbody td:nth-child(11)::before { content: none; }

  /* Products block */
  .bk-page .bk-table tbody td:nth-child(4) {
    grid-column: 1 / -1; grid-row: 3;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid var(--bk-border-lt) !important;
    border-radius: 10px;
    padding: .45rem .55rem !important;
    font-weight: 600;
    font-size: .8rem !important;
  }
  .bk-page .bk-table tbody td:nth-child(4)::before {
    margin-bottom: 2px;
    color: #2563eb;
    font-size: .52rem;
  }

  /* Qty / Price / Disc line */
  .bk-page .bk-table tbody td:nth-child(5) { grid-column: 1; grid-row: 4; }
  .bk-page .bk-table tbody td:nth-child(6) { grid-column: 2; grid-row: 4; }
  .bk-page .bk-table tbody td:nth-child(7) { grid-column: 3; grid-row: 4; }

  /* Monetary summary strip */
  .bk-page .bk-table tbody td:nth-child(8) {
    grid-column: 1; grid-row: 5;
    background: #eff6ff; border-radius: 8px; padding: .4rem .45rem !important;
  }
  .bk-page .bk-table tbody td:nth-child(9) {
    grid-column: 2; grid-row: 5;
    background: #ecfdf5; border-radius: 8px; padding: .4rem .45rem !important;
  }
  .bk-page .bk-table tbody td:nth-child(10) {
    grid-column: 3; grid-row: 5;
    background: #fef2f2; border-radius: 8px; padding: .4rem .45rem !important;
  }
  .bk-page .bk-table tbody td:nth-child(8) .fw-bold,
  .bk-page .bk-table tbody td:nth-child(9) .fw-bold,
  .bk-page .bk-table tbody td:nth-child(10) .fw-bold { font-size: .78rem !important; }

  /* Actions */
  .bk-page .bk-table tbody td:nth-child(12) {
    grid-column: 1 / -1; grid-row: 6;
    border-top: 1px dashed var(--bk-border) !important;
    padding-top: .5rem !important;
  }
  .bk-page .bk-table tbody td:nth-child(12)::before { content: none; }
  .bk-page .bk-table tbody td:nth-child(12) .d-flex {
    display: grid !important; grid-template-columns: 1fr 1fr 1fr; gap: .4rem; width: 100%;
  }
  .bk-page .bk-table tbody td:nth-child(12) .btn,
  .bk-page .bk-table tbody td:nth-child(12) form {
    width: 100% !important; min-height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .68rem; padding: .25rem .3rem; border-radius: 8px;
  }
  .bk-page .bk-table tbody td:nth-child(12) .badge.bg-dark {
    grid-column: 1 / -1; width: 100%; text-align: center; padding: .5rem !important; border-radius: 8px;
  }

  /* DataTables controls */
  .bk-page .dataTables_filter, .bk-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .bk-page .dataTables_filter input, .bk-page .dataTables_length select {
    width: 100% !important; max-width: 100%; min-height: 38px; font-size: .8rem;
    border-radius: 10px; padding: .35rem .6rem; border: 1.5px solid var(--bk-border); margin-top: 4px;
  }
  .bk-page .dataTables_length { display: none; }
  .bk-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .bk-page .dataTables_paginate { text-align: center !important; padding: .6rem 0; }
  .bk-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 30px !important; height: 30px !important;
    align-items: center; justify-content: center;
    padding: 0 .4rem !important; font-size: .7rem !important; margin: 0 1px !important;
    border-radius: 8px !important; border: none !important;
  }
}
</style>

<div class="bk-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="bk-hero">
      <div class="bk-hero-title">
        <div class="bk-hero-icon">
          <i class="bi bi-journal-bookmark-fill"></i>
        </div>
        <div>
          <h2>Advance Bookings</h2>
          <span class="bk-hero-badge">Pre-Orders & Reservations</span>
        </div>
      </div>

      <div>
        <a href="{{ url()->previous() }}" class="bk-btn bk-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalBookingsCount = count($bookings);
      $totalAdvancePaid = $bookings->sum(fn($b) => floatval($b->advance_payment ?? 0) + floatval($b->cash ?? 0) + floatval($b->card ?? 0));
      $totalRemainingBal = $bookings->sum(fn($b) => floatval($b->total_net ?? 0) - (floatval($b->advance_payment ?? 0) + floatval($b->cash ?? 0) + floatval($b->card ?? 0)));
    @endphp

    <div class="bk-kpi-grid">
      <div class="bk-kpi-card">
        <div class="bk-kpi-info">
          <p>Total Bookings</p>
          <h3>{{ number_format($totalBookingsCount) }}</h3>
        </div>
        <div class="bk-kpi-icon amber">
          <i class="bi bi-journal-check"></i>
        </div>
      </div>

      <div class="bk-kpi-card">
        <div class="bk-kpi-info">
          <p>Total Advance Collected</p>
          <h3>PKR {{ number_format($totalAdvancePaid, 2) }}</h3>
        </div>
        <div class="bk-kpi-icon green">
          <i class="bi bi-cash-coin"></i>
        </div>
      </div>

      <div class="bk-kpi-card">
        <div class="bk-kpi-info">
          <p>Total Balance Due</p>
          <h3>PKR {{ number_format($totalRemainingBal, 2) }}</h3>
        </div>
        <div class="bk-kpi-icon red">
          <i class="bi bi-clock-history"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="bk-table-card">
      <div class="bk-table-header">
        <h3 class="bk-table-title">
          <i class="bi bi-list-stars text-primary me-1"></i> Active Booking Records
        </h3>
      </div>

      <div class="bk-table-wrapper">
        <table class="table bk-table datanew">
          <thead>
            <tr>
              <th width="4%">ID</th>
              <th width="14%">Customer</th>
              <th width="10%">Ref</th>
              <th width="18%">Product</th>
              <th width="6%">Qty</th>
              <th width="8%">Price</th>
              <th width="8%">Disc</th>
              <th width="9%">Total</th>
              <th width="8%">Paid</th>
              <th width="8%">Remaining</th>
              <th width="9%">Date</th>
              <th width="12%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $booking)
            @php
              $totalNet = floatval($booking->total_net ?? 0);
              $paid = floatval($booking->advance_payment ?? 0) + floatval($booking->cash ?? 0) + floatval($booking->card ?? 0);
              $remaining = $totalNet - $paid;
            @endphp
            <tr>
              <td class="fw-bold" data-label="#">#{{ $booking->id }}</td>
              <td class="fw-bold text-dark" data-label="Customer">
                {{ $booking->customer_relation->customer_name ?? 'Walk-in Customer' }}
              </td>
              <td data-label="Ref"><span class="badge bg-light text-dark border">{{ $booking->reference ?? '—' }}</span></td>

              <td data-label="Product">
                @php
                  $productNames = [];
                  $productIds = explode(',', $booking->product);
                  foreach ($productIds as $pid) {
                    $product = \App\Models\Product::find($pid);
                    if ($product) $productNames[] = e($product->item_name);
                  }
                @endphp
                {!! implode('<br>', $productNames) !!}
              </td>

              <td data-label="Qty">
                @php $qtys = explode(',', $booking->qty); @endphp
                {!! implode('<br>', $qtys) !!}
              </td>

              <td data-label="Price">
                @php
                  $prices = explode(',', $booking->per_price);
                  foreach ($prices as &$p) { $p = number_format((float)$p, 2); }
                @endphp
                {!! implode('<br>', $prices) !!}
              </td>

              <td data-label="Disc">
                @php
                  $discounts = explode(',', $booking->per_discount);
                  foreach ($discounts as &$d) { $d = number_format((float)$d, 2); }
                @endphp
                {!! implode('<br>', $discounts) !!}
              </td>

              <td class="fw-bold text-dark" data-label="Total">PKR {{ number_format($totalNet, 2) }}</td>
              <td class="fw-bold text-success" data-label="Paid">PKR {{ number_format($paid, 2) }}</td>

              <td data-label="Remaining">
                @if($remaining <= 0)
                  <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                    PKR {{ number_format($remaining, 2) }}
                  </span>
                @else
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                    PKR {{ number_format($remaining, 2) }}
                  </span>
                @endif
              </td>

              <td data-label="Date">
                <span class="badge bg-light text-dark border fw-normal">
                  {{ $booking->created_at->format('d-m-Y') }}
                </span>
              </td>

              <td class="text-center" data-label="Action">
                <div class="d-flex align-items-center justify-content-center gap-1">
                  <a href="{{ route('booking.receipt', $booking->id) }}"
                    target="_blank"
                    class="btn btn-sm btn-outline-secondary rounded-2">
                    <i class="bi bi-printer me-1"></i> Slip
                  </a>

                  @if($booking->sale_date != null)
                    <span class="badge bg-dark">Converted</span>
                  @else
                    <a href="{{ route('sales.from.booking', $booking->id) }}" class="btn btn-sm btn-success rounded-2">
                      <i class="bi bi-check-lg me-1"></i> Confirm
                    </a>

                    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Are you sure you want to delete this booking?')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @endif
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
  if ($.fn.DataTable.isDataTable('.datanew')) {
    $('.datanew').DataTable().destroy();
  }
  $('.datanew').DataTable({
    "pageLength": 25,
    "language": {
      "search": "_INPUT_",
      "searchPlaceholder": "Search bookings..."
    }
  });
});
</script>
@endsection