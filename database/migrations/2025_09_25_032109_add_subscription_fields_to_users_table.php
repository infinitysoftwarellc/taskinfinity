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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->string('stripe_id')->nullable()->unique()->after('remember_token');
            $table->string('pm_type')->nullable()->after('stripe_id');
            $table->string('pm_last_four', 4)->nullable()->after('pm_type');
            $table->timestamp('trial_ends_at')->nullable()->after('pm_last_four');
            $table->string('plan_key')->nullable()->after('trial_ends_at')->index();
            $table->timestamp('plan_ends_at')->nullable()->after('plan_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'trial_ends_at',
                'plan_key',
                'plan_ends_at',
            ]);
        });
    }
};
