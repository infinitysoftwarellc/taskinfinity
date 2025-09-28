<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('habit_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->date('checked_on_local');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['habit_id', 'checked_on_local']);
        });

        Schema::create('habit_monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('days_done_count')->default(0);
            $table->unsignedInteger('total_checkins')->default(0);
            $table->unsignedInteger('best_streak_in_month')->default(0);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['habit_id', 'year', 'month']);
        });

        Schema::create('habit_streaks_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('longest_streak')->default(0);
            $table->date('last_checkin_local')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique('habit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_streaks_cache');
        Schema::dropIfExists('habit_monthly_stats');
        Schema::dropIfExists('habit_checkins');
        Schema::dropIfExists('habits');
    }
};
