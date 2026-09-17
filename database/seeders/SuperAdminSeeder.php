<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('12345678'),
            ]
        );

        // Create super-admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign all permissions to super-admin role
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Assign role to Super Admin
        $superAdmin->syncRoles([$superAdminRole]);

        $this->command->info('✅ Super Admin created: admin@admin.com | Password: 12345678');
    }
}
