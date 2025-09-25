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
        Schema::create('habit_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->date('entry_date');
            $table->boolean('completed')->default(false);
            $table->unsignedSmallInteger('value')->default(0);
            $table->timestamps();

            $table->unique(['habit_id', 'entry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_entries');
    }
};
