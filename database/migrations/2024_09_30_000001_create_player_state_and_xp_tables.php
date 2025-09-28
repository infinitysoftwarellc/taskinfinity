<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_state', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('level')->default(1);
            $table->unsignedBigInteger('xp_total')->default(0);
            $table->unsignedInteger('life_current')->default(0);
            $table->unsignedInteger('life_max')->default(0);
            $table->unsignedInteger('energy_current')->default(0);
            $table->unsignedInteger('energy_max')->default(0);
            $table->dateTime('last_energy_calc_at')->nullable();
            $table->dateTime('last_life_calc_at')->nullable();
            $table->dateTime('last_daily_reset_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('xp_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('source', [
                'mission_complete',
                'checkpoint_complete',
                'pomodoro_complete',
                'achievement',
                'purchase_refund',
            ]);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('delta_xp');
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xp_events');
        Schema::dropIfExists('player_state');
    }
};
