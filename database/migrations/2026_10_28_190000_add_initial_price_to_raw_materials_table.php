<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('raw_materials') && !Schema::hasColumn('raw_materials', 'initial_price')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->decimal('initial_price', 12, 2)->default(0)->after('alert_qty');
            });
        }

        // Migrate prices from any existing INIT- purchases before deleting them
        $initPurchases = DB::table('raw_material_purchases')
            ->where('invoice_no', 'like', 'INIT-%')
            ->orWhere('vendor_name', 'Initial Cost Setup')
            ->get();

        foreach ($initPurchases as $purchase) {
            $items = DB::table('raw_material_purchase_items')
                ->where('purchase_id', $purchase->id)
                ->get();

            foreach ($items as $item) {
                if ($item->price_per_unit > 0) {
                    DB::table('raw_materials')
                        ->where('id', $item->raw_material_id)
                        ->update(['initial_price' => $item->price_per_unit]);
                }
            }

            DB::table('raw_material_purchase_items')->where('purchase_id', $purchase->id)->delete();
            DB::table('raw_material_purchases')->where('id', $purchase->id)->delete();
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('raw_materials') && Schema::hasColumn('raw_materials', 'initial_price')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->dropColumn('initial_price');
            });
        }
    }
};
