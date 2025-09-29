<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            if (! Schema::hasColumn('checkpoints', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('mission_id')
                    ->constrained('checkpoints')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            if (Schema::hasColumn('checkpoints', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
        });
    }
};
