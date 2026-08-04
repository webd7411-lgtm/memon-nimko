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
        return $this->belongsTo(RawMaterial::class);
    }
}
