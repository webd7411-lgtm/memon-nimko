@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --rp-bg: #f8fafc;
  --rp-surface: #ffffff;
  --rp-border: #e2e8f0;
  --rp-primary: #0284c7;
  --rp-primary-dark: #0369a1;
  --rp-secondary: #0f766e;
  --rp-accent: #f59e0b;
  --rp-text-main: #0f172a;
  --rp-text-muted: #64748b;
  --rp-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --rp-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --rp-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --rp-radius: 14px;
}

body {
  background-color: var(--rp-bg);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: var(--rp-text-main);
}

.rp-container {
  max-width: 1440px;
  margin: 0 auto;
  padding: 1.5rem 1rem;
}

/* Header */
.rp-hdr {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}
.rp-hdr-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #0284c7, #0369a1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.5rem;
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
}
.rp-hdr h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: var(--rp-text-main);
  margin: 0;
  letter-spacing: -0.02em;
}
.rp-hdr-badge {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--rp-primary);
  background: #e0f2fe;
  padding: 3px 10px;
  border-radius: 9999px;
  display: inline-block;
  margin-top: 3px;
}

/* Card */
.rp-card {
  background: var(--rp-surface);
  border-radius: var(--rp-radius);
  border: 1px solid var(--rp-border);
  box-shadow: var(--rp-shadow-sm);
  margin-bottom: 1.5rem;
  overflow: hidden;
}
.rp-card-body {
  padding: 1.25rem;
}

/* Form inputs */
.rp-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--rp-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 0.35rem;
  display: block;
}
.rp-input {
  width: 100%;
  border: 1px solid var(--rp-border);
  border-radius: 8px;
  padding: 0.48rem 0.75rem;
  font-size: 0.88rem;
  color: var(--rp-text-main);
  background-color: #fff;
  transition: all 0.15s ease;
}
.rp-input:focus {
  outline: none;
  border-color: var(--rp-primary);
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Buttons */
.rp-btn {
  font-size: 0.86rem;
  font-weight: 600;
  padding: 0.5rem 1.15rem;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}
.rp-btn-primary {
  background: linear-gradient(135deg, var(--rp-primary), var(--rp-primary-dark));
  color: #fff;
  box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
}
.rp-btn-primary:hover {
  filter: brightness(1.08);
  color: #fff;
}
.rp-btn-glass {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid var(--rp-border);
}
.rp-btn-glass:hover {
  background: #e2e8f0;
}

/* KPI Stat Cards */
.kpi-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.kpi-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid var(--rp-border);
  padding: 1.1rem 1.25rem;
  box-shadow: var(--rp-shadow-sm);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.kpi-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}
.kpi-card.direct::before { background: #0284c7; }
.kpi-card.distributor::before { background: #0f766e; }
.kpi-card.bookers::before { background: #8b5cf6; }
.kpi-card.market::before { background: #10b981; }

.kpi-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.4rem;
}
.kpi-title {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--rp-text-muted);
  letter-spacing: 0.03em;
}
.kpi-icon {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}
.kpi-card.direct .kpi-icon { background: rgba(2, 132, 199, 0.1); color: #0284c7; }
.kpi-card.distributor .kpi-icon { background: rgba(15, 118, 110, 0.1); color: #0f766e; }
.kpi-card.bookers .kpi-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.kpi-card.market .kpi-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.kpi-value {
  font-size: 1.55rem;
  font-weight: 900;
  color: var(--rp-text-main);
  line-height: 1.2;
}
.kpi-sub {
  font-size: 0.78rem;
  color: var(--rp-text-muted);
  margin-top: 0.35rem;
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.kpi-sub span strong {
  color: #1e293b;
}

/* Nav Tabs */
.rp-tabs {
  display: flex;
  gap: 0.5rem;
  border-bottom: 2px solid var(--rp-border);
  margin-bottom: 1.25rem;
}
.rp-tab-btn {
  background: transparent;
  border: none;
  padding: 0.7rem 1.2rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--rp-text-muted);
  cursor: pointer;
  position: relative;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.rp-tab-btn.active {
  color: var(--rp-primary);
}
.rp-tab-btn.active::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 100%;
  height: 2px;
  background: var(--rp-primary);
}
.rp-tab-btn .badge {
  font-size: 0.7rem;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
}

/* Tables */
.table-custom {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.table-custom thead th {
  background: #f8fafc;
  font-size: 0.78rem;
  text-transform: uppercase;
  color: #475569;
  font-weight: 700;
  letter-spacing: 0.03em;
  padding: 0.75rem 0.9rem;
  border-bottom: 2px solid #e2e8f0;
}
.table-custom tbody td {
  padding: 0.75rem 0.9rem;
  font-size: 0.85rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.table-custom tbody tr:hover {
  background-color: #f8fafc;
}

/* Hierarchical Accordion Rows */
.dist-row {
  cursor: pointer;
  background-color: #ffffff;
  transition: background 0.15s ease;
}
.dist-row:hover {
  background-color: #f0f9ff !important;
}
.expand-icon {
  transition: transform 0.2s ease;
  display: inline-block;
  font-size: 0.75rem;
  color: var(--rp-primary);
}
.dist-row.expanded .expand-icon {
  transform: rotate(90deg);
}

.child-pane {
  background: #f8fafc;
  padding: 1rem 1.25rem;
  border-bottom: 2px solid #e2e8f0;
}

.nested-card {
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  padding: 1rem;
  margin-bottom: 0.75rem;
}
.nested-card-title {
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: #334155;
  margin-bottom: 0.65rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

/* Badges */
.badge-soft-primary { background: #e0f2fe; color: #0284c7; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.badge-soft-warning { background: #fef3c7; color: #b45309; }
.badge-soft-danger  { background: #fee2e2; color: #b91c1c; }
.badge-soft-purple  { background: #ede9fe; color: #6d28d9; }

/* Loader */
#loader {
  display: none;
  text-align: center;
  padding: 3rem 1rem;
}
</style>

<div class="rp-container">
  
  {{-- ═══════ HEADER ═══════ --}}
  <div class="rp-hdr">
    <div class="d-flex align-items-center gap-3">
      <div class="rp-hdr-icon">
        <i class="fas fa-sitemap"></i>
      </div>
      <div>
        <h2>Master Channel Operations & Sales Report</h2>
        <span class="rp-hdr-badge">Direct Customers • Distributors • Order Bookers • Retail Shops</span>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="button" class="rp-btn rp-btn-glass" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Print Report
      </button>
      <a href="{{ url()->previous() }}" class="rp-btn rp-btn-glass">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  {{-- ═══════ FILTER CARD ═══════ --}}
  <div class="rp-card">
    <div class="rp-card-body">
      <form id="reportFilterForm" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label class="rp-label"><i class="far fa-calendar-alt me-1 text-primary"></i> Start Date</label>
          <input type="date" name="start_date" id="start_date" class="rp-input" value="{{ date('Y-m-01') }}">
        </div>
        <div class="col-12 col-md-3">
          <label class="rp-label"><i class="far fa-calendar-check me-1 text-primary"></i> End Date</label>
          <input type="date" name="end_date" id="end_date" class="rp-input" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-12 col-md-3">
          <label class="rp-label"><i class="fas fa-store-alt me-1 text-primary"></i> Branch (Direct Sales)</label>
          <select name="branch_id" id="branch_id" class="rp-input">
            <option value="all">All Branches</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
          <button type="submit" class="rp-btn rp-btn-primary w-100">
            <i class="fas fa-search me-1"></i> Generate Report
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════ SUMMARY KPI HEADINGS (IN-DEPTH BREAKDOWN) ═══════ --}}
  <div class="kpi-row" id="kpiSection">
    
    <!-- 1. Direct Sales -->
    <div class="kpi-card direct">
      <div>
        <div class="kpi-header">
          <div class="kpi-title">Company Direct Sales</div>
          <div class="kpi-icon"><i class="fas fa-store"></i></div>
        </div>
        <div class="kpi-value" id="kpiDirectSales">₨ 0.00</div>
      </div>
      <div class="kpi-sub">
        <span>Paid: <strong id="kpiDirectPaid" class="text-success">₨ 0.00</strong></span>
        <span>Due/Ledger: <strong id="kpiDirectDue" class="text-danger">₨ 0.00</strong></span>
      </div>
    </div>

    <!-- 2. Primary Sales: Company to Distributors -->
    <div class="kpi-card distributor">
      <div>
        <div class="kpi-header">
          <div class="kpi-title">Company to Distributors</div>
          <div class="kpi-icon"><i class="fas fa-truck-moving"></i></div>
        </div>
        <div class="kpi-value" id="kpiDistOrders">₨ 0.00</div>
      </div>
      <div class="kpi-sub">
        <span>Channel: <strong>Primary Supply</strong></span>
        <span>Total Orders: <strong id="kpiDistOrderCount">0</strong></span>
      </div>
    </div>

    <!-- 3. Order Bookers Demand -->
    <div class="kpi-card bookers">
      <div>
        <div class="kpi-header">
          <div class="kpi-title">Bookers Market Demand</div>
          <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
        </div>
        <div class="kpi-value" id="kpiBookings">₨ 0.00</div>
      </div>
      <div class="kpi-sub">
        <span>Field Bookings from Retail Shops</span>
      </div>
    </div>

    <!-- 4. Secondary / Market Sales -->
    <div class="kpi-card market">
      <div>
        <div class="kpi-header">
          <div class="kpi-title">Overall Market / Retail Sales</div>
          <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="kpi-value" id="kpiMarketSales">₨ 0.00</div>
      </div>
      <div class="kpi-sub">
        <span>Collected: <strong id="kpiRetailPaid" class="text-success">₨ 0.00</strong></span>
        <span>Market Due: <strong id="kpiRetailDue" class="text-danger">₨ 0.00</strong></span>
      </div>
    </div>

  </div>

  {{-- ═══════ TABS FOR DRILL-DOWN VIEWS ═══════ --}}
  <div class="rp-tabs">
    <button type="button" class="rp-tab-btn active" data-tab="tab-distributors">
      <i class="fas fa-network-wired text-primary"></i> Distributors & Market Hierarchy
      <span class="badge badge-soft-primary" id="badgeDistCount">0</span>
    </button>
    <button type="button" class="rp-tab-btn" data-tab="tab-direct">
      <i class="fas fa-user-tag text-info"></i> Direct Customers & Ledgers
      <span class="badge badge-soft-primary" id="badgeDirectCount">0</span>
    </button>
  </div>

  {{-- Loader --}}
  <div id="loader">
    <div class="spinner-border text-primary" role="status"></div>
    <div class="mt-2 text-muted fw-bold">Analyzing channel sales, ledgers & order booker details...</div>
  </div>

  {{-- ═══════ TAB 1: DISTRIBUTORS & HIERARCHY ═══════ --}}
  <div id="tab-distributors" class="tab-content-pane">
    <div class="rp-card">
      <div class="rp-card-body p-0">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-layer-group text-primary me-2"></i>Distributor Drill-down: Click any row to expand Order Bookers, Shops & Ledgers</h6>
            <small class="text-muted">Single-record view of Company Sales, Bookers Performance, and Retail Shop Accounts</small>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleAll">
            <i class="fas fa-expand-arrows-alt me-1"></i> Expand / Collapse All
          </button>
        </div>

        <div class="table-responsive">
          <table class="table-custom" id="distHierarchyTable">
            <thead>
              <tr>
                <th width="3%"></th>
                <th>Distributor Name & Code</th>
                <th>City / Region</th>
                <th class="text-end">Ledger Balance</th>
                <th class="text-end">Company Sales (Primary)</th>
                <th class="text-end">Order Bookings</th>
                <th class="text-end">Retail Sales (Secondary)</th>
                <th class="text-center">Bookers / Shops</th>
              </tr>
            </thead>
            <tbody id="distHierarchyBody">
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">Click "Generate Report" to load distributor channel operations.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════ TAB 2: DIRECT CUSTOMERS & LEDGERS ═══════ --}}
  <div id="tab-direct" class="tab-content-pane" style="display: none;">
    <div class="rp-card">
      <div class="rp-card-body p-0">
        <div class="p-3 bg-light border-bottom">
          <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i>Company Direct Customers & Sale Ledgers</h6>
          <small class="text-muted">Summary of direct branch sales, cash receipts, and outstanding dues per customer</small>
        </div>

        <div class="table-responsive">
          <table class="table-custom" id="directCustomerTable">
            <thead>
              <tr>
                <th>Customer Name</th>
                <th>Type</th>
                <th>Contact</th>
                <th class="text-center">Invoices</th>
                <th class="text-end">Current Ledger Balance</th>
                <th class="text-end">Period Sales</th>
                <th class="text-end">Received (Cash)</th>
                <th class="text-end">Period Due</th>
              </tr>
            </thead>
            <tbody id="directCustomerBody">
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">Click "Generate Report" to view direct customer sales.</td>
              </tr>
            </tbody>
            <tfoot class="bg-light fw-bold">
              <tr>
                <td colspan="4" class="text-end">Grand Total Direct Customers:</td>
                <td class="text-end" id="ftDirectLedger">₨ 0.00</td>
                <td class="text-end text-primary" id="ftDirectSales">₨ 0.00</td>
                <td class="text-end text-success" id="ftDirectPaid">₨ 0.00</td>
                <td class="text-end text-danger" id="ftDirectDue">₨ 0.00</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {

  // Currency Formatter
  function money(val) {
    let num = parseFloat(val) || 0;
    return '₨ ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Tab switching
  $('.rp-tab-btn').on('click', function() {
    $('.rp-tab-btn').removeClass('active');
    $(this).addClass('active');

    let targetTab = $(this).data('tab');
    $('.tab-content-pane').hide();
    $('#' + targetTab).fadeIn(150);
  });

  // Load report on submit
  $('#reportFilterForm').on('submit', function(e) {
    e.preventDefault();
    loadReport();
  });

  // Initial load
  loadReport();

  function loadReport() {
    $('#loader').show();
    $('#distHierarchyBody').empty();
    $('#directCustomerBody').empty();

    $.ajax({
      url: "{{ route('report.consolidated_sales.fetch') }}",
      method: 'GET',
      data: $('#reportFilterForm').serialize(),
      dataType: 'json',
      success: function(res) {
        $('#loader').hide();
        if(!res.status) {
          alert('Error fetching report');
          return;
        }

        // 1. Fill KPI Summary
        $('#kpiDirectSales').text(money(res.summary.total_direct_sales));
        $('#kpiDirectPaid').text(money(res.summary.total_direct_paid));
        $('#kpiDirectDue').text(money(res.summary.total_direct_due));

        $('#kpiDistOrders').text(money(res.summary.total_company_dist_sales));
        $('#kpiBookings').text(money(res.summary.total_order_booker_bookings));
        
        $('#kpiMarketSales').text(money(res.summary.total_market_retail_sales));
        $('#kpiRetailPaid').text(money(res.summary.total_retail_paid));
        $('#kpiRetailDue').text(money(res.summary.total_retail_due));

        $('#badgeDistCount').text(res.hierarchy.length);
        $('#badgeDirectCount').text(res.direct_customers.length);

        // 2. Render Tab 1: Distributor Hierarchy
        renderHierarchy(res.hierarchy);

        // 3. Render Tab 2: Direct Customers
        renderDirectCustomers(res.direct_customers);
      },
      error: function(err) {
        $('#loader').hide();
        console.error(err);
        alert('An error occurred while loading data.');
      }
    });
  }

  // Render Hierarchical Drill-down (Distributor -> Booker -> Shop -> Ledger)
  function renderHierarchy(distributors) {
    let tbody = $('#distHierarchyBody');
    tbody.empty();

    if(distributors.length === 0) {
      tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No distributor data found for the selected dates.</td></tr>');
      return;
    }

    let totalDistOrders = 0;

    distributors.forEach(function(d, idx) {
      totalDistOrders += d.company_orders_count;

      let distRow = `
        <tr class="dist-row" data-target="#child_pane_${d.distributor_id}">
          <td class="text-center">
            <i class="fas fa-chevron-right expand-icon"></i>
          </td>
          <td>
            <div class="fw-bold text-dark">${d.name}</div>
            <small class="text-muted"><i class="fas fa-barcode me-1"></i>${d.code} | <i class="fas fa-phone me-1"></i>${d.phone}</small>
          </td>
          <td><span class="badge badge-soft-primary">${d.city}</span></td>
          <td class="text-end fw-bold ${d.ledger_balance > 0 ? 'text-danger' : 'text-success'}">
            ${money(d.ledger_balance)}
          </td>
          <td class="text-end fw-bold text-primary">
            ${money(d.company_sales_total)}
            <div class="small text-muted" style="font-size: 0.72rem;">${d.company_orders_count} Orders</div>
          </td>
          <td class="text-end fw-bold text-purple" style="color: #6d28d9;">
            ${money(d.bookings_total)}
          </td>
          <td class="text-end fw-bold text-success">
            ${money(d.market_sales_total)}
            <div class="small text-muted" style="font-size: 0.72rem;">Due: ${money(d.market_sales_due)}</div>
          </td>
          <td class="text-center">
            <span class="badge badge-soft-purple me-1" title="Order Bookers"><i class="fas fa-user-edit me-1"></i>${d.bookers_count} Bookers</span>
            <span class="badge badge-soft-primary" title="Retail Customers"><i class="fas fa-store me-1"></i>${d.customers_count} Shops</span>
          </td>
        </tr>
      `;

      // Child Drill-down Pane
      let bookersHtml = '';
      if(d.bookers.length > 0) {
        bookersHtml = `
          <div class="nested-card">
            <div class="nested-card-title text-purple" style="color: #6d28d9;">
              <i class="fas fa-users-cog"></i> Assigned Order Bookers (${d.bookers.length})
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead class="bg-light">
                  <tr>
                    <th>Booker Name</th>
                    <th>Phone</th>
                    <th class="text-center">Bookings Taken</th>
                    <th class="text-end">Total Booked Value</th>
                    <th class="text-center">Delivered Invoices</th>
                    <th class="text-end">Delivered Sales Value</th>
                    <th class="text-center">Shops Covered</th>
                  </tr>
                </thead>
                <tbody>
                  ${d.bookers.map(b => `
                    <tr>
                      <td class="fw-bold">${b.name}</td>
                      <td>${b.phone}</td>
                      <td class="text-center"><span class="badge bg-light text-dark">${b.booking_count}</span></td>
                      <td class="text-end fw-semibold text-purple" style="color: #6d28d9;">${money(b.total_bookings)}</td>
                      <td class="text-center"><span class="badge bg-light text-dark">${b.sales_count}</span></td>
                      <td class="text-end fw-bold text-success">${money(b.total_sales)}</td>
                      <td class="text-center"><span class="badge badge-soft-primary">${b.shops_covered}</span></td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        `;
      } else {
        bookersHtml = `<div class="nested-card"><small class="text-muted">No order bookers registered for this distributor yet.</small></div>`;
      }

      // Retail Shops / Customers Pane
      let shopsHtml = '';
      if(d.shops.length > 0) {
        shopsHtml = `
          <div class="nested-card">
            <div class="nested-card-title text-primary">
              <i class="fas fa-store"></i> Retail Shops / Customers & Ledgers (${d.shops.length})
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead class="bg-light">
                  <tr>
                    <th>Shop / Customer</th>
                    <th>Area / Phone</th>
                    <th class="text-center">Discount %</th>
                    <th class="text-end">Current Ledger Balance</th>
                    <th class="text-center">Period Invoices</th>
                    <th class="text-end">Period Sales</th>
                    <th class="text-end">Received (Cash)</th>
                    <th class="text-end">Outstanding Due</th>
                  </tr>
                </thead>
                <tbody>
                  ${d.shops.map(s => `
                    <tr>
                      <td>
                        <div class="fw-bold text-dark">${s.shop_name}</div>
                        <small class="text-muted">Owner: ${s.owner_name}</small>
                      </td>
                      <td>${s.area} | ${s.phone}</td>
                      <td class="text-center"><span class="badge bg-light text-dark">${s.approved_discount}%</span></td>
                      <td class="text-end fw-bold ${s.ledger_balance > 0 ? 'text-danger' : 'text-success'}">
                        ${money(s.ledger_balance)}
                      </td>
                      <td class="text-center"><span class="badge bg-light text-dark">${s.invoices_count}</span></td>
                      <td class="text-end fw-bold text-primary">${money(s.period_sales)}</td>
                      <td class="text-end text-success">${money(s.period_paid)}</td>
                      <td class="text-end fw-bold ${s.period_due > 0 ? 'text-danger' : 'text-secondary'}">
                        ${money(s.period_due)}
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>
        `;
      } else {
        shopsHtml = `<div class="nested-card"><small class="text-muted">No retail shops registered under this distributor yet.</small></div>`;
      }

      let childPane = `
        <tr id="child_pane_${d.distributor_id}" style="display: none;">
          <td colspan="8" class="child-pane">
            <div class="row g-2">
              <div class="col-12 col-lg-5">
                ${bookersHtml}
              </div>
              <div class="col-12 col-lg-7">
                ${shopsHtml}
              </div>
            </div>
          </td>
        </tr>
      `;

      tbody.append(distRow + childPane);
    });

    $('#kpiDistOrderCount').text(totalDistOrders);
  }

  // Toggle Row Expansion
  $(document).on('click', '.dist-row', function() {
    let targetId = $(this).data('target');
    let targetRow = $(targetId);
    
    if(targetRow.is(':visible')) {
      targetRow.hide();
      $(this).removeClass('expanded');
    } else {
      targetRow.fadeIn(150);
      $(this).addClass('expanded');
    }
  });

  // Expand / Collapse All
  let allExpanded = false;
  $('#btnToggleAll').on('click', function() {
    if(!allExpanded) {
      $('.dist-row').addClass('expanded');
      $('[id^="child_pane_"]').show();
      allExpanded = true;
    } else {
      $('.dist-row').removeClass('expanded');
      $('[id^="child_pane_"]').hide();
      allExpanded = false;
    }
  });

  // Render Direct Customers Tab
  function renderDirectCustomers(customers) {
    let tbody = $('#directCustomerBody');
    tbody.empty();

    if(customers.length === 0) {
      tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No direct customer sales recorded for this period.</td></tr>');
      $('#ftDirectLedger, #ftDirectSales, #ftDirectPaid, #ftDirectDue').text('₨ 0.00');
      return;
    }

    let totalLedger = 0;
    let totalSales  = 0;
    let totalPaid   = 0;
    let totalDue    = 0;

    customers.forEach(function(c) {
      totalLedger += c.ledger_balance;
      totalSales  += c.total_sales;
      totalPaid   += c.total_paid;
      totalDue    += c.total_due;

      let row = `
        <tr>
          <td>
            <div class="fw-bold text-dark">${c.customer_name}</div>
          </td>
          <td><span class="badge badge-soft-primary">${c.type}</span></td>
          <td>${c.phone}</td>
          <td class="text-center"><span class="badge bg-light text-dark">${c.total_invoices}</span></td>
          <td class="text-end fw-bold ${c.ledger_balance > 0 ? 'text-danger' : 'text-success'}">
            ${money(c.ledger_balance)}
          </td>
          <td class="text-end fw-bold text-primary">${money(c.total_sales)}</td>
          <td class="text-end text-success">${money(c.total_paid)}</td>
          <td class="text-end fw-bold ${c.total_due > 0 ? 'text-danger' : 'text-secondary'}">
            ${money(c.total_due)}
          </td>
        </tr>
      `;
      tbody.append(row);
    });

    $('#ftDirectLedger').text(money(totalLedger));
    $('#ftDirectSales').text(money(totalSales));
    $('#ftDirectPaid').text(money(totalPaid));
    $('#ftDirectDue').text(money(totalDue));
  }

});
</script>
@endpush
@endsection
