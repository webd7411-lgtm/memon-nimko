@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --pl-bg: #f8fafc;
  --pl-surface: #ffffff;
  --pl-border: #e2e8f0;
  --pl-border-lt: #f1f5f9;
  --pl-text: #0f172a;
  --pl-text-sec: #475569;
  --pl-text-muted: #64748b;
  --pl-primary: #0f766e;
  --pl-primary-drk: #115e59;
  --pl-success: #10b981;
  --pl-danger: #ef4444;
  --pl-warning: #f59e0b;
  --pl-radius: 16px;
  --pl-radius-sm: 10px;
  --pl-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --pl-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --pl-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.pl-page * { font-family: var(--pl-font); }
.pl-page { background-color: var(--pl-bg); min-height: 100vh; padding-bottom: 3rem; }

/* ═══════ HERO HEADER ═══════ */
.pl-hdr {
  position: relative;
  background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
  border-radius: var(--pl-radius);
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

.pl-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(15, 118, 110, 0.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.pl-hdr > * { position: relative; z-index: 1; }

.pl-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.pl-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.pl-hdr-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2dd4bf;
  font-size: 1.5rem;
}

.pl-hdr-badge {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #99f6e4;
  text-transform: uppercase;
}

/* ═══════ CARDS & FORMS ═══════ */
.pl-card {
  background: var(--pl-surface);
  border: 1px solid var(--pl-border);
  border-radius: var(--pl-radius);
  box-shadow: var(--pl-shadow);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.pl-card-body { padding: 1.25rem 1.5rem; }

.pl-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--pl-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.pl-input {
  border: 1px solid var(--pl-border);
  border-radius: var(--pl-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--pl-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}

.pl-input:focus {
  border-color: var(--pl-primary);
  box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
  outline: none;
}

.pl-select-main {
  font-weight: 700;
  font-size: 0.95rem;
  background-color: #f0fdfa !important;
  border: 2px solid #5eead4 !important;
  color: var(--pl-primary-drk) !important;
}

.pl-select-main:focus {
  border-color: var(--pl-primary) !important;
  box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.2) !important;
}

/* ═══════ BUTTONS ═══════ */
.pl-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  border-radius: var(--pl-radius-sm);
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  padding: 0.6rem 1.15rem;
  text-decoration: none;
  min-height: 42px;
}

.pl-btn-primary {
  background: linear-gradient(135deg, var(--pl-primary), var(--pl-primary-drk));
  color: #fff !important;
  box-shadow: 0 4px 14px rgba(15, 118, 110, 0.35);
}
.pl-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(15, 118, 110, 0.45);
}

.pl-btn-success {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff !important;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}
.pl-btn-success:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
}

.pl-btn-secondary {
  background: #ffffff;
  color: var(--pl-text-sec) !important;
  border: 1px solid var(--pl-border);
}
.pl-btn-secondary:hover { background: #f1f5f9; color: var(--pl-text) !important; }

/* ═══════ KPI STAT CARDS ═══════ */
.kpi-card {
  background: #ffffff;
  border-radius: var(--pl-radius);
  border: 1px solid var(--pl-border);
  padding: 1.15rem 1.25rem;
  box-shadow: var(--pl-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  transition: all 0.2s ease;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: var(--pl-shadow-lg); }

.kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.kpi-title {
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--pl-text-muted);
  margin-bottom: 0.2rem;
}

.kpi-value {
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--pl-text);
  line-height: 1.2;
}

.kpi-sub {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--pl-text-muted);
  margin-top: 0.2rem;
}

/* ═══════ DATA TABLE ═══════ */
.pl-table-card {
  background: var(--pl-surface);
  border: 1px solid var(--pl-border);
  border-radius: var(--pl-radius);
  box-shadow: var(--pl-shadow-lg);
  overflow: hidden;
}

.pl-table-wrapper {
  overflow-x: auto;
  padding: 0 1.25rem 1.25rem 1.25rem;
  -webkit-overflow-scrolling: touch;
}

.pl-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.pl-table thead th {
  background: #f8fafc;
  font-size: 0.74rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--pl-text-muted);
  padding: 0.85rem 0.9rem;
  border-bottom: 2px solid var(--pl-border);
  border-top: none;
  white-space: nowrap;
}

.pl-table tbody td {
  padding: 0.75rem 0.9rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--pl-border-lt);
  font-size: 0.86rem;
  color: var(--pl-text-sec);
}

.pl-table tbody tr:hover td {
  background-color: #f8fafc;
}

.pl-total-row td {
  background: #0f172a !important;
  color: #ffffff !important;
  font-weight: 800 !important;
  border-top: 2px solid #334155;
  border-bottom: none;
}

/* Badges */
.badge-profit {
  padding: 0.35rem 0.65rem;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.75rem;
}
.badge-profit-pos { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
.badge-profit-neg { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-profit-zero { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

/* Loading overlay */
#loadingOverlay {
  display: none;
  background: rgba(255, 255, 255, 0.85);
  position: absolute;
  inset: 0;
  z-index: 100;
  backdrop-filter: blur(3px);
  border-radius: var(--pl-radius);
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 0.75rem;
}

/* Print Styles */
@media print {
  .pl-hdr, .pl-card:first-of-type, .no-print, header, footer, .sidebar, #sidebar {
    display: none !important;
  }
  .pl-page { background: #fff !important; padding: 0 !important; }
  .pl-table-card { border: none !important; box-shadow: none !important; }
  .pl-table-wrapper { padding: 0 !important; }
  .kpi-card { border: 1px solid #000 !important; }
}
</style>

<div class="pl-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="pl-hdr">
      <div class="pl-hdr-title">
        <div class="pl-hdr-icon">
          <i class="fas fa-balance-scale"></i>
        </div>
        <div>
          <h2>Profit & Loss Intelligence Report</h2>
          <span class="pl-hdr-badge">Focused Multi-Dimension Financial Analysis</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-light" onclick="window.history.back()">
          <i class="fas fa-arrow-left me-1"></i> Back
        </button>
      </div>
    </div>

    {{-- ═══════ MAIN FILTER CARD ═══════ --}}
    <div class="pl-card">
      <div class="pl-card-body">
        <form id="ProfitLossFilterForm" class="row g-3 align-items-end">
          @csrf

          {{-- 1. PRIMARY REPORT TYPE DROPDOWN --}}
          <div class="col-12 col-md-4">
            <label class="pl-label text-primary">
              <i class="fas fa-layer-group me-1"></i> 1. Select Report Dimension
            </label>
            <select name="report_type" id="report_type" class="pl-input pl-select-main">
              <option value="product" selected>📦 Product-wise Profit & Loss</option>
              <option value="category">🏷️ Category-wise Profit & Loss</option>
              <option value="customer">👥 Customer-wise Profit & Loss</option>
              <option value="vendor">🏢 Vendor-wise Profit & Loss</option>
              <option value="date">📅 Date-wise (Daily) Profit & Loss</option>
              <option value="time">⏰ Time-wise (Hourly Peak) Profit & Loss</option>
            </select>
          </div>

          {{-- 2. DATE RANGE --}}
          <div class="col-6 col-md-2">
            <label class="pl-label"><i class="fas fa-calendar-alt me-1 text-primary"></i> From Date</label>
            <input type="date" name="start_date" id="start_date" class="pl-input" value="{{ date('Y-m-01') }}">
          </div>
          <div class="col-6 col-md-2">
            <label class="pl-label"><i class="fas fa-calendar-check me-1 text-primary"></i> To Date</label>
            <input type="date" name="end_date" id="end_date" class="pl-input" value="{{ date('Y-m-d') }}">
          </div>

          {{-- 3. BRANCH SELECTOR --}}
          <div class="col-12 col-md-2">
            <label class="pl-label"><i class="fas fa-store me-1 text-primary"></i> Branch</label>
            <select name="branch_id" id="branch_id" class="pl-input">
              <option value="all" {{ is_all_branches() ? 'selected' : '' }}>All Branches</option>
              @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ (!is_all_branches() && (string)$b->id === (string)active_branch_id()) ? 'selected' : '' }}>{{ $b->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- 4. ACTION BUTTONS --}}
          <div class="col-12 col-md-2 d-flex gap-2">
            <button type="button" id="btnFilter" class="pl-btn pl-btn-primary w-100">
              <i class="fas fa-search me-1"></i> Generate
            </button>
          </div>
        </form>

        {{-- OPTIONAL TIME RANGE COLLAPSIBLE --}}
        <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
          <button class="btn btn-link btn-sm text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#timeFilterCollapse">
            <i class="far fa-clock me-1 text-primary"></i> <span id="timeFilterToggleText">Time Filter (Optional 00:00 - 23:59)</span>
          </button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success fw-bold" id="btnExportExcel">
              <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="window.print()">
              <i class="fas fa-print me-1"></i> Print
            </button>
          </div>
        </div>

        <div class="collapse mt-2" id="timeFilterCollapse">
          <div class="row g-2 p-2 bg-light rounded border">
            <div class="col-6 col-md-3">
              <label class="small text-muted fw-bold">Start Time</label>
              <input type="time" name="start_time" id="start_time" class="pl-input form-control-sm" value="00:00">
            </div>
            <div class="col-6 col-md-3">
              <label class="small text-muted fw-bold">End Time</label>
              <input type="time" name="end_time" id="end_time" class="pl-input form-control-sm" value="23:59">
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- ═══════ KPI SUMMARY STAT CARDS ═══════ --}}
    <div class="row g-3 mb-4" id="kpiCardsArea">
      {{-- CARD 1: TOTAL SALES --}}
      <div class="col-6 col-lg-3">
        <div class="kpi-card" style="border-left: 4px solid #2563eb;">
          <div>
            <div class="kpi-title">Total Sales Revenue</div>
            <div class="kpi-value text-primary" id="kpiTotalSales">Rs 0</div>
            <div class="kpi-sub" id="kpiInvoicesCount">0 Invoices</div>
          </div>
          <div class="kpi-icon" style="background:#eff6ff;color:#2563eb;">
            <i class="fas fa-cash-register"></i>
          </div>
        </div>
      </div>

      {{-- CARD 2: TOTAL COGS --}}
      <div class="col-6 col-lg-3">
        <div class="kpi-card" style="border-left: 4px solid #64748b;">
          <div>
            <div class="kpi-title">Total Cost (COGS)</div>
            <div class="kpi-value text-secondary" id="kpiTotalCost">Rs 0</div>
            <div class="kpi-sub" id="kpiItemsQty">0 Items Sold</div>
          </div>
          <div class="kpi-icon" style="background:#f1f5f9;color:#64748b;">
            <i class="fas fa-boxes"></i>
          </div>
        </div>
      </div>

      {{-- CARD 3: GROSS PROFIT --}}
      <div class="col-6 col-lg-3">
        <div class="kpi-card" style="border-left: 4px solid #10b981;" id="kpiProfitCard">
          <div>
            <div class="kpi-title">Gross Profit</div>
            <div class="kpi-value text-success" id="kpiGrossProfit">Rs 0</div>
            <div class="kpi-sub" id="kpiNetProfitText">Revenue - Purchase Cost</div>
          </div>
          <div class="kpi-icon" style="background:#ecfdf5;color:#10b981;">
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
      </div>

      {{-- CARD 4: PROFIT MARGIN --}}
      <div class="col-6 col-lg-3">
        <div class="kpi-card" style="border-left: 4px solid #0f766e;">
          <div>
            <div class="kpi-title">Overall Margin %</div>
            <div class="kpi-value text-teal" id="kpiMarginPercent" style="color:var(--pl-primary);">0.00%</div>
            <div class="kpi-sub" id="kpiExpenseSub">Operating Margin</div>
          </div>
          <div class="kpi-icon" style="background:#f0fdfa;color:#0f766e;">
            <i class="fas fa-percentage"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══════ DYNAMIC REPORT DATA TABLE CARD ═══════ --}}
    <div class="pl-table-card position-relative">
      
      {{-- LOADING OVERLAY --}}
      <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <strong class="text-dark">Calculating Profit & Loss...</strong>
      </div>

      {{-- TABLE HEADER TOOLBAR --}}
      <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill" id="currentReportBadge">
            <i class="fas fa-box me-1"></i> Product-wise Profit & Loss
          </span>
          <span class="text-muted small fw-semibold" id="tableRecordCount">(0 records)</span>
        </div>

        {{-- Live Search Filter Box --}}
        <div style="min-width: 250px;">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter text-muted"></i></span>
            <input type="text" id="tableSearchInput" class="form-control border-start-0" placeholder="Quick search in table...">
          </div>
        </div>
      </div>

      {{-- TABLE CONTENT WRAPPER --}}
      <div class="pl-table-wrapper" id="printArea">
        <table class="pl-table table table-hover" id="profitLossTable">
          <thead id="tableHead">
            {{-- Injected dynamically --}}
          </thead>
          <tbody id="tableBody">
            <tr>
              <td colspan="12" class="text-center py-5 text-muted">
                <i class="fas fa-chart-pie fs-1 d-block mb-3 text-secondary opacity-50"></i>
                Please select options and click <strong>Generate</strong> to view the report.
              </td>
            </tr>
          </tbody>
          <tfoot id="tableFoot">
            {{-- Injected dynamically --}}
          </tfoot>
        </table>
      </div>

    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  
  // Format Number as Pakistani Rupee representation
  function formatMoney(num) {
    var val = parseFloat(num) || 0;
    return 'Rs ' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatPercent(num) {
    var val = parseFloat(num) || 0;
    return val.toFixed(2) + '%';
  }

  // Active loaded data store
  var activeData = [];
  var currentType = 'product';

  // Fetch Report Data
  function loadReport() {
    var rType     = $('#report_type').val();
    var startDate = $('#start_date').val();
    var endDate   = $('#end_date').val();
    var startTime = $('#start_time').val() || '00:00';
    var endTime   = $('#end_time').val() || '23:59';
    var branchId  = $('#branch_id').val();

    currentType = rType;

    // Show loading spinner
    $('#loadingOverlay').css('display', 'flex');

    $.ajax({
      url: "{{ route('report.profit_loss.fetch') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        report_type: rType,
        start_date: startDate,
        end_date: endDate,
        start_time: startTime,
        end_time: endTime,
        branch_id: branchId
      },
      dataType: "json",
      success: function(res) {
        $('#loadingOverlay').hide();
        if (res.success) {
          activeData = res.rows || [];
          renderSummary(res.summary, rType);
          renderTable(res.rows, rType);
        } else {
          alert('Error fetching report data');
        }
      },
      error: function(xhr) {
        $('#loadingOverlay').hide();
        console.error(xhr);
        alert('Server error while generating report. Please check date range.');
      }
    });
  }

  // Render KPI Top Cards
  function renderSummary(summary, type) {
    $('#kpiTotalSales').text(formatMoney(summary.total_sales));
    $('#kpiTotalCost').text(formatMoney(summary.total_cost));
    $('#kpiGrossProfit').text(formatMoney(summary.gross_profit));
    $('#kpiMarginPercent').text(formatPercent(summary.margin_percent));

    $('#kpiInvoicesCount').text((summary.invoices_count || 0) + ' Invoices');
    $('#kpiItemsQty').text((parseFloat(summary.items_sold_qty) || 0).toLocaleString() + ' Units Sold');

    if (summary.gross_profit < 0) {
      $('#kpiProfitCard').css('border-left', '4px solid #ef4444');
      $('#kpiGrossProfit').removeClass('text-success').addClass('text-danger');
    } else {
      $('#kpiProfitCard').css('border-left', '4px solid #10b981');
      $('#kpiGrossProfit').removeClass('text-danger').addClass('text-success');
    }

    if (type === 'date' && summary.total_expenses > 0) {
      $('#kpiNetProfitText').html('Net Profit: <strong class="' + (summary.net_profit >= 0 ? 'text-success' : 'text-danger') + '">' + formatMoney(summary.net_profit) + '</strong> (Exp: ' + formatMoney(summary.total_expenses) + ')');
    } else {
      $('#kpiNetProfitText').text('Revenue - Purchase Cost');
    }
  }

  // Render Table Head and Body specifically for chosen dimension
  function renderTable(rows, type) {
    var $thead = $('#tableHead');
    var $tbody = $('#tableBody');
    var $tfoot = $('#tableFoot');

    $thead.empty();
    $tbody.empty();
    $tfoot.empty();

    $('#tableRecordCount').text('(' + rows.length + ' records)');

    // Set Header Badge Text
    var badgeLabels = {
      product: '📦 Product-wise Profit & Loss',
      category: '🏷️ Category-wise Profit & Loss',
      customer: '👥 Customer-wise Profit & Loss',
      vendor: '🏢 Vendor-wise Profit & Loss',
      date: '📅 Date-wise (Daily) Profit & Loss',
      time: '⏰ Time-wise (Hourly Peak) Profit & Loss'
    };
    $('#currentReportBadge').html(badgeLabels[type] || 'Profit & Loss');

    if (!rows || rows.length === 0) {
      $tbody.html('<tr><td colspan="12" class="text-center py-5 text-muted"><i class="fas fa-info-circle fs-3 d-block mb-2 text-secondary"></i>No sales records found for the selected criteria.</td></tr>');
      return;
    }

    var totals = {
      qty: 0,
      sales: 0,
      cost: 0,
      profit: 0,
      expenses: 0,
      net: 0,
      invoices: 0
    };

    // ────────────────────── ① PRODUCT-WISE ──────────────────────
    if (type === 'product') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Item Code</th>
          <th>Product Name</th>
          <th>Category</th>
          <th class="text-end">Qty Sold</th>
          <th class="text-end">Avg Sale Price</th>
          <th class="text-end">Cost Price</th>
          <th class="text-end">Total Sales</th>
          <th class="text-end">Total Cost</th>
          <th class="text-end">Gross Profit</th>
          <th class="text-center">Margin %</th>
          <th class="text-center">Status</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.profit) || 0;

        var profitClass = r.profit > 0 ? 'text-success' : (r.profit < 0 ? 'text-danger fw-bold' : 'text-muted');
        var badge = r.profit > 0 
          ? '<span class="badge-profit badge-profit-pos">Profitable</span>' 
          : (r.profit < 0 ? '<span class="badge-profit badge-profit-neg">Loss ⚠️</span>' : '<span class="badge-profit badge-profit-zero">Even</span>');

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="font-monospace fw-bold">${r.item_code || '-'}</td>
            <td class="fw-bold text-dark">${r.item_name}</td>
            <td><span class="badge bg-light text-secondary border">${r.category}</span></td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end">Rs ${parseFloat(r.avg_sale_price).toFixed(2)}</td>
            <td class="text-end text-muted">Rs ${parseFloat(r.cost_price).toFixed(2)}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end fw-bold ${profitClass}">Rs ${parseFloat(r.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${profitClass}">${r.margin_percent}%</td>
            <td class="text-center">${badge}</td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.profit / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="4" class="text-uppercase">Total</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">-</td>
          <td class="text-end">-</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
          <td></td>
        </tr>
      `);
    }

    // ────────────────────── ② CATEGORY-WISE ──────────────────────
    else if (type === 'category') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Category Name</th>
          <th class="text-center">Products Sold</th>
          <th class="text-end">Total Units</th>
          <th class="text-end">Total Sales</th>
          <th class="text-end">Total Cost</th>
          <th class="text-end">Gross Profit</th>
          <th class="text-center">Margin %</th>
          <th class="text-center">Sales Contribution</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.profit) || 0;

        var profitClass = r.profit >= 0 ? 'text-success' : 'text-danger fw-bold';

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="fw-bold fs-6 text-dark">${r.category_name}</td>
            <td class="text-center"><span class="badge bg-primary-subtle text-primary border px-2 py-1">${r.products_count} Items</span></td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end fw-bold ${profitClass}">Rs ${parseFloat(r.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${profitClass}">${r.margin_percent}%</td>
            <td class="text-center">
              <div class="progress" style="height: 14px; min-width: 80px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: ${r.contribution_percent}%;">
                  ${r.contribution_percent}%
                </div>
              </div>
            </td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.profit / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="2" class="text-uppercase">Total</td>
          <td class="text-center">-</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
          <td class="text-center">100%</td>
        </tr>
      `);
    }

    // ────────────────────── ③ CUSTOMER-WISE ──────────────────────
    else if (type === 'customer') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Customer Name</th>
          <th>Contact / Type</th>
          <th class="text-center">Orders Count</th>
          <th class="text-end">Units Bought</th>
          <th class="text-end">Total Purchased Value</th>
          <th class="text-end">Total Cost</th>
          <th class="text-end">Profit Generated</th>
          <th class="text-center">Margin %</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.invoices += parseInt(r.invoices_count) || 0;
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.profit) || 0;

        var profitClass = r.profit >= 0 ? 'text-success' : 'text-danger fw-bold';

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i> ${r.customer_name}</td>
            <td><span class="badge bg-light text-secondary border font-monospace">${r.phone || '-'}</span></td>
            <td class="text-center fw-bold">${r.invoices_count}</td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end fw-bold ${profitClass}">Rs ${parseFloat(r.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${profitClass}">${r.margin_percent}%</td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.profit / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="3" class="text-uppercase">Total</td>
          <td class="text-center">${totals.invoices}</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
        </tr>
      `);
    }

    // ────────────────────── ④ VENDOR-WISE ──────────────────────
    else if (type === 'vendor') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Vendor Name</th>
          <th class="text-center">Products Supplied</th>
          <th class="text-end">Units Sold</th>
          <th class="text-end">Sales Value Generated</th>
          <th class="text-end">Purchase Cost Paid</th>
          <th class="text-end">Profit Earned</th>
          <th class="text-center">Margin %</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.profit) || 0;

        var profitClass = r.profit >= 0 ? 'text-success' : 'text-danger fw-bold';

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="fw-bold text-dark"><i class="fas fa-truck text-teal me-1"></i> ${r.vendor_name}</td>
            <td class="text-center"><span class="badge bg-info-subtle text-info-emphasis border px-2 py-1">${r.products_count} Items</span></td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end fw-bold ${profitClass}">Rs ${parseFloat(r.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${profitClass}">${r.margin_percent}%</td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.profit / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="2" class="text-uppercase">Total</td>
          <td class="text-center">-</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
        </tr>
      `);
    }

    // ────────────────────── ⑤ DATE-WISE ──────────────────────
    else if (type === 'date') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Date</th>
          <th>Day</th>
          <th class="text-center">Invoices</th>
          <th class="text-end">Units Sold</th>
          <th class="text-end">Total Sales</th>
          <th class="text-end">Total Cost</th>
          <th class="text-end">Gross Profit</th>
          <th class="text-end">Operating Expenses</th>
          <th class="text-end">Net Profit</th>
          <th class="text-center">Margin %</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.invoices += parseInt(r.invoices_count) || 0;
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.gross_profit) || 0;
        totals.expenses += parseFloat(r.expenses) || 0;
        totals.net += parseFloat(r.net_profit) || 0;

        var gpClass = r.gross_profit >= 0 ? 'text-success' : 'text-danger fw-bold';
        var npClass = r.net_profit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="fw-bold font-monospace text-dark">${r.date}</td>
            <td><span class="badge bg-light text-secondary border">${r.day}</span></td>
            <td class="text-center fw-bold">${r.invoices_count}</td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end ${gpClass}">Rs ${parseFloat(r.gross_profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-danger">Rs ${parseFloat(r.expenses).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end ${npClass}">Rs ${parseFloat(r.net_profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${npClass}">${r.margin_percent}%</td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.net / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="3" class="text-uppercase">Total</td>
          <td class="text-center">${totals.invoices}</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-danger">Rs ${totals.expenses.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success fs-6">Rs ${totals.net.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
        </tr>
      `);
    }

    // ────────────────────── ⑥ TIME-WISE (HOURLY) ──────────────────────
    else if (type === 'time') {
      $thead.html(`
        <tr>
          <th style="width: 50px;">#</th>
          <th>Hour Slot</th>
          <th class="text-center">Orders Count</th>
          <th class="text-end">Units Sold</th>
          <th class="text-end">Total Sales</th>
          <th class="text-end">Total Cost</th>
          <th class="text-end">Profit Earned</th>
          <th class="text-center">Margin %</th>
          <th class="text-center">Peak Intensity</th>
        </tr>
      `);

      rows.forEach(function(r, idx) {
        totals.invoices += parseInt(r.invoices_count) || 0;
        totals.qty += parseFloat(r.qty_sold) || 0;
        totals.sales += parseFloat(r.total_sales) || 0;
        totals.cost += parseFloat(r.total_cost) || 0;
        totals.profit += parseFloat(r.profit) || 0;

        var profitClass = r.profit >= 0 ? 'text-success' : 'text-danger fw-bold';

        $tbody.append(`
          <tr>
            <td class="text-muted small">${idx + 1}</td>
            <td class="fw-bold font-monospace text-dark fs-6"><i class="far fa-clock text-primary me-1"></i> ${r.time_slot}</td>
            <td class="text-center fw-bold">${r.invoices_count}</td>
            <td class="text-end fw-semibold">${parseFloat(r.qty_sold).toLocaleString()}</td>
            <td class="text-end fw-bold text-dark">Rs ${parseFloat(r.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-secondary">Rs ${parseFloat(r.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end fw-bold ${profitClass}">Rs ${parseFloat(r.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-center fw-bold ${profitClass}">${r.margin_percent}%</td>
            <td class="text-center"><span class="badge ${r.badge_class} px-3 py-1 fs-6">${r.intensity}</span></td>
          </tr>
        `);
      });

      var overallMargin = totals.sales > 0 ? ((totals.profit / totals.sales) * 100).toFixed(2) : 0;
      $tfoot.html(`
        <tr class="pl-total-row">
          <td colspan="2" class="text-uppercase">Total</td>
          <td class="text-center">${totals.invoices}</td>
          <td class="text-end">${totals.qty.toLocaleString()}</td>
          <td class="text-end">Rs ${totals.sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end">Rs ${totals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-end text-success">Rs ${totals.profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
          <td class="text-center">${overallMargin}%</td>
          <td></td>
        </tr>
      `);
    }
  }

  // Instant Live Search Filter in Table
  $('#tableSearchInput').on('keyup', function() {
    var val = $(this).val().toLowerCase();
    $('#tableBody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
    });
  });

  // When Dropdown Changes, auto-refresh report
  $('#report_type').on('change', function() {
    loadReport();
  });

  // Filter Button Click
  $('#btnFilter').on('click', function() {
    loadReport();
  });

  // Simple and clean Excel Export using HTML Blob
  $('#btnExportExcel').on('click', function() {
    var table = document.getElementById('profitLossTable');
    if (!table) return;

    var rType = $('#report_type').val();
    var filename = 'profit_loss_' + rType + '_' + ($('#start_date').val() || '') + '_to_' + ($('#end_date').val() || '') + '.xls';

    var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;}th{background:#f2f2f2;font-weight:bold;}</style></head><body>' + table.outerHTML + '</body></html>';
    var blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  });

  // Initial Load on page open
  loadReport();

});
</script>
@endsection
