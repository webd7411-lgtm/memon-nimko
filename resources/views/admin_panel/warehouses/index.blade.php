@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --wh-bg: #f8fafc;
  --wh-surface: #ffffff;
  --wh-border: #e2e8f0;
  --wh-border-lt: #f1f5f9;
  --wh-text: #0f172a;
  --wh-text-sec: #475569;
  --wh-text-muted: #64748b;
  --wh-primary: #2563eb;
  --wh-primary-dark: #1d4ed8;
  --wh-radius: 16px;
  --wh-radius-sm: 10px;
  --wh-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --wh-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --wh-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.wh-page * {
  font-family: var(--wh-font);
}

.wh-page {
  background-color: var(--wh-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.wh-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--wh-radius);
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

.wh-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.wh-hero > * {
  position: relative;
  z-index: 1;
}

.wh-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.wh-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.wh-hero-icon {
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

.wh-hero-badge {
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

.wh-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.wh-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--wh-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.wh-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.wh-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.wh-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.wh-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.wh-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .wh-kpi-grid { grid-template-columns: 1fr; }
}

.wh-kpi-card {
  background: var(--wh-surface);
  border: 1px solid var(--wh-border);
  border-radius: var(--wh-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--wh-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.wh-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--wh-shadow-lg);
}

.wh-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--wh-text-muted);
}
.wh-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--wh-text);
  letter-spacing: -0.02em;
}

.wh-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.wh-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.wh-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.wh-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.wh-table-card {
  background: var(--wh-surface);
  border: 1px solid var(--wh-border);
  border-radius: var(--wh-radius);
  box-shadow: var(--wh-shadow-lg);
  overflow: hidden;
}

.wh-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--wh-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.wh-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--wh-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.wh-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.wh-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.wh-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--wh-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--wh-border);
  border-top: none;
  white-space: nowrap;
}

.wh-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--wh-border-lt);
  font-size: 0.88rem;
  color: var(--wh-text-sec);
}

.wh-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* ═══════ MODAL STYLING ═══════ */
.wh-modal .modal-content {
  border: none;
  border-radius: var(--wh-radius);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.wh-modal .modal-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  padding: 1.15rem 1.5rem;
  border-bottom: none;
}

.wh-modal .modal-title {
  font-weight: 800;
  font-size: 1.1rem;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.wh-modal .modal-body {
  padding: 1.5rem;
  background: #ffffff;
}

.wh-modal .modal-footer {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid var(--wh-border-lt);
}

.wh-input {
  border: 1px solid var(--wh-border);
  border-radius: var(--wh-radius-sm);
  padding: 0.6rem 0.9rem;
  font-size: 0.9rem;
  color: var(--wh-text);
  transition: all 0.2s ease;
  width: 100%;
}
.wh-input:focus {
  border-color: var(--wh-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.wh-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--wh-text-sec);
  margin-bottom: 0.4rem;
}

/* ═══════ FULL MOBILE RESPONSIVE — CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .wh-page { overflow-x: hidden !important; }

  .wh-hero { padding: 1rem 1rem !important; gap: .5rem; }
  .wh-hero-title h2 { font-size: 1.05rem !important; }
  .wh-hero-actions { width: 100%; display: grid !important; grid-template-columns: 1fr 1fr; gap: .4rem; }
  .wh-hero-actions .wh-btn { justify-content: center; width: 100%; min-height: 36px; font-size: .75rem; }
  .wh-table-header { padding: .9rem 1rem; }
  .wh-table-title { font-size: .95rem; }

  /* Kill scroll wrappers — higher specificity */
  .wh-page .wh-table-wrapper,
  .wh-page .dataTables_wrapper,
  .wh-page .dataTables_scrollBody,
  .wh-page .dataTables_scrollHead,
  .wh-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    padding: 0 !important;
  }

  .wh-page .wh-table,
  .wh-page .wh-table.dataTable {
    display: block !important;
    width: 100% !important;
    font-size: .75rem !important;
  }
  .wh-page .wh-table thead { display: none !important; }
  .wh-page .wh-table tbody { display: block !important; }
  .wh-page .wh-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .55rem;
    align-items: start;
    background: var(--wh-surface);
    border: 1px solid var(--wh-border) !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05), 0 2px 8px rgba(15,23,42,.05);
    padding: .55rem .65rem !important;
    margin-bottom: .55rem;
    overflow: hidden !important;
  }
  .wh-page .wh-table tbody tr:hover td { background: transparent !important; }
  .wh-page .wh-table tbody td {
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
    font-size: .75rem;
    line-height: 1.35;
    text-align: left !important;
  }
  .wh-page .wh-table tbody td::before {
    content: attr(data-label) ":";
    font-size: .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--wh-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .wh-page .wh-table tbody td:nth-child(1) { order: 0; justify-self: start; }
  .wh-page .wh-table tbody td:nth-child(1)::before { content: none; }
  .wh-page .wh-table tbody td:nth-child(1) {
    background: #eff6ff; color: #2563eb !important; border-radius: 6px;
    padding: .05rem .4rem !important; font-weight: 700 !important; font-size: .62rem;
  }
  .wh-page .wh-table tbody td:nth-child(3) { order: 1; grid-column: 1 / -1; }
  .wh-page .wh-table tbody td:nth-child(3)::before { content: none; }
  .wh-page .wh-table tbody td:nth-child(3) { font-weight: 600; font-size: .8rem; padding-top: 2px; }
  .wh-page .wh-table tbody td:nth-child(4) { order: 2; grid-column: 1 / -1; }
  .wh-page .wh-table tbody td:nth-child(2) { order: 3; }
  .wh-page .wh-table tbody td:nth-child(5) { order: 4; grid-column: 1 / -1; }
  .wh-page .wh-table tbody td:nth-child(6) { order: 5; grid-column: 1 / -1; }
  .wh-page .wh-table tbody td:nth-child(6)::before { content: none; }
  .wh-page .wh-table tbody td:nth-child(6) .btn { width: 100%; min-height: 34px; display: inline-flex; align-items: center; justify-content: center; }

  .wh-page .dataTables_filter, .wh-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .wh-page .dataTables_filter input, .wh-page .dataTables_length select {
    width: 100% !important; min-height: 36px; font-size: .78rem; border-radius: 8px;
    padding: .3rem .6rem; border: 1.5px solid var(--wh-border); margin-top: 4px;
  }
  .wh-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .wh-page .dataTables_paginate { text-align: center !important; padding: .5rem 0; }
  .wh-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 28px !important; height: 28px !important;
    padding: 0 .35rem !important; font-size: .68rem !important; margin: 0 1px !important;
    border-radius: 6px !important;
  }
}
</style>

<div class="wh-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="wh-hero">
      <div class="wh-hero-title">
        <div class="wh-hero-icon">
          <i class="bi bi-building"></i>
        </div>
        <div>
          <h2>Warehouse Directory</h2>
          <span class="wh-hero-badge">Storage Locations & Depots</span>
        </div>
      </div>

      <div class="wh-hero-actions">
        <button class="wh-btn wh-btn-primary" data-bs-toggle="modal" data-bs-target="#warehouseModal" onclick="clearWarehouse()">
          <i class="bi bi-plus-lg"></i> Add Warehouse
        </button>

        <a href="{{ url()->previous() }}" class="wh-btn wh-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalWarehouses = count($warehouses);
      $locationsCount = $warehouses->pluck('location')->filter()->unique()->count();
    @endphp

    <div class="wh-kpi-grid">
      <div class="wh-kpi-card">
        <div class="wh-kpi-info">
          <p>Total Warehouses</p>
          <h3>{{ number_format($totalWarehouses) }}</h3>
        </div>
        <div class="wh-kpi-icon blue">
          <i class="bi bi-building"></i>
        </div>
      </div>

      <div class="wh-kpi-card">
        <div class="wh-kpi-info">
          <p>Unique Locations</p>
          <h3>{{ number_format($locationsCount) }}</h3>
        </div>
        <div class="wh-kpi-icon green">
          <i class="bi bi-geo-alt"></i>
        </div>
      </div>

      <div class="wh-kpi-card">
        <div class="wh-kpi-info">
          <p>Managed Depots</p>
          <h3>Active</h3>
        </div>
        <div class="wh-kpi-icon amber">
          <i class="bi bi-shield-check"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="wh-table-card">
      <div class="wh-table-header">
        <h3 class="wh-table-title">
          <i class="bi bi-list-task text-primary me-1"></i> Registered Warehouses
        </h3>
      </div>

      <div class="wh-table-wrapper">
        <table class="table wh-table datanew">
          <thead>
            <tr>
              <th width="6%">#</th>
              <th width="20%">Created By</th>
              <th width="24%">Warehouse Name</th>
              <th width="22%">Location</th>
              <th width="18%">Remarks</th>
              <th width="10%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($warehouses as $key => $w)
            <tr>
              <td class="fw-bold" data-label="#">#{{ $key+1 }}</td>
              <td data-label="Created By">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width:30px;height:30px;font-size:0.75rem;">
                    {{ strtoupper(substr($w->user?->name ?? 'A', 0, 1)) }}
                  </div>
                  <span>{{ $w->user?->name ?? 'System' }}</span>
                </div>
              </td>
              <td data-label="Warehouse">
                <span class="fw-bold text-dark"><i class="bi bi-building me-1 text-primary"></i> {{ $w->warehouse_name }}</span>
              </td>
              <td data-label="Location">
                <span class="badge bg-light text-dark border px-2 py-1">
                  <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $w->location ?? 'N/A' }}
                </span>
              </td>
              <td data-label="Remarks">{{ $w->remarks ?? '—' }}</td>
              <td class="text-center" data-label="Action">
                <button class="btn btn-sm btn-primary edit-warehouse-btn rounded-2" style="background:#2563eb; border:none; color:#fff; font-weight:600; min-width:70px;"
                  data-id="{{ $w->id }}"
                  data-name="{{ $w->warehouse_name }}"
                  data-location="{{ $w->location }}"
                  data-remarks="{{ $w->remarks }}"
                  data-bs-toggle="modal"
                  data-bs-target="#warehouseModal">
                  <i class="bi bi-pencil-square me-1"></i> Edit
                </button>
                <a href="#" onclick="confirmWarehouseDelete('{{ $w->id }}'); return false;" class="btn btn-sm btn-danger rounded-2 ms-1" style="background:#ef4444; border:none; color:#fff; font-weight:600; min-width:70px;" title="Delete">
                  <i class="bi bi-trash-fill me-1"></i> Delete
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

{{-- ═══════ ADD/EDIT MODAL ═══════ --}}
<div class="modal fade wh-modal" id="warehouseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ url('warehouse/store') }}" method="POST" class="w-100">
      @csrf
      <input type="hidden" name="id" id="warehouse_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-building-add me-2 text-info"></i> Add / Edit Warehouse
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="wh-label">Warehouse Name <span class="text-danger">*</span></label>
            <input class="wh-input" name="warehouse_name" id="warehouse_name" placeholder="e.g. Latifabad Central Depot" required>
          </div>
          <div class="mb-2 d-none">
            <input class="wh-input" name="creater_id" id="" value="{{ Auth()->user()->id }}" required>
          </div>
          <div class="mb-3">
            <label class="wh-label">Location Address</label>
            <input class="wh-input" name="location" id="location" placeholder="e.g. Unit #6, Latifabad, Hyderabad">
          </div>
          <div class="mb-3">
            <label class="wh-label">Remarks / Description</label>
            <textarea class="wh-input" name="remarks" id="remarks" rows="3" placeholder="Optional notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="wh-btn wh-btn-glass text-dark border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="wh-btn wh-btn-primary">
            <i class="bi bi-check-lg me-1"></i> Save Warehouse
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function clearWarehouse() {
  $('#warehouse_id').val('');
  $('#warehouse_name').val('');
  $('#location').val('');
  $('#remarks').val('');
}

function confirmWarehouseDelete(id) {
  if (confirm('Are you sure you want to delete this warehouse? This action cannot be undone.')) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/warehouse/delete/' + id;
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrf);
    var method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }
}

$(document).on('click', '.edit-warehouse-btn', function () {
  $('#warehouse_id').val($(this).data('id'));
  $('#warehouse_name').val($(this).data('name'));
  $('#location').val($(this).data('location'));
  $('#remarks').val($(this).data('remarks'));
});

$(document).ready(function() {
  if ($.fn.DataTable.isDataTable('.datanew')) {
    $('.datanew').DataTable().destroy();
  }
  $('.datanew').DataTable({
    "pageLength": 25,
    "language": {
      "search": "_INPUT_",
      "searchPlaceholder": "Search warehouses..."
    }
  });

  // Prevent aria-hidden on focused element — move focus before modal closes
  $('.modal').on('hide.bs.modal', function() {
    $(document.body).focus();
  });
});
</script>
@endsection
