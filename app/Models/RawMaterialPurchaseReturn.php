<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseReturn extends Model
{
    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(RawMaterialPurchase::class, 'purchase_id');
    }

    public function items()
    {
        return $this->hasMany(RawMaterialPurchaseReturnItem::class, 'purchase_return_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
