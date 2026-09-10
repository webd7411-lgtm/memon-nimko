<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MANUAL CORRECTION FOR PURCHASE PUR-048 ===\n\n";

// The purchase updated the WRONG records (warehouse=1) due to missing warehouse filter.
// We need to fix the stock records so the report shows correct values.

// For Product 83 (2 in 1 Biscuits, 20 KG) — KG product, purchase creates variant=NULL
$existing83 = DB::table('stocks')
    ->where('product_id', 83)
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->whereNull('variant_id')
    ->first();

echo "Product 83 (KG): Existing shop stock (warehouse=NULL, variant=NULL): ";
echo ($existing83 ? "ID:{$existing83->id} qty:{$existing83->qty}" : 'NONE') . "\n";

if ($existing83) {
    // Update existing
    DB::table('stocks')
        ->where('id', $existing83->id)
        ->update([
            'qty' => 20000,
            'updated_at' => now()
        ]);
    echo "  Updated existing ID:{$existing83->id} to qty=20000 (20 KG in grams)\n";
} else {
    // Create new
    $newId = DB::table('stocks')->insertGetId([
        'branch_id' => 1,
        'warehouse_id' => null,
        'product_id' => 83,
        'variant_id' => null,
        'qty' => 20000,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  Created new record ID:{$newId} qty=20000\n";
}

// For Product 227 (2 PC BUN, 20 PC) — non-KG, purchase creates variant=334
$existing227 = DB::table('stocks')
    ->where('product_id', 227)
    ->where('branch_id', 1)
    ->whereNull('warehouse_id')
    ->where('variant_id', 334)
    ->first();

echo "\nProduct 227 (PC): Existing shop stock (warehouse=NULL, variant=334): ";
echo ($existing227 ? "ID:{$existing227->id} qty:{$existing227->qty}" : 'NONE') . "\n";

if ($existing227) {
    DB::table('stocks')
        ->where('id', $existing227->id)
        ->update([
            'qty' => 20,
            'updated_at' => now()
        ]);
    echo "  Updated existing ID:{$existing227->id} to qty=20\n";
} else {
    $newId = DB::table('stocks')->insertGetId([
        'branch_id' => 1,
        'warehouse_id' => null,
        'product_id' => 227,
        'variant_id' => 334,
        'qty' => 20,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  Created new record ID:{$newId} qty=20\n";
}

echo "\n=== MANUAL CORRECTION DONE ===\n";
