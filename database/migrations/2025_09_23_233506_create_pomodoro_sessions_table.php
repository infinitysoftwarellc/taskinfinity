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
        Schema::create('pomodoro_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_type'); // 'work', 'short_break', 'long_break'
            $table->integer('configured_duration'); // Duração em minutos
            $table->integer('actual_duration'); // Duração real em segundos
            $table->timestamp('started_at');
            $table->timestamp('stopped_at')->nullable();
            $table->string('status'); // 'completed', 'stopped'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pomodoro_sessions');
    }
};