<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorSalesReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function sale()
    {
        return $this->belongsTo(DistributorSale::class, 'distributor_sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(DistributorCustomer::class, 'distributor_customer_id');
    }

    public function booker()
    {
        return $this->belongsTo(User::class, 'booker_user_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function items()
    {
        return $this->hasMany(DistributorSalesReturnItem::class);
    }

    public static function generateReturnNo()
    {
        $last = self::latest('id')->first();
        $num = $last ? ($last->id + 1) : 1;
        return 'DRET-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
