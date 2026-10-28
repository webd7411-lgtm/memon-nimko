<?php

// app/Models/CustomerPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $guarded = [];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
