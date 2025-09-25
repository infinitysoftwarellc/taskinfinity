<?php

use App\Http\Controllers\WebApp\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\WebApp\AiController;
use App\Http\Controllers\WebApp\BillingController;
use App\Http\Controllers\WebApp\DashboardController;
use App\Http\Controllers\WebApp\ExportController;
use App\Http\Controllers\WebApp\GamerController;
use App\Http\Controllers\WebApp\HabitController;
use App\Http\Controllers\WebApp\PomodoroController;
use App\Http\Controllers\WebApp\Support\TicketController;
use App\Http\Controllers\WebApp\TaskController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::prefix('webapp')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware(['verified'])
            ->name('dashboard');

        Route::view('/profile', 'profile')
            ->name('profile');

        Route::get('/tasks', [TaskController::class, 'index'])
            ->name('tasks.index');
        Route::get('/tasks/board', [TaskController::class, 'board'])
            ->name('tasks.board');
        Route::get('/tasks/timeline', [TaskController::class, 'timeline'])
            ->name('tasks.timeline');

        Route::get('/pomodoro', [PomodoroController::class, 'index'])
            ->name('pomodoro');

        Route::get('/habits', [HabitController::class, 'index'])
            ->name('habits');

        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/plan', [AiController::class, 'plan'])->name('plan');
            Route::get('/autolabel', [AiController::class, 'autolabel'])->name('autolabel');
            Route::get('/split', [AiController::class, 'split'])->name('split');
        });

        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('/', [BillingController::class, 'index'])->name('index');
            Route::get('/stripe', [BillingController::class, 'stripe'])->name('stripe');
            Route::get('/mercado-pago', [BillingController::class, 'mercadoPago'])->name('mercado-pago');
        });

        Route::get('/gamer', [GamerController::class, 'index'])
            ->name('gamer');

        Route::get('/export', [ExportController::class, 'index'])
            ->name('export');

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
        });
    });

require __DIR__.'/auth.php';
