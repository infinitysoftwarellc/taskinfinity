<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;

Route::middleware(['auth'])->prefix('webapp')->name('webapp')->group(function () {

    Route::get('/task', [TaskController::class, 'index'])
        ->middleware(['verified'])
        ->name('task.index');

    Route::view('/profile', 'profile')
        ->name('profile');

});

require __DIR__.'/auth.php';
