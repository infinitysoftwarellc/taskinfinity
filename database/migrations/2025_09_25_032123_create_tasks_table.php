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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->enum('priority', ['none', 'low', 'med', 'high'])->default('none');
            $table->enum('status', ['todo', 'doing', 'done', 'archived'])->default('todo');
            $table->unsignedSmallInteger('estimate_pomodoros')->default(0);
            $table->unsignedSmallInteger('pomodoros_done')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
