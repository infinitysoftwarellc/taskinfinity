<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pomodoro_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('focus_minutes')->default(25);
            $table->unsignedSmallInteger('short_break_minutes')->default(5);
            $table->unsignedSmallInteger('long_break_minutes')->default(10);
            $table->unsignedSmallInteger('cycles_before_long_break')->default(4);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pomodoro_preferences');
    }
};
