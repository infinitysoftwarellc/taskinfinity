<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_items', function (Blueprint $table) {
            $table->id();
            $table->enum('theme_code', ['default', 'gamer', 'forest']);
            $table->enum('type', ['sound', 'icon', 'background', 'skin']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('asset_path');
            $table->integer('cost_xp')->nullable();
            $table->integer('cost_coins')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
            $table->integer('spent_xp')->nullable();
            $table->integer('spent_coins')->nullable();
            $table->dateTime('acquired_at');
            $table->timestamps();

            $table->index(['user_id', 'acquired_at']);
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
            $table->boolean('is_equipped')->default(false);
            $table->dateTime('equipped_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'store_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('store_items');
    }
};
