<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ms = DB::table('migrations')->where('migration','like','2026_09_06%')->pluck('migration')->toArray();
echo "Migrations run: " . implode(', ', $ms) . "\n";

// Check columns
$conn = DB::connection()->getPdo();
$stmt = $conn->query("SHOW COLUMNS FROM production_raw_material_usage LIKE 'branch_id'");
echo "production_raw_material_usage.branch_id: " . ($stmt->rowCount() ? "EXISTS" : "MISSING") . "\n";

$stmt2 = $conn->query("SHOW COLUMNS FROM production_entry_items LIKE 'branch_id'");
echo "production_entry_items.branch_id: " . ($stmt2->rowCount() ? "EXISTS" : "MISSING") . "\n";

$stmt3 = $conn->query("SHOW COLUMNS FROM product_raw_material_bom LIKE 'branch_id'");
echo "product_raw_material_bom.branch_id: " . ($stmt3->rowCount() ? "EXISTS" : "MISSING") . "\n";

$stmt4 = $conn->query("SHOW COLUMNS FROM production_entries LIKE 'branch_id'");
echo "production_entries.branch_id: " . ($stmt4->rowCount() ? "EXISTS" : "MISSING") . "\n";

$stmt5 = $conn->query("SHOW COLUMNS FROM raw_materials LIKE 'branch_id'");
echo "raw_materials.branch_id: " . ($stmt5->rowCount() ? "EXISTS" : "MISSING") . "\n";

// Check branches table exists
$stmt6 = $conn->query("SHOW TABLES LIKE 'branches'");
echo "branches table: " . ($stmt6->rowCount() ? "EXISTS" : "MISSING") . "\n";
