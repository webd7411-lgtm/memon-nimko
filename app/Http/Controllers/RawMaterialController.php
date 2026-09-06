<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialPurchase;
use App\Models\RawMaterialPurchaseItem;
use App\Models\RawMaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function active_branch_id;
use function is_all_branches;

class RawMaterialController extends Controller
{
    // ==================== RAW MATERIALS CRUD ====================
    public function index()
    {
        $branchId = is_all_branches() ? null : active_branch_id();

        $materialsQuery = RawMaterial::with(['stocks' => function($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }, 'stocks.warehouse']);

        $materials = $materialsQuery->orderBy('name')->get();

        $purchasesQuery = RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch']);
        if (!is_all_branches()) {
            $purchasesQuery->where('branch_id', active_branch_id());
        }
        $purchases = $purchasesQuery->orderBy('date', 'desc')->orderBy('id', 'desc')->take(50)->get();

        $vendors = \App\Models\Vendor::orderBy('name')->get(['id', 'name']);
        $warehouses = \App\Models\Warehouse::orderBy('warehouse_name')->get(['id', 'warehouse_name']);

        $productsWithBom = \App\Models\Product::has('bom')->with(['bom.rawMaterial'])->orderBy('item_name')->get();

        return view('admin_panel.raw_material.index', compact('materials', 'purchases', 'vendors', 'warehouses', 'productsWithBom'));
    }

    public function storeRawMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'urdu_name' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'alert_qty' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if ($request->edit_id) {
            $material = RawMaterial::find($request->edit_id);
            $msg = ['success' => 'Raw Material Updated Successfully', 'reload' => true];
        } else {
            $material = new RawMaterial();
            $msg = ['success' => 'Raw Material Created Successfully', 'reload' => true];
        }

        $material->name = $request->name;
        $material->urdu_name = $request->urdu_name;
        $material->unit = $request->unit;
        $material->consumption_unit = $request->consumption_unit ?? $request->unit;
        $material->conversion_factor = number_format((float)($request->conversion_factor ?? 1) > 0 ? (float)$request->conversion_factor : 1, 0, '.', '');
        $material->alert_qty = $request->alert_qty ?? 0;
        $material->notes = $request->notes;
        $material->save();

        // Save initial / updated purchase price
        if ($request->has('initial_price')) {
            $price = (float)$request->initial_price;
            $latestItem = RawMaterialPurchaseItem::where('raw_material_id', $material->id)->latest('id')->first();
            if ($latestItem) {
                $latestItem->price_per_unit = $price;
                $latestItem->total = $latestItem->qty * $price;
                $latestItem->save();
            } elseif ($price > 0) {
                $purchase = RawMaterialPurchase::create([
                    'date' => date('Y-m-d'),
                    'invoice_no' => 'INIT-RM-' . $material->id,
                    'vendor_name' => 'Initial Cost Setup',
                    'total_cost' => 0,
                    'notes' => 'Auto-generated for initial raw material cost setup',
                    'created_by' => Auth::id(),
                    'branch_id' => is_all_branches() ? null : active_branch_id(),
                ]);
                RawMaterialPurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $material->id,
                    'qty' => 0,
                    'price_per_unit' => $price,
                    'total' => 0,
                    'branch_id' => is_all_branches() ? null : active_branch_id(),
                ]);
            }
        }

        // Ensure stock record exists with opening stock (converted to consumption units)
        $branchId = is_all_branches() ? null : active_branch_id();
        $openingQty = (float)($request->opening_stock ?? 0);
        $factor = (float)($material->conversion_factor ?? 1);
        $openingQtyInConsumptionUnit = $openingQty * ($factor > 0 ? $factor : 1);
        $stockRecord = RawMaterialStock::firstOrCreate(
            [
                'raw_material_id' => $material->id,
                'branch_id' => $branchId,
            ],
            ['qty' => $openingQtyInConsumptionUnit]
        );
        // If opening stock provided, update it
        if ($openingQty > 0) {
            $stockRecord->qty = $openingQtyInConsumptionUnit;
            $stockRecord->branch_id = $branchId;
            $stockRecord->save();
        }

        return response()->json($msg);
    }

    public function deleteRawMaterial($id)
    {
        $material = RawMaterial::find($id);
        if ($material) {
            $material->delete();
            return response()->json(['success' => 'Raw Material Deleted Successfully', 'reload' => route('raw-materials.index')]);
        }
        return response()->json(['error' => 'Not Found']);
    }

    // ==================== RAW MATERIAL PURCHASES ====================
    public function storePurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'raw_material_id' => 'required|array',
            'raw_material_id.*' => 'required|exists:raw_materials,id',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0.01',
            'price_per_unit' => 'required|array',
            'price_per_unit.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        try {
            $branchId = is_all_branches() ? null : active_branch_id();
            DB::beginTransaction();

            $totalCost = 0;
            $itemsData = [];

            foreach ($request->raw_material_id as $i => $rmId) {
                $qty = (float)($request->qty[$i] ?? 0);
                $price = (float)($request->price_per_unit[$i] ?? 0);
                $total = $qty * $price;
                $totalCost += $total;

                $itemsData[] = [
                    'raw_material_id' => $rmId,
                    'qty' => $qty,
                    'price_per_unit' => $price,
                    'total' => $total,
                    'branch_id' => $branchId,
                ];
            }

            $purchase = RawMaterialPurchase::create([
                'date' => $request->date,
                'invoice_no' => 'RMP-' . date('Ymd-His'),
                'vendor_name' => $request->vendor_name,
                'warehouse_id' => $request->warehouse_id ?: null,
                'total_cost' => $totalCost,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'branch_id' => $branchId,
            ]);

            foreach ($itemsData as $item) {
                $rmId = $item['raw_material_id'];
                $item['purchase_id'] = $purchase->id;
                RawMaterialPurchaseItem::create($item);

                // Update stock in consumption/recipe units
                $rawMat = RawMaterial::find($rmId);
                $factor = ($rawMat && (float)$rawMat->conversion_factor > 0) ? (float)$rawMat->conversion_factor : 1;
                $addQty = $item['qty'] * $factor;
                $warehouseId = $request->warehouse_id ?: null;

                $stock = RawMaterialStock::firstOrCreate(
                    [
                        'raw_material_id' => $rmId,
                        'warehouse_id' => $warehouseId,
                        'branch_id' => $branchId,
                    ],
                    ['qty' => 0]
                );
                $stock->qty += $addQty;
                $stock->save();
            }

            DB::commit();
            return response()->json(['success' => 'Raw Material Purchase Saved Successfully', 'reload' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deletePurchase($id)
    {
        try {
            DB::beginTransaction();
            $purchase = RawMaterialPurchase::with('items')->findOrFail($id);

            if (!is_all_branches() && $purchase->branch_id && $purchase->branch_id != active_branch_id()) {
                return response()->json(['error' => 'Unauthorized!']);
            }

            // Reverse stock
            foreach ($purchase->items as $item) {
                $rawMat = RawMaterial::find($item->raw_material_id);
                $factor = ($rawMat && (float)$rawMat->conversion_factor > 0) ? (float)$rawMat->conversion_factor : 1;
                $deductQty = $item->qty * $factor;

                $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                    ->where('warehouse_id', $purchase->warehouse_id)
                    ->where('branch_id', is_all_branches() ? null : active_branch_id());
                if (!is_all_branches()) {
                    $stock->where('branch_id', active_branch_id());
                }
                $stock = $stock->first();
                if (!$stock) {
                    $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)->first();
                }
                if ($stock) {
                    $stock->qty = max(0, $stock->qty - $deductQty);
                    $stock->save();
                }
            }

            $purchase->delete();
            DB::commit();
            return response()->json(['success' => 'Purchase Deleted Successfully & Stock Reversed', 'reload' => route('raw-materials.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function printPurchase($id)
    {
        $purchase = RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch'])->findOrFail($id);
        return view('admin_panel.raw_material.purchase_print', compact('purchase'));
    }

    // ==================== STOCK AJAX ====================
    public function getStock()
    {
        $branchId = is_all_branches() ? null : active_branch_id();

        $materials = RawMaterial::with(['stocks' => function($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }, 'stocks.warehouse'])->orderBy('name')->get()->map(function ($m) use ($branchId) {
            $stockQuery = $m->stocks();
            if ($branchId) $stockQuery->where('branch_id', $branchId);
            $totalStock = (float)$stockQuery->sum('qty');
            return [
                'id' => $m->id,
                'name' => $m->name,
                'unit' => $m->unit,
                'consumption_unit' => $m->consumption_unit ?? $m->unit,
                'conversion_factor' => (float)($m->conversion_factor ?? 1),
                'stock' => $totalStock,
                'alert_qty' => $m->alert_qty,
            ];
        });
        return response()->json($materials);
    }
}
