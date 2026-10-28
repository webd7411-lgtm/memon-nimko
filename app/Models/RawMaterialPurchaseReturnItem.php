<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseReturnItem extends Model
{
    protected $guarded = [];

    public function purchaseReturn()
    {
        return $this->belongsTo(RawMaterialPurchaseReturn::class, 'purchase_return_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
