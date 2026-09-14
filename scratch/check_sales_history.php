<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productId = 83;

echo "=== SALES FOR PRODUCT 83 ===\n";
$sales = DB::table('sales')
    ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
    ->where('sale_details.product_id', $productId)
    ->select('sales.id as sale_id', 'sales.created_at', 'sale_details.*')
    ->get();

print_r($sales);

echo "=== STOCK MOVEMENTS FOR PRODUCT 83 ===\n";
if (DB::getSchemaBuilder()->hasTable('stock_movements')) {
    print_r(DB::table('stock_movements')->where('product_id', $productId)->get());
}
