<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialStock extends Model
{
    protected $guarded = [];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
