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
        Schema::create('topup_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->string('provider')->default('digiflazz'); // digiflazz
            $table->string('ref_id')->unique(); // ID Transaksi unik e.g. TPU-20260815-00001
            $table->string('provider_product_code'); // SKU Digiflazz
            $table->string('customer_number'); // ID Akun Game
            $table->decimal('price', 15, 2)->default(0); // Harga modal Digiflazz
            $table->string('status')->default('pending'); // pending, processing, success, failed
            $table->string('response_code')->nullable(); // Response code API e.g. 00
            $table->text('message')->nullable(); // Pesan status / error
            $table->string('serial_number')->nullable(); // SN pengiriman / Voucher code
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('ref_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_transactions');
    }
};
