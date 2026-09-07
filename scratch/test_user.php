<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@admin.com')->first();
echo "User: " . $user->name . "\n";
echo "Pass '12345678': " . (Illuminate\Support\Facades\Hash::check('12345678', $user->password) ? 'MATCH' : 'NO MATCH') . "\n";
echo "Pass 'admin': " . (Illuminate\Support\Facades\Hash::check('admin', $user->password) ? 'MATCH' : 'NO MATCH') . "\n";
echo "Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
