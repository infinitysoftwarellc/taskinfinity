<?php

use App\Http\Controllers\Ai\AiRequestController;
use App\Http\Controllers\Economy\EconomyWalletController;
use App\Http\Controllers\Gamification\AbilityController;
use App\Http\Controllers\Gamification\AchievementController;
use App\Http\Controllers\Gamification\UserAbilityController;
use App\Http\Controllers\Gamification\UserAchievementController;
use App\Http\Controllers\Gamification\XpEventController;
use App\Http\Controllers\Goals\BigGoalController;
use App\Http\Controllers\Goals\GoalStepController;
use App\Http\Controllers\Habits\HabitCheckinController;
use App\Http\Controllers\Habits\HabitController;
use App\Http\Controllers\Habits\HabitMonthlyStatController;
use App\Http\Controllers\Habits\HabitStreakController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Plans\PlanLimitController;
use App\Http\Controllers\Player\PlayerStateController;
use App\Http\Controllers\Pomodoro\PomodoroDailyStatController;
use App\Http\Controllers\Pomodoro\PomodoroPauseController;
use App\Http\Controllers\Pomodoro\PomodoroSessionController;
use App\Http\Controllers\Preferences\SoundPackController;
use App\Http\Controllers\Preferences\ThemeController;
use App\Http\Controllers\Preferences\UserThemePreferenceController;
use App\Http\Controllers\Rituals\RitualController;
use App\Http\Controllers\Rituals\RitualEntryController;
use App\Http\Controllers\Store\InventoryController;
use App\Http\Controllers\Store\PurchaseController;
use App\Http\Controllers\Store\StoreItemController;
use App\Http\Controllers\Tasks\AttachmentController;
use App\Http\Controllers\Tasks\CheckpointController;
use App\Http\Controllers\Tasks\MissionController;
use App\Http\Controllers\Tasks\TaskListController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lists', TaskListController::class);
    Route::apiResource('missions', MissionController::class);
    Route::apiResource('checkpoints', CheckpointController::class)->except(['create', 'edit']);
    Route::apiResource('attachments', AttachmentController::class)->except(['create', 'edit']);

    Route::apiResource('pomodoro-sessions', PomodoroSessionController::class)->except(['create', 'edit']);
    Route::apiResource('pomodoro-pauses', PomodoroPauseController::class)->except(['create', 'edit']);
    Route::apiResource('pomodoro-daily-stats', PomodoroDailyStatController::class)->except(['create', 'edit']);

    Route::apiResource('habits', HabitController::class);
    Route::apiResource('habit-checkins', HabitCheckinController::class)->except(['create', 'edit']);
    Route::apiResource('habit-monthly-stats', HabitMonthlyStatController::class)->except(['create', 'edit']);
    Route::apiResource('habit-streaks', HabitStreakController::class)->only(['index', 'show', 'update']);

    Route::apiResource('big-goals', BigGoalController::class);
    Route::apiResource('goal-steps', GoalStepController::class)->except(['create', 'edit']);
    Route::apiResource('rituals', RitualController::class);
    Route::apiResource('ritual-entries', RitualEntryController::class)->except(['create', 'edit']);

    Route::apiResource('store-items', StoreItemController::class)->except(['index', 'show', 'create', 'edit']);
    Route::apiResource('purchases', PurchaseController::class)->except(['create', 'edit']);
    Route::apiResource('inventory', InventoryController::class)->except(['create', 'edit']);

    Route::apiResource('notifications', NotificationController::class)->except(['create', 'edit']);
    Route::apiResource('ai-requests', AiRequestController::class)->except(['create', 'edit']);

    Route::apiResource('user-abilities', UserAbilityController::class)->except(['create', 'edit']);
    Route::apiResource('user-achievements', UserAchievementController::class)->except(['create', 'edit']);

    Route::get('player-state', [PlayerStateController::class, 'show']);
    Route::put('player-state', [PlayerStateController::class, 'update']);

    Route::get('economy-wallet', [EconomyWalletController::class, 'show']);
    Route::put('economy-wallet', [EconomyWalletController::class, 'update']);

    Route::apiResource('xp-events', XpEventController::class)->only(['index', 'store', 'show']);

    Route::apiResource('user-theme-preferences', UserThemePreferenceController::class)->except(['create', 'edit']);
});

Route::apiResource('abilities', AbilityController::class);
Route::apiResource('achievements', AchievementController::class);
Route::apiResource('sound-packs', SoundPackController::class);
Route::apiResource('themes', ThemeController::class);
Route::apiResource('plan-limits', PlanLimitController::class);
Route::apiResource('store-items', StoreItemController::class)->only(['index', 'show']);
