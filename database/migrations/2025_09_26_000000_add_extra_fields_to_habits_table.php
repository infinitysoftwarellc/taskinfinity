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
        Schema::table('habits', function (Blueprint $table) {
            $table->string('frequency')->nullable()->after('schedule');
            $table->string('goal')->nullable()->after('frequency');
            $table->date('start_date')->nullable()->after('goal');
            $table->string('goal_days')->nullable()->after('start_date');
            $table->string('reminder')->nullable()->after('goal_days');
            $table->boolean('auto_popup')->default(false)->after('reminder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn([
                'frequency',
                'goal',
                'start_date',
                'goal_days',
                'reminder',
                'auto_popup',
            ]);
        });
    }
};
