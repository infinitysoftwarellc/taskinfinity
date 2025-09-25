<?php

namespace App\Livewire\Pomodoro;

use App\Models\PomodoroSession;
use App\Models\PomodoroSetting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Timer extends Component
{
    #[Validate('required|integer|min:1|max:180')]
    public int $focusMinutes = 25;

    #[Validate('required|integer|min:1|max:60')]
    public int $shortBreakMinutes = 5;

    #[Validate('required|integer|min:1|max:120')]
    public int $longBreakMinutes = 15;

    #[Validate('required|integer|min:1|max:10')]
    public int $longBreakEvery = 4;

    public string $timezone;

    public ?PomodoroSession $currentSession = null;

    public ?int $remainingSeconds = null;

    public ?int $editingSessionId = null;

    #[Validate('required|in:focus,short,long')]
    public string $editingType = PomodoroSession::TYPE_FOCUS;

    #[Validate('required|integer|min:1|max:180')]
    public int $editingDurationMinutes = 25;

    /**
     * @var list<array<string, mixed>>
     */
    public array $recentSessions = [];

    public function mount(): void
    {
        $user = $this->user();

        $settings = $user->pomodoroSetting()->firstOrCreate([], [
            'focus_minutes' => $this->focusMinutes,
            'short_break_minutes' => $this->shortBreakMinutes,
            'long_break_minutes' => $this->longBreakMinutes,
            'long_break_every' => $this->longBreakEvery,
        ]);

        $this->focusMinutes = $settings->focus_minutes;
        $this->shortBreakMinutes = $settings->short_break_minutes;
        $this->longBreakMinutes = $settings->long_break_minutes;
        $this->longBreakEvery = $settings->long_break_every;

        $this->timezone = session('pomodoro.timezone', $user->timezone ?? config('app.timezone', 'UTC'));

        $this->refreshSession();
        $this->refreshRecentSessions();
    }

    public function syncTimezone(string $timezone): void
    {
        if (! in_array($timezone, timezone_identifiers_list(), true)) {
            return;
        }

        if ($timezone === $this->timezone) {
            return;
        }

        $this->timezone = $timezone;
        session(['pomodoro.timezone' => $timezone]);
    }

    public function saveSettings(): void
    {
        $this->validate();

        /** @var PomodoroSetting $settings */
        $settings = $this->user()->pomodoroSetting;

        $settings->update([
            'focus_minutes' => $this->focusMinutes,
            'short_break_minutes' => $this->shortBreakMinutes,
            'long_break_minutes' => $this->longBreakMinutes,
            'long_break_every' => $this->longBreakEvery,
        ]);

        $this->dispatch('settings-saved');
    }

    public function startFocus(): void
    {
        $this->startSession(PomodoroSession::TYPE_FOCUS, $this->focusMinutes);
    }

    public function startShortBreak(): void
    {
        $this->startSession(PomodoroSession::TYPE_SHORT, $this->shortBreakMinutes);
    }

    public function startLongBreak(): void
    {
        $this->startSession(PomodoroSession::TYPE_LONG, $this->longBreakMinutes);
    }

    public function pause(): void
    {
        if (! $this->currentSession || $this->currentSession->status !== PomodoroSession::STATUS_RUNNING) {
            return;
        }

        $remaining = $this->currentSession->secondsRemaining($this->timezone);
        $this->currentSession->pause($remaining, $this->timezone);
        $this->currentSession->refresh();
        $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
    }

    public function resume(): void
    {
        if (! $this->currentSession || $this->currentSession->status !== PomodoroSession::STATUS_PAUSED) {
            return;
        }

        $this->currentSession->resume($this->timezone);
        $this->currentSession->refresh();
        $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
    }

    public function stop(): void
    {
        if (! $this->currentSession) {
            return;
        }

        $this->currentSession->cancel($this->timezone);
        $this->currentSession = null;
        $this->remainingSeconds = null;
        $this->refreshRecentSessions();
    }

    public function tick(): void
    {
        if (! $this->currentSession) {
            $this->refreshSession();
            return;
        }

        $this->currentSession->refresh();

        if ($this->currentSession->status === PomodoroSession::STATUS_RUNNING) {
            $remaining = $this->currentSession->secondsRemaining($this->timezone);

            if ($remaining <= 0) {
                $finishedSession = $this->currentSession;
                $finishedSession->markFinished($this->timezone);
                $this->currentSession = null;
                $this->remainingSeconds = null;
                $this->refreshRecentSessions();
                $this->handleFinishedSession($finishedSession);

                return;
            }

            $this->remainingSeconds = $remaining;
        } elseif ($this->currentSession->status === PomodoroSession::STATUS_PAUSED) {
            $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
        } else {
            $this->currentSession = null;
            $this->remainingSeconds = null;
            $this->refreshRecentSessions();
        }
    }

    public function getDisplayTimeProperty(): string
    {
        $seconds = $this->remainingSeconds ?? ($this->focusMinutes * 60);
        $seconds = max(0, $seconds);

        $minutes = intdiv($seconds, 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function render()
    {
        return view('livewire.pomodoro.timer', [
            'displayTime' => $this->displayTime,
            'currentLabel' => $this->currentLabel(),
        ]);
    }

    public function beginEditingSession(int $sessionId): void
    {
        $session = $this->user()->pomodoroSessions()->find($sessionId);

        if (! $session) {
            return;
        }

        $this->editingSessionId = $session->id;
        $this->editingType = $session->type;
        $durationMinutes = (int) ceil(max(1, $session->duration_seconds ?? 60) / 60);
        $this->editingDurationMinutes = max(1, $durationMinutes);
    }

    public function cancelEditingSession(): void
    {
        $this->resetEditing();
    }

    public function saveEditedSession(): void
    {
        if (! $this->editingSessionId) {
            return;
        }

        $this->validateOnly('editingType');

        $this->validateOnly('editingDurationMinutes');

        $session = $this->user()
            ->pomodoroSessions()
            ->find($this->editingSessionId);

        if (! $session) {
            $this->resetEditing();

            return;
        }

        $session->forceFill([
            'type' => $this->editingType,
            'duration_seconds' => $this->editingDurationMinutes * 60,
        ])->save();

        if ($this->currentSession && $this->currentSession->id === $session->id) {
            $this->currentSession = $session->fresh();
            $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
        }

        $this->dispatch('session-saved');

        $this->resetEditing();
        $this->refreshRecentSessions();
    }

    public function deleteSession(int $sessionId): void
    {
        $session = $this->user()->pomodoroSessions()->find($sessionId);

        if (! $session) {
            return;
        }

        if ($this->currentSession && $this->currentSession->id === $session->id) {
            $this->currentSession = null;
            $this->remainingSeconds = null;
        }

        if ($this->editingSessionId === $session->id) {
            $this->resetEditing();
        }

        $session->delete();

        $this->dispatch('session-deleted');

        $this->refreshRecentSessions();
    }

    protected function currentLabel(): string
    {
        if (! $this->currentSession) {
            return 'Focus';
        }

        return match ($this->currentSession->type) {
            PomodoroSession::TYPE_SHORT => 'Short Break',
            PomodoroSession::TYPE_LONG => 'Long Break',
            default => 'Focus',
        };
    }

    protected function startSession(string $type, int $minutes): void
    {
        $minutes = max(1, $minutes);

        if ($this->currentSession) {
            $this->currentSession->cancel($this->timezone);
        }

        $activeTimezone = $this->timezone !== ''
            ? $this->timezone
            : config('app.timezone', 'UTC');

        $nowUtc = Carbon::now('UTC')->toImmutable();
        $nowLocal = $nowUtc->setTimezone($activeTimezone);
        $durationSeconds = $minutes * 60;

        $session = $this->user()->pomodoroSessions()->create([
            'type' => $type,
            'status' => PomodoroSession::STATUS_RUNNING,
            'started_at' => $nowUtc,
            'duration_seconds' => $durationSeconds,
            'meta' => [
                'timezone' => $activeTimezone,
                'initial_started_at' => $nowLocal->format('Y-m-d H:i'),
                'local_started_at' => $nowLocal->format('Y-m-d H:i'),
                'started_at_utc' => $nowUtc->format('Y-m-d H:i:s'),
            ],
        ]);

        $this->currentSession = $session;
        $this->remainingSeconds = $durationSeconds;
        $this->refreshRecentSessions();
    }

    protected function handleFinishedSession(PomodoroSession $session): void
    {
        if ($session->type !== PomodoroSession::TYPE_FOCUS) {
            return;
        }

        $completedFocusCount = $this->user()
            ->pomodoroSessions()
            ->where('type', PomodoroSession::TYPE_FOCUS)
            ->where('status', PomodoroSession::STATUS_FINISHED)
            ->count();

        if ($this->longBreakEvery > 0
            && $completedFocusCount > 0
            && $completedFocusCount % $this->longBreakEvery === 0
        ) {
            $this->startSession(PomodoroSession::TYPE_LONG, $this->longBreakMinutes);

            return;
        }

        $this->startSession(PomodoroSession::TYPE_SHORT, $this->shortBreakMinutes);
    }

    protected function refreshSession(): void
    {
        $this->currentSession = $this->user()
            ->pomodoroSessions()
            ->active()
            ->latest('started_at')
            ->first();

        if ($this->currentSession) {
            $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
        }
    }

    protected function refreshRecentSessions(): void
    {
        $this->recentSessions = $this->user()
            ->pomodoroSessions()
            ->latest('started_at')
            ->limit(5)
            ->get()
            ->map(function (PomodoroSession $session) {
                $meta = $session->meta ?? [];
                $timezone = Arr::get($meta, 'timezone', $this->timezone);

                return [
                    'id' => $session->id,
                    'type' => $session->type,
                    'status' => $session->status,
                    'duration_seconds' => $session->duration_seconds,
                    'started_at' => optional($session->started_at)?->setTimezone($timezone),
                    'ended_at' => optional($session->ended_at)?->setTimezone($timezone),
                    'meta' => $meta,
                ];
            })
            ->all();
    }

    protected function resetEditing(): void
    {
        $this->editingSessionId = null;
        $this->editingType = PomodoroSession::TYPE_FOCUS;
        $this->editingDurationMinutes = 25;
    }

    protected function user(): Authenticatable
    {
        /** @var Authenticatable $user */
        $user = Auth::user();

        return $user;
    }
}
