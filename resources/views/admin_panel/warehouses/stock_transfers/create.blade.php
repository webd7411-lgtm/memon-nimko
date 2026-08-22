@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --st-bg: #f8fafc;
  --st-surface: #ffffff;
  --st-border: #e2e8f0;
  --st-border-lt: #f1f5f9;
  --st-text: #0f172a;
  --st-text-sec: #475569;
  --st-text-muted: #64748b;
  --st-primary: #2563eb;
  --st-primary-dark: #1d4ed8;
  --st-success: #10b981;
  --st-warning: #f59e0b;
  --st-danger: #ef4444;
  --st-radius: 16px;
  --st-radius-sm: 10px;
  --st-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --st-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --st-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.st-page * {
  font-family: var(--st-font);
}

.st-page {
  background-color: var(--st-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.st-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--st-radius);
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

.st-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.st-hero > * {
  position: relative;
  z-index: 1;
}

.st-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.st-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.st-hero-icon {
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

.st-hero-badge {
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

.st-btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--st-radius-sm);
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.6rem 1.25rem;
  text-decoration: none;
  transition: all 0.2s ease;
}
.st-btn-back:hover {
  background: rgba(255, 255, 255, 0.22);
  color: #ffffff;
  transform: translateY(-1px);
}

/* ═══════ FORM SECTIONS ═══════ */
.st-section {
  background: var(--st-surface);
  border: 1px solid var(--st-border);
  border-radius: var(--st-radius);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--st-shadow);
  transition: all 0.2s ease;
}
.st-section:hover {
  box-shadow: var(--st-shadow-lg);
}

.st-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--st-border-lt);
}

.st-section-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--st-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.st-section-title i {
  color: var(--st-primary);
  font-size: 1.2rem;
}

/* ═══════ CUSTOM SOURCE CARDS ═══════ */
.from-location-group .form-check-input {
  display: none;
}
.from-location-group .form-check-label {
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid var(--st-border);
  background: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 1.2rem 0.8rem;
  border-radius: 14px;
  position: relative;
  overflow: hidden;
}
.from-location-group .form-check-label:hover {
  border-color: #93c5fd;
  background: #f8fafc;
  transform: translateY(-2px);
}
.from-location-group .form-check-input:checked + .form-check-label {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  border-color: #2563eb;
  box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
  transform: translateY(-2px);
}
.from-location-group .form-check-input:checked + .form-check-label .loc-icon {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}
.from-location-group .form-check-input:checked + .form-check-label .loc-title {
  color: #ffffff !important;
}

.loc-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: #f1f5f9;
  color: var(--st-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  margin-bottom: 0.6rem;
  transition: all 0.2s ease;
}
.loc-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--st-text);
}

/* ═══════ DESTINATION SWITCH ═══════ */
.st-radio-pill-group {
  display: flex;
  gap: 0.75rem;
}

.st-radio-pill {
  flex: 1;
}

.st-radio-pill input {
  display: none;
}

.st-radio-pill label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.8rem 1rem;
  border: 2px solid var(--st-border);
  border-radius: 12px;
  background: #ffffff;
  font-weight: 700;
  font-size: 0.9rem;
  color: var(--st-text-sec);
  cursor: pointer;
  transition: all 0.2s ease;
}

.st-radio-pill label:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.st-radio-pill input:checked + label {
  border-color: var(--st-primary);
  background: #eff6ff;
  color: var(--st-primary);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

/* ═══════ SEARCH PRODUCT BUTTON ═══════ */
.st-btn-search-trigger {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  padding: 0.8rem 1.5rem;
  font-weight: 700;
  font-size: 0.95rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
  transition: all 0.2s ease;
  cursor: pointer;
}
.st-btn-search-trigger:hover {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.35);
}
.st-btn-search-trigger .kbd-badge {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 6px;
  padding: 0.15rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  color: #60a5fa;
}

/* ═══════ PRODUCTS TABLE ═══════ */
.st-table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border: 1px solid var(--st-border);
  border-radius: 12px;
}

.st-product-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin: 0;
}

.st-product-table thead th {
  background: #f8fafc;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--st-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 2px solid var(--st-border);
}

.st-product-table tbody td {
  padding: 0.75rem 0.85rem;
  border-bottom: 1px solid var(--st-border-lt);
  vertical-align: middle;
}

.st-form-control {
  border: 1px solid var(--st-border);
  border-radius: 8px;
  padding: 0.55rem 0.75rem;
  font-size: 0.88rem;
  color: var(--st-text);
  background: #ffffff;
  transition: all 0.2s ease;
}
.st-form-control:focus {
  border-color: var(--st-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}
.st-form-control[readonly] {
  background-color: #f8fafc;
  color: var(--st-text-sec);
  font-weight: 600;
}
.st-product-table td .productSearch {
  width: 100%;
  min-width: 320px;
}

.unit-total-box {
  min-width: 90px;
  padding: 8px 12px;
  border-radius: 10px;
  text-align: center;
  display: inline-block;
  flex-shrink: 0;
  box-shadow: var(--st-shadow);
}
.unit-total-box .label {
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 3px;
}
.unit-total-box input {
  font-weight: 800;
  text-align: center;
  border: none;
  background: rgba(255, 255, 255, 0.95);
  height: 28px;
  border-radius: 6px;
  width: 100%;
  font-size: 0.9rem;
}

/* Modal Search List */
.modal-product-item {
  border-left: 4px solid transparent;
  padding: 0.85rem 1rem;
  cursor: pointer;
  transition: all 0.15s ease;
}
.modal-product-item:hover, .modal-product-item.active {
  background-color: #eff6ff !important;
  border-left-color: var(--st-primary);
}
.modal-product-item.active .product-price {
  color: var(--st-primary) !important;
}

/* Submit Action Button */
.st-btn-submit {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  padding: 0.85rem 3rem;
  font-weight: 800;
  font-size: 1rem;
  box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
  transition: all 0.25s ease;
  cursor: pointer;
}
.st-btn-submit:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 12px 25px -4px rgba(37, 99, 235, 0.5);
}

@media (max-width: 768px) {
  .st-hero { padding: 1.2rem 1.25rem; }
  .st-hero-title h2 { font-size: 1.2rem; }
  .st-section { padding: 1rem; }
  .loc-icon { width: 38px; height: 38px; font-size: 1.1rem; }
  .loc-title { font-size: 0.8rem; }
  .st-form-control { font-size: 16px !important; min-height: 44px; }
}
</style>

<div class="st-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="st-hero">
      <div class="st-hero-title">
        <div class="st-hero-icon">
          <i class="bi bi-arrow-left-right"></i>
        </div>
        <div>
          <h2>Create Stock Transfer</h2>
          <span class="st-hero-badge">New Dispatch Entry</span>
        </div>
      </div>

      <a href="{{ url()->previous() }}" class="st-btn-back">
        <i class="bi bi-arrow-left"></i> Back to Ledger
      </a>
    </div>

    <form action="{{ route('stock_transfers.store') }}" method="POST" novalidate>
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
      <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      {{-- ═══════ SECTION 1: TRANSFER INFO & SOURCE ═══════ --}}
      <div class="st-section">
        <div class="st-section-header">
          <h3 class="st-section-title">
            <i class="bi bi-box-arrow-up-right me-2 text-primary"></i> 1. Source Location (Dispatch From)
          </h3>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-bold">Transfer Date:</span>
            <input type="date" name="transfer_date" class="st-form-control" style="width: auto;" value="{{ date('Y-m-d') }}">
          </div>
        </div>

        <div class="row g-3 from-location-group">
          <!-- Shop Radio Card -->
          <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-check p-0 m-0">
              <input class="form-check-input fromLocation" type="radio" name="from_warehouse_id" id="fromShop" value="Shop">
              <label class="form-check-label shadow-sm" for="fromShop">
                <div class="loc-icon">
                  <i class="bi bi-shop"></i>
                </div>
                <div class="loc-title text-center">Shop Stock</div>
              </label>
            </div>
          </div>

          <!-- Warehouse Radios Cards -->
          @foreach($warehouses as $warehouse)
          <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-check p-0 m-0">
              <input class="form-check-input fromLocation" type="radio" name="from_warehouse_id" id="fromWh{{ $warehouse->id }}" value="{{ $warehouse->id }}">
              <label class="form-check-label shadow-sm" for="fromWh{{ $warehouse->id }}">
                <div class="loc-icon">
                  <i class="bi bi-building"></i>
                </div>
                <div class="loc-title text-center text-truncate w-100 px-1">{{ $warehouse->warehouse_name }}</div>
              </label>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- ═══════ SECTION 2: DESTINATION ═══════ --}}
      <div class="st-section">
        <div class="st-section-header">
          <h3 class="st-section-title">
            <i class="bi bi-geo-alt-fill me-2 text-primary"></i> 2. Destination Location (Receive To)
          </h3>
        </div>

        <div class="row g-4 align-items-center">
          <div class="col-12 col-md-5 col-lg-4">
            <label class="st-label mb-2">Select Target Type</label>
            <div class="st-radio-pill-group">
              <div class="st-radio-pill">
                <input class="transferType" type="radio" name="transfer_to" value="warehouse" id="toWarehouse">
                <label for="toWarehouse">
                  <i class="bi bi-building me-1"></i> Warehouse
                </label>
              </div>

              <div class="st-radio-pill">
                <input class="transferType" type="radio" name="transfer_to" value="shop" id="toShop">
                <label for="toShop">
                  <i class="bi bi-shop me-1"></i> Shop
                </label>
              </div>
            </div>
          </div>

          <!-- TO WAREHOUSE SELECT -->
          <div class="col-12 col-md-7 col-lg-5 d-none" id="toWarehouseBox">
            <label class="st-label mb-2">Target Warehouse</label>
            <select name="to_warehouse_id" class="form-control select2 st-form-control">
              <option value="">-- Select Destination Warehouse --</option>
              @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
              @endforeach
            </select>
          </div>

          <!-- TO SHOP NAME INPUT -->
          <div class="col-12 col-md-7 col-lg-5 d-none" id="toShopBox">
            <label class="st-label mb-2">Shop Destination Name</label>
            <input type="text" name="shop_name" class="st-form-control w-100" placeholder="Enter shop name">
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 3: PRODUCTS & SEARCH ═══════ --}}
      <div class="st-section">
        <div class="st-section-header">
          <h3 class="st-section-title">
            <i class="bi bi-boxes me-2 text-primary"></i> 3. Products to Transfer
          </h3>

          <button type="button" class="st-btn-search-trigger" id="openProductModal">
            <i class="bi bi-search me-1"></i> Search Product <span class="kbd-badge">F2</span>
          </button>
        </div>

        <div class="st-table-responsive">
          <table class="table st-product-table align-middle text-center" id="product_table">
            <thead>
              <tr>
                <th style="min-width: 320px; width: 42%;" class="text-start">Product Item</th>
                <th style="min-width: 90px; width: 10%;">Unit</th>
                <th style="min-width: 110px; width: 12%;">Retail Price</th>
                <th style="min-width: 120px; width: 13%;">Available Stock</th>
                <th style="min-width: 130px; width: 15%;">Transfer Qty</th>
                <th style="min-width: 70px; width: 8%;">Action</th>
              </tr>
            </thead>

            <tbody id="product_body">
              <tr class="product_row">
                <td style="position:relative" class="text-start">
                  <input type="hidden" name="product_id[]" class="product_id">
                  <input type="hidden" name="variant_id[]" class="variant_id">
                  <input type="text" class="st-form-control productSearch w-100" placeholder="Click 'Search Product' or press F2..." readonly>
                </td>
                <td>
                  <input type="text" class="st-form-control unit text-center" readonly>
                </td>
                <td>
                  <input type="number" class="st-form-control price text-center" readonly>
                </td>
                <td>
                  <input type="number" class="st-form-control stock text-center fw-bold" readonly>
                </td>
                <td>
                  <input type="number" name="quantity[]" class="st-form-control quantity text-center fw-bold" required step="any" inputmode="numeric" placeholder="0.00">
                </td>
                <td>
                  <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-circle p-2" style="width:34px;height:34px;line-height:1;">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>

            <tfoot>
              <tr class="bg-light fw-bold">
                <td colspan="4" class="text-end text-uppercase small tracking-wider text-muted">Summary Totals by UOM:</td>
                <td id="unitTotalsFooter" colspan="2" class="text-end"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      {{-- ═══════ SECTION 4: REMARKS & SUBMIT ═══════ --}}
      <div class="st-section">
        <div class="st-section-header">
          <h3 class="st-section-title">
            <i class="bi bi-journal-text me-2 text-primary"></i> 4. Transfer Remarks & Notes
          </h3>
        </div>
        <textarea name="remarks" rows="3" class="st-form-control w-100" placeholder="Add optional dispatch notes or references..."></textarea>
      </div>

      <div class="d-flex justify-content-end mb-4">
        <button type="submit" class="st-btn-submit">
          <i class="bi bi-check-circle-fill me-2"></i> Confirm & Transfer Stock
        </button>
      </div>

    </form>

  </div>
</div>

{{-- ═══════ PRODUCT SEARCH MODAL ═══════ --}}
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

      <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <h5 class="modal-title fw-bold"><i class="bi bi-search me-2"></i> Fast Product Finder</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4" style="background-color: #f8fafc;">
        <div class="input-group input-group-lg mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden;">
          <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
          <input type="text" id="modalProductSearch" class="form-control border-start-0 ps-2" placeholder="Type product name, item code, or scan barcode..." autofocus style="font-size: 1rem;">
        </div>

        <ul class="list-group shadow-sm" id="modalSearchResults" style="max-height:360px; overflow-y:auto; border-radius: 12px;"></ul>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    // Prevent double submission and remove empty rows
    $('form').on('submit', function(e) {
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
        $btn.addClass('disabled').html('<i class="bi bi-hourglass-split me-2"></i> Transferring...');
    });

    function addNewRow() {
        const row = `
<tr class="product_row">
    <td style="position:relative" class="text-start">
        <input type="hidden" name="product_id[]" class="product_id">
        <input type="hidden" name="variant_id[]" class="variant_id">
        <input type="text" class="st-form-control productSearch w-100" placeholder="Click 'Search Product' or press F2..." readonly>
    </td>
    <td>
        <input type="text" class="st-form-control unit text-center" readonly>
    </td>
    <td>
        <input type="number" class="st-form-control price text-center" readonly>
    </td>
    <td>
        <input type="number" class="st-form-control stock text-center fw-bold" readonly>
    </td>
    <td>
        <input type="number" name="quantity[]" class="st-form-control quantity text-center fw-bold" placeholder="0.00">
    </td>
    <td>
        <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-circle p-2" style="width:34px;height:34px;line-height:1;">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>`;

        $('#product_body').append(row);

        setTimeout(() => {
            $('#product_body tr:last .productSearch').focus();
        }, 10);
    }

    function calculateCreateUnitTotals() {
        let totals = {};

        $('#product_table tbody tr').each(function() {
            let qtyVal = $(this).find('.quantity').val();
            let unitVal = $(this).find('.unit').val();

            if (!qtyVal || !unitVal) return;

            let qty = parseFloat(qtyVal);
            let unit = unitVal.trim();

            if (isNaN(qty) || qty <= 0) return;

            totals[unit] = (totals[unit] || 0) + qty;
        });

        let html = '';

        if (Object.keys(totals).length === 0) {
            html = `<span class="text-muted small">No quantities entered</span>`;
        } else {
            html += `<div class="d-flex gap-2 justify-content-end align-items-center">`;

            Object.keys(totals).forEach(unit => {
                let color = unitColors[unit] || 'secondary';

                html += `
            <div class="unit-total-box bg-${color} text-${color === 'warning' ? 'dark' : 'white'}">
                <div class="label">${unit}</div>
                <input type="text" value="${totals[unit].toFixed(2)}" readonly>
            </div>
        `;
            });

            html += `</div>`;
        }

        $('#unitTotalsFooter').html(html);
    }

    function refreshStockForAllRows() {
        var fromWarehouse = $('.fromLocation:checked').val();
        if (!fromWarehouse) {
            $('#product_body tr').find('.stock').val('');
            return;
        }

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
                    $row.find('.stock').val(response.quantity ?? 0);
                });
            }
        });
    }

    const unitColors = {
        Yard: 'primary',
        Piece: 'success',
        Meter: 'warning'
    };

    $(document).ready(function() {
        calculateCreateUnitTotals();

        $('.transferType').on('change', function() {
            let type = $(this).val();

            $('#toWarehouseBox').addClass('d-none');
            $('#toShopBox').addClass('d-none');

            $('select[name="to_warehouse_id"]').val('').trigger('change');
            $('input[name="shop_name"]').val('');

            if (type === 'warehouse') {
                $('#toWarehouseBox').removeClass('d-none');
            }

            if (type === 'shop') {
                $('#toShopBox').removeClass('d-none');
                $('input[name="shop_name"]').val('Shop');
            }
        });

        $(document).on('change', '.fromLocation', function() {
            refreshStockForAllRows();
        });

        $(document).on('input', '.quantity', function() {
            calculateCreateUnitTotals();
        });

        $(document).on('click', '.remove-row', function() {
            var $row = $(this).closest('tr');
            $row.remove();
            calculateCreateUnitTotals();
        });
    });

    let IS_SCANNING = false;
    $(document).on('keydown', '.quantity', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let row = $(this).closest('tr');
            if ($('#product_body tr:last').is(row)) {
                addNewRow();
            }
            setTimeout(() => {
                $('#product_body tr:last .productSearch').focus();
            }, 80);
        }
    });

    let scanBuffer = '';
    let scanTimer = null;
    let lastBarcode = null;
    let lastScanTime = 0;
    const SCAN_DELAY = 500;

    $(document).on('keydown', function(e) {
        if ($(e.target).is('textarea, input')) return;
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
                unit: product.unit_id,
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
            qtyInput.val((+qtyInput.val() || 0) + 1).trigger('input');
            foundRow.addClass('table-success');
            setTimeout(() => foundRow.removeClass('table-success'), 1000);
            foundRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        let row = $('#product_body tr:last');
        if (row.find('.product_id').val()) {
            addNewRow();
            row = $('#product_body tr:last');
        }

        row.find('.product_id').val(res.id).data('barcode', barcode);
        row.find('.variant_id').val(res.variant_id || '');
        row.find('.productSearch').val(res.name).prop('readonly', true);
        row.find('.unit').val(res.unit);
        row.find('.price').val(res.price);
        row.find('.quantity').val(1).trigger('input');

        refreshStockForAllRows();

        setTimeout(() => {
            row.find('.quantity').focus().select();
        }, 50);
    }

    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            e.stopPropagation();
            openProductModal();
            return false;
        }
    });

    $('#openProductModal').on('click', function(e) {
        e.preventDefault();
        openProductModal();
    });

    $(document).on('click', '.modal-product-item', function() {
        if (typeof $('#productModal').modal === 'function') {
            $('#productModal').modal('hide');
        } else {
            $('#productModal').removeClass('show').hide();
        }
        $('#modalProductSearch').val('');
        $('#modalSearchResults').empty();
    });

    $('#productModal').on('shown.bs.modal', function() {
        const $input = $('#modalProductSearch');
        $input.focus();
        activeIndex = 0;
        setActiveItem(activeIndex);
    });

    $('#productModal').on('hidden.bs.modal', function () {
        setTimeout(() => {
            let $row = $('#product_body tr').filter(function () {
                return $(this).find('.product_id').val();
            }).last();
            $row.find('.quantity').focus().select();
        }, 100);
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

    let allProducts = [];

    function loadProducts() {
        $.get("{{ route('get-all-products-for-search') }}", function(res) {
            allProducts = res;
        });
    }

    $(document).ready(function() {
        loadProducts();
    });

    $('#modalProductSearch').on('input', function() {
        let q = $(this).val().trim().toLowerCase();

        if (q.length < 2) {
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
            let noteText = (p.note && p.note.trim() !== '') ? p.note : '-';

            html += `
                <li class="list-group-item modal-product-item"
                    data-id="${p.id}"
                    data-variant_id="${p.variant_id || ''}"
                    data-name="${p.item_name}"
                    data-code="${p.item_code}"
                    data-barcode="${p.barcode || ''}"
                    data-price="${p.price}"
                    data-unit="${p.unit_id}"
                    data-brand="${p.brand ?? ''}"
                    data-note="${noteText}">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-dark fs-6">${p.item_name}</strong>
                        <span class="product-price fw-extrabold text-primary fs-5">Rs ${p.price}</span>
                    </div>
                    <div class="small text-muted mt-1">
                        Code: <strong>${p.item_code || '-'}</strong> | Brand: <span>${p.brand || '-'}</span>
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
        $row.find('.productSearch').val(p.data('name')).prop('readonly', true);
        $row.find('.unit').val(p.data('unit'));
        $row.find('.price').val(p.data('price'));
        $row.find('.quantity').val(1).trigger('input');

        refreshStockForAllRows();
        $('#productModal').modal('hide');
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
