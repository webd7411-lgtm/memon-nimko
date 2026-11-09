<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Stock;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function allVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeDiscount()
    {
        return $this->hasOne(ProductDiscount::class, 'product_id')
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('branch_id', active_branch_id())
                  ->orWhereNull('branch_id');
            });
    }

    public function discountProduct()
    {
        return $this->hasOne(ProductDiscount::class, 'product_id', 'id')
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('branch_id', active_branch_id())
                  ->orWhereNull('branch_id');
            });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function category_relation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category_relation()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class)->orderByRaw('variant_id IS NULL DESC, id DESC');
    }

    public function baseStock()
    {
        $query = $this->hasOne(Stock::class, 'product_id')
            ->whereNull('variant_id')
            ->where(function($q) {
                $q->whereNull('warehouse_id')->orWhere('warehouse_id', 0);
            });
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }
        return $query;
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function distributorStocks()
    {
        return $this->hasMany(DistributorStock::class, 'product_id');
    }

    public function bom()
    {
        return $this->hasMany(ProductRawMaterialBom::class, 'product_id');
    }

    /**
     * Get real branch stock for a product or specific variant,
     * unifying base stock (variant_id IS NULL) with default variant stock consistently.
     */
    public function getBranchStock($variantId = null, $branchId = null, $warehouseId = null)
    {
        $branchId = $branchId ?? active_branch_id();
        $isKg = strtolower($this->unit_type ?? '') === 'kg';

        if ($isKg) {
            $q = Stock::where('product_id', $this->id)
                ->where('branch_id', $branchId);
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            } else {
                $q->whereNull('warehouse_id');
            }
            return (float) $q->sum('qty');
        }

        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();

        if (!$variants || $variants->isEmpty()) {
            $q = Stock::where('product_id', $this->id)
                ->where('branch_id', $branchId);
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            } else {
                $q->whereNull('warehouse_id');
            }
            return (float) $q->sum('qty');
        }

        // Product has variants
        $variant = $variantId ? $variants->firstWhere('id', $variantId) : null;
        if (!$variant) {
            $variant = $variants->firstWhere('is_default', 1) ?? $variants->first();
        }

        if (!$variant) {
            return 0;
        }

        $vStockQ = Stock::where('product_id', $this->id)
            ->where('branch_id', $branchId)
            ->where('variant_id', $variant->id);
        if ($warehouseId) {
            $vStockQ->where('warehouse_id', $warehouseId);
        } else {
            $vStockQ->whereNull('warehouse_id');
        }
        $vStock = $vStockQ->first();
        $stock = $vStock ? (float)$vStock->qty : (float)($variant->stock_qty ?? 0);

        // Always merge unallocated base product stock (variant_id IS NULL or 0) into default/first variant
        if ($variant->is_default || ($variants->first() && $variants->first()->id == $variant->id)) {
            $baseStockQ = Stock::where('product_id', $this->id)
                ->where('branch_id', $branchId)
                ->where(function($sq) {
                    $sq->whereNull('variant_id')->orWhere('variant_id', 0);
                });
            if ($warehouseId) {
                $baseStockQ->where('warehouse_id', $warehouseId);
            } else {
                $baseStockQ->whereNull('warehouse_id');
            }
            $stock += (float) $baseStockQ->sum('qty');
        }

        return $stock;
    }

    /**
     * Automatically sync any unsynced base stocks into default variants.
     * Runs seamlessly on web requests without needing CLI commands.
     */
    public static function autoSyncVariantStocks(): void
    {
        try {
            $hasUnsynced = Stock::where(function($q) {
                    $q->whereNull('variant_id')->orWhere('variant_id', 0);
                })
                ->where('qty', '!=', 0)
                ->whereIn('product_id', function($sq) {
                    $sq->select('product_id')->from('product_variants');
                })
                ->exists();

            if (!$hasUnsynced) {
                return;
            }

            self::runVariantStockSync();
        } catch (\Throwable $e) {
            Log::warning('autoSyncVariantStocks error: ' . $e->getMessage());
        }
    }

    /**
     * Merge legacy/base product stocks (variant_id is null/0) into default variants.
     */
    public static function runVariantStockSync(): void
    {
        $productsWithVariants = self::whereHas('variants')->with('variants')->get();

        foreach ($productsWithVariants as $product) {
            $defaultVariant = $product->variants->where('is_default', 1)->first() ?? $product->variants->first();
            if (!$defaultVariant) {
                continue;
            }

            // A. Update production_entry_items where variant_id is null or 0
            DB::table('production_entry_items')
                ->where('product_id', $product->id)
                ->where(function($q) {
                    $q->whereNull('variant_id')->orWhere('variant_id', 0)->orWhere('variant_id', '');
                })
                ->update(['variant_id' => $defaultVariant->id]);

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

                $variantStock->qty += $bQty;
                $variantStock->save();

                // Reset base stock to 0
                $bStock->qty = 0;
                $bStock->save();
            }
        }
    }
}
