<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorClaim extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'claim_date'      => 'date',
        'approved_at'     => 'datetime',
        'total_amount'    => 'float',
        'approved_amount' => 'float',
    ];

    /**
     * Relationship: Distributor who submitted the claim
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    /**
     * Relationship: Itemized items (products, damaged stock, scheme rates)
     */
    public function items()
    {
        return $this->hasMany(DistributorClaimItem::class, 'distributor_claim_id');
    }

    /**
     * Relationship: Expense Head in Chart of Accounts
     */
    public function expenseHead()
    {
        return $this->belongsTo(AccountHead::class, 'expense_head_id');
    }

    /**
     * Relationship: Expense Account
     */
    public function expenseAccount()
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    /**
     * Relationship: Linked Company Expense Voucher
     */
    public function expenseVoucher()
    {
        return $this->belongsTo(ExpenseVoucher::class, 'expense_voucher_id');
    }

    /**
     * Relationship: User who submitted
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Relationship: User who approved
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate unique Claim Number
     */
    public static function generateClaimNo()
    {
        $prefix = 'CLM-' . date('Ymd') . '-';
        $latest = self::where('claim_no', 'like', $prefix . '%')->latest('id')->first();

        if ($latest) {
            $num = (int) substr($latest->claim_no, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Human readable Claim Type title
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->claim_type) {
            'trade_discount'   => 'Trade Scheme / Discount to Shops',
            'damage_stock'     => 'Damaged / Expired Stock',
            'freight'          => 'Bilty / Freight Paid',
            'target_incentive' => 'Sales Target Incentive',
            default            => ucfirst(str_replace('_', ' ', $this->claim_type ?: 'Other Claim')),
        };
    }

    /**
     * Badge UI color for claim type
     */
    public function getTypeBadgeClassAttribute()
    {
        return match ($this->claim_type) {
            'trade_discount'   => 'bg-primary-subtle text-primary border border-primary-subtle',
            'damage_stock'     => 'bg-danger-subtle text-danger border border-danger-subtle',
            'freight'          => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'target_incentive' => 'bg-success-subtle text-success border border-success-subtle',
            default            => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }

    /**
     * Badge UI color for status
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'approved' => 'bg-success text-white',
            'rejected' => 'bg-danger text-white',
            default    => 'bg-warning text-dark',
        };
    }
}
