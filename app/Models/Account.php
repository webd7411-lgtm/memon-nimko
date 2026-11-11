<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Account extends Model
{
    use HasFactory;

    /**
     * Optional metadata attached before saving an opening_balance change
     * so AccountLedger receives rich reference and description context.
     */
    public array $ledgerMeta = [];

    /**
     * Captures the pre-update opening_balance during updating -> updated hooks.
     */
    protected ?float $ledgerOldBalance = null;

    protected $fillable = [
        'distributor_id', // null = Super Admin / Head Office, > 0 = Distributor
        'head_id',        // foreign key: account head
        'account_code',   // account code
        'title',          // account title
        'account_number', // account number / IBAN
        'bank_name',      // bank name
        'branch_name',    // branch name
        'type',           // Debit / Credit
        'status',         // active/inactive
        'opening_balance',
    ];

    public function setLedgerMeta(array $meta): self
    {
        $this->ledgerMeta = $meta;
        return $this;
    }

    protected static function hasLedgerTable(): bool
    {
        static $exists = null;
        if ($exists === null || $exists === false) {
            $exists = Schema::hasTable('account_ledgers');
        }
        return $exists;
    }

    protected static function booted(): void
    {
        static::created(function (Account $account) {
            if (!static::hasLedgerTable()) {
                return;
            }
            $bal = round((float) ($account->opening_balance ?? 0), 2);
            if (abs($bal) < 0.005) {
                $account->ledgerMeta = [];
                return;
            }
            $meta = $account->ledgerMeta ?? [];
            $account->ledgerMeta = [];

            AccountLedger::create([
                'account_id'       => $account->id,
                'branch_id'        => $meta['branch_id'] ?? (function_exists('active_branch_id') ? active_branch_id() : null),
                'transaction_date' => $meta['transaction_date'] ?? ($account->created_at ?? now()),
                'reference_type'   => $meta['reference_type'] ?? 'Account Opening',
                'reference_no'     => $meta['reference_no'] ?? $account->account_code,
                'reference_id'     => $meta['reference_id'] ?? $account->id,
                'description'      => $meta['description'] ?? 'Initial opening balance on account creation',
                'previous_balance' => 0,
                'credit'           => $bal > 0 ? $bal : 0,
                'debit'            => $bal < 0 ? abs($bal) : 0,
                'balance'          => $bal,
                'created_by'       => $meta['created_by'] ?? auth()->id(),
            ]);
        });

        static::updating(function (Account $account) {
            if ($account->isDirty('opening_balance')) {
                $account->ledgerOldBalance = (float) ($account->getOriginal('opening_balance') ?? 0);
            }
        });

        static::updated(function (Account $account) {
            if ($account->ledgerOldBalance === null) {
                return;
            }
            $old = round($account->ledgerOldBalance, 2);
            $new = round((float) ($account->opening_balance ?? 0), 2);
            $account->ledgerOldBalance = null;
            $diff = round($new - $old, 2);

            $meta = $account->ledgerMeta ?? [];
            $account->ledgerMeta = [];

            if (abs($diff) < 0.005 || !static::hasLedgerTable()) {
                return;
            }

            AccountLedger::create([
                'account_id'       => $account->id,
                'branch_id'        => $meta['branch_id'] ?? (function_exists('active_branch_id') ? active_branch_id() : null),
                'transaction_date' => $meta['transaction_date'] ?? now(),
                'reference_type'   => $meta['reference_type'] ?? 'Manual / System Adjustment',
                'reference_no'     => $meta['reference_no'] ?? null,
                'reference_id'     => $meta['reference_id'] ?? null,
                'description'      => $meta['description'] ?? ($diff > 0 ? 'Balance credited / increased' : 'Balance debited / decreased'),
                'previous_balance' => $old,
                'credit'           => $diff > 0 ? $diff : 0,
                'debit'            => $diff < 0 ? abs($diff) : 0,
                'balance'          => $new,
                'created_by'       => $meta['created_by'] ?? auth()->id(),
            ]);
        });
    }

    // Relation with AccountHead
    public function head()
    {
        return $this->belongsTo(AccountHead::class, 'head_id');
    }

    // Relation with Distributor
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function ledgers()
    {
        return $this->hasMany(AccountLedger::class, 'account_id');
    }
}
