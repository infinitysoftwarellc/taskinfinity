<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('big_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'done', 'archived'])->default('active');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->boolean('created_by_ai')->default(false);
            $table->timestamps();
        });

        Schema::create('goal_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('big_goals')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->integer('position')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('rituals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('frequency', ['weekly', 'monthly']);
            $table->timestamps();
        });

        Schema::create('ritual_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ritual_id')->constrained('rituals')->cascadeOnDelete();
            $table->date('date_local');
            $table->boolean('completed')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['ritual_id', 'date_local']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ritual_entries');
        Schema::dropIfExists('rituals');
        Schema::dropIfExists('goal_steps');
        Schema::dropIfExists('big_goals');
    }
};
