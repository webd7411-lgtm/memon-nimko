<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

echo "Check Permission 'Dashboard':\n";
$dashboard = Permission::where('name', 'Dashboard')->first();
if ($dashboard) {
    echo "Permission 'Dashboard' exists (ID: {$dashboard->id})\n";
} else {
    echo "Permission 'Dashboard' DOES NOT EXIST in permissions table!\n";
}

echo "\nAll Permissions in DB:\n";
$all = Permission::pluck('name')->toArray();
echo implode(', ', $all) . "\n\n";

$user = User::where('email', 'admin@admin.com')->first();
echo "User: " . $user->name . "\n";
echo "User roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
echo "User can 'Dashboard': " . ($user->can('Dashboard') ? 'YES' : 'NO') . "\n";
echo "User hasPermissionTo('Dashboard'): " . ($user->hasPermissionTo('Dashboard') ? 'YES' : 'NO') . "\n";
