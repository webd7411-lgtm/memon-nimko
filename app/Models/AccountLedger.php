<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'branch_id',
        'transaction_date',
        'reference_type',
        'reference_no',
        'reference_id',
        'description',
        'previous_balance',
        'credit',
        'debit',
        'balance',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'previous_balance' => 'float',
        'credit'           => 'float',
        'debit'            => 'float',
        'balance'          => 'float',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
