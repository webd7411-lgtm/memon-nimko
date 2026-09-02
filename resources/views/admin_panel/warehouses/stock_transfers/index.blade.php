@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --st-green: #0e8349;
  --st-green-hover: #0b6b3b;
  --st-bg: #f8fafc;
  --st-card-bg: #ffffff;
  --st-border: #e2e8f0;
  --st-text-dark: #0f172a;
  --st-text-muted: #64748b;
  --st-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.st-container * {
  font-family: var(--st-font);
  box-sizing: border-box;
}

.st-container {
  background-color: var(--st-bg);
  min-height: 100vh;
  padding: 1.25rem;
  overflow-x: hidden !important;
}

/* ══════════ TOP HEADER BAR ══════════ */
.st-header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid var(--st-border);
  border-radius: 10px;
  padding: 0.85rem 1.25rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.st-header-title {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.st-header-title h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--st-text-dark);
  margin: 0;
}

.st-header-title .st-icon {
  font-size: 1.4rem;
  color: var(--st-green);
  line-height: 1;
}

.st-badge-pill {
  background: #e0e7ff;
  color: #4338ca;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 12px;
  padding: 3px 10px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: inline-block;
}

.st-btn-back {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #475569;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.45rem 1rem;
  border-radius: 8px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: all 0.2s ease;
}

.st-btn-back:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.st-btn-create {
  background: var(--st-green);
  color: #ffffff !important;
  font-weight: 700;
  font-size: 0.85rem;
  padding: 0.48rem 1.1rem;
  border-radius: 8px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  box-shadow: 0 3px 8px rgba(14, 131, 73, 0.2);
  transition: all 0.2s ease;
}

.st-btn-create:hover {
  background: var(--st-green-hover);
  color: #ffffff !important;
}

.st-btn-export {
  background: #ffffff;
  border: 1px solid #10b981;
  color: #047857;
  font-weight: 700;
  font-size: 0.85rem;
  padding: 0.48rem 1.1rem;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  white-space: nowrap;
  transition: all 0.2s ease;
  cursor: pointer;
}

.st-btn-export:hover {
  background: #10b981;
  color: #ffffff;
  border-color: #10b981;
}

/* ══════════ KPI GRID ══════════ */
.st-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.st-kpi-card {
  background: #ffffff;
  border: 1px solid var(--st-border);
  border-radius: 10px;
  padding: 1rem 1.15rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.st-kpi-info p {
  margin: 0;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--st-text-muted);
}

.st-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--st-text-dark);
}

.st-kpi-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.st-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.st-kpi-icon.green { background: #ecfdf5; color: var(--st-green); }
.st-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
.st-kpi-icon.purple { background: #f5f3ff; color: #8b5cf6; }

/* ══════════ CARDS ══════════ */
.st-card {
  background: var(--st-card-bg);
  border: 1px solid var(--st-border);
  border-radius: 10px;
  padding: 1.1rem 1.25rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.st-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.35rem;
  display: block;
}

.st-input {
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  font-size: 0.88rem;
  color: #0f172a;
  background-color: #ffffff;
  width: 100%;
}

.st-input:focus {
  border-color: var(--st-green);
  outline: none;
  box-shadow: 0 0 0 3px rgba(14, 131, 73, 0.12);
}

/* ══════════ TABLE STYLING ══════════ */
.st-table-card {
  background: #ffffff;
  border: 1px solid var(--st-border);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.st-table-header {
  padding: 0.9rem 1.25rem;
  border-bottom: 1px solid var(--st-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
}

.st-table-title {
  font-size: 1rem;
  font-weight: 800;
  color: var(--st-text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.st-table-title i {
  color: var(--st-green);
}

.st-table {
  width: 100%;
  border-collapse: collapse;
  margin: 0;
}

.st-table th {
  background: #f1f5f9;
  color: #334155;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--st-border);
}

.st-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.88rem;
}

.st-table tbody tr:hover {
  background-color: #f8fafc;
}

/* ══════════ BADGES & CHIPS ══════════ */
.st-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  border-radius: 6px;
  font-size: 0.76rem;
  font-weight: 700;
}

.st-badge-shop { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.st-badge-wh { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.st-badge-branch { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
.st-badge-date { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-family: monospace; }

.st-item-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 0.2rem 0.6rem;
  font-size: 0.78rem;
  margin: 0.15rem;
  font-weight: 600;
  color: #0f172a;
}

.st-item-chip .qty-tag {
  background: var(--st-green);
  color: #ffffff;
  border-radius: 10px;
  padding: 0.05rem 0.45rem;
  font-size: 0.72rem;
  font-weight: 800;
}

.st-btn-action {
  padding: 0.35rem 0.75rem;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #2563eb;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  transition: all 0.15s ease;
}

.st-btn-action:hover {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
}

/* ══════════ MOBILE CARDS VIEW ══════════ */
.st-mlist { display: none; }

@media (max-width: 991.98px) {
  .st-container {
    padding: 0.6rem 0.4rem !important;
  }

  .st-header-bar {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0.75rem !important;
    padding: 0.85rem 1rem !important;
  }

  .st-header-title {
    width: 100%;
  }

  .st-header-actions {
    width: 100% !important;
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 0.5rem !important;
  }

  .st-header-actions > * {
    font-size: 0.82rem !important;
    padding: 0.48rem 0.75rem !important;
    white-space: nowrap !important;
    box-sizing: border-box !important;
  }

  .st-btn-back {
    flex: 0 0 auto !important;
  }

  .st-btn-create {
    flex: 1 1 auto !important;
    text-align: center !important;
    justify-content: center !important;
  }

  .st-btn-export {
    flex: 1 1 auto !important;
    text-align: center !important;
    justify-content: center !important;
  }

  .st-kpi-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.5rem !important;
  }

  .st-table-card { display: none !important; }
  .st-mlist { display: block !important; }

  .st-mcard {
    background: #ffffff;
    border: 1px solid var(--st-border);
    border-radius: 10px;
    padding: 0.9rem;
    margin-bottom: 0.85rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  }

  .st-mcard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.6rem;
    margin-bottom: 0.6rem;
  }

  .st-mcard-route {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    padding: 0.6rem;
    margin-bottom: 0.6rem;
  }

  .st-mcard-loc {
    flex: 1;
  }

  .st-mcard-loc .lbl {
    font-size: 0.68rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
  }

  .st-mcard-loc .val {
    font-size: 0.82rem;
    font-weight: 700;
    color: #0f172a;
  }
}

@media (max-width: 575.98px) {
  .st-header-actions {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 0.4rem !important;
  }

  .st-btn-back {
    flex: 0 0 auto !important;
  }

  .st-btn-create {
    flex: 1 1 auto !important;
  }

  .st-btn-export {
    flex: 1 1 100% !important;
    width: 100% !important;
    margin-top: 0.1rem !important;
  }
}

@media (max-width: 420px) {
  .st-kpi-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<div class="st-container">

  {{-- ══════════ TOP HEADER BAR ══════════ --}}
  <div class="st-header-bar">
    <div class="st-header-title">
      <i class="bi bi-arrow-left-right st-icon"></i>
      <h2>Stock Transfers</h2>
      <span class="st-badge-pill">Inventory Movement Ledger</span>
    </div>

    <div class="d-flex align-items-center gap-2 st-header-actions">
      <a href="{{ route('warehouses.index') }}" class="st-btn-back">
        <i class="bi bi-arrow-left"></i> Back
      </a>

      <a href="{{ route('stock_transfers.create') }}" class="st-btn-create">
        <i class="bi bi-plus-lg"></i> New Transfer
      </a>

      <button type="button" class="st-btn-export" id="btnExportAll">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
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
  <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- ══════════ KPI SUMMARY CARDS ══════════ --}}
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

  @php
    $pendingTransfersCount = $transfers->where('status', 'pending')->where('to_branch_id', active_branch_id())->count();
  @endphp

  @if($pendingTransfersCount > 0)
  <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 mb-3" style="border-radius: 10px; background: #fffbe6; border: 1px solid #ffe58f;">
    <div class="d-flex align-items-center gap-3">
      <div style="width: 38px; height: 38px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
        <i class="bi bi-bell-fill"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">You have {{ $pendingTransfersCount }} Pending Stock Transfer(s)</h6>
        <small class="text-muted">Review incoming stock dispatched to your branch and click "Accept Stock" to update your inventory.</small>
      </div>
    </div>
  </div>
  @endif

  {{-- ══════════ FILTER BAR ══════════ --}}
  <div class="st-card">
    <form id="filterForm" class="row g-2 align-items-end">
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label class="st-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
        <input type="date" id="startDate" class="st-input" value="{{ request('start_date') }}">
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label class="st-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
        <input type="date" id="endDate" class="st-input" value="{{ request('end_date') }}">
      </div>

      <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
        <button type="button" class="btn text-white fw-bold px-3 py-2 w-100" id="btnFilter" style="background-color: #0e8349; border-radius:8px; font-size:0.85rem;">
          <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>

        <button type="button" class="btn btn-outline-secondary fw-bold px-3 py-2 w-100" id="btnReset" style="border-radius:8px; font-size:0.85rem;">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </button>
      </div>
    </form>
  </div>

  {{-- ══════════ MAIN TABLE CARD ══════════ --}}
  <div class="st-table-card">
    <div class="st-table-header">
      <h3 class="st-table-title">
        <i class="bi bi-info-circle text-success"></i> Recent Transfer Movements
      </h3>
    </div>

    <div class="table-responsive">
      <table class="st-table" id="stockTransferTable">
        <thead>
          <tr>
            <th width="4%" class="text-center">
              <input type="checkbox" id="selectAll" class="form-check-input">
            </th>
            <th width="6%">ID</th>
            <th width="12%">Date</th>
            <th width="14%">From Location</th>
            <th width="14%">To Location</th>
            <th width="24%">Transferred Items</th>
            <th width="12%">Status</th>
            <th width="14%" class="text-center">Action</th>
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
              @elseif($transfer->fromBranch)
                <span class="st-badge st-badge-shop">
                  <i class="bi bi-geo-alt me-1"></i> {{ $transfer->fromBranch->name }}
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
              @elseif ($transfer->transfer_to === 'branch')
                <span class="st-badge st-badge-branch">
                  <i class="bi bi-geo-alt me-1"></i> {{ $transfer->toBranch->name ?? 'Branch' }}
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

            <td>
              @if($transfer->status === 'pending')
                <span class="st-badge" style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5;">
                  <i class="bi bi-clock-history me-1"></i> Pending Acceptance
                </span>
              @elseif($transfer->status === 'rejected')
                <span class="st-badge" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2;">
                  <i class="bi bi-x-circle me-1"></i> Rejected
                </span>
              @else
                <span class="st-badge" style="background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7;">
                  <i class="bi bi-check-circle me-1"></i> Completed
                </span>
              @endif
            </td>

            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                <a href="{{ route('stock_transfers.receipt', $transfer->id) }}" class="st-btn-action" target="_blank" title="Receipt">
                  <i class="bi bi-receipt me-1"></i> Receipt
                </a>

                @if($transfer->status === 'pending')
                  @if(is_all_branches() || $transfer->to_branch_id == active_branch_id())
                    <form action="{{ route('stock_transfers.accept', $transfer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm acceptance of this stock transfer into your branch stock?');">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success px-2 py-1" style="font-size:0.75rem; font-weight:600; border-radius:6px;" title="Accept Stock">
                        <i class="bi bi-check-lg me-1"></i> Accept
                      </button>
                    </form>
                    <form action="{{ route('stock_transfers.reject', $transfer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this stock transfer? Stock will be returned to sender.');">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size:0.75rem; font-weight:600; border-radius:6px;" title="Reject Transfer">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </form>
                  @else
                    <span class="badge bg-light text-muted border">Awaiting Receiver</span>
                  @endif
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              No stock transfers recorded yet.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ══════════ MOBILE CARDS VIEW ══════════ --}}
  <div class="st-mlist">
    @forelse ($transfers as $transfer)
    <div class="st-mcard">
      <div class="st-mcard-header">
        <div>
          <span class="fw-bold text-dark fs-6">Transfer #{{ $transfer->id }}</span>
          <div class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y') }}</div>
        </div>
        <div>
          @if($transfer->status === 'pending')
            <span class="badge bg-warning text-dark">Pending</span>
          @elseif($transfer->status === 'rejected')
            <span class="badge bg-danger">Rejected</span>
          @else
            <span class="badge bg-success">Completed</span>
          @endif
        </div>
      </div>

      <div class="st-mcard-route">
        <div class="st-mcard-loc">
          <div class="lbl">From</div>
          <div class="val">
            @if($transfer->fromWarehouse)
              {{ $transfer->fromWarehouse->warehouse_name }}
            @elseif($transfer->fromBranch)
              {{ $transfer->fromBranch->name }}
            @else
              Shop Stock
            @endif
          </div>
        </div>
        <div class="text-muted"><i class="bi bi-arrow-right fs-5"></i></div>
        <div class="st-mcard-loc">
          <div class="lbl">To</div>
          <div class="val">
            @if ($transfer->transfer_to === 'shop')
              {{ $transfer->shop_name ?? 'Shop' }}
            @elseif ($transfer->transfer_to === 'branch')
              {{ $transfer->toBranch->name ?? 'Branch' }}
            @else
              {{ $transfer->toWarehouse->warehouse_name ?? 'Warehouse' }}
            @endif
          </div>
        </div>
      </div>

      @if (!empty($transfer->items))
      <div class="mb-2">
        <div class="small fw-bold text-muted mb-1"><i class="bi bi-box-seam me-1"></i>Items Transferred:</div>
        @foreach ($transfer->items as $item)
        <div class="d-flex justify-content-between align-items-center bg-light rounded px-2 py-1 mb-1 small">
          <span class="fw-semibold text-dark">{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</span>
          <span class="badge bg-primary">{{ $item['qty'] }} {{ $item['unit'] }}</span>
        </div>
        @endforeach
      </div>
      @endif

      <div class="d-flex align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
        <a href="{{ route('stock_transfers.receipt', $transfer->id) }}" class="btn btn-sm btn-outline-primary flex-fill" target="_blank">
          <i class="bi bi-receipt me-1"></i> Receipt
        </a>

        @if($transfer->status === 'pending' && (is_all_branches() || $transfer->to_branch_id == active_branch_id()))
          <form action="{{ route('stock_transfers.accept', $transfer->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Accept this transfer into your branch stock?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-success w-100">
              <i class="bi bi-check-lg me-1"></i> Accept
            </button>
          </form>
          <form action="{{ route('stock_transfers.reject', $transfer->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Reject this transfer?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Reject Transfer">
              <i class="bi bi-x-lg me-1"></i> Reject
            </button>
          </form>
        @endif
      </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted bg-white rounded border">
      <i class="bi bi-inbox fs-1"></i>
      <p class="mt-2 mb-0">No stock transfers recorded yet.</p>
    </div>
    @endforelse
  </div>

</div>
@endsection

@section('scripts')
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
            { "orderable": false, "targets": [0, 5, 7] }
        ]
    });

    $('#selectAll').on('click', function() {
        var checked = this.checked;
        $('.row-checkbox').prop('checked', checked);
    });

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

    $('#btnExportAll').on('click', function() {
        var data = [];
        table.rows({ search: 'applied' }).every(function() {
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
});
</script>
@endsection