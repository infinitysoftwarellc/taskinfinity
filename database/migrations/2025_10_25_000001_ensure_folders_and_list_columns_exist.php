<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('folders')) {
            Schema::create('folders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('color')->nullable();
                $table->integer('position')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->dateTime('archived_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'position']);
            });
        }

        if (! Schema::hasColumn('lists', 'view_type')) {
            Schema::table('lists', function (Blueprint $table) {
                $table->string('view_type', 40)->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('lists', 'folder_id')) {
            Schema::table('lists', function (Blueprint $table) {
                $table->foreignId('folder_id')->nullable()->after('icon')->constrained('folders')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('lists', 'is_pinned')) {
            Schema::table('lists', function (Blueprint $table) {
                $table->boolean('is_pinned')->default(false)->after('position');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty: we only ensure presence of structures without
        // assuming ownership to safely drop them when rolling back.
    }
};
