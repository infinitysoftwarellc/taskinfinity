<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('list_id');
            }

            if (! Schema::hasColumn('tasks', 'depth')) {
                $table->unsignedTinyInteger('depth')->default(0)->after('parent_id');
            }
        });

        if (! $this->foreignKeyExists('tasks', 'tasks_parent_id_foreign') && Schema::hasColumn('tasks', 'parent_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('tasks')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->indexExists('tasks', 'tasks_list_id_parent_id_index') && Schema::hasColumn('tasks', 'parent_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index(['list_id', 'parent_id'], 'tasks_list_id_parent_id_index');
            });
        }

        if (Schema::hasColumn('tasks', 'depth')) {
            DB::table('tasks')->whereNull('depth')->update(['depth' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('tasks', 'tasks_list_id_parent_id_index')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropIndex('tasks_list_id_parent_id_index');
            });
        }

        if ($this->foreignKeyExists('tasks', 'tasks_parent_id_foreign')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign('tasks_parent_id_foreign');
            });
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'depth')) {
                $table->dropColumn('depth');
            }

            if (Schema::hasColumn('tasks', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        $prefixedTable = $connection->getTablePrefix() . $table;

        if ($driver === 'mysql') {
            $result = DB::select(
                sprintf('SHOW INDEX FROM `%s` WHERE Key_name = ?', $prefixedTable),
                [$index]
            );

            return ! empty($result);
        }

        if ($driver === 'sqlite') {
            $tableName = str_replace("'", "''", $prefixedTable);
            $indexes = DB::select("PRAGMA index_list('{$tableName}')");

            foreach ($indexes as $indexData) {
                $name = $indexData->name ?? null;

                if ($name === $index || $name === $connection->getTablePrefix() . $index) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        $prefixedTable = $connection->getTablePrefix() . $table;

        if ($driver === 'mysql') {
            $result = DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS '
                . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\'',
                [
                    $connection->getDatabaseName(),
                    $prefixedTable,
                    $constraint,
                ]
            );

            return ! empty($result);
        }

        return false;
    }
};
