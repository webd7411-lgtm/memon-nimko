<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('distributor_payments')) {
            Schema::create('distributor_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_no', 50)->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->string('payment_method', 50)->default('cash'); // bank_transfer, cash, online, cheque, receipt_voucher, etc.
                $table->string('bank_name', 100)->nullable();
                $table->string('account_number', 100)->nullable();
                $table->string('transaction_reference', 100)->nullable();
                $table->string('slip_image')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 20)->default('pending')->index(); // pending, approved, rejected
                $table->unsignedBigInteger('approved_by')->nullable()->index();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_payments');
    }
};
