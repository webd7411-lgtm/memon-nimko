<?php

use Illuminate\Database\Migrations\Migration;
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
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            $perm = Permission::firstOrCreate([
                'name'       => 'Profile Settings',
                'guard_name' => 'web',
            ]);

            // Assign Profile Settings permission to super-admin & admin roles
            $adminRoles = Role::whereIn('name', ['super-admin', 'Super Admin', 'admin', 'Admin'])->get();
            foreach ($adminRoles as $role) {
                if (!$role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                }
            }

            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Profile settings permission migration notice: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe keep permissions
    }
};
