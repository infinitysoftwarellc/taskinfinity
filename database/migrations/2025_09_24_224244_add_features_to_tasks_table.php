<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Renomeia 'description' para 'name' para ficar mais claro
            $table->renameColumn('description', 'name');

            // --- SUBTAREFAS (Hierarquia Auto-referencial) ---
            // Uma tarefa pode ter uma "tarefa pai". Isso cria a hierarquia.
            $table->foreignId('parent_id')->nullable()->after('project_id')->constrained('tasks')->cascadeOnDelete();

            // --- POMODORO ---
            $table->unsignedInteger('pomodoros_completed')->default(0)->after('is_completed');
            $table->unsignedInteger('pomodoros_estimated')->default(1)->after('pomodoros_completed');
            $table->unsignedInteger('pomodoro_minutes_total')->default(0)->after('pomodoros_estimated');

            // --- FLAGS (Sinalizadores) ---
            $table->boolean('wont_do')->default(false)->after('pomodoro_minutes_total'); // Para "não farei"
            $table->boolean('is_pinned')->default(false)->after('wont_do'); // Para "fixar"
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('name', 'description');
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'pomodoros_completed',
                'pomodoros_estimated',
                'pomodoro_minutes_total',
                'wont_do',
                'is_pinned'
            ]);
        });
    }
};