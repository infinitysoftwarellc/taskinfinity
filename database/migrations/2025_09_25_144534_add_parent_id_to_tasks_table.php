<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Coluna para hierarquia (tarefa pai)
            if (!Schema::hasColumn('tasks', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('tasks')   // FK para a própria tabela
                    ->cascadeOnDelete()
                    ->after('id');           // ajuste a posição se quiser
            }

            // (Opcional) manter a ordenação usada no código
            if (!Schema::hasColumn('tasks', 'position')) {
                $table->unsignedInteger('position')->default(0)->index();
            }

            // (Opcional) se seu código usa profundidade
            if (!Schema::hasColumn('tasks', 'depth')) {
                $table->unsignedTinyInteger('depth')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('tasks', 'position')) {
                $table->dropIndex(['position']);
                $table->dropColumn('position');
            }
            if (Schema::hasColumn('tasks', 'depth')) {
                $table->dropColumn('depth');
            }
        });
    }
};
