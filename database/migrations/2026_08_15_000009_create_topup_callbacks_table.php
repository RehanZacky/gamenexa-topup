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
        Schema::create('topup_callbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topup_transaction_id')->nullable()->constrained('topup_transactions')->nullOnDelete();
            $table->string('event')->default('digiflazz.webhook');
            $table->json('payload'); // Raw response data dari webhook Digiflazz
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_callbacks');
    }
};
