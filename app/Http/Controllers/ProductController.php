<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ProductDiscount;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\ProductRawMaterialBom;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
// use App\Models\Size;
use Carbon\Carbon;
use Milon\Barcode\DNS1D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function searchProducts(Request $request)
    {
        $q = $request->get('q');

        $products = Product::with('brand')->where(function ($query) use ($q) {
            $query->where('item_name', 'like', "%{$q}%")
                ->orWhere('item_code', 'like', "%{$q}%")
                ->orWhere('barcode_path', 'like', "%{$q}%");
        })->get();

        return response()->json($products);
    }
    // public function searchProducts(Request $request)
    // {
    //     $q = $request->get('q');

    //     $products = Product::with(['brand', 'activeDiscount'])
    //         ->whereHas('activeDiscount') // only products with active discount
    //         ->where(function ($query) use ($q) {
    //             $query->where('item_name', 'like', "%{$q}%")
    //                   ->orWhere('item_code', 'like', "%{$q}%")
    //                   ->orWhere('barcode_path', 'like', "%{$q}%");
    //         })
    //         ->get();

    //     return response()->json($products);
    // }


    public function product(Request $request)
    {
        $search = $request->search;

        $products = Product::with([
            'category_relation',
            'sub_category_relation',
            'unit',
            'brand',
            'stock',
            'discountProduct',
            'variants.stock'
        ])
            ->withSum(['stocks as total_stock' => function($q) {
                if (!is_all_branches()) {
                    $q->where('branch_id', active_branch_id());
                }
            }], 'qty')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%")
                        ->orWhere('barcode_path', 'like', "%{$search}%")
                        ->orWhereHas('brand', function ($b) use ($search) {
                            $b->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category_relation', function ($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('item_code', 'desc')
            ->paginate(100);


        // 🔧 THIS LINE FIXES EVERYTHING
        $categories = Category::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return view('admin_panel.product.index', compact('products', 'categories'))->render();
        }
        return view('admin_panel.product.index', compact('products', 'categories'));
    }





    public function view_store()
    {
        $categories = Category::select('id', 'name')->get();
        $units = Unit::select('id', 'name')->get();
        $brands = Brand::select('id', 'name')->get();
        $rawMaterials = RawMaterial::orderBy('name')->get();
        $allProducts = Product::select('id', 'item_code', 'item_name', 'unit_type')->orderBy('item_name')->get();
        return view('admin_panel.product.create', compact('categories', 'units', 'brands', 'rawMaterials', 'allProducts'));
    }

    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }
    public function generateBarcode(Request $request)
    {
        // normalize to exactly 6 digits if provided
        $candidate = null;
        if ($request->filled('code')) {
            $digits   = preg_replace('/\D+/', '', $request->query('code')); // keep only digits
            $digits   = substr($digits, 0, 6);
            $candidate = str_pad($digits, 6, '0', STR_PAD_LEFT);             // ensure 6 digits
        }

        $maxRetries = 10;
        $code = $candidate;

        for ($i = 0; $i < $maxRetries; $i++) {
            if (!$code || $this->codeExists($code)) {
                // either not provided OR collision found → generate new
                $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                if ($this->codeExists($code)) {
                    $code = null; // loop again
                    continue;
                }
            }
            // unique mil gaya
            break;
        }

        if (!$code || $this->codeExists($code)) {
            return response()->json([
                'message' => 'Could not generate a unique 6-digit barcode. Please try again.'
            ], 409);
        }

        // Barcode image (CODE128 recommended; C39 bhi chalega)
        $png = (new \Milon\Barcode\DNS1D)->getBarcodePNG($code, 'C128', 2, 50);
        $barcodeImage = 'data:image/png;base64,' . $png;

        return response()->json([
            'barcode_number' => $code,
            'barcode_image'  => $barcodeImage,
        ]);
    }

    /** Check uniqueness across products & discounts & variants */
    private function codeExists(string $code): bool
    {
        return Product::where('barcode_path', $code)->exists()
            || ProductDiscount::where('discount_code', $code)->exists()
            || ProductVariant::where('barcode_path', $code)->exists();
    }

    /** Generate a unique 6-digit barcode for variants */
    private function generateUniqueBarcode(?string $candidate = null): string
    {
        if ($candidate) {
            $digits = preg_replace('/\D+/', '', $candidate);
            $digits = substr($digits, 0, 6);
            $candidate = str_pad($digits, 6, '0', STR_PAD_LEFT);
            if (!$this->codeExists($candidate)) {
                return $candidate;
            }
        }

        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while ($this->codeExists($code));

        return $code;
    }





    public function store_product(Request $request)
    {
        if (!Auth::id()) {
            return redirect()->back();
        }
        $userId = Auth::id();

        // basic validation
        $request->validate([
            'product_name'   => 'required|string|max:255|unique:products,item_name',
            'category_id'    => 'nullable|integer',
            'barcode_path'    => 'nullable|unique:products,barcode_path',
            'sub_category_id' => 'nullable|integer',
            'unit_type'      => 'required|string|in:kg,piece,pound',
        ]);

        // Generate next item code
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextCode = 'ITEM-0001';
        if ($lastProduct) {
            $lastId = $lastProduct->id + 1;
            $nextCode = 'ITEM-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        }

        // Image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = $filename;
        }

        // Normalize fields
        $categoryId = $request->input('category_id') ? (int)$request->input('category_id') : null;
        $subCategoryId = $request->input('sub_category_id') ? (int)$request->input('sub_category_id') : null;

        $brandInput = $request->input('brand_id');
        if (is_array($brandInput)) {
            $brandInput = reset($brandInput);
        }
        $brandId = $brandInput !== null ? (int)$brandInput : null;

        try {
            DB::beginTransaction();

            $product = Product::create([
                'creater_id'      => $userId,
                'category_id'     => $categoryId,
                'sub_category_id' => $subCategoryId,
                'item_code'       => $nextCode,
                'item_name'       => $request->input('product_name'),
                'barcode_path'    => $request->input('barcode_path') ?? null,
                'unit_id'         => $request->input('unit'),
                'unit_type'       => $request->input('unit_type'),
                'brand_id'        => $brandId,
                'wholesale_price' => 0,
                'price'           => 0,
                'initial_stock'   => 0,
                'alert_quantity'  => $request->input('alert_quantity') ? (int)$request->input('alert_quantity') : 0,
                'note'            => $request->input('note'),
                'recipe_batch_yield' => (float)($request->input('recipe_batch_yield', 1) ?: 1),
                'image'           => $imagePath,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // --- Save Product Variants ---
            $variantNames = $request->input('variant_name', []);
            $variantSizes = $request->input('variant_size_value', []);
            $variantUnits = $request->input('variant_size_unit', []);
            $variantPrices = $request->input('variant_price', []);
            $variantCosts = $request->input('variant_cost_price', []);
            $variantStocks = $request->input('variant_stock', []);
            $variantBarcodes = $request->input('variant_barcode', []);
            $defaultVariantIndex = $request->input('variant_default', 0);
            $unitType = $request->input('unit_type');
            $recipeYield = (float)($request->input('recipe_batch_yield', 1) ?: 1);

            $totalStock = 0;
            $defaultPrice = 0;
            $createdVariants = [];
            if (!empty($variantNames)) {
                foreach ($variantNames as $i => $vName) {
                    $sizeVal  = floatval($variantSizes[$i] ?? 0);
                    $vPrice   = floatval($variantPrices[$i] ?? 0);
                    $vCost    = floatval($variantCosts[$i] ?? 0);
                    $vStock   = floatval($variantStocks[$i] ?? 0);
                    $vBarcode = trim($variantBarcodes[$i] ?? '');
                    $sizeUnit = strtolower(trim($variantUnits[$i] ?? $unitType));
                    $isDefault = ($defaultVariantIndex == $i);

                    // Skip empty variant rows
                    if (empty($vName) && $vPrice == 0 && $sizeVal == 0) continue;

                    // Convert size to DB value (grams to kg for kg products)
                    $dbSizeValue = $sizeVal;
                    if ($sizeUnit === 'kg') {
                        $dbSizeValue = $sizeVal / 1000;
                        $sizeLabel = $vName . ' (' . number_format($dbSizeValue, 3) . ' KG)';
                    } else {
                        $sizeLabel = $vName;
                    }

                    // Auto-generate barcode if blank
                    if (empty($vBarcode)) {
                        $vBarcode = $this->generateUniqueBarcode();
                    }

                    $variant = ProductVariant::create([
                        'product_id'      => $product->id,
                        'variant_name'    => $vName,
                        'barcode_path'    => $vBarcode,
                        'size_label'      => $sizeLabel,
                        'size_value'      => $dbSizeValue,
                        'size_unit'       => $sizeUnit,
                        'price'           => $vPrice,
                        'wholesale_price' => 0,
                        'cost_price'      => $vCost,
                        'stock_qty'       => $vStock,
                        'alert_quantity'  => 0,
                        'is_default'      => $isDefault,
                        'is_active'       => true,
                    ]);
                    $createdVariants[$i] = $variant->id;

                    // Single variant stock entry
                    DB::table('stocks')->insert([
                        'branch_id'    => active_branch_id(),
                        'warehouse_id' => 1,
                        'product_id'   => $product->id,
                        'variant_id'   => $variant->id,
                        'qty'          => $vStock,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }

            // --- Save BOM (Bill of Materials) ---
            ProductRawMaterialBom::where('product_id', $product->id)->delete();
            $bomTypes = $request->input('bom_type', []);
            $bomItemIds = $request->input('bom_item_id', []);
            $qtys = $request->input('qty_per_unit', []);
            $rmIds = $request->input('raw_material_id', []);

            if (!empty($bomItemIds)) {
                foreach ($bomItemIds as $i => $itemId) {
                    $rawQty = (float)($qtys[$i] ?? 0);
                    $qtyPerUnit = $recipeYield > 0 ? ($rawQty / $recipeYield) : $rawQty;
                    $type = $bomTypes[$i] ?? 'rm';
                    if ($itemId && $rawQty > 0) {
                        ProductRawMaterialBom::create([
                            'product_id' => $product->id,
                            'variant_id' => null,
                            'raw_material_id' => ($type === 'rm') ? $itemId : null,
                            'ingredient_product_id' => ($type === 'product') ? $itemId : null,
                            'qty_per_unit' => $qtyPerUnit,
                        ]);
                    }
                }
            } else {
                foreach ($rmIds as $i => $rmId) {
                    $rawQty = (float)($qtys[$i] ?? 0);
                    $qtyPerUnit = $recipeYield > 0 ? ($rawQty / $recipeYield) : $rawQty;
                    if ($rmId && $rawQty > 0) {
                        ProductRawMaterialBom::create([
                            'product_id' => $product->id,
                            'variant_id' => null,
                            'raw_material_id' => $rmId,
                            'qty_per_unit' => $qtyPerUnit,
                        ]);
                    }
                }
            }

            // --- Save Custom Variant BOMs ---
            $variantBomTypes = $request->input('variant_bom_type', []);
            $variantBomItemIds = $request->input('variant_bom_item_id', []);
            $variantBomQtys = $request->input('variant_qty_per_unit', []);

            if (!empty($variantBomItemIds)) {
                foreach ($variantBomItemIds as $vKey => $itemIds) {
                    $targetVariantId = $createdVariants[$vKey] ?? (is_numeric($vKey) ? $vKey : null);
                    if (!$targetVariantId) continue;

                    foreach ($itemIds as $j => $itemId) {
                        $rawQty = (float)($variantBomQtys[$vKey][$j] ?? 0);
                        $type = $variantBomTypes[$vKey][$j] ?? 'rm';
                        if ($itemId && $rawQty > 0) {
                            ProductRawMaterialBom::create([
                                'product_id' => $product->id,
                                'variant_id' => $targetVariantId,
                                'raw_material_id' => ($type === 'rm') ? $itemId : null,
                                'ingredient_product_id' => ($type === 'product') ? $itemId : null,
                                'qty_per_unit' => $rawQty,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect('Product')->with('success', 'Product created successfully with variants and recipe BOM!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }





    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }
        $userId = Auth::id();
        $product = Product::findOrFail($id);

        \Log::info("Updating product ID: " . $id, $request->all());

        // basic validation
        $request->validate([
            'product_name'   => 'required|string|max:255|unique:products,item_name,' . $id,
            'category_id'    => 'nullable|integer',
            'barcode_path'    => 'nullable|unique:products,barcode_path,' . $id,
            'sub_category_id' => 'nullable|integer',
            'unit_type'      => 'required|string|in:kg,piece,pound',
        ]);

        // Image upload
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = $filename;
        }

        // Normalize fields
        $categoryId = $request->input('category_id') ? (int)$request->input('category_id') : null;
        $subCategoryId = $request->input('sub_category_id') ? (int)$request->input('sub_category_id') : null;

        $brandInput = $request->input('brand_id');
        if (is_array($brandInput)) {
            $brandInput = reset($brandInput);
        }
        $brandId = !empty($brandInput) ? (int)$brandInput : null;

        try {
            DB::beginTransaction();

            $product->update([
                'creater_id'      => $userId,
                'category_id'     => $categoryId,
                'sub_category_id' => $subCategoryId,
                'item_name'       => $request->input('product_name'),
                'barcode_path'    => $request->input('barcode_path') ?? $product->barcode_path,
                'unit_id'         => $request->input('unit'),
                'unit_type'       => $request->input('unit_type'),
                'brand_id'        => $brandId,
                'alert_quantity'  => $request->input('alert_quantity') ? (int)$request->input('alert_quantity') : 0,
                'note'            => $request->input('note'),
                'recipe_batch_yield' => (float)($request->input('recipe_batch_yield', 1) ?: 1),
                'image'           => $imagePath,
            ]);

            // --- Sync Product Variants ---
            $variantIds = $request->input('variant_id', []);
            $variantNames = $request->input('variant_name', []);
            $variantSizeValues = $request->input('variant_size_value', []);
            $variantSizeUnits = $request->input('variant_size_unit', []);
            $variantPrices = $request->input('variant_price', []);
            $variantCostPrices = $request->input('variant_cost_price', []);
            $variantStocks = $request->input('variant_stock', []);
            $variantBarcodes = $request->input('variant_barcode', []);
            $variantDefault = $request->input('variant_default', 0);

            // 1. Delete removed variants and their stocks
            $keepVariantIds = array_filter($variantIds);
            ProductVariant::where('product_id', $product->id)
                ->whereNotIn('id', $keepVariantIds)
                ->each(function($v) use ($product) {
                    DB::table('stocks')->where('product_id', $product->id)->where('variant_id', $v->id)->delete();
                    $v->delete();
                });

            $defaultPrice = 0;
            $updatedVariantMap = [];

            if (!empty($variantNames)) {
                foreach ($variantNames as $index => $vName) {
                    if (empty($vName)) continue;

                    $vId = $variantIds[$index] ?? null;
                    $sizeValue = floatval($variantSizeValues[$index] ?? 0);
                    $sizeUnit = $variantSizeUnits[$index] ?? $request->input('unit_type');

                    $dbSizeValue = $sizeValue;
                    $sizeLabel = $vName;
                    
                    if ($sizeUnit === 'kg') {
                        $dbSizeValue = $sizeValue / 1000;
                        $sizeLabel = $vName . ' (' . number_format($dbSizeValue, 3) . ' KG)';
                    }

                    $vPrice = floatval($variantPrices[$index] ?? 0);
                    $vCost = floatval($variantCostPrices[$index] ?? 0);
                    $vStock = floatval($variantStocks[$index] ?? 0);
                    $isDefault = ((int)$variantDefault === $index);

                    $vBarcode = !empty($variantBarcodes[$index]) ? $variantBarcodes[$index] : null;
                    if (empty($vBarcode)) {
                        if ($vId) {
                            $existingV = ProductVariant::find($vId);
                            $vBarcode = (!empty($existingV) && !empty($existingV->barcode_path)) ? $existingV->barcode_path : $this->generateUniqueBarcode();
                        } else {
                            $vBarcode = $this->generateUniqueBarcode();
                        }
                    }

                    $variantData = [
                        'product_id'      => $product->id,
                        'variant_name'    => $vName,
                        'barcode_path'    => $vBarcode,
                        'size_label'      => $sizeLabel,
                        'size_value'      => $dbSizeValue,
                        'size_unit'       => $sizeUnit,
                        'price'           => $vPrice,
                        'wholesale_price' => 0,
                        'cost_price'      => $vCost,
                        'alert_quantity'  => 0,
                        'is_default'      => $isDefault,
                        'is_active'       => true,
                    ];

                    if ($vId) {
                        // Update existing
                        $variant = ProductVariant::findOrFail($vId);
                        $variant->update($variantData);
                        $updatedVariantMap[$index] = $variant->id;
                        $updatedVariantMap[$vId] = $variant->id;
                        // Stock is preserved for existing variants
                    } else {
                        // Create new
                        $variantData['stock_qty'] = $vStock;
                        $variant = ProductVariant::create($variantData);
                        $updatedVariantMap[$index] = $variant->id;

                        // Insert initial stock for new variant
                        DB::table('stocks')->insert([
                            'branch_id'    => active_branch_id(),
                            'warehouse_id' => 1,
                            'product_id'   => $product->id,
                            'variant_id'   => $variant->id,
                            'qty'          => $vStock,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }

                    if ($isDefault) {
                        $defaultPrice = $vPrice;
                    }
                }

                // Update product metadata
                $product->update([
                    'price' => $defaultPrice,
                ]);
            }

            // --- Sync BOM (Bill of Materials) ---
            ProductRawMaterialBom::where('product_id', $product->id)->delete();
            $bomTypes = $request->input('bom_type', []);
            $bomItemIds = $request->input('bom_item_id', []);
            $qtys = $request->input('qty_per_unit', []);
            $rmIds = $request->input('raw_material_id', []);
            $recipeYield = (float)($request->input('recipe_batch_yield', 1) ?: 1);

            if (!empty($bomItemIds)) {
                foreach ($bomItemIds as $i => $itemId) {
                    $rawQty = (float)($qtys[$i] ?? 0);
                    $qtyPerUnit = $recipeYield > 0 ? ($rawQty / $recipeYield) : $rawQty;
                    $type = $bomTypes[$i] ?? 'rm';
                    if ($itemId && $rawQty > 0) {
                        ProductRawMaterialBom::create([
                            'product_id' => $product->id,
                            'variant_id' => null,
                            'raw_material_id' => ($type === 'rm') ? $itemId : null,
                            'ingredient_product_id' => ($type === 'product') ? $itemId : null,
                            'qty_per_unit' => $qtyPerUnit,
                        ]);
                    }
                }
            } else {
                foreach ($rmIds as $i => $rmId) {
                    $rawQty = (float)($qtys[$i] ?? 0);
                    $qtyPerUnit = $recipeYield > 0 ? ($rawQty / $recipeYield) : $rawQty;
                    if ($rmId && $rawQty > 0) {
                        ProductRawMaterialBom::create([
                            'product_id' => $product->id,
                            'variant_id' => null,
                            'raw_material_id' => $rmId,
                            'qty_per_unit' => $qtyPerUnit,
                        ]);
                    }
                }
            }

            // --- Save Custom Variant BOMs ---
            $variantBomTypes = $request->input('variant_bom_type', []);
            $variantBomItemIds = $request->input('variant_bom_item_id', []);
            $variantBomQtys = $request->input('variant_qty_per_unit', []);

            if (!empty($variantBomItemIds)) {
                foreach ($variantBomItemIds as $vKey => $itemIds) {
                    $targetVariantId = $updatedVariantMap[$vKey] ?? (is_numeric($vKey) ? $vKey : null);
                    if (!$targetVariantId) continue;

                    foreach ($itemIds as $j => $itemId) {
                        $rawQty = (float)($variantBomQtys[$vKey][$j] ?? 0);
                        $type = $variantBomTypes[$vKey][$j] ?? 'rm';
                        if ($itemId && $rawQty > 0) {
                            ProductRawMaterialBom::create([
                                'product_id' => $product->id,
                                'variant_id' => $targetVariantId,
                                'raw_material_id' => ($type === 'rm') ? $itemId : null,
                                'ingredient_product_id' => ($type === 'product') ? $itemId : null,
                                'qty_per_unit' => $rawQty,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('product')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating product: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::with(['category_relation', 'sub_category_relation', 'unit', 'brand', 'variants', 'bom.rawMaterial', 'bom.ingredientProduct'])->findOrFail($id);
        $categories = Category::select('id', 'name')->get();
        $units = Unit::select('id', 'name')->get();
        $brands = Brand::select('id', 'name')->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $rawMaterials = RawMaterial::orderBy('name')->get();
        $allProducts = Product::where('id', '!=', $id)->select('id', 'item_code', 'item_name', 'unit_type')->orderBy('item_name')->get();

        return view('admin_panel.product.edit', compact('product', 'categories', 'subcategories', 'brands', 'units', 'rawMaterials', 'allProducts'));
    }

    public function getBom($id)
    {
        $bom = ProductRawMaterialBom::with(['rawMaterial', 'ingredientProduct'])->where('product_id', $id)->get();
        return response()->json($bom);
    }

    public function storeBom(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'raw_material_id' => 'required|array',
            'raw_material_id.*' => 'exists:raw_materials,id',
            'qty_per_unit' => 'required|array',
            'qty_per_unit.*' => 'numeric|min:0',
        ]);

        ProductRawMaterialBom::where('product_id', $request->product_id)->delete();

        foreach ($request->raw_material_id as $i => $rmId) {
            $qty = (float)($request->qty_per_unit[$i] ?? 0);
            if ($rmId && $qty > 0) {
                ProductRawMaterialBom::create([
                    'product_id' => $request->product_id,
                    'raw_material_id' => $rmId,
                    'qty_per_unit' => $qty,
                ]);
            }
        }

        return redirect()->back()->with('success', 'BOM saved successfully!');
    }

    // Add function in ProductController.php
    public function barcode($id)
    {
        $product = Product::with(['variants', 'activeDiscount', 'brand'])->findOrFail($id);

        foreach ($product->variants as $variant) {
            if (empty($variant->barcode_path)) {
                $variant->barcode_path = $this->generateUniqueBarcode();
                $variant->save();
            }
        }

        return view('admin_panel.product.barcode', compact('product'));
    }

    // public function searchProducts(Request $request)
    // {
    //     $query = $request->get('q');

    //     \Log::info("Search query: " . $query); // Debug log

    //     $products = Product::where('item_name', 'like', '%' . $query . '%')
    //         ->get(['id', 'item_name', 'item_code', 'retail_price', 'uom', 'measurement', 'unit']);

    //     if ($products->isEmpty()) {
    //         return response()->json(['message' => 'Product not found'], 404);
    //     }

    //     $products = $products->map(function ($product) {
    //         return [
    //             'id' => $product->id,
    //             'name' => $product->item_name,
    //             'code' => $product->item_code,
    //             'price' => $product->retail_price,
    //             'uom' => $product->uom,
    //             'measurement' => $product->measurement,
    //             'unit' => $product->unit,
    //         ];
    //     });

    //     return response()->json($products);
    // }


    public function getAllProductsForSearch()
    {
        $products = Product::with(['brand', 'variants', 'unit'])
            ->select('id', 'item_name', 'item_code', 'barcode_path', 'price', 'unit_id', 'unit_type', 'brand_id', 'note')
            ->get();

        $results = [];
        foreach ($products as $p) {
            $unitName = $p->unit->name ?? ($p->unit_type ? strtoupper($p->unit_type) : 'Pc');
            if ($p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $results[] = [
                        'id'         => $p->id,
                        'variant_id' => $v->id,
                        'item_name'  => $p->item_name . ' (' . ($v->size_label ?: $v->variant_name) . ')',
                        'item_code'  => $p->item_code,
                        'barcode'    => $p->barcode_path,
                        'price'      => $v->price ?: $p->price,
                        'unit_id'    => $p->unit_id,
                        'unit'       => $unitName,
                        'unit_type'  => $p->unit_type,
                        'brand'      => $p->brand->name ?? '',
                        'note'       => $p->note ?? ''
                    ];
                }
            } else {
                $results[] = [
                    'id'         => $p->id,
                    'variant_id' => null,
                    'item_name'  => $p->item_name,
                    'item_code'  => $p->item_code,
                    'barcode'    => $p->barcode_path,
                    'price'      => $p->price,
                    'unit_id'    => $p->unit_id,
                    'unit'       => $unitName,
                    'unit_type'  => $p->unit_type,
                    'brand'      => $p->brand->name ?? '',
                    'note'       => $p->note ?? ''
                ];
            }
        }

        return response()->json($results);
    }

    public function getAllProductIds(Request $request)
    {
        $search = $request->search;
        $ids = Product::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
                    ->orWhere('barcode_path', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category_relation', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        })->pluck('id');
        return response()->json($ids);
    }

    public function bulkEditStore(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return redirect()->route('product')->with('error', 'No products selected');
        }
        session(['bulk_edit_ids' => $ids]);
        return redirect()->route('products.bulk-edit');
    }

    public function bulkEdit(Request $request)
    {
        $ids = session('bulk_edit_ids', []);
        if (empty($ids)) {
            return redirect()->route('product')->with('error', 'No products selected');
        }
        $products = Product::with([
            'category_relation', 'sub_category_relation', 'unit', 'brand',
            'variants.stock', 'stocks'
        ])->whereIn('id', $ids)->orderBy('item_code')->get();

        $categories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::select('id', 'name')->get();
        $subcategories = Subcategory::all();
        return view('admin_panel.product.bulk_edit', compact('products', 'categories', 'brands', 'subcategories'));
    }

    public function bulkUpdate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        \Log::info('BulkUpdate called', ['input_keys' => array_keys($request->all()), 'product_count' => count($request->input('product_name', []))]);

        try {
            DB::beginTransaction();

            $productNames = $request->input('product_name', []);
            $prices = $request->input('price', []);
            $categories = $request->input('category_id', []);
            $subCategories = $request->input('sub_category_id', []);
            $unitTypes = $request->input('unit_type', []);
            $alertQtys = $request->input('alert_quantity', []);
            $brandIds = $request->input('brand_id', []);

            // Variant data arrays (keyed by product_id)
            $variantNames = $request->input('variant_name', []);
            $variantSizeValues = $request->input('variant_size_value', []);
            $variantSizeUnits = $request->input('variant_size_unit', []);
            $variantPrices = $request->input('variant_price', []);
            $variantCostPrices = $request->input('variant_cost_price', []);
            $variantStocks = $request->input('variant_stock', []);
            $variantIds = $request->input('variant_id', []);
            $variantDefaults = $request->input('variant_default', []);
            $kgStocks = $request->input('kg_stock', []);

            foreach ($productNames as $pid => $name) {
                $product = Product::findOrFail($pid);

                $product->update([
                    'item_name'       => $name,
                    'category_id'     => isset($categories[$pid]) ? (int)$categories[$pid] : $product->category_id,
                    'sub_category_id' => isset($subCategories[$pid]) ? (int)$subCategories[$pid] : $product->sub_category_id,
                    'unit_type'       => $unitTypes[$pid] ?? $product->unit_type,
                    'alert_quantity'  => isset($alertQtys[$pid]) ? (int)$alertQtys[$pid] : $product->alert_quantity,
                    'brand_id'        => isset($brandIds[$pid]) ? (int)$brandIds[$pid] : $product->brand_id,
                    'price'           => isset($prices[$pid]) ? (float)$prices[$pid] : $product->price,
                ]);

                // --- Sync Variants for this product ---
                $vNames = $variantNames[$pid] ?? [];
                $vIds = $variantIds[$pid] ?? [];
                $keepIds = array_filter($vIds ?? []);

                // Remove deleted variants
                ProductVariant::where('product_id', $product->id)
                    ->whereNotIn('id', $keepIds)
                    ->each(function ($v) use ($product) {
                        DB::table('stocks')->where('product_id', $product->id)->where('variant_id', $v->id)->delete();
                        $v->delete();
                    });

                if (!empty($vNames)) {
                    foreach ($vNames as $idx => $vName) {
                        if (empty($vName)) continue;
                        if (!isset($vIds[$idx]) || empty($vIds[$idx])) continue;

                        $vId = $vIds[$idx];
                        $sizeValue = (float)($variantSizeValues[$pid][$idx] ?? 0);
                        $sizeUnit = $variantSizeUnits[$pid][$idx] ?? ($product->unit_type ?? 'piece');
                        $vPrice = (float)($variantPrices[$pid][$idx] ?? 0);
                        $vCost = (float)($variantCostPrices[$pid][$idx] ?? 0);
                        // For KG products, use single product-level stock instead of per-variant
                        // Stock is stored in grams (same as production: 10 KG = 10000 grams)
                        if ($product->unit_type == 'kg' && isset($kgStocks[$pid])) {
                            $vStockQty = (float)$kgStocks[$pid] * 1000;
                        } else {
                            $vStockQty = (float)($variantStocks[$pid][$idx] ?? 0);
                        }
                        $isDefault = ($variantDefaults[$pid] == $idx);

                        $dbSizeValue = $sizeValue;
                        $sizeLabel = $vName;
                        if ($sizeUnit === 'kg') {
                            $dbSizeValue = $sizeValue / 1000;
                            $sizeLabel = $vName . ' (' . number_format($dbSizeValue, 3) . ' KG)';
                        }

                        $variantData = [
                            'product_id'      => $product->id,
                            'variant_name'    => $vName,
                            'size_label'      => $sizeLabel,
                            'size_value'      => $dbSizeValue,
                            'size_unit'       => $sizeUnit,
                            'price'           => $vPrice,
                            'wholesale_price' => 0,
                            'cost_price'      => $vCost,
                            'alert_quantity'  => 0,
                            'is_default'      => $isDefault,
                            'is_active'       => true,
                        ];

                        if ($vId) {
                            $variant = ProductVariant::findOrFail($vId);
                            // For KG products, don't update per-variant stock — handled at product level
                            if ($product->unit_type != 'kg') {
                                $variantData['stock_qty'] = $vStockQty;
                            }
                            $variant->update($variantData);
                            // For non-KG, update per-variant stock record
                            if ($product->unit_type != 'kg') {
                                DB::table('stocks')
                                    ->where('product_id', $product->id)
                                    ->where('variant_id', $variant->id)
                                    ->update(['qty' => $vStockQty, 'updated_at' => now()]);
                            }
                        } else {
                            if ($product->unit_type != 'kg') {
                                $variantData['stock_qty'] = $vStockQty;
                            }
                            $variant = ProductVariant::create($variantData);
                            if ($product->unit_type != 'kg') {
                                DB::table('stocks')->insert([
                                    'branch_id'    => active_branch_id(),
                                    'warehouse_id' => 1,
                                    'product_id'   => $product->id,
                                    'variant_id'   => $variant->id,
                                    'qty'          => $vStockQty,
                                    'created_at'   => now(),
                                    'updated_at'   => now(),
                                ]);
                            }
                        }
                    }

                    // After variant loop: update product-level stock for KG products
                    if ($product->unit_type == 'kg' && isset($kgStocks[$pid])) {
                        $kgQtyGrams = (float)$kgStocks[$pid] * 1000;
                        $stockRec = DB::table('stocks')
                            ->where('product_id', $product->id)
                            ->whereNull('variant_id')
                            ->where('branch_id', active_branch_id())
                            ->where('warehouse_id', 1)
                            ->first();
                        if ($stockRec) {
                            DB::table('stocks')
                                ->where('id', $stockRec->id)
                                ->update(['qty' => $kgQtyGrams, 'updated_at' => now()]);
                        } else {
                            DB::table('stocks')->insert([
                                'branch_id'    => active_branch_id(),
                                'warehouse_id' => 1,
                                'product_id'   => $product->id,
                                'variant_id'   => null,
                                'qty'          => $kgQtyGrams,
                                'created_at'   => now(),
                                'updated_at'   => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            \Log::info('BulkUpdate completed successfully', ['updated_count' => count($productNames)]);
            return redirect()->route('product')->with('success', 'All selected products updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Bulk update error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Bulk update failed: ' . $e->getMessage());
        }
    }

    public function resetStock(Request $request)
    {
        if (!Auth::check() || Auth::user()->email !== 'admin@admin.com') {
            return redirect()->back()->with('error', 'Unauthorized! Only Admin can perform this action.');
        }

        try {
            DB::beginTransaction();

            // 1. Reset all stock values in stocks table (branch-wise)
            DB::table('stocks')->where('branch_id', active_branch_id())->update([
                'qty' => 0,
                'reserved_qty' => 0,
                'updated_at' => now()
            ]);

            // 2. Reset all stock values in warehouse_stocks table (branch-wise)
            DB::table('warehouse_stocks')->where('branch_id', active_branch_id())->update([
                'quantity' => 0,
                'updated_at' => now()
            ]);

            // 3. Reset all stock values in product_variants table
            DB::table('product_variants')->update([
                'stock_qty' => 0,
                'updated_at' => now()
            ]);

            // 4. Reset initial_stock in products table
            DB::table('products')->update([
                'initial_stock' => 0,
                'updated_at' => now()
            ]);

            // 5. Store reset timestamp in storage to filter stock reports
            \Illuminate\Support\Facades\Storage::put('stock_reset_timestamp.txt', now()->toDateTimeString());

            DB::commit();

            return redirect()->back()->with('success', 'All stock records have been reset to zero successfully! Sales, purchases, and ledgers remain unaffected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reset stock: ' . $e->getMessage());
        }
    }
}
