<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\ProductRawMaterialBom;
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
        $products = Product::with('unit')->orderBy('item_name')->get();
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

            // Calculate total production cost from items
            $totalItemCost = 0;
            $itemCosts = [];
            if ($request->has('item_cost')) {
                foreach ($request->item_cost as $ic) {
                    $itemCosts[] = (float)($ic ?? 0);
                }
                $totalItemCost = array_sum($itemCosts);
            }

            // Calculate total raw material cost used
            $totalRmCost = 0;
            if ($request->has('rm_id')) {
                foreach ($request->rm_id as $ri => $rmId) {
                    if ($rmId && isset($request->rm_qty[$ri]) && (float)$request->rm_qty[$ri] > 0) {
                        $costPerUnit = (float)($request->rm_cost[$ri] ?? 0);
                        $qtyUsed = (float)$request->rm_qty[$ri];
                        $totalRmCost += $costPerUnit * $qtyUsed;
                    }
                }
            }

            $totalProductionCost = $totalItemCost + $totalRmCost;
            $currentBranchId = active_branch_id();

            $entryId = DB::table('production_entries')->insertGetId([
                'branch_id' => $currentBranchId,
                'entry_no' => 'PROD-' . date('Ymd-His'),
                'production_date' => $request->production_date,
                'source' => $request->source ?? 'kitchen',
                'notes' => $request->notes,
                'production_cost' => $totalProductionCost,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert production items and update finished goods stock
            foreach ($request->product_id as $index => $productId) {
                $qtyTyped = (float)($request->qty[$index] ?? 0);
                if ($qtyTyped <= 0) continue;

                $variantId = $request->variant_id[$index] ?? null;
                if ($variantId === '') $variantId = null;

                $product = Product::with('unit')->find($productId);
                if (!$product) continue;

                $unitName = strtolower($product->unit->name ?? '');
                $prodName = strtolower($product->item_name);
                $isGram = str_contains($unitName, 'gram') || str_contains($unitName, 'gm') ||
                          str_contains($prodName, 'gram') || str_contains($prodName, ' gm') ||
                          $product->unit_type === 'kg';

                $qtyStock = $qtyTyped;
                $dbVariantId = $variantId;

                if ($isGram) {
                    $dbVariantId = null;
                    if ($variantId) {
                        $vModel = \App\Models\ProductVariant::find($variantId);
                        if ($vModel) {
                            $kgSize = floatval($vModel->size_value);
                            if ($vModel->size_unit === 'kg') {
                                $qtyStock = ($kgSize * $qtyTyped * 1000);
                            } else {
                                $qtyStock = ($kgSize * $qtyTyped);
                            }
                        }
                    } else {
                        $qtyStock = $qtyTyped * 1000;
                    }
                }

                DB::table('production_entry_items')->insert([
                    'production_entry_id' => $entryId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'unit' => $product->unit->name ?? ($product->unit_type ? strtoupper($product->unit_type) : 'Pc'),
                    'qty_entered' => $qtyTyped,
                    'qty_stock' => $qtyStock,
                    'notes' => $request->item_note[$index] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update Stock
                $stockQuery = Stock::where('product_id', $productId)
                    ->where('branch_id', $currentBranchId)
                    ->where('warehouse_id', 1);

                if ($dbVariantId) {
                    $stockQuery->where('variant_id', $dbVariantId);
                } else {
                    $stockQuery->whereNull('variant_id');
                }

                $stock = $stockQuery->first();

                if ($stock) {
                    $stock->qty += $qtyStock;
                    $stock->save();
                } else {
                    Stock::create([
                        'product_id' => $productId,
                        'variant_id' => $dbVariantId,
                        'branch_id' => $currentBranchId,
                        'warehouse_id' => 1,
                        'qty' => $qtyStock,
                    ]);
                }
            }

            // Insert raw material / product ingredient usage and deduct stock
            if ($request->has('rm_id')) {
                foreach ($request->rm_id as $ri => $rmId) {
                    if (!$rmId) continue;
                    $qtyUsed = (float)($request->rm_qty[$ri] ?? 0);
                    if ($qtyUsed <= 0) continue;

                    $costPerUnit = (float)($request->rm_cost[$ri] ?? 0);
                    $totalCost = $qtyUsed * $costPerUnit;
                    $rmType = $request->rm_type[$ri] ?? 'rm';

                    if ($rmType === 'product') {
                        DB::table('production_raw_material_usage')->insert([
                            'production_entry_id' => $entryId,
                            'raw_material_id' => null,
                            'ingredient_product_id' => $rmId,
                            'qty_used' => $qtyUsed,
                            'cost_per_unit' => $costPerUnit,
                            'total_cost' => $totalCost,
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
                                'warehouse_id' => 1,
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
                            'total_cost' => $totalCost,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $rmStock = RawMaterialStock::where('raw_material_id', $rmId)
                            ->where(function($q) {
                                $q->where('warehouse_id', 1)->orWhereNull('warehouse_id');
                            })
                            ->orderByRaw('warehouse_id DESC')
                            ->first();

                        if (!$rmStock) {
                            $rmStock = RawMaterialStock::firstOrCreate(
                                ['raw_material_id' => $rmId],
                                ['qty' => 0]
                            );
                        }
                        $rmStock->qty -= $qtyUsed;
                        $rmStock->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Production entry saved with raw material consumption!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
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

        $rawMaterialUsage = DB::table('production_raw_material_usage')
            ->where('production_entry_id', $id)
            ->get();

        $products = Product::with('unit')->orderBy('item_name')->get();
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

            // 1. Reverse old finished goods stock
            $oldItems = DB::table('production_entry_items')->where('production_entry_id', $id)->get();
            foreach ($oldItems as $oi) {
                $oldProduct = Product::find($oi->product_id);
                $oldIsGram = $oldProduct && ($oldProduct->unit_type === 'kg' || str_contains(strtolower($oldProduct->item_name), 'gram'));

                $stockQuery = Stock::where('product_id', $oi->product_id)
                    ->where('branch_id', $currentBranchId)
                    ->where('warehouse_id', 1);

                if ($oldIsGram || !$oi->variant_id) {
                    $stockQuery->whereNull('variant_id');
                } else {
                    $stockQuery->where('variant_id', $oi->variant_id);
                }

                $stock = $stockQuery->first();
                if ($stock) {
                    $stock->qty -= $oi->qty_stock;
                    $stock->save();
                }
            }

            // 2. Reverse old raw material / product ingredient stock
            $oldRmUsage = DB::table('production_raw_material_usage')->where('production_entry_id', $id)->get();
            foreach ($oldRmUsage as $rmu) {
                if ($rmu->ingredient_product_id) {
                    $pModel = Product::find($rmu->ingredient_product_id);
                    $isKg = $pModel && ($pModel->unit_type === 'kg');
                    $addStockQty = $isKg ? ($rmu->qty_used * 1000) : $rmu->qty_used;

                    $prodStock = Stock::firstOrCreate(
                        [
                            'product_id' => $rmu->ingredient_product_id,
                            'branch_id' => $currentBranchId,
                            'warehouse_id' => 1,
                            'variant_id' => null,
                        ],
                        ['qty' => 0]
                    );
                    $prodStock->qty += $addStockQty;
                    $prodStock->save();
                } else if ($rmu->raw_material_id) {
                    $rmStock = RawMaterialStock::where('raw_material_id', $rmu->raw_material_id)
                        ->where(function($q) {
                            $q->where('warehouse_id', 1)->orWhereNull('warehouse_id');
                        })
                        ->orderByRaw('warehouse_id DESC')
                        ->first();

                    if (!$rmStock) {
                        $rmStock = RawMaterialStock::firstOrCreate(
                            ['raw_material_id' => $rmu->raw_material_id],
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

            // Calculate costs
            $totalItemCost = 0;
            if ($request->has('item_cost')) {
                foreach ($request->item_cost as $ic) {
                    $totalItemCost += (float)($ic ?? 0);
                }
            }
            $totalRmCost = 0;
            if ($request->has('rm_id')) {
                foreach ($request->rm_id as $ri => $rmId) {
                    if ($rmId && isset($request->rm_qty[$ri]) && (float)$request->rm_qty[$ri] > 0) {
                        $costPerUnit = (float)($request->rm_cost[$ri] ?? 0);
                        $qtyUsed = (float)$request->rm_qty[$ri];
                        $totalRmCost += $costPerUnit * $qtyUsed;
                    }
                }
            }

            // 4. Update entry header
            DB::table('production_entries')->where('id', $id)->update([
                'production_date' => $request->production_date,
                'source' => $request->source ?? 'kitchen',
                'notes' => $request->notes,
                'production_cost' => $totalItemCost + $totalRmCost,
                'updated_at' => now(),
            ]);

            // 5. Insert new items + update stock
            foreach ($request->product_id as $index => $productId) {
                $qtyTyped = (float)($request->qty[$index] ?? 0);
                if ($qtyTyped <= 0) continue;

                $variantId = $request->variant_id[$index] ?? null;
                if ($variantId === '') $variantId = null;

                $product = Product::with('unit')->find($productId);
                if (!$product) continue;

                $unitName = strtolower($product->unit->name ?? '');
                $prodName = strtolower($product->item_name);
                $isGram = str_contains($unitName, 'gram') || str_contains($unitName, 'gm') ||
                          str_contains($prodName, 'gram') || str_contains($prodName, ' gm') ||
                          $product->unit_type === 'kg';

                $qtyStock = $qtyTyped;
                $dbVariantId = $variantId;

                if ($isGram) {
                    $dbVariantId = null;
                    if ($variantId) {
                        $vModel = \App\Models\ProductVariant::find($variantId);
                        if ($vModel) {
                            $kgSize = floatval($vModel->size_value);
                            if ($vModel->size_unit === 'kg') {
                                $qtyStock = ($kgSize * $qtyTyped * 1000);
                            } else {
                                $qtyStock = ($kgSize * $qtyTyped);
                            }
                        }
                    } else {
                        $qtyStock = $qtyTyped * 1000;
                    }
                }

                DB::table('production_entry_items')->insert([
                    'production_entry_id' => $id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'unit' => $product->unit->name ?? ($product->unit_type ? strtoupper($product->unit_type) : 'Pc'),
                    'qty_entered' => $qtyTyped,
                    'qty_stock' => $qtyStock,
                    'notes' => $request->item_note[$index] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $stockQuery = Stock::where('product_id', $productId)
                    ->where('branch_id', $currentBranchId)
                    ->where('warehouse_id', 1);

                if ($dbVariantId) {
                    $stockQuery->where('variant_id', $dbVariantId);
                } else {
                    $stockQuery->whereNull('variant_id');
                }

                $stock = $stockQuery->first();
                if ($stock) {
                    $stock->qty += $qtyStock;
                    $stock->save();
                } else {
                    Stock::create([
                        'product_id' => $productId,
                        'variant_id' => $dbVariantId,
                        'branch_id' => $currentBranchId,
                        'warehouse_id' => 1,
                        'qty' => $qtyStock,
                    ]);
                }
            }

            // 6. Insert raw material / product ingredient usage + deduct stock
            if ($request->has('rm_id')) {
                foreach ($request->rm_id as $ri => $rmId) {
                    if (!$rmId) continue;
                    $qtyUsed = (float)($request->rm_qty[$ri] ?? 0);
                    if ($qtyUsed <= 0) continue;

                    $costPerUnit = (float)($request->rm_cost[$ri] ?? 0);
                    $totalCost = $qtyUsed * $costPerUnit;
                    $rmType = $request->rm_type[$ri] ?? 'rm';

                    if ($rmType === 'product') {
                        DB::table('production_raw_material_usage')->insert([
                            'production_entry_id' => $id,
                            'raw_material_id' => null,
                            'ingredient_product_id' => $rmId,
                            'qty_used' => $qtyUsed,
                            'cost_per_unit' => $costPerUnit,
                            'total_cost' => $totalCost,
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
                                'warehouse_id' => 1,
                                'variant_id' => null,
                            ],
                            ['qty' => 0]
                        );

                        $prodStock->qty -= $deductStockQty;
                        $prodStock->save();
                    } else {
                        DB::table('production_raw_material_usage')->insert([
                            'production_entry_id' => $id,
                            'raw_material_id' => $rmId,
                            'ingredient_product_id' => null,
                            'qty_used' => $qtyUsed,
                            'cost_per_unit' => $costPerUnit,
                            'total_cost' => $totalCost,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $rmStock = RawMaterialStock::where('raw_material_id', $rmId)
                            ->where(function($q) {
                                $q->where('warehouse_id', 1)->orWhereNull('warehouse_id');
                            })
                            ->orderByRaw('warehouse_id DESC')
                            ->first();

                        if (!$rmStock) {
                            $rmStock = RawMaterialStock::firstOrCreate(
                                ['raw_material_id' => $rmId],
                                ['qty' => 0]
                            );
                        }
                        $rmStock->qty -= $qtyUsed;
                        $rmStock->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Production entry updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
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
                $item->unit_cost = $item->rawMaterial->lastPurchaseCost();
            } elseif ($item->ingredientProduct) {
                $item->unit_cost = (float)($item->ingredientProduct->price ?? 0);
            } else {
                $item->unit_cost = 0;
            }
        }
        return response()->json($bom);
    }
}
