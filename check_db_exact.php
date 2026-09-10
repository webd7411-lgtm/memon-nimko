<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ALL STOCK RECORDS FOR PRODUCT 83 ===\n";
$all83 = DB::table('stocks')->where('product_id', 83)->get();
foreach ($all83 as $s) {
    $wStr = ($s->warehouse_id === null || $s->warehouse_id === '') ? 'NULL' : $s->warehouse_id;
    $vStr = ($s->variant_id === null || $s->variant_id === '') ? 'NULL' : $s->variant_id;
    echo "  ID:{$s->id} Branch:{$s->branch_id} Warehouse:{$wStr} Variant:{$vStr} Qty:{$s->qty} Updated:{$s->updated_at}\n";
}

echo "\n=== ALL STOCK RECORDS FOR PRODUCT 227 ===\n";
$all227 = DB::table('stocks')->where('product_id', 227)->get();
foreach ($all227 as $s) {
    $wStr = ($s->warehouse_id === null || $s->warehouse_id === '') ? 'NULL' : $s->warehouse_id;
    $vStr = ($s->variant_id === null || $s->variant_id === '') ? 'NULL' : $s->variant_id;
    echo "  ID:{$s->id} Branch:{$s->branch_id} Warehouse:{$wStr} Variant:{$vStr} Qty:{$s->qty} Updated:{$s->updated_at}\n";
}

echo "\n=== CHECK PURCHASE 58 ITEMS ===\n";
$items58 = DB::table('purchase_items')->where('purchase_id', 58)->get();
foreach ($items58 as $i) {
    $vStr = ($i->variant_id === null || $i->variant_id === '') ? 'NULL' : $i->variant_id;
    echo "  Product:{$i->product_id} Qty:{$i->qty} Variant:{$vStr}\n";
}

echo "\n=== CHECK PURCHASE 58 STOCK EFFECT ===\n";
// Does the purchase update go to a different record?
// The purchase uses Stock::where('branch_id', 1)->where('product_id', 83)->where('variant_id', '142')->first()
$queryResult = DB::table('stocks')
    ->where('branch_id', 1)
    ->where('product_id', 83)
    ->where('variant_id', '142')
    ->first();
echo "Query for product 83, variant 142: " . ($queryResult ? "Found ID:{$queryResult->id} qty:{$queryResult->qty}" : 'NONE') . "\n";

$queryResult2 = DB::table('stocks')
    ->where('branch_id', 1)
    ->where('product_id', 227)
    ->where('variant_id', '334')
    ->first();
echo "Query for product 227, variant 334: " . ($queryResult2 ? "Found ID:{$queryResult2->id} qty:{$queryResult2->qty}" : 'NONE') . "\n";

echo "\n=== THE CORE BUG ===\n";
echo "Purchase creates/updates VARIANT-level stock (variant_id = 142 or 334)\n";
echo "But the report reads BASE-level stock (variant_id = NULL) for some cases\n";
echo "OR: The purchase updates the WRONG record (warehouse=1) because of missing warehouse filter\n";
echo "This creates a MISMATCH between purchase updates and report readings.\n";
