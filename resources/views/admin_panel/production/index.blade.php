@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-green: #0e8349;
  --pc-green-hover: #0b6b3b;
  --pc-bg: #f8fafc;
  --pc-card-bg: #ffffff;
  --pc-border: #e2e8f0;
  --pc-text-dark: #0f172a;
  --pc-text-muted: #64748b;
  --pc-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.pc-container * {
  font-family: var(--pc-font);
  box-sizing: border-box;
}

.pc-container {
  background-color: var(--pc-bg);
  min-height: 100vh;
  padding: 1.25rem;
  overflow-x: hidden !important;
}

/* ══════════ TOP HEADER BAR ══════════ */
.pc-header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid var(--pc-border);
  border-radius: 10px;
  padding: 0.85rem 1.25rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.pc-header-title {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.pc-header-title h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--pc-text-dark);
  margin: 0;
}

.pc-header-title .pc-icon {
  font-size: 1.4rem;
  color: var(--pc-green);
  line-height: 1;
}

.pc-badge-pill {
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

.pc-btn-back {
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

.pc-btn-back:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.pc-btn-create {
  background: var(--pc-green);
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

.pc-btn-create:hover {
  background: var(--pc-green-hover);
  color: #ffffff !important;
}

/* ══════════ KPI GRID ══════════ */
.pc-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.pc-kpi-card {
  background: #ffffff;
  border: 1px solid var(--pc-border);
  border-radius: 10px;
  padding: 1rem 1.15rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.pc-kpi-info p {
  margin: 0;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--pc-text-muted);
}

.pc-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--pc-text-dark);
}

.pc-kpi-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.pc-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.pc-kpi-icon.green { background: #ecfdf5; color: var(--pc-green); }
.pc-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
.pc-kpi-icon.purple { background: #f5f3ff; color: #8b5cf6; }

/* ══════════ TABLE STYLING ══════════ */
.pc-table-card {
  background: #ffffff;
  border: 1px solid var(--pc-border);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.pc-table-header {
  padding: 0.9rem 1.25rem;
  border-bottom: 1px solid var(--pc-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
}

.pc-table-title {
  font-size: 1rem;
  font-weight: 800;
  color: var(--pc-text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.pc-table-title i {
  color: var(--pc-green);
}

.pc-table {
  width: 100%;
  border-collapse: collapse;
  margin: 0;
}

.pc-table th {
  background: #f1f5f9;
  color: #334155;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--pc-border);
}

.pc-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.88rem;
}

.pc-table tbody tr:hover {
  background-color: #f8fafc;
}

.pc-badge-source {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  border-radius: 6px;
  font-size: 0.76rem;
  font-weight: 700;
}

.pc-badge-kitchen { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
.pc-badge-warehouse { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

.pc-btn-action {
  padding: 0.32rem 0.65rem;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #334155;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  transition: all 0.15s ease;
}

.pc-btn-action:hover {
  background: #f1f5f9;
  color: #0f172a;
}

/* ══════════ MOBILE CARDS VIEW ══════════ */
.pc-mlist { display: none; }

@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .pc-container {
    padding: 0.65rem 0.65rem 1.25rem !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
  }

  .pc-header-bar {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0.6rem !important;
    margin-bottom: 0.75rem !important;
  }
  .pc-title-group h4 {
    font-size: 1.05rem !important;
    line-height: 1.25 !important;
  }
  .pc-title-group p {
    font-size: 0.74rem !important;
  }

  .pc-header-actions {
    width: 100% !important;
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.45rem !important;
  }

  .pc-header-actions > * {
    width: 100% !important;
    min-height: 34px !important;
    padding: 0.35rem 0.55rem !important;
    font-size: 0.75rem !important;
    text-align: center !important;
    justify-content: center !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }
  .pc-header-actions > *:last-child:nth-child(odd) {
    grid-column: 1 / -1 !important;
  }

  .pc-kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.5rem !important;
    margin-bottom: 0.75rem !important;
  }
  .pc-kpi-card {
    padding: 0.65rem 0.75rem !important;
    gap: 0.6rem !important;
    border-radius: 10px !important;
  }
  .pc-kpi-icon {
    width: 36px !important;
    height: 36px !important;
    font-size: 1rem !important;
    border-radius: 8px !important;
    flex-shrink: 0 !important;
  }
  .pc-kpi-info .lbl {
    font-size: 0.64rem !important;
  }
  .pc-kpi-info .val {
    font-size: 1.15rem !important;
  }

  .pc-table-card { display: none !important; }
  .pc-mlist { display: block !important; }

  .pc-msearch-wrap {
    position: relative;
    margin-bottom: 0.65rem;
  }
  .pc-msearch-wrap i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
  }
  .pc-msearch {
    width: 100% !important;
    min-height: 36px !important;
    height: 36px !important;
    padding: 0.35rem 0.75rem 0.35rem 2.1rem !important;
    font-size: 0.8rem !important;
    border: 1px solid var(--pc-border) !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    color: var(--pc-text) !important;
  }
  .pc-msearch:focus {
    outline: none !important;
    border-color: var(--pc-accent) !important;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12) !important;
  }

  .pc-mcard {
    background: #ffffff;
    border: 1px solid var(--pc-border);
    border-radius: 12px;
    padding: 0.72rem 0.8rem;
    margin-bottom: 0.6rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }

  .pc-mcard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.45rem;
    margin-bottom: 0.5rem;
  }
  .pc-mcard-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem 0.75rem;
    font-size: 0.71rem;
    color: #64748b;
    margin-top: 0.35rem;
  }
  .pc-mcard-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.45rem;
    margin-top: 0.55rem;
    padding-top: 0.5rem;
    border-top: 1px dashed #e2e8f0;
  }
  .pc-mcard-actions .btn {
    min-height: 34px !important;
    padding: 0.3rem 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
}

@media (max-width: 575.98px) {
  .pc-kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  }
  .pc-kpi-card:last-child:nth-child(odd) {
    grid-column: 1 / -1 !important;
  }
}
</style>

<div class="pc-container">

  {{-- ══════════ TOP HEADER BAR ══════════ --}}
  <div class="pc-header-bar">
    <div class="pc-header-title">
      <i class="bi bi-gear-wide-connected pc-icon"></i>
      <h2>Production Batches</h2>
      <span class="pc-badge-pill">{{ count($entries) }} Batches Record</span>
    </div>

    <div class="d-flex align-items-center gap-2 pc-header-actions">
      <a href="{{ route('report.production') }}" class="pc-btn-back">
        <i class="bi bi-file-earmark-bar-graph me-1"></i> Production Report
      </a>

      <a href="{{ route('raw-materials.index') }}" class="pc-btn-back">
        <i class="bi bi-box-seam me-1"></i> Raw Materials
      </a>

      <a href="{{ route('production.create') }}" class="pc-btn-create">
        <i class="bi bi-plus-lg me-1"></i> + New Entry
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- ══════════ KPI SUMMARY CARDS ══════════ --}}
  <div class="pc-kpi-grid">
    <div class="pc-kpi-card">
      <div class="pc-kpi-info">
        <p>Total Batches</p>
        <h3>{{ number_format(count($entries)) }}</h3>
      </div>
      <div class="pc-kpi-icon blue">
        <i class="bi bi-layers-fill"></i>
      </div>
    </div>

    <div class="pc-kpi-card">
      <div class="pc-kpi-info">
        <p>Today's Batches</p>
        <h3>{{ $entries->filter(fn($e) => \Carbon\Carbon::parse($e->production_date)->isToday())->count() }}</h3>
      </div>
      <div class="pc-kpi-icon amber">
        <i class="bi bi-calendar2-check"></i>
      </div>
    </div>

    <div class="pc-kpi-card">
      <div class="pc-kpi-info">
        <p>Finished Items</p>
        <h3>{{ number_format($entries->sum('items_count')) }} Items</h3>
      </div>
      <div class="pc-kpi-icon purple">
        <i class="bi bi-box2-heart"></i>
      </div>
    </div>
  </div>

  {{-- ══════════ MAIN TABLE CARD ══════════ --}}
  <div class="pc-table-card">
    <div class="pc-table-header">
      <h3 class="pc-table-title">
        <i class="bi bi-list-task"></i> Production Log
      </h3>
    </div>

    <div class="table-responsive">
      <table class="pc-table" id="productionTable">
        <thead>
          <tr>
            <th>Batch #</th>
            <th>Date</th>
            <th>Source</th>
            <th>Items Produced</th>
            <th>Notes</th>
            <th>Created By</th>
            <th class="text-center" style="width: 160px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($entries as $e)
          <tr>
            <td data-order="{{ $e->id }}"><span class="fw-bold text-dark">#{{ $e->entry_no }}</span></td>
            <td data-order="{{ \Carbon\Carbon::parse($e->production_date)->format('Y-m-d') }}">
              <span class="font-monospace text-muted">
                {{ \Carbon\Carbon::parse($e->production_date)->format('d M Y') }}
              </span>
            </td>
            <td>
              @if($e->source === 'branch')
              <span class="pc-badge-source pc-badge-warehouse">
                <i class="bi bi-building me-1"></i>{{ active_branch_name() }}
              </span>
              @else
              <span class="pc-badge-source {{ $e->source === 'kitchen' ? 'pc-badge-kitchen' : 'pc-badge-warehouse' }}">
                <i class="bi bi-{{ $e->source === 'kitchen' ? 'house-door' : 'building' }} me-1"></i>
                {{ ucfirst($e->source) }}
              </span>
              @endif
            </td>
            <td>
              <div class="fw-semibold text-dark" title="{{ $e->product_details }}">
                {{ \Illuminate\Support\Str::limit($e->product_details, 55) }}
                <div class="small text-muted fw-normal"><i class="bi bi-box-seam me-1"></i> {{ $e->items_count }} item(s)</div>
              </div>
            </td>
            <td><span class="text-muted small">{{ $e->notes ?? '-' }}</span></td>
            <td><span class="fw-semibold text-secondary">{{ $e->user_name ?? 'System' }}</span></td>
            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="{{ route('production.edit', $e->id) }}" class="pc-btn-action" title="Edit Batch">
                  <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('production.gatepass', $e->id) }}" class="pc-btn-action text-primary" target="_blank" title="Gatepass Receipt">
                  <i class="bi bi-printer me-1"></i> Pass
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>
              No production entries recorded yet.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ══════════ MOBILE CARDS VIEW ══════════ --}}
  <div class="pc-mlist">
    @if($entries->count() > 0)
    <div class="pc-msearch-wrap">
      <i class="bi bi-search"></i>
      <input type="text" id="pcMobileSearch" class="pc-msearch" placeholder="Search entry #, product, source, date...">
    </div>
    @endif

    @forelse($entries as $e)
    <div class="pc-mcard" data-search="{{ strtolower($e->entry_no . ' ' . $e->production_date . ' ' . $e->source . ' ' . $e->product_details . ' ' . ($e->creator_name ?? '') . ' ' . ($e->notes ?? '')) }}">
      <div class="pc-mcard-header">
        <div>
          <span class="fw-bold text-dark" style="font-size: 0.88rem;">#{{ $e->entry_no }}</span>
          <div class="text-muted" style="font-size: 0.71rem;"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($e->production_date)->format('d M Y') }}</div>
        </div>
        <div>
          @if($e->source === 'branch')
          <span class="pc-badge-source pc-badge-warehouse" style="font-size:.68rem;">
            <i class="bi bi-building me-1"></i>Branch: {{ active_branch_name() }}
          </span>
          @else
          <span class="pc-badge-source {{ $e->source === 'kitchen' ? 'pc-badge-kitchen' : 'pc-badge-warehouse' }}" style="font-size:.68rem;">
            <i class="bi bi-{{ $e->source === 'kitchen' ? 'house-door' : 'building' }} me-1"></i>
            {{ ucfirst($e->source) }}
          </span>
          @endif
        </div>
      </div>

      <div style="font-size: 0.78rem; font-weight: 600; color: #0f172a; line-height: 1.38;">
        {{ $e->product_details }}
      </div>

      @if(!empty($e->creator_name) || !empty($e->notes))
      <div class="pc-mcard-meta">
        @if(!empty($e->creator_name))
        <span><i class="bi bi-person me-1"></i>{{ $e->creator_name }}</span>
        @endif
        @if(!empty($e->notes))
        <span class="text-truncate" style="max-width: 100%;"><i class="bi bi-chat-left-text me-1"></i>{{ $e->notes }}</span>
        @endif
      </div>
      @endif

      <div class="pc-mcard-actions">
        <a href="{{ route('production.edit', $e->id) }}" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('production.gatepass', $e->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
          <i class="bi bi-printer me-1"></i> Gatepass
        </a>
      </div>
    </div>
    @empty
    <div class="text-center py-4 text-muted bg-white rounded-3 border">
      <i class="bi bi-inbox fs-2"></i>
      <p class="mt-2 mb-0 small">No production entries recorded yet.</p>
    </div>
    @endforelse

    <div id="pcMobileNoMatch" class="text-center py-4 text-muted bg-white rounded-3 border" style="display:none;">
      <i class="bi bi-search fs-4"></i>
      <p class="mt-1 mb-0 small">No matching production entries found.</p>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('#productionTable').DataTable({
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [[1, 'desc'], [0, 'desc']],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search production entries...",
        emptyTable: "No production entries found"
      }
    });

    $('#pcMobileSearch').on('input', function() {
      const q = ($(this).val() || '').toLowerCase().trim();
      let visible = 0;
      $('.pc-mcard').each(function() {
        const hay = $(this).data('search') || '';
        const match = !q || hay.indexOf(q) !== -1;
        $(this).toggle(match);
        if (match) visible++;
      });
      $('#pcMobileNoMatch').toggle(visible === 0 && $('.pc-mcard').length > 0);
    });
  });
</script>
@endsection
