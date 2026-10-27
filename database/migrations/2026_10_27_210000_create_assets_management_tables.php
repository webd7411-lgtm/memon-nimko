<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asset Categories Table
        if (!Schema::hasTable('asset_categories')) {
            Schema::create('asset_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 20)->nullable();
                $table->text('description')->nullable();
                $table->decimal('depreciation_rate', 5, 2)->default(10.00); // Annual %
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

            // Seed default categories
            DB::table('asset_categories')->insert([
                [
                    'name' => 'Machinery & Plant',
                    'code' => 'MCH',
                    'description' => 'Production machines, fryers, packaging machines, mixers, ovens, generators',
                    'depreciation_rate' => 10.00,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Furniture & Fixtures',
                    'code' => 'FRN',
                    'description' => 'Display racks, cash counters, office tables, chairs, cabinets, shelves',
                    'depreciation_rate' => 10.00,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Electronics & IT',
                    'code' => 'ELC',
                    'description' => 'POS terminals, barcode printers, computers, AC units, CCTV systems',
                    'depreciation_rate' => 20.00,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Factory & Kitchen Tools',
                    'code' => 'EQP',
                    'description' => 'Weighing scales, trays, cutters, sealing machines, hand trolleys',
                    'depreciation_rate' => 15.00,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Vehicles & Transport',
                    'code' => 'VEH',
                    'description' => 'Delivery vans, cargo rickshaws, motorcycles',
                    'depreciation_rate' => 20.00,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Main Assets Table
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code')->unique();
                $table->string('name');
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->string('location_details')->nullable();
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_cost', 15, 2)->default(0.00);
                $table->decimal('current_value', 15, 2)->default(0.00);
                $table->decimal('salvage_value', 15, 2)->default(0.00);
                $table->integer('useful_life_years')->nullable()->default(5);
                $table->decimal('depreciation_rate', 5, 2)->default(10.00);
                $table->string('model_no')->nullable();
                $table->string('serial_no')->nullable();
                $table->date('warranty_expiry')->nullable();
                $table->string('custodian_name')->nullable();
                $table->string('condition', 30)->default('good'); // new, good, fair, needs_repair, damaged
                $table->string('status', 30)->default('active'); // active, under_maintenance, idle, disposed, scrapped
                $table->date('disposal_date')->nullable();
                $table->decimal('disposal_amount', 15, 2)->nullable();
                $table->text('disposal_reason')->nullable();
                $table->string('image')->nullable();
                $table->string('invoice_doc')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('category_id')->references('id')->on('asset_categories')->nullOnDelete();
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
                $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 3. Asset Maintenance / Repairs Table
        if (!Schema::hasTable('asset_maintenances')) {
            Schema::create('asset_maintenances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id');
                $table->date('maintenance_date');
                $table->string('service_type')->default('Routine Service'); // Routine Service, Repair, Parts Replacement, Overhaul
                $table->decimal('cost', 12, 2)->default(0.00);
                $table->string('performed_by')->nullable(); // Technician, Company, Vendor
                $table->date('next_service_date')->nullable();
                $table->text('description')->nullable();
                $table->text('parts_replaced')->nullable();
                $table->string('status', 30)->default('completed'); // scheduled, in_progress, completed
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 4. Asset Transfers Table
        if (!Schema::hasTable('asset_transfers')) {
            Schema::create('asset_transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id');
                $table->unsignedBigInteger('from_branch_id')->nullable();
                $table->unsignedBigInteger('to_branch_id');
                $table->string('from_location')->nullable();
                $table->string('to_location')->nullable();
                $table->date('transfer_date');
                $table->unsignedBigInteger('transferred_by')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
                $table->foreign('from_branch_id')->references('id')->on('branches')->nullOnDelete();
                $table->foreign('to_branch_id')->references('id')->on('branches')->cascadeOnDelete();
                $table->foreign('transferred_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 5. Seed Permissions for Assets
        if (Schema::hasTable('permissions')) {
            $permissions = [
                'View Assets',
                'Create Asset',
                'Edit Asset',
                'Delete Asset',
                'Asset Maintenance',
                'Asset Transfer',
                'Asset Report',
            ];

            foreach ($permissions as $perm) {
                DB::table('permissions')->insertOrIgnore([
                    'name' => $perm,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign to Super Admin role if exists
            $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $permIds = DB::table('permissions')->whereIn('name', $permissions)->pluck('id');
                foreach ($permIds as $pId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $pId,
                        'role_id' => $superAdminRole->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
