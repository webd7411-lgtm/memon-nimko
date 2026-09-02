<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Raw Material Purchase - {{ $purchase->invoice_no }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
* { box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { margin: 0; padding: 30px 20px; background: #f1f5f9; color: #0f172a; }

.actions { max-width: 820px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; }
.btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: .88rem; cursor: pointer; text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-primary { background: #2563eb; color: #fff; box-shadow: 0 2px 6px rgba(37,99,235,0.3); }
.btn-secondary { background: #fff; color: #334155; border: 1px solid #cbd5e1; }

.invoice-card { max-width: 820px; margin: 0 auto; background: #fff; border-radius: 14px; padding: 35px 40px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }

.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px dashed #e2e8f0; padding-bottom: 22px; margin-bottom: 22px; }
.logo-title h2 { margin: 0; font-size: 1.6rem; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
.logo-title p { margin: 4px 0 0; color: #64748b; font-size: .88rem; font-weight: 500; }

.inv-meta { text-align: right; }
.inv-meta .inv-no { display: inline-block; background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 1.05rem; padding: 4px 12px; border-radius: 6px; border: 1px solid #bfdbfe; font-family: monospace; }
.inv-meta p { margin: 6px 0 0; font-size: .88rem; color: #475569; }

.info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #f8fafc; padding: 18px 20px; border-radius: 10px; border: 1px solid #f1f5f9; margin-bottom: 25px; }
.info-item span { display: block; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 4px; }
.info-item strong { font-size: .98rem; color: #0f172a; word-break: break-word; }

table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
th { background: #f1f5f9; font-size: .75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 14px; text-align: left; color: #475569; border-bottom: 2px solid #cbd5e1; }
td { padding: 14px; font-size: .9rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
.text-end { text-align: right; }
.text-center { text-align: center; }

.total-box { margin-left: auto; width: 320px; background: #f8fafc; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0; }
.total-row { display: flex; justify-content: space-between; align-items: center; font-size: 1.1rem; font-weight: 800; color: #0f172a; }

@media print {
  @page { size: A4 portrait; margin: 12mm 12mm; }
  body { background: #fff; padding: 10px 15px; font-size: 12px; color: #000; }
  .actions { display: none !important; }
  .invoice-card { border: none; box-shadow: none; padding: 10px 15px; max-width: 100%; width: 100%; }
  .header { border-bottom: 2px solid #000; padding-bottom: 14px; margin-bottom: 16px; }
  .logo-title h2 { font-size: 1.4rem; color: #000; }
  .inv-meta .inv-no { background: none; color: #000; border: none; padding: 0; font-size: 1rem; }
  .info-grid { background: #fff; padding: 10px 12px; border: 1px solid #000; border-radius: 6px; margin-bottom: 18px; }
  .info-item span { color: #333; }
  .info-item strong { color: #000; }
  table { margin-bottom: 20px; }
  th { background: #f1f5f9 !important; -webkit-print-color-adjust: exact; color: #000; padding: 8px 10px; border-bottom: 2px solid #000; }
  td { padding: 8px 10px; border-bottom: 1px solid #ddd; color: #000; }
  .total-box { border: 2px solid #000; background: #fff; width: 280px; margin-top: 15px; padding: 12px 16px; }
  .total-row span { color: #000 !important; }
}
</style>
</head>
<body>

<div class="actions">
  <a href="{{ route('raw-materials.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Raw Materials</a>
  <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print Invoice</button>
</div>

<div class="invoice-card">
  <div class="header">
    <div class="logo-title">
      <h2>Memon Nimko</h2>
      <p>Raw Material Purchase Voucher</p>
    </div>
    <div class="inv-meta">
      <div class="inv-no">{{ $purchase->invoice_no }}</div>
      <p><strong>Date:</strong> {{ date('d M, Y', strtotime($purchase->date)) }}</p>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-item">
      <span>Vendor Name</span>
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
  <div style="margin-bottom: 20px; font-size: .88rem; color: #334155; background: #fffbeeb3; padding: 12px 16px; border-left: 4px solid #f59e0b; border-radius: 6px; border: 1px solid #fef3c7;">
    <strong>Notes:</strong> {{ $purchase->notes }}
  </div>
  @endif

  <table>
    <thead>
      <tr>
        <th style="width: 40px;" class="text-center">#</th>
        <th>Raw Material Name</th>
        <th class="text-end" style="width: 110px;">Qty</th>
        <th class="text-end" style="width: 130px;">Price / Unit</th>
        <th class="text-end" style="width: 140px;">Total (PKR)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($purchase->items as $index => $item)
      <tr>
        <td class="text-center" style="font-weight: 600; color: #64748b;">{{ $index + 1 }}</td>
        <td>
          <strong style="color: #0f172a;">{{ $item->rawMaterial->name ?? 'Item' }}</strong>
          @if($item->rawMaterial->unit)
            <small style="color: #64748b; font-weight: 600; margin-left: 4px;">({{ $item->rawMaterial->unit }})</small>
          @endif
        </td>
        <td class="text-end" style="font-weight: 600;">{{ number_format($item->qty, 2) }}</td>
        <td class="text-end">Rs {{ number_format($item->price_per_unit, 2) }}</td>
        <td class="text-end" style="font-weight: 800; color: #0f172a;">Rs {{ number_format($item->total, 2) }}</td>
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
