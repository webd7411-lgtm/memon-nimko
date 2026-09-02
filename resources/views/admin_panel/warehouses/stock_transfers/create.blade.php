@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --st-green: #0e8349;
  --st-green-hover: #0b6b3b;
  --st-bg: #f8fafc;
  --st-card-bg: #ffffff;
  --st-border: #e2e8f0;
  --st-text-dark: #0f172a;
  --st-text-muted: #64748b;
  --st-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.st-container * {
  font-family: var(--st-font);
  box-sizing: border-box;
}

.st-container {
  background-color: var(--st-bg);
  min-height: 100vh;
  padding: 1.25rem;
  overflow-x: hidden !important;
}

/* ══════════ TOP HEADER BAR ══════════ */
.st-header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid var(--st-border);
  border-radius: 8px;
  padding: 0.85rem 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.st-header-title {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.st-header-title h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--st-text-dark);
  margin: 0;
}

.st-header-title .st-icon {
  font-size: 1.4rem;
  color: var(--st-green);
  line-height: 1;
}

.st-badge-pill {
  background: #e0e7ff;
  color: #4338ca;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 12px;
  padding: 3px 10px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: inline-block;
}

.st-btn-back {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #475569;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.45rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: all 0.2s ease;
}

.st-btn-back:hover {
  background: #f1f5f9;
  color: #0f172a;
}

/* ══════════ CARDS ══════════ */
.st-card {
  background: var(--st-card-bg);
  border: 1px solid var(--st-border);
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

.st-card-header {
  font-size: 0.98rem;
  font-weight: 700;
  color: var(--st-text-dark);
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
  margin-bottom: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.st-card-header i {
  color: var(--st-green);
  font-size: 1.15rem;
  margin-right: 0.4rem;
}

.st-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.4rem;
  display: block;
}

.st-input, .st-select {
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 0.55rem 0.75rem;
  font-size: 0.88rem;
  color: #0f172a;
  background-color: #ffffff;
  width: 100%;
  transition: border-color 0.2s;
}

.st-input:focus, .st-select:focus {
  border-color: var(--st-green);
  outline: none;
  box-shadow: 0 0 0 3px rgba(14, 131, 73, 0.12);
}

.st-input[readonly] {
  background-color: #f1f5f9;
  color: #475569;
  font-weight: 600;
}

/* ══════════ RADIO SWITCH ══════════ */
.st-radio-group {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding-top: 0.2rem;
}

.st-radio-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  color: #334155;
}

.st-radio-item input[type="radio"] {
  accent-color: var(--st-green);
  width: 16px;
  height: 16px;
  cursor: pointer;
}

/* ══════════ SECTION 3 TABLE STYLING (EXACT SCREENSHOT MATCH) ══════════ */
.st-table-section3 {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #e2e8f0;
}

.st-table-section3 th {
  background-color: #ebf5f0 !important;
  color: #1e293b;
  font-size: 0.82rem;
  font-weight: 700;
  padding: 0.75rem 0.85rem;
  border-bottom: 1px solid #cbd5e1;
  border-right: 1px solid #e2e8f0;
  text-align: center;
}

.st-table-section3 th.text-start {
  text-align: left;
}

.st-table-section3 td {
  padding: 0.65rem 0.85rem;
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  font-size: 0.88rem;
  color: #0f172a;
}

.st-table-section3 td:last-child, .st-table-section3 th:last-child {
  border-right: none;
}

.st-table-section3 tbody tr:hover {
  background-color: #f8fafc;
}

/* ══════════ SUMMARY TOTALS CARDS ══════════ */
.st-summary-container {
  background: #ffffff;
  padding-top: 1.1rem;
}

.st-summary-label {
  font-size: 0.82rem;
  font-weight: 800;
  color: #334155;
  margin-bottom: 0.6rem;
}

.st-summary-grid {
  display: flex;
  gap: 0.85rem;
  flex-wrap: wrap;
}

.st-summary-box {
  flex: 1;
  min-width: 130px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 0.5rem;
  text-align: center;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

.st-summary-box .lbl {
  font-size: 0.75rem;
  font-weight: 700;
  color: #475569;
  margin-bottom: 0.25rem;
}

.st-summary-box .val {
  font-size: 1.25rem;
  font-weight: 800;
  color: #2563eb;
}

.st-summary-box .val-green {
  color: var(--st-green) !important;
}

.st-summary-box .val-muted {
  color: #94a3b8 !important;
}

/* Modal Search item */
.modal-product-item {
  border-left: 4px solid transparent;
  padding: 0.75rem 1rem;
  cursor: pointer;
}
.modal-product-item:hover, .modal-product-item.active {
  background-color: #f0fdf4 !important;
  border-left-color: var(--st-green);
}

/* ══════════ MOBILE RESPONSIVE OPTIMIZATION (NO HORIZONTAL SCROLL) ══════════ */
.mobile-lbl { display: none; }

@media (max-width: 767.98px) {
  .st-container {
    padding: 0.6rem 0.4rem !important;
    width: 100% !important;
    overflow-x: hidden !important;
  }

  .st-header-bar {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 0.75rem !important;
    padding: 0.85rem 1rem !important;
  }

  .st-header-title h2 {
    font-size: 1.15rem !important;
  }

  .st-btn-back {
    width: 100% !important;
    justify-content: center !important;
  }

  .st-card {
    padding: 0.9rem 0.75rem !important;
    margin-bottom: 0.85rem !important;
    border-radius: 8px !important;
  }

  .st-table-section3, .st-table-section3 tbody, .st-table-section3 tr, .st-table-section3 td {
    display: block !important;
    width: 100% !important;
  }

  .st-table-section3 thead {
    display: none !important;
  }

  .st-table-section3 tbody tr.product_row {
    background: #ffffff !important;
    border: 1px solid var(--st-border) !important;
    border-radius: 10px !important;
    padding: 0.85rem !important;
    margin-bottom: 0.85rem !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    position: relative !important;
  }

  .st-table-section3 td {
    padding: 0.3rem 0 !important;
    border: none !important;
    text-align: left !important;
  }

  .st-table-section3 td.td-number {
    position: absolute !important;
    top: 0.75rem !important;
    left: 0.85rem !important;
    width: auto !important;
    font-size: 0.9rem !important;
  }

  .st-table-section3 td.td-product {
    padding-left: 2rem !important;
    padding-right: 4.5rem !important;
    margin-bottom: 0.5rem !important;
  }

  .st-table-section3 td.td-action {
    position: absolute !important;
    top: 0.65rem !important;
    right: 0.85rem !important;
    width: auto !important;
  }

  .mobile-lbl {
    display: block !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    color: #64748b !important;
    margin-bottom: 0.2rem !important;
    text-transform: uppercase !important;
  }

  .st-table-section3 td.td-item,
  .st-table-section3 td.td-unit,
  .st-table-section3 td.td-price,
  .st-table-section3 td.td-stock {
    display: inline-block !important;
    width: 48% !important;
    margin-bottom: 0.4rem !important;
    vertical-align: top !important;
  }

  .st-table-section3 td.td-qty {
    display: block !important;
    width: 100% !important;
    margin-top: 0.4rem !important;
  }

  .st-summary-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.5rem !important;
  }

  .st-summary-box {
    min-width: 0 !important;
    padding: 0.6rem 0.5rem !important;
  }
}
</style>

<div class="st-container">

  {{-- ══════════ TOP HEADER BAR ══════════ --}}
  <div class="st-header-bar">
    <div class="st-header-title">
      <i class="bi bi-arrow-left-right st-icon"></i>
      <h2>Create Stock Transfer</h2>
      <span class="st-badge-pill">New Dispatch Entry</span>
    </div>

    <a href="{{ url('/stock_transfers') }}" class="st-btn-back">
      <i class="bi bi-arrow-left"></i> Back to Ledger
    </a>
  </div>

  <form action="{{ route('stock_transfers.store') }}" method="POST" id="stockTransferForm" novalidate>
    @csrf

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-3" role="alert">
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ══════════ ROW 1: SOURCE & DESTINATION CARDS ══════════ --}}
    <div class="row g-3">
      <!-- 1. Source Location -->
      <div class="col-12 col-md-6">
        <div class="st-card h-100 mb-0">
          <div class="st-card-header">
            <div><i class="bi bi-shop"></i> 1. Source Location (Dispatch From)</div>
          </div>

          <div class="row g-3">
            <div class="col-12 col-sm-6">
              <label class="st-label">Transfer Date <span class="text-danger">*</span></label>
              <input type="date" name="transfer_date" class="st-input" value="{{ date('Y-m-d') }}">
            </div>

            <div class="col-12 col-sm-6">
              <label class="st-label">Current Branch Stock</label>
              <input type="text" class="st-input" value="{{ auth()->user()->branch->name ?? 'Main Store' }}" readonly>
            </div>

            <div class="col-12">
              <label class="st-label">Source Location <span class="text-danger">*</span></label>
              <select name="from_warehouse_id" class="st-select fromLocationSelect" required>
                <option value="Shop" selected>Main Store</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Destination Location -->
      <div class="col-12 col-md-6">
        <div class="st-card h-100 mb-0">
          <div class="st-card-header">
            <div><i class="bi bi-geo-alt-fill"></i> 2. Destination Location (Receive To)</div>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="st-label">Select Target Type <span class="text-danger">*</span></label>
              <div class="st-radio-group">
                <label class="st-radio-item">
                  <input type="radio" class="transferType" name="transfer_to" value="branch" id="toBranch" checked>
                  <span>Branch</span>
                </label>

                <label class="st-radio-item">
                  <input type="radio" class="transferType" name="transfer_to" value="warehouse" id="toWarehouse">
                  <span>Warehouse</span>
                </label>
              </div>
            </div>

            <!-- TO BRANCH SELECT -->
            <div class="col-12" id="toBranchBox">
              <label class="st-label">Target Branch <span class="text-danger">*</span></label>
              <select name="to_branch_id" class="st-select select2">
                <option value="">-- Select Destination Branch --</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- TO WAREHOUSE SELECT -->
            <div class="col-12 d-none" id="toWarehouseBox">
              <label class="st-label">Target Warehouse <span class="text-danger">*</span></label>
              <select name="to_warehouse_id" class="st-select select2">
                <option value="">-- Select Destination Warehouse --</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══════════ SECTION 3: PRODUCTS TO TRANSFER ══════════ --}}
    <div class="st-card mt-3">
      <div class="st-card-header border-0 pb-0 mb-3">
        <div><i class="bi bi-box-seam-fill"></i> 3. Products to Transfer</div>
      </div>

      <!-- Top Toolbar: Search Bar + Add Product Button -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="input-group" style="max-width: 320px; flex: 1 1 auto;">
          <input type="text" class="form-control form-control-sm st-input border-end-0" id="triggerSearchInput" placeholder="Search Product" readonly style="cursor:pointer; background:#ffffff;">
          <span class="input-group-text bg-white border-start-0 border-end-0 text-muted small fw-bold px-2" style="font-size: 0.75rem; color: #64748b;">F2</span>
          <button type="button" class="btn text-white px-3" id="openProductModalBtn" style="background-color: #0e8349; border-radius: 0 6px 6px 0;">
            <i class="bi bi-search"></i>
          </button>
        </div>

        <button type="button" class="btn text-white fw-bold px-3 py-2" id="btnAddProductRow" style="background-color: #0e8349; border-radius: 6px; font-size: 0.85rem;">
          <i class="bi bi-plus-lg me-1"></i> Add Product
        </button>
      </div>

      <!-- Table Container -->
      <div class="table-responsive">
        <table class="st-table-section3" id="product_table">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th class="text-start" style="min-width: 220px;">Product</th>
              <th style="min-width: 120px;">Item</th>
              <th style="min-width: 90px;">Unit</th>
              <th style="min-width: 120px;">Retail Price</th>
              <th style="min-width: 130px;">Available Stock</th>
              <th style="min-width: 140px;">Transfer Qty</th>
              <th style="width: 90px;">Action</th>
            </tr>
          </thead>
          <tbody id="product_body">
            <!-- Dynamic Rows Added by JS -->
          </tbody>
        </table>
      </div>

      <!-- Summary Totals Row -->
      <div class="st-summary-container">
        <div class="st-summary-label">Summary Totals by UOM:</div>
        <div class="st-summary-grid" id="summaryCardsContainer">
          <!-- Dynamic UOM Summary Cards Added by JS -->
        </div>
      </div>
    </div>

    {{-- ══════════ SECTION 4: REMARKS & ACTION BUTTONS ══════════ --}}
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <div class="st-card mb-0 h-100">
          <div class="st-card-header">
            <div><i class="bi bi-pencil-square"></i> 4. Transfer Remarks & Notes</div>
          </div>
          <label class="st-label text-muted fw-bold">Remarks / Notes</label>
          <textarea name="remarks" id="remarksTextarea" rows="3" class="st-input w-100" maxlength="500" placeholder="Enter any additional transfer notes..."></textarea>
          <div class="text-start text-muted small mt-1" id="charCounter" style="font-size: 0.75rem;">0/500</div>
        </div>
      </div>

      <div class="col-12 col-md-6 d-flex align-items-end justify-content-end">
        <div class="d-flex flex-wrap align-items-center gap-3 w-100 justify-content-end pt-3">
          <a href="{{ url('/stock_transfers') }}" class="btn btn-outline-secondary fw-bold px-4 py-2" style="border-radius: 6px; font-size: 0.9rem; background:#ffffff; color:#1e293b; border-color:#cbd5e1;">
            <i class="bi bi-x-lg me-1"></i> Cancel / Back to Ledger
          </a>

          <button type="submit" class="btn text-white fw-bold px-4 py-2" style="background-color: #0e8349; border-radius: 6px; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(14, 131, 73, 0.2);">
            <i class="bi bi-send-fill me-1"></i> Confirm & Transfer Stock
          </button>
        </div>
      </div>
    </div>

  </form>

</div>

{{-- ══════════ PRODUCT SEARCH MODAL ══════════ --}}
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">

      <div class="modal-header text-white" style="background-color: #0e8349;">
        <h5 class="modal-title fw-bold"><i class="bi bi-search me-2"></i> Fast Product Finder</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
      </div>

      <div class="modal-body p-3" style="background-color: #f8fafc;">
        <div class="input-group input-group-lg mb-3 shadow-sm" style="border-radius: 8px; overflow: hidden;">
          <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
          <input type="text" id="modalProductSearch" class="form-control border-start-0 ps-2" placeholder="Type product name, item code, or scan barcode..." autofocus style="font-size: 0.95rem;">
        </div>

        <ul class="list-group shadow-sm" id="modalSearchResults" style="max-height:360px; overflow-y:auto; border-radius: 8px;"></ul>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    let rowCounter = 0;
    let allProducts = [];

    // Prevent double submission and remove empty rows
    $('#stockTransferForm').on('submit', function(e) {
        $('#product_body tr').each(function() {
            var productId = $(this).find('.product_id').val();
            if (!productId || productId.trim() === '') {
                $(this).remove();
            }
        });

        if ($('#product_body tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one product before submitting.');
            addNewRow();
            return false;
        }

        const $btn = $(this).find('button[type="submit"]');
        if ($btn.hasClass('disabled')) return false; 
        $btn.addClass('disabled').html('<i class="bi bi-arrow-repeat me-2"></i> Transferring...');
    });

    function addNewRow(data = null) {
        rowCounter++;

        let pid = data ? (data.id || '') : '';
        let vid = data ? (data.variant_id || '') : '';
        let name = data ? (data.name || '') : '';
        let code = data ? (data.code || '') : '';
        let unit = data ? (data.unit || '') : '';
        let price = data ? (data.price || '') : '';
        let stock = data ? (data.stock || '0.000') : '';
        let qty = data ? (data.qty || '') : '';

        let priceText = price ? ('Rs. ' + parseFloat(price).toFixed(2)) : '-';
        let stockText = stock ? parseFloat(stock).toFixed(3) : '-';

        const row = `
<tr class="product_row">
    <td class="td-number text-center align-middle"><span class="row-index fw-bold text-muted">${rowCounter}</span></td>
    <td class="td-product text-center align-middle">
        <input type="hidden" name="product_id[]" class="product_id" value="${pid}">
        <input type="hidden" name="variant_id[]" class="variant_id" value="${vid}">
        <span class="product-name-text fw-bold text-dark ${!pid ? 'text-muted fst-italic' : ''}">${name || "Click 'Search Product' or press F2..."}</span>
    </td>
    <td class="td-item text-center align-middle">
        <span class="mobile-lbl">Item Code</span>
        <span class="item-code-text text-dark">${code || '-'}</span>
    </td>
    <td class="td-unit text-center align-middle">
        <span class="mobile-lbl">Unit</span>
        <span class="unit-text text-dark fw-semibold">${unit || '-'}</span>
    </td>
    <td class="td-price text-center align-middle">
        <span class="mobile-lbl">Price</span>
        <span class="price-text text-dark">${priceText}</span>
    </td>
    <td class="td-stock text-center align-middle">
        <span class="mobile-lbl">Available</span>
        <span class="stock-text fw-bold text-dark">${stockText}</span>
    </td>
    <td class="td-qty text-center align-middle">
        <span class="mobile-lbl text-dark fw-bold">Transfer Qty *</span>
        <input type="number" name="quantity[]" class="form-control form-control-sm quantity text-center fw-bold mx-auto" value="${qty}" placeholder="0.000" step="any" required inputmode="numeric" style="max-width: 130px; border-radius: 6px; border: 1px solid #cbd5e1;">
    </td>
    <td class="td-action text-center align-middle">
        <div class="d-flex align-items-center justify-content-center gap-1">
            <button type="button" class="btn btn-success btn-sm add-row-btn shadow-sm" style="background-color: #0e8349; border: none; border-radius: 6px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;" title="Add new product row">
                <i class="bi bi-plus-lg text-white"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm remove-row shadow-sm" style="background-color: #ef4444; border: none; border-radius: 6px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;" title="Delete row">
                <i class="bi bi-trash-fill text-white"></i>
            </button>
        </div>
    </td>
</tr>`;

        $('#product_body').append(row);
        updateRowNumbers();
        calculateSummaryTotals();
    }

    function updateRowNumbers() {
        $('#product_body tr').each(function(idx) {
            $(this).find('.row-index').text(idx + 1);
        });
    }

    function calculateSummaryTotals() {
        let totalItems = 0;
        let totalQty = 0;
        let unitTotals = {};

        $('#product_body tr').each(function() {
            let pid = $(this).find('.product_id').val();
            let qtyVal = $(this).find('.quantity').val();
            let unitVal = $(this).find('.unit-text').text();

            if (pid && pid.trim() !== '') {
                totalItems++;
            }

            if (qtyVal && !isNaN(parseFloat(qtyVal))) {
                let q = parseFloat(qtyVal);
                totalQty += q;

                let u = (unitVal && unitVal.trim() !== '' && unitVal.trim() !== '-') ? unitVal.trim().toUpperCase() : 'UNIT';
                unitTotals[u] = (unitTotals[u] || 0) + q;
            }
        });

        let html = `
            <div class="st-summary-box">
                <div class="lbl">Total Items</div>
                <div class="val">${totalItems}</div>
            </div>
            <div class="st-summary-box">
                <div class="lbl">Total Quantity</div>
                <div class="val val-green">${totalQty.toFixed(3)}</div>
            </div>
        `;

        if (Object.keys(unitTotals).length === 0) {
            html += `
                <div class="st-summary-box">
                    <div class="lbl">No quantities entered</div>
                    <div class="val val-muted">-</div>
                </div>
            `;
        } else {
            Object.keys(unitTotals).forEach(u => {
                html += `
                    <div class="st-summary-box">
                        <div class="lbl">${u}</div>
                        <div class="val">${unitTotals[u].toFixed(3)}</div>
                    </div>
                `;
            });
            html += `
                <div class="st-summary-box">
                    <div class="lbl">No quantities entered</div>
                    <div class="val val-muted">-</div>
                </div>
            `;
        }

        $('#summaryCardsContainer').html(html);
    }

    function refreshStockForAllRows() {
        var fromWarehouse = $('.fromLocationSelect').val() || 'Shop';

        $('#product_body tr').each(function() {
            var $row = $(this);
            var productId = $row.find('.product_id').val();
            var variantId = $row.find('.variant_id').val();
            if (productId) {
                $.get('/warehouse-stock-quantity', {
                    warehouse_id: fromWarehouse,
                    product_id: productId,
                    variant_id: variantId
                }, function(response) {
                    let stockQty = parseFloat(response.quantity ?? 0);
                    $row.find('.stock-text').text(stockQty.toFixed(3));
                    if (response.unit) {
                        $row.find('.unit-text').text(response.unit);
                    }
                    calculateSummaryTotals();
                });
            }
        });
    }

    $(document).ready(function() {
        if ($('#product_body tr').length === 0) {
            addNewRow();
        }

        loadProducts();

        $('.transferType').on('change', function() {
            let type = $(this).val();

            $('#toBranchBox').addClass('d-none');
            $('#toWarehouseBox').addClass('d-none');

            $('select[name="to_branch_id"]').val('').trigger('change');
            $('select[name="to_warehouse_id"]').val('').trigger('change');

            if (type === 'branch') {
                $('#toBranchBox').removeClass('d-none');
            } else if (type === 'warehouse') {
                $('#toWarehouseBox').removeClass('d-none');
            }
        });

        $(document).on('change', '.fromLocationSelect', function() {
            refreshStockForAllRows();
        });

        $(document).on('input', '.quantity', function() {
            calculateSummaryTotals();
        });

        $(document).on('click', '.remove-row', function() {
            var $row = $(this).closest('tr');
            $row.remove();
            updateRowNumbers();
            calculateSummaryTotals();

            if ($('#product_body tr').length === 0) {
                addNewRow();
            }
        });

        $(document).on('click', '.add-row-btn', function() {
            openProductModal();
        });

        $('#btnAddProductRow').on('click', function() {
            openProductModal();
        });

        $('#triggerSearchInput, #openProductModalBtn').on('click', function() {
            openProductModal();
        });

        $(document).on('click', '.productSearch, .product-name-text', function() {
            openProductModal();
        });

        $('#remarksTextarea').on('input', function() {
            let len = $(this).val().length;
            $('#charCounter').text(len + '/500');
        });
    });

    // Global Keydown Handler: Enter Key opens Search Product Modal
    $(document).on('keydown', function(e) {
        if (e.key === 'Enter') {
            // If modal is open, let modal navigation/selection handle Enter
            if ($('#productModal').hasClass('show')) {
                return;
            }

            // If user is typing in remarks textarea, allow normal newline
            if ($(e.target).is('#remarksTextarea')) {
                return;
            }

            // Otherwise (e.g. quantity input or anywhere on page), open modal
            e.preventDefault();
            openProductModal();
        }
    });

    // Barcode Scanning logic
    let scanBuffer = '';
    let scanTimer = null;
    let lastBarcode = null;
    let lastScanTime = 0;
    const SCAN_DELAY = 500;

    $(document).on('keydown', function(e) {
        if ($(e.target).is('textarea, input:not(.quantity)')) return;
        if (e.key === 'Enter') {
            e.preventDefault();
            if (scanBuffer.length >= 5) {
                handleTransferBarcode(scanBuffer.trim());
            }
            scanBuffer = '';
            return;
        }

        if (e.key.length === 1) {
            scanBuffer += e.key;
        }

        clearTimeout(scanTimer);
        scanTimer = setTimeout(() => {
            scanBuffer = '';
        }, 200);
    });

    function handleTransferBarcode(barcode) {
        const now = Date.now();
        if (barcode === lastBarcode && (now - lastScanTime) < SCAN_DELAY) {
            return;
        }

        lastBarcode = barcode;
        lastScanTime = now;

        let product = allProducts.find(p => p.barcode === barcode || p.item_code === barcode);

        if (product) {
            let res = {
                id: product.id,
                variant_id: product.variant_id,
                name: product.item_name,
                code: product.item_code,
                unit: product.unit_id || product.unit || '',
                price: product.price,
                note: product.note
            };
            processTransferBarcodeResult(res, barcode);
        } else {
            $.get("{{ route('search-product-by-barcode') }}", { barcode }, function(res) {
                if (res && res.id) {
                    processTransferBarcodeResult(res, barcode);
                } else {
                    alert('Product not found!');
                }
            });
        }
    }

    function processTransferBarcodeResult(res, barcode) {
        let foundRow = null;

        $('#product_body tr').each(function() {
            const pid = $(this).find('.product_id').val();
            const vid = $(this).find('.variant_id').val();
            if (pid && parseInt(pid) === parseInt(res.id) && vid == res.variant_id) {
                foundRow = $(this);
                return false;
            }
        });

        if (foundRow) {
            const qtyInput = foundRow.find('.quantity');
            qtyInput.val((parseFloat(qtyInput.val()) || 0) + 1).trigger('input');
            foundRow.addClass('table-success');
            setTimeout(() => foundRow.removeClass('table-success'), 1000);
            return;
        }

        let row = $('#product_body tr:last');
        if (row.find('.product_id').val()) {
            addNewRow();
            row = $('#product_body tr:last');
        }

        row.find('.product_id').val(res.id).data('barcode', barcode);
        row.find('.variant_id').val(res.variant_id || '');
        row.find('.product-name-text').removeClass('text-muted fst-italic').text(res.name);
        row.find('.item-code-text').text(res.code || '-');
        row.find('.unit-text').text(res.unit || '-');
        row.find('.price-text').text(res.price ? ('Rs. ' + parseFloat(res.price).toFixed(2)) : 'Rs. 0.00');
        row.find('.quantity').val(1).trigger('input');

        refreshStockForAllRows();
    }

    // Modal Search Functions
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            e.stopPropagation();
            openProductModal();
            return false;
        }
    });

    function openProductModal() {
        $('#modalProductSearch').val('');
        $('#modalSearchResults').empty();
        if (typeof $('#productModal').modal === 'function') {
            $('#productModal').modal('show');
        } else {
            $('#productModal').addClass('show').show();
        }

        $('#productModal').one('shown.bs.modal', function() {
            $('#modalProductSearch').focus();
            activeIndex = 0;
        });
    }

    function loadProducts() {
        $.get("{{ route('get-all-products-for-search') }}", function(res) {
            allProducts = res;
        });
    }

    $('#modalProductSearch').on('input', function() {
        let q = $(this).val().trim().toLowerCase();

        if (q.length < 1) {
            $('#modalSearchResults').empty();
            return;
        }

        let filtered = allProducts.filter(p => {
            const name = (p.item_name || "").toLowerCase();
            const code = (p.item_code || "").toLowerCase();
            const barcode = (p.barcode || "").toLowerCase();
            const brand = (p.brand || "").toLowerCase();
            return name.includes(q) || code.includes(q) || barcode.includes(q) || brand.includes(q);
        });

        let results = filtered.slice(0, 50);

        let html = '';
        results.forEach(p => {
            let unitLabel = p.unit_id || p.unit || '-';
            let priceLabel = p.price ? ('Rs. ' + parseFloat(p.price).toFixed(2)) : 'Rs. 0.00';

            html += `
                <li class="list-group-item modal-product-item"
                    data-id="${p.id}"
                    data-variant_id="${p.variant_id || ''}"
                    data-name="${p.item_name}"
                    data-code="${p.item_code || ''}"
                    data-barcode="${p.barcode || ''}"
                    data-price="${priceLabel}"
                    data-rawprice="${p.price || 0}"
                    data-unit="${unitLabel}">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-dark fs-6">${p.item_name}</strong>
                        <span class="product-price fw-extrabold text-success fs-6">${priceLabel}</span>
                    </div>
                    <div class="small text-muted mt-1">
                        Code: <strong>${p.item_code || '-'}</strong> | Unit: <span>${unitLabel}</span>
                    </div>
                </li>`;
        });

        $('#modalSearchResults').html(html);
        activeIndex = -1;
        setActiveItem(0);
    });

    $(document).on('mouseenter', '.modal-product-item', function() {
        const index = $(this).index();
        setActiveItem(index);
    });

    $('#modalProductSearch').on('keydown', function(e) {
        const items = $('#modalSearchResults .modal-product-item');
        if (!items.length) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                setActiveItem(activeIndex + 1);
                break;
            case 'ArrowUp':
                e.preventDefault();
                setActiveItem(activeIndex - 1);
                break;
            case 'Enter':
                e.preventDefault();
                if (activeIndex >= 0) {
                    items.eq(activeIndex).trigger('click');
                }
                break;
        }
    });

    $(document).on('click', '.modal-product-item', function() {
        const p = $(this);

        let $row = $('#product_body tr').filter(function() {
            return !$(this).find('.product_id').val();
        }).first();

        if (!$row.length) {
            addNewRow();
            $row = $('#product_body tr:last');
        }

        $row.find('.product_id').val(p.data('id'));
        $row.find('.variant_id').val(p.data('variant_id') || '');
        $row.find('.product-name-text').removeClass('text-muted fst-italic').text(p.data('name'));
        $row.find('.item-code-text').text(p.data('code') || '-');
        $row.find('.unit-text').text(p.data('unit') || '-');
        $row.find('.price-text').text(p.data('price') || 'Rs. 0.00');
        $row.find('.quantity').val(1).trigger('input');

        refreshStockForAllRows();

        if (typeof $('#productModal').modal === 'function') {
            $('#productModal').modal('hide');
        } else {
            $('#productModal').removeClass('show').hide();
        }
    });

    let activeIndex = -1;

    function setActiveItem(index) {
        const items = $('#modalSearchResults .modal-product-item');
        items.removeClass('active');

        if (items.length === 0) return;
        if (index < 0) index = 0;
        if (index >= items.length) index = items.length - 1;

        activeIndex = index;
        const activeItem = items.eq(activeIndex);
        activeItem.addClass('active');

        if (activeItem[0]) {
            activeItem[0].scrollIntoView({ block: 'nearest' });
        }
    }
</script>
@endsection
