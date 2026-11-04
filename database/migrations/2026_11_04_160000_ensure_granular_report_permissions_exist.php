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

            $reportPermissions = [
                'Financial Command Center (360°)',
                'Reporting 360',
                'Balance Sheet Report',
                'Print Balance Sheet',
                'Profit Loss Report',
                'Daily Sale Closing Report',
                'Print Sale Closing',
                'Category Sale Report',
                'Branch Stock Report',
                'Production Report',
                'Consolidated Sales Report',
                'Master Channel Report',
                'Distribution Recovery & Chain Ledger',
                'Stock Adjustment Audit Report',
                'Expense Audit Report',
                'Item Stock Report',
                'Purchase Report',
                'Sale Report',
                'Customer Ledger',
                'Vendor Ledger',
                'Expense Report',
                'System Reports',
                'Asset Report',
            ];

            $createdPerms = [];
            foreach ($reportPermissions as $permName) {
                $createdPerms[] = Permission::firstOrCreate([
                    'name'       => $permName,
                    'guard_name' => 'web',
                ]);
            }

            // Assign all report permissions to super-admin & admin roles
            $adminRoles = Role::whereIn('name', ['super-admin', 'Super Admin', 'admin', 'Admin'])->get();
            foreach ($adminRoles as $role) {
                foreach ($createdPerms as $perm) {
                    if (!$role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                    }
                }
            }

            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Report permissions migration notice: ' . $e->getMessage());
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
