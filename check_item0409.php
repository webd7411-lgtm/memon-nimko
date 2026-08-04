<?php
$p = DB::table('products')->where('item_code', 'item-0409')->first();
echo "Product: " . $p->item_name . " (id=" . $p->id . ", unit_type=" . $p->unit_type . ")\n";

// Variants
$variants = DB::table('product_variants')->where('product_id', $p->id)->get();
echo "Variants: " . count($variants) . "\n";
foreach ($variants as $v) {
    echo "  id=" . $v->id . " name=" . $v->variant_name . " size=" . $v->size_value . $v->size_unit . " default=" . $v->is_default . " stock_qty=" . $v->stock_qty . "\n";
}

// Stocks
$stocks = DB::table('stocks')->where('product_id', $p->id)->get();
echo "Stocks table:\n";
foreach ($stocks as $s) {
    echo "  variant_id=" . ($s->variant_id ?? 'NULL') . " qty=" . $s->qty . " branch=" . $s->branch_id . " warehouse=" . ($s->warehouse_id ?? 'NULL') . "\n";
}

// Purchases
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
$purchases = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->where('purchase_items.product_id', $p->id)
    ->whereBetween('purchases.purchase_date', [$startDate, $endDate])
    ->select('purchase_items.*', 'purchases.purchase_date')
    ->get();
echo "Purchases this month: " . count($purchases) . "\n";
foreach ($purchases as $pr) {
    echo "  date=" . $pr->purchase_date . " qty=" . $pr->qty . " variant_id=" . ($pr->variant_id ?? 'NULL') . "\n";
}

// Productions
$productions = DB::table('production_entry_items')
    ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
    ->where('production_entry_items.product_id', $p->id)
    ->whereDate('production_entries.production_date', '>=', $startDate)
    ->whereDate('production_entries.production_date', '<=', $endDate)
    ->select('production_entry_items.*', 'production_entries.production_date')
    ->get();
echo "Productions this month: " . count($productions) . "\n";
foreach ($productions as $pd) {
    echo "  date=" . $pd->production_date . " qty_entered=" . $pd->qty_entered . " qty_stock=" . $pd->qty_stock . " variant_id=" . ($pd->variant_id ?? 'NULL') . "\n";
}

// Check if there are after-period transactions
$today = date('Y-m-d');
if ($endDate < $today) {
    $purchAfter = DB::table('purchase_items')
        ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
        ->where('purchase_items.product_id', $p->id)
        ->where('purchases.purchase_date', '>', $endDate)
        ->sum('purchase_items.qty');
    echo "Purchases after end date: $purchAfter\n";

    $prodAfter = DB::table('production_entry_items')
        ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
        ->where('production_entry_items.product_id', $p->id)
        ->whereDate('production_entries.production_date', '>', $endDate)
        ->sum('production_entry_items.qty_stock');
    echo "Productions after end date: $prodAfter\n";
}
