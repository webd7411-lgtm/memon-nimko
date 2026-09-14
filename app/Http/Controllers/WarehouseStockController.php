<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class WarehouseStockController extends Controller
{
    public function index(Request $request)
    {
        $branchId  = active_branch_id();
        $type      = $request->stock_type ?? 'all';
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $warehouseFilter = $request->warehouse_id;

        /* ======================================================
       1️⃣ PRODUCTS
    ====================================================== */
        $products = Product::with('unit', 'brand')->get();

        /* ======================================================
       2️⃣ SHOP STOCK — current balance from stocks table (same as Item Stock Report)
          stocks table has one row per product per variant per branch — SUM gives current total
    ====================================================== */
        $shopStocksRaw = DB::table('stocks')
            ->select('product_id', DB::raw('SUM(qty) as shop_qty'))
            ->where('branch_id', $branchId)
            ->whereNull('warehouse_id')   // only branch/shop stock, not warehouse rows
            ->groupBy('product_id')
            ->pluck('shop_qty', 'product_id')
            ->toArray();

        /* ======================================================
       3️⃣ WAREHOUSE STOCK
    ====================================================== */
        $warehouseQuery = WarehouseStock::with('warehouse')
            ->where('branch_id', $branchId);

        if ($warehouseFilter) {
            $warehouseQuery->where('warehouse_id', $warehouseFilter);
        }

        $warehouseRows = $warehouseQuery->get();

        $warehouseQtyByProduct = [];
        $warehouseByProduct    = [];

        foreach ($warehouseRows as $ws) {
            $pid = $ws->product_id;
            $warehouseQtyByProduct[$pid] = ($warehouseQtyByProduct[$pid] ?? 0) + (float) $ws->quantity;
            if (!isset($warehouseByProduct[$pid])) {
                $warehouseByProduct[$pid] = $ws->warehouse;
            }
        }

        /* ======================================================
       4️⃣ FINAL STOCK COLLECTION
    ====================================================== */
        $stocks = collect();

        foreach ($products as $product) {

            $pid = $product->id;

            // ✅ Unit: use unit_type directly (most products have null unit_id)
            $unitType  = strtolower($product->unit_type ?? 'piece');
            $unitName  = strtolower($product->unit->name ?? '');
            $isKg      = ($unitType === 'kg' || $unitType === 'kilogram' || $unitName === 'kg');
            $unitLabel = $isKg ? 'KG' : ($unitType === 'pound' ? 'LB' : ($product->unit->name ?? 'PC'));

            // ✅ Raw shop qty (in grams for KG products)
            $shopQtyRaw   = (float)($shopStocksRaw[$pid] ?? 0);
            $warehouseQty = (float)($warehouseQtyByProduct[$pid] ?? 0);

            // ✅ Convert grams → KG for KG products
            $shopQty = $isKg ? round($shopQtyRaw / 1000, 3) : $shopQtyRaw;
            if ($isKg) {
                $warehouseQty = round($warehouseQty / 1000, 3);
            }

            // 🔴 FILTER LOGIC
            if ($type === 'shop'      && $shopQty == 0) continue;
            if ($type === 'warehouse' && $warehouseQty == 0) continue;
            if ($type === 'all'       && $shopQty == 0 && $warehouseQty == 0) continue;

            $row = new WarehouseStock();
            $row->product_id      = $pid;
            $row->product         = $product;
            $row->unit_label      = $unitLabel;
            $row->warehouse       = $warehouseByProduct[$pid] ?? null;
            $row->warehouse_id    = isset($warehouseByProduct[$pid]) ? $warehouseByProduct[$pid]->id : null;
            $row->shop_stock      = $shopQty;
            $row->warehouse_stock = $warehouseQty;
            $row->total_stock     = $shopQty + $warehouseQty;
            $row->quantity        = $warehouseQty;
            $row->remarks         = ($warehouseQty == 0 && $shopQty > 0) ? 'Shop Only' : null;
            $row->created_at      = now();

            $stocks->push($row);
        }

        return view('admin_panel.warehouses.warehouse_stocks.index',
            compact('stocks')
        );
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin_panel.warehouses.warehouse_stocks.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:0'
        ]);

        WarehouseStock::create($request->all());
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(WarehouseStock $warehouseStock)
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin_panel.warehouses.warehouse_stocks.edit', compact('warehouseStock', 'warehouses', 'products'));
    }

    public function update(Request $request, WarehouseStock $warehouseStock)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:0'
        ]);

        $warehouseStock->update($request->all());
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(WarehouseStock $warehouseStock)
    {
        $warehouseStock->delete();
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
