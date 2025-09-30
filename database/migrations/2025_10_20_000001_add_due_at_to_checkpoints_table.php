<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            if (! Schema::hasColumn('checkpoints', 'due_at')) {
                $table->dateTime('due_at')->nullable()->after('xp_reward');
            }
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            if (Schema::hasColumn('checkpoints', 'due_at')) {
                $table->dropColumn('due_at');
            }
        });
    }
};
