<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Reset cached roles and permissions
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            $permissions = [
                'Distributor Management',
                'Distributor Orders',
                'Distributor Portal',
            ];

            foreach ($permissions as $permName) {
                Permission::firstOrCreate([
                    'name'       => $permName,
                    'guard_name' => 'web',
                ]);
            }

            // Create Distributor role
            $distributorRole = Role::firstOrCreate([
                'name'       => 'Distributor',
                'guard_name' => 'web',
            ]);

            $portalPerm = Permission::where('name', 'Distributor Portal')->where('guard_name', 'web')->first();
            if ($portalPerm && !$distributorRole->hasPermissionTo($portalPerm)) {
                $distributorRole->givePermissionTo($portalPerm);
            }

            // Grant admin / super-admin permissions to manage distributors
            $roles = Role::whereIn('name', ['Super Admin', 'super-admin', 'Admin', 'admin'])->get();
            $adminPerms = Permission::whereIn('name', ['Distributor Management', 'Distributor Orders'])->where('guard_name', 'web')->get();
            foreach ($roles as $role) {
                foreach ($adminPerms as $perm) {
                    if (!$role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration ensure_distributor_role notice: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive reverse
    }
};
