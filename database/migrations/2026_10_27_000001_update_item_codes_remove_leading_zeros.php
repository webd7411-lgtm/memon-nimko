<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update products table
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'item_code')) {
            $products = DB::table('products')->where('item_code', 'LIKE', 'ITEM-%')->get(['id', 'item_code']);
            foreach ($products as $product) {
                $newCode = preg_replace('/^ITEM-0*([1-9]\d*|0)$/i', 'ITEM-$1', trim($product->item_code));
                if ($newCode !== $product->item_code) {
                    DB::table('products')->where('id', $product->id)->update(['item_code' => $newCode]);
                }
            }
        }

        // 2. Update sales table (product_code column may contain comma-separated codes)
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'product_code')) {
            $sales = DB::table('sales')->where('product_code', 'LIKE', '%ITEM-%')->get(['id', 'product_code']);
            foreach ($sales as $sale) {
                $parts = explode(',', $sale->product_code);
                $updatedParts = array_map(function ($part) {
                    return preg_replace('/^ITEM-0*([1-9]\d*|0)$/i', 'ITEM-$1', trim($part));
                }, $parts);
                $newCodes = implode(',', $updatedParts);
                if ($newCodes !== $sale->product_code) {
                    DB::table('sales')->where('id', $sale->id)->update(['product_code' => $newCodes]);
                }
            }
        }

        // 3. Update sales_returns table if exists
        if (Schema::hasTable('sales_returns') && Schema::hasColumn('sales_returns', 'product_code')) {
            $returns = DB::table('sales_returns')->where('product_code', 'LIKE', '%ITEM-%')->get(['id', 'product_code']);
            foreach ($returns as $ret) {
                $parts = explode(',', $ret->product_code);
                $updatedParts = array_map(function ($part) {
                    return preg_replace('/^ITEM-0*([1-9]\d*|0)$/i', 'ITEM-$1', trim($part));
                }, $parts);
                $newCodes = implode(',', $updatedParts);
                if ($newCodes !== $ret->product_code) {
                    DB::table('sales_returns')->where('id', $ret->id)->update(['product_code' => $newCodes]);
                }
            }
        }

        // 4. Update product_bookings table if exists
        if (Schema::hasTable('product_bookings') && Schema::hasColumn('product_bookings', 'product_code')) {
            $bookings = DB::table('product_bookings')->where('product_code', 'LIKE', '%ITEM-%')->get(['id', 'product_code']);
            foreach ($bookings as $b) {
                $parts = explode(',', $b->product_code);
                $updatedParts = array_map(function ($part) {
                    return preg_replace('/^ITEM-0*([1-9]\d*|0)$/i', 'ITEM-$1', trim($part));
                }, $parts);
                $newCodes = implode(',', $updatedParts);
                if ($newCodes !== $b->product_code) {
                    DB::table('product_bookings')->where('id', $b->id)->update(['product_code' => $newCodes]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'item_code')) {
            $products = DB::table('products')->where('item_code', 'LIKE', 'ITEM-%')->get(['id', 'item_code']);
            foreach ($products as $product) {
                if (preg_match('/^ITEM-(\d+)$/i', trim($product->item_code), $matches)) {
                    $oldCode = 'ITEM-' . str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                    DB::table('products')->where('id', $product->id)->update(['item_code' => $oldCode]);
                }
            }
        }

        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'product_code')) {
            $sales = DB::table('sales')->where('product_code', 'LIKE', '%ITEM-%')->get(['id', 'product_code']);
            foreach ($sales as $sale) {
                $parts = explode(',', $sale->product_code);
                $updatedParts = array_map(function ($part) {
                    if (preg_match('/^ITEM-(\d+)$/i', trim($part), $matches)) {
                        return 'ITEM-' . str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                    }
                    return trim($part);
                }, $parts);
                DB::table('sales')->where('id', $sale->id)->update(['product_code' => implode(',', $updatedParts)]);
            }
        }
    }
};
