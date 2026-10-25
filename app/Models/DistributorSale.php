<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorSale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function customer()
    {
        return $this->belongsTo(DistributorCustomer::class, 'distributor_customer_id');
    }

    public function items()
    {
        return $this->hasMany(DistributorSaleItem::class);
    }

    public static function generateInvoiceNo()
    {
        $last = self::latest('id')->first();
        $num = $last ? ($last->id + 1) : 1;
        return 'DSAL-' . date('ymd') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
