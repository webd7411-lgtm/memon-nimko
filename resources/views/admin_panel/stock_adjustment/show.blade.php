@extends('admin_panel.layout.app')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
:root {
  --pc-bg: #f1f4f9; --pc-surface: #ffffff; --pc-border: #e9edf2; --pc-border-lt: #f1f4f9;
  --pc-text: #0b1a33; --pc-text-sec: #54657e; --pc-text-muted: #8896ab;
  --pc-accent: #2b7fff; --pc-accent-drk: #1a6ae8; --pc-success: #0fae6b; --pc-danger: #e54545;
  --pc-radius: 14px; --pc-radius-sm: 9px;
  --pc-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --pc-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --pc-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --pc-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}
.pc-page * { font-family: var(--pc-font); }
.pc-page { background: var(--pc-bg); min-height: 100vh; padding-bottom: 2.5rem; overflow-x: hidden; }
.pc-page, .pc-page .container-fluid { max-width: 100%; }

.pc-hdr {
  position: relative; background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--pc-radius); padding: 1.3rem 2rem; margin-bottom: 1.5rem;
  box-shadow: var(--pc-shadow-xl); display: flex; flex-wrap: wrap; align-items: center;
  justify-content: space-between; overflow: hidden;
}
.pc-hdr::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.15) 0%, transparent 100%), radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.08) 0%, transparent 100%); pointer-events: none; }
.pc-hdr::after { content: ''; position: absolute; inset: 0; background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); opacity: .5; pointer-events: none; }
.pc-hdr > * { position: relative; z-index: 1; }
.pc-hdr h2 { font-size: 1.3rem; font-weight: 800; color: #fff; letter-spacing: -.4px; margin: 0; display: flex; align-items: center; gap: .65rem; word-break: break-word; }
.pc-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }
.pc-back {
  background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); color: #fff;
  display: inline-flex; align-items: center; gap: .45rem; border-radius: var(--pc-radius-sm);
  padding: .5rem 1.1rem; font-size: .8rem; font-weight: 600; transition: all .2s ease; text-decoration: none;
}
.pc-back:hover { background: rgba(255,255,255,.15); color: #fff; text-decoration: none; }

.pc-card { background: var(--pc-surface); border: 1px solid var(--pc-border); border-radius: var(--pc-radius); box-shadow: var(--pc-shadow); margin-bottom: 1.25rem; }
.pc-card-hdr {
  padding: 1rem 1.5rem; border-bottom: 1px solid var(--pc-border-lt);
  font-size: .8rem; font-weight: 700; color: var(--pc-text); display: flex; align-items: center; gap: .5rem;
}
.pc-card-hdr i { color: var(--pc-accent); font-size: .95rem; }

.pc-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: .3rem .85rem; font-size: .74rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap; }
.pc-badge-inc { background: #e8f9f0; color: #0b7a4d; border: 1px solid #b9ecd2; }
.pc-badge-dec { background: #fdf0f0; color: #a72d2d; border: 1px solid #f3c9c9; }

.pc-info { padding: .5rem 1.5rem; }
.pc-info-row { display: flex; align-items: flex-start; gap: .9rem; padding: .85rem 0; border-bottom: 1px solid var(--pc-border-lt); }
.pc-info-row:last-child { border-bottom: none; }
.pc-info-ic {
  width: 2.2rem; height: 2.2rem; flex: 0 0 auto; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; background: #f0f4fe; color: var(--pc-accent); font-size: .95rem;
}
.pc-info-lb { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--pc-text-muted); margin-bottom: 2px; }
.pc-info-val { font-size: .9rem; font-weight: 600; color: var(--pc-text); word-break: break-word; line-height: 1.35; }

.pc-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .84rem; }
.pc-tbl thead th {
  background: #f8fafc; font-size: .66rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .5px; color: var(--pc-text-muted); padding: .6rem .9rem;
  border-bottom: 2px solid var(--pc-border); text-align: left; white-space: nowrap;
}
.pc-tbl tbody td { padding: .6rem .9rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; color: var(--pc-text); }
.pc-tbl tbody tr:hover td { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }
.pc-var { color: var(--pc-text-muted); font-size: .76rem; font-weight: 500; }

.pc-empty { text-align: center; padding: 2.5rem .85rem; color: var(--pc-text-muted); }
.pc-empty i { font-size: 2rem; color: #ced8e6; display: block; margin-bottom: .5rem; }

@media (max-width: 768px) {
  .pc-hdr { padding: 1.1rem 1.25rem; flex-direction: column; align-items: stretch; gap: .7rem; }
  .pc-hdr h2 { font-size: 1.05rem; }
  .pc-card-hdr { padding: .85rem 1rem; }
  .pc-info { padding: .3rem 1rem; }
}

/* ═══════ MOBILE PREMIUM ═══════ */
@media (max-width: 767.98px) {
  .pc-hdr { padding: 1rem; }
  .pc-hdr h2 { font-size: .98rem; }
  .pc-back { width: 100%; justify-content: center; }

  .pc-info-row { gap: .7rem; padding: .8rem 0; }

  .pc-card-bd { padding: 0; }

  /* Items table → stacked cards */
  .pc-tbl-wrap-mob { padding: .9rem; }
  .pc-tbl, .pc-tbl tbody, .pc-tbl tr, .pc-tbl td { display: block; width: 100% !important; }
  .pc-tbl thead { display: none; }
  .pc-tbl tbody tr {
    border: 1.5px solid var(--pc-border);
    border-radius: var(--pc-radius);
    margin-bottom: .8rem;
    padding: .75rem .85rem;
    background: var(--pc-surface);
  }
  .pc-tbl tbody tr:last-child { margin-bottom: 0; }
  .pc-tbl tbody td { padding: 0 0 .55rem; border: none !important; }
  .pc-tbl tbody td:last-child { padding-bottom: 0; }
  .pc-tbl tbody td::before {
    content: attr(data-label);
    display: block; font-size: .62rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--pc-text-muted); margin-bottom: .25rem;
  }
  .pc-tbl tbody td.adj-num::before { display: none; }
}
</style>

<div class="pc-page">
<div class="container-fluid px-3 px-md-4 py-3">

  <div class="pc-hdr">
    <h2><i class="bi bi-box-seam"></i>Adjustment Detail</h2>
    <div class="d-flex flex-wrap align-items-center gap-2">
      <span class="pc-badge {{ $adjustment->type === 'increase' ? 'pc-badge-inc' : 'pc-badge-dec' }}">
        <i class="bi bi-{{ $adjustment->type === 'increase' ? 'plus-lg' : 'dash-lg' }}"></i>
        {{ $adjustment->type === 'increase' ? 'Increase' : 'Decrease' }}
      </span>
      <a href="{{ route('stock-adjustment.index') }}" class="pc-back"><i class="bi bi-arrow-left"></i>Back to Adjustments</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="pc-card">
        <div class="pc-card-hdr"><i class="bi bi-clipboard-data"></i>Adjustment Information</div>
        <div class="pc-info">
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-hash"></i></span>
            <div>
              <div class="pc-info-lb">Ref No</div>
              <div class="pc-info-val">{{ $adjustment->ref_no }}</div>
            </div>
          </div>
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-calendar3"></i></span>
            <div>
              <div class="pc-info-lb">Date</div>
              <div class="pc-info-val">{{ \Carbon\Carbon::parse($adjustment->adjustment_date)->format('d-M-Y') }}</div>
            </div>
          </div>
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-tag"></i></span>
            <div>
              <div class="pc-info-lb">Reason</div>
              <div class="pc-info-val">{{ $adjustment->reason }}</div>
            </div>
          </div>
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-building"></i></span>
            <div>
              <div class="pc-info-lb">Warehouse</div>
              <div class="pc-info-val">{{ $adjustment->warehouse ? $adjustment->warehouse->warehouse_name : 'N/A' }}</div>
            </div>
          </div>
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-person"></i></span>
            <div>
              <div class="pc-info-lb">Created By</div>
              <div class="pc-info-val">{{ optional($adjustment->user)->name ?? 'System' }}</div>
            </div>
          </div>
          @if($adjustment->notes)
          <div class="pc-info-row">
            <span class="pc-info-ic"><i class="bi bi-chat-dots"></i></span>
            <div>
              <div class="pc-info-lb">Notes</div>
              <div class="pc-info-val">{{ $adjustment->notes }}</div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="pc-card">
        <div class="pc-card-hdr"><i class="bi bi-box-seam"></i>Items <span class="pc-badge pc-badge-inc ms-auto" style="font-size:.68rem;padding:.15rem .65rem;">{{ $adjustment->items->count() }}</span></div>
        <div class="pc-tbl-wrap-mob">
          <table class="pc-tbl">
            <thead>
              <tr>
                <th>#</th><th>Product</th><th>Code</th><th>Unit</th>
                <th>Qty Entered</th><th>Stock Impact</th><th>Note</th>
              </tr>
            </thead>
            <tbody>
              @forelse($adjustment->items as $i => $item)
              @php
                $isKg = optional($item->product)->unit_type === 'kg';
                $qty = (float)$item->qty;
                if ($isKg) {
                  $kg = floor($qty); $gm = round(($qty - $kg) * 1000);
                  $qtyFmt = ($kg > 0 ? $kg.'kg ' : '') . ($gm > 0 ? $gm.'g' : ($kg > 0 ? '' : '0g'));
                } else {
                  $qtyFmt = number_format($qty, 0) . ' ' . $item->unit;
                }
                $stockFmt = $isKg ? number_format($item->qty_stock, 0) . ' g' : number_format($item->qty_stock, 0) . ' ' . $item->unit;
              @endphp
              <tr>
                <td class="adj-num"><strong>{{ $i + 1 }}</strong></td>
                <td data-label="Product">
                  <strong>{{ optional($item->product)->item_name ?: 'Product #'.$item->product_id }}</strong>
                  @if($item->variant)<div class="pc-var">{{ $item->variant->size_label ?: $item->variant->variant_name }}</div>@endif
                </td>
                <td data-label="Code">{{ optional($item->product)->item_code }}</td>
                <td data-label="Unit">{{ $item->unit }}</td>
                <td data-label="Qty Entered"><strong>{{ $qtyFmt }}</strong></td>
                <td data-label="Stock Impact">
                  @if($adjustment->type === 'increase')
                    <span style="color:var(--pc-success);font-weight:800;">+{{ $stockFmt }}</span>
                  @else
                    <span style="color:var(--pc-danger);font-weight:800;">-{{ $stockFmt }}</span>
                  @endif
                </td>
                <td data-label="Note">{{ $item->notes ?? '-' }}</td>
              </tr>
              @empty
              <tr><td colspan="7" class="pc-empty"><i class="bi bi-inbox"></i><span>No items on this adjustment.</span></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
@endsection