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

.rp-sale * {
  font-family: var(--rp-font);
}

.rp-sale {
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

.rp-chk input { accent-color: var(--rp-primary); width: 16px; height: 16px; }
.rp-chk .form-check-label { font-size: 0.85rem; font-weight: 600; color: var(--rp-text); }

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

.return-cell {
  max-width: 200px;
  max-height: 90px;
  overflow-y: auto;
  overflow-x: hidden;
  white-space: normal;
  font-size: 0.78rem;
  line-height: 1.4;
  background: #f8fafc;
  border: 1px solid var(--rp-border-lt);
  border-radius: 6px;
  padding: 6px;
  scrollbar-width: thin;
}

.rp-loader { display: none; text-align: center; padding: 1.5rem 0; }
.rp-loader .spinner-border { color: var(--rp-primary); width: 2.2rem; height: 2.2rem; }

/* Grand total row — premium dark strip (desktop) */
.rp-sale .rp-table tbody tr.rp-total-row {
  background: #0f172a !important;
}
.rp-sale .rp-table tbody tr.rp-total-row td {
  background: transparent !important;
  color: #ffffff !important;
  font-weight: 700;
}
.rp-sale .rp-table tbody tr.rp-total-row .text-success {
  color: #ffffff !important;
}

/* ═══════ FULL MOBILE RESPONSIVE — PREMIUM CARD BOXES ═══════ */
@media (max-width: 991.98px) {
  body, html { overflow-x: hidden !important; }
  .rp-sale, .rp-sale * { max-width: 100%; box-sizing: border-box; }
  .rp-sale { overflow-x: hidden !important; }

  .rp-hdr { padding: 1.05rem 1rem !important; gap: .6rem; }
  .rp-hdr-title { gap: .6rem; }
  .rp-hdr-title h2 { font-size: 1.1rem !important; }
  .rp-hdr-icon { width: 40px; height: 40px; font-size: 1.15rem; }
  .rp-hdr-badge { font-size: .65rem; padding: .22rem .7rem; }
  .rp-btn { font-size: .8rem; padding: .5rem .9rem; }
  .rp-card-body { padding: 1rem; }

  /* Kill all scroll wrappers */
  .rp-table-wrapper,
  .rp-sale .dataTables_wrapper,
  .rp-sale .dataTables_scrollHead,
  .rp-sale .dataTables_scrollBody,
  .rp-sale .dataTables_scroll,
  .rp-sale .table-responsive {
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
  .rp-sale .rp-table tbody tr {
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
  .rp-sale .rp-table tbody tr:hover td { background: transparent !important; }

  .rp-sale .rp-table tbody td {
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
  .rp-sale .rp-table tbody td::before {
    content: attr(data-label);
    font-size: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--rp-text-muted);
    white-space: nowrap;
  }

  .rp-sale .rp-table tbody tr > td:nth-child(1) {
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
  .rp-sale .rp-table tbody tr > td:nth-child(1)::before { content: none; }
  .rp-sale .rp-table tbody tr > td:nth-child(2) { grid-column: 2; grid-row: 1; }
  .rp-sale .rp-table tbody tr > td:nth-child(3) { grid-column: 1; grid-row: 2; }
  .rp-sale .rp-table tbody tr > td:nth-child(5) { grid-column: 2; grid-row: 2; }
  .rp-sale .rp-table tbody tr > td:nth-child(5)::before { content: none; }
  .rp-sale .rp-table tbody tr > td:nth-child(4) {
    grid-column: 1 / -1; grid-row: 3;
    font-weight: 800 !important; font-size: .85rem !important; color: var(--rp-text) !important;
  }
  .rp-sale .rp-table tbody tr > td:nth-child(4)::before { content: none; }
  .rp-sale .rp-table tbody tr > td:nth-child(6) {
    grid-column: 1 / -1; grid-row: 4;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid var(--rp-border-lt) !important;
    border-radius: 10px;
    padding: .45rem .55rem !important;
    font-weight: 600 !important; font-size: .8rem !important;
  }
  .rp-sale .rp-table tbody tr > td:nth-child(6)::before { color: #2563eb !important; margin-bottom: 3px; }
  .rp-sale .rp-table tbody tr > td:nth-child(7) { grid-column: 1; grid-row: 5; }
  .rp-sale .rp-table tbody tr > td:nth-child(8) { grid-column: 2; grid-row: 5; font-weight: 700 !important; }
  .rp-sale .rp-table tbody tr > td:nth-child(9) { grid-column: 1; grid-row: 6; }
  .rp-sale .rp-table tbody tr > td:nth-child(10) { grid-column: 2; grid-row: 6; font-weight: 700 !important; }
  .rp-sale .rp-table tbody tr > td:nth-child(11) {
    grid-column: 1 / -1; grid-row: 7;
    background: #ecfdf5; border-radius: 10px; padding: .5rem .55rem !important;
    font-weight: 800 !important; font-size: .86rem !important; color: #059669 !important;
  }
  .rp-sale .rp-table tbody tr > td:nth-child(11)::before { color: #059669 !important; }
  .rp-sale .rp-table tbody tr > td:nth-child(12) {
    grid-column: 1 / -1; grid-row: 8;
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    border: 1px solid #fed7aa !important;
    border-radius: 10px;
    padding: .45rem .55rem !important;
  }
  .rp-sale .rp-table tbody tr > td:nth-child(12)::before { color: #c2410c !important; margin-bottom: 3px; }

  /* Return cell — expand fully on mobile (no scroll) */
  .rp-sale .return-cell {
    max-width: 100% !important;
    max-height: none !important;
    overflow: visible !important;
    overflow-y: visible !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    font-size: .76rem !important;
  }

  /* Grand total box */
  .rp-sale .rp-table tbody tr.rp-total-row {
    grid-template-columns: 1fr 1fr;
    background: linear-gradient(135deg, #0f172a, #1e3a8a) !important;
    border: none !important;
    color: #ffffff !important;
    padding: .9rem .8rem !important;
    margin-top: .3rem;
  }
  .rp-sale .rp-table tbody tr.rp-total-row td {
    color: #ffffff !important;
    font-size: .78rem !important;
    background: transparent !important;
  }
  .rp-sale .rp-table tbody tr.rp-total-row td::before {
    content: attr(data-label);
    color: rgba(255,255,255,.65) !important;
    font-size: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
  }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(1) {
    grid-column: 1 / -1; grid-row: 1;
    font-weight: 800; font-size: .85rem !important;
    text-transform: uppercase; letter-spacing: .5px;
  }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(1)::before { content: none; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(2) { grid-column: 1 / -1; grid-row: 2; font-size: .72rem !important; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(3) { grid-column: 1; grid-row: 3; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(4) { grid-column: 2; grid-row: 3; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(5) { grid-column: 1; grid-row: 4; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(6) { grid-column: 2; grid-row: 4; }
  .rp-sale .rp-table tbody tr.rp-total-row td:nth-child(7) { grid-column: 1 / -1; grid-row: 5; }
}
</style>

<div class="rp-sale">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="rp-hdr">
      <div class="rp-hdr-title">
        <div class="rp-hdr-icon">
          <i class="bi bi-receipt"></i>
        </div>
        <div>
          <h2>Sales Register Report</h2>
          <span class="rp-hdr-badge">Filtered Sales & Item Breakdown</span>
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
        <form id="SaleFilterForm" class="row g-3 align-items-end">
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-event me-1 text-primary"></i> Start Date & Time</label>
            <input type="datetime-local" name="start_date" id="start_date" class="rp-input" value="{{ date('Y-m-d\T00:00') }}">
          </div>
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-event-fill me-1 text-primary"></i> End Date & Time</label>
            <input type="datetime-local" name="end_date" id="end_date" class="rp-input" value="{{ date('Y-m-d\T23:59') }}">
          </div>
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-people me-1 text-primary"></i> Customer Type</label>
            <div class="d-flex gap-3 rp-chk pt-1">
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="customer_type[]" value="Wholesaler" id="type_wholesaler">
                <label class="form-check-label" for="type_wholesaler">Wholesaler</label>
              </div>
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="customer_type[]" value="Retailer" id="type_retailer" checked>
                <label class="form-check-label" for="type_retailer">Retailer</label>
              </div>
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="customer_type[]" value="Walking Customer" id="type_walking" checked>
                <label class="form-check-label" for="type_walking">Walking</label>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 d-flex gap-2">
            <button type="button" id="btnSearch" class="rp-btn rp-btn-primary w-100">
              <i class="bi bi-search me-1"></i> Search
            </button>
            <button type="button" id="btnExportCsv" class="rp-btn rp-btn-danger w-100">
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
      <button class="btn btn-sm btn-light border shadow-sm fw-semibold text-secondary" onclick="document.getElementById('grandTotalRow').scrollIntoView({behavior: 'smooth'});">
        <i class="bi bi-arrow-down-circle me-1 text-primary"></i> Jump to Grand Total
      </button>
    </div>

    {{-- ═══════ DATA TABLE CARD ═══════ --}}
    <div class="rp-table-card">
      <div class="rp-card-body p-0">
        <div id="loader" class="rp-loader">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2 text-muted small">Generating sales report...</p>
        </div>

        <div class="rp-table-wrapper">
          <table class="table rp-table align-middle nowrap" id="saleReport" style="width:100%;">
            <thead>
              <tr>
                <th width="4%">#</th>
                <th width="12%">Date | Time</th>
                <th width="10%">Invoice No</th>
                <th width="15%">Customer</th>
                <th width="10%">Reference</th>
                <th width="15%">Products</th>
                <th width="8%">Unit</th>
                <th width="8%">Qty</th>
                <th width="8%">Price</th>
                <th width="10%">Total</th>
                <th width="10%">Net</th>
                <th width="12%">Returns</th>
              </tr>
            </thead>
            <tbody id="saleBody"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '#btnSearch', function() {
    let start = $('#start_date').val();
    let end = $('#end_date').val();

    $("#loader").show();
    $.ajax({
        url: "{{ route('report.sale.fetch') }}",
        type: "GET",
        data: {
            start_date: start,
            end_date: end,
            customer_type: $('input[name="customer_type[]"]:checked').map(function(){return $(this).val();}).get(),
            _t: new Date().getTime()
        },
        success: function(res) {
            $("#loader").hide();
            let html = "";

            function num(v) {
                if (v === null || v === undefined) return 0;
                if (typeof v === 'number') return v;
                v = String(v).replace(/[^0-9.\-]/g, '');
                const f = parseFloat(v);
                return isNaN(f) ? 0 : f;
            }

            function sumArray(arr) {
                return arr.reduce((a, b) => a + num(b), 0);
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                const d = new Date(dateString);
                if (isNaN(d)) return dateString;

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();

                let hours = d.getHours();
                const minutes = String(d.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                hours = hours % 12;
                hours = hours ? hours : 12;
                hours = String(hours).padStart(2, '0');

                return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
            }

            let grandQty = 0,
                grandTotal = 0,
                grandNet = 0,
                grandReturnQty = 0,
                grandReturnAmount = 0;
            
            let grandPiece = 0;
            let grandKG = 0;
            let grandPound = 0;
            let grandMeter = 0;
            let grandYard = 0;

            (res || []).forEach((s, i) => {
                let products = '-';
                if (s.product_names) {
                    products = s.product_names.split('|').map(p => p.trim()).join('<br>');
                }

                const qtyArrRaw = (s.qty || '').toString().trim();
                const qtyArr = qtyArrRaw.length ? qtyArrRaw.split(',').map(x => x.trim()) : [];
                const qtyArrNums = qtyArr.map(num);
                const rowQty = sumArray(qtyArrNums);
                grandQty += rowQty;

                const unitArrRaw = (s.unit || '').toString().trim();
                const unitArr = unitArrRaw.length ? unitArrRaw.split('|').map(x => x.trim()) : [];
                
                if (unitArr.length === qtyArr.length) {
                    unitArr.forEach((u, idx) => {
                        const q = qtyArrNums[idx];
                        const uLower = u.toLowerCase();
                        if (uLower.includes('piece') || uLower.includes('pcs')) grandPiece += q;
                        else if (uLower.includes('kg')) grandKG += q;
                        else if (uLower.includes('pound')) grandPound += q;
                        else if (uLower.includes('meter')) grandMeter += q;
                        else if (uLower.includes('yard')) grandYard += q;
                    });
                }

                const priceDisplay = (s.per_price || '').toString().trim().length ?
                    s.per_price.toString().split(',').map(p => p.trim()).join('<br>') : '-';

                const perTotalRaw = (s.per_total || '').toString().trim();
                const perTotalArr = perTotalRaw.length ? perTotalRaw.split(',').map(x => x.trim()) : [];
                const perTotalNums = perTotalArr.map(num);
                const rowTotal = sumArray(perTotalNums);
                grandTotal += rowTotal;

                const rowNet = num(s.total_net) || rowTotal;
                grandNet += rowNet;

                let returnHtml = '';
                let returnQtyTotal = 0;
                let returnAmountTotal = 0;

                if (s.returns) {
                    if (Array.isArray(s.returns)) {
                        const lines = s.returns.map(r => {
                            const rQty = num(r.qty);
                            const rAmt = num(r.per_total || r.amount || r.total);
                            returnQtyTotal += rQty;
                            returnAmountTotal += rAmt;
                            const qtyDisplay = Number.isInteger(rQty) ? rQty : rQty.toFixed(2).replace(/\.?0+$/, '');
                            return `<small class="fw-bold text-dark">${(r.product || '').toString().trim()}</small><br>
                                    <span class="text-danger">Qty: ${qtyDisplay}</span> | 
                                    <span class="fw-bold text-dark">Rs ${rAmt.toFixed(2)}</span>`;
                        });
                        returnHtml = lines.join('<hr class="my-1 text-muted">');
                    } else {
                        let parsed = null;
                        try { parsed = JSON.parse(s.returns); } catch (e) { parsed = null; }

                        if (Array.isArray(parsed)) {
                            const lines = parsed.map(r => {
                                const rQty = num(r.qty);
                                const rAmt = num(r.per_total || r.amount || r.total);
                                returnQtyTotal += rQty;
                                returnAmountTotal += rAmt;
                                return `${(r.product || '').toString().trim()} (${rQty}) - Rs ${rAmt.toFixed(2)}`;
                            });
                            returnHtml = lines.join('<br>');
                        } else {
                            const raw = s.returns.toString();
                            const candidates = raw.split(/\r?\n|;|\|/).map(r => r.trim()).filter(Boolean);
                            const lines = candidates.map(c => {
                                const m = c.match(/(.+?)\s*\(?(\d+\.?\d*)\)?\s*[-:]\s*([0-9.,]+)/);
                                if (m) {
                                    const prod = m[1].trim();
                                    const rQty = num(m[2]);
                                    const rAmt = num(m[3]);
                                    returnQtyTotal += rQty;
                                    returnAmountTotal += rAmt;
                                    return `${prod} (${rQty}) - Rs ${rAmt.toFixed(2)}`;
                                } else {
                                    return c;
                                }
                            });
                            returnHtml = lines.join('<br>');
                        }
                    }
                }

                grandReturnQty += returnQtyTotal;
                grandReturnAmount += returnAmountTotal;

                const createdAt = s.created_at || s.date || s.sale_date || s.created || '';

                const totalDisplay = perTotalArr.length ? perTotalArr.map(x => {
                    const v = num(x);
                    return v ? v.toFixed(2) : '0.00';
                }).join('<br>') : (num(s.per_total) ? num(s.per_total).toFixed(2) : '-');

                html += `<tr>
                    <td data-label="ID" class="fw-bold">#${i+1}</td>
                    <td data-label="Date" class="text-muted small">${formatDate(createdAt)}</td>
                    <td data-label="Invoice"><span class="badge bg-light text-dark border font-monospace">${s.invoice_no ?? '-'}</span></td>
                    <td data-label="Customer" class="fw-bold text-dark">${s.customer_name ?? '-'}</td>
                    <td data-label="Ref"><span class="badge bg-secondary-subtle text-secondary">${s.reference ?? '-'}</span></td>
                    <td data-label="Products" class="fw-medium">${products}</td>
                    <td data-label="Unit">${unitArr.length ? unitArr.join('<br>') : '-'}</td>
                    <td data-label="Qty">${qtyArr.length ? qtyArr.map((x, idx) => (num(x) ? num(x).toFixed(2) + ' <small class="text-muted">' + (unitArr[idx]||'') + '</small>' : '0.00')).join('<br>') : '-'}</td>
                    <td data-label="Price">${priceDisplay}</td>
                    <td data-label="Total">${totalDisplay}</td>
                    <td data-label="Net" class="fw-bold text-success">Rs ${rowNet.toFixed(2)}</td>
                    <td data-label="Returns"><div class="return-cell">${returnHtml || '-'}</div></td>
                </tr>`;
            });

            html += `<tr class="rp-total-row" id="grandTotalRow">
                <td colspan="6" class="text-end">Grand Total:</td>
                <td data-label="Units" class="small">
                    Pieces: ${grandPiece.toFixed(2)}<br>
                    KG: ${grandKG.toFixed(2)}<br>
                    ${grandPound > 0 ? 'Pound: ' + grandPound.toFixed(2) + '<br>' : ''}
                    ${grandMeter > 0 ? 'Meters: ' + grandMeter.toFixed(2) + '<br>' : ''}
                    ${grandYard > 0 ? 'Yards: ' + grandYard.toFixed(2) + '<br>' : ''}
                </td>
                <td data-label="Qty" class="fs-6">${grandQty.toFixed(2)}</td>
                <td data-label="" class="text-center">-</td>
                <td data-label="Gross" >Rs ${grandTotal.toFixed(2)}</td>
                <td data-label="Net" class="text-success fs-6">Rs ${grandNet.toFixed(2)}</td>
                <td data-label="Returns" class="small">Qty: ${grandReturnQty.toFixed(2)}<br>Return: Rs ${grandReturnAmount.toFixed(2)}</td>
            </tr>`;

            $('#saleBody').html(html);
        },
        error: function() {
            $("#loader").hide();
            alert('Failed to fetch report. Please try again.');
        }
    });
});

$(document).ready(function() {
    $(document).on('click', '#btnExportCsv', function() {
        let csv = [];
        $("#saleReport tr").each(function() {
            let row = [];
            $(this).find('th,td').each(function() {
                let cellHtml = $(this).html();

                let cellText = cellHtml
                    .replace(/<br\s*\/?>/gi, " | ")
                    .replace(/&nbsp;/gi, " ")
                    .replace(/<[^>]*>/g, "")
                    .trim();

                row.push('"' + cellText.replace(/"/g, '""') + '"');
            });
            csv.push(row.join(","));
        });

        let csvString = csv.join("\n");
        let blob = new Blob([csvString], {
            type: 'text/csv;charset=utf-8;'
        });

        let link = document.createElement("a");
        if (link.download !== undefined) {
            let url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "sale_report.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('CSV download not supported in this browser.');
        }
    });
});
</script>
@endsection