<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorPayment extends Model
{
    use HasFactory;

    protected $table = 'distributor_payments';

    protected $fillable = [
        'payment_no',
        'distributor_id',
        'amount',
        'payment_date',
        'payment_method',
        'bank_name',
        'account_number',
        'transaction_reference',
        'slip_image',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'payment_date'=> 'date',
        'approved_at' => 'datetime',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique payment number.
     */
    public static function generatePaymentNo()
    {
        $last = self::latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'DPAY-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
