<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorShopBooking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'booking_date' => 'date',
        'converted_at' => 'datetime',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function booker()
    {
        return $this->belongsTo(User::class, 'booker_user_id');
    }

    public function customer()
    {
        return $this->belongsTo(DistributorCustomer::class, 'distributor_customer_id');
    }

    public function items()
    {
        return $this->hasMany(DistributorShopBookingItem::class, 'booking_id');
    }

    public function sale()
    {
        return $this->belongsTo(DistributorSale::class, 'converted_sale_id');
    }

    public static function generateBookingNo()
    {
        $last = self::latest('id')->first();
        $num = $last ? ($last->id + 1) : 1;
        return 'BK-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
