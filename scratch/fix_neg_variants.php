<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$updated = DB::table('product_variants')->where('stock_qty', '<', 0)->update(['stock_qty' => 0]);
echo "Reset negative stock_qty for $updated variants in product_variants table.\n";
