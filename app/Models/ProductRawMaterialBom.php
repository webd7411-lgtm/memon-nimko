<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRawMaterialBom extends Model
{
    protected $table = 'product_raw_material_bom';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function ingredientProduct()
    {
        return $this->belongsTo(Product::class, 'ingredient_product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
