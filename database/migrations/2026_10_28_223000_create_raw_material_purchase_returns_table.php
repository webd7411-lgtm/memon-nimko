<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('raw_material_purchase_returns')) {
            Schema::create('raw_material_purchase_returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->nullable()->constrained('raw_material_purchases')->onDelete('cascade');
                $table->string('return_invoice')->unique();
                $table->date('return_date');
                $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
                $table->string('vendor_name')->nullable();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->decimal('total_cost', 14, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('raw_material_purchase_return_items')) {
            Schema::create('raw_material_purchase_return_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_return_id')->constrained('raw_material_purchase_returns')->cascadeOnDelete();
                $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
                $table->decimal('qty', 12, 2);
                $table->decimal('price_per_unit', 12, 2);
                $table->decimal('total', 14, 2);
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchase_return_items');
        Schema::dropIfExists('raw_material_purchase_returns');
    }
};
