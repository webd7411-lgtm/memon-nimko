<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRawMaterialUsage extends Model
{
    protected $guarded = [];

    public function productionEntry()
    {
        return $this->belongsTo(\App\Models\ProductionEntry::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
