@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --bs-bg: #f8fafc;
  --bs-surface: #ffffff;
  --bs-border: #e2e8f0;
  --bs-border-lt: #f1f5f9;
  --bs-text: #0f172a;
  --bs-text-sec: #475569;
  --bs-text-muted: #64748b;
  --bs-primary: #0f766e;
  --bs-primary-drk: #115e59;
  --bs-success: #10b981;
  --bs-danger: #ef4444;
  --bs-warning: #f59e0b;
  --bs-info: #0284c7;
  --bs-radius: 16px;
  --bs-radius-sm: 10px;
  --bs-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --bs-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --bs-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.bs-page * { font-family: var(--bs-font); }
.bs-page { background-color: var(--bs-bg); min-height: 100vh; padding-bottom: 3rem; }

/* Hero Header */
.bs-hdr {
  position: relative;
  background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
  border-radius: var(--bs-radius);
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

.bs-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(15, 118, 110, 0.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.bs-hdr > * { position: relative; z-index: 1; }

.bs-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.bs-hdr-title .hdr-icon {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  color: #34d399;
}

.bs-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.bs-hdr-title p {
  margin: 0.2rem 0 0 0;
  color: #94a3b8;
  font-size: 0.85rem;
}

.bs-hdr-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.bs-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1rem;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: var(--bs-radius-sm);
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.18s ease;
  text-decoration: none;
}

.bs-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff;
  border-color: rgba(255, 255, 255, 0.2);
}
.bs-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  color: #ffffff;
}

.bs-btn-primary {
  background: #10b981;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
}
.bs-btn-primary:hover {
  background: #059669;
  color: #ffffff;
}

/* Filter Card */
.bs-filter-card {
  background: var(--bs-surface);
  border: 1px solid var(--bs-border);
  border-radius: var(--bs-radius);
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--bs-shadow);
}

.bs-filter-card .form-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--bs-text-muted);
  margin-bottom: 0.35rem;
}

.bs-filter-card .form-control,
.bs-filter-card .form-select {
  border-radius: var(--bs-radius-sm);
  border-color: var(--bs-border);
  font-size: 0.88rem;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
}

/* KPI Summary Cards */
.bs-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.bs-kpi-card {
  background: var(--bs-surface);
  border: 1px solid var(--bs-border);
  border-radius: var(--bs-radius);
  padding: 1.25rem;
  box-shadow: var(--bs-shadow);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.bs-kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}
.bs-kpi-assets::before { background: linear-gradient(90deg, #10b981, #059669); }
.bs-kpi-liabilities::before { background: linear-gradient(90deg, #ef4444, #dc2626); }
.bs-kpi-equity::before { background: linear-gradient(90deg, #0ea5e9, #0284c7); }
.bs-kpi-status::before { background: linear-gradient(90deg, #8b5cf6, #6366f1); }

.bs-kpi-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.bs-kpi-title {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--bs-text-muted);
}

.bs-kpi-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}
.bs-kpi-assets .bs-kpi-icon { background: #ecfdf5; color: #10b981; }
.bs-kpi-liabilities .bs-kpi-icon { background: #fef2f2; color: #ef4444; }
.bs-kpi-equity .bs-kpi-icon { background: #f0f9ff; color: #0284c7; }
.bs-kpi-status .bs-kpi-icon { background: #f5f3ff; color: #8b5cf6; }

.bs-kpi-value {
  font-size: 1.45rem;
  font-weight: 800;
  color: var(--bs-text);
  margin-bottom: 0.25rem;
  letter-spacing: -0.02em;
}

.bs-kpi-sub {
  font-size: 0.75rem;
  color: var(--bs-text-muted);
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

/* Balance Sheet T-Account Layout */
.bs-sheet-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 991px) {
  .bs-sheet-container { grid-template-columns: 1fr; }
}

.bs-column-card {
  background: var(--bs-surface);
  border: 1px solid var(--bs-border);
  border-radius: var(--bs-radius);
  box-shadow: var(--bs-shadow);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.bs-column-header {
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--bs-border);
}
.bs-column-header.assets-hdr {
  background: #f0fdf4;
  border-bottom-color: #bbf7d0;
}
.bs-column-header.liab-hdr {
  background: #fef2f2;
  border-bottom-color: #fecaca;
}

.bs-column-title {
  font-size: 1rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.assets-hdr .bs-column-title { color: #065f46; }
.liab-hdr .bs-column-title { color: #991b1b; }

.bs-column-body {
  padding: 1.25rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* Category Sections inside columns */
.bs-section-box {
  background: #ffffff;
  border: 1px solid var(--bs-border);
  border-radius: var(--bs-radius-sm);
  overflow: hidden;
}

.bs-section-title {
  background: #f8fafc;
  padding: 0.6rem 0.9rem;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--bs-text-sec);
  border-bottom: 1px solid var(--bs-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.bs-row-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.65rem 0.9rem;
  border-bottom: 1px solid var(--bs-border-lt);
  font-size: 0.85rem;
  transition: background 0.15s ease;
}
.bs-row-item:last-child { border-bottom: none; }
.bs-row-item:hover { background: #f8fafc; }

.bs-row-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--bs-text);
  font-weight: 500;
}

.bs-row-badge {
  font-size: 0.7rem;
  padding: 0.15rem 0.45rem;
  border-radius: 6px;
  background: #f1f5f9;
  color: var(--bs-text-muted);
  cursor: pointer;
}
.bs-row-badge:hover { background: #e2e8f0; color: #0f172a; }

.bs-row-value {
  font-weight: 700;
  color: var(--bs-text);
  letter-spacing: -0.01em;
}

.bs-subtotal-row {
  background: #f8fafc;
  border-top: 1px dashed var(--bs-border);
  padding: 0.65rem 0.9rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--bs-text-sec);
}

/* Grand Total Footer for Columns */
.bs-column-footer {
  padding: 1.1rem 1.25rem;
  background: #f8fafc;
  border-top: 2px solid var(--bs-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.bs-column-footer.assets-ftr {
  background: #ecfdf5;
  border-top: 2px solid #10b981;
}
.bs-column-footer.liab-ftr {
  background: #f0f9ff;
  border-top: 2px solid #0284c7;
}

.bs-grand-title {
  font-size: 1.05rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}
.assets-ftr .bs-grand-title { color: #065f46; }
.liab-ftr .bs-grand-title { color: #075985; }

.bs-grand-val {
  font-size: 1.35rem;
  font-weight: 900;
  letter-spacing: -0.02em;
}
.assets-ftr .bs-grand-val {
  color: #047857;
  border-bottom: 3px double #047857;
}
.liab-ftr .bs-grand-val {
  color: #0369a1;
  border-bottom: 3px double #0369a1;
}

/* Health Badge */
.balance-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 700;
}
.status-balanced {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #86efac;
}

/* Modals */
.modal-content {
  border-radius: var(--bs-radius);
  border: 1px solid var(--bs-border);
  box-shadow: var(--bs-shadow-lg);
}
.modal-header {
  border-bottom: 1px solid var(--bs-border);
  padding: 1rem 1.5rem;
}
.modal-title { font-weight: 800; font-size: 1.1rem; }
.modal-body { padding: 1.25rem 1.5rem; }

/* Loading State */
#loadingOverlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(4px);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 0.85rem;
  color: #ffffff;
}
.spinner-ring {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255, 255, 255, 0.2);
  border-top-color: #34d399;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="bs-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="bs-hdr">
      <div class="bs-hdr-title">
        <div class="hdr-icon">
          <i class="fas fa-balance-scale"></i>
        </div>
        <div>
          <h2>Balance Sheet</h2>
          <p>Statement of Financial Position &bull; Assets, Liabilities &amp; Capital</p>
        </div>
      </div>
      <div class="bs-hdr-actions">
        <button type="button" class="bs-btn bs-btn-glass" id="btnExportExcel">
          <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button type="button" class="bs-btn bs-btn-glass" id="btnPrint">
          <i class="fas fa-print"></i> Print / PDF
        </button>
        <button type="button" class="bs-btn bs-btn-primary" id="btnRefresh">
          <i class="fas fa-sync-alt"></i> Refresh Data
        </button>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="bs-filter-card">
      <form id="balanceSheetFilterForm">
        <div class="row g-3 align-items-end">
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="form-label" for="as_of_date"><i class="far fa-calendar-alt me-1"></i> As on Date</label>
            <input type="date" class="form-control" id="as_of_date" name="as_of_date" value="{{ $asOfDate }}">
          </div>

          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="form-label" for="branch_id"><i class="fas fa-code-branch me-1"></i> Branch / Scope</label>
            <select class="form-select" id="branch_id" name="branch_id">
              <option value="all" selected>All Branches (Consolidated)</option>
              @foreach($branches as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-4 col-lg-3">
            <button type="submit" class="btn btn-primary w-100" style="padding: 0.55rem; font-weight:600; border-radius: var(--bs-radius-sm);">
              <i class="fas fa-filter me-1"></i> Generate Balance Sheet
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- ═══════ TOP KPI CARDS ═══════ --}}
    <div class="bs-kpi-grid">
      {{-- Card 1: Total Assets --}}
      <div class="bs-kpi-card bs-kpi-assets">
        <div>
          <div class="bs-kpi-head">
            <span class="bs-kpi-title">Total Assets</span>
            <div class="bs-kpi-icon"><i class="fas fa-coins"></i></div>
          </div>
          <div class="bs-kpi-value" id="kpiTotalAssets">Rs 0.00</div>
        </div>
        <div class="bs-kpi-sub">
          <span>Current: <strong id="kpiCurrentAssets">Rs 0</strong> &bull; Fixed: <strong id="kpiFixedAssets">Rs 0</strong></span>
        </div>
      </div>

      {{-- Card 2: Total Liabilities --}}
      <div class="bs-kpi-card bs-kpi-liabilities">
        <div>
          <div class="bs-kpi-head">
            <span class="bs-kpi-title">Total Liabilities</span>
            <div class="bs-kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
          </div>
          <div class="bs-kpi-value" id="kpiTotalLiabilities">Rs 0.00</div>
        </div>
        <div class="bs-kpi-sub">
          <span>Payables &amp; Creditors</span>
        </div>
      </div>

      {{-- Card 3: Owner's Equity --}}
      <div class="bs-kpi-card bs-kpi-equity">
        <div>
          <div class="bs-kpi-head">
            <span class="bs-kpi-title">Owner's Equity &amp; Net Worth</span>
            <div class="bs-kpi-icon"><i class="fas fa-user-shield"></i></div>
          </div>
          <div class="bs-kpi-value" id="kpiTotalEquity">Rs 0.00</div>
        </div>
        <div class="bs-kpi-sub">
          <span>Capital: <strong id="kpiInvestedCapital">Rs 0</strong> + Profit: <strong id="kpiNetProfit">Rs 0</strong></span>
        </div>
      </div>

      {{-- Card 4: Working Capital & Health --}}
      <div class="bs-kpi-card bs-kpi-status">
        <div>
          <div class="bs-kpi-head">
            <span class="bs-kpi-title">Working Capital</span>
            <div class="bs-kpi-icon"><i class="fas fa-heartbeat"></i></div>
          </div>
          <div class="bs-kpi-value" id="kpiWorkingCapital">Rs 0.00</div>
        </div>
        <div class="bs-kpi-sub">
          <span class="balance-status-pill status-balanced" id="kpiStatusBadge">
            <i class="fas fa-check-circle"></i> Balanced (Diff: Rs 0.00)
          </span>
        </div>
      </div>
    </div>

    {{-- ═══════ MAIN T-ACCOUNT DUAL COLUMN SHEET ═══════ --}}
    <div class="bs-sheet-container">

      {{-- ──────── LEFT COLUMN: ASSETS ──────── --}}
      <div class="bs-column-card">
        <div class="bs-column-header assets-hdr">
          <div class="bs-column-title">
            <i class="fas fa-arrow-circle-down"></i> ASSETS (Kul Asasay)
          </div>
          <span class="badge bg-success" style="font-size:0.75rem;">Debits</span>
        </div>

        <div class="bs-column-body">
          {{-- Section 1: Current Assets --}}
          <div class="bs-section-box">
            <div class="bs-section-title">
              <span>Current Assets (Rawaan Asasay)</span>
              <span class="text-muted" style="font-size:0.7rem;">Liquid Assets</span>
            </div>

            {{-- 1. Cash in Hand --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-money-bill-wave text-success"></i>
                <span>Cash in Hand (Counter &amp; Till)</span>
                <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#cashDetailModal">Details</span>
              </div>
              <div class="bs-row-value" id="rowCashInHand">Rs 0.00</div>
            </div>

            {{-- 2. Bank Accounts --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-university text-primary"></i>
                <span>Bank Accounts Balance</span>
                <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#bankDetailModal">View Banks</span>
              </div>
              <div class="bs-row-value" id="rowBankBalances">Rs 0.00</div>
            </div>

            {{-- 3. Accounts Receivable (Customers) --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-user-friends text-info"></i>
                <span>Accounts Receivable (Customer Udhaar)</span>
                <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#customersModal">
                  <span id="badgeCustCount">0</span> Customers
                </span>
              </div>
              <div class="bs-row-value" id="rowReceivables">Rs 0.00</div>
            </div>

            {{-- 4. Inventory / Stock --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-boxes text-warning"></i>
                <span>Closing Stock / Inventory Value</span>
                <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#stockModal">Stock Value</span>
              </div>
              <div class="bs-row-value" id="rowInventory">Rs 0.00</div>
            </div>

            {{-- 5. Advances to Vendors (if any) --}}
            <div class="bs-row-item" id="rowAdvanceVendorsContainer" style="display:none;">
              <div class="bs-row-label">
                <i class="fas fa-hand-holding text-secondary"></i>
                <span>Advances to Vendors (Peshgi Di Gayi)</span>
              </div>
              <div class="bs-row-value" id="rowAdvanceVendors">Rs 0.00</div>
            </div>

            <div class="bs-subtotal-row">
              <span>Subtotal Current Assets</span>
              <span id="subCurrentAssets">Rs 0.00</span>
            </div>
          </div>

          {{-- Section 2: Fixed Assets --}}
          <div class="bs-section-box">
            <div class="bs-section-title">
              <span>Fixed Assets (Mustaqil Asasay)</span>
              <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#assetsModal">Asset Register</span>
            </div>

            <div id="fixedAssetsContainer">
              {{-- Populated via AJAX --}}
            </div>

            <div class="bs-subtotal-row">
              <span>Subtotal Fixed Assets (Net Book Value)</span>
              <span id="subFixedAssets">Rs 0.00</span>
            </div>
          </div>
        </div>

        <div class="bs-column-footer assets-ftr">
          <div class="bs-grand-title">
            <i class="fas fa-check-double me-1"></i> TOTAL ASSETS
          </div>
          <div class="bs-grand-val" id="grandTotalAssets">Rs 0.00</div>
        </div>
      </div>

      {{-- ──────── RIGHT COLUMN: LIABILITIES & EQUITY ──────── --}}
      <div class="bs-column-card">
        <div class="bs-column-header liab-hdr">
          <div class="bs-column-title">
            <i class="fas fa-arrow-circle-up"></i> LIABILITIES &amp; CAPITAL
          </div>
          <span class="badge bg-danger" style="font-size:0.75rem;">Credits</span>
        </div>

        <div class="bs-column-body">
          {{-- Section 1: Liabilities --}}
          <div class="bs-section-box">
            <div class="bs-section-title">
              <span>Liabilities (Wajib-ul-Ada / Den-dariyan)</span>
              <span class="text-muted" style="font-size:0.7rem;">Obligations</span>
            </div>

            {{-- 1. Accounts Payable (Vendors) --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-truck text-danger"></i>
                <span>Accounts Payable (Vendor Udhaar)</span>
                <span class="bs-row-badge" data-bs-toggle="modal" data-bs-target="#vendorsModal">
                  <span id="badgeVendorCount">0</span> Vendors
                </span>
              </div>
              <div class="bs-row-value" id="rowPayables">Rs 0.00</div>
            </div>

            {{-- 2. Advances from Customers (if any) --}}
            <div class="bs-row-item" id="rowCustAdvancesContainer" style="display:none;">
              <div class="bs-row-label">
                <i class="fas fa-user-clock text-warning"></i>
                <span>Customer Advances Received</span>
              </div>
              <div class="bs-row-value" id="rowCustAdvances">Rs 0.00</div>
            </div>

            <div class="bs-subtotal-row">
              <span>Subtotal Total Liabilities</span>
              <span id="subTotalLiabilities">Rs 0.00</span>
            </div>
          </div>

          {{-- Section 2: Owner's Equity / Capital --}}
          <div class="bs-section-box">
            <div class="bs-section-title">
              <span>Owner's Equity (Maalik ka Sarmaya)</span>
              <span class="text-muted" style="font-size:0.7rem;">Capital &amp; Reserves</span>
            </div>

            {{-- 1. Invested Capital --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-landmark text-primary"></i>
                <span>Owner's Invested Capital</span>
              </div>
              <div class="bs-row-value" id="rowInvestedCapital">Rs 0.00</div>
            </div>

            {{-- 2. Retained Earnings --}}
            <div class="bs-row-item" id="rowRetainedContainer">
              <div class="bs-row-label">
                <i class="fas fa-history text-secondary"></i>
                <span>Retained Earnings / Reserves</span>
              </div>
              <div class="bs-row-value" id="rowRetainedEarnings">Rs 0.00</div>
            </div>

            {{-- 3. Net Profit / (Loss) from P&L --}}
            <div class="bs-row-item">
              <div class="bs-row-label">
                <i class="fas fa-chart-line text-success"></i>
                <span>Net Profit / (Loss) for Period</span>
                <a href="{{ route('report.profit_loss') }}" target="_blank" class="bs-row-badge text-decoration-none">
                  P&amp;L Report <i class="fas fa-external-link-alt" style="font-size:0.6rem;"></i>
                </a>
              </div>
              <div class="bs-row-value" id="rowNetProfit">Rs 0.00</div>
            </div>

            <div class="bs-subtotal-row">
              <span>Subtotal Owner's Equity</span>
              <span id="subTotalEquity">Rs 0.00</span>
            </div>
          </div>
        </div>

        <div class="bs-column-footer liab-ftr">
          <div class="bs-grand-title">
            <i class="fas fa-check-double me-1"></i> TOTAL LIABILITIES &amp; EQUITY
          </div>
          <div class="bs-grand-val" id="grandTotalLiabilitiesEquity">Rs 0.00</div>
        </div>
      </div>

    </div>

  </div>
</div>

{{-- ═══════ MODAL: CASH BREAKDOWN ═══════ --}}
<div class="modal fade" id="cashDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-money-bill-wave text-success me-2"></i> Cash in Hand Breakdown</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="list-group list-group-flush mb-3">
          <div class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span>Sales Counter / Till Net Cash</span>
            <strong id="modalCounterCash">Rs 0.00</strong>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span>Cash in Hand Accounts Opening</span>
            <strong id="modalAccountCashOpening">Rs 0.00</strong>
          </div>
        </div>
        <div class="p-3 bg-light rounded">
          <small class="text-muted d-block">
            <strong>Formula:</strong> (Cash Sales + Customer Cash Recoveries) - (Vendor Cash Payments + Cash Expenses) + Opening Cash.
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════ MODAL: BANK ACCOUNTS ═══════ --}}
<div class="modal fade" id="bankDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-university text-primary me-2"></i> Bank Accounts Detail</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Account Title</th>
                <th>Code</th>
                <th class="text-end">Opening</th>
                <th class="text-end">Inflow (Card/Bank)</th>
                <th class="text-end">Outflow</th>
                <th class="text-end">Current Balance</th>
              </tr>
            </thead>
            <tbody id="bankAccountsTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════ MODAL: CUSTOMER RECEIVABLES ═══════ --}}
<div class="modal fade" id="customersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-friends text-info me-2"></i> Customer Receivables (Sundry Debtors)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 bg-light border-bottom">
          <input type="text" id="custSearchInput" class="form-control form-control-sm" placeholder="Search customer name or phone...">
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="custTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th class="text-end">Receivable Balance</th>
              </tr>
            </thead>
            <tbody id="customersTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════ MODAL: VENDOR PAYABLES ═══════ --}}
<div class="modal fade" id="vendorsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-truck text-danger me-2"></i> Vendor Payables (Sundry Creditors)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 bg-light border-bottom">
          <input type="text" id="vendSearchInput" class="form-control form-control-sm" placeholder="Search vendor name or phone...">
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="vendTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Vendor Name</th>
                <th>Phone</th>
                <th class="text-end">Payable Balance</th>
              </tr>
            </thead>
            <tbody id="vendorsTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════ MODAL: INVENTORY VALUATION ═══════ --}}
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-boxes text-warning me-2"></i> Inventory &amp; Stock Valuation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
          <div>
            <span>Finished Goods: <strong id="modalFinishedStock">Rs 0</strong></span>
            <span class="mx-2">&bull;</span>
            <span>Raw Materials: <strong id="modalRawStock">Rs 0</strong></span>
          </div>
          <div>
            <span>Total Units: <strong id="modalTotalStockQty">0</strong></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th class="text-end">Quantity</th>
                <th class="text-end">Unit Cost</th>
                <th class="text-end">Total Valuation</th>
              </tr>
            </thead>
            <tbody id="stockTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════ MODAL: FIXED ASSETS ═══════ --}}
<div class="modal fade" id="assetsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-tools text-secondary me-2"></i> Fixed Assets Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th class="text-end">Purchase Cost</th>
                <th class="text-end">Current Book Value</th>
              </tr>
            </thead>
            <tbody id="assetsTableBody">
              {{-- Populated via AJAX --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Loading Spinner --}}
<div id="loadingOverlay">
  <div class="spinner-ring"></div>
  <div style="font-weight: 600; font-size: 0.95rem;">Calculating Financial Position...</div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {

  function formatMoney(num) {
    var val = parseFloat(num) || 0;
    return 'Rs ' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Active loaded data store
  var activeData = null;

  function loadBalanceSheet() {
    var asOfDate = $('#as_of_date').val();
    var branchId = $('#branch_id').val();

    $('#loadingOverlay').css('display', 'flex');

    $.ajax({
      url: "{{ route('report.balance_sheet.fetch') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        as_of_date: asOfDate,
        branch_id: branchId
      },
      dataType: "json",
      success: function(res) {
        $('#loadingOverlay').hide();
        if (res.success) {
          activeData = res;
          renderBalanceSheet(res);
        } else {
          alert('Failed to load balance sheet: ' + (res.message || 'Unknown error'));
        }
      },
      error: function(xhr) {
        $('#loadingOverlay').hide();
        console.error(xhr);
        alert('Server error while loading balance sheet data.');
      }
    });
  }

  function renderBalanceSheet(data) {
    var a = data.assets;
    var l = data.liabilities;
    var e = data.equity;
    var m = data.metrics;

    // 1. KPI Cards
    $('#kpiTotalAssets').text(formatMoney(a.total_assets));
    $('#kpiCurrentAssets').text(formatMoney(a.total_current_assets));
    $('#kpiFixedAssets').text(formatMoney(a.total_fixed_assets));

    $('#kpiTotalLiabilities').text(formatMoney(l.total_liabilities));
    $('#kpiTotalEquity').text(formatMoney(e.total_equity));
    $('#kpiInvestedCapital').text(formatMoney(e.invested_capital));
    $('#kpiNetProfit').text(formatMoney(e.net_profit));

    $('#kpiWorkingCapital').text(formatMoney(m.working_capital));

    if (m.is_balanced) {
      $('#kpiStatusBadge').html('<i class="fas fa-check-circle"></i> Balanced (Diff: ' + formatMoney(m.difference) + ')')
        .removeClass('bg-danger text-white').addClass('status-balanced');
    } else {
      $('#kpiStatusBadge').html('<i class="fas fa-exclamation-triangle"></i> Imbalance: ' + formatMoney(m.difference))
        .removeClass('status-balanced').addClass('bg-danger text-white');
    }

    // 2. Assets Column
    $('#rowCashInHand').text(formatMoney(a.cash_in_hand.total));
    $('#rowBankBalances').text(formatMoney(a.bank_accounts.total));
    $('#rowReceivables').text(formatMoney(a.accounts_receivable.total));
    $('#badgeCustCount').text(a.accounts_receivable.count);
    $('#rowInventory').text(formatMoney(a.inventory.total));

    if (a.advances_to_vendors && a.advances_to_vendors.total > 0) {
      $('#rowAdvanceVendorsContainer').show();
      $('#rowAdvanceVendors').text(formatMoney(a.advances_to_vendors.total));
    } else {
      $('#rowAdvanceVendorsContainer').hide();
    }

    $('#subCurrentAssets').text(formatMoney(a.total_current_assets));

    // Fixed assets list
    var fixedHtml = '';
    if (a.fixed_assets.assets && a.fixed_assets.assets.length > 0) {
      a.fixed_assets.assets.forEach(function(item) {
        fixedHtml += '<div class="bs-row-item">' +
          '<div class="bs-row-label">' +
            '<i class="fas fa-cube text-muted"></i>' +
            '<span>' + item.name + ' (' + item.code + ')</span>' +
          '</div>' +
          '<div class="bs-row-value">' + formatMoney(item.current_value) + '</div>' +
        '</div>';
      });
    } else {
      fixedHtml = '<div class="bs-row-item text-muted"><div class="bs-row-label">No active fixed assets</div><div class="bs-row-value">Rs 0.00</div></div>';
    }
    $('#fixedAssetsContainer').html(fixedHtml);
    $('#subFixedAssets').text(formatMoney(a.total_fixed_assets));
    $('#grandTotalAssets').text(formatMoney(a.total_assets));

    // 3. Liabilities & Capital Column
    $('#rowPayables').text(formatMoney(l.accounts_payable.total));
    $('#badgeVendorCount').text(l.accounts_payable.count);

    if (l.customer_advances && l.customer_advances.total > 0) {
      $('#rowCustAdvancesContainer').show();
      $('#rowCustAdvances').text(formatMoney(l.customer_advances.total));
    } else {
      $('#rowCustAdvancesContainer').hide();
    }

    $('#subTotalLiabilities').text(formatMoney(l.total_liabilities));

    $('#rowInvestedCapital').text(formatMoney(e.invested_capital));
    $('#rowRetainedEarnings').text(formatMoney(e.retained_earnings));
    $('#rowNetProfit').text(formatMoney(e.net_profit));

    if (e.net_profit < 0) {
      $('#rowNetProfit').css('color', '#ef4444');
    } else {
      $('#rowNetProfit').css('color', '#10b981');
    }

    $('#subTotalEquity').text(formatMoney(e.total_equity));
    $('#grandTotalLiabilitiesEquity').text(formatMoney(e.total_liabilities_and_equity));

    // Populate Modals
    populateModals(data);
  }

  function populateModals(data) {
    var a = data.assets;
    var l = data.liabilities;

    // Cash Modal
    $('#modalCounterCash').text(formatMoney(a.cash_in_hand.counter_cash));
    $('#modalAccountCashOpening').text(formatMoney(a.cash_in_hand.account_opening));

    // Bank Modal
    var bankHtml = '';
    if (a.bank_accounts.accounts && a.bank_accounts.accounts.length > 0) {
      a.bank_accounts.accounts.forEach(function(b) {
        bankHtml += '<tr>' +
          '<td><strong>' + b.title + '</strong><br><small class="text-muted">' + (b.bank_name || '-') + '</small></td>' +
          '<td><span class="badge bg-light text-dark">' + b.account_code + '</span></td>' +
          '<td class="text-end">' + formatMoney(b.opening_balance) + '</td>' +
          '<td class="text-end text-success">+' + formatMoney(b.inflow) + '</td>' +
          '<td class="text-end text-danger">-' + formatMoney(b.outflow) + '</td>' +
          '<td class="text-end"><strong>' + formatMoney(b.current_balance) + '</strong></td>' +
        '</tr>';
      });
    } else {
      bankHtml = '<tr><td colspan="6" class="text-center text-muted py-3">No bank accounts configured</td></tr>';
    }
    $('#bankAccountsTableBody').html(bankHtml);

    // Customers Modal
    var custHtml = '';
    if (a.accounts_receivable.customers && a.accounts_receivable.customers.length > 0) {
      a.accounts_receivable.customers.forEach(function(c, i) {
        custHtml += '<tr>' +
          '<td>' + (i + 1) + '</td>' +
          '<td><strong>' + c.name + '</strong></td>' +
          '<td>' + c.phone + '</td>' +
          '<td class="text-end font-monospace text-primary"><strong>' + formatMoney(c.balance) + '</strong></td>' +
        '</tr>';
      });
    } else {
      custHtml = '<tr><td colspan="4" class="text-center text-muted py-3">No customer receivables</td></tr>';
    }
    $('#customersTableBody').html(custHtml);

    // Vendors Modal
    var vendHtml = '';
    if (l.accounts_payable.vendors && l.accounts_payable.vendors.length > 0) {
      l.accounts_payable.vendors.forEach(function(v, i) {
        vendHtml += '<tr>' +
          '<td>' + (i + 1) + '</td>' +
          '<td><strong>' + v.name + '</strong></td>' +
          '<td>' + v.phone + '</td>' +
          '<td class="text-end font-monospace text-danger"><strong>' + formatMoney(v.balance) + '</strong></td>' +
        '</tr>';
      });
    } else {
      vendHtml = '<tr><td colspan="4" class="text-center text-muted py-3">No vendor payables</td></tr>';
    }
    $('#vendorsTableBody').html(vendHtml);

    // Stock Modal
    $('#modalFinishedStock').text(formatMoney(a.inventory.finished_goods));
    $('#modalRawStock').text(formatMoney(a.inventory.raw_materials));
    $('#modalTotalStockQty').text(a.inventory.total_qty);

    var stockHtml = '';
    if (a.inventory.items && a.inventory.items.length > 0) {
      a.inventory.items.forEach(function(it) {
        stockHtml += '<tr>' +
          '<td><strong>' + it.name + '</strong><br><small class="text-muted">' + it.code + '</small></td>' +
          '<td><span class="badge bg-light text-dark">' + it.category + '</span></td>' +
          '<td class="text-end">' + it.qty + '</td>' +
          '<td class="text-end">' + formatMoney(it.unit_cost) + '</td>' +
          '<td class="text-end"><strong>' + formatMoney(it.total_val) + '</strong></td>' +
        '</tr>';
      });
    } else {
      stockHtml = '<tr><td colspan="5" class="text-center text-muted py-3">No inventory items in stock</td></tr>';
    }
    $('#stockTableBody').html(stockHtml);

    // Assets Modal
    var astHtml = '';
    if (a.fixed_assets.assets && a.fixed_assets.assets.length > 0) {
      a.fixed_assets.assets.forEach(function(ast) {
        astHtml += '<tr>' +
          '<td><span class="badge bg-light text-dark">' + ast.code + '</span></td>' +
          '<td><strong>' + ast.name + '</strong></td>' +
          '<td>' + ast.category + '</td>' +
          '<td class="text-end">' + formatMoney(ast.purchase_cost) + '</td>' +
          '<td class="text-end text-success"><strong>' + formatMoney(ast.current_value) + '</strong></td>' +
        '</tr>';
      });
    } else {
      astHtml = '<tr><td colspan="5" class="text-center text-muted py-3">No fixed assets registered</td></tr>';
    }
    $('#assetsTableBody').html(astHtml);
  }

  // Filter submit
  $('#balanceSheetFilterForm').on('submit', function(e) {
    e.preventDefault();
    loadBalanceSheet();
  });

  $('#btnRefresh').on('click', function() {
    loadBalanceSheet();
  });

  // Print button
  $('#btnPrint').on('click', function() {
    var date = $('#as_of_date').val();
    var branch = $('#branch_id').val();
    var url = "{{ route('report.balance_sheet.print') }}?as_of_date=" + encodeURIComponent(date) + "&branch_id=" + encodeURIComponent(branch);
    window.open(url, '_blank');
  });

  // Export Excel
  $('#btnExportExcel').on('click', function() {
    if (!activeData) return;
    var d = activeData;
    var table = '<table>' +
      '<tr><th colspan="4" style="font-size:16px;text-align:center;"><b>BALANCE SHEET</b></th></tr>' +
      '<tr><th colspan="4" style="text-align:center;">As on: ' + d.as_of_date + ' | Branch: ' + d.branch_name + '</th></tr>' +
      '<tr><th>ASSETS</th><th>Amount (PKR)</th><th>LIABILITIES & CAPITAL</th><th>Amount (PKR)</th></tr>' +
      '<tr><td><b>Current Assets:</b></td><td></td><td><b>Current Liabilities:</b></td><td></td></tr>' +
      '<tr><td>Cash in Hand</td><td>' + d.assets.cash_in_hand.total + '</td><td>Accounts Payable (Vendors)</td><td>' + d.liabilities.accounts_payable.total + '</td></tr>' +
      '<tr><td>Bank Balances</td><td>' + d.assets.bank_accounts.total + '</td><td>Customer Advances</td><td>' + d.liabilities.customer_advances.total + '</td></tr>' +
      '<tr><td>Accounts Receivable (Customers)</td><td>' + d.assets.accounts_receivable.total + '</td><td><b>Owner\'s Equity:</b></td><td></td></tr>' +
      '<tr><td>Closing Inventory / Stock</td><td>' + d.assets.inventory.total + '</td><td>Invested Capital</td><td>' + d.equity.invested_capital + '</td></tr>' +
      '<tr><td>Advances to Vendors</td><td>' + d.assets.advances_to_vendors.total + '</td><td>Retained Reserves</td><td>' + d.equity.retained_earnings + '</td></tr>' +
      '<tr><td><b>Fixed Assets:</b></td><td>' + d.assets.total_fixed_assets + '</td><td>Net Profit / (Loss)</td><td>' + d.equity.net_profit + '</td></tr>' +
      '<tr><td><b>TOTAL ASSETS</b></td><td><b>' + d.assets.total_assets + '</b></td><td><b>TOTAL LIABILITIES & EQUITY</b></td><td><b>' + d.equity.total_liabilities_and_equity + '</b></td></tr>' +
      '</table>';

    var blob = new Blob([table], { type: 'application/vnd.ms-excel' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Balance_Sheet_' + d.as_of_date + '.xls';
    a.click();
  });

  // Client-side quick search inside modals
  $('#custSearchInput').on('keyup', function() {
    var val = $(this).val().toLowerCase();
    $('#customersTableBody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
    });
  });

  $('#vendSearchInput').on('keyup', function() {
    var val = $(this).val().toLowerCase();
    $('#vendorsTableBody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
    });
  });

  // Auto trigger on initial load
  loadBalanceSheet();
});
</script>
@endsection
