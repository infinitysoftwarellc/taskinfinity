<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->isSqliteConnection()) {
            $this->rebuildMissionsTable(listIdNullable: true);

            return;
        }

        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['list_id']);
        });

        DB::statement('ALTER TABLE missions MODIFY list_id BIGINT UNSIGNED NULL');

        Schema::table('missions', function (Blueprint $table) {
            $table->foreign('list_id')->references('id')->on('lists')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if ($this->isSqliteConnection()) {
            DB::table('missions')->whereNull('list_id')->delete();

            $this->rebuildMissionsTable(listIdNullable: false);

            return;
        }

        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['list_id']);
        });

        DB::statement('DELETE FROM missions WHERE list_id IS NULL');

        DB::statement('ALTER TABLE missions MODIFY list_id BIGINT UNSIGNED NOT NULL');

        Schema::table('missions', function (Blueprint $table) {
            $table->foreign('list_id')->references('id')->on('lists')->cascadeOnDelete();
        });
    }

    private function isSqliteConnection(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }

    private function rebuildMissionsTable(bool $listIdNullable): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('missions_temp', function (Blueprint $table) use ($listIdNullable) {
            $this->defineMissionsTable($table, $listIdNullable);
        });

        DB::statement('INSERT INTO missions_temp (id, user_id, list_id, title, description, priority, labels_json, is_starred, status, position, xp_reward, created_at, updated_at)
            SELECT id, user_id, list_id, title, description, priority, labels_json, is_starred, status, position, xp_reward, created_at, updated_at FROM missions');

        Schema::drop('missions');
        Schema::rename('missions_temp', 'missions');

        Schema::enableForeignKeyConstraints();
    }

    private function defineMissionsTable(Blueprint $table, bool $listIdNullable): void
    {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $listColumn = $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();

        if ($listIdNullable) {
            $listColumn->nullable();
        }

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
    }
};
