<?php
// Check entry 11 items
$items = DB::table('production_entry_items')->where('production_entry_id', 11)->get();
echo 'Entry 11 items: ' . count($items) . PHP_EOL;
foreach ($items as $it) {
    echo '  product_id=' . $it->product_id . ' variant_id=' . ($it->variant_id ?? 'NULL') . ' qty=' . $it->qty_entered . PHP_EOL;
}
// Check entry 12 items
$items12 = DB::table('production_entry_items')->where('production_entry_id', 12)->get();
echo PHP_EOL . 'Entry 12 items: ' . count($items12) . PHP_EOL;
foreach ($items12 as $it) {
    echo '  product_id=' . $it->product_id . ' variant_id=' . ($it->variant_id ?? 'NULL') . ' qty=' . $it->qty_entered . PHP_EOL;
}
