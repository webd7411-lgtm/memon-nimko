<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFY REPORT VALUES AFTER MANUAL FIX ===\n\n";

// Check the current stock records after manual fix
$stocks = DB::table('stocks')
    ->whereIn('product_id', [83, 227])
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->get();

echo "Shop stock (warehouse=NULL) for products 83, 227:\n";
foreach ($stocks as $s) {
    echo "  Product:{$s->product_id} ID:{$s->id} Variant:" . ($s->variant_id ?? 'NULL') . " Qty:{$s->qty}\n";
}

echo "\n=== SIMULATE REPORT CALCULATION ===\n";

// For Product 227: Post-reset purchase of 20 PC, no other transactions
// The report should show:
// Initial = 0 (reset within period)
// Purchased = 20
// All other = 0
// Balance = 20 (stocks.qty for the variant record)

// Check stocks.qty for product 227 with variant=334
$qty227 = DB::table('stocks')
    ->where('product_id', 227)
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->where('variant_id', 334)
    ->value('qty');

echo "Product 227 (variant 334) qty: {$qty227}\n";

// For Product 83: Post-reset purchase of 20 KG (stored as 20000 grams)
// The report should show:
// Initial = 0
// Purchased = 20 (after KG conversion: 20000 / 1000 = 20)
// Balance = 20

// Check stocks.qty for product 83 with variant=NULL
$qty83 = DB::table('stocks')
    ->where('product_id', 83)
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->whereNull('variant_id')
    ->value('qty');

echo "Product 83 (base, grams) qty: {$qty83}\n";

// The report converts grams to KG: qty / 1000
$qty83_kg = ($qty83 ? $qty83 / 1000 : 0);
echo "Product 83 (converted to KG): {$qty83_kg}\n";

echo "\n=== EXPECTED REPORT RESULTS ===\n";
echo "Product 83 (2 in 1 Biscuits):\n";
echo "  Initial: 0\n";
echo "  Purchased: 20 KG\n";
echo "  Stock Qty: 20 KG (qty={$qty83} grams / 1000 = {$qty83_kg} KG)\n";

echo "\nProduct 227 (2 PC BUN):\n";
echo "  Initial: 0\n";
echo "  Purchased: 20 PC\n";
echo "  Stock Qty: 20 PC (qty={$qty227})\n";

// Test new sale of 5 units for product 227
echo "\n=== TEST NEW SALE (5 UNITS) ===\n";
// After sale of 5, stock should be 15
// But let's just verify the logic, not create a real sale
$expectedAfterSale = $qty227 - 5;
echo "After sale of 5: Expected qty = {$expectedAfterSale}\n";

echo "\n=== ALL CHECKS COMPLETE ===\n";
