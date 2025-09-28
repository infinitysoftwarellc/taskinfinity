<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['list_id']);
        });

        DB::statement('DELETE FROM missions WHERE list_id IS NULL');

        DB::statement('ALTER TABLE missions MODIFY list_id BIGINT UNSIGNED NOT NULL');

        Schema::table('missions', function (Blueprint $table) {
            $table->foreign('list_id')->references('id')->on('lists')->cascadeOnDelete();
        });
    }
};
