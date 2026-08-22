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
  --rp-primary: #059669;
  --rp-radius: 16px;
  --rp-radius-sm: 10px;
  --rp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --rp-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --rp-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.rp-cl * {
  font-family: var(--rp-font);
}

.rp-cl {
  background-color: var(--rp-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.rp-hdr {
  position: relative;
  background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
  border-radius: var(--rp-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 20px 30px -10px rgba(6, 78, 59, 0.3);
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
    radial-gradient(circle at 10% 90%, rgba(16, 185, 129, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(59, 130, 246, 0.15) 0%, transparent 50%);
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
  color: #6ee7b7;
  font-size: 1.4rem;
}

.rp-hdr-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #a7f3d0;
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
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
}
.rp-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(5, 150, 105, 0.45);
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

.rp-input, .rp-select {
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--rp-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.rp-input:focus, .rp-select:focus {
  border-color: var(--rp-primary);
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
  outline: none;
}

/* ═══════ LEDGER DOCUMENT STYLING ═══════ */
.ledger-box {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  padding: 1.5rem;
  box-shadow: var(--rp-shadow-lg);
}

.ledger-title {
  text-align: center;
  font-weight: 800;
  font-size: 1.35rem;
  margin-bottom: 1rem;
  letter-spacing: 0.05em;
  color: #064e3b;
  text-transform: uppercase;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  border: 1px solid #a7f3d0;
  border-radius: var(--rp-radius-sm);
  padding: 0.85rem;
}

.ledger-header {
  background: #f8fafc;
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  padding: 0.85rem 1.25rem;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--rp-text);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.ledger-header strong { color: #064e3b; }

.cl-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  overflow: hidden;
  font-size: 0.88rem;
}

.cl-table thead th {
  background: #f8fafc;
  color: var(--rp-text-muted);
  font-weight: 700;
  text-align: center;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--rp-border);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.cl-table tbody td {
  padding: 0.75rem 1rem;
  color: var(--rp-text-sec);
  vertical-align: middle;
  text-align: center;
  border-bottom: 1px solid var(--rp-border-lt);
}

.cl-table tbody tr:last-child td { border-bottom: none; }
.cl-table tbody tr:hover td { background: #f8fafc; }

.text-left { text-align: left !important; }

.balance-positive { color: #059669; font-weight: 800; }
.balance-negative { color: #dc2626; font-weight: 800; }
.balance-neutral { color: #2563eb; font-weight: 800; }

.opening-row td {
  background: #f8fafc;
  font-weight: 600;
  color: var(--rp-text-muted);
  border-bottom: 1px dashed var(--rp-border) !important;
}

.totals-row td {
  font-weight: 800;
  background: #ecfdf5 !important;
  color: var(--rp-text) !important;
  border-top: 2px solid #a7f3d0 !important;
}

.rp-loader { display: none; text-align: center; padding: 1.5rem 0; }
.rp-loader .spinner-border { color: var(--rp-primary); width: 2.2rem; height: 2.2rem; }

/* ═══════ FULL MOBILE RESPONSIVE — CARD LEDGER (no horizontal scroll) ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .rp-cl { overflow-x: hidden !important; padding-bottom: 2rem; }

  .rp-hdr { padding: 1rem 1rem; border-radius: 14px; gap: .75rem; }
  .rp-hdr-title { gap: .6rem; }
  .rp-hdr-title h2 { font-size: 1.02rem; }
  .rp-hdr-icon { width: 40px; height: 40px; font-size: 1.15rem; }
  .rp-hdr-badge { font-size: .6rem; padding: .25rem .6rem; }
  .rp-btn { padding: .5rem .8rem; font-size: .76rem; }
  .rp-card-body { padding: .9rem; }
  .rp-input, .rp-select { min-height: 44px; font-size: .85rem; }

  .ledger-box { padding: .9rem .7rem; border-radius: 14px; }
  .ledger-title { font-size: .92rem; padding: .6rem .5rem; letter-spacing: .04em; }
  .ledger-header { padding: .7rem .8rem; font-size: .78rem; gap: .4rem 1rem; align-items: flex-start; flex-direction: column; }
  .ledger-header span { display: block; }

  .ledger-box .table-responsive { overflow: visible !important; }

  .cl-table { display: block; font-size: .72rem !important; border: none !important; }
  .cl-table thead { display: none; }
  .cl-table tbody { display: block; }

  .cl-table tbody tr {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .5rem;
    background: var(--rp-surface);
    border: 1px solid var(--rp-border);
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 8px rgba(0,0,0,.05);
    padding: .6rem;
    margin-bottom: .55rem;
    overflow: hidden;
  }
  .cl-table tbody tr:hover td { background: transparent; }

  .cl-table tbody td {
    display: flex; flex-wrap: wrap; align-items: baseline; gap: 1px 4px;
    border: none !important; padding: 0 !important;
    text-align: left !important; white-space: normal !important;
    word-break: break-word; overflow-wrap: anywhere; line-height: 1.35; font-size: .72rem;
    min-width: 0;
  }
  .cl-table tbody td::before {
    content: "";
    font-size: .5rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .35px; color: var(--rp-text-muted); white-space: nowrap; flex-shrink: 0;
  }

  /* transaction rows */
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(1)::before { content: "Date"; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(2) { grid-column: 1 / -1; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(2)::before { content: "Invoice / Ref"; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(3) { grid-column: 1 / -1; font-weight: 600; font-size: .78rem; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(3)::before { content: none; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(4)::before { content: "Debit"; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(5)::before { content: "Credit"; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(6) { grid-column: 1 / -1; font-size: .85rem !important; }
  .cl-table tbody tr:not(.opening-row):not(.totals-row) td:nth-child(6)::before { content: "Balance"; font-size: .56rem; }

  .cl-table tbody tr[style*="fef2f2"] { border-color: #f3c2c2 !important; }

  /* opening row */
  .cl-table tbody tr.opening-row td:nth-child(1),
  .cl-table tbody tr.opening-row td:nth-child(2),
  .cl-table tbody tr.opening-row td:nth-child(4),
  .cl-table tbody tr.opening-row td:nth-child(5) { display: none; }
  .cl-table tbody tr.opening-row td:nth-child(3) { grid-column: 1 / -1; font-weight: 700; font-size: .78rem; letter-spacing: .04em; }
  .cl-table tbody tr.opening-row td:nth-child(6) { grid-column: 1 / -1; font-size: .85rem !important; }
  .cl-table tbody tr.opening-row td:nth-child(6)::before { content: "Opening Balance"; font-size: .56rem; }

  /* totals row */
  .cl-table tbody tr.totals-row td:nth-child(1) { grid-column: 1 / -1; }
  .cl-table tbody tr.totals-row td:nth-child(1)::before { content: none; }
  .cl-table tbody tr.totals-row td:nth-child(2)::before { content: "Debit"; }
  .cl-table tbody tr.totals-row td:nth-child(3)::before { content: "Credit"; }
  .cl-table tbody tr.totals-row td:nth-child(4) { grid-column: 1 / -1; font-size: .85rem !important; }
  .cl-table tbody tr.totals-row td:nth-child(4)::before { content: "Total Balance"; font-size: .56rem; }
}
</style>

<div class="rp-cl">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="rp-hdr">
      <div class="rp-hdr-title">
        <div class="rp-hdr-icon">
          <i class="bi bi-person-lines-fill"></i>
        </div>
        <div>
          <h2>Customer Ledger Statement</h2>
          <span class="rp-hdr-badge">Receivables & Transaction History</span>
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
        <form id="ledgerForm" class="row g-3 align-items-end">
          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-person me-1 text-primary"></i> Select Customer</label>
            <select name="customer_id" id="customer_id" class="rp-select" required>
              <option value="">Select Customer</option>
              @foreach($customers as $c)
              <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-minus me-1 text-primary"></i> Start Date</label>
            <input type="date" name="start_date" id="start_date" class="rp-input" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-12 col-md-3">
            <label class="rp-label"><i class="bi bi-calendar-plus me-1 text-primary"></i> End Date</label>
            <input type="date" name="end_date" id="end_date" class="rp-input" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-12 col-md-3">
            <button type="button" id="btnSearch" class="rp-btn rp-btn-primary w-100">
              <i class="bi bi-search me-1"></i> Generate Ledger
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══════ LEDGER RESULT CARD ═══════ --}}
    <div class="rp-card">
      <div class="rp-card-body">
        <div id="loader" class="rp-loader">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2 text-muted small">Building customer ledger statement...</p>
        </div>

        <div class="text-end mb-3">
          <button id="exportPdfBtn" class="rp-btn rp-btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF Statement
          </button>
        </div>

        <div id="ledgerBox" style="display:none;">
          <div class="ledger-box" id="ledgerPdfArea">
            <div class="ledger-title">CUSTOMER LEDGER STATEMENT</div>
            <div id="ledgerHeader" class="ledger-header"></div>

            <div class="table-responsive">
              <table class="cl-table">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $(document).on('click', '#btnSearch', function() {
        let cid = $("#customer_id").val();
        let start = $("#start_date").val();
        let end = $("#end_date").val();

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        if (!cid || !start || !end) {
            alert("Please select customer and date range.");
            return;
        }

        $("#loader").show();
        $.get("{{ route('report.customer.ledger.fetch') }}", {
            customer_id: cid,
            start_date: start,
            end_date: end
        }, function(res) {
            $("#loader").hide();
            $("#ledgerBox").show();

            $("#ledgerHeader").html(`
                <span><strong>Customer:</strong> ${res.customer.customer_name}</span>
                <span><strong>Duration:</strong> ${formatDate(start)} to ${formatDate(end)}</span>
            `);

            let openingBalance = parseFloat(res.opening_balance);

            let html = `
            <tr class="opening-row">
                <td></td>
                <td></td>
                <td class="text-left"><strong>Opening Balance</strong></td>
                <td>-</td>
                <td>-</td>
                <td class="balance-neutral">
                    Rs. ${openingBalance.toFixed(2)}
                </td>
            </tr>
            `;

            let totalDebit = 0;
            let totalCredit = 0;
            let lastBalance = openingBalance;

            if (res.transactions.length > 0) {
                res.transactions.forEach((t) => {
                    let debit = t.debit && t.debit > 0 ? parseFloat(t.debit) : 0;
                    let credit = t.credit && t.credit > 0 ? parseFloat(t.credit) : 0;
                    let isReturn = t.description === 'By Sale Return';

                    totalDebit += debit;
                    totalCredit += credit;
                    lastBalance = parseFloat(t.balance);

                    html += `
                    <tr ${isReturn ? 'style="background:#fef2f2;"' : ''}>
                        <td class="text-muted">${formatDate(t.date.split(" ")[0])}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">${t.invoice ?? '-'}</span>
                            ${isReturn ? '<br><small class="text-danger fw-bold">SALE RETURN</small>' : ''}
                            (${t.reference ?? '-'})
                        </td>
                        <td class="text-left">
                            ${isReturn ? '<strong class="text-danger">By Sale Return</strong>' : t.description}
                        </td>
                        <td class="fw-bold">${debit > 0 ? 'Rs. ' + debit.toFixed(2) : '-'}</td>
                        <td class="fw-bold">${credit > 0 ? 'Rs. ' + credit.toFixed(2) : '-'}</td>
                        <td class="${lastBalance > 0 ? 'balance-positive' : (lastBalance < 0 ? 'balance-negative' : 'balance-neutral')}">
                            Rs. ${lastBalance.toFixed(2)}
                        </td>
                    </tr>
                    `;
                });
            } else {
                lastBalance = openingBalance;
            }

            html += `
            <tr class="totals-row">
                <td colspan="3" class="text-left">Totals:</td>
                <td>${totalDebit > 0 ? 'Rs. ' + totalDebit.toFixed(2) : '-'}</td>
                <td>${totalCredit > 0 ? 'Rs. ' + totalCredit.toFixed(2) : '-'}</td>
                <td class="${lastBalance > 0 ? 'balance-positive' : (lastBalance < 0 ? 'balance-negative' : 'balance-neutral')}">
                    Rs. ${lastBalance.toFixed(2)}
                </td>
            </tr>
            `;

            $("#ledgerBody").html(html);
        });
    });

    $("#exportPdfBtn").on("click", function() {
        if ($("#ledgerBox").is(":hidden")) {
            alert("Please generate ledger first");
            return;
        }

        const element = document.getElementById("ledgerPdfArea");

        const opt = {
            margin: [10, 10, 10, 10],
            filename: 'Customer_Ledger.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
    });
});
</script>
@endsection
