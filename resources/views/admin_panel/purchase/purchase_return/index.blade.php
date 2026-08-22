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
.pc-page { background: var(--pc-bg); min-height: 100vh; padding-bottom: 2.5rem; }

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
.pc-hdr .hdr-badge { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1); border-radius: 20px; padding: .25rem .9rem; font-size: .7rem; font-weight: 600; color: rgba(255,255,255,.65); letter-spacing: .4px; text-transform: uppercase; }

.pc-btn {
  display: inline-flex; align-items: center; gap: .4rem; border-radius: var(--pc-radius-sm);
  font-weight: 600; font-size: .78rem; transition: all .25s ease; cursor: pointer;
  text-decoration: none; border: none; padding: .45rem 1.1rem;
}
.pc-btn-primary { background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%); color: #fff; }
.pc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }
.pc-btn-sm { font-size: .72rem; padding: .35rem .85rem; }

.pc-card { background: var(--pc-surface); border: 1px solid var(--pc-border); border-radius: var(--pc-radius); box-shadow: var(--pc-shadow); transition: box-shadow .3s ease; }
.pc-card:hover { box-shadow: var(--pc-shadow-lg); }
.pc-card-body { padding: 1.5rem; }

.pc-tbl-wrap { overflow-x: auto; border: 1px solid var(--pc-border); border-radius: var(--pc-radius-sm); }
.pc-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .82rem; }
.pc-tbl thead th { background: #f8fafc; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--pc-text-muted); padding: .55rem .7rem; border-bottom: 2px solid var(--pc-border); text-align: left; white-space: nowrap; }
.pc-tbl tbody td { padding: .5rem .7rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; }
.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }
.pc-tbl .pc-inv { font-weight: 700; color: var(--pc-text); white-space: nowrap; }
.pc-tbl .pc-date { color: var(--pc-text-sec); white-space: nowrap; }
.pc-tbl .pc-vendor { font-weight: 600; color: var(--pc-text); }
.pc-tbl .pc-wh { color: var(--pc-text-sec); }
.pc-tbl .pc-prod-name { font-size: .78rem; line-height: 1.3; }
.pc-tbl .pc-qty { font-weight: 700; text-align: center; }
.pc-tbl .pc-amount { font-weight: 700; white-space: nowrap; }
.pc-tbl .pc-net { font-weight: 800; color: var(--pc-accent); white-space: nowrap; }

.pc-act {
  display: inline-flex; align-items: center; gap: 3px; border-radius: 5px;
  padding: .3rem .7rem; font-size: .72rem; font-weight: 600; transition: all .2s ease;
  text-decoration: none; border: 1.5px solid transparent;
}
.pc-act-inv { background: #fef2f2; border-color: #f5d0d0; color: #991b1b; }
.pc-act-inv:hover { background: #fde8e8; color: #7f1d1d; }

.pc-empty { text-align: center; padding: 2.5rem .85rem; color: var(--pc-text-muted); }
.pc-empty i { font-size: 2rem; color: #ced8e6; display: block; margin-bottom: .5rem; }
.pc-empty span { font-size: .9rem; font-weight: 500; }

@media (max-width: 768px) { .pc-hdr { padding: 1rem 1.25rem; flex-direction: column; align-items: stretch; gap: .5rem; } .pc-hdr h2 { font-size: 1.1rem; } .pc-card-body { padding: 1rem; } }

/* ═══════ FULL MOBILE RESPONSIVE — CARD LAYOUT ═══════ */
@media (max-width: 767.98px) {
  body, html { overflow-x: hidden !important; }
  .pc-page { overflow-x: hidden !important; }

  .pc-hdr { padding: .75rem .85rem !important; flex-direction: column; align-items: stretch; gap: .5rem; }
  .pc-hdr h2 { font-size: .95rem !important; }
  .pc-card-body { padding: .6rem !important; overflow: hidden !important; }

  /* Kill ALL scroll wrappers — higher specificity */
  .pc-page .pc-tbl-wrap,
  .pc-page .dataTables_wrapper,
  .pc-page .dataTables_scrollBody,
  .pc-page .dataTables_scrollHead,
  .pc-page .dataTables_scrollFoot,
  .pc-page .table-responsive,
  .pc-page > .container-fluid > .pc-card > .pc-card-body > div {
    overflow: visible !important;
    overflow-x: visible !important;
    border: none !important;
    background: transparent !important;
    max-width: 100% !important;
  }

  .pc-page .pc-tbl,
  .pc-page .pc-tbl.dataTable {
    display: block !important;
    width: 100% !important;
    font-size: .72rem !important;
  }
  .pc-page .pc-tbl thead { display: none !important; }
  .pc-page .pc-tbl tbody { display: block !important; }
  .pc-page .pc-tbl tbody tr {
    display: grid !important;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .55rem;
    align-items: start;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 6px rgba(0,0,0,.04);
    padding: .5rem .6rem;
    margin-bottom: .5rem;
    overflow: hidden !important;
  }
  .pc-page .pc-tbl tbody tr:hover { background: var(--pc-surface); }
  .pc-page .pc-tbl tbody td {
    display: flex !important;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 2px 4px;
    border: none !important;
    padding: 0 !important;
    min-width: 0;
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: anywhere;
    font-size: .72rem;
    line-height: 1.35;
  }
  .pc-page .pc-tbl tbody td::before {
    content: attr(data-label) ":";
    font-size: .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--pc-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .pc-page .pc-tbl tbody td:nth-child(1) { order: 0; justify-self: start; }
  .pc-page .pc-tbl tbody td:nth-child(1)::before { content: none; }
  .pc-page .pc-tbl tbody td:nth-child(1) {
    background: #f0f4fe; color: #3b5bb3 !important; border-radius: 6px;
    padding: .05rem .4rem !important; font-weight: 700 !important; font-size: .62rem;
  }
  .pc-page .pc-tbl tbody td:nth-child(2) { order: 1; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(2)::before { content: none; }
  .pc-page .pc-tbl tbody td:nth-child(2) { font-weight: 600; font-size: .8rem; padding-top: 2px; }
  .pc-page .pc-tbl tbody td:nth-child(3) { order: 2; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(3)::before { content: none; }
  .pc-page .pc-tbl tbody td:nth-child(3) { font-weight: 600; font-size: .8rem; }
  .pc-page .pc-tbl tbody td:nth-child(4) { order: 3; }
  .pc-page .pc-tbl tbody td:nth-child(5) { order: 4; }
  .pc-page .pc-tbl tbody td:nth-child(6) { order: 5; }
  .pc-page .pc-tbl tbody td:nth-child(7) { order: 6; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(8) { order: 7; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(9) { order: 8; }
  .pc-page .pc-tbl tbody td:nth-child(10) { order: 9; }
  .pc-page .pc-tbl tbody td:nth-child(11) { order: 10; }
  .pc-page .pc-tbl tbody td:nth-child(12) { order: 11; grid-column: 1 / -1; font-size: .82rem !important; }
  .pc-page .pc-tbl tbody td:nth-child(13) { order: 12; grid-column: 1 / -1; }
  .pc-page .pc-tbl tbody td:nth-child(13)::before { content: none; }

  .pc-page .dataTables_filter, .pc-page .dataTables_length {
    width: 100% !important; text-align: left !important; margin-bottom: .5rem;
  }
  .pc-page .dataTables_filter input, .pc-page .dataTables_length select {
    width: 100% !important; min-height: 36px; font-size: .78rem; border-radius: 8px;
    padding: .3rem .6rem; border: 1.5px solid var(--pc-border); margin-top: 4px;
  }
  .pc-page .dataTables_info { font-size: .68rem !important; text-align: center !important; padding: .5rem 0 0; }
  .pc-page .dataTables_paginate { text-align: center !important; padding: .5rem 0; }
  .pc-page .dataTables_paginate .paginate_button {
    display: inline-flex !important; min-width: 28px !important; height: 28px !important;
    padding: 0 .35rem !important; font-size: .68rem !important; margin: 0 1px !important;
    border-radius: 6px !important;
  }
}
</style>

<div class="pc-page">
<div class="container-fluid px-3 px-md-4 py-3">

  <div class="pc-hdr">
    <div class="d-flex align-items-center gap-3">
      <h2><i class="bi bi-arrow-return-left"></i>Purchase Returns</h2>
      <span class="hdr-badge d-none d-sm-inline">{{ count($returns) }} Records</span>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:var(--pc-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
    <strong><i class="bi bi-check-circle me-1"></i></strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="pc-card">
    <div class="pc-card-body">
      <div class="pc-tbl-wrap">
        <table id="purchasereturn-table" class="pc-tbl">
          <thead>
            <tr>
              <th>ID</th><th>Purchase Invoice</th><th>Return Invoice</th><th>Vendor</th>
              <th>Warehouse</th><th>Return Date</th><th>Products</th><th style="width:50px;">Qty</th>
              <th class="pc-amount">Bill Amt</th><th class="pc-amount">Item Disc</th><th class="pc-amount">Extra Disc</th>
              <th class="pc-amount">Net Amount</th><th style="width:90px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($returns as $return)
            <tr>
              <td style="color:var(--pc-text-muted);font-weight:600;" data-label="ID">{{ $return->id }}</td>
              <td class="pc-inv" data-label="Purchase Inv">{{ $return->purchase->invoice_no ?? 'N/A' }}</td>
              <td class="pc-inv" data-label="Return Inv">{{ $return->return_invoice }}</td>
              <td class="pc-vendor" data-label="Vendor">{{ $return->vendor->name ?? 'N/A' }}</td>
              <td class="pc-wh" data-label="Warehouse">{{ $return->warehouse->warehouse_name ?? 'N/A' }}</td>
              <td class="pc-date" data-label="Date">{{ \Carbon\Carbon::parse($return->return_date)->format('d-M-Y') }}</td>
              <td data-label="Products">
                @foreach($return->items as $item)
                <div class="pc-prod-name">{{ $item->product->item_name ?? 'N/A' }}</div>
                @endforeach
              </td>
              <td class="pc-qty" data-label="Qty">
                @foreach($return->items as $item)
                <div>{{ $item->qty + 0 }}</div>
                @endforeach
              </td>
              <td class="pc-amount" data-label="Bill Amt">{{ number_format($return->bill_amount, 2) }}</td>
              <td class="pc-amount" data-label="Item Disc">{{ number_format($return->item_discount, 2) }}</td>
              <td class="pc-amount" data-label="Extra Disc">{{ number_format($return->extra_discount, 2) }}</td>
              <td class="pc-net" data-label="Net Amount">Rs {{ number_format($return->net_amount, 2) }}</td>
              <td data-label="Action">
                <a href="{{ route('purchasereturn.invoice', $return->id) }}" class="pc-act pc-act-inv"><i class="bi bi-file-text"></i>Invoice</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('#purchasereturn-table').DataTable({
      pageLength: 10, lengthMenu: [5, 10, 25, 50, 100],
      order: [[0, 'desc']],
      language: { search: "Search Returns:", lengthMenu: "Show _MENU_ entries" }
    });
  });
</script>
@endsection
