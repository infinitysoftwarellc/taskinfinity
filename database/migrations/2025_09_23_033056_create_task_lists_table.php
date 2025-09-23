<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/2025_09_23_033056_create_task_lists_table.php

public function up(): void
{
    Schema::create('task_lists', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('folder_id')->constrained()->onDelete('cascade');
        
        // ADICIONE ESTA LINHA
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_lists');
    }
};