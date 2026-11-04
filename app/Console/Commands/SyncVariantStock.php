<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Stock;

class SyncVariantStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:sync-variants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync past productions and stocks created without variant_id to their default product variants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("==================================================");
        $this->info("Starting Variant & Stock Synchronization...");
        $this->info("==================================================");

        // 1. Find all products that have variants
        $productsWithVariants = Product::whereHas('variants')->with('variants')->get();
        $this->info("Found {$productsWithVariants->count()} products with variants in database.");

        $updatedProductionCount = 0;
        $updatedStockCount = 0;

        foreach ($productsWithVariants as $product) {
            $defaultVariant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();
            if (!$defaultVariant) {
                continue;
            }

            // A. Update production_entry_items where variant_id is null or 0
            $affectedPei = DB::table('production_entry_items')
                ->where('product_id', $product->id)
                ->where(function($q) {
                    $q->whereNull('variant_id')->orWhere('variant_id', 0)->orWhere('variant_id', '');
                })
                ->update(['variant_id' => $defaultVariant->id]);

            if ($affectedPei > 0) {
                $this->info("✔ Updated {$affectedPei} production entries for [{$product->item_name}] to variant [{$defaultVariant->size_label}].");
                $updatedProductionCount += $affectedPei;
            }

            // B. Sync stocks table: transfer base stock (variant_id IS NULL or 0) to defaultVariant
            $baseStocks = Stock::where('product_id', $product->id)
                ->where(function($q) {
                    $q->whereNull('variant_id')->orWhere('variant_id', 0);
                })
                ->where('qty', '!=', 0)
                ->get();

            foreach ($baseStocks as $bStock) {
                $bQty = (float)$bStock->qty;
                if ($bQty == 0) continue;

                // Find or create variant stock in the same branch & warehouse
                $variantStock = Stock::firstOrCreate(
                    [
                        'product_id'   => $product->id,
                        'variant_id'   => $defaultVariant->id,
                        'branch_id'    => $bStock->branch_id,
                        'warehouse_id' => $bStock->warehouse_id,
                    ],
                    [
                        'qty' => 0,
                    ]
                );

                $oldVariantQty = (float)$variantStock->qty;
                $variantStock->qty += $bQty;
                $variantStock->save();

                $this->info("✔ Shifted {$bQty} base stock of [{$product->item_name}] to variant [{$defaultVariant->size_label}] (Old: {$oldVariantQty}, New: {$variantStock->qty}).");

                // Reset base stock to 0
                $bStock->qty = 0;
                $bStock->save();

                $updatedStockCount++;
            }
        }

        $this->info("==================================================");
        $this->info("Sync Completed Successfully!");
        $this->info("Updated {$updatedProductionCount} production item records.");
        $this->info("Adjusted {$updatedStockCount} stock balance records.");
        $this->info("==================================================");

        return 0;
    }
}
