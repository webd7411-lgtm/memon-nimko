<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

foreach (['sale_items', 'sale_details', 'sales_items', 'sales_details'] as $t) {
    echo "$t: " . (Schema::hasTable($t) ? "EXISTS" : "NO") . "\n";
}

$columns = Schema::getColumnListing('sales');
print_r($columns);

$sale = DB::table('sales')->latest()->first();
print_r($sale);
