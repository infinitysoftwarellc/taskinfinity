<?php

// This routes file registers HTTP endpoints for the web section.
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
use App\Http\Controllers\Habits\HabitTrackerPageController;
use App\Http\Controllers\Habits\HabitController;
use App\Http\Controllers\Habits\HabitMonthlyStatController;
use App\Http\Controllers\Habits\HabitStreakController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Plans\PlanLimitController;
use App\Http\Controllers\Player\PlayerStateController;
use App\Http\Controllers\Pomodoro\PomodoroController;
use App\Http\Controllers\Pomodoro\PomodoroDailyStatController;
use App\Http\Controllers\Pomodoro\PomodoroPauseController;
use App\Http\Controllers\Pomodoro\PomodoroSessionController;
use App\Http\Controllers\Pomodoro\PomodoroStatisticsController;
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
use App\Models\TaskList;
use App\Support\MissionShortcutFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');
Route::view('/teste', 'teste');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('pomodoro', PomodoroController::class)->name('app.pomodoro');
    Route::get('pomodoro/estatisticas', PomodoroStatisticsController::class)->name('app.pomodoro.stats');
    Route::get('habits', HabitTrackerPageController::class)->name('app.habits.tracker');
    Route::view('habitos', 'app.habits.index')->name('app.habits');
    Route::view('configuracoes', 'app.settings.index')->name('app.settings');
    Route::view('completed', 'app.tasks.completed')->name('tasks.completed');

    Volt::route('configuracoes/perfil', 'settings.profile')
        ->name('profile.edit');

    Volt::route('configuracoes/senha', 'settings.password')
        ->name('password.edit')
        ->middleware('password.confirm');

    Volt::route('configuracoes/aparencia', 'settings.appearance')
        ->name('appearance.edit');

    Volt::route('two-factor', 'settings.two-factor')
        ->name('two-factor.show')
        ->middleware('password.confirm');

    Route::resource('lists', TaskListController::class);

    Route::get('tasks/{taskList?}', function (Request $request, ?TaskList $taskList = null) {
        $shortcut = $request->string('shortcut')->trim()->lower()->value();

        if ($shortcut === '') {
            $shortcut = null;
        }

        if ($shortcut && ! in_array($shortcut, MissionShortcutFilter::supported(), true)) {
            $shortcut = null;
        }

        if ($taskList) {
            $shortcut = null;
        }

        return view('app.tasks.index', [
            'listId' => $taskList?->id,
            'shortcut' => $shortcut,
        ]);
    })->name('tasks.index');

    Route::resource('missions', MissionController::class);
    Route::resource('checkpoints', CheckpointController::class)->except(['create', 'edit']);
    Route::resource('attachments', AttachmentController::class)->except(['create', 'edit']);

    Route::resource('pomodoro-sessions', PomodoroSessionController::class)->except(['create', 'edit']);
    Route::resource('pomodoro-pauses', PomodoroPauseController::class)->except(['create', 'edit']);
    Route::resource('pomodoro-daily-stats', PomodoroDailyStatController::class)->except(['create', 'edit']);

    Route::resource('habits', HabitController::class);
    Route::resource('habit-checkins', HabitCheckinController::class)->except(['create', 'edit']);
    Route::resource('habit-monthly-stats', HabitMonthlyStatController::class)->except(['create', 'edit']);
    Route::resource('habit-streaks', HabitStreakController::class)->only(['index', 'show', 'update']);

    Route::resource('big-goals', BigGoalController::class);
    Route::resource('goal-steps', GoalStepController::class)->except(['create', 'edit']);
    Route::resource('rituals', RitualController::class);
    Route::resource('ritual-entries', RitualEntryController::class)->except(['create', 'edit']);

    Route::resource('store-items', StoreItemController::class)->except(['index', 'show', 'create', 'edit']);
    Route::resource('purchases', PurchaseController::class)->except(['create', 'edit']);
    Route::resource('inventory', InventoryController::class)->except(['create', 'edit']);

    Route::resource('notifications', NotificationController::class)->except(['create', 'edit']);
    Route::resource('ai-requests', AiRequestController::class)->except(['create', 'edit']);

    Route::resource('user-abilities', UserAbilityController::class)->except(['create', 'edit']);
    Route::resource('user-achievements', UserAchievementController::class)->except(['create', 'edit']);

    Route::get('player-state', [PlayerStateController::class, 'show']);
    Route::put('player-state', [PlayerStateController::class, 'update']);

    Route::get('economy-wallet', [EconomyWalletController::class, 'show']);
    Route::put('economy-wallet', [EconomyWalletController::class, 'update']);

    Route::resource('xp-events', XpEventController::class)->only(['index', 'store', 'show']);

    Route::resource('user-theme-preferences', UserThemePreferenceController::class)->except(['create', 'edit']);
});

Route::resource('abilities', AbilityController::class);
Route::resource('achievements', AchievementController::class);
Route::resource('sound-packs', SoundPackController::class);
Route::resource('themes', ThemeController::class);
Route::resource('plan-limits', PlanLimitController::class);
Route::resource('store-items', StoreItemController::class)->only(['index', 'show']);
