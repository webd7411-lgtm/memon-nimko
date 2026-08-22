<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barcodes - {{ $product->item_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --label-w: 52mm;
  --barcode-w: 48mm;
  --barcode-h: 12mm;
}

*, *::before, *::after { box-sizing: border-box; }

body {
  font-family: 'Inter', 'Arial', sans-serif;
  margin: 0;
  padding: 20px;
  background: #f4f6fa;
  color: #1e293b;
}

.top-bar {
  max-width: 900px;
  margin: 0 auto 24px;
  background: #fff;
  border-radius: 12px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.top-bar h2 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: #0b1a33;
}

.top-bar .actions {
  display: flex;
  gap: 10px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: all .2s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #2b7fff, #1a6ae8);
  color: #fff;
}
.btn-primary:hover { opacity: .9; transform: translateY(-1px); }

.btn-secondary {
  background: #e2e8f0;
  color: #334155;
}
.btn-secondary:hover { background: #cbd5e1; }

.labels-grid {
  max-width: 1000px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
  justify-items: center;
}

.label-card-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.label {
  border: 1px dashed #cbd5e1;
  width: var(--label-w);
  padding: 12px 10px;
  text-align: center;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  page-break-inside: avoid;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.label img {
  display: block;
  margin: 0 auto 2px;
}

.brand-name {
  font-size: 14px;
  font-weight: 800;
  letter-spacing: 1.5px;
  margin: 4px 0 2px;
  text-transform: uppercase;
  color: #000;
  text-align: center;
  width: 100%;
}

.product-title {
  font-size: 12px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 4px;
  line-height: 1.2;
  text-align: center;
  width: 100%;
}

.variant-title {
  font-size: 11px;
  font-weight: 800;
  color: #2b7fff;
  margin-bottom: 4px;
  text-align: center;
  width: 100%;
}

.barcode-block {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 6px auto 0;
  text-align: center;
}

.barcode-block svg {
  display: block !important;
  margin: 0 auto !important;
  shape-rendering: crispEdges;
  overflow: visible;
  max-width: 100%;
  height: 35px !important;
}

.barcode-text {
  text-align: center;
  font-size: 11px;
  font-weight: 700;
  line-height: 1.3;
  margin-top: 3px;
  font-family: 'Consolas', monospace;
  color: #000;
}

.price {
  font-size: 13px;
  font-weight: 800;
  margin: 4px 0 2px;
  color: #000;
}

/* PRINT STYLES */
@media print {
  body {
    background: #fff;
    padding: 0;
  }
  .no-print {
    display: none !important;
  }
  .labels-grid {
    display: block;
  }
  .label-card-wrap {
    display: block;
    margin-bottom: 20px;
    page-break-inside: avoid;
  }
  .label {
    border: none;
    box-shadow: none;
    margin: 0 auto;
  }
  /* Single variant print override */
  body.print-single .label-card-wrap:not(.active-print) {
    display: none !important;
  }
}
</style>
</head>
<body>

<div class="top-bar no-print">
  <h2><i class="bi bi-upc-scan"></i> Barcodes for {{ $product->item_name }}</h2>
  <div class="actions">
    <button class="btn btn-primary" onclick="printAll()">
      Print All Labels
    </button>
    <a href="{{ route('product') }}" class="btn btn-secondary">
      Back to Products
    </a>
  </div>
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
    @endphp
    <div class="label-card-wrap" id="variant-wrap-{{ $variant->id }}">
      <div class="label" id="label-{{ $variant->id }}">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 40px; margin-bottom: 2px;" onerror="this.style.display='none'">
        <div class="brand-name">{{ $product->brand->name ?? 'Memon Nimko' }}</div>
        <div class="product-title">{{ $product->item_name }}</div>
        <div class="variant-title">{{ $variant->variant_name }}</div>
        
        @if($vCode)
        <div class="barcode-block">
          {!! DNS1D::getBarcodeSVG($vCode, 'C128', 1.6, 23, 'black', false) !!}
        </div>
        <div class="barcode-text">{{ $vCode }}</div>
        @else
        <div style="font-size:11px;color:#999;margin:10px 0;">No Barcode</div>
        @endif
        
        <div class="price">PKR {{ number_format($vPrice) }}</div>
      </div>
      <button class="btn btn-secondary no-print" style="font-size:11px;padding:4px 12px;" onclick="printSingle({{ $variant->id }})">
        Print Label
      </button>
    </div>
    @endforeach
  @else
    {{-- Main Product Label Fallback --}}
    <div class="label-card-wrap" id="variant-wrap-main">
      <div class="label" id="label-main">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 40px; margin-bottom: 2px;" onerror="this.style.display='none'">
        <div class="brand-name">{{ $product->brand->name ?? 'Memon Nimko' }}</div>
        <div class="product-title">{{ $product->item_name }}</div>
        
        @if($product->barcode_path)
        <div class="barcode-block">
          {!! DNS1D::getBarcodeSVG($product->barcode_path, 'C128', 1.6, 23, 'black', false) !!}
        </div>
        <div class="barcode-text">{{ $product->barcode_path }}</div>
        @else
        <div style="font-size:11px;color:#999;margin:10px 0;">No Barcode</div>
        @endif
        
        <div class="price">PKR {{ number_format($product->activeDiscount ? $product->activeDiscount->final_price : $product->price) }}</div>
      </div>
      <button class="btn btn-secondary no-print" style="font-size:11px;padding:4px 12px;" onclick="printSingle('main')">
        Print Label
      </button>
    </div>
  @endif
</div>

<script>
function printAll() {
  document.body.classList.remove('print-single');
  document.querySelectorAll('.label-card-wrap').forEach(el => el.classList.remove('active-print'));
  window.print();
}

function printSingle(id) {
  document.querySelectorAll('.label-card-wrap').forEach(el => el.classList.remove('active-print'));
  const wrap = document.getElementById('variant-wrap-' + id);
  if (wrap) {
    wrap.classList.add('active-print');
  }
  document.body.classList.add('print-single');
  window.print();
  document.body.classList.remove('print-single');
}
</script>
</body>
</html>
