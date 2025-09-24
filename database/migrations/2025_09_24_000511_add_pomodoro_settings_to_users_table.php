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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('pomodoro_work_minutes')->default(25)->after('remember_token');
            $table->integer('pomodoro_short_break_minutes')->default(5)->after('pomodoro_work_minutes');
            $table->integer('pomodoro_long_break_minutes')->default(15)->after('pomodoro_short_break_minutes');
            $table->integer('pomodoro_cycles')->default(4)->after('pomodoro_long_break_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pomodoro_work_minutes',
                'pomodoro_short_break_minutes',
                'pomodoro_long_break_minutes',
                'pomodoro_cycles',
            ]);
        });
    }
};