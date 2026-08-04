<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseItem extends Model
{
    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(RawMaterialPurchase::class, 'purchase_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
