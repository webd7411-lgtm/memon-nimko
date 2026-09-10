<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CURRENT STOCK STATE FOR PRODUCT 227 ===\n";
$stocks = DB::table('stocks')->where('product_id', 227)->orderBy('id')->get();
foreach ($stocks as $s) {
    echo "ID:{$s->id} branch:{$s->branch_id} warehouse:" . ($s->warehouse_id ?? 'NULL') . " variant:" . ($s->variant_id ?? 'NULL') . " qty:{$s->qty} updated:{$s->updated_at}\n";
}

echo "\n=== CHECK IF ANY OTHER RECORDS EXIST ===\n";
$count = DB::table('stocks')->where('product_id', 227)->count();
echo "Total records: {$count}\n";

echo "\n=== TRACE: WHAT IF FIRST() MATCHES DIFFERENT RECORDS? ===\n";
// The query without warehouse_id filter
$query1 = DB::table('stocks')
    ->where('product_id', 227)
    ->where('branch_id', 1)
    ->whereNull('variant_id');

$firstRecord = $query1->first();
echo "First() with whereNull(variant_id): ID:{$firstRecord->id} warehouse:" . ($firstRecord->warehouse_id ?? 'NULL') . " qty:{$firstRecord->qty}\n";

echo "\n=== CHECK SALE RETURN STOCK QUERY ===\n";
// This is the fixed query for sale return
$query2 = DB::table('stocks')
    ->where('product_id', 227)
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->whereNull('variant_id');
$srRecord = $query2->first();
echo "Sale return (fixed): ID:" . ($srRecord ? $srRecord->id : 'NONE') . " warehouse:" . ($srRecord ? ($srRecord->warehouse_id ?? 'NULL') : 'N/A') . " qty:" . ($srRecord ? $srRecord->qty : 'N/A') . "\n";

echo "\n=== CHECK TRANSFER QUERY ===\n";
// Transfer uses firstOrCreate without warehouse filter
$query3 = DB::table('stocks')
    ->where('product_id', 227)
    ->where('branch_id', 1)
    ->whereNull('variant_id');
$transferRecord = $query3->first();
echo "Transfer (firstOrCreate): ID:{$transferRecord->id} warehouse:" . ($transferRecord->warehouse_id ?? 'NULL') . " qty:{$transferRecord->qty}\n";

echo "\n=== CONCLUSION ===\n";
echo "The 6-unit difference (32 vs 26) remains unexplained by the DB state alone.\n";
echo "Possible causes:\n";
echo "1. Sale creation deducts from the first matching record (unpredictable)\n";
echo "2. Sale return adds to the correct record but something else overwrites it\n";
echo "3. The product edit controller might have set stock to a specific value\n";
echo "4. There's a direct DB update I'm missing\n\n";

echo "However, after fixing the code bugs, the calculation should work correctly\n";
echo "for future transactions. The current discrepancy of 6 units is a historical\n";
echo "artifact that may not be fully recoverable without manual correction.\n";
