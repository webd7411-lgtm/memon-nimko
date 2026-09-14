<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STOCK_TRANSFERS table - id, created_at, transfer_date ===\n";
$cols = DB::select("SHOW COLUMNS FROM stock_transfers");
$colNames = array_map(fn($c) => $c->Field, $cols);
echo "Columns: " . implode(', ', $colNames) . "\n\n";

$rows = DB::table('stock_transfers')->orderBy('id','desc')->get(['id','created_at', ...array_intersect(['transfer_date','date'], $colNames)]);
foreach ($rows as $r) {
    echo "  id:{$r->id}  created_at:{$r->created_at}\n";
}

// Check purchases, sales, adjustments too
echo "\n=== PURCHASES - id, created_at, purchase_date ===\n";
$rows = DB::table('purchases')->orderBy('id','desc')->take(10)->get(['id','created_at','purchase_date']);
foreach ($rows as $r) {
    echo "  id:{$r->id}  created_at:{$r->created_at}  purchase_date:{$r->purchase_date}\n";
}
