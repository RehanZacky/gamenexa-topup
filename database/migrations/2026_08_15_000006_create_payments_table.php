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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider')->default('midtrans'); // midtrans, manual, etc.
            $table->string('transaction_id')->nullable(); // ID transaksi dari Midtrans
            $table->string('payment_type')->nullable(); // qris, bank_transfer, gopay, shopeepay, cstore
            $table->string('payment_method_code')->nullable(); // bca_va, bri_va, indomaret, qris
            $table->decimal('gross_amount', 15, 2);
            $table->string('transaction_status')->default('pending'); // pending, settlement, capture, deny, cancel, expire, failure
            $table->string('fraud_status')->nullable(); // accept, challenge, deny
            $table->string('snap_token')->nullable();
            $table->text('checkout_url')->nullable();
            $table->text('qr_string')->nullable();
            $table->string('va_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'transaction_status']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
