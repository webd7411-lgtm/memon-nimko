@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --sr-bg: #f8fafc;
  --sr-surface: #ffffff;
  --sr-border: #e2e8f0;
  --sr-border-lt: #f1f5f9;
  --sr-text: #0f172a;
  --sr-text-sec: #475569;
  --sr-text-muted: #64748b;
  --sr-primary: #2563eb;
  --sr-radius: 16px;
  --sr-radius-sm: 10px;
  --sr-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --sr-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --sr-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sr-page * {
  font-family: var(--sr-font);
}

.sr-page {
  background-color: var(--sr-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.sr-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--sr-radius);
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

.sr-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(239, 68, 68, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(37, 99, 235, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.sr-hero > * {
  position: relative;
  z-index: 1;
}

.sr-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.sr-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.sr-hero-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #f87171;
  font-size: 1.4rem;
}

.sr-hero-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #fca5a5;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.sr-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--sr-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.sr-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.sr-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.sr-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .sr-kpi-grid { grid-template-columns: 1fr; }
}

.sr-kpi-card {
  background: var(--sr-surface);
  border: 1px solid var(--sr-border);
  border-radius: var(--sr-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--sr-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.sr-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--sr-shadow-lg);
}

.sr-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sr-text-muted);
}
.sr-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--sr-text);
  letter-spacing: -0.02em;
}

.sr-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.sr-kpi-icon.red { background: #fef2f2; color: #ef4444; }
.sr-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
.sr-kpi-icon.blue { background: #eff6ff; color: #2563eb; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.sr-table-card {
  background: var(--sr-surface);
  border: 1px solid var(--sr-border);
  border-radius: var(--sr-radius);
  box-shadow: var(--sr-shadow-lg);
  overflow: hidden;
}

.sr-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--sr-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.sr-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--sr-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sr-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.sr-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.sr-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--sr-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--sr-border);
  border-top: none;
  white-space: nowrap;
}

.sr-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--sr-border-lt);
  font-size: 0.88rem;
  color: var(--sr-text-sec);
}

.sr-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* ═══════════════════════════════════════════════════
   MOBILE PREMIUM — return cards, no horizontal scroll
═══════════════════════════════════════════════════ */
.sr-mlist { display: none; }

.sr-mcard {
  background: var(--sr-surface);
  border: 1px solid var(--sr-border);
  border-radius: var(--sr-radius);
  box-shadow: var(--sr-shadow);
  margin-bottom: .9rem;
  overflow: hidden;
}

.sr-mcard-top {
  display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem;
  padding: 1rem 1.05rem .85rem;
}
.sr-mcard-ref { display: flex; align-items: center; gap: .7rem; min-width: 0; }
.sr-mcard-ic {
  width: 2.6rem; height: 2.6rem; flex: 0 0 auto; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #fef2f2 0%, #ffe4e4 100%);
  color: #ef4444; font-size: 1.15rem; border: 1px solid #fecaca;
}
.sr-mcard-no { font-weight: 800; font-size: .95rem; color: var(--sr-text); letter-spacing: -.2px; }
.sr-mcard-date { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: var(--sr-text-muted); font-weight: 500; margin-top: 2px; }
.sr-mcard-date i { font-size: .78rem; }
.sr-mcard-status {
  display: inline-flex; align-items: center; gap: 4px;
  background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
  border-radius: 20px; padding: .3rem .7rem; font-size: .7rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .3px; white-space: nowrap;
}

.sr-mcard-inv {
  display: flex; align-items: center; gap: .55rem;
  background: #f8fafc; border: 1px solid var(--sr-border-lt); border-radius: 10px;
  padding: .6rem .75rem; margin: 0 1.05rem .75rem;
}
.sr-mcard-inv i { color: var(--sr-primary); font-size: .95rem; }
.sr-mcard-inv-lb { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--sr-text-muted); }
.sr-mcard-inv-val { font-size: .9rem; font-weight: 800; color: var(--sr-text); }

.sr-mcard-body { padding: 0 1.05rem .85rem; }

.sr-mcard-row { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; padding: .45rem 0; }
.sr-mcard-row + .sr-mcard-row { border-top: 1px dashed var(--sr-border-lt); }
.sr-mcard-row-lb { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--sr-text-muted); flex: 0 0 auto; padding-top: 2px; }
.sr-mcard-row-val { font-size: .86rem; font-weight: 600; color: var(--sr-text); text-align: right; word-break: break-word; min-width: 0; }
.sr-mcard-row-val.refund { color: #dc2626; font-weight: 800; font-size: .95rem; }

.sr-mcard-items { margin: .15rem 1.05rem .8rem; border: 1px solid var(--sr-border-lt); border-radius: 12px; overflow: hidden; }
.sr-mcard-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .55rem .75rem; background: #f8fafc; border-bottom: 1px solid var(--sr-border-lt);
  font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--sr-text-sec);
}
.sr-mcard-items-hd i { color: #ef4444; font-size: .82rem; }
.sr-mcard-items-hd b { color: #ef4444; background: #fef2f2; border-radius: 20px; padding: .05rem .5rem; font-size: .68rem; }
.sr-mcard-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .55rem .75rem; background: #fff; border-bottom: 1px solid var(--sr-border-lt);
  font-size: .84rem; font-weight: 600; color: var(--sr-text); word-break: break-word; line-height: 1.3;
}
.sr-mcard-item:last-child { border-bottom: none; }
.sr-mcard-item i { color: #64748b; font-size: .82rem; flex: 0 0 auto; }

.sr-mcard-foot { padding: 0 1.05rem .95rem; }
.sr-mcard-slip {
  display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%;
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  color: #fff !important; text-decoration: none; border-radius: 11px; padding: .68rem .9rem;
  font-size: .82rem; font-weight: 700; box-shadow: 0 6px 16px rgba(14, 165, 233, .22);
}
.sr-mcard-slip:hover { color: #fff !important; text-decoration: none; box-shadow: 0 10px 24px rgba(14, 165, 233, .32); }
.sr-mcard-slip .bi-arrow-right { margin-left: auto; }

.sr-mempty {
  background: var(--sr-surface); border: 1px dashed var(--sr-border); border-radius: var(--sr-radius);
  text-align: center; padding: 2.5rem 1rem; color: var(--sr-text-muted); min-height: 220px;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .4rem;
}
.sr-mempty i { font-size: 2.2rem; color: #cbd5e1; }

@media (max-width: 991.98px) {
  .sr-page { overflow-x: hidden; }
  .sr-page, .sr-page .container-fluid { max-width: 100%; }

  .sr-table-wrapper { display: none !important; }
  .sr-mlist { display: block; }

  .sr-hero { padding: 1.1rem; flex-direction: column; align-items: stretch; gap: .75rem; }
  .sr-hero-title { gap: .7rem; }
  .sr-hero-title h2 { font-size: 1.12rem; }
  .sr-hero-icon { width: 42px; height: 42px; font-size: 1.15rem; }
  .sr-hero .sr-btn { width: 100%; justify-content: center; padding: .6rem .5rem; }

  .sr-kpi-grid { grid-template-columns: 1fr; gap: .6rem; }
  .sr-kpi-card { padding: .9rem .85rem; }
  .sr-kpi-info p { font-size: .68rem; }
  .sr-kpi-info h3 { font-size: 1.15rem; }
  .sr-kpi-icon { width: 38px; height: 38px; font-size: 1.05rem; }
}
</style>

<div class="sr-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="sr-hero">
      <div class="sr-hero-title">
        <div class="sr-hero-icon">
          <i class="bi bi-arrow-return-left"></i>
        </div>
        <div>
          <h2>Sale Returns Ledger</h2>
          <span class="sr-hero-badge">Refunds & Returned Merchandise</span>
        </div>
      </div>

      <div>
        <a href="{{ url()->previous() }}" class="sr-btn sr-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalReturnsCount = count($salesReturns);
      $totalNetReturned = $salesReturns->sum('total_net');
      $totalReturnedItems = $salesReturns->sum('total_items');
    @endphp

    <div class="sr-kpi-grid">
      <div class="sr-kpi-card">
        <div class="sr-kpi-info">
          <p>Return Slips</p>
          <h3>{{ number_format($totalReturnsCount) }}</h3>
        </div>
        <div class="sr-kpi-icon red">
          <i class="bi bi-receipt-cutoff"></i>
        </div>
      </div>

      <div class="sr-kpi-card">
        <div class="sr-kpi-info">
          <p>Total Refund Value</p>
          <h3>PKR {{ number_format($totalNetReturned, 2) }}</h3>
        </div>
        <div class="sr-kpi-icon amber">
          <i class="bi bi-cash-stack"></i>
        </div>
      </div>

      <div class="sr-kpi-card">
        <div class="sr-kpi-info">
          <p>Returned Quantity</p>
          <h3>{{ number_format($totalReturnedItems) }}</h3>
        </div>
        <div class="sr-kpi-icon blue">
          <i class="bi bi-box-seam"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="sr-table-card">
      <div class="sr-table-header">
        <h3 class="sr-table-title">
          <i class="bi bi-card-list text-danger me-1"></i> Return Transaction History
        </h3>
      </div>

      <div class="sr-table-wrapper">
        @if($salesReturns->isEmpty())
        <div class="alert alert-info text-center m-4 rounded-3 border-0">
          <i class="bi bi-info-circle me-1"></i> No sale returns recorded yet.
        </div>
        @else
        <table class="table sr-table datanew">
          <thead>
            <tr>
              <th width="4%">#</th>
              <th width="10%">Invoice</th>
              <th width="24%">Returned Items</th>
              <th width="16%">Customer</th>
              <th width="10%" class="text-center">Total Items</th>
              <th width="12%" class="text-end">Total Net</th>
              <th width="14%">Return Note</th>
              <th width="10%" class="text-center">Date</th>
              <th width="8%" class="text-center">Status</th>
              <th width="8%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($salesReturns as $return)
            <tr>
              <td class="fw-bold">#{{ $loop->iteration }}</td>
              <td>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                  {{ $return->sale->invoice_no ?? 'N/A' }}
                </span>
              </td>
              <td>
                @php
                  $products = explode(',', $return->product ?? '');
                @endphp
                @if(!empty($products))
                  @foreach($products as $p)
                    <span class="badge bg-light text-dark border mb-1"><i class="bi bi-box me-1 text-secondary"></i> {{ trim($p) }}</span><br>
                  @endforeach
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </td>
              <td class="fw-bold text-dark">
                {{ $return->sale->customer_relation->customer_name ?? 'Walk-in Customer' }}
              </td>
              <td class="text-center fw-bold text-dark">{{ $return->total_items }}</td>
              <td class="text-end fw-bold text-danger">PKR {{ number_format($return->total_net, 2) }}</td>
              <td><small class="text-muted">{{ $return->return_note ?? '—' }}</small></td>
              <td class="text-center">
                <span class="badge bg-light text-dark border fw-normal">
                  {{ $return->created_at->format('d-m-Y') }}
                </span>
              </td>
              <td class="text-center">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-2 py-1">
                  <i class="bi bi-arrow-return-left me-1"></i> Returned
                </span>
              </td>
              <td class="text-center">
                <a href="{{ route('saleReturn.invoice', $return->id) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-2 fw-semibold">
                  <i class="bi bi-printer-fill me-1"></i> Slip
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>

      {{-- ═══════ MOBILE CARD LIST ═══════ --}}
      <div class="sr-mlist">
        @forelse($salesReturns as $return)
        @php
          $products = explode(',', $return->product ?? '');
        @endphp
        <div class="sr-mcard">
          <div class="sr-mcard-top">
            <div class="sr-mcard-ref">
              <span class="sr-mcard-ic"><i class="bi bi-arrow-return-left"></i></span>
              <div>
                <div class="sr-mcard-no">Return #{{ $loop->iteration }}</div>
                <div class="sr-mcard-date"><i class="bi bi-calendar3"></i>{{ $return->created_at->format('d-M-Y') }}</div>
              </div>
            </div>
            <span class="sr-mcard-status"><i class="bi bi-arrow-return-left"></i>Returned</span>
          </div>

          <div class="sr-mcard-inv">
            <i class="bi bi-receipt"></i>
            <div>
              <div class="sr-mcard-inv-lb">Invoice</div>
              <div class="sr-mcard-inv-val">{{ $return->sale->invoice_no ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="sr-mcard-body">
            <div class="sr-mcard-row">
              <span class="sr-mcard-row-lb">Customer</span>
              <span class="sr-mcard-row-val">{{ $return->sale->customer_relation->customer_name ?? 'Walk-in Customer' }}</span>
            </div>
            <div class="sr-mcard-row">
              <span class="sr-mcard-row-lb">Total Items</span>
              <span class="sr-mcard-row-val">{{ $return->total_items }}</span>
            </div>
            <div class="sr-mcard-row">
              <span class="sr-mcard-row-lb">Refund Value</span>
              <span class="sr-mcard-row-val refund">PKR {{ number_format($return->total_net, 2) }}</span>
            </div>
            @if($return->return_note)
            <div class="sr-mcard-row">
              <span class="sr-mcard-row-lb">Note</span>
              <span class="sr-mcard-row-val">{{ $return->return_note }}</span>
            </div>
            @endif
          </div>

          @if(!empty($products))
          <div class="sr-mcard-items">
            <div class="sr-mcard-items-hd">
              <span><i class="bi bi-box-seam me-1"></i>Returned Items</span>
              <b>{{ count(array_filter($products, 'strlen')) }}</b>
            </div>
            @foreach($products as $p)
            @if(trim($p) !== '')
            <div class="sr-mcard-item"><i class="bi bi-box"></i>{{ trim($p) }}</div>
            @endif
            @endforeach
          </div>
          @endif

          <div class="sr-mcard-foot">
            <a href="{{ route('saleReturn.invoice', $return->id) }}" target="_blank" class="sr-mcard-slip">
              <i class="bi bi-printer-fill"></i> View Return Slip <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
        @empty
        <div class="sr-mempty"><i class="bi bi-inbox"></i><span>No sale returns recorded yet.</span></div>
        @endforelse
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
      "searchPlaceholder": "Search returns..."
    }
  });
});
</script>
@endsection