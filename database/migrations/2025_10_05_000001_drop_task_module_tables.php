<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_tag_task');
        Schema::dropIfExists('task_tags');
        Schema::dropIfExists('subtasks');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('lists');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('filters');
        Schema::dropIfExists('attachments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Task module tables intentionally removed.
    }
};
