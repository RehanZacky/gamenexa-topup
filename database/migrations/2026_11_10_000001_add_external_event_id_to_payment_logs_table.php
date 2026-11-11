<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->string('external_event_id')->nullable()->unique()->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->dropUnique(['external_event_id']);
            $table->dropColumn('external_event_id');
        });
    }
};
