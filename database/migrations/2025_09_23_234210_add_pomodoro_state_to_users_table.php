<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('pomodoro_ends_at')->nullable()->after('remember_token');
            $table->string('pomodoro_session_type')->nullable()->after('pomodoro_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pomodoro_ends_at', 'pomodoro_session_type']);
        });
    }
};