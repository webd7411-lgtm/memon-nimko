@extends('admin_panel.layout.app')
@section('content')
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
.pc-page { background: var(--pc-bg); min-height: 100vh; padding-bottom: 2.5rem; }

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
  padding: .5rem 1.35rem;
}

.pc-btn-primary {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff;
}
.pc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }

.pc-btn-dark { background: #0b1a33; color: #fff; }
.pc-btn-dark:hover { background: #162d50; color: #fff; }

/* ═══════ CARD ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  transition: box-shadow .3s ease;
}
.pc-card-body { padding: 1.5rem; }

/* ═══════ SUMMARY CARDS ═══════ */
.pc-sum-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 14px;
  margin-bottom: 16px;
}

.pc-sum-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 14px 18px;
  box-shadow: var(--pc-shadow);
  border-left: 4px solid var(--pc-accent);
}

.pc-sum-card.green { border-color: var(--pc-success); }
.pc-sum-card.purple { border-color: #764ba2; }

.pc-sum-card .lbl {
  font-size: .68rem;
  color: var(--pc-text-muted);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
}

.pc-sum-card .val {
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--pc-text);
  margin-top: 4px;
}

/* ═══════ FILTER BAR ═══════ */
.pc-filter {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 16px 18px;
  box-shadow: var(--pc-shadow);
  margin-bottom: 16px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: flex-end;
}

.pc-filter .fg { display: flex; flex-direction: column; gap: 4px; }
.pc-filter label {
  font-size: .68rem;
  font-weight: 700;
  color: var(--pc-text-sec);
  text-transform: uppercase;
  letter-spacing: .4px;
}

.pc-filter .pc-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: 7px;
  padding: .45rem .75rem;
  font-size: .83rem;
  font-weight: 500;
  color: var(--pc-text);
  outline: none;
  transition: all .2s ease;
}
.pc-filter .pc-fld:focus { border-color: var(--pc-accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }

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
  font-size: .85rem;
}

.pc-tbl thead th {
  background: #0f172a;
  color: #f8fafc;
  font-size: .72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  padding: .75rem .85rem;
  border-bottom: 2px solid var(--pc-border);
  white-space: nowrap;
}

.pc-tbl thead th.branch-hdr {
  background: #1e293b;
  color: #60a5fa;
  text-align: center;
}

.pc-tbl tbody td { padding: .6rem .85rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; }
.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #f8fafc; }
.pc-tbl tbody td.num { text-align: center; font-variant-numeric: tabular-nums; font-weight: 600; }
.pc-tbl tbody td.bold { font-weight: 700; }

.branch-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-weight: 700;
  font-size: .8rem;
  background: #f1f5f9;
  color: #334155;
}
.branch-badge.has-stock {
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
}
.branch-badge.zero-stock {
  background: #f8fafc;
  color: #94a3b8;
}

.tot-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 6px;
  font-weight: 800;
  font-size: .85rem;
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}
.tot-badge.zero {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fecaca;
}

.pc-code {
  font-size: .75rem;
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Consolas', monospace;
  color: #475569;
}

.pc-tfoot {
  background: #0f172a;
  color: #ffffff;
  font-weight: 700;
}

.pc-tfoot td {
  padding: .75rem .85rem;
  border-top: 2px solid var(--pc-border);
  font-size: .85rem;
  color: #fff;
}

/* ═══════ LIVE SEARCH ═══════ */
.pc-ls-wrap { position: relative; }
.pc-ls-wrap input {
  padding-left: 32px; border: 1.5px solid var(--pc-border); border-radius: 7px;
  padding: .45rem .7rem .45rem 32px; font-size: .82rem; outline: none;
  min-width: 250px;
}
.pc-ls-wrap input:focus { border-color: var(--pc-accent); }
.pc-ls-wrap .ls-ico {
  position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
  color: var(--pc-text-muted); font-size: 14px; pointer-events: none;
}

.pc-empty { text-align: center; padding: 2.5rem .85rem; color: var(--pc-text-muted); }

.spin {
  display: inline-block;
  animation: spin 1s linear infinite;
}
@keyframes spin { 100% { transform: rotate(360deg); } }

/* ═══════ PRINT STYLES ═══════ */
.print-only-header { display: none; }

@media print {
  /* Hide standard ERP chrome & filters */
  nav, .rt_nav_header, .top_nav, .nav-bottom, .pc-hdr, .pc-filter, .pc-sum-grid, .hdr-actions, .pc-ls-wrap, footer, .navbar-toggler, #mobileNavToggle {
    display: none !important;
  }

  body, .pc-page, .container-fluid, .pc-card, .pc-card-body {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    box-shadow: none !important;
    width: 100% !important;
  }

  .print-only-header {
    display: block !important;
    text-align: center;
    margin-bottom: 15px !important;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
  }

  .print-only-header h2 {
    font-size: 22px;
    font-weight: 800;
    margin: 0;
    text-transform: uppercase;
  }

  .print-only-header h4 {
    font-size: 15px;
    font-weight: 700;
    margin: 3px 0;
    color: #333;
  }

  .print-only-header p {
    font-size: 11px;
    margin: 2px 0;
    color: #555;
  }

  .pc-tbl-wrap {
    max-height: none !important;
    overflow: visible !important;
    border: none !important;
  }

  .pc-tbl {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 9.5pt !important;
  }

  .pc-tbl th, .pc-tbl td {
    border: 1px solid #475569 !important;
    padding: 5px 8px !important;
    color: #000 !important;
    background: #fff !important;
  }

  .pc-tbl thead th {
    background: #0f172a !important;
    color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    font-size: 9pt !important;
  }

  .pc-tbl thead th.branch-hdr {
    background: #1e293b !important;
    color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .pc-tfoot {
    background: #0f172a !important;
    color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .pc-tfoot td {
    background: #0f172a !important;
    color: #ffffff !important;
    font-weight: bold !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .branch-badge, .tot-badge {
    background: transparent !important;
    border: none !important;
    color: #000 !important;
    font-weight: bold !important;
    padding: 0 !important;
  }

  .tot-badge.zero, .branch-badge.zero-stock {
    color: #94a3b8 !important;
  }

  @page {
    size: A4 landscape;
    margin: 8mm;
  }
}
</style>

<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══ PRINT HEADER (VISIBLE ONLY ON PRINT) ═══ --}}
    <div class="print-only-header">
      <h2>Memon Nimkos</h2>
      <h4>Branch-wise Stock Matrix Report</h4>
      <p>Printed Date: {{ date('d-M-Y h:i A') }} | Category: <span id="printCatText">All Categories</span></p>
    </div>

    {{-- ═══ HEADER ═══ --}}
    <div class="pc-hdr">
      <div class="d-flex align-items-center gap-3">
        <h2><i class="bi bi-shop text-info"></i> Branch-wise Stock Matrix</h2>
        <span class="badge bg-primary px-3 py-2" id="totalBadge" style="font-size: .8rem; border-radius: 8px;">Loading...</span>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap hdr-actions">
        <button class="pc-btn pc-btn-primary" onclick="exportToExcel()">
          <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </button>
        <button class="pc-btn pc-btn-dark" onclick="window.print()">
          <i class="bi bi-printer-fill me-1"></i> Print Matrix
        </button>
      </div>
    </div>

    {{-- ═══ SUMMARY CARDS ═══ --}}
    <div class="pc-sum-grid">
      <div class="pc-sum-card">
        <div class="lbl"><i class="bi bi-boxes me-1 text-primary"></i> Total Items</div>
        <div class="val" id="cTotalItems">–</div>
      </div>
      <div class="pc-sum-card purple">
        <div class="lbl"><i class="bi bi-diagram-3 me-1 text-purple"></i> Active Branches</div>
        <div class="val" id="cBranches">{{ count($branches) }} Branches</div>
      </div>
      <div class="pc-sum-card green">
        <div class="lbl"><i class="bi bi-pie-chart-fill me-1 text-success"></i> Consolidated Total Stock</div>
        <div class="val" id="cTotalStock">–</div>
      </div>
    </div>

    {{-- ═══ FILTER BAR ═══ --}}
    <div class="pc-filter">
      <div class="fg" style="min-width: 220px;">
        <label><i class="bi bi-funnel me-1"></i> Filter by Category</label>
        <select id="category_id" class="pc-fld" onchange="fetchReport()">
          <option value="all">— All Categories —</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="fg" style="flex:1; min-width: 240px;">
        <label><i class="bi bi-search me-1"></i> Search Product</label>
        <div class="pc-ls-wrap">
          <i class="bi bi-search ls-ico"></i>
          <input type="text" id="liveSearch" placeholder="Search by name, code or barcode..." oninput="applyFilter()">
        </div>
      </div>

      <button class="pc-btn pc-btn-primary" onclick="fetchReport()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
    </div>

    {{-- ═══ TABLE CARD ═══ --}}
    <div class="pc-card">
      <div class="pc-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 style="font-weight:700;color:var(--pc-text);margin:0;font-size:.95rem;">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Stock Breakdown per Branch
          </h6>
          <span style="font-size:.8rem;color:var(--pc-text-muted);font-weight:600;" id="rowCount">Showing 0 items</span>
        </div>

        <div class="pc-tbl-wrap" style="max-height:calc(100vh - 350px); overflow-y:auto;">
          <table class="pc-tbl" id="matrixTable">
            <thead style="position:sticky; top:0; z-index:3;">
              <tr>
                <th style="width: 50px;">#</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                @foreach($branches as $b)
                  <th class="branch-hdr">📍 {{ $b->name }}</th>
                @endforeach
                <th class="text-center" style="background:#047857; color:#fff;">Total Stock</th>
              </tr>
            </thead>
            <tbody id="reportBody">
              <tr>
                <td colspan="{{ 5 + count($branches) }}" class="pc-empty">
                  <i class="bi bi-arrow-repeat spin text-primary fs-3 d-block mb-2"></i>
                  <span>Loading Branch Stock Matrix...</span>
                </td>
              </tr>
            </tbody>
            <tfoot class="pc-tfoot">
              <tr>
                <td colspan="4" style="text-align:right; font-weight:800; letter-spacing: 0.5px;">BRANCH TOTALS:</td>
                @foreach($branches as $b)
                  <td class="num bold" id="ftBranch_{{ $b->id }}">–</td>
                @endforeach
                <td class="num bold text-center" id="ftGrandTotal" style="color:#34d399; font-size:1rem;">–</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
let branchesList = @json($branches);
let allRows = [];

$(document).ready(function() {
    fetchReport();
});

function fetchReport() {
    const categoryId = $('#category_id').val();
    const searchQ    = $('#liveSearch').val();
    const catText    = $('#category_id option:selected').text();
    $('#printCatText').text(catText);
    
    $('#reportBody').html('<tr><td colspan="' + (5 + branchesList.length) + '" class="pc-empty"><i class="bi bi-arrow-repeat spin text-primary fs-3 d-block mb-2"></i><span>Loading Branch Stock Matrix...</span></td></tr>');

    $.ajax({
        url: "{{ route('report.branch_stock.fetch') }}",
        type: "GET",
        data: { category_id: categoryId, q: searchQ },
        dataType: "json",
        success: function(res) {
            branchesList = res.branches || branchesList;
            allRows = res.data || [];
            applyFilter();
        },
        error: function(err) {
            console.error(err);
            $('#reportBody').html('<tr><td colspan="' + (5 + branchesList.length) + '" class="pc-empty" style="color:var(--pc-danger)"><i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-2"></i><span>Failed to load stock data. Please retry.</span></td></tr>');
        }
    });
}

function applyFilter() {
    const q = (document.getElementById('liveSearch').value || '').toLowerCase().trim();
    const vis = q ? allRows.filter(r => (r.item_name||'').toLowerCase().includes(q) || (r.item_code||'').toLowerCase().includes(q) || (r.category||'').toLowerCase().includes(q)) : allRows;
    renderTable(vis);
}

function formatVal(val, isKg, unit) {
    val = parseFloat(val) || 0;
    if (!isKg) {
        let fmtVal = Number.isInteger(val) ? val : val.toFixed(2);
        return fmtVal + ' ' + (unit || 'PC');
    }
    let isNegative = val < 0;
    let grams = Math.abs(val);
    if (grams >= 1000) {
        const kg = Math.floor(grams / 1000);
        const gm = Math.round(grams % 1000);
        return (isNegative ? '-' : '') + kg + 'kg' + (gm > 0 ? ' ' + gm + 'g' : '');
    } else if (grams > 0) {
        return (isNegative ? '-' : '') + Math.round(grams) + 'g';
    }
    return '0';
}

function renderTable(rows) {
    const tbody = document.getElementById('reportBody');
    if (!rows || !rows.length) {
        tbody.innerHTML = '<tr><td colspan="' + (5 + branchesList.length) + '" class="pc-empty"><i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i><span>No products found matching your search.</span></td></tr>';
        $('#rowCount').text('Showing 0 items');
        $('#cTotalItems').text('0 Items');
        $('#cTotalStock').text('0');
        clearFooter();
        return;
    }

    let html = '';
    let branchTotals = {};
    branchesList.forEach(b => { branchTotals[b.id] = 0; });
    let grandTotalStock = 0;

    rows.forEach((r, idx) => {
        let branchCells = '';
        branchesList.forEach(b => {
            const qty = parseFloat(r.branch_stocks[b.id]) || 0;
            branchTotals[b.id] += qty;
            
            const formatted = formatVal(qty, r.is_kg, r.unit);
            const badgeClass = qty > 0 ? 'has-stock' : 'zero-stock';
            branchCells += `<td class="num"><span class="branch-badge ${badgeClass}">${formatted}</span></td>`;
        });

        const totalQty = parseFloat(r.total_stock) || 0;
        grandTotalStock += totalQty;
        const formattedTotal = formatVal(totalQty, r.is_kg, r.unit);
        const totBadgeClass = totalQty > 0 ? 'tot-badge' : 'tot-badge zero';

        html += `
            <tr>
                <td style="color:#94a3b8; font-weight:600;">${idx + 1}</td>
                <td><span class="pc-code">${r.item_code}</span></td>
                <td style="font-weight:700; color:var(--pc-text);">${r.item_name}</td>
                <td><span class="badge bg-light text-dark font-weight-bold" style="border: 1px solid #cbd5e1;">${r.category}</span></td>
                ${branchCells}
                <td class="text-center"><span class="${totBadgeClass}">${formattedTotal}</span></td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    $('#rowCount').text('Showing ' + rows.length + ' items');
    $('#totalBadge').text(rows.length + ' Products');
    $('#cTotalItems').text(rows.length + ' Items');
    $('#cTotalStock').text(rows.length + ' Items Loaded');

    // Update Footer
    branchesList.forEach(b => {
        $(`#ftBranch_${b.id}`).text(branchTotals[b.id].toLocaleString() + ' qty');
    });
    $('#ftGrandTotal').text(grandTotalStock.toLocaleString() + ' Total Qty');
}

function clearFooter() {
    branchesList.forEach(b => {
        $(`#ftBranch_${b.id}`).text('0');
    });
    $('#ftGrandTotal').text('0');
}

function exportToExcel() {
    let table = document.querySelector(".pc-tbl");
    if (!table) return;
    let html = table.outerHTML;
    let blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Branch_Stock_Matrix_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
@endsection
