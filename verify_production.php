<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== PRODUCTION TABLES VERIFICATION ===\n\n";

// Check production_raw_material_usage
$cols = DB::select("SHOW COLUMNS FROM production_raw_material_usage WHERE Field = 'branch_id'");
echo "production_raw_material_usage.branch_id: " . (count($cols) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check production_entries
$cols2 = DB::select("SHOW COLUMNS FROM production_entries WHERE Field = 'branch_id'");
echo "production_entries.branch_id: " . (count($cols2) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check product_raw_material_bom
$cols3 = DB::select("SHOW COLUMNS FROM product_raw_material_bom WHERE Field = 'branch_id'");
echo "product_raw_material_bom.branch_id: " . (count($cols3) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check production_entry_items
$cols4 = DB::select("SHOW COLUMNS FROM production_entry_items WHERE Field = 'branch_id'");
echo "production_entry_items.branch_id: " . (count($cols4) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";
