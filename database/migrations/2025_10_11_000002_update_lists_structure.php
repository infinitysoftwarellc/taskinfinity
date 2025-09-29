<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

        Schema::table('lists', function (Blueprint $table) {
            $table->string('view_type', 40)->nullable()->after('name');
            $table->foreignId('folder_id')->nullable()->after('icon')->constrained('folders')->nullOnDelete();
            $table->boolean('is_pinned')->default(false)->after('position');
        });

        DB::table('lists')->update([
            'view_type' => DB::raw("COALESCE(view_type, 'lista')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('lists', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
            $table->dropConstrainedForeignId('folder_id');
            $table->dropColumn('view_type');
        });

        Schema::dropIfExists('folders');
    }
};
