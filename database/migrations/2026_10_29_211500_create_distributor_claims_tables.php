<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AccountHead;
use App\Models\Account;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Distributor Claims Header Table
        if (!Schema::hasTable('distributor_claims')) {
            Schema::create('distributor_claims', function (Blueprint $table) {
                $table->id();
                $table->string('claim_no', 50)->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->string('claim_type', 40)->default('trade_discount'); 
                // types: trade_discount, damage_stock, freight, target_incentive, other
                
                $table->date('claim_date');
                $table->decimal('total_amount', 15, 2)->default(0.00); // Claimed by distributor
                $table->decimal('approved_amount', 15, 2)->default(0.00); // Verified & approved by company
                $table->string('settlement_type', 30)->default('credit_note'); 
                // credit_note (ledger credit + company expense), stock_replacement (fresh goods)
                
                $table->string('status', 30)->default('pending'); // pending, approved, rejected
                
                $table->unsignedBigInteger('expense_head_id')->nullable()->index();
                $table->unsignedBigInteger('expense_account_id')->nullable()->index();
                $table->unsignedBigInteger('expense_voucher_id')->nullable()->index();
                
                $table->string('attachment')->nullable(); // Bill picture, damage photo, bilty receipt
                $table->text('description')->nullable(); // Distributor's note / explanation
                $table->text('admin_notes')->nullable(); // Internal notes / reason for rejection or adjustment
                
                $table->unsignedBigInteger('submitted_by')->nullable()->index();
                $table->unsignedBigInteger('approved_by')->nullable()->index();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Distributor Claim Items Table (For itemized breakdowns)
        if (!Schema::hasTable('distributor_claim_items')) {
            Schema::create('distributor_claim_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_claim_id')->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->string('batch_no')->nullable();
                $table->decimal('qty', 15, 2)->default(1.00);
                $table->decimal('unit_rate', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->text('reason')->nullable(); // e.g. "5% Scheme to 20 retail shops", "Puncture / Leaked packaging"
                $table->timestamps();

                $table->foreign('distributor_claim_id')
                    ->references('id')->on('distributor_claims')
                    ->onDelete('cascade');
            });
        }

        // 3. Ensure Default Account Heads & Expense Accounts for Claims
        try {
            if (Schema::hasTable('account_heads') && Schema::hasTable('accounts')) {
                // Head 1: Trade Promotion & Market Discounts
                $tradeHead = AccountHead::firstOrCreate(
                    ['name' => 'Trade Promotion & Market Discounts'],
                    ['status' => 1]
                );
                Account::firstOrCreate(
                    ['account_code' => 'ACC-CLAIM-TRADE-01'],
                    [
                        'head_id'         => $tradeHead->id,
                        'title'           => 'Trade Promotion & Retail Schemes Expense',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]
                );

                // Head 2: Damaged & Expired Stock Claims
                $damageHead = AccountHead::firstOrCreate(
                    ['name' => 'Damaged & Expired Stock Claims'],
                    ['status' => 1]
                );
                Account::firstOrCreate(
                    ['account_code' => 'ACC-CLAIM-DMG-01'],
                    [
                        'head_id'         => $damageHead->id,
                        'title'           => 'Goods Damage & Expiry Loss Account',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]
                );

                // Head 3: Distributor Freight & Delivery Claims
                $freightHead = AccountHead::firstOrCreate(
                    ['name' => 'Distributor Freight & Carriage Claims'],
                    ['status' => 1]
                );
                Account::firstOrCreate(
                    ['account_code' => 'ACC-CLAIM-FRT-01'],
                    [
                        'head_id'         => $freightHead->id,
                        'title'           => 'Distributor Freight Reimbursement Account',
                        'type'            => 'Debit',
                        'opening_balance' => 0,
                        'status'          => 1,
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Claim account heads creation warning: ' . $e->getMessage());
        }

        // 4. Permissions & Role Setup
        try {
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            $permissions = [
                'Distributor Claims',
                'Create Distributor Claim',
                'Approve Distributor Claim',
                'Reject Distributor Claim',
                'Delete Distributor Claim',
            ];

            foreach ($permissions as $permName) {
                Permission::firstOrCreate([
                    'name'       => $permName,
                    'guard_name' => 'web',
                ]);
            }

            // Assign permissions to Admin & Super Admin roles
            $adminRoles = Role::whereIn('name', ['Super Admin', 'super-admin', 'Admin', 'admin'])->get();
            $claimPerms = Permission::whereIn('name', $permissions)->get();
            foreach ($adminRoles as $role) {
                foreach ($claimPerms as $perm) {
                    if (!$role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                    }
                }
            }

            // Assign view/create to Distributor role
            $distributorRole = Role::where('name', 'Distributor')->first();
            if ($distributorRole) {
                $distPerms = Permission::whereIn('name', ['Distributor Claims', 'Create Distributor Claim'])->get();
                foreach ($distPerms as $dp) {
                    if (!$distributorRole->hasPermissionTo($dp)) {
                        $distributorRole->givePermissionTo($dp);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Claim permissions setup warning: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_claim_items');
        Schema::dropIfExists('distributor_claims');
    }
};
