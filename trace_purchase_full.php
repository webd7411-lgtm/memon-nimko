<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TRACE PURCHASE PUR-048 (OR LATEST PURCHASE) ===\n\n";

// Check the latest purchase
$purchase = DB::table('purchases')
    ->where('branch_id', 1)
    ->orderBy('id', 'desc')
    ->first();

echo "Latest Purchase ID: " . ($purchase ? $purchase->id : 'NONE') . "\n";
if ($purchase) {
    echo "  branch_id: {$purchase->branch_id}\n";
    $wVal = ($purchase->warehouse_id === null || $purchase->warehouse_id === '') ? 'NULL' : $purchase->warehouse_id;
    echo "  warehouse_id: " . $wVal . "\n";
    echo "  purchase_to: \n";  // We need to check purchase_items for details
}

// Check purchase items for the latest purchase
if ($purchase) {
    $items = DB::table('purchase_items')
        ->where('purchase_id', $purchase->id)
        ->get();
    echo "\nPurchase Items (ID:{$purchase->id}):\n";
    foreach ($items as $item) {
        $vVal = ($item->variant_id === null || $item->variant_id === '') ? 'NULL' : $item->variant_id;
        echo "  product_id:{$item->product_id} qty:{$item->qty} variant_id:" . $vVal . "\n";
    }
}

// Check all purchase items for products 83 and 227
$allItems = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->whereIn('purchase_items.product_id', [83, 227])
    ->where('purchases.branch_id', 1)
    ->select('purchase_items.purchase_id', 'purchase_items.product_id', 'purchase_items.qty', 'purchase_items.variant_id', 'purchases.branch_id', 'purchases.warehouse_id', 'purchases.created_at')
    ->get();

echo "\nAll purchases for products 83 and 227:\n";
foreach ($allItems as $item) {
    $vStr = ($item->variant_id === null || $item->variant_id === '') ? 'NULL' : $item->variant_id;
    $wStr = ($item->warehouse_id === null || $item->warehouse_id === '') ? 'NULL' : $item->warehouse_id;
    echo "  PurchaseID:{$item->purchase_id} Product:{$item->product_id} Qty:{$item->qty} Variant:{$vStr} Branch:{$item->branch_id} Warehouse:{$wStr} Date:{$item->created_at}\n";
}

// Check the actual stock state after any purchases
$stocks = DB::table('stocks')
    ->whereIn('product_id', [83, 227])
    ->where('branch_id', 1)
    ->get();
echo "\nCurrent stock for products 83 and 227 (branch 1):\n";
foreach ($stocks as $s) {
    $wStr = ($s->warehouse_id === null || $s->warehouse_id === '') ? 'NULL' : $s->warehouse_id;
    $vStr2 = ($s->variant_id === null || $s->variant_id === '') ? 'NULL' : $s->variant_id;
    echo "  Product:{$s->product_id} ID:{$s->id} Branch:{$s->branch_id} Warehouse:" . $wStr . " Variant:" . $vStr2 . " Qty:{$s->qty}\n";
}

// Check if purchase stock query would find these
$pid83 = 83;
$pid227 = 227;

// For shop purchase, the query is:
// Stock::where('branch_id', 1)->where('product_id', $pid)->where('variant_id', $variantId)->first()

// Check what variant_id the purchase form sends for products 83 and 227
// Look at purchase_items for a purchase that includes them
$purchaseWith83 = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->where('purchase_items.product_id', 83)
    ->where('purchases.branch_id', 1)
    ->first();

echo "\nSample purchase item for product 83:\n";
if ($purchaseWith83) {
    $vVal = ($purchaseWith83->variant_id === null || $purchaseWith83->variant_id === '') ? 'NULL' : $purchaseWith83->variant_id;
    $vLen = (strlen($purchaseWith83->variant_id ?? '') == 0) ? 0 : strlen($purchaseWith83->variant_id);
    echo "  variant_id value: '" . $vVal . "' (length: " . $vLen . ")\n";
    echo "  This could be empty string '' or NULL\n";
}

// Now test the query with different variant_id values
echo "\n=== QUERY TEST FOR PRODUCT 83 ===\n";
$tests = [
    ['variant_id' => null],
    ['variant_id' => ''],
    ['variant_id' => '0'],
];

foreach ($tests as $test) {
    $vid = $test['variant_id'];
    $queryDesc = $vid === null ? 'IS NULL' : (strlen($vid) == 0 ? "= '' (empty string)" : "= '$vid'");
    
    $query = DB::table('stocks')
        ->where('branch_id', 1)
        ->where('product_id', 83);
    
    if ($vid === null) {
        $query->whereNull('variant_id');
    } else {
        $query->where('variant_id', $vid);
    }
    
    $record = $query->first();
    echo "  Query variant_id {$queryDesc}: Found: " . ($record ? "ID:{$record->id} qty:{$record->qty}" : 'NONE') . "\n";
}

echo "\n=== KEY ISSUE ===\n";
if ($purchaseWith83 && $purchaseWith83->variant_id === '') {
    echo "The purchase form sends variant_id = '' (empty string), not NULL!\n";
    echo "The stock query uses 'where(variant_id, )' which looks for empty string.\n";
    echo "But stocks table stores variant_id = NULL for products without variants.\n";
    echo "So the query does NOT find the stock record!\n";
    echo "Then it creates a NEW record with variant_id = '', but the report reads NULL.\n";
} else {
    $pv = ($purchaseWith83 ? (($purchaseWith83->variant_id === null || $purchaseWith83->variant_id === '') ? 'NULL' : $purchaseWith83->variant_id) : 'N/A');
    echo "Variant value: '" . $pv . "'\n";
}
