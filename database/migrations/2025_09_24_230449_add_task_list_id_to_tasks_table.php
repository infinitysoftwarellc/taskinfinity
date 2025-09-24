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
        Schema::table('tasks', function (Blueprint $table) {
            // The foreign key can be nullable because a task might not belong to any list initially
            // and can be directly under a project.
            $table->foreignId('task_list_id')->nullable()->constrained()->onDelete('set null')->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_list_id']);
            $table->dropColumn('task_list_id');
        });
    }
};
