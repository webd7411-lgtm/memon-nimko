@extends('admin_panel.layout.app')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-bg: #f1f4f9;
  --pc-surface: #ffffff;
  --pc-border: #e9edf2;
  --pc-border-lt: #f1f4f9;
  --pc-text: #0b1a33;
  --pc-text-sec: #54657e;
  --pc-text-muted: #8896ab;
  --pc-accent: #2b7fff;
  --pc-accent-drk: #1a6ae8;
  --pc-success: #0fae6b;
  --pc-danger: #e54545;
  --pc-warning: #f5a623;
  --pc-radius: 14px;
  --pc-radius-sm: 9px;
  --pc-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --pc-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --pc-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --pc-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.pc-page * { font-family: var(--pc-font); }

.pc-page {
  background: var(--pc-bg);
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

/* ═══════ HEADER ═══════ */
.pc-hdr {
  position: relative;
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--pc-radius);
  padding: 1.3rem 2rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--pc-shadow-xl);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
}

.pc-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.15) 0%, transparent 100%),
    radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.08) 0%, transparent 100%);
  pointer-events: none;
}

.pc-hdr::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: .5;
  pointer-events: none;
}

.pc-hdr > * { position: relative; z-index: 1; }

.pc-hdr h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.4px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: .65rem;
}

.pc-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }

.pc-hdr .hdr-badge {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 20px;
  padding: .25rem .9rem;
  font-size: .7rem;
  font-weight: 600;
  color: rgba(255,255,255,.65);
  letter-spacing: .4px;
  text-transform: uppercase;
}

.pc-btn {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  border-radius: var(--pc-radius-sm);
  font-weight: 600;
  font-size: .82rem;
  transition: all .25s ease;
  cursor: pointer;
  text-decoration: none;
  border: none;
}

.pc-btn-primary {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff;
  padding: .5rem 1.35rem;
}

.pc-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(43,127,255,.25);
  color: #fff;
}

/* ═══════ CARD ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  transition: box-shadow .3s ease;
}

.pc-card:hover { box-shadow: var(--pc-shadow-lg); }

.pc-card-body { padding: 1.5rem; }

/* ═══════ TABLE ═══════ */
.pc-tbl-wrap {
  overflow-x: auto;
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
}

.pc-tbl {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: .84rem;
}

.pc-tbl thead th {
  background: #f8fafc;
  font-size: .68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: var(--pc-text-muted);
  padding: .6rem .85rem;
  border-bottom: 2px solid var(--pc-border);
  text-align: left;
}

.pc-tbl tbody td {
  padding: .55rem .85rem;
  border-bottom: 1px solid var(--pc-border-lt);
  vertical-align: middle;
}

.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }

.pc-tbl .pc-batch {
  font-weight: 700;
  color: var(--pc-text);
  font-size: .88rem;
  white-space: nowrap;
}

.pc-tbl .pc-date {
  color: var(--pc-text-sec);
  white-space: nowrap;
}

.pc-badge {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  border-radius: 6px;
  padding: .18rem .65rem;
  font-size: .72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .3px;
  white-space: nowrap;
}

.pc-badge-kitchen {
  background: #eef2ff;
  color: #3b5bb3;
}

.pc-badge-warehouse {
  background: #f0fdf4;
  color: #0f7a47;
}

.pc-tbl .pc-items {
  line-height: 1.4;
}

.pc-tbl .pc-items small {
  color: var(--pc-text-muted);
  font-size: .72rem;
  font-weight: 500;
}

.pc-tbl .pc-notes {
  color: var(--pc-text-sec);
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.pc-tbl .pc-user {
  color: var(--pc-text-sec);
  font-weight: 500;
  font-size: .8rem;
}

.pc-tbl .pc-actions {
  display: flex;
  gap: 5px;
  white-space: nowrap;
}

.pc-act {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  border-radius: 6px;
  padding: .35rem .8rem;
  font-size: .74rem;
  font-weight: 600;
  transition: all .2s ease;
  text-decoration: none;
  cursor: pointer;
  border: 1.5px solid transparent;
}

.pc-act-edit {
  background: #eef2ff;
  border-color: #dde4f7;
  color: #3b5bb3;
}

.pc-act-edit:hover { background: #dde4f7; color: #2a4a9e; }

.pc-act-print {
  background: #f8fafc;
  border-color: var(--pc-border);
  color: var(--pc-text-sec);
}

.pc-act-print:hover { background: #e9edf2; color: var(--pc-text); }

/* ═══════ EMPTY ═══════ */
.pc-empty {
  text-align: center;
  padding: 2.5rem .85rem;
  color: var(--pc-text-muted);
}

.pc-empty i { font-size: 2rem; color: #ced8e6; display: block; margin-bottom: .5rem; }
.pc-empty span { font-size: .9rem; font-weight: 500; }

/* ═══════ RESPONSIVE ═══════ */
@media (max-width: 768px) {
  .pc-hdr { padding: 1rem 1rem; flex-direction: column; align-items: stretch; gap: .6rem; }
  .pc-hdr h2 { font-size: 1rem !important; line-height: 1.35; }
}

/* ═══════ MOBILE PREMIUM CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .pc-page { overflow-x: hidden !important; padding-bottom: 2rem; }

  /* Header */
  .pc-hdr { padding: .85rem .9rem !important; }
  .pc-hdr h2 { font-size: .98rem !important; }
  .pc-hdr h2 i { font-size: 1.05rem; }
  .pc-hdr > div:last-child { display: flex !important; flex-direction: column; align-items: stretch !important; gap: .45rem !important; margin-top: 0 !important; }
  .pc-hdr .pc-btn { justify-content: center; min-height: 40px; font-size: .76rem; }

  /* KPI stat cards — compact */
  .pc-page .row.g-3 { margin-bottom: .9rem !important; }
  .pc-page .row.g-3 > .col-6 > .pc-card { padding: .55rem .6rem !important; gap: .5rem !important; border-radius: 12px; }
  .pc-page .row.g-3 .rounded-circle { width: 36px !important; height: 36px !important; padding: .45rem !important; flex-shrink: 0; }
  .pc-page .row.g-3 .fs-5 { font-size: .92rem !important; }
  .pc-page .row.g-3 .small { font-size: .56rem !important; letter-spacing: .3px; }

  /* Card body */
  .pc-card-body { padding: .65rem !important; overflow: hidden !important; }

  /* Kill ALL scroll wrappers — no horizontal scroll */
  .pc-page .pc-tbl-wrap,
  .pc-page .dataTables_wrapper,
  .pc-page .dataTables_scrollBody,
  .pc-page .dataTables_scrollHead,
  .pc-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
  }

  /* Table → stacked cards */
  .pc-page .pc-tbl,
  .pc-page .pc-tbl.dataTable {
    display: block !important;
    width: 100% !important;
  }
  .pc-page .pc-tbl thead { display: none !important; }
  .pc-page .pc-tbl tbody { display: block !important; }
  .pc-page .pc-tbl tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .5rem .55rem;
    align-items: start;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-top: 3px solid var(--pc-accent);
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 8px rgba(0,0,0,.05);
    padding: .6rem .7rem;
    margin-bottom: .65rem;
    overflow: hidden !important;
  }
  .pc-page .pc-tbl tbody tr:hover { background: var(--pc-surface); }

  .pc-page .pc-tbl tbody td {
    display: flex !important;
    flex-direction: column;
    gap: .15rem;
    border: none !important;
    padding: 0 !important;
    min-width: 0;
    text-align: left;
    word-break: break-word;
    overflow-wrap: anywhere;
  }
  .pc-page .pc-tbl tbody td::before {
    content: attr(data-label);
    font-size: .54rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .45px;
    color: var(--pc-text-muted);
    white-space: nowrap;
  }

  /* ── Column ordering & styling ── */
  /* Batch # → badge top-left */
  .pc-page .pc-tbl tbody td:nth-child(1) {
    order: 0; justify-self: start; align-self: start;
    background: #eef3fc; border-radius: 7px; padding: .18rem .55rem !important;
    font-weight: 700; font-size: .68rem; color: #3b5bb3;
  }
  .pc-page .pc-tbl tbody td:nth-child(1)::before { content: none; }
  .pc-page .pc-tbl tbody td:nth-child(1) .pc-batch { font-size: .72rem; white-space: normal; }

  /* Date */
  .pc-page .pc-tbl tbody td:nth-child(2) { order: 1; align-self: end; justify-self: end; }
  .pc-page .pc-tbl tbody td:nth-child(2)::before { content: "Date"; }
  .pc-page .pc-tbl tbody td:nth-child(2) .pc-date { font-size: .72rem; white-space: normal; }

  /* Source badge */
  .pc-page .pc-tbl tbody td:nth-child(3) { order: 2; }
  .pc-page .pc-tbl tbody td:nth-child(3) .pc-badge { font-size: .64rem; }

  /* Cost — accent highlight */
  .pc-page .pc-tbl tbody td:nth-child(5) { order: 3; }
  .pc-page .pc-tbl tbody td:nth-child(5)::before { content: "Cost"; }
  .pc-page .pc-tbl tbody td:nth-child(5) { font-weight: 800; font-size: .82rem; color: var(--pc-accent); }

  /* Items — full width */
  .pc-page .pc-tbl tbody td:nth-child(4) { order: 4; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(4) .pc-items small { font-size: .66rem; }

  /* Notes — full width */
  .pc-page .pc-tbl tbody td:nth-child(6) { order: 5; grid-column: 1 / -1; background: #f8fafc; border-radius: 8px; padding: .25rem .5rem !important; }
  .pc-page .pc-tbl tbody td:nth-child(6).pc-notes,
  .pc-page .pc-tbl tbody td:nth-child(6) .pc-notes { max-width: 100%; white-space: normal; overflow: visible; text-overflow: unset; font-size: .72rem; }

  /* Created By */
  .pc-page .pc-tbl tbody td:nth-child(7) { order: 6; grid-column: 1 / -1; flex-direction: row; align-items: center; gap: .4rem; }
  .pc-page .pc-tbl tbody td:nth-child(7) .pc-user { font-size: .7rem; }

  /* Actions — full width 2-col grid */
  .pc-page .pc-tbl tbody td:nth-child(8) { order: 7; grid-column: 1 / -1; }
  .pc-page .pc-tbl .pc-actions {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr);
    gap: .4rem;
    width: 100%;
    white-space: normal;
  }
  .pc-page .pc-act {
    justify-content: center;
    width: 100%;
    min-height: 34px;
    padding: .38rem .4rem;
    font-size: .66rem;
    border-radius: 8px;
  }

  /* Empty state */
  .pc-page .pc-tbl tbody td.pc-empty { grid-column: 1 / -1; }
}
</style>

@section('content')
<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══ HEADER ═══ --}}
    <div class="pc-hdr">
      <div class="d-flex align-items-center gap-3">
        <h2><i class="bi bi-gear-wide-connected"></i>Own Production & Recipe Batch System</h2>
        <span class="hdr-badge d-none d-sm-inline">{{ count($entries) }} Batches Record</span>
      </div>
      <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
        <a href="{{ route('raw-materials.index') }}" class="pc-btn" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);">
          <i class="bi bi-box-seam me-1"></i>Raw Materials Inventory
        </a>
        <a href="{{ route('production.create') }}" class="pc-btn pc-btn-primary">
          <i class="bi bi-plus-circle me-1"></i>New Production Entry
        </a>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:var(--pc-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
      <strong><i class="bi bi-check-circle me-1"></i></strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══ KPI STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="pc-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--pc-accent);">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#eef2ff;color:var(--pc-accent);width:48px;height:48px;">
            <i class="bi bi-layers-fill fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.75rem;text-transform:uppercase;">Total Batches</span>
            <strong class="fs-5 text-dark">{{ count($entries) }}</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pc-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--pc-success);">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#ecfdf5;color:var(--pc-success);width:48px;height:48px;">
            <i class="bi bi-currency-dollar fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.75rem;text-transform:uppercase;">Total Production Cost</span>
            <strong class="fs-5 text-dark">Rs {{ number_format($entries->sum('production_cost') ?? 0, 0) }}</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pc-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid var(--pc-warning);">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#fffbeb;color:var(--pc-warning);width:48px;height:48px;">
            <i class="bi bi-calendar2-check fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.75rem;text-transform:uppercase;">Today's Batches</span>
            <strong class="fs-5 text-dark">{{ $entries->filter(fn($e) => \Carbon\Carbon::parse($e->production_date)->isToday())->count() }}</strong>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pc-card p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #8b5cf6;">
          <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background:#f3e8ff;color:#8b5cf6;width:48px;height:48px;">
            <i class="bi bi-box2-heart fs-5"></i>
          </div>
          <div>
            <span class="d-block text-muted small fw-semibold" style="font-size:.75rem;text-transform:uppercase;">Finished Items</span>
            <strong class="fs-5 text-dark">{{ $entries->sum('items_count') }} Items</strong>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ TABLE CARD ═══ --}}
    <div class="pc-card">
      <div class="pc-card-body">
        <div class="pc-tbl-wrap">
          <table id="productionTable" class="pc-tbl">
            <thead>
              <tr>
                <th>Batch #</th>
                <th>Date</th>
                <th>Source</th>
                <th>Items</th>
                <th>Cost (Rs)</th>
                <th>Notes</th>
                <th>Created By</th>
                <th style="width:170px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($entries as $e)
              <tr>
                <td data-label="Batch #"><span class="pc-batch">{{ $e->entry_no }}</span></td>
                <td data-label="Date"><span class="pc-date">{{ \Carbon\Carbon::parse($e->production_date)->format('d-M-Y') }}</span></td>
                <td data-label="Source">
                  <span class="pc-badge {{ $e->source === 'kitchen' ? 'pc-badge-kitchen' : 'pc-badge-warehouse' }}">
                    <i class="bi bi-{{ $e->source === 'kitchen' ? 'house-door' : 'building' }}"></i>
                    {{ $e->source }}
                  </span>
                </td>
                <td data-label="Items">
                  <div class="pc-items" title="{{ $e->product_details }}">
                    {{ \Illuminate\Support\Str::limit($e->product_details, 50) }}
                    <br>
                    <small><i class="bi bi-box-seam"></i> {{ $e->items_count }} items</small>
                  </div>
                </td>
                <td data-label="Cost (Rs)" style="font-weight:700;color:var(--pc-accent);">Rs {{ number_format($e->production_cost ?? 0, 0) }}</td>
                <td class="pc-notes" data-label="Notes">{{ $e->notes ?? '-' }}</td>
                <td data-label="Created By"><span class="pc-user">{{ $e->user_name ?? 'System' }}</span></td>
                <td>
                  <div class="pc-actions">
                    <a href="{{ route('production.edit', $e->id) }}" class="pc-act pc-act-edit">
                      <i class="bi bi-pencil"></i>Edit
                    </a>
                    <a href="{{ route('production.gatepass', $e->id) }}" class="pc-act pc-act-print" target="_blank">
                      <i class="bi bi-printer"></i>Gatepass
                    </a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="pc-empty">
                  <i class="bi bi-inbox"></i>
                  <span>No production entries found.</span>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('#productionTable').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      order: [[1, 'desc']],
      language: {
        search: "Search Production:",
        lengthMenu: "Show _MENU_ entries",
        emptyTable: "No production entries found"
      }
    });
  });
</script>
@endsection
