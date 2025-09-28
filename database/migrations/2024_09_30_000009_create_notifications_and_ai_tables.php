<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['web', 'email', 'push']);
            $table->string('title');
            $table->text('body');
            $table->json('payload_json')->nullable();
            $table->enum('theme_code', ['default', 'gamer', 'forest'])->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'sent_at']);
        });

        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['generate_checkpoints', 'summarize', 'suggest_plan']);
            $table->unsignedInteger('input_chars')->default(0);
            $table->unsignedInteger('output_chars')->default(0);
            $table->string('model');
            $table->integer('cost_tokens')->nullable();
            $table->enum('status', ['ok', 'error']);
            $table->text('error')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'type']);
        });

        Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_code', ['free', 'pro'])->unique();
            $table->json('limits_json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_limits');
        Schema::dropIfExists('ai_requests');
        Schema::dropIfExists('notifications');
    }
};
