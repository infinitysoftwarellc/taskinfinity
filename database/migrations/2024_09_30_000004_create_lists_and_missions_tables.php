<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('position')->default(0);
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'position']);
        });

        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('priority')->default(0);
            $table->json('labels_json')->nullable();
            $table->boolean('is_starred')->default(false);
            $table->enum('status', ['active', 'done', 'archived'])->default('active');
            $table->integer('position')->default(0);
            $table->integer('xp_reward')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'list_id', 'position']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->integer('position')->default(0);
            $table->integer('xp_reward')->nullable();
            $table->timestamps();

            $table->index(['mission_id', 'position']);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('size');
            $table->string('mime');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('checkpoints');
        Schema::dropIfExists('missions');
        Schema::dropIfExists('lists');
    }
};
