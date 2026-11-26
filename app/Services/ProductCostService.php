<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductCostService
{
    public static function isKgProduct(?Product $product): bool
    {
        if (!$product) {
            return false;
        }
        return ($product->unit_type === 'kg') || str_contains(strtolower($product->unit->name ?? ''), 'kg');
    }

    public static function variantSizeInKg(?ProductVariant $variant): float
    {
        if (!$variant) {
            return 1.0;
        }
        $sVal = (float) ($variant->size_value ?? 0);
        $unit = strtolower(trim($variant->size_unit ?? ''));
        if (in_array($unit, ['g', 'gm', 'gram', 'grams'], true) && $sVal > 0) {
            $sVal /= 1000;
        }
        return $sVal > 0 ? $sVal : 1.0;
    }

    /**
     * Resolve effective manufacturing labour cost per produced unit/piece.
     * Priority: explicit variant override (including 0) -> product default (scaled by KG size for KG products).
     */
    public function resolveLabourCost(?Product $product, ?ProductVariant $variant = null): float
    {
        if (!$product) {
            return 0.0;
        }

        if ($variant !== null && $variant->labour_cost !== null) {
            return max(0.0, round((float) $variant->labour_cost, 2));
        }

        $baseLabour = max(0.0, (float) ($product->labour_cost ?? 0));
        if ($baseLabour <= 0) {
            return 0.0;
        }

        if ($variant !== null && self::isKgProduct($product)) {
            return round($baseLabour * self::variantSizeInKg($variant), 2);
        }

        return round($baseLabour, 2);
    }

    /**
     * Sum raw material and semi-finished ingredient costs for a collection of BOM rows.
     */
    public function computeBomItemsCost($bomItems): float
    {
        $cost = 0.0;

        foreach ($bomItems as $bItem) {
            $qty = (float) ($bItem->qty_per_unit ?? $bItem->qty ?? 0);
            if ($qty <= 0) {
                continue;
            }

            if ($bItem->rawMaterial) {
                $rmCost = (float) $bItem->rawMaterial->lastPurchaseCost();
                $factor = (float) ($bItem->rawMaterial->conversion_factor ?? 1);
                if ($factor <= 0) {
                    $factor = 1;
                }
                $cost += ($qty * ($rmCost / $factor));
            } elseif ($bItem->ingredientProduct) {
                $ingCost = (float) ($bItem->ingredientProduct->wholesale_price ?: $bItem->ingredientProduct->price ?: 0);
                $cost += ($qty * $ingCost);
            }
        }

        return $cost;
    }

    /**
     * Build BOM material, labour, and combined cost maps keyed by "{product_id}_{variant_id}".
     */
    public function buildBomCostBreakdown($products): array
    {
        $map = [];
        $material = [];
        $labour = [];

        foreach ($products as $pObj) {
            $pBoms = $pObj->bom ?? collect();
            $isObjKg = self::isKgProduct($pObj);

            $baseMatCost = $pBoms->isNotEmpty()
                ? $this->computeBomItemsCost($pBoms->whereNull('variant_id'))
                : 0.0;
            $baseLabour = $this->resolveLabourCost($pObj, null);

            $baseKey = $pObj->id . '_0';
            if ($baseMatCost > 0) {
                $material[$baseKey] = round($baseMatCost, 4);
                $map[$baseKey] = round($baseMatCost + $baseLabour, 4);
            }
            if ($baseLabour > 0) {
                $labour[$baseKey] = round($baseLabour, 4);
            }

            foreach ($pObj->variants as $vObj) {
                $vKey = $pObj->id . '_' . $vObj->id;
                $vBom = $pBoms->where('variant_id', $vObj->id);

                $vMatCost = 0.0;
                if ($vBom->isNotEmpty()) {
                    $vMatCost = $this->computeBomItemsCost($vBom);
                } elseif ($baseMatCost > 0) {
                    $vMatCost = $isObjKg
                        ? ($baseMatCost * self::variantSizeInKg($vObj))
                        : $baseMatCost;
                }

                $vLabour = $this->resolveLabourCost($pObj, $vObj);

                if ($vMatCost > 0) {
                    $material[$vKey] = round($vMatCost, 4);
                    $map[$vKey] = round($vMatCost + $vLabour, 4);
                }
                if ($vLabour > 0) {
                    $labour[$vKey] = round($vLabour, 4);
                }
            }
        }

        return [
            'map'      => $map,
            'material' => $material,
            'labour'   => $labour,
        ];
    }

    /**
     * Backward-compatible flat BOM + Labour unit cost map keyed by "{product_id}_{variant_id}".
     */
    public function buildBomProductCostMap($products): array
    {
        return $this->buildBomCostBreakdown($products)['map'];
    }

    /**
     * Fetch latest per-unit RM and Labour cost from recorded production batches (fallback when BOM/default isn't set).
     */
    public function buildLatestProductionCostMap(): array
    {
        if (!Schema::hasTable('production_entry_items') || !Schema::hasColumn('production_entry_items', 'labour_cost_per_pc')) {
            return [];
        }

        $rows = DB::table('production_entry_items')
            ->select('product_id', 'variant_id', 'qty_entered', 'rm_cost', 'labour_cost_per_pc')
            ->where('qty_entered', '>', 0)
            ->orderByDesc('id')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $vKey = $r->product_id . '_' . ($r->variant_id ?: 0);
            $pKey = $r->product_id . '_0';
            $rmPerUnit = (float) $r->qty_entered > 0 ? round((float) $r->rm_cost / (float) $r->qty_entered, 4) : 0.0;
            $labPerUnit = (float) $r->labour_cost_per_pc;

            if (!isset($map[$vKey])) {
                $map[$vKey] = [
                    'rm_per_unit'     => $rmPerUnit,
                    'labour_per_unit' => $labPerUnit,
                ];
            }
            if (!isset($map[$pKey])) {
                $map[$pKey] = [
                    'rm_per_unit'     => $rmPerUnit,
                    'labour_per_unit' => $labPerUnit,
                ];
            }
        }

        return $map;
    }

    /**
     * Resolve unit cost with material vs manufacturing labour breakdown.
     *
     * @return array{material_cost: float, labour_cost: float, total_cost: float}
     */
    public function resolveUnitCostWithBreakdown(
        $pid,
        $vid,
        Collection $productsWithBom,
        Collection $variantsDict,
        array $breakdown,
        array $purchasePriceMap,
        array $latestProductionMap = []
    ): array {
        $vidClean = ($vid !== null && $vid !== '' && $vid !== '0' && $vid !== 0) ? (string) $vid : '0';
        $vKey = $pid . '_' . $vidClean;
        $pKey = $pid . '_0';

        /** @var Product|null $p */
        $p = $productsWithBom->get((int) $pid);
        /** @var ProductVariant|null $vObj */
        $vObj = ($vidClean !== '0' && $variantsDict->has($vidClean)) ? $variantsDict->get($vidClean) : null;

        // 1. Resolve Material / Purchase Base Cost
        $matMap = $breakdown['material'] ?? [];
        $materialCost = 0.0;

        if (isset($matMap[$vKey]) && $matMap[$vKey] > 0) {
            $materialCost = (float) $matMap[$vKey];
        } elseif (isset($matMap[$pKey]) && $matMap[$pKey] > 0) {
            if ($p && self::isKgProduct($p) && $vObj) {
                $materialCost = (float) $matMap[$pKey] * self::variantSizeInKg($vObj);
            } else {
                $materialCost = (float) $matMap[$pKey];
            }
        } elseif (isset($latestProductionMap[$vKey]['rm_per_unit']) && $latestProductionMap[$vKey]['rm_per_unit'] > 0) {
            $materialCost = (float) $latestProductionMap[$vKey]['rm_per_unit'];
        } elseif (isset($purchasePriceMap[$vKey]) && $purchasePriceMap[$vKey] > 0) {
            $materialCost = (float) $purchasePriceMap[$vKey];
        } elseif (isset($purchasePriceMap[$pKey]) && $purchasePriceMap[$pKey] > 0) {
            $materialCost = (float) $purchasePriceMap[$pKey];
        } elseif ($vObj && (float) $vObj->cost_price > 0) {
            $materialCost = (float) $vObj->cost_price;
        } elseif ($p && ($firstV = $p->variants->first()) && (float) $firstV->cost_price > 0) {
            $materialCost = (float) $firstV->cost_price;
        } elseif ($p && (float) $p->wholesale_price > 0) {
            $materialCost = (float) $p->wholesale_price;
        }

        // 2. Resolve Manufacturing Labour Cost per piece/unit
        $labMap = $breakdown['labour'] ?? [];
        $labourCost = 0.0;

        if (array_key_exists($vKey, $labMap)) {
            $labourCost = (float) $labMap[$vKey];
        } elseif ($vObj !== null) {
            $labourCost = $this->resolveLabourCost($p, $vObj);
        } elseif (array_key_exists($pKey, $labMap)) {
            $labourCost = (float) $labMap[$pKey];
        } elseif ($p) {
            $labourCost = $this->resolveLabourCost($p, null);
        }

        if ($labourCost <= 0 && isset($latestProductionMap[$vKey]['labour_per_unit']) && $latestProductionMap[$vKey]['labour_per_unit'] > 0) {
            $labourCost = (float) $latestProductionMap[$vKey]['labour_per_unit'];
        }

        return [
            'material_cost' => round($materialCost, 4),
            'labour_cost'   => round($labourCost, 4),
            'total_cost'    => round($materialCost + $labourCost, 4),
        ];
    }
}
