<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pomodoro_pauses', function (Blueprint $table) {
            $table->dateTime('paused_at_server')->nullable()->after('paused_at_client');
            $table->dateTime('resumed_at_server')->nullable()->after('resumed_at_client');
        });
    }

    public function down(): void
    {
        Schema::table('pomodoro_pauses', function (Blueprint $table) {
            $table->dropColumn(['paused_at_server', 'resumed_at_server']);
        });
    }
};
