<?php
$p = DB::table('products')->where('item_code', 'item-0372')->first();
echo "=== Variant production (qty_stock) ===\n";
$prod = DB::table('production_entry_items')
    ->where('product_id', $p->id)
    ->selectRaw('variant_id, SUM(qty_stock) as total')
    ->groupBy('variant_id')
    ->get();
foreach ($prod as $pr) {
    echo "  variant_id=" . ($pr->variant_id ?? 'NULL') . " total=" . $pr->total . "\n";
}

echo "\n=== Stock summary ===\n";
$stocks = DB::table('stocks')->where('product_id', $p->id)->get();
foreach ($stocks as $s) {
    echo "  variant_id=" . ($s->variant_id ?? 'NULL') . " qty=" . $s->qty . "\n";
}

echo "\n=== Opening stock calculation ===\n";
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');

$soldVariant = DB::table('sales')
    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
    ->whereRaw("FIND_IN_SET(" . $p->id . ", product)")
    ->get();
$sold570 = 0;
foreach ($soldVariant as $s) {
    $pids = explode(',', $s->product);
    $vids = explode(',', $s->variant_id ?? '');
    $qtys = explode(',', $s->qty);
    foreach ($pids as $i => $pid) {
        if ((int)trim($pid) === $p->id && trim($vids[$i] ?? '0') === '570') {
            $sold570 += (float)($qtys[$i] ?? 0);
        }
    }
}
echo "Sold (variant 570) this month: $sold570\n";

$prod570 = DB::table('production_entry_items')
    ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
    ->where('production_entry_items.product_id', $p->id)
    ->where(function($q) { $q->where('production_entry_items.variant_id', 570)->orWhereNull('production_entry_items.variant_id'); })
    ->whereDate('production_entries.production_date', '>=', $startDate)
    ->whereDate('production_entries.production_date', '<=', $endDate)
    ->sum('production_entry_items.qty_stock');
echo "Produced (variant 570 + null) this month: $prod570\n";

$balance = -2879 + 3992;
$closing = $balance;
$opening = $closing - $prod570 + $sold570;
echo "Calculated opening: balance($balance) - produced($prod570) + sold($sold570) = $opening\n";
