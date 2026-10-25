<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'dispatched_warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(DistributorOrderItem::class);
    }

    public static function generateOrderNo()
    {
        $last = self::latest('id')->first();
        $num = $last ? ($last->id + 1) : 1;
        return 'DO-' . date('ymd') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
