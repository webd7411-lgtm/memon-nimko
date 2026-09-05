<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rm = App\Models\RawMaterial::find(3);
echo 'id='.$rm->id.' name='.$rm->name.' unit='.$rm->unit.' consumption_unit='.($rm->consumption_unit ?? 'NULL').PHP_EOL;

$rm5 = App\Models\RawMaterial::find(5);
echo 'id='.$rm5->id.' name='.$rm5->name.' unit='.$rm5->unit.' consumption_unit='.($rm5->consumption_unit ?? 'NULL').PHP_EOL;
