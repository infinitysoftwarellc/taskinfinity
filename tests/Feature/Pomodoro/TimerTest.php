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
});

test('focus session can be paused and resumed keeping remaining time', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'America/Sao_Paulo'));

    $this->actingAs($user);

    $component = Livewire::test(Timer::class)
        ->set('timezone', 'America/Sao_Paulo');

    $component->call('startFocus');

    Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 5, 0, 'America/Sao_Paulo'));
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
