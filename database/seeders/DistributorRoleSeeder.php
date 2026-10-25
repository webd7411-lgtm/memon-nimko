<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DistributorRoleSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'Distributor Management',
            'Distributor Orders',
            'Distributor Portal',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }

        // Create or get Distributor role
        $distributorRole = Role::firstOrCreate(['name' => 'Distributor']);
        $distributorRole->givePermissionTo('Distributor Portal');

        // Give Admin and Super Admin access to Distributor Management & Orders
        $superAdmin = Role::whereIn('name', ['Super Admin', 'super-admin'])->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(['Distributor Management', 'Distributor Orders']);
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(['Distributor Management', 'Distributor Orders']);
        }
    }
}
