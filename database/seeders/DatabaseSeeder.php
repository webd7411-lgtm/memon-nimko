<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(100)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            WarehouseSeeder::class,
            SuperAdminSeeder::class,
            PermissionSeeder::class,
        ]);

        
        $branchUser = User::updateOrCreate(
                    ['email' => 'soban@soban.com'],
                    [
                        'name' => 'soban',
                        'password' => Hash::make('soban')
                    ]
                );
        $adminUser = User::updateOrCreate(
                    ['email' => 'admin@admin.com'],
                    [
                        'name' => 'Super Admin',
                        'password' => Hash::make('12345678')
                    ]
                );

        $allPermissions = Permission::pluck('name')->all();

        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $branchRole = Role::firstOrCreate(['name' => 'branch']);

        // Grant the full permission set to roles
        $superAdminRole->syncPermissions($allPermissions);
        $adminRole->syncPermissions($allPermissions);
        $branchRole->syncPermissions($allPermissions);

        if ($adminUser) {
            $adminUser->syncRoles([$superAdminRole, $adminRole]);
        }
        if ($branchUser) {
            $branchUser->syncRoles([$branchRole]);
        }
    }
}
