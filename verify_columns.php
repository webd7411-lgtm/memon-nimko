<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;
$cols = DB::select("SHOW COLUMNS FROM raw_material_stocks WHERE Field = 'branch_id'");
echo count($cols) > 0 ? "branch_id EXISTS in raw_material_stocks\n" : "branch_id MISSING in raw_material_stocks\n";
$cols2 = DB::select("SHOW COLUMNS FROM raw_materials WHERE Field = 'branch_id'");
echo count($cols2) > 0 ? "branch_id EXISTS in raw_materials\n" : "branch_id DOES NOT EXIST in raw_materials\n";
