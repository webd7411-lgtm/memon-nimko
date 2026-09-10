<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SIMULATE ACTUAL REPORT FOR PRODUCT 83 AND 227 ===\n\n";

// Replicate the report logic for these two products
$productIds = [83, 227];
$startDate = '2026-09-10';
$endDate = '2026-09-10';
$startDT = $startDate . ' 00:00:00';
$endDT = $endDate . ' 23:59:59';
$resetTime = trim(file_get_contents('storage/app/stock_reset_timestamp_1.txt'));

// 1. Get products
$products = DB::table('products')->whereIn('id', $productIds)->get();

// 2. Current stocks (shop: warehouse_id = NULL)
$currStocks = DB::table('stocks')
    ->whereIn('product_id', $productIds)
    ->whereNull('warehouse_id')
    ->where('branch_id', 1)
    ->select('product_id', 'variant_id', 'qty')
    ->get();
$mapS = [];
foreach ($currStocks as $cs) {
    $key = $cs->product_id . '_' . ($cs->variant_id ?? 0);
    $mapS[$key] = ($mapS[$key] ?? 0) + $cs->qty;
}

echo "Current stock map (shop):\n";
foreach ($mapS as $k => $v) echo "  {$k}: {$v}\n";

// 3. Purchases (shop: warehouse_id = NULL)
$purchases = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->whereIn('purchase_items.product_id', $productIds)
    ->whereNull('purchases.warehouse_id')
    ->where('purchases.branch_id', 1)
    ->where('purchases.created_at', '>=', $resetTime)
    ->where('purchases.purchase_date', '>=', $startDate)
    ->where('purchases.purchase_date', '<=', $endDate)
    ->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
    ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')
    ->get();

$mapP = [];
foreach ($purchases as $p) {
    $key = $p->product_id . '_' . ($p->variant_id ?? 0);
    $mapP[$key] = $p->total_qty;
    echo "Purchase: {$key} = {$p->total_qty}\n";
}

// 4. Check the actual report output
$rows = [];
foreach ($products as $p) {
    $is_kg = $p->unit_type === 'kg';
    $key = $p->id . '_0';
    
    $balance = (float)($mapS[$key] ?? 0);
    
    // For KG products with variants, accumulate variant-level stock
    if ($is_kg && DB::table('product_variants')->where('product_id', $p->id)->count() > 0) {
        $variants = DB::table('product_variants')->where('product_id', $p->id)->get();
        $balanceGrams = 0;
        $purchasedGrams = 0;
        foreach ($variants as $v) {
            $vKey = $p->id . '_' . $v->id;
            $vStock = (float)($mapS[$vKey] ?? 0);
            $vPurch = (float)($mapP[$vKey] ?? 0);
            $balanceGrams += $vStock;
            $purchasedGrams += $vPurch * 1000; // Purchase qty is in KG, convert for comparison
        }
        // Actually, let's just show the base-level values
        $qtyKG = ($mapS[$key] ?? 0) / 1000;
        $purchKG = 0;
        // Find purchase for this KG product
        $purchRow = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where('purchase_items.product_id', $p->id)
            ->whereNull('purchases.warehouse_id')
            ->where('purchases.branch_id', 1)
            ->where('purchases.created_at', '>=', $resetTime)
            ->first();
        
        if ($purchRow) {
            $isKg = strtolower(DB::table('products')->where('id', $p->id)->value('unit_type') ?? '') === 'kg';
            $qtyStock = $isKg ? ($purchRow->qty * 1000) : $purchRow->qty;
            $purchKG = $purchRow->qty; // Show in KG (original qty)
        }
        
        $rows[] = [
            'product_id' => $p->id,
            'name' => $p->item_name,
            'initial_stock' => 0,
            'purchased' => $purchKG,
            'produced' => 0,
            'purchase_return' => 0,
            'transfer' => 0,
            'transfer_in' => 0,
            'adj_increase' => 0,
            'adj_decrease' => 0,
            'sold' => 0,
            'sale_return' => 0,
            'balance' => $qtyKG,
            'unit' => 'KG'
        ];
    } else {
        // Non-KG product
        $qty = $mapS[$key] ?? 0;
        $purchQty = $mapP[$key] ?? 0;
        
        $rows[] = [
            'product_id' => $p->id,
            'name' => $p->item_name,
            'initial_stock' => 0,
            'purchased' => $purchQty,
            'produced' => 0,
            'purchase_return' => 0,
            'transfer' => 0,
            'transfer_in' => 0,
            'adj_increase' => 0,
            'adj_decrease' => 0,
            'sold' => 0,
            'sale_return' => 0,
            'balance' => $qty,
            'unit' => 'PC'
        ];
    }
}

echo "=== REPORT OUTPUT FOR PRODUCTS 83 AND 227 ===\n";
foreach ($rows as $row) {
    echo "\nProduct: {$row['name']} (ID: {$row['product_id']})\n";
    echo "  Initial: {$row['initial_stock']} {$row['unit']}\n";
    echo "  Produced: {$row['produced']} {$row['unit']}\n";
    echo "  Purchased: {$row['purchased']} {$row['unit']}\n";
    echo "  Purchase Return: {$row['purchase_return']} {$row['unit']}\n";
    echo "  Transfer Out: {$row['transfer']} {$row['unit']}\n";
    echo "  Transfer In: {$row['transfer_in']} {$row['unit']}\n";
    echo "  ADJ+: {$row['adj_increase']} {$row['unit']}\n";
    echo "  ADJ-: {$row['adj_decrease']} {$row['unit']}\n";
    echo "  Sold: {$row['sold']} {$row['unit']}\n";
    echo "  Sale Return: {$row['sale_return']} {$row['unit']}\n";
    echo "  Balance (Stock Qty): {$row['balance']} {$row['unit']}\n";
    
    $expected = $row['initial_stock'] + $row['purchased'] + $row['produced'] + $row['transfer_in'] + $row['adj_increase'] + $row['sale_return'] - $row['purchase_return'] - $row['transfer'] - $row['adj_decrease'] - $row['sold'];
    echo "  Formula verification: 0 + {$row['purchased']} = {$expected} => " . ($expected == $row['balance'] ? 'PASS' : 'FAIL') . "\n";
}
