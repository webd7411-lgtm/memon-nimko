<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Closing Report - {{ $dateLabel }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; }

@media print {
  body { margin: 0; padding: 0; }
  .no-print { display: none !important; }
  @page { size: 80mm auto; margin: 2mm; }
}

body {
  font-family: 'Arial', sans-serif;
  font-size: 10px;
  color: #000 !important;
  background: #fff;
  margin: 0;
  padding: 0;
}

.receipt {
  width: 100%;
  max-width: 285px;
  margin: 0 auto;
  padding: 6px 4px;
}

.center { text-align: center; }
.bold { font-weight: 800 !important; }
.line { border-top: 1px dashed #000; margin: 4px 0; }
.dbl-line { border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; height: 3px; margin: 4px 0; }

.brand { font-size: 15px; font-weight: 900; margin-bottom: 1px; text-transform: uppercase; letter-spacing: 0.5px; }
.address { font-size: 9px; font-weight: 700; line-height: 1.2; }
.title { font-size: 12px; font-weight: 900; margin: 4px 0; text-transform: uppercase; letter-spacing: 0.5px; }

table { width: 100%; border-collapse: collapse; margin: 4px 0; }
th {
  text-align: left; font-size: 9px; font-weight: 800;
  border-bottom: 1.5px solid #000; padding: 3px 1px;
}
td {
  font-size: 9px; font-weight: 700;
  padding: 3px 1px; vertical-align: top;
  border-bottom: 1px dotted #bbb;
}
table tbody tr:last-child td { border-bottom: none; }
.r { text-align: right; }
.c { text-align: center; }

.info-row { display: flex; justify-content: space-between; font-size: 9px; margin: 2px 0; font-weight: 700; }
.badge-tag {
  display: inline-block;
  background: #000;
  color: #fff;
  font-size: 8px;
  font-weight: 800;
  padding: 1px 5px;
  border-radius: 3px;
  margin: 3px 0;
  text-transform: uppercase;
}

.print-btn {
  display: block; margin: 10px auto; padding: 8px 20px;
  background: #000; color: #fff; border: none; border-radius: 4px;
  font-size: 12px; font-weight: 700; cursor: pointer; text-transform: uppercase;
}

.item-name { font-size: 9px; font-weight: 800; line-height: 1.15; word-break: break-word; }
.item-code { font-size: 8px; font-weight: 700; color: #333; }

.sig-area {
  display: flex;
  justify-content: space-between;
  margin-top: 28px;
  font-size: 9px;
  font-weight: 800;
  padding: 0 4px;
}
.sig-box {
  text-align: center;
  width: 42%;
  border-top: 1px dashed #000;
  padding-top: 4px;
}
</style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">🖨️ Print Closing</button>

<div class="receipt">
  <div class="center">
    @php
        $currentBranch = isset($branchId) && $branchId 
            ? \App\Models\Branch::find($branchId) 
            : (\App\Models\Branch::find(active_branch_id()) ?? \App\Models\Branch::first());

        $bAddress = !empty($currentBranch?->address) ? $currentBranch->address : '';
        $rawPhone = !empty($currentBranch?->number) ? $currentBranch->number : (!empty($currentBranch?->phone) ? $currentBranch->phone : '');
        $bPhone = preg_replace('/^Phone:\s*/i', '', $rawPhone);
    @endphp
    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 50px; margin-bottom: 3px;">
    <div class="brand">{{ shop_name() }}</div>
    <div class="address" style="font-size:11px; font-weight:900;">{{ shop_tagline() }}</div>
    @if(!empty($bAddress))
    <div class="address">{{ $bAddress }}</div>
    @endif
    @if(!empty($bPhone))
    <div class="address">Ph: {{ $bPhone }}</div>
    @endif
  </div>

  <div class="dbl-line" style="margin-top:6px;"></div>
  <div class="center title">Daily Stock Closing</div>
  @if(!empty($onlyMovement))
  <div class="center"><span class="badge-tag">⚡ Active Movement Only (+ / −)</span></div>
  @endif
  <div class="dbl-line" style="margin-bottom:6px;"></div>

  <div class="info-row"><span>Branch:</span><span class="bold">{{ $branchName ?? 'Main Branch' }}</span></div>
  <div class="info-row"><span>Date:</span><span>{{ $dateLabel }}</span></div>
  <div class="info-row"><span>Shift:</span><span>{{ $timeLabel }}</span></div>
  <div class="info-row"><span>Cashier / User:</span><span>{{ $printedBy ?? 'Admin' }}</span></div>
  <div class="info-row"><span>Printed At:</span><span>{{ \Carbon\Carbon::now()->format('d-M-Y h:i A') }}</span></div>

  <div class="line"></div>

  <table>
    <thead>
      <tr>
        <th style="width:34%;">Item</th>
        <th style="width:16%;" class="r">Open</th>
        <th style="width:17%;" class="r">In(+)</th>
        <th style="width:16%;" class="r">Sold</th>
        <th style="width:17%;" class="r">Close</th>
      </tr>
    </thead>
    <tbody>
      @php
        $totOpen = 0; $totIn = 0; $totSold = 0; $totBal = 0;
      @endphp
      @forelse($rows as $r)
      @php
        $r = (object)$r;
        $isKg = !empty($r->is_kg);
        
        $initial = (float)($r->initial_stock ?? 0);
        $prod    = (float)($r->produced ?? 0);
        $purch   = (float)($r->purchased ?? 0);
        $trIn    = (float)($r->transfer_in ?? 0);
        $sRet    = (float)($r->sale_return ?? 0);
        $adjInc  = (float)($r->adj_increase ?? 0);
        $inward  = $prod + $purch + $trIn + $sRet + $adjInc;

        $sold    = (float)($r->sold ?? 0);
        $bal     = (float)($r->balance ?? 0);

        $effOpen = $isKg ? ($initial / 1000) : $initial;
        $effIn   = $isKg ? ($inward / 1000) : $inward;
        $effSold = $isKg ? ($sold / 1000) : $sold;
        $effBal  = $isKg ? ($bal / 1000) : $bal;

        $totOpen += $effOpen;
        $totIn   += $effIn;
        $totSold += $effSold;
        $totBal  += $effBal;

        $fmt = function($val) use ($isKg) {
            if ($isKg) {
                $g = abs($val);
                if ($g >= 1000) {
                    $k = floor($g / 1000);
                    $m = round($g % 1000);
                    return ($val < 0 ? '-' : '') . ($m > 0 ? number_format($g / 1000, 2) : $k) . 'k';
                } elseif ($g > 0) {
                    return ($val < 0 ? '-' : '') . round($g) . 'g';
                }
                return '0';
            }
            return (float)$val == floor($val) ? number_format($val, 0) : number_format($val, 2);
        };
      @endphp
      <tr>
        <td>
          <div class="item-name">{{ $r->item_name }}</div>
          <span class="item-code">[{{ $r->item_code }}]</span>
        </td>
        <td class="r">{{ $fmt($initial) }}</td>
        <td class="r" style="color:#0f766e;">{{ $fmt($inward) }}</td>
        <td class="r" style="color:#8e44ad;">{{ $fmt($sold) }}</td>
        <td class="r bold" style="{{ $bal <= 0 ? 'color:#ef4444;' : '' }}">{{ $fmt($bal) }}</td>
      </tr>
      @empty
      <tr><td colspan="5" class="center" style="padding:10px 0;">No stock records found for this period</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="line"></div>
  <div style="display:flex; justify-content:space-between; font-weight:900; font-size:9px; padding:3px 1px;">
    <span style="width:34%;">TOTAL</span>
    <span style="width:16%; text-align:right;">{{ (float)$totOpen == floor($totOpen) ? number_format($totOpen, 0) : number_format($totOpen, 2) }}</span>
    <span style="width:17%; text-align:right; color:#0f766e;">{{ (float)$totIn == floor($totIn) ? number_format($totIn, 0) : number_format($totIn, 2) }}</span>
    <span style="width:16%; text-align:right; color:#8e44ad;">{{ (float)$totSold == floor($totSold) ? number_format($totSold, 0) : number_format($totSold, 2) }}</span>
    <span style="width:17%; text-align:right;">{{ (float)$totBal == floor($totBal) ? number_format($totBal, 0) : number_format($totBal, 2) }}</span>
  </div>
  <div class="line" style="margin-bottom:6px;"></div>

  <div class="info-row"><span>Total Items:</span><span class="bold">{{ $total }}</span></div>
  @if(!empty($grandTotal))
  <div class="info-row"><span>Stock Value (Rs):</span><span class="bold">Rs {{ number_format($grandTotal, 0) }}</span></div>
  @endif

  <div class="sig-area">
    <div class="sig-box">CASHIER / PREPARED</div>
    <div class="sig-box">MANAGER / VERIFIED</div>
  </div>

  <div class="center" style="font-size:8px; margin-top:8px; color:#777;">
    Develop By: <strong>{{ developer_name() }}</strong>
  </div>
  <div class="center bold" style="font-size:8px; margin-top:4px; color:#555;">
    — End of Closing Report —
  </div>
</div>

<script>
  window.onload = function() {
    if (!window.matchMedia('print').matches) {
      window.print();
    }
  };
</script>
</body>
</html>
