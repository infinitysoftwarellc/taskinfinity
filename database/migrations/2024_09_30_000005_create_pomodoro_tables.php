<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pomodoro_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mission_id')->nullable()->constrained('missions')->nullOnDelete();
            $table->enum('type', ['work', 'break']);
            $table->dateTime('started_at_client');
            $table->dateTime('ended_at_client')->nullable();
            $table->string('client_timezone');
            $table->integer('client_utc_offset_minutes');
            $table->dateTime('started_at_server');
            $table->dateTime('ended_at_server')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->unsignedInteger('pause_count')->default(0);
            $table->integer('pause_total_seconds')->default(0);
            $table->string('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'started_at_client']);
        });

        Schema::create('pomodoro_pauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('pomodoro_sessions')->cascadeOnDelete();
            $table->dateTime('paused_at_client');
            $table->dateTime('resumed_at_client')->nullable();
            $table->integer('duration_seconds')->default(0);
        });

        Schema::create('pomodoro_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date_local');
            $table->unsignedInteger('sessions_count')->default(0);
            $table->unsignedInteger('work_seconds')->default(0);
            $table->unsignedInteger('break_seconds')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date_local']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pomodoro_daily_stats');
        Schema::dropIfExists('pomodoro_pauses');
        Schema::dropIfExists('pomodoro_sessions');
    }
};
