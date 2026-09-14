<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$productId = 83;

echo "=== TABLES CONTAINING SALE OR STOCK ===\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $t = array_values((array)$table)[0];
    if (str_contains($t, 'sale') || str_contains($t, 'stock') || str_contains($t, 'product') || str_contains($t, 'transfer') || str_contains($t, 'adjust')) {
        echo "- $t\n";
    }
}

echo "\n=== PRODUCT VARIANTS FOR PRODUCT 83 ===\n";
print_r(DB::table('product_variants')->where('product_id', $productId)->get());

echo "\n=== STOCKS FOR PRODUCT 83 ===\n";
print_r(DB::table('stocks')->where('product_id', $productId)->get());

if (Schema::hasTable('sale_details')) {
    echo "\n=== SALE DETAILS FOR PRODUCT 83 ===\n";
    print_r(DB::table('sale_details')->where('product_id', $productId)->get());
}

if (Schema::hasTable('warehouse_stocks')) {
    echo "\n=== WAREHOUSE STOCKS FOR PRODUCT 83 ===\n";
    print_r(DB::table('warehouse_stocks')->where('product_id', $productId)->get());
}
