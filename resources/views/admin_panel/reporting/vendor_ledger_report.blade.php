@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --vl-bg: #f8fafc;
  --vl-surface: #ffffff;
  --vl-border: #e2e8f0;
  --vl-border-lt: #f1f5f9;
  --vl-text: #0f172a;
  --vl-text-sec: #475569;
  --vl-text-muted: #64748b;
  --vl-primary: #4f46e5;
  --vl-radius: 16px;
  --vl-radius-sm: 10px;
  --vl-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --vl-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --vl-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.vl-page * {
  font-family: var(--vl-font);
}

.vl-page {
  background-color: var(--vl-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.vl-hdr {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
  border-radius: var(--vl-radius);
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

.vl-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(99, 102, 241, 0.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(168, 85, 247, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.vl-hdr > * {
  position: relative;
  z-index: 1;
}

.vl-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.vl-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.vl-hdr-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #818cf8;
  font-size: 1.4rem;
}

.vl-hdr-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #c7d2fe;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.vl-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--vl-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.vl-btn-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}
.vl-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
}

.vl-btn-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
}
.vl-btn-danger:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45);
}

.vl-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.vl-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ CARD & CONTROLS ═══════ */
.vl-card {
  background: var(--vl-surface);
  border: 1px solid var(--vl-border);
  border-radius: var(--vl-radius);
  box-shadow: var(--vl-shadow);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.vl-card-body { padding: 1.35rem; }

.vl-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--vl-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.vl-input, .vl-select {
  border: 1px solid var(--vl-border);
  border-radius: var(--vl-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--vl-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.vl-input:focus, .vl-select:focus {
  border-color: var(--vl-primary);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
  outline: none;
}

/* ═══════ LEDGER DOCUMENT STYLING ═══════ */
.vl-ledger-box {
  background: var(--vl-surface);
  border: 1px solid var(--vl-border);
  border-radius: var(--vl-radius);
  padding: 1.5rem;
  box-shadow: var(--vl-shadow-lg);
}

.vl-ledger-title {
  text-align: center;
  font-weight: 800;
  font-size: 1.35rem;
  margin-bottom: 1rem;
  letter-spacing: 0.05em;
  color: #312e81;
  text-transform: uppercase;
  background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
  border: 1px solid #a5b4fc;
  border-radius: var(--vl-radius-sm);
  padding: 0.85rem;
}

.vl-ledger-header {
  background: #f8fafc;
  border: 1px solid var(--vl-border);
  border-radius: var(--vl-radius-sm);
  padding: 0.85rem 1.25rem;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--vl-text);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.vl-ledger-header strong { color: #312e81; }

.vl-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid var(--vl-border);
  border-radius: var(--vl-radius-sm);
  overflow: hidden;
  font-size: 0.88rem;
}

.vl-table thead th {
  background: #f8fafc;
  color: var(--vl-text-muted);
  font-weight: 700;
  text-align: center;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--vl-border);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.vl-table tbody td {
  padding: 0.75rem 1rem;
  color: var(--vl-text-sec);
  vertical-align: middle;
  text-align: center;
  border-bottom: 1px solid var(--vl-border-lt);
}

.vl-table tbody tr:last-child td { border-bottom: none; }
.vl-table tbody tr:hover td { background: #f8fafc; }

.vl-table .text-left { text-align: left !important; }

.vl-badge-positive { color: #059669; font-weight: 800; }
.vl-badge-negative { color: #dc2626; font-weight: 800; }
.vl-badge-neutral { color: #4f46e5; font-weight: 800; }

.vl-opening td {
  background: #f8fafc;
  font-weight: 600;
  color: var(--vl-text-muted);
  border-bottom: 1px dashed var(--vl-border) !important;
}

.vl-total-row td {
  font-weight: 800;
  background: #e0e7ff !important;
  color: var(--vl-text) !important;
  border-top: 2px solid #a5b4fc !important;
}

.vl-loader { display: none; text-align: center; padding: 1.5rem 0; }
.vl-loader .spinner-border { color: var(--vl-primary); width: 2.2rem; height: 2.2rem; }

/* ═══════ FULL MOBILE RESPONSIVE — CARD LEDGER (no horizontal scroll) ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .vl-page { overflow-x: hidden !important; padding-bottom: 2rem; }

  .vl-hdr { padding: 1rem 1rem; border-radius: 14px; gap: .75rem; }
  .vl-hdr-title { gap: .6rem; }
  .vl-hdr-title h2 { font-size: 1.02rem; }
  .vl-hdr-icon { width: 40px; height: 40px; font-size: 1.15rem; }
  .vl-hdr-badge { font-size: .6rem; padding: .25rem .6rem; }
  .vl-btn { padding: .5rem .8rem; font-size: .76rem; }
  .vl-card-body { padding: .9rem; }
  .vl-input, .vl-select { min-height: 44px; font-size: .85rem; }

  .vl-card-body .col-12 .d-flex { flex-wrap: wrap; }
  .vl-card-body .col-12 .d-flex .vl-btn { flex: 1 1 100%; justify-content: center; }

  .vl-ledger-box { padding: .9rem .7rem; border-radius: 14px; }
  .vl-ledger-title { font-size: .92rem; padding: .6rem .5rem; letter-spacing: .04em; }
  .vl-ledger-header { padding: .7rem .8rem; font-size: .78rem; gap: .4rem 1rem; align-items: flex-start; flex-direction: column; }
  .vl-ledger-header span { display: block; }

  .vl-ledger-box .table-responsive { overflow: visible !important; }

  .vl-table { display: block; font-size: .72rem !important; border: none !important; }
  .vl-table thead { display: none; }
  .vl-table tbody { display: block; }

  .vl-table tbody tr {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .5rem;
    background: var(--vl-surface);
    border: 1px solid var(--vl-border);
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 8px rgba(0,0,0,.05);
    padding: .6rem;
    margin-bottom: .55rem;
    overflow: hidden;
  }
  .vl-table tbody tr:hover td { background: transparent; }

  .vl-table tbody td {
    display: flex; flex-wrap: wrap; align-items: baseline; gap: 1px 4px;
    border: none !important; padding: 0 !important;
    text-align: left !important; white-space: normal !important;
    word-break: break-word; overflow-wrap: anywhere; line-height: 1.35; font-size: .72rem;
    min-width: 0;
  }
  .vl-table tbody td::before {
    content: "";
    font-size: .5rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .35px; color: var(--vl-text-muted); white-space: nowrap; flex-shrink: 0;
  }

  /* transaction rows */
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(1)::before { content: "Date"; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(2) { grid-column: 1 / -1; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(2)::before { content: "Invoice / Ref"; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(3) { grid-column: 1 / -1; font-weight: 600; font-size: .78rem; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(3)::before { content: none; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(4)::before { content: "Debit"; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(5)::before { content: "Credit"; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(6) { grid-column: 1 / -1; font-size: .85rem !important; }
  .vl-table tbody tr:not(.vl-opening):not(.vl-total-row) td:nth-child(6)::before { content: "Balance"; font-size: .56rem; }

  /* opening row */
  .vl-table tbody tr.vl-opening td:nth-child(1),
  .vl-table tbody tr.vl-opening td:nth-child(2),
  .vl-table tbody tr.vl-opening td:nth-child(4),
  .vl-table tbody tr.vl-opening td:nth-child(5) { display: none; }
  .vl-table tbody tr.vl-opening td:nth-child(3) { grid-column: 1 / -1; font-weight: 700; font-size: .78rem; letter-spacing: .04em; }
  .vl-table tbody tr.vl-opening td:nth-child(6) { grid-column: 1 / -1; font-size: .85rem !important; }
  .vl-table tbody tr.vl-opening td:nth-child(6)::before { content: "Opening Balance"; font-size: .56rem; }

  /* totals row */
  .vl-table tbody tr.vl-total-row td:nth-child(1) { grid-column: 1 / -1; }
  .vl-table tbody tr.vl-total-row td:nth-child(1)::before { content: none; }
  .vl-table tbody tr.vl-total-row td:nth-child(2)::before { content: "Debit"; }
  .vl-table tbody tr.vl-total-row td:nth-child(3)::before { content: "Credit"; }
  .vl-table tbody tr.vl-total-row td:nth-child(4) { grid-column: 1 / -1; font-size: .85rem !important; }
  .vl-table tbody tr.vl-total-row td:nth-child(4)::before { content: "Total Balance"; font-size: .56rem; }
}
</style>

<div class="vl-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="vl-hdr">
      <div class="vl-hdr-title">
        <div class="vl-hdr-icon">
          <i class="bi bi-journal-text"></i>
        </div>
        <div>
          <h2>Vendor Ledger Statement</h2>
          <span class="vl-hdr-badge">Payables & Supplier Audit</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="vl-btn vl-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="vl-card">
      <div class="vl-card-body">
        <form id="ledgerForm" class="row g-3 align-items-end">
          <div class="col-12 col-md-3">
            <label class="vl-label"><i class="bi bi-building me-1 text-primary"></i> Select Vendor</label>
            <select name="Vendor_id" id="Vendor_id" class="vl-select" required>
              <option value="">Select Vendor</option>
              @foreach($vendors as $v)
              <option value="{{ $v->id }}">{{ $v->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3">
            <label class="vl-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
            <input type="date" name="start_date" id="start_date" class="vl-input" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-12 col-md-3">
            <label class="vl-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
            <input type="date" name="end_date" id="end_date" class="vl-input" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-12 col-md-3 d-flex gap-2">
            <button type="button" id="btnSearch" class="vl-btn vl-btn-primary w-100">
              <i class="bi bi-search me-1"></i> Search
            </button>
            <button type="button" id="exportPdfBtn" class="vl-btn vl-btn-danger w-100" onclick="exportPDF()">
              <i class="bi bi-file-earmark-pdf me-1"></i> PDF
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══════ LEDGER RESULT CARD ═══════ --}}
    <div class="vl-card">
      <div class="vl-card-body">
        <div id="loader" class="vl-loader">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2 text-muted small">Loading vendor ledger statement...</p>
        </div>

        <div id="ledgerBox" style="display:none;">
          <div class="vl-ledger-box" id="ledgerPdfArea">
            <div class="vl-ledger-title">VENDOR LEDGER STATEMENT</div>
            <div id="ledgerHeader" class="vl-ledger-header"></div>

            <div class="table-responsive">
              <table class="vl-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Inv / Ref</th>
                    <th>Description</th>
                    <th>Debit (Rs)</th>
                    <th>Credit (Rs)</th>
                    <th>Balance (Rs)</th>
                  </tr>
                </thead>
                <tbody id="ledgerBody"></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#Vendor_id').select2({ placeholder: "Select Vendor", allowClear: true, width: '100%' });

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr + 'T00:00:00');
        return String(d.getDate()).padStart(2,'0') + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + d.getFullYear();
    }

    $(document).on('click', '#btnSearch', function() {
        var cid = $("#Vendor_id").val();
        var start = $("#start_date").val();
        var end = $("#end_date").val();
        if (!cid || !start || !end) { alert("Please select all fields"); return; }

        $("#loader").show();
        $("#ledgerBox").hide();

        $.get("{{ route('report.vendor.ledger.fetch') }}", { vendor_id: cid, start_date: start, end_date: end }, function(res) {
            $("#loader").hide();
            $("#ledgerBox").show();
            $("#ledgerHeader").html('<span><strong>Vendor:</strong> ' + res.vendor.name + '</span><span><strong>Duration:</strong> ' + formatDate(start) + ' to ' + formatDate(end) + '</span>');

            var totalDebit = 0, totalCredit = 0, lastBalance = parseFloat(res.opening_balance) || 0;
            var html = '<tr class="vl-opening"><td>N/A</td><td>-</td><td class="text-left">Opening Balance</td><td>-</td><td>-</td><td class="vl-badge-neutral">Rs. ' + lastBalance.toFixed(2) + '</td></tr>';

            res.transactions.forEach(function(t) {
                var debit = 0, credit = 0;
                if (t.type === 'purchase') debit = parseFloat(t.amount) || 0;
                else if (t.type === 'purchase_return') credit = parseFloat(t.amount) || 0;
                else if (t.type === 'vendor_payment') credit = parseFloat(t.amount) || 0;
                else { debit = parseFloat(t.debit) || 0; credit = parseFloat(t.credit) || 0; }
                totalDebit += debit; totalCredit += credit;
                lastBalance = lastBalance + debit - credit;
                var invRef = t.invoice ?? '-';
                if (t.reference) invRef += ' (' + t.reference + ')';
                var balClass = lastBalance > 0 ? 'vl-badge-positive' : (lastBalance < 0 ? 'vl-badge-negative' : 'vl-badge-neutral');
                html += '<tr><td class="text-muted">' + formatDate(t.date.split(" ")[0]) + '</td><td><span class="badge bg-light text-dark border font-monospace">' + invRef + '</span></td><td class="text-left">' + t.description + '</td><td class="fw-bold">' + (debit > 0 ? 'Rs. ' + debit.toFixed(2) : '-') + '</td><td class="fw-bold">' + (credit > 0 ? 'Rs. ' + credit.toFixed(2) : '-') + '</td><td class="' + balClass + '">Rs. ' + lastBalance.toFixed(2) + '</td></tr>';
            });

            html += '<tr class="vl-total-row"><td colspan="3" class="text-left">Totals:</td><td>Rs. ' + totalDebit.toFixed(2) + '</td><td>Rs. ' + totalCredit.toFixed(2) + '</td><td class="' + (lastBalance > 0 ? 'vl-badge-positive' : (lastBalance < 0 ? 'vl-badge-negative' : 'vl-badge-neutral')) + '">Rs. ' + lastBalance.toFixed(2) + '</td></tr>';
            $("#ledgerBody").html(html);
        });
    });

    window.exportPDF = function() {
        var cid = $("#Vendor_id").val();
        var start = $("#start_date").val();
        var end = $("#end_date").val();
        if (!cid || !start || !end) { alert("Please generate ledger first"); return; }
        window.open("{{ route('report.vendor.ledger.pdf') }}?vendor_id=" + cid + "&start_date=" + start + "&end_date=" + end, "_blank");
    };
});
</script>
@endsection