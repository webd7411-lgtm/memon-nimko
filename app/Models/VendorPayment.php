<?php

// app/Models/VendorPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $guarded = [];


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function cardAccount()
    {
        return $this->belongsTo(Account::class, 'card_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_or_user_id');
    }
}
