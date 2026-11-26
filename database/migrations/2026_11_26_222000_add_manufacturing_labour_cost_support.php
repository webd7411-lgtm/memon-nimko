<?php

use App\Models\AccountHead;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'labour_cost')) {
                    $table->decimal('labour_cost', 12, 2)
                        ->default(0)
                        ->after('recipe_batch_yield')
                        ->comment('Default manufacturing labour cost per piece/base unit');
                }
            });
        }

        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'labour_cost')) {
                    $table->decimal('labour_cost', 12, 2)
                        ->nullable()
                        ->after('cost_price')
                        ->comment('Optional variant-specific manufacturing labour cost per piece');
                }
            });
        }

        if (Schema::hasTable('production_entries')) {
            Schema::table('production_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('production_entries', 'raw_material_cost')) {
                    $table->decimal('raw_material_cost', 14, 2)->default(0)->after('production_cost');
                }
                if (!Schema::hasColumn('production_entries', 'labour_cost')) {
                    $table->decimal('labour_cost', 14, 2)->default(0)->after('raw_material_cost');
                }
            });

            // Backfill raw_material_cost from existing production_cost records
            DB::table('production_entries')
                ->where('raw_material_cost', 0)
                ->where('production_cost', '>', 0)
                ->update(['raw_material_cost' => DB::raw('production_cost')]);
        }

        if (Schema::hasTable('production_entry_items')) {
            Schema::table('production_entry_items', function (Blueprint $table) {
                if (!Schema::hasColumn('production_entry_items', 'labour_cost_per_pc')) {
                    $table->decimal('labour_cost_per_pc', 12, 2)->default(0)->after('qty_stock');
                }
                if (!Schema::hasColumn('production_entry_items', 'total_labour_cost')) {
                    $table->decimal('total_labour_cost', 14, 2)->default(0)->after('labour_cost_per_pc');
                }
                if (!Schema::hasColumn('production_entry_items', 'rm_cost')) {
                    $table->decimal('rm_cost', 14, 2)->default(0)->after('total_labour_cost');
                }
                if (!Schema::hasColumn('production_entry_items', 'total_cost')) {
                    $table->decimal('total_cost', 14, 2)->default(0)->after('rm_cost');
                }
            });
        }

        if (Schema::hasTable('expense_vouchers')) {
            Schema::table('expense_vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('expense_vouchers', 'is_manufacturing_labour')) {
                    $table->boolean('is_manufacturing_labour')
                        ->default(false)
                        ->after('is_stock_adjustment')
                        ->comment('True when voucher pays factory wages already absorbed into per-pc production cost');
                }
            });
        }

        if (Schema::hasTable('account_heads')) {
            AccountHead::firstOrCreate(
                ['name' => 'Direct Manufacturing Labour'],
                ['type' => 'Expense', 'status' => 1]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expense_vouchers') && Schema::hasColumn('expense_vouchers', 'is_manufacturing_labour')) {
            Schema::table('expense_vouchers', function (Blueprint $table) {
                $table->dropColumn('is_manufacturing_labour');
            });
        }

        if (Schema::hasTable('production_entry_items')) {
            Schema::table('production_entry_items', function (Blueprint $table) {
                foreach (['total_cost', 'rm_cost', 'total_labour_cost', 'labour_cost_per_pc'] as $col) {
                    if (Schema::hasColumn('production_entry_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('production_entries')) {
            Schema::table('production_entries', function (Blueprint $table) {
                foreach (['labour_cost', 'raw_material_cost'] as $col) {
                    if (Schema::hasColumn('production_entries', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('product_variants') && Schema::hasColumn('product_variants', 'labour_cost')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('labour_cost');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'labour_cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('labour_cost');
            });
        }
    }
};
