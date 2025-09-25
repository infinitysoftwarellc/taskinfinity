<?php

use App\Http\Controllers\WebApp\TaskController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::prefix('webapp')
    ->middleware(['auth'])
    ->group(function () {
        Route::view('/dashboard', 'dashboard')
            ->middleware(['verified'])
            ->name('dashboard');

        Route::view('/profile', 'profile')
            ->name('profile');

        Route::get('/task', [TaskController::class, 'index'])
            ->name('task');
    });

require __DIR__.'/auth.php';
