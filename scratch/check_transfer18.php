<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$t18 = DB::table('stock_transfers')->where('id', 18)->first();
echo "TRANSFER #18:\n";
print_r($t18);

echo "\nLAST 5 TRANSFERS:\n";
$transfers = DB::table('stock_transfers')->orderBy('id', 'desc')->limit(5)->get();
foreach ($transfers as $t) {
    echo "ID: {$t->id}, from_wh: {$t->from_warehouse_id}, transfer_to: {$t->transfer_to}, to_wh: {$t->to_warehouse_id}, branch_id: {$t->branch_id}, to_branch: {$t->to_branch_id}, status: {$t->status}\n";
}
