<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barcode Label - {{ $product->item_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style id="pagePrintStyle">
@page {
  size: 4in 1.2in;
  margin: 0mm;
}
</style>
<style>
:root {
  --label-w: 4in;
  --label-h: 1.2in;
}

*, *::before, *::after { 
  box-sizing: border-box; 
  margin: 0; 
  padding: 0; 
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: #f1f5f9;
  color: #000000;
  padding: 16px;
}

/* TOP BAR (Controls) */
.top-bar {
  max-width: 940px;
  margin: 0 auto 16px;
  background: #ffffff;
  border-radius: 12px;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.top-bar-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.top-bar h2 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: #0f172a;
}

.badge-landscape {
  background: #0f766e;
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  padding: 3px 8px;
  border-radius: 6px;
}

.controls-group {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.control-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
}

.control-input {
  padding: 4px 8px;
  border-radius: 6px;
  border: 1.5px solid #cbd5e1;
  font-size: 12px;
  font-weight: 700;
  outline: none;
  background: #f8fafc;
  cursor: pointer;
}
.control-input:focus {
  border-color: #0f766e;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: all .2s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #0f766e, #0d9488);
  color: #fff;
  box-shadow: 0 2px 8px rgba(15,118,110,0.25);
}
.btn-primary:hover { opacity: .92; transform: translateY(-1px); }

.btn-secondary {
  background: #e2e8f0;
  color: #334155;
}
.btn-secondary:hover { background: #cbd5e1; }

.print-notice {
  max-width: 940px;
  margin: 0 auto 16px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* LABELS PREVIEW GRID */
.labels-grid {
  max-width: 960px;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  justify-content: center;
}

.label-card-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

/* ULTRA COMPACT THERMAL STICKER */
.label {
  width: var(--label-w);
  height: var(--label-h);
  max-height: var(--label-h);
  padding: 4px 8px;
  background: #ffffff;
  border: 1.5px dashed #94a3b8;
  border-radius: 4px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  box-sizing: border-box;
  overflow: hidden;
  page-break-inside: avoid;
}

/* INNER TIGHT WRAPPER */
.label-inner {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1.5px;
}

/* 1. PRODUCT NAME + VARIANT */
.item-title-wrap {
  width: 100%;
  font-size: 13.5px;
  font-weight: 900;
  color: #000000;
  line-height: 1.15;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  letter-spacing: -0.2px;
  margin-top: -2px;
  margin-bottom: 3px;
}

.product-name {
  font-weight: 900;
}

.variant-name {
  font-weight: 800;
  margin-left: 2px;
}

/* 2. BARCODE BLOCK */
.barcode-block {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0;
  overflow: hidden;
}

.barcode-block svg {
  display: block !important;
  margin: 0 auto !important;
  shape-rendering: crispEdges;
  overflow: visible;
  max-width: 86%;
  height: 22px !important;
}

/* 3. BARCODE NUMBER */
.barcode-text {
  text-align: center;
  font-size: 12px;
  font-weight: 900;
  line-height: 1;
  font-family: 'Consolas', 'Courier New', monospace;
  color: #000000;
  letter-spacing: 2px;
}

/* 4. BOTTOM ROW: PRICE + EXPIRE DATE (CENTERED) */
.bottom-info-row {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  line-height: 1;
  margin-top: 1.5px;
  text-align: center;
}

.price-tag {
  font-size: 11.5px;
  font-weight: 900;
  color: #000000;
  letter-spacing: 0.1px;
}

.info-divider {
  font-size: 10px;
  font-weight: 700;
  color: #64748b;
  user-select: none;
}

.expiry-tag {
  font-size: 10.5px;
  font-weight: 800;
  color: #000000;
  letter-spacing: 0.1px;
}

.no-barcode {
  font-size: 10px;
  font-weight: 700;
  color: #dc2626;
  padding: 1px 0;
}

.card-actions {
  display: flex;
  align-items: center;
  gap: 6px;
}

.hide-expiry-toggle {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  user-select: none;
  background: #f1f5f9;
  padding: 4px 7px;
  border-radius: 6px;
  border: 1.5px solid #cbd5e1;
  transition: all .15s ease;
}

.hide-expiry-toggle:hover {
  background: #e2e8f0;
}

.hide-expiry-toggle input {
  cursor: pointer;
  accent-color: #0f766e;
}

.copies-input {
  width: 50px;
  text-align: center;
  padding: 4px;
  border-radius: 6px;
  border: 1.5px solid #cbd5e1;
  font-size: 12px;
  font-weight: 700;
}

#dynamicPrintContainer {
  display: none;
}

/* PRINT MEDIA QUERIES */
@media print {
  html, body {
    width: 100% !important;
    height: auto !important;
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  .no-print {
    display: none !important;
  }
  
  .labels-grid {
    display: block !important;
    width: var(--label-w) !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  .label-card-wrap {
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  .label {
    width: var(--label-w) !important;
    height: var(--label-h) !important;
    max-height: var(--label-h) !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    margin: 0 auto !important;
    padding: 3px 6px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    page-break-inside: avoid !important;
    page-break-after: always !important;
    break-after: page !important;
  }

  body.printing-dynamic .labels-grid {
    display: none !important;
  }

  body.printing-dynamic #dynamicPrintContainer {
    display: block !important;
    width: var(--label-w);
    margin: 0 !important;
    padding: 0 !important;
  }
}
</style>
</head>
<body>

<div class="top-bar no-print">
  <div class="top-bar-title">
    <h2><i class="bi bi-upc-scan"></i> Barcode Labels</h2>
    <span class="badge-landscape">Landscape Fit</span>
  </div>

  <div class="controls-group">
    {{-- Label Size Preset Selector --}}
    <div class="control-item">
      <span><i class="bi bi-aspect-ratio"></i> Label Size:</span>
      <select id="sizePresetSelector" class="control-input" onchange="changeLabelPreset(this.value)">
        <option value="4in|1.2in" selected>4" x 1.2" (Ultra Compact Fit)</option>
        <option value="4in|1.0in">4" x 1.0" (1-Inch Low Height)</option>
        <option value="4in|1.5in">4" x 1.5" (Standard 2/4 Landscape)</option>
        <option value="4in|2.0in">4" x 2.0" (Full 4x2 Landscape)</option>
        <option value="2in|1.0in">2" x 1.0" (50mm x 25mm Mini Roll)</option>
        <option value="2in|1.5in">2" x 1.5" (50mm x 38mm Roll)</option>
      </select>
    </div>

    {{-- Expiry Date Quick Picker --}}
    <div class="control-item">
      <span><i class="bi bi-calendar-event"></i> Expire Date:</span>
      <input type="date" id="expiryPicker" class="control-input" value="" onchange="updateAllExpiry(this.value)">
    </div>

    {{-- Master No Expiry Checkbox --}}
    <div class="control-item">
      <label class="hide-expiry-toggle" style="background:#f8fafc; padding:5px 9px;" title="Tamam labels se expire date hide karein">
        <input type="checkbox" id="masterNoExpiry" onchange="toggleAllExpiry(this.checked)">
        <span><i class="bi bi-calendar-x"></i> No Expiry (All)</span>
      </label>
    </div>

    <button class="btn btn-primary" onclick="printAll()">
      <i class="bi bi-printer-fill"></i> Print All Labels
    </button>
    <a href="{{ route('product') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Back to Products
    </a>
  </div>
</div>

<div class="print-notice no-print">
  <i class="bi bi-info-circle-fill"></i>
  <span><strong>Tip:</strong> Chrome / Edge print dialog me <strong>Margins: None</strong> select karein taake print sticker se bahar na nikle.</span>
</div>

<div class="labels-grid">
  @if($product->variants && $product->variants->count() > 0)
    @foreach($product->variants as $variant)
    @php
      $vCode = $variant->barcode_path ?: $product->barcode_path;
      $vPrice = $variant->price ?: $product->price;
      if($product->activeDiscount) {
        $vPrice = $product->activeDiscount->final_price;
      }
      $variantTitle = $variant->size_label ?: $variant->variant_name;
      if ($variantTitle) {
          $variantTitle = trim(str_ireplace($product->item_name, '', $variantTitle));
          $variantTitle = ltrim($variantTitle, ' -:()');
          $variantTitle = rtrim($variantTitle, ')');
      }
      $hasExpiry = !empty($variant->expiry_date) && !in_array(trim((string)$variant->expiry_date), ['0000-00-00', 'null', '']);
      $vExpiry = $hasExpiry ? \Carbon\Carbon::parse($variant->expiry_date)->format('d/m/Y') : null;
    @endphp
    <div class="label-card-wrap" id="variant-wrap-{{ $variant->id }}">
      <div class="label" id="label-{{ $variant->id }}">
        <div class="label-inner">
          {{-- 1. Product Name + Variant --}}
          <div class="item-title-wrap">
            <span class="product-name">{{ $product->item_name }}</span>
            @if(!empty($variantTitle))
              <span class="variant-name">({{ $variantTitle }})</span>
            @endif
          </div>
          
          {{-- 2. Barcode SVG --}}
          @if($vCode)
          <div class="barcode-block">
            {!! DNS1D::getBarcodeSVG($vCode, 'C128', 1.5, 22, 'black', false) !!}
          </div>
          {{-- 3. Barcode Number --}}
          <div class="barcode-text">{{ $vCode }}</div>
          @else
          <div class="no-barcode">No Barcode Assigned</div>
          @endif

          {{-- 4. Price & Expire Date (Centered) --}}
          <div class="bottom-info-row">
            <span class="price-tag">PKR {{ number_format($vPrice) }}</span>
            <span class="info-divider expiry-element" style="{{ $hasExpiry ? '' : 'display:none;' }}">|</span>
            <span class="expiry-tag expiry-element" style="{{ $hasExpiry ? '' : 'display:none;' }}">Exp: <span class="expiry-text">{{ $vExpiry ?? '' }}</span></span>
          </div>
        </div>
      </div>

      <div class="card-actions no-print">
        <label class="hide-expiry-toggle" title="Check karein agar expire date print nahi karni">
          <input type="checkbox" class="variant-expiry-toggle" data-id="{{ $variant->id }}" {{ $hasExpiry ? '' : 'checked' }} onchange="toggleExpiry(this, '{{ $variant->id }}')">
          <span>No Expiry</span>
        </label>
        <input type="number" id="copies-{{ $variant->id }}" class="copies-input" value="1" min="1" max="500" title="Copies count">
        <button class="btn btn-secondary" style="font-size:11.5px; padding:4px 12px;" onclick="printSingle({{ $variant->id }})">
          <i class="bi bi-printer"></i> Print
        </button>
      </div>
    </div>
    @endforeach
  @else
    {{-- Main Product Label Fallback --}}
    @php
      $mainPrice = $product->activeDiscount ? $product->activeDiscount->final_price : $product->price;
      $mainHasExpiry = !empty($product->expiry_date) && !in_array(trim((string)$product->expiry_date), ['0000-00-00', 'null', '']);
      $mainExpiry = $mainHasExpiry ? \Carbon\Carbon::parse($product->expiry_date)->format('d/m/Y') : null;
    @endphp
    <div class="label-card-wrap" id="variant-wrap-main">
      <div class="label" id="label-main">
        <div class="label-inner">
          {{-- 1. Product Name --}}
          <div class="item-title-wrap">
            <span class="product-name">{{ $product->item_name }}</span>
          </div>
          
          {{-- 2. Barcode SVG --}}
          @if($product->barcode_path)
          <div class="barcode-block">
            {!! DNS1D::getBarcodeSVG($product->barcode_path, 'C128', 1.5, 22, 'black', false) !!}
          </div>
          {{-- 3. Barcode Number --}}
          <div class="barcode-text">{{ $product->barcode_path }}</div>
          @else
          <div class="no-barcode">No Barcode Assigned</div>
          @endif

          {{-- 4. Price & Expire Date (Centered) --}}
          <div class="bottom-info-row">
            <span class="price-tag">PKR {{ number_format($mainPrice) }}</span>
            <span class="info-divider expiry-element" style="{{ $mainHasExpiry ? '' : 'display:none;' }}">|</span>
            <span class="expiry-tag expiry-element" style="{{ $mainHasExpiry ? '' : 'display:none;' }}">Exp: <span class="expiry-text">{{ $mainExpiry ?? '' }}</span></span>
          </div>
        </div>
      </div>

      <div class="card-actions no-print">
        <label class="hide-expiry-toggle" title="Check karein agar expire date print nahi karni">
          <input type="checkbox" class="variant-expiry-toggle" data-id="main" {{ $mainHasExpiry ? '' : 'checked' }} onchange="toggleExpiry(this, 'main')">
          <span>No Expiry</span>
        </label>
        <input type="number" id="copies-main" class="copies-input" value="1" min="1" max="500" title="Copies count">
        <button class="btn btn-secondary" style="font-size:11.5px; padding:4px 12px;" onclick="printSingle('main')">
          <i class="bi bi-printer"></i> Print
        </button>
      </div>
    </div>
  @endif
</div>

{{-- Container for multi-copy single item printing --}}
<div id="dynamicPrintContainer"></div>

<script>
function changeLabelPreset(val) {
  const parts = val.split('|');
  const wVal = parts[0];
  const hVal = parts[1];
  const root = document.documentElement;
  const printStyle = document.getElementById('pagePrintStyle');
  root.style.setProperty('--label-w', wVal);
  root.style.setProperty('--label-h', hVal);
  printStyle.innerHTML = `@page { size: ${wVal} ${hVal}; margin: 0mm; }`;
}

function updateAllExpiry(dateVal) {
  if (!dateVal) return;
  const parts = dateVal.split('-');
  if (parts.length === 3) {
    const formatted = `${parts[2]}/${parts[1]}/${parts[0]}`;
    document.querySelectorAll('.expiry-text').forEach(el => {
      el.textContent = formatted;
    });
    document.querySelectorAll('.variant-expiry-toggle').forEach(cb => {
      cb.checked = false;
      const id = cb.getAttribute('data-id');
      if (id) toggleExpiry(cb, id);
    });
  }
}

function toggleExpiry(checkbox, id) {
  const label = document.getElementById('label-' + id);
  if (!label) return;
  const expiryElements = label.querySelectorAll('.expiry-element');
  expiryElements.forEach(el => {
    el.style.display = checkbox.checked ? 'none' : '';
  });
}

function toggleAllExpiry(isChecked) {
  document.querySelectorAll('.variant-expiry-toggle').forEach(cb => {
    cb.checked = isChecked;
    const id = cb.getAttribute('data-id');
    if (id) {
      toggleExpiry(cb, id);
    }
  });
}

function printAll() {
  document.body.classList.remove('printing-dynamic');
  window.print();
}

function printSingle(id) {
  const qtyInput = document.getElementById('copies-' + id);
  const copies = parseInt(qtyInput ? qtyInput.value : 1) || 1;
  const sourceLabel = document.getElementById('label-' + id);
  
  if (!sourceLabel) return;

  const printContainer = document.getElementById('dynamicPrintContainer');
  printContainer.innerHTML = '';
  
  for (let i = 0; i < copies; i++) {
    const clone = sourceLabel.cloneNode(true);
    clone.id = 'label-print-' + id + '-' + i;
    printContainer.appendChild(clone);
  }
  
  document.body.classList.add('printing-dynamic');
  window.print();
  
  setTimeout(() => {
    document.body.classList.remove('printing-dynamic');
    printContainer.innerHTML = '';
  }, 600);
}
</script>
</body>
</html>
