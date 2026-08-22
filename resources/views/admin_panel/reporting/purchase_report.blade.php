@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --rp-bg: #f8fafc;
  --rp-surface: #ffffff;
  --rp-border: #e2e8f0;
  --rp-border-lt: #f1f5f9;
  --rp-text: #0f172a;
  --rp-text-sec: #475569;
  --rp-text-muted: #64748b;
  --rp-primary: #2563eb;
  --rp-radius: 16px;
  --rp-radius-sm: 10px;
  --rp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --rp-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --rp-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.rp-page * {
  font-family: var(--rp-font);
}

.rp-page {
  background-color: var(--rp-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.rp-hdr {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--rp-radius);
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

.rp-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.rp-hdr > * {
  position: relative;
  z-index: 1;
}

.rp-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.rp-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.rp-hdr-icon {
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

.rp-hdr-badge {
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

.rp-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--rp-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.rp-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.rp-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.rp-btn-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
}
.rp-btn-danger:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45);
}

.rp-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.rp-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ CARD & CONTROLS ═══════ */
.rp-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.rp-card-body { padding: 1.35rem; }

.rp-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--rp-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.rp-input {
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--rp-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.rp-input:focus {
  border-color: var(--rp-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

/* ═══════ DATA TABLE ═══════ */
.rp-table-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow-lg);
  overflow: hidden;
}

.rp-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.rp-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.rp-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--rp-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--rp-border);
  border-top: none;
  white-space: nowrap;
}

.rp-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--rp-border-lt);
  font-size: 0.88rem;
  color: var(--rp-text-sec);
}

.rp-table tbody tr:hover td {
  background-color: #f8fafc;
}

.rp-table tbody tr.fw-bold td {
  background-color: #eff6ff !important;
  color: var(--rp-text) !important;
  font-weight: 800;
}

.rp-loader { display: none; text-align: center; padding: 1.5rem 0; }
.rp-loader .spinner-border { color: var(--rp-primary); width: 2.2rem; height: 2.2rem; }

/* Grand total row — premium dark strip (desktop) */
.rp-table tbody tr.rp-total-row {
  background: #0f172a !important;
}
.rp-table tbody tr.rp-total-row td {
  background: transparent !important;
  color: #ffffff !important;
  font-weight: 700;
}
.rp-table tbody tr.rp-total-row .text-success,
.rp-table tbody tr.rp-total-row .text-danger {
  color: #ffffff !important;
}

/* ═══════ FULL MOBILE RESPONSIVE — PREMIUM CARD BOXES ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .rp-page, .rp-page * { max-width: 100%; box-sizing: border-box; }
  .rp-page { overflow-x: hidden !important; }

  .rp-hdr { padding: 1.05rem 1rem !important; gap: .6rem; }
  .rp-hdr-title { gap: .6rem; }
  .rp-hdr-title h2 { font-size: 1.1rem !important; }
  .rp-hdr-icon { width: 40px; height: 40px; font-size: 1.15rem; }
  .rp-hdr-badge { font-size: .65rem; padding: .22rem .7rem; }
  .rp-btn { font-size: .8rem; padding: .5rem .9rem; }
  .rp-card-body { padding: 1rem; }

  /* Kill all scroll wrappers */
  .rp-table-wrapper,
  .rp-page .dataTables_wrapper,
  .rp-page .dataTables_scrollHead,
  .rp-page .dataTables_scrollBody,
  .rp-page .dataTables_scroll,
  .rp-page .table-responsive {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .rp-table { display: block !important; width: 100% !important; }
  .rp-table thead { display: none !important; }
  .rp-table tbody { display: block !important; width: 100%; }

  /* Row → Premium box */
  .rp-table tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    grid-auto-rows: auto;
    column-gap: .55rem;
    row-gap: .35rem;
    align-items: center;
    background: #ffffff;
    border: 1px solid var(--rp-border) !important;
    border-radius: 14px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 7px 20px rgba(15,23,42,.07);
    padding: .8rem .75rem !important;
    margin-bottom: .7rem;
    overflow: hidden !important;
  }
  .rp-table tbody tr:hover td { background: transparent !important; }
  .rp-table tbody tr.fw-bold td { background: transparent !important; }

  .rp-table tbody td {
    display: flex !important;
    flex-direction: column;
    align-items: flex-start;
    gap: 1px;
    border: none !important;
    padding: .05rem 0 !important;
    min-width: 0;
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: anywhere;
    font-size: .74rem;
    line-height: 1.4;
    text-align: left !important;
  }
  .rp-table tbody td::before {
    content: attr(data-label);
    font-size: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--rp-text-muted);
    white-space: nowrap;
  }

  .rp-table tbody tr > td:nth-child(1) {
    grid-column: 1; grid-row: 1;
    justify-self: start;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff !important;
    border-radius: 8px;
    padding: .2rem .55rem !important;
    font-weight: 800 !important;
    font-size: .66rem;
    box-shadow: 0 3px 8px rgba(37,99,235,.35);
  }
  .rp-table tbody tr > td:nth-child(1)::before { content: none; }
  .rp-table tbody tr > td:nth-child(2) { grid-column: 2; grid-row: 1; justify-self: end; }
  .rp-table tbody tr > td:nth-child(2)::before { content: none; }
  .rp-table tbody tr > td:nth-child(3) { grid-column: 1; grid-row: 2; }
  .rp-table tbody tr > td:nth-child(4) { grid-column: 2; grid-row: 2; }
  .rp-table tbody tr > td:nth-child(5) {
    grid-column: 1 / -1; grid-row: 3;
    font-weight: 800 !important; font-size: .85rem !important; color: var(--rp-text) !important;
  }
  .rp-table tbody tr > td:nth-child(5)::before { content: none; }
  .rp-table tbody tr > td:nth-child(7) {
    grid-column: 1 / -1; grid-row: 4;
    font-weight: 700 !important; font-size: .8rem !important;
  }
  .rp-table tbody tr > td:nth-child(6) { grid-column: 1; grid-row: 5; }
  .rp-table tbody tr > td:nth-child(8) { grid-column: 2; grid-row: 5; font-weight: 700 !important; }
  .rp-table tbody tr > td:nth-child(9) { grid-column: 1; grid-row: 6; }
  .rp-table tbody tr > td:nth-child(10) { grid-column: 2; grid-row: 6; }
  .rp-table tbody tr > td:nth-child(11) { grid-column: 1; grid-row: 7; }
  .rp-table tbody tr > td:nth-child(12) { grid-column: 2; grid-row: 7; font-weight: 700 !important; }
  .rp-table tbody tr > td:nth-child(13) { grid-column: 1; grid-row: 8; }
  .rp-table tbody tr > td:nth-child(14) { grid-column: 2; grid-row: 8; }
  .rp-table tbody tr > td:nth-child(15) { grid-column: 1; grid-row: 9; }
  .rp-table tbody tr > td:nth-child(16) { grid-column: 2; grid-row: 9; font-weight: 800 !important; color: #059669 !important; }
  .rp-table tbody tr > td:nth-child(17) { grid-column: 1; grid-row: 10; }
  .rp-table tbody tr > td:nth-child(18) { grid-column: 2; grid-row: 10; font-weight: 800 !important; color: #dc2626 !important; }

  /* Grand total box */
  .rp-table tbody tr.rp-total-row {
    grid-template-columns: 1fr 1fr;
    background: linear-gradient(135deg, #0f172a, #1e3a8a) !important;
    border: none !important;
    color: #ffffff !important;
    padding: .9rem .8rem !important;
    margin-top: .3rem;
  }
  .rp-table tbody tr.rp-total-row td {
    color: #ffffff !important;
    font-size: .78rem !important;
  }
  .rp-table tbody tr.rp-total-row td::before {
    content: attr(data-label);
    color: rgba(255,255,255,.65) !important;
    font-size: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
  }
  .rp-table tbody tr.rp-total-row td:nth-child(1) {
    grid-column: 1 / -1; grid-row: 1;
    font-weight: 800; font-size: .85rem !important;
    text-transform: uppercase; letter-spacing: .5px;
  }
  .rp-table tbody tr.rp-total-row td:nth-child(1)::before { content: none; }
  .rp-table tbody tr.rp-total-row td:nth-child(2) { grid-column: 1; grid-row: 2; }
  .rp-table tbody tr.rp-total-row td:nth-child(3) { grid-column: 2; grid-row: 2; }
  .rp-table tbody tr.rp-total-row td:nth-child(4) { grid-column: 1; grid-row: 3; }
  .rp-table tbody tr.rp-total-row td:nth-child(5) { grid-column: 2; grid-row: 3; }
  .rp-table tbody tr.rp-total-row td:nth-child(6) { grid-column: 1; grid-row: 4; }
  .rp-table tbody tr.rp-total-row td:nth-child(7) { grid-column: 2; grid-row: 4; }

  /* DataTables controls */
  .rp-page .dataTables_filter, .rp-page .dataTables_length {
    width: 100% !important; float: none !important; text-align: left !important; margin-bottom: .5rem;
  }
  .rp-page .dataTables_filter input, .rp-page .dataTables_length select {
    width: 100% !important; max-width: 100%; min-height: 38px; font-size: .8rem;
    border-radius: 10px; padding: .35rem .6rem; border: 1.5px solid var(--rp-border); margin-top: 4px;
  }
  .rp-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .rp-page .dataTables_paginate { text-align: center !important; padding: .6rem 0; }
  .rp-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 30px !important; height: 30px !important;
    align-items: center; justify-content: center;
    padding: 0 .4rem !important; font-size: .7rem !important; margin: 0 1px !important;
    border-radius: 8px !important; border: none !important;
  }
}
</style>

<div class="rp-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="rp-hdr">
      <div class="rp-hdr-title">
        <div class="rp-hdr-icon">
          <i class="bi bi-bag-check-fill"></i>
        </div>
        <div>
          <h2>Purchase Report</h2>
          <span class="rp-hdr-badge">Vendor Purchases & Inward Items</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="rp-btn rp-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="rp-card">
      <div class="rp-card-body">
        <form id="purchaseFilterForm" class="row g-3 align-items-end">
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
            <input type="date" name="start_date" id="start_date" class="rp-input" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
            <input type="date" name="end_date" id="end_date" class="rp-input" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-12 col-md-3">
            <button type="button" id="btnSearch" class="rp-btn rp-btn-primary w-100">
              <i class="bi bi-search me-1"></i> Generate Report
            </button>
          </div>
          <div class="col-12 col-md-3 text-end">
            <button type="button" id="btnExportCsv" class="rp-btn rp-btn-danger w-100">
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══════ DATA TABLE CARD ═══════ --}}
    <div class="rp-table-card">
      <div class="rp-card-body p-0">
        <div id="loader" class="rp-loader">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2 text-muted small">Loading purchase dataset...</p>
        </div>

        <div class="rp-table-wrapper">
          <table id="purchaseTable" class="table rp-table align-middle nowrap" style="width:100%;">
            <thead>
              <tr>
                <th width="4%">#</th>
                <th width="8%">Source</th>
                <th width="10%">Purchase Date</th>
                <th width="10%">Invoice No</th>
                <th width="15%">Vendor</th>
                <th width="8%">Item Code</th>
                <th width="15%">Item Name</th>
                <th width="5%">Qty</th>
                <th width="5%">Unit</th>
                <th width="8%">Price</th>
                <th width="8%">Item Disc.</th>
                <th width="10%">Line Total</th>
                <th width="10%">Subtotal</th>
                <th width="8%">Discount</th>
                <th width="8%">Extra Cost</th>
                <th width="10%">Net Amount</th>
                <th width="10%">Paid Amount</th>
                <th width="10%">Due Amount</th>
              </tr>
            </thead>
            <tbody id="reportBody"></tbody>
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
    var purchaseTable = $('#purchaseTable').DataTable({
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        columns: [
            { data: 'index' },
            { data: 'source_type' },
            { data: 'purchase_date' },
            { data: 'invoice_no' },
            { data: 'vendor_name' },
            { data: 'item_code' },
            { data: 'item_name' },
            { data: 'qty' },
            { data: 'unit' },
            { data: 'price' },
            { data: 'item_discount' },
            { data: 'line_total' },
            { data: 'subtotal' },
            { data: 'discount' },
            { data: 'extra_cost' },
            { data: 'net_amount' },
            { data: 'paid_amount' },
            { data: 'due_amount' }
        ]
    });

    function renderRows(rows) {
        if ($.fn.DataTable.isDataTable('#purchaseTable')) {
            purchaseTable.clear().draw();
        }

        let tableContent = '';
        let grandSubtotal = 0;
        let grandDiscount = 0;
        let grandExtraCost = 0;
        let grandNet = 0;
        let grandPaid = 0;
        let grandDue = 0;

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const day = d.getDate();
            const month = d.getMonth() + 1;
            const year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        rows.forEach(function(r, idx) {
            tableContent += `<tr>
                <td data-label="ID" class="fw-bold">#${idx + 1}</td>
                <td data-label="Source">
                    <span class="badge ${
                        r.source_type === 'inward' ? 'bg-danger-subtle text-danger border border-danger-subtle' :
                        r.source_type === 'purchase' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary'
                    } px-2 py-1">
                        ${r.source_type}
                    </span>
                </td>
                <td data-label="Date" class="text-muted">${formatDate(r.purchase_date)}</td>
                <td data-label="Invoice"><span class="badge bg-light text-dark border font-monospace">${r.invoice_no}</span></td>
                <td data-label="Vendor" class="fw-bold text-dark">${r.vendor_name}</td>
                <td data-label="Item Code"><span class="badge bg-secondary-subtle text-secondary font-monospace">${r.item_code}</span></td>
                <td data-label="Item Name" class="fw-bold">${r.item_name}</td>
                <td data-label="Qty" class="fw-bold">${r.qty}</td>
                <td data-label="Unit"><span class="badge bg-light text-dark border">${r.unit}</span></td>
                <td data-label="Price">${parseFloat(r.price).toFixed(2)}</td>
                <td data-label="Disc">${parseFloat(r.item_discount).toFixed(2)}</td>
                <td data-label="Line Total" class="fw-semibold">${parseFloat(r.line_total).toFixed(2)}</td>
                
                <td data-label="Subtotal" class="${r.is_duplicate ? 'text-muted text-center' : 'fw-bold'}">${r.is_duplicate ? '-' : parseFloat(r.subtotal).toFixed(2)}</td>
                <td data-label="Discount" class="${r.is_duplicate ? 'text-muted text-center' : ''}">${r.is_duplicate ? '-' : parseFloat(r.discount).toFixed(2)}</td>
                <td data-label="Extra Cost" class="${r.is_duplicate ? 'text-muted text-center' : ''}">${r.is_duplicate ? '-' : parseFloat(r.extra_cost).toFixed(2)}</td>
                <td data-label="Net" class="${r.is_duplicate ? 'text-muted text-center fw-bold text-success' : 'fw-bold text-success'}">${r.is_duplicate ? '-' : parseFloat(r.net_amount).toFixed(2)}</td>
                <td data-label="Paid" class="${r.is_duplicate ? 'text-muted text-center' : ''}">${r.is_duplicate ? '-' : parseFloat(r.paid_amount).toFixed(2)}</td>
                <td data-label="Due" class="${r.is_duplicate ? 'text-muted text-center' : 'text-danger fw-bold'}">${r.is_duplicate ? '-' : parseFloat(r.due_amount).toFixed(2)}</td>
            </tr>`;

            grandSubtotal += parseFloat(r.subtotal);
            grandDiscount += parseFloat(r.discount);
            grandExtraCost += parseFloat(r.extra_cost);
            grandNet += parseFloat(r.net_amount);
            grandPaid += parseFloat(r.paid_amount);
            grandDue += parseFloat(r.due_amount);
        });

        tableContent += `<tr class="rp-total-row">
            <td colspan="12" class="text-end">Grand Total:</td>
            <td data-label="Subtotal">${grandSubtotal.toFixed(2)}</td>
            <td data-label="Discount">${grandDiscount.toFixed(2)}</td>
            <td data-label="Extra Cost">${grandExtraCost.toFixed(2)}</td>
            <td data-label="Net">${grandNet.toFixed(2)}</td>
            <td data-label="Paid">${grandPaid.toFixed(2)}</td>
            <td data-label="Due">${grandDue.toFixed(2)}</td>
        </tr>`;

        $('#reportBody').html(tableContent);
    }

    $('#btnSearch').on('click', function() {
        fetchReport();
    });

    function fetchReport() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        $('#loader').show();

        $.ajax({
            url: "{{ route('report.purchase.fetch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                start_date: start_date,
                end_date: end_date
            },
            success: function(response) {
                $('#loader').hide();
                renderRows(response.data);
            },
            error: function() {
                $('#loader').hide();
                alert('Error fetching purchase report');
            }
        });
    }

    $('#btnExportCsv').on('click', function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        $('#loader').show();

        $.ajax({
            url: "{{ route('report.purchase.fetch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                start_date: start_date,
                end_date: end_date
            },
            success: function(response) {
                $('#loader').hide();
                if (!response.data.length) {
                    alert('No data to export');
                    return;
                }

                var csv = 'Source,Purchase Date,Invoice No,Vendor,Item Code,Item Name,Qty,Unit,Price,Item Discount,Line Total,Subtotal,Discount,Extra Cost,Net Amount,Paid Amount,Due Amount\n';

                response.data.forEach(function(r) {
                    csv += `"${r.source_type}","${r.purchase_date}","${r.invoice_no}","${r.vendor_name}","${r.item_code}","${r.item_name}",${r.qty},${r.unit},${r.price},${r.item_discount},${r.line_total},${r.subtotal},${r.discount},${r.extra_cost},${r.net_amount},${r.paid_amount},${r.due_amount}\n`;
                });

                var blob = new Blob([csv], {
                    type: 'text/csv;charset=utf-8;'
                });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'purchase_report.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            },
            error: function() {
                $('#loader').hide();
                alert('Export failed');
            }
        });
    });
});
</script>
@endsection