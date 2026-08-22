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
.pc-hdr h2 { font-size: 1.35rem; font-weight: 800; color: #fff; letter-spacing: -.4px; margin: 0; display: flex; align-items: center; gap: .65rem; }
.pc-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }
.hdr-badge { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: 20px; padding: .25rem .9rem; font-size: .7rem; font-weight: 600; color: rgba(255,255,255,.7); letter-spacing: .4px; text-transform: uppercase; }

.pc-btn {
  display: inline-flex; align-items: center; gap: .4rem; border-radius: var(--pc-radius-sm);
  font-weight: 600; font-size: .8rem; transition: all .25s ease; cursor: pointer;
  text-decoration: none; border: none; padding: .45rem 1.15rem;
}
.pc-btn-primary { background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%); color: #fff; }
.pc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }
.pc-btn-ghost { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); color: #fff; }
.pc-btn-ghost:hover { background: rgba(255,255,255,.14); color: #fff; }
.pc-btn-sm { font-size: .74rem; padding: .35rem .9rem; }
.pc-btn-outline { background: transparent; border: 1.5px solid var(--pc-border); color: var(--pc-text-sec); }
.pc-btn-outline:hover { border-color: var(--pc-accent); color: var(--pc-accent); }

.pc-card { background: var(--pc-surface); border: 1px solid var(--pc-border); border-radius: var(--pc-radius); box-shadow: var(--pc-shadow); transition: box-shadow .3s ease; }
.pc-card:hover { box-shadow: var(--pc-shadow-lg); }
.pc-card-body { padding: 1.5rem; }

.pc-filter {
  display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
  padding: 14px 18px; background: var(--pc-surface); border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm); box-shadow: var(--pc-shadow); margin-bottom: 16px;
}
.pc-filter .fg { display: flex; flex-direction: column; gap: 3px; }
.pc-filter label { font-size: .68rem; font-weight: 700; color: var(--pc-text-sec); text-transform: uppercase; letter-spacing: .4px; }
.pc-filter .pc-fld { border: 1.5px solid var(--pc-border); border-radius: 7px; padding: .38rem .7rem; font-size: .82rem; font-weight: 500; color: var(--pc-text); outline: none; transition: all .2s ease; background: var(--pc-surface); }
.pc-filter .pc-fld:focus { border-color: var(--pc-accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }
select.pc-fld { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%238896ab' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .6rem center; padding-right: 1.8rem; }

.pc-tbl-wrap { border: 1px solid var(--pc-border); border-radius: var(--pc-radius-sm); }
.pc-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .82rem; }
.pc-tbl thead th { background: #f8fafc; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--pc-text-muted); padding: .55rem .7rem; border-bottom: 2px solid var(--pc-border); text-align: left; white-space: nowrap; }
.pc-tbl tbody td { padding: .5rem .7rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; }
.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }
.pc-tbl .pc-ref { font-weight: 700; color: var(--pc-text); white-space: nowrap; }
.pc-tbl .pc-date { color: var(--pc-text-sec); white-space: nowrap; }
.pc-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: .22rem .7rem; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap; }
.pc-badge-inc { background: #e8f9f0; color: #0b7a4d; border: 1px solid #b9ecd2; }
.pc-badge-dec { background: #fdf0f0; color: #a72d2d; border: 1px solid #f3c9c9; }
.pc-tbl .pc-items { line-height: 1.3; min-width: 200px; }
.pc-tbl .pc-items > div { margin-bottom: 2px; font-size: .78rem; }
.pc-tbl .pc-reason { color: var(--pc-text-sec); font-size: .78rem; }
.pc-tbl .pc-user { color: var(--pc-text-sec); font-size: .78rem; font-weight: 500; }
.pc-act { display: inline-flex; align-items: center; gap: 3px; border-radius: 5px; padding: .3rem .7rem; font-size: .72rem; font-weight: 600; transition: all .2s ease; text-decoration: none; border: 1.5px solid transparent; }
.pc-act-view { background: #f0f4fe; border-color: #dde4f7; color: #3b5bb3; }
.pc-act-view:hover { background: #dde4f7; color: #2a4a9e; }

.pc-empty { text-align: center; padding: 2.5rem .85rem; color: var(--pc-text-muted); }
.pc-empty i { font-size: 2rem; color: #ced8e6; display: block; margin-bottom: .5rem; }
.pc-empty span { font-size: .9rem; font-weight: 500; }

.pc-pagi { margin-top: 1rem; }
.pc-pagi nav span, .pc-pagi nav a { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 .5rem; margin: 0 2px; border: 1.5px solid var(--pc-border); border-radius: 6px; font-size: .78rem; font-weight: 600; color: var(--pc-text-sec); text-decoration: none; }
.pc-pagi > nav > div { display: flex; flex-wrap: wrap; justify-content: center; gap: 4px; }

@media (max-width: 768px) { .pc-hdr { padding: 1rem 1.25rem; flex-direction: column; align-items: stretch; gap: .5rem; } .pc-hdr h2 { font-size: 1.1rem; } .pc-card-body { padding: 1rem; } }

/* ═══════════════════════════════════════════════════
   MOBILE PREMIUM CARD LIST
═══════════════════════════════════════════════════ */
.adj-list { display: none; }
.adj-lb {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
  color: var(--pc-text-muted); background: #f5f7fa; border: 1px solid var(--pc-border);
  padding: .18rem .55rem; border-radius: 20px; white-space: nowrap;
}
.adj-lb i { font-size: .72rem; color: var(--pc-accent); }

.adj-card {
  background: var(--pc-surface); border: 1px solid var(--pc-border); border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow); margin-bottom: .9rem; overflow: hidden;
  transition: box-shadow .25s ease;
}
.adj-card:active { box-shadow: var(--pc-shadow-lg); }

.adj-top {
  display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem;
  padding: 1rem 1.05rem .8rem;
}
.adj-ref { display: flex; align-items: center; gap: .7rem; min-width: 0; }
.adj-ref-ic {
  width: 2.6rem; height: 2.6rem; flex: 0 0 auto; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #eef4ff 0%, #e1ecfe 100%);
  color: var(--pc-accent); font-size: 1.15rem; border: 1px solid #dbe7fb;
}
.adj-card.type-dec .adj-ref-ic { background: linear-gradient(135deg, #fdf0f0 0%, #fbe3e3 100%); color: var(--pc-danger); border-color: #f5cfcf; }
.adj-ref-no { font-weight: 800; font-size: .95rem; color: var(--pc-text); letter-spacing: -.2px; word-break: break-word; }
.adj-date { display: flex; align-items: center; gap: 5px; font-size: .74rem; color: var(--pc-text-muted); font-weight: 500; margin-top: 2px; }
.adj-date i { color: var(--pc-text-muted); font-size: .78rem; }

.adj-body { padding: .15rem 1.05rem .85rem; }

.adj-reason { display: flex; align-items: flex-start; gap: .55rem; background: #fafbfd; border: 1px solid var(--pc-border-lt); border-radius: 10px; padding: .6rem .7rem; }
.adj-reason .adj-lb { flex: 0 0 auto; }
.adj-reason-val { font-size: .82rem; font-weight: 600; color: var(--pc-text-sec); word-break: break-word; line-height: 1.35; }

.adj-items { margin-top: .8rem; border: 1px solid var(--pc-border-lt); border-radius: 12px; overflow: hidden; }
.adj-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .55rem .75rem; background: #f7f9fc; border-bottom: 1px solid var(--pc-border-lt);
  font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--pc-text-sec);
}
.adj-items-hd i { color: var(--pc-accent); font-size: .82rem; }
.adj-items-hd b { color: var(--pc-accent); background: #e8f0fe; border-radius: 20px; padding: .05rem .5rem; font-size: .68rem; }
.adj-item {
  display: flex; align-items: center; justify-content: space-between; gap: .7rem;
  padding: .6rem .75rem; background: #fff; border-bottom: 1px solid var(--pc-border-lt);
}
.adj-item:last-child { border-bottom: none; }
.adj-item-nm { font-size: .84rem; font-weight: 600; color: var(--pc-text); line-height: 1.3; word-break: break-word; min-width: 0; flex: 1 1 auto; }
.adj-item-var { display: block; font-size: .7rem; color: var(--pc-text-muted); font-weight: 500; margin-top: 1px; }
.adj-item-qty {
  flex: 0 0 auto; font-size: .76rem; font-weight: 800; color: var(--pc-accent-drk);
  background: #eef4ff; border: 1px solid #dbe7fb; border-radius: 8px; padding: .25rem .6rem; white-space: nowrap;
}
.adj-item-qty.neg { color: var(--pc-danger); background: #fdf0f0; border-color: #f5cfcf; }

.adj-meta { display: flex; flex-wrap: wrap; gap: .5rem .9rem; padding: .7rem 1.05rem .5rem; }
.adj-meta span { display: inline-flex; align-items: center; gap: 5px; font-size: .76rem; color: var(--pc-text-sec); font-weight: 500; min-width: 0; word-break: break-word; }
.adj-meta span i { color: var(--pc-text-muted); font-size: .82rem; }

.adj-foot { padding: .65rem 1.05rem 1rem; }
.adj-view {
  display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%;
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff; text-decoration: none; border-radius: 11px; padding: .68rem .9rem;
  font-size: .82rem; font-weight: 700; letter-spacing: -.1px;
  box-shadow: 0 6px 16px rgba(43,127,255,.18); transition: all .22s ease;
}
.adj-view:hover { color: #fff; text-decoration: none; box-shadow: 0 10px 24px rgba(43,127,255,.3); }
.adj-view i { font-size: .9rem; }
.adj-view .bi-arrow-right { margin-left: auto; }

@media (max-width: 767.98px) {
  .pc-tbl-desk { display: none !important; }
  .adj-list { display: block; }
  .pc-hdr { padding: 1rem 1rem; flex-direction: column; align-items: stretch; gap: .65rem; }
  .pc-hdr h2 { font-size: 1.02rem; margin-bottom: 0; }
  .pc-hdr .pc-hdr-actions { justify-content: stretch; gap: .5rem; }
  .pc-hdr .pc-btn { flex: 1 1 auto; justify-content: center; padding: .55rem .6rem; font-size: .76rem; }
  .hdr-badge { align-self: flex-start; }

  .pc-filter { padding: 12px 12px; margin-bottom: 10px; gap: 10px; }
  .pc-filter .fg { gap: 4px; }
  .pc-filter label { font-size: .6rem; }
  .pc-filter .pc-fld { font-size: .78rem; padding: .5rem .65rem; }
  .pc-filter .fg { width: 100%; }
  .pc-filter .pc-btn { flex: 1 1 auto; justify-content: center; min-height: 42px; font-size: .78rem; }

  .pc-pagi { margin-top: .5rem; }
  .pc-pagi nav span, .pc-pagi nav a { min-width: 34px; height: 34px; padding: 0 .4rem; font-size: .74rem; }
}
</style>

<div class="pc-page">
<div class="container-fluid px-3 px-md-4 py-3">

  <div class="pc-hdr">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <h2><i class="bi bi-arrow-left-right"></i>Stock Adjustments</h2>
      <span class="hdr-badge">{{ $adjustments->total() }} Records</span>
    </div>
    <div class="d-flex gap-2 flex-wrap pc-hdr-actions">
      <a href="{{ route('stock-adjustment.report') }}" class="pc-btn pc-btn-ghost pc-btn-sm"><i class="bi bi-bar-chart"></i>Report</a>
      <a href="{{ route('stock-adjustment.create') }}" class="pc-btn pc-btn-primary pc-btn-sm"><i class="bi bi-plus-circle"></i>New Adjustment</a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:var(--pc-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
    <strong><i class="bi bi-check-circle me-1"></i></strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- FILTERS --}}
  <form method="GET" class="pc-filter">
    <div class="fg"><label>From</label><input type="date" name="from_date" value="{{ request('from_date') }}" class="pc-fld"></div>
    <div class="fg"><label>To</label><input type="date" name="to_date" value="{{ request('to_date') }}" class="pc-fld"></div>
    <div class="fg"><label>Type</label>
      <select name="type" class="pc-fld">
        <option value="">All</option>
        <option value="increase" {{ request('type')=='increase' ? 'selected':'' }}>Increase</option>
        <option value="decrease" {{ request('type')=='decrease' ? 'selected':'' }}>Decrease</option>
      </select>
    </div>
    <button type="submit" class="pc-btn pc-btn-primary pc-btn-sm"><i class="bi bi-funnel"></i>Filter</button>
    <a href="{{ route('stock-adjustment.index') }}" class="pc-btn pc-btn-outline pc-btn-sm"><i class="bi bi-x-circle"></i>Reset</a>
  </form>

  {{-- ══════════════════ DESKTOP TABLE ══════════════════ --}}
  <div class="pc-card pc-tbl-desk">
    <div class="pc-card-body">
      <div class="pc-tbl-wrap">
        <table class="pc-tbl">
          <thead>
            <tr>
              <th>Ref #</th><th>Date</th><th>Type</th><th>Reason</th>
              <th>Items</th><th>By</th><th>Notes</th><th style="width:90px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($adjustments as $adj)
            <tr>
              <td><span class="pc-ref">{{ $adj->ref_no }}</span></td>
              <td><span class="pc-date">{{ \Carbon\Carbon::parse($adj->adjustment_date)->format('d-M-Y') }}</span></td>
              <td>
                <span class="pc-badge {{ $adj->type === 'increase' ? 'pc-badge-inc' : 'pc-badge-dec' }}">
                  <i class="bi bi-{{ $adj->type === 'increase' ? 'plus' : 'dash' }}"></i>
                  {{ ucfirst($adj->type) }}
                </span>
              </td>
              <td class="pc-reason">{{ $adj->reason }}</td>
              <td>
                <div class="pc-items">
                  @foreach($adj->items as $item)
                  @php
                    $isKg = optional($item->product)->unit_type === 'kg';
                    $qty = (float)$item->qty;
                    if ($isKg) { $kg = floor($qty); $gm = round(($qty - $kg) * 1000); $qtyFmt = ($kg > 0 ? $kg.'kg ' : '') . ($gm > 0 ? $gm.'g' : ($kg > 0 ? '' : '0g')); }
                    else { $qtyFmt = number_format($qty, 0) . ' ' . $item->unit; }
                  @endphp
                  <div><strong>{{ optional($item->product)->item_name }}</strong> : <span style="background:#f5f5f5;border-radius:4px;padding:1px 5px;font-weight:700;">{{ $qtyFmt }}</span>@if($item->variant) <span style="color:var(--pc-text-muted);font-size:.7rem;">({{ $item->variant->size_label ?: $item->variant->variant_name }})</span> @endif</div>
                  @endforeach
                </div>
              </td>
              <td><span class="pc-user">{{ optional($adj->user)->name ?? 'System' }}</span></td>
              <td style="color:var(--pc-text-muted);font-size:.78rem;">{{ Str::limit($adj->notes, 35) }}</td>
              <td>
                <a href="{{ route('stock-adjustment.show', $adj->id) }}" class="pc-act pc-act-view"><i class="bi bi-eye"></i>View</a>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="pc-empty"><i class="bi bi-inbox"></i><span>No adjustments found.</span></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="pc-pagi mt-3">{{ $adjustments->links() }}</div>
    </div>
  </div>

  {{-- ══════════════════ MOBILE CARD LIST ══════════════════ --}}
  <div class="adj-list">
    @forelse($adjustments as $adj)
    <div class="adj-card type-{{ $adj->type }}">
      <div class="adj-top">
        <div class="adj-ref">
          <span class="adj-ref-ic"><i class="bi bi-arrow-left-right"></i></span>
          <div>
            <div class="adj-ref-no">{{ $adj->ref_no }}</div>
            <div class="adj-date"><i class="bi bi-calendar3"></i>{{ \Carbon\Carbon::parse($adj->adjustment_date)->format('d-M-Y') }}</div>
          </div>
        </div>
        <span class="pc-badge {{ $adj->type === 'increase' ? 'pc-badge-inc' : 'pc-badge-dec' }}">
          <i class="bi bi-{{ $adj->type === 'increase' ? 'plus-lg' : 'dash-lg' }}"></i>
          {{ $adj->type === 'increase' ? 'Increase' : 'Decrease' }}
        </span>
      </div>

      <div class="adj-body">
        <div class="adj-reason">
          <span class="adj-lb"><i class="bi bi-tag"></i>Reason</span>
          <span class="adj-reason-val">{{ $adj->reason }}</span>
        </div>

        @if(count($adj->items))
        <div class="adj-items">
          <div class="adj-items-hd">
            <span><i class="bi bi-box-seam me-1"></i>Items</span>
            <b>{{ count($adj->items) }}</b>
          </div>
          @foreach($adj->items as $item)
          @php
            $isKg = optional($item->product)->unit_type === 'kg';
            $qty = (float)$item->qty;
            if ($isKg) { $kg = floor($qty); $gm = round(($qty - $kg) * 1000); $qtyFmt = ($kg > 0 ? $kg.'kg ' : '') . ($gm > 0 ? $gm.'g' : ($kg > 0 ? '' : '0g')); }
            else { $qtyFmt = number_format($qty, 0) . ' ' . $item->unit; }
          @endphp
          <div class="adj-item">
            <div class="adj-item-nm">
              {{ optional($item->product)->item_name ?: 'Product #'. $item->product_id }}
              @if($item->variant)
              <span class="adj-item-var">{{ $item->variant->size_label ?: $item->variant->variant_name }}</span>
              @endif
            </div>
            <span class="adj-item-qty {{ $adj->type === 'decrease' ? 'neg' : '' }}">{{ $qtyFmt }}</span>
          </div>
          @endforeach
        </div>
        @endif

        <div class="adj-meta">
          <span><i class="bi bi-person"></i>{{ optional($adj->user)->name ?? 'System' }}</span>
          @if($adj->notes)
          <span class="w-100"><i class="bi bi-chat-dots"></i>{{ $adj->notes }}</span>
          @endif
        </div>
      </div>

      <div class="adj-foot">
        <a href="{{ route('stock-adjustment.show', $adj->id) }}" class="adj-view">
          View Details <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
    @empty
    <div class="pc-card pc-empty" style="min-height:220px;"><i class="bi bi-inbox"></i><span>No adjustments found.</span></div>
    @endforelse
    <div class="pc-pagi">{{ $adjustments->links() }}</div>
  </div>

</div>
</div>
@endsection