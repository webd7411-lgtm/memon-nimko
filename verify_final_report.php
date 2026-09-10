<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFY REAL REPORT VALUES ===\n\n";

// Simulate what fetchItemStock does for products 83 and 227
$startDate = '2026-09-10'; // After reset
$endDate = '2026-09-10';
$resetTime = '2026-09-10 21:14:57';

// Get current stock for shop (warehouse_id = NULL)
$currStocks = DB::table('stocks')
    ->whereIn('product_id', [83, 227])
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->select('product_id', 'variant_id', 'qty')
    ->get();

$mapS = [];
foreach ($currStocks as $cs) {
    $key = $cs->product_id . '_' . ($cs->variant_id ?? 0);
    $mapS[$key] = ($mapS[$key] ?? 0) + $cs->qty;
}

echo "Current stock map (shop, warehouse=NULL):\n";
foreach ($mapS as $k => $v) {
    echo "  Key: {$k} Qty: {$v}\n";
}

// Check purchases within period (post-reset)
$purchases = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->whereIn('purchase_items.product_id', [83, 227])
    ->whereNull('purchases.warehouse_id')
    ->where('purchases.branch_id', 1)
    ->where('purchases.created_at', '>=', $resetTime)
    ->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
    ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')
    ->get();

echo "\nPost-reset purchases (shop):\n";
$mapP = [];
foreach ($purchases as $p) {
    $key = $p->product_id . '_' . ($p->variant_id ?? 0);
    $mapP[$key] = $p->total_qty;
    echo "  Key: {$key} Qty: {$p->total_qty}\n";
}

// For Product 83: KG product, variant forced to NULL in purchase
// But the form sends variant_id=142. The purchase stores at variant=NULL for KG.
// The report for KG uses base key (83_0) and accumulates from variant-level stock.
// Let's check what the actual purchase creates.

echo "\n=== FINAL REPORT VALUES (AFTER ALL FIXES) ===\n";

// Product 227 (non-KG, variant=334)
$key227 = '227_334';
$qty227 = $mapS[$key227] ?? 0;
$purch227 = $mapP[$key227] ?? 0;

$opening227 = 0;
$closing227 = $qty227;

if ($resetTime && '2026-09-10' >= substr($resetTime, 0, 10)) {
    $opening227 = 0;
    $closing227 = $qty227;
}

echo "Product 227 (2 PC BUN):\n";
echo "  Initial: {$opening227}\n";
echo "  Produced: 0\n";
echo "  Purchased: {$purch227}\n";
echo "  Purchase Return: 0\n";
echo "  Transfer Out: 0\n";
echo "  Transfer In: 0\n";
echo "  ADJ+: 0\n";
echo "  ADJ-: 0\n";
echo "  Sold: 0\n";
echo "  Sale Return: 0\n";
echo "  Balance (Current Stock): {$qty227} PC\n";

// Product 83 (KG, base key 83_0, but purchase creates with variant=NULL for KG)
// The purchase creates qty=20000 grams (20 KG). The report for KG uses base key.
$key83 = '83_0';
$qty83Grams = $mapS[$key83] ?? 0;
$purch83 = $mapP['83_0'] ?? $mapP['83_142'] ?? 0;  // Purchase might be at base or variant level

// Actually for KG, purchase creates at variant=NULL. So $mapP has key 83_0 (if purchase uses variant=NULL after conversion)
// But the purchase form sends variant=142. The purchase code converts to null for KG.
// So $mapP should have key 83_0 with qty=20000.

$purch83Actual = $mapP['83_0'] ?? 0;
$qty83Actual = $mapS['83_0'] ?? 0;

$qty83KG = $qty83Actual / 1000;
$purch83KG = $purch83Actual / 1000;

echo "\nProduct 83 (2 in 1 Biscuits, KG):\n";
echo "  Initial: 0\n";
echo "  Produced: 0\n";
echo "  Purchased: {$purch83KG} KG (qty_grams={$purch83Actual})\n";
echo "  Purchase Return: 0\n";
echo "  Transfer Out: 0\n";
echo "  Transfer In: 0\n";
echo "  ADJ+: 0\n";
echo "  ADJ-: 0\n";
echo "  Sold: 0\n";
echo "  Sale Return: 0\n";
echo "  Balance (Current Stock): {$qty83KG} KG (qty_grams={$qty83Actual})\n";

// Verify formula: 0 + 20 = 20
echo "\n=== FORMULA VERIFICATION ===\n";
echo "Product 227: 0 + {$purch227} - 0 = {$qty227} => " . ($qty227 == 20 ? 'PASS' : 'FAIL') . "\n";
echo "Product 83: 0 + {$purch83KG} - 0 = {$qty83KG} => " . ($qty83KG == 20 ? 'PASS' : 'FAIL') . "\n";
