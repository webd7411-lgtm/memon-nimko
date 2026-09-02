@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --tb-bg: #f8fafc;
  --tb-surface: #ffffff;
  --tb-border: #e2e8f0;
  --tb-border-lt: #f1f5f9;
  --tb-text: #0f172a;
  --tb-text-sec: #475569;
  --tb-text-muted: #64748b;
  --tb-primary: #2563eb;
  --tb-radius: 16px;
  --tb-radius-sm: 10px;
  --tb-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --tb-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --tb-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.tb-page * {
  font-family: var(--tb-font);
}

.tb-page {
  background-color: var(--tb-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.tb-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--tb-radius);
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

.tb-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.tb-hero > * {
  position: relative;
  z-index: 1;
}

.tb-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.tb-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.tb-hero-icon {
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

.tb-hero-badge {
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

.tb-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--tb-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.tb-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.tb-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.tb-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.tb-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ KPI SUMMARY CARDS ═══════ */
.tb-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .tb-kpi-grid { grid-template-columns: 1fr; }
}

.tb-kpi-card {
  background: var(--tb-surface);
  border: 1px solid var(--tb-border);
  border-radius: var(--tb-radius);
  padding: 1.2rem 1.25rem;
  box-shadow: var(--tb-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.tb-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--tb-shadow-lg);
}

.tb-kpi-info p {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--tb-text-muted);
}
.tb-kpi-info h3 {
  margin: 0.25rem 0 0 0;
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--tb-text);
  letter-spacing: -0.02em;
}

.tb-kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.tb-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
.tb-kpi-icon.green { background: #ecfdf5; color: #10b981; }
.tb-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }

/* ═══════ MAIN DATA TABLE CARD ═══════ */
.tb-table-card {
  background: var(--tb-surface);
  border: 1px solid var(--tb-border);
  border-radius: var(--tb-radius);
  box-shadow: var(--tb-shadow-lg);
  overflow: hidden;
}

.tb-table-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--tb-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #ffffff;
}

.tb-table-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--tb-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.tb-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.tb-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.tb-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--tb-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--tb-border);
  border-top: none;
  white-space: nowrap;
}

.tb-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--tb-border-lt);
  font-size: 0.88rem;
  color: var(--tb-text-sec);
}

.tb-table tbody tr:hover td {
  background-color: #f8fafc;
}

/* ═══════ MODAL STYLING ═══════ */
.tb-modal .modal-content {
  border: none;
  border-radius: var(--tb-radius);
  box-shadow: var(--tb-shadow-lg);
  overflow: hidden;
}

.tb-modal .modal-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}

.tb-modal .modal-title {
  font-weight: 700;
  font-size: 1.15rem;
}

.tb-input {
  border: 1px solid var(--tb-border);
  border-radius: var(--tb-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--tb-text);
  width: 100%;
}
.tb-input:focus {
  border-color: var(--tb-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

/* ═══════ FULL MOBILE RESPONSIVE — CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .tb-page { overflow-x: hidden !important; }

  .tb-hero { padding: 1rem 1rem !important; gap: .5rem; }
  .tb-hero-title h2 { font-size: 1.05rem !important; }
  .tb-hero > div:last-child { width: 100%; display: grid !important; grid-template-columns: 1fr 1fr; gap: .4rem; }
  .tb-hero .tb-btn { justify-content: center; width: 100%; min-height: 36px; font-size: .75rem; padding: .45rem .6rem; }
  .tb-table-header { padding: .9rem 1rem; }
  .tb-table-title { font-size: .95rem; }

  /* Kill scroll wrappers — higher specificity */
  .tb-page .tb-table-wrapper,
  .tb-page .dataTables_wrapper,
  .tb-page .dataTables_scrollBody,
  .tb-page .dataTables_scrollHead,
  .tb-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    padding: 0 !important;
  }

  .tb-page .tb-table,
  .tb-page .tb-table.dataTable {
    display: block !important;
    width: 100% !important;
    font-size: .75rem !important;
  }
  .tb-page .tb-table thead { display: none !important; }
  .tb-page .tb-table tbody { display: block !important; }
  .tb-page .tb-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .55rem;
    align-items: start;
    background: var(--tb-surface);
    border: 1px solid var(--tb-border) !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05), 0 2px 8px rgba(15,23,42,.05);
    padding: .55rem .65rem !important;
    margin-bottom: .55rem;
    overflow: hidden !important;
  }
  .tb-page .tb-table tbody tr:hover td { background: transparent !important; }
  .tb-page .tb-table tbody td {
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
  .tb-page .tb-table tbody td::before {
    content: attr(data-label) ":";
    font-size: .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--tb-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .tb-page .tb-table tbody td:nth-child(1) { order: 0; justify-self: start; }
  .tb-page .tb-table tbody td:nth-child(1)::before { content: none; }
  .tb-page .tb-table tbody td:nth-child(1) {
    background: #eff6ff; color: #2563eb !important; border-radius: 6px;
    padding: .05rem .4rem !important; font-weight: 700 !important; font-size: .62rem;
  }
  .tb-page .tb-table tbody td:nth-child(2) { order: 1; grid-column: 1 / -1; }
  .tb-page .tb-table tbody td:nth-child(2)::before { content: none; }
  .tb-page .tb-table tbody td:nth-child(2) { font-weight: 600; font-size: .8rem; padding-top: 2px; }
  .tb-page .tb-table tbody td:nth-child(3) { order: 2; }
  .tb-page .tb-table tbody td:nth-child(4) { order: 3; grid-column: 1 / -1; }
  .tb-page .tb-table tbody td:nth-child(4)::before { content: none; }
  .tb-page .tb-table tbody td:nth-child(4) .d-flex { display: grid !important; grid-template-columns: 1fr 1fr; gap: .4rem; width: 100%; }
  .tb-page .tb-table tbody td:nth-child(4) .btn { width: 100%; min-height: 34px; display: inline-flex; align-items: center; justify-content: center; font-size: .7rem; padding: .2rem .4rem; }

  .tb-page .dataTables_filter, .tb-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .tb-page .dataTables_filter input, .tb-page .dataTables_length select {
    width: 100% !important; min-height: 36px; font-size: .78rem; border-radius: 8px;
    padding: .3rem .6rem; border: 1.5px solid var(--tb-border); margin-top: 4px;
  }
  .tb-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .tb-page .dataTables_paginate { text-align: center !important; padding: .5rem 0; }
  .tb-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 28px !important; height: 28px !important;
    padding: 0 .35rem !important; font-size: .68rem !important; margin: 0 1px !important;
    border-radius: 6px !important;
  }
}
</style>

<div class="tb-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="tb-hero">
      <div class="tb-hero-title">
        <div class="tb-hero-icon">
          <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div>
          <h2>Dining & Service Tables</h2>
          <span class="tb-hero-badge">Seating Management & Realtime Status</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="tb-btn tb-btn-primary" data-bs-toggle="modal" data-bs-target="#tableModal" id="reset">
          <i class="bi bi-plus-lg me-1"></i> Create Table
        </button>

        <a href="{{ url()->previous() }}" class="tb-btn tb-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ KPI SUMMARY CARDS ═══════ --}}
    @php
      $totalTablesCount = count($tables);
      $availableCount = $tables->where('status', 'available')->count();
      $occupiedCount = $tables->where('status', 'occupied')->count();
      $reservedCount = $tables->where('status', 'reserved')->count();
    @endphp

    <div class="tb-kpi-grid">
      <div class="tb-kpi-card">
        <div class="tb-kpi-info">
          <p>Total Tables</p>
          <h3>{{ number_format($totalTablesCount) }}</h3>
        </div>
        <div class="tb-kpi-icon blue">
          <i class="bi bi-ui-checks-grid"></i>
        </div>
      </div>

      <div class="tb-kpi-card">
        <div class="tb-kpi-info">
          <p>Available Tables</p>
          <h3>{{ number_format($availableCount) }}</h3>
        </div>
        <div class="tb-kpi-icon green">
          <i class="bi bi-check-circle-fill"></i>
        </div>
      </div>

      <div class="tb-kpi-card">
        <div class="tb-kpi-info">
          <p>Occupied / Reserved</p>
          <h3>{{ number_format($occupiedCount + $reservedCount) }}</h3>
        </div>
        <div class="tb-kpi-icon amber">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN TABLE CARD ═══════ --}}
    <div class="tb-table-card">
      <div class="tb-table-header">
        <h3 class="tb-table-title">
          <i class="bi bi-list-nested text-primary me-1"></i> Table Configuration
        </h3>
      </div>

      <div class="tb-table-wrapper">
        <table id="default-datatable" class="table tb-table align-middle">
          <thead>
            <tr>
              <th width="10%" class="text-center">ID</th>
              <th width="45%" class="text-start">Table Name</th>
              <th width="25%" class="text-center">Status</th>
              <th width="20%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($tables as $table)
            <tr>
              <td class="id text-center fw-bold" data-label="#">#{{ $table->id }}</td>
              <td class="name text-start fw-bold text-dark" data-label="Table">
                <i class="bi bi-shop me-2 text-primary"></i>{{ $table->table_name }}
              </td>
              <td class="status text-center" data-label="Status">
                @if($table->status == 'available')
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Available
                  </span>
                @elseif($table->status == 'occupied')
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold">
                    <i class="bi bi-dash-circle me-1"></i> Occupied
                  </span>
                @else
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-bold">
                    <i class="bi bi-clock me-1"></i> Reserved
                  </span>
                @endif
              </td>
              <td class="text-center" data-label="Action">
                <div class="d-flex align-items-center justify-content-center gap-1">
                  <button class="btn btn-sm btn-outline-primary rounded-2 edit-btn" title="Edit Table">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </button>
                  <button class="btn btn-sm btn-outline-danger rounded-2 delete-btn"
                    data-url="{{ route('table.delete', $table->id) }}"
                    data-msg="Are you sure you want to delete this table?"
                    data-method="DELETE"
                    onclick="logoutAndDeleteFunction(this)">
                    <i class="bi bi-trash me-1"></i> Delete
                  </button>
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

{{-- ═══════ MODAL DIALOG ═══════ --}}
<div class="modal fade tb-modal" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tableModalLabel">
          <i class="bi bi-grid-plus me-1 text-primary"></i> Add / Edit Table
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="myform" action="{{ route('table.store') }}" method="POST">
        <div class="modal-body p-4">
          @csrf
          <input type="hidden" name="edit_id" id="edit_id" />
          <div class="mb-3">
            <label for="table_name" class="form-label fw-bold text-dark small text-uppercase">Table Name / Number</label>
            <input type="text" name="table_name" class="tb-input" id="table_name" placeholder="e.g. Table 1, VIP 2" required />
          </div>
          <div class="mb-3">
            <label for="status_select" class="form-label fw-bold text-dark small text-uppercase">Table Status</label>
            <select name="status" id="status_select" class="tb-input">
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="reserved">Reserved</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-light border fw-semibold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="tb-btn tb-btn-primary save-btn">
            <i class="bi bi-save me-1"></i> Save Table
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('submit', '.myform', function(e) {
    e.preventDefault();
    var formdata = new FormData(this);
    var url = $(this).attr('action');
    var method = $(this).attr('method');
    $(this).find(':submit').attr('disabled', true);
    myAjax(url, formdata, method);
});

$(document).on('click', '.edit-btn', function() {
    var tr = $(this).closest("tr");
    var id = tr.find(".id").text().replace('#', '').trim();
    var name = tr.find(".name").text().trim();
    var status = tr.find(".status").text().trim().toLowerCase();
    
    $('#edit_id').val(id);
    $('#table_name').val(name);
    $('#status_select').val(status);
    $("#tableModal").modal("show");
});

$('#reset').click(function() {
    $('#edit_id').val('');
    $('#table_name').val('');
    $('#status_select').val('available');
});

// Prevent aria-hidden on focused element — move focus before modal closes
$(document).on('hide.bs.modal', '.modal', function() {
    $(document.body).focus();
});
</script>
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#default-datatable')) {
        $('#default-datatable').DataTable().destroy();
    }
    $('#default-datatable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "order": [
            [0, 'desc']
        ],
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search tables..."
        }
    });
});
</script>
@endsection
