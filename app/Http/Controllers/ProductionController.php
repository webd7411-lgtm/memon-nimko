<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\ProductRawMaterialBom;
use App\Models\ProductVariant;
use App\Services\ProductCostService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index()
    {
        $query = DB::table('production_entries as pe')
            ->leftJoin('users as u', 'u.id', '=', 'pe.created_by');

        if (!is_all_branches()) {
            $query->where('pe.branch_id', active_branch_id());
        }

        $entries = $query->select('pe.*', 'u.name as user_name')
            ->orderBy('pe.created_at', 'desc')
            ->get();

        if ($entries->isNotEmpty()) {
            $entryIds = $entries->pluck('id');
            $items = DB::table('production_entry_items as pei')
                ->leftJoin('products as p', 'p.id', '=', 'pei.product_id')
                ->leftJoin('product_variants as pv', 'pv.id', '=', 'pei.variant_id')
                ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
                ->whereIn('pei.production_entry_id', $entryIds)
                ->select('pei.*', 'p.item_name', 'p.unit_type', 'u.name as unit_name', 'pv.size_label', 'pv.variant_name', 'pv.size_value')
                ->get()
                ->groupBy('production_entry_id');

            foreach ($entries as $entry) {
                $entryItems = $items->get($entry->id, collect());
                $entry->items_count = $entryItems->count();

                $details = [];
                foreach ($entryItems as $item) {
                    $pName = $item->item_name ?? 'Unknown';
                    $vName = $item->size_label ?: $item->variant_name;
                    $unitName = $item->unit ?? $item->unit_name ?? 'Pc';

                    $isKg = ($item->unit_type === 'kg') || str_contains(strtolower($unitName), 'kg');

                    if ($isKg) {
                        $totalKg = floatval($item->qty_stock) / 1000;
                        $formattedQty = ($totalKg == (int)$totalKg) ? number_format($totalKg, 0) : rtrim(rtrim(number_format($totalKg, 3), '0'), '.');
                        $displayUnit = 'KG';
                    } else {
                        $qty = floatval($item->qty_stock);
                        $formattedQty = ($qty == (int)$qty) ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3), '0'), '.');
                        $displayUnit = $unitName;
                    }

                    $str = $pName;
                    if (!empty($vName)) {
                        $str .= ' ' . $vName;
                    }
                    $str .= ' (' . $formattedQty . ' ' . $displayUnit . ')';
                    $details[] = $str;
                }

                $entry->product_details = implode(', ', $details);
            }
        }

        return view('admin_panel.production.index', compact('entries'));
    }

    public function create()
    {
        $products = Product::with(['unit', 'variants'])->orderBy('item_name')->get();
        $this->attachResolvedLabourCosts($products);
        $rawMaterials = RawMaterial::with('stock')->orderBy('name')->get();
        return view('admin_panel.production.create', compact('products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_date' => 'required|date',
            'product_id' => 'required|array',
            'qty' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $currentBranchId = active_branch_id();
            $targetWarehouseId = !empty($request->warehouse_id) ? (int)$request->warehouse_id : null;

            $entryId = DB::table('production_entries')->insertGetId([
                'branch_id' => $currentBranchId,
                'entry_no' => 'PROD-' . date('Ymd-His'),
                'production_date' => $request->production_date,
                'source' => $request->source ?? 'kitchen',
                'warehouse_id' => $targetWarehouseId,
                'notes' => $request->notes,
                'production_cost' => 0,
                'raw_material_cost' => 0,
                'labour_cost' => 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $costs = $this->syncProductionItemsAndMaterials($entryId, $request, $currentBranchId, $targetWarehouseId);

            DB::table('production_entries')->where('id', $entryId)->update([
                'raw_material_cost' => $costs['raw_material_cost'],
                'labour_cost' => $costs['labour_cost'],
                'production_cost' => $costs['production_cost'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Production entry saved with raw material & labour costing!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Error: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $entry = DB::table('production_entries')->where('id', $id)->first();
        if (!$entry) abort(404);

        $items = DB::table('production_entry_items as pei')
            ->leftJoin('products as p', 'p.id', '=', 'pei.product_id')
            ->where('pei.production_entry_id', $id)
            ->select('pei.*', 'p.item_name', 'p.item_code', 'p.unit_type')
            ->get();

        $rawMaterialUsage = DB::table('production_raw_material_usage as rmu')
            ->leftJoin('raw_materials as rm', 'rm.id', '=', 'rmu.raw_material_id')
            ->where('rmu.production_entry_id', $id)
            ->select('rmu.*', 'rm.conversion_factor')
            ->get();

        $products = Product::with(['unit', 'variants'])->orderBy('item_name')->get();
        $this->attachResolvedLabourCosts($products);
        $rawMaterials = RawMaterial::with('stock')->orderBy('name')->get();

        return view('admin_panel.production.edit', compact('entry', 'items', 'products', 'rawMaterials', 'rawMaterialUsage'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'production_date' => 'required|date',
            'product_id' => 'required|array',
            'qty' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            $currentBranchId = active_branch_id();
            $targetWarehouseId = !empty($request->warehouse_id) ? (int)$request->warehouse_id : null;

            // Fetch old production entry header BEFORE update to get old warehouse_id & branch_id
            $oldEntryHeader = DB::table('production_entries')->where('id', $id)->first();
            $oldWarehouseId = $oldEntryHeader ? $oldEntryHeader->warehouse_id : null;
            $oldBranchId = $oldEntryHeader ? ($oldEntryHeader->branch_id ?? $currentBranchId) : $currentBranchId;

            // 1. Reverse old finished goods stock from OLD warehouse/branch location
            $oldItems = DB::table('production_entry_items')->where('production_entry_id', $id)->get();
            foreach ($oldItems as $oi) {
                $stockQuery = Stock::where('product_id', $oi->product_id)
                    ->where('branch_id', $oldBranchId);

                if (!empty($oldWarehouseId)) {
                    $stockQuery->where('warehouse_id', $oldWarehouseId);
                } else {
                    $stockQuery->whereNull('warehouse_id');
                }

                if ($oi->variant_id) {
                    $stockQuery->where('variant_id', $oi->variant_id);
                    $deductStock = (float)$oi->qty_entered;
                } else {
                    $stockQuery->whereNull('variant_id');
                    $deductStock = (float)$oi->qty_stock;
                }

                $stock = $stockQuery->first();
                if ($stock) {
                    $stock->qty -= $deductStock;
                    $stock->save();
                }
            }

            // 2. Reverse old raw material / product ingredient stock from OLD warehouse/branch location
            $oldRmUsage = DB::table('production_raw_material_usage')->where('production_entry_id', $id)->get();
            foreach ($oldRmUsage as $rmu) {
                if ($rmu->ingredient_product_id) {
                    $pModel = Product::find($rmu->ingredient_product_id);
                    $isKg = $pModel && ($pModel->unit_type === 'kg');
                    $addStockQty = $isKg ? ($rmu->qty_used * 1000) : $rmu->qty_used;

                    $prodStockQuery = Stock::where('product_id', $rmu->ingredient_product_id)
                        ->where('branch_id', $oldBranchId)
                        ->whereNull('variant_id');

                    if (!empty($oldWarehouseId)) {
                        $prodStockQuery->where('warehouse_id', $oldWarehouseId);
                    } else {
                        $prodStockQuery->whereNull('warehouse_id');
                    }

                    $prodStock = $prodStockQuery->first();
                    if ($prodStock) {
                        $prodStock->qty += $addStockQty;
                        $prodStock->save();
                    } else {
                        Stock::create([
                            'product_id' => $rmu->ingredient_product_id,
                            'branch_id' => $oldBranchId,
                            'warehouse_id' => $oldWarehouseId,
                            'variant_id' => null,
                            'qty' => $addStockQty,
                        ]);
                    }
                } elseif ($rmu->raw_material_id) {
                    if (!empty($oldWarehouseId)) {
                        $rmStock = RawMaterialStock::firstOrCreate(
                            [
                                'raw_material_id' => $rmu->raw_material_id,
                                'warehouse_id' => $oldWarehouseId,
                            ],
                            [
                                'branch_id' => $oldBranchId,
                                'qty' => 0
                            ]
                        );
                    } else {
                        $rmStock = RawMaterialStock::firstOrCreate(
                            [
                                'raw_material_id' => $rmu->raw_material_id,
                                'warehouse_id' => null,
                                'branch_id' => $oldBranchId,
                            ],
                            ['qty' => 0]
                        );
                    }
                    $rmStock->qty += $rmu->qty_used;
                    $rmStock->save();
                }
            }

            // 3. Delete old items + raw material usage
            DB::table('production_entry_items')->where('production_entry_id', $id)->delete();
            DB::table('production_raw_material_usage')->where('production_entry_id', $id)->delete();

            // 4. Re-insert items, RM consumption & compute RM + Labour costs
            $costs = $this->syncProductionItemsAndMaterials((int)$id, $request, $currentBranchId, $targetWarehouseId);

            // 5. Update entry header
            DB::table('production_entries')->where('id', $id)->update([
                'production_date' => $request->production_date,
                'source' => $request->source ?? 'kitchen',
                'warehouse_id' => $targetWarehouseId,
                'notes' => $request->notes,
                'raw_material_cost' => $costs['raw_material_cost'],
                'labour_cost' => $costs['labour_cost'],
                'production_cost' => $costs['production_cost'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Production entry updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    private function attachResolvedLabourCosts($products): void
    {
        $costService = app(ProductCostService::class);
        foreach ($products as $p) {
            $p->resolved_labour_cost = $costService->resolveLabourCost($p, null);
            foreach ($p->variants as $v) {
                $v->resolved_labour_cost = $costService->resolveLabourCost($p, $v);
            }
        }
    }

    /**
     * Persist finished goods, raw material usage, and per-item/batch RM + labour costs.
     */
    private function syncProductionItemsAndMaterials(int $entryId, Request $request, $currentBranchId, ?int $targetWarehouseId): array
    {
        $costService = app(ProductCostService::class);
        $totalItemLegacyCost = 0.0;
        if ($request->has('item_cost')) {
            foreach ((array)$request->item_cost as $ic) {
                $totalItemLegacyCost += max(0, (float)($ic ?? 0));
            }
        }

        // 1. Insert raw material / product ingredient usage & deduct stock, summing total RM cost
        $totalRmCost = 0.0;
        if ($request->has('rm_id')) {
            foreach ($request->rm_id as $ri => $rmId) {
                if (!$rmId) continue;
                $qtyUsed = (float)($request->rm_qty[$ri] ?? 0);
                if ($qtyUsed <= 0) continue;

                $costPerUnit = (float)($request->rm_cost[$ri] ?? 0);
                $rmType = $request->rm_type[$ri] ?? 'rm';
                if ($costPerUnit <= 0) {
                    if ($rmType === 'product') {
                        $pModel = Product::find($rmId);
                        $costPerUnit = (float)($pModel->price ?? 0);
                    } else {
                        $rmModel = RawMaterial::find($rmId);
                        $costPerUnit = $rmModel ? (float)$rmModel->lastPurchaseCost() : 0;
                    }
                }
                $factor = isset($request->rm_factor[$ri]) ? (float)$request->rm_factor[$ri] : 1;
                if ($factor <= 0) $factor = 1;
                $rowRmCost = $qtyUsed * ($costPerUnit / $factor);
                $totalRmCost += $rowRmCost;

                if ($rmType === 'product') {
                    DB::table('production_raw_material_usage')->insert([
                        'production_entry_id' => $entryId,
                        'raw_material_id' => null,
                        'ingredient_product_id' => $rmId,
                        'qty_used' => $qtyUsed,
                        'cost_per_unit' => $costPerUnit,
                        'total_cost' => $rowRmCost,
                        'branch_id' => $currentBranchId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $pModel = Product::find($rmId);
                    $isKg = $pModel && ($pModel->unit_type === 'kg');
                    $deductStockQty = $isKg ? ($qtyUsed * 1000) : $qtyUsed;

                    $prodStock = Stock::firstOrCreate(
                        [
                            'product_id' => $rmId,
                            'branch_id' => $currentBranchId,
                            'warehouse_id' => $targetWarehouseId,
                            'variant_id' => null,
                        ],
                        ['qty' => 0]
                    );
                    $prodStock->qty -= $deductStockQty;
                    $prodStock->save();
                } else {
                    DB::table('production_raw_material_usage')->insert([
                        'production_entry_id' => $entryId,
                        'raw_material_id' => $rmId,
                        'ingredient_product_id' => null,
                        'qty_used' => $qtyUsed,
                        'cost_per_unit' => $costPerUnit,
                        'total_cost' => $rowRmCost,
                        'branch_id' => $currentBranchId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($targetWarehouseId) {
                        $rmStock = RawMaterialStock::firstOrCreate(
                            [
                                'raw_material_id' => $rmId,
                                'warehouse_id' => $targetWarehouseId,
                            ],
                            [
                                'branch_id' => $currentBranchId,
                                'qty' => 0
                            ]
                        );
                    } else {
                        $rmStock = RawMaterialStock::firstOrCreate(
                            [
                                'raw_material_id' => $rmId,
                                'warehouse_id' => null,
                                'branch_id' => $currentBranchId,
                            ],
                            ['qty' => 0]
                        );
                    }
                    $rmStock->qty -= $qtyUsed;
                    $rmStock->save();
                }
            }
        }

        // 2. Collect produced finished items, calculate labour costs, and update finished stock
        $preparedItems = [];
        $totalLabourCost = 0.0;

        foreach ($request->product_id as $index => $productId) {
            $qtyTyped = (float)($request->qty[$index] ?? 0);
            if ($qtyTyped <= 0) continue;

            $variantId = $request->variant_id[$index] ?? null;
            if ($variantId === '') $variantId = null;

            $product = Product::with(['unit', 'bom.rawMaterial', 'bom.ingredientProduct', 'variants'])->find($productId);
            if (!$product) continue;

            $vModel = $variantId ? ProductVariant::find($variantId) : null;

            $unitName = strtolower($product->unit->name ?? '');
            $prodName = strtolower($product->item_name);
            $isGram = str_contains($unitName, 'gram') || str_contains($unitName, 'gm') ||
                      str_contains($prodName, 'gram') || str_contains($prodName, ' gm') ||
                      $product->unit_type === 'kg';

            $qtyStock = $qtyTyped;
            $dbVariantId = $variantId;

            if ($isGram) {
                if ($vModel) {
                    $kgSize = floatval($vModel->size_value);
                    if ($vModel->size_unit === 'kg') {
                        $qtyStock = ($kgSize * $qtyTyped * 1000);
                    } else {
                        $qtyStock = ($kgSize * $qtyTyped);
                    }
                } else {
                    $qtyStock = $qtyTyped * 1000;
                }
            }

            // Resolve labour cost (form input override -> default configured rate)
            $hasPerPcInput = $request->has("item_labour_per_pc.$index") && $request->item_labour_per_pc[$index] !== null && $request->item_labour_per_pc[$index] !== '';
            $hasTotalInput = $request->has("item_labour_total.$index") && $request->item_labour_total[$index] !== null && $request->item_labour_total[$index] !== '';

            $labourPerPc = $hasPerPcInput
                ? max(0, round((float)$request->item_labour_per_pc[$index], 2))
                : $costService->resolveLabourCost($product, $vModel);

            if ($hasTotalInput) {
                $itemLabourTotal = max(0, round((float)$request->item_labour_total[$index], 2));
                $labourPerPc = $qtyTyped > 0 ? round($itemLabourTotal / $qtyTyped, 2) : $labourPerPc;
            } else {
                $itemLabourTotal = round($qtyTyped * $labourPerPc, 2);
            }
            $totalLabourCost += $itemLabourTotal;

            // Compute expected BOM RM weight for proportional RM allocation across multiple items in one batch
            $bomWeight = 0.0;
            $breakdown = $costService->buildBomCostBreakdown(collect([$product]));
            $vKey = $product->id . '_' . ($variantId ?: 0);
            $pKey = $product->id . '_0';
            $unitBomMat = (float)($breakdown['material'][$vKey] ?? $breakdown['material'][$pKey] ?? 0);
            if ($unitBomMat > 0) {
                $bomWeight = $unitBomMat * $qtyTyped;
            }

            $preparedItems[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'unit' => $product->unit->name ?? ($product->unit_type ? strtoupper($product->unit_type) : 'Pc'),
                'qty_entered' => $qtyTyped,
                'qty_stock' => $qtyStock,
                'labour_cost_per_pc' => $labourPerPc,
                'total_labour_cost' => $itemLabourTotal,
                'notes' => $request->item_note[$index] ?? null,
                'bom_weight' => $bomWeight,
            ];

            // Update Stock (target selected warehouse, not always shop)
            $qtyToAdd = $qtyTyped;
            if (!$dbVariantId && $isGram) {
                $qtyToAdd = $qtyStock; // in grams for loose product
            }

            $stockQuery = Stock::where('product_id', $productId)
                ->where('branch_id', $currentBranchId)
                ->where('warehouse_id', $targetWarehouseId);

            if ($dbVariantId) {
                $stockQuery->where('variant_id', $dbVariantId);
            } else {
                $stockQuery->whereNull('variant_id');
            }

            $stock = $stockQuery->first();
            if ($stock) {
                $stock->qty += $qtyToAdd;
                $stock->save();
            } else {
                Stock::create([
                    'product_id' => $productId,
                    'variant_id' => $dbVariantId,
                    'branch_id' => $currentBranchId,
                    'warehouse_id' => $targetWarehouseId,
                    'qty' => $qtyToAdd,
                ]);
            }
        }

        // 3. Allocate batch RM cost across prepared items and insert production_entry_items
        $totalRmRounded = round($totalRmCost, 2);
        $totalBomWeight = array_sum(array_column($preparedItems, 'bom_weight'));
        $totalQtyWeight = array_sum(array_column($preparedItems, 'qty_entered'));
        $itemCount = count($preparedItems);
        $allocatedSum = 0.0;

        foreach ($preparedItems as $idx => $pi) {
            if ($idx === $itemCount - 1) {
                $rmShare = round($totalRmRounded - $allocatedSum, 2);
            } elseif ($totalBomWeight > 0) {
                $rmShare = round($totalRmRounded * ($pi['bom_weight'] / $totalBomWeight), 2);
                $allocatedSum += $rmShare;
            } elseif ($totalQtyWeight > 0) {
                $rmShare = round($totalRmRounded * ($pi['qty_entered'] / $totalQtyWeight), 2);
                $allocatedSum += $rmShare;
            } else {
                $rmShare = 0.0;
            }

            DB::table('production_entry_items')->insert([
                'production_entry_id' => $entryId,
                'product_id' => $pi['product_id'],
                'variant_id' => $pi['variant_id'],
                'branch_id' => $currentBranchId,
                'unit' => $pi['unit'],
                'qty_entered' => $pi['qty_entered'],
                'qty_stock' => $pi['qty_stock'],
                'labour_cost_per_pc' => $pi['labour_cost_per_pc'],
                'total_labour_cost' => $pi['total_labour_cost'],
                'rm_cost' => $rmShare,
                'total_cost' => round($rmShare + $pi['total_labour_cost'], 2),
                'notes' => $pi['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $totalLabourRounded = round($totalLabourCost, 2);
        $totalProdCost = round($totalRmRounded + $totalLabourRounded + $totalItemLegacyCost, 2);

        return [
            'raw_material_cost' => $totalRmRounded,
            'labour_cost' => $totalLabourRounded,
            'production_cost' => $totalProdCost,
        ];
    }

    public function gatepass($id)
    {
        $entry = DB::table('production_entries as pe')
            ->leftJoin('users as u', 'u.id', '=', 'pe.created_by')
            ->where('pe.id', $id)
            ->select('pe.*', 'u.name as user_name')
            ->first();

        if (!$entry) abort(404);

        $items = DB::table('production_entry_items as pei')
            ->leftJoin('products as p', 'p.id', '=', 'pei.product_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'pei.variant_id')
            ->where('pei.production_entry_id', $id)
            ->select('pei.*', 'p.item_name', 'p.item_code', 'p.unit_type', 'pv.size_label', 'pv.variant_name')
            ->get();

        $rawMaterialUsage = DB::table('production_raw_material_usage as rmu')
            ->leftJoin('raw_materials as rm', 'rm.id', '=', 'rmu.raw_material_id')
            ->leftJoin('products as p', 'p.id', '=', 'rmu.ingredient_product_id')
            ->where('rmu.production_entry_id', $id)
            ->select('rmu.*', 'rm.name as rm_name', 'rm.unit', 'p.item_name as p_name', 'p.item_code as p_code')
            ->get();

        return view('admin_panel.production.gatepass', compact('entry', 'items', 'rawMaterialUsage'));
    }

    public function getBomRawMaterials($id, Request $request)
    {
        $variantId = $request->query('variant_id');

        $bom = collect();
        if (!empty($variantId)) {
            $bom = ProductRawMaterialBom::with(['rawMaterial', 'ingredientProduct'])
                ->where('product_id', $id)
                ->where('variant_id', $variantId)
                ->get();
        }

        $isCustomVariantBom = true;
        if ($bom->isEmpty()) {
            $isCustomVariantBom = false;
            $bom = ProductRawMaterialBom::with(['rawMaterial', 'ingredientProduct'])
                ->where('product_id', $id)
                ->whereNull('variant_id')
                ->get();
        }

        foreach ($bom as $item) {
            $item->is_custom_variant_bom = $isCustomVariantBom;
            if ($item->rawMaterial) {
                $purchaseCost = (float)$item->rawMaterial->lastPurchaseCost();
                $factor = (float)($item->rawMaterial->conversion_factor ?? 1);
                $item->conversion_factor = $factor > 0 ? $factor : 1;
                $item->purchase_cost = $purchaseCost;
                $item->unit_cost = $purchaseCost;
            } elseif ($item->ingredientProduct) {
                $item->conversion_factor = 1;
                $item->purchase_cost = (float)($item->ingredientProduct->price ?? 0);
                $item->unit_cost = (float)($item->ingredientProduct->price ?? 0);
            } else {
                $item->conversion_factor = 1;
                $item->purchase_cost = 0;
                $item->unit_cost = 0;
            }
        }
        return response()->json($bom);
    }
}
