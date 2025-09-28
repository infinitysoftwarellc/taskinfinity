<?php

use App\Http\Controllers\API\AbilityController;
use App\Http\Controllers\API\AchievementController;
use App\Http\Controllers\API\AiRequestController;
use App\Http\Controllers\API\AttachmentController;
use App\Http\Controllers\API\BigGoalController;
use App\Http\Controllers\API\CheckpointController;
use App\Http\Controllers\API\EconomyWalletController;
use App\Http\Controllers\API\HabitCheckinController;
use App\Http\Controllers\API\HabitController;
use App\Http\Controllers\API\HabitMonthlyStatController;
use App\Http\Controllers\API\HabitStreakController;
use App\Http\Controllers\API\GoalStepController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\MissionController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PlanLimitController;
use App\Http\Controllers\API\PlayerStateController;
use App\Http\Controllers\API\PomodoroDailyStatController;
use App\Http\Controllers\API\PomodoroPauseController;
use App\Http\Controllers\API\PomodoroSessionController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\RitualController;
use App\Http\Controllers\API\RitualEntryController;
use App\Http\Controllers\API\SoundPackController;
use App\Http\Controllers\API\StoreItemController;
use App\Http\Controllers\API\TaskListController;
use App\Http\Controllers\API\ThemeController;
use App\Http\Controllers\API\UserAbilityController;
use App\Http\Controllers\API\UserAchievementController;
use App\Http\Controllers\API\UserThemePreferenceController;
use App\Http\Controllers\API\XpEventController;
use Illuminate\Support\Facades\Route;

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
