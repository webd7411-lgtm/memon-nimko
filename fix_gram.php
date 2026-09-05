<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\RawMaterial::where('id', 3)->update(['consumption_unit' => 'gram']);
App\Models\RawMaterial::where('id', 5)->update(['consumption_unit' => 'gram']);
echo "Updated RM 3 and 5 to gram\n";
