<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Raw Material Purchase - {{ $purchase->invoice_no }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
* { box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { margin: 0; padding: 20px; background: #f8fafc; color: #0f172a; }
.invoice-card { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px; }
.logo-title h2 { margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e293b; }
.logo-title p { margin: 4px 0 0; color: #64748b; font-size: .85rem; }
.inv-meta { text-align: right; }
.inv-meta h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: #2563eb; }
.inv-meta p { margin: 4px 0 0; font-size: .85rem; color: #475569; }

.info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 25px; }
.info-item span { display: block; font-size: .75rem; font-weight: 700; text-transform: uppercase; color: #64748b; }
.info-item strong { font-size: .95rem; color: #0f172a; }

table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
th { background: #f1f5f9; font-size: .78rem; font-weight: 700; text-transform: uppercase; padding: 10px 12px; text-align: left; color: #475569; border-bottom: 1px solid #e2e8f0; }
td { padding: 12px; font-size: .88rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
.text-end { text-align: right; }

.total-box { margin-left: auto; width: 280px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; }
.total-row { display: flex; justify-content: space-between; font-size: 1rem; font-weight: 800; color: #0f172a; }

.actions { max-width: 800px; margin: 0 auto 20px; display: flex; justify-content: space-between; }
.btn { padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: .85rem; cursor: pointer; text-decoration: none; border: none; }
.btn-primary { background: #2563eb; color: #fff; }
.btn-secondary { background: #e2e8f0; color: #334155; }

@media print {
  @page { size: auto; margin: 3mm; }
  body { background: #fff; padding: 0; font-size: 11px; color: #000; }
  .invoice-card { border: none; box-shadow: none; padding: 0; max-width: 100%; width: 100%; }
  .actions { display: none !important; }
  th, td { padding: 4px 5px; font-size: 10px; border-bottom: 1px solid #000; }
  .info-grid { background: #fff; padding: 6px 0; border-bottom: 1px dashed #000; border-top: 1px dashed #000; margin-bottom: 15px; display: flex; justify-content: space-between; }
  .total-box { border: 1px solid #000; background: #fff; width: 100%; margin-top: 10px; }
}
</style>
</head>
<body>

<div class="actions">
  <a href="{{ route('raw-materials.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
  <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i> Print Invoice</button>
</div>

<div class="invoice-card">
  <div class="header">
    <div class="logo-title">
      <h2>Memon Nimko</h2>
      <p>Raw Material Purchase Voucher</p>
    </div>
    <div class="inv-meta">
      <h3>{{ $purchase->invoice_no }}</h3>
      <p><strong>Date:</strong> {{ date('d M, Y', strtotime($purchase->date)) }}</p>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-item">
      <span>Vendor</span>
      <strong>{{ $purchase->vendor_name ?? 'N/A' }}</strong>
    </div>
    <div class="info-item">
      <span>Warehouse / Storage</span>
      <strong>{{ $purchase->warehouse->warehouse_name ?? $purchase->warehouse->name ?? 'Main Stock' }}</strong>
    </div>
    <div class="info-item">
      <span>Created By</span>
      <strong>{{ $purchase->creator->name ?? 'System' }}</strong>
    </div>
  </div>

  @if($purchase->notes)
  <div style="margin-bottom: 20px; font-size: .85rem; color: #475569; background: #fffbeeb3; padding: 10px 14px; border-left: 3px solid #f59e0b; border-radius: 4px;">
    <strong>Notes:</strong> {{ $purchase->notes }}
  </div>
  @endif

  <table>
    <thead>
      <tr>
        <th style="width: 40px;">#</th>
        <th>Raw Material Name</th>
        <th class="text-end">Qty</th>
        <th class="text-end">Price / Unit</th>
        <th class="text-end">Total (PKR)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($purchase->items as $index => $item)
      <tr>
        <td>{{ $index + 1 }}</td>
        <td><strong>{{ $item->rawMaterial->name ?? 'Item' }}</strong> @if($item->rawMaterial->unit) <small style="color:#64748b;">({{ $item->rawMaterial->unit }})</small> @endif</td>
        <td class="text-end">{{ number_format($item->qty, 2) }}</td>
        <td class="text-end">Rs {{ number_format($item->price_per_unit, 2) }}</td>
        <td class="text-end"><strong>Rs {{ number_format($item->total, 2) }}</strong></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="total-box">
    <div class="total-row">
      <span>Grand Total:</span>
      <span style="color: #2563eb;">Rs {{ number_format($purchase->total_cost, 2) }}</span>
    </div>
  </div>
</div>

</body>
</html>
