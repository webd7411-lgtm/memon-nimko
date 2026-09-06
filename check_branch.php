<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check branch 4 exists
$branch = DB::table('branches')->find(4);
echo "Branch ID 4: " . ($branch ? $branch->name : "NOT FOUND") . "\n";

// Check branches count
$count = DB::table('branches')->count();
echo "Total branches: $count\n";

// Show all branches
$branches = DB::table('branches')->select('id','name')->get();
foreach ($branches as $b) echo "  Branch: id={$b->id} name={$b->name}\n";

// Check production_raw_material_usage columns
$conn = DB::connection()->getPdo();
$stmt = $conn->query("SHOW COLUMNS FROM production_raw_material_usage");
echo "\nproduction_raw_material_usage columns:\n";
foreach ($stmt->fetchAll() as $col) {
    echo "  {$col['Field']} ({$col['Type']}) " . ($col['Null']==='NO'?'NOT NULL':'NULL') . ($col['Default'] ? " default={$col['Default']}" : "") . "\n";
}

// Check production_entries columns
$stmt2 = $conn->query("SHOW COLUMNS FROM production_entries");
echo "\nproduction_entries columns:\n";
foreach ($stmt2->fetchAll() as $col) {
    echo "  {$col['Field']} ({$col['Type']}) " . ($col['Null']==='NO'?'NOT NULL':'NULL') . "\n";
}
