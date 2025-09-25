<?php

use App\Livewire\Pomodoro\Timer;
use App\Models\PomodoroSession;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

afterEach(function (): void {
    Carbon::setTestNow();
});

test('user can start a focus session with user timezone metadata', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 9, 30, 0, 'America/Sao_Paulo'));

    $this->actingAs($user);

    Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo')
        ->call('startFocus');

    $session = PomodoroSession::first();

    expect($session)->not->toBeNull();
    expect($session->type)->toBe(PomodoroSession::TYPE_FOCUS);
    expect($session->status)->toBe(PomodoroSession::STATUS_RUNNING);
    expect($session->duration_seconds)->toBe(25 * 60);
    expect($session->meta['timezone'] ?? null)->toBe('America/Sao_Paulo');
    expect($session->meta['initial_started_at'] ?? null)->toBe('2024-01-01 09:30');
    expect($session->meta['started_at_utc'] ?? null)->toBe('2024-01-01 12:30:00');
});

test('focus session can be paused and resumed keeping remaining time', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'America/Sao_Paulo'));

    $this->actingAs($user);

    $component = Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo');

    $component->call('startFocus');

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 5, 0, 'America/Sao_Paulo'));
    $instance = $component->instance()->currentSession;
    expect($instance->secondsRemaining('America/Sao_Paulo'))
        ->toBe(20 * 60);
    $component->call('pause');

    $session = PomodoroSession::first();
    $session->refresh();

    expect($session->status)->toBe(PomodoroSession::STATUS_PAUSED);
    expect($session->remaining_seconds)->toBe(20 * 60);

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 6, 0, 'America/Sao_Paulo'));
    $component->call('resume');

    $session->refresh();

    expect($session->status)->toBe(PomodoroSession::STATUS_RUNNING);
    expect($session->duration_seconds)->toBe(20 * 60);
    expect($session->remaining_seconds)->toBeNull();
});

test('focus session only finishes after duration elapses', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 11, 0, 0, 'America/Sao_Paulo'));

    $this->actingAs($user);

    $component = Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo');

    $component->call('startFocus');

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 11, 5, 0, 'America/Sao_Paulo'));
    $component->call('tick');

    $session = PomodoroSession::first();
    $session->refresh();

    expect($session->status)->toBe(PomodoroSession::STATUS_RUNNING);
    expect($session->duration_seconds)->toBe(25 * 60);

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 11, 25, 0, 'America/Sao_Paulo'));
    $component->call('tick');

    $session->refresh();

    expect($session->status)->toBe(PomodoroSession::STATUS_FINISHED);
    expect($session->meta['local_finished_at'] ?? null)->toBe('2024-01-01 11:25');
});

test('focus session automatically transitions to short break when finished', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 0, 0, 'America/Sao_Paulo'));

    $this->actingAs($user);

    $component = Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo');

    $component->call('startFocus');

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 25, 0, 'America/Sao_Paulo'));

    $component->call('tick');

    $activeSession = PomodoroSession::latest('started_at')->first();

    expect($activeSession)->not->toBeNull();
    expect($activeSession->type)->toBe(PomodoroSession::TYPE_SHORT);
    expect($activeSession->status)->toBe(PomodoroSession::STATUS_RUNNING);
    expect($component->instance()->currentSession?->type)->toBe(PomodoroSession::TYPE_SHORT);
});

test('long break starts after configured number of focus sessions', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 8, 0, 0, 'UTC'));

    PomodoroSession::create([
        'user_id' => $user->id,
        'type' => PomodoroSession::TYPE_FOCUS,
        'status' => PomodoroSession::STATUS_FINISHED,
        'started_at' => Carbon::now()->subHour(),
        'ended_at' => Carbon::now()->subMinutes(30),
        'duration_seconds' => 25 * 60,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo');

    $component->set('longBreakEvery', 2);

    $component->call('startFocus');

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 8, 25, 0, 'UTC'));

    $component->call('tick');

    $sessions = PomodoroSession::orderBy('started_at')->get();

    expect($sessions)->toHaveCount(3); // previous finished + focus + new long break
    expect($sessions->last()->type)->toBe(PomodoroSession::TYPE_LONG);
    expect($sessions->last()->status)->toBe(PomodoroSession::STATUS_RUNNING);
});

test('user can edit a recent pomodoro session', function () {
    $user = User::factory()->create();

    $session = PomodoroSession::create([
        'user_id' => $user->id,
        'type' => PomodoroSession::TYPE_FOCUS,
        'status' => PomodoroSession::STATUS_FINISHED,
        'started_at' => Carbon::now('UTC')->subHours(2),
        'ended_at' => Carbon::now('UTC')->subHour(),
        'duration_seconds' => 25 * 60,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(Timer::class);

    $component->call('beginEditingSession', $session->id);
    $component->set('editingType', PomodoroSession::TYPE_LONG);
    $component->set('editingDurationMinutes', 30);
    $component->call('saveEditedSession');

    $session->refresh();

    expect($session->type)->toBe(PomodoroSession::TYPE_LONG);
    expect($session->duration_seconds)->toBe(30 * 60);
});

test('user can delete a pomodoro session from the timer', function () {
    $user = User::factory()->create();

    $session = PomodoroSession::create([
        'user_id' => $user->id,
        'type' => PomodoroSession::TYPE_SHORT,
        'status' => PomodoroSession::STATUS_FINISHED,
        'started_at' => Carbon::now('UTC')->subHours(3),
        'ended_at' => Carbon::now('UTC')->subHours(2),
        'duration_seconds' => 5 * 60,
    ]);

    $this->actingAs($user);

    Livewire::test(Timer::class)
        ->call('deleteSession', $session->id);

    expect(PomodoroSession::find($session->id))->toBeNull();
});
