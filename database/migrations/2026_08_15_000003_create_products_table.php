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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->string('provider_product_code'); // SKU Digiflazz e.g. ML86
            $table->string('name'); // e.g. 86 Diamonds
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('modal_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->string('status')->default('active'); // active, inactive, cut_off
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index('provider_product_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
