<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('account_ledgers')) {
            Schema::create('account_ledgers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('account_id')->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->dateTime('transaction_date')->index();
                $table->string('reference_type', 80)->nullable();
                $table->string('reference_no', 80)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('description')->nullable();
                $table->decimal('previous_balance', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0); // Money In (+)
                $table->decimal('debit', 15, 2)->default(0);  // Money Out (-)
                $table->decimal('balance', 15, 2)->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['account_id', 'transaction_date', 'id'], 'account_ledger_lookup_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_ledgers');
    }
};
