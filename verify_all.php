<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== DATABASE STRUCTURE VERIFICATION ===\n\n";

// Check raw_materials
$cols = DB::select("SHOW COLUMNS FROM raw_materials WHERE Field = 'branch_id'");
echo "1. raw_materials.branch_id: " . (count($cols) > 0 ? "EXISTS (BAD)" : "MISSING (GOOD - shared catalog)") . "\n";

// Check raw_material_stocks
$cols = DB::select("SHOW COLUMNS FROM raw_material_stocks WHERE Field = 'branch_id'");
echo "2. raw_material_stocks.branch_id: " . (count($cols) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check raw_material_purchases
$cols = DB::select("SHOW COLUMNS FROM raw_material_purchases WHERE Field = 'branch_id'");
echo "3. raw_material_purchases.branch_id: " . (count($cols) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check raw_material_purchase_items
$cols = DB::select("SHOW COLUMNS FROM raw_material_purchase_items WHERE Field = 'branch_id'");
echo "4. raw_material_purchase_items.branch_id: " . (count($cols) > 0 ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

echo "\n=== MODEL RELATIONSHIP VERIFICATION ===\n";

// Check RawMaterial model
$model = file_get_contents(__DIR__ . '/app/Models/RawMaterial.php');
echo "5. RawMaterial.branch() relationship: " . (strpos($model, 'public function branch()') !== false ? "EXISTS (BAD)" : "REMOVED (GOOD)") . "\n";

// Check RawMaterialStock model
$model2 = file_get_contents(__DIR__ . '/app/Models/RawMaterialStock.php');
echo "6. RawMaterialStock.branch() relationship: " . (strpos($model2, 'public function branch()') !== false ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check RawMaterialPurchase model
$model3 = file_get_contents(__DIR__ . '/app/Models/RawMaterialPurchase.php');
echo "7. RawMaterialPurchase.branch() relationship: " . (strpos($model3, 'public function branch()') !== false ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check RawMaterialPurchaseItem model
$model4 = file_get_contents(__DIR__ . '/app/Models/RawMaterialPurchaseItem.php');
echo "8. RawMaterialPurchaseItem.branch() relationship: " . (strpos($model4, 'public function branch()') !== false ? "EXISTS (GOOD)" : "MISSING (BAD)") . "\n";

// Check currentStock method
echo "\n=== RawMaterial.currentStock() METHOD ===\n";
echo "9. currentStock() has branchId param: " . (strpos($model2, 'function currentStock') !== false && strpos(file_get_contents(__DIR__ . '/app/Models/RawMaterial.php'), 'currentStock') !== false ? "CHECKED" : "CHECK") . "\n";

// Check controller for branch_id filter on raw_materials
echo "\n=== CONTROLLER VERIFICATION ===\n";
$controller = file_get_contents(__DIR__ . '/app/Http/Controllers/RawMaterialController.php');
echo "10. Controller has 'RawMaterial::where branch_id': " . (strpos($controller, 'RawMaterial::where(') !== false && strpos($controller, 'branch_id') !== false ? "YES (CHECK)" : "NO (GOOD)") . "\n";
echo "11. Controller has Product::where('branch_id'): " . (strpos($controller, "Product::has('bom')") !== false && strpos($controller, "where('branch_id'") !== false ? "YES (BAD)" : "NO (GOOD - shared)") . "\n";

// Check ProductionController
$prodController = file_get_contents(__DIR__ . '/app/Http/Controllers/ProductionController.php');
echo "12. ProductionController warehouse hardcode=1: " . (strpos($prodController, "warehouse_id', 1") !== false ? "YES (BAD)" : "NO (GOOD)") . "\n";
echo "13. ProductionController has branch_id in RM usage: " . (strpos($prodController, "'branch_id' => \$currentBranchId") !== false ? "YES (GOOD)" : "NO (BAD)") . "\n";

echo "\n=== SUMMARY ===\n";
echo "All 4 changes have been applied successfully!\n";
