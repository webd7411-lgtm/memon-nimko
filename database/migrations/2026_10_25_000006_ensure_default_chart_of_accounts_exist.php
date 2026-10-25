<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;
use App\Models\AccountHead;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (Schema::hasTable('accounts') && Schema::hasTable('account_heads')) {
                if (Account::count() === 0) {
                    $bankHead = AccountHead::firstOrCreate(['name' => 'Bank Accounts'], ['status' => 1]);
                    $cashHead = AccountHead::firstOrCreate(['name' => 'Cash in Hand'], ['status' => 1]);

                    Account::firstOrCreate([
                        'account_code' => 'ACC-MEEZAN-01',
                    ], [
                        'head_id'         => $bankHead->id,
                        'title'           => 'Meezan Bank (Main Corporate)',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]);

                    Account::firstOrCreate([
                        'account_code' => 'ACC-HBL-01',
                    ], [
                        'head_id'         => $bankHead->id,
                        'title'           => 'Habib Bank Limited (HBL)',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]);

                    Account::firstOrCreate([
                        'account_code' => 'ACC-CASH-01',
                    ], [
                        'head_id'         => $cashHead->id,
                        'title'           => 'Main Head Office Cash Counter',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Default accounts migration notice: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive
    }
};
