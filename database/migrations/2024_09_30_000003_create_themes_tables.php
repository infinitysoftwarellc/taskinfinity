<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sound_packs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->json('files_json');
            $table->timestamps();
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->json('palette_json');
            $table->string('background_asset');
            $table->foreignId('sound_pack_id')->nullable()->constrained('sound_packs')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_theme_prefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('theme_code', ['default', 'gamer', 'forest']);
            $table->json('settings_json');
            $table->timestamps();

            $table->unique(['user_id', 'theme_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_theme_prefs');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('sound_packs');
    }
};
