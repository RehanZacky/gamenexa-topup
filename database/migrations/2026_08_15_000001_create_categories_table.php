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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('games'); // games, pulsa, voucher, ewallet, pln
            $table->string('publisher')->nullable(); // Moonton, Garena, Hoyoverse, etc.
            $table->string('image')->nullable();
            $table->string('banner')->nullable();
            $table->text('instruction')->nullable(); // e.g. cara mencari User ID / Zone ID
            $table->boolean('has_zone_id')->default(false); // apakah butuh Server/Zone ID
            $table->string('zone_id_label')->nullable()->default('Zone ID');
            $table->string('user_id_label')->nullable()->default('User ID');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
