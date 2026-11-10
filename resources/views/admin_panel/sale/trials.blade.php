@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --sl-bg: #f8fafc;
  --sl-surface: #ffffff;
  --sl-border: #e2e8f0;
  --sl-border-lt: #f1f5f9;
  --sl-text: #0f172a;
  --sl-text-sec: #475569;
  --sl-text-muted: #64748b;
  --sl-primary: #d97706;
  --sl-radius: 12px;
  --sl-radius-sm: 8px;
  --sl-font: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.trials-page *:not(i):not([class*="bi"]):not([class*="fa"]):not([class*="ft"]) {
  font-family: var(--sl-font);
}

.trials-page {
  background-color: var(--sl-bg);
  min-height: calc(100vh - 60px);
  padding: 0.5rem 0.85rem 1.5rem;
}

/* ═══════ HERO HEADER ═══════ */
.trials-hero {
  position: relative;
  background: linear-gradient(135deg, #78350f 0%, #92400e 50%, #b45309 100%);
  border-radius: var(--sl-radius);
  padding: 0.65rem 1.1rem;
  margin-bottom: 0.65rem;
  box-shadow: 0 8px 20px -4px rgba(180, 83, 9, 0.25);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.65rem;
  color: #ffffff;
}

.trials-hero-title {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.trials-hero-icon {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.trials-hero-title h2 {
  font-size: 1.18rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.01em;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.trials-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  flex-wrap: wrap;
}

.trials-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 0.42rem 0.85rem;
  border-radius: 8px;
  text-decoration: none !important;
  transition: all 0.15s ease;
  border: 1px solid transparent;
}

.trials-btn-primary {
  background: #ffffff;
  color: #92400e !important;
}
.trials-btn-primary:hover {
  background: #fef3c7;
  transform: translateY(-1px);
}

.trials-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  border-color: rgba(255, 255, 255, 0.2);
}
.trials-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
}

/* ═══════ KPI METRIC CARDS ═══════ */
.trials-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
  gap: 0.65rem;
  margin-bottom: 0.75rem;
}

.trials-kpi-card {
  background: #ffffff;
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.trials-kpi-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}

.trials-kpi-amber .trials-kpi-icon { background: #fffbeb; color: #d97706; }
.trials-kpi-red .trials-kpi-icon { background: #fef2f2; color: #dc2626; }
.trials-kpi-blue .trials-kpi-icon { background: #eff6ff; color: #2563eb; }

.trials-kpi-info {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.trials-kpi-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sl-text-muted);
}

.trials-kpi-value {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--sl-text);
  margin-top: 2px;
}

.trials-kpi-sub {
  font-size: 0.66rem;
  color: var(--sl-text-sec);
  font-weight: 600;
}

/* ═══════ TABLE WRAPPER ═══════ */
.trials-table-wrapper {
  background: #ffffff;
  border: 1px solid var(--sl-border);
  border-radius: var(--sl-radius);
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  padding: 0.85rem;
}

.trials-table thead th {
  background: #f8fafc;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--sl-text-muted);
  padding: 0.65rem 0.55rem;
  border-bottom: 1.5px solid var(--sl-border);
  border-top: none;
  white-space: nowrap;
  vertical-align: middle;
}

.trials-table tbody td {
  padding: 0.55rem 0.55rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--sl-border-lt);
  font-size: 0.82rem;
  color: var(--sl-text-sec);
  background: #ffffff;
}

.trials-table tbody tr:hover td {
  background-color: #fffdf5;
}
</style>

<div class="trials-page">
  <div class="container-fluid px-1 px-md-2">

    {{-- HERO HEADER --}}
    <div class="trials-hero">
      <div class="trials-hero-title">
        <div class="trials-hero-icon">
          <i class="fas fa-gift"></i>
        </div>
        <div>
          <h2>Product Trials & Free Samples <span class="badge bg-warning text-dark border ms-1" style="font-size: 10px; font-weight: 800;">Trade Offer Only</span></h2>
          <small class="text-white-50" style="font-size: 11px;">Customer trial pieces (Net Bill: Rs. 0.00 | Cost Price Auto Expense Voucher Linked)</small>
        </div>
      </div>

      <div class="trials-hero-actions">
        <a href="{{ route('sale.screen') }}" class="trials-btn trials-btn-primary">
          <i class="fas fa-plus-circle me-1"></i> Issue Product Trial
        </a>

        <a href="{{ route('sale.index') }}" class="trials-btn trials-btn-glass">
          <i class="fas fa-file-invoice-dollar me-1"></i> Regular Sales Register
        </a>

        <button type="button" id="btnToggleFilter" class="trials-btn trials-btn-glass">
          <i class="fas fa-filter me-1"></i> Filters
        </button>

        <a href="{{ url()->previous() }}" class="trials-btn trials-btn-glass">
          <i class="fas fa-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- KPI METRICS --}}
    <div class="trials-kpi-grid">
      <div class="trials-kpi-card trials-kpi-amber">
        <div class="trials-kpi-icon">
          <i class="fas fa-boxes"></i>
        </div>
        <div class="trials-kpi-info">
          <span class="trials-kpi-label">Total Trial Dispatches</span>
          <span class="trials-kpi-value">{{ number_format($totalTrialsCount ?? 0) }}</span>
          <span class="trials-kpi-sub">Trial / Sampling Orders</span>
        </div>
      </div>

      <div class="trials-kpi-card trials-kpi-red">
        <div class="trials-kpi-icon">
          <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="trials-kpi-info">
          <span class="trials-kpi-label">Company Cost Expense (COGS)</span>
          <span class="trials-kpi-value">PKR {{ number_format($totalExpenseValue ?? 0, 2) }}</span>
          <span class="trials-kpi-sub">Option B: Cost Price Valuation</span>
        </div>
      </div>

      <div class="trials-kpi-card trials-kpi-blue">
        <div class="trials-kpi-icon">
          <i class="fas fa-book"></i>
        </div>
        <div class="trials-kpi-info">
          <span class="trials-kpi-label">Expense Head</span>
          <span class="trials-kpi-value" style="font-size: 0.95rem;">Trade Promotion</span>
          <span class="trials-kpi-sub">Account: ACC-CLAIM-TRADE-01</span>
        </div>
      </div>
    </div>

    {{-- FILTER BAR (Collapsible) --}}
    <div class="card mb-3 border-0 shadow-sm d-none" id="filterCard" style="border-radius: var(--sl-radius);">
      <div class="card-body py-2 px-3">
        <div class="row g-2 align-items-end">
          <div class="col-md-3 col-6">
            <label class="form-label small fw-bold text-muted mb-1">From Date</label>
            <input type="date" id="filter_from_date" class="form-control form-control-sm">
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label small fw-bold text-muted mb-1">To Date</label>
            <input type="date" id="filter_to_date" class="form-control form-control-sm">
          </div>
          <div class="col-md-3 col-12 d-flex gap-2">
            <button type="button" id="btnApplyFilter" class="btn btn-warning btn-sm fw-bold flex-grow-1"><i class="fas fa-search me-1"></i> Filter</button>
            <button type="button" id="btnResetFilter" class="btn btn-light btn-sm border"><i class="fas fa-undo"></i></button>
          </div>
        </div>
      </div>
    </div>

    {{-- TABLE WRAPPER --}}
    <div class="trials-table-wrapper">
      <div class="table-responsive">
        <table id="trialsTable" class="table table-hover trials-table w-100">
          <thead>
            <tr>
              <th style="width: 40px;" class="text-center">#</th>
              <th>Ref / Invoice #</th>
              <th>Date & Time</th>
              <th>Customer / Recipient</th>
              <th>Issued By</th>
              <th>Trial Items & Pieces</th>
              <th class="text-end">Expense Value (Cost)</th>
              <th class="text-center">Expense Voucher</th>
              <th class="text-center" style="width: 140px;">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>
</div>

{{-- MODAL FOR DETAILS --}}
<div class="modal fade erp-modal" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
      <div class="modal-header bg-dark text-white py-2.5 px-3">
        <h6 class="modal-title fw-bold"><i class="fas fa-gift text-warning me-2"></i>Product Trial Details</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3" id="saleDetailsModalBody">
        <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i><p class="mt-2 text-muted">Loading Details...</p></div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const table = $('#trialsTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            url: "{{ route('sale.trials') }}",
            data: function(d) {
                d.from_date = $('#filter_from_date').val();
                d.to_date = $('#filter_to_date').val();
            }
        },
        columns: [
            { data: 0, className: 'text-center fw-bold text-muted' },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, className: 'text-end' },
            { data: 7, className: 'text-center' },
            { data: 8, className: 'text-center' }
        ],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by Invoice, Customer, EVID...",
            processing: '<div class="spinner-border text-warning spinner-border-sm" role="status"></div> Loading...'
        }
    });

    $('#btnToggleFilter').on('click', function() {
        $('#filterCard').toggleClass('d-none');
    });

    $('#btnApplyFilter').on('click', function() {
        table.draw();
    });

    $('#btnResetFilter').on('click', function() {
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        table.draw();
    });

    // View Details
    $(document).on('click', '.view-sale-details-btn', function() {
        const saleId = $(this).data('sale-id');
        const modalBody = $('#saleDetailsModalBody');
        modalBody.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i><p class="mt-2 text-muted">Loading Details...</p></div>');
        $('#saleDetailsModal').modal('show');

        $.ajax({
            url: `/sales/${saleId}/details`,
            type: 'GET',
            success: function(resp) {
                if (resp.success) {
                    let itemsHtml = '';
                    const items = resp.items || [];
                    items.forEach((item, idx) => {
                        itemsHtml += `<tr>
                            <td class="text-center">${idx + 1}</td>
                            <td class="fw-bold">${item.name} <br><small class="text-muted">${item.code || ''}</small></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">${item.trade_offer || item.qty} Pcs</span></td>
                            <td class="text-end">PKR ${parseFloat(item.price || 0).toFixed(2)}</td>
                            <td class="text-end fw-bold">Free (Trial)</td>
                        </tr>`;
                    });

                    modalBody.html(`
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Customer</small>
                                    <strong>${resp.sale.customer_display_name || 'N/A'}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Invoice No & Date</small>
                                    <strong>${resp.sale.invoice_no}</strong> <span class="text-muted">(${resp.sale.created_at})</span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Product</th>
                                        <th class="text-center">Trial Pieces</th>
                                        <th class="text-end">Standard Rate</th>
                                        <th class="text-end">Customer Payable</th>
                                    </tr>
                                </thead>
                                <tbody>${itemsHtml}</tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-warning-subtle border border-warning mt-2">
                            <span><i class="fas fa-receipt me-1"></i> <strong>Expense Voucher:</strong> ${resp.fin.trade_offer_voucher_evid || 'Auto-created'}</span>
                            <span class="text-danger fw-bold">Cost Expense: PKR ${parseFloat(resp.fin.trade_offer_amount || 0).toFixed(2)}</span>
                        </div>
                    `);
                }
            },
            error: function() {
                modalBody.html('<div class="alert alert-danger">Error loading details.</div>');
            }
        });
    });

    // Delete Sale / Trial
    $(document).on('click', '.delete-sale-btn', function() {
        const url = $(this).data('url');
        Swal.fire({
            title: 'Delete Trial Dispatch?',
            text: 'This will restore the stock pieces and remove the linked Expense Voucher!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(resp) {
                        toastr.success('Trial record and linked expense voucher removed successfully.');
                        table.draw();
                    },
                    error: function(err) {
                        toastr.error('Failed to delete trial record.');
                    }
                });
            }
        });
    });
});
</script>
@endsection
