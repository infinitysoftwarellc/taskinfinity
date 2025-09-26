<?php

namespace App\Livewire\Pomodoro;

use App\Models\PomodoroSession;
use App\Models\PomodoroSetting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use DateTimeInterface;
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

    #[Validate('nullable|string|max:500')]
    public ?string $focusNote = null;

    public ?PomodoroSession $currentSession = null;

    public ?int $remainingSeconds = null;

    public ?int $currentDurationSeconds = null;

    public ?int $editingSessionId = null;

    #[Validate('required|in:focus,short,long')]
    public string $editingType = PomodoroSession::TYPE_FOCUS;

    #[Validate('required|integer|min:1|max:180')]
    public int $editingDurationMinutes = 25;

    /**
     * @var list<array<string, mixed>>
     */
    public array $recentSessions = [];

    /**
     * @var array<string, int>
     */
    public array $overview = [
        'today_pomodoros' => 0,
        'today_focus_seconds' => 0,
        'total_pomodoros' => 0,
        'total_focus_seconds' => 0,
    ];

    /**
     * @var list<array<string, mixed>>
     */
    public array $todayRecords = [];

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
        $this->updateCurrentDurationReference();
        $this->refreshMetrics();
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

        $this->persistSettings();
        $this->notifySettingsSaved();
    }

    public function updated(string $propertyName): void
    {
        if (! in_array($propertyName, ['focusMinutes', 'shortBreakMinutes', 'longBreakMinutes', 'longBreakEvery'], true)) {
            return;
        }

        $this->validateOnly($propertyName);

        $this->persistSettings();
        $this->notifySettingsSaved();
    }

    protected function persistSettings(): void
    {
        /** @var PomodoroSetting $settings */
        $settings = $this->user()->pomodoroSetting;

        $settings->update([
            'focus_minutes' => $this->focusMinutes,
            'short_break_minutes' => $this->shortBreakMinutes,
            'long_break_minutes' => $this->longBreakMinutes,
            'long_break_every' => $this->longBreakEvery,
        ]);
    }

    protected function notifySettingsSaved(): void
    {
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
        $this->updateCurrentDurationReference();
    }

    public function resume(): void
    {
        if (! $this->currentSession || $this->currentSession->status !== PomodoroSession::STATUS_PAUSED) {
            return;
        }

        $this->currentSession->resume($this->timezone);
        $this->currentSession->refresh();
        $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
        $this->updateCurrentDurationReference();
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
        $this->updateCurrentDurationReference();
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
            $this->updateCurrentDurationReference();
        } elseif ($this->currentSession->status === PomodoroSession::STATUS_PAUSED) {
            $this->remainingSeconds = $this->currentSession->secondsRemaining($this->timezone);
            $this->updateCurrentDurationReference();
        } else {
            $this->currentSession = null;
            $this->remainingSeconds = null;
            $this->refreshRecentSessions();
            $this->updateCurrentDurationReference();
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
            'progressRatio' => $this->progressRatio(),
            'isRunning' => $this->currentSession && $this->currentSession->status === PomodoroSession::STATUS_RUNNING,
            'isPaused' => $this->currentSession && $this->currentSession->status === PomodoroSession::STATUS_PAUSED,
            'currentSessionType' => $this->currentSession?->type ?? PomodoroSession::TYPE_FOCUS,
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
        $this->updateCurrentDurationReference();
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

        if ($type === PomodoroSession::TYPE_FOCUS) {
            $this->validateOnly('focusNote');
        }

        $nowUtc = Carbon::now('UTC')->toImmutable();
        $nowLocal = $nowUtc->setTimezone($activeTimezone);
        $durationSeconds = $minutes * 60;

        $focusNote = $type === PomodoroSession::TYPE_FOCUS
            ? $this->normalizedFocusNote()
            : null;

        $meta = [
            'timezone' => $activeTimezone,
            'initial_started_at' => $nowLocal->format('Y-m-d H:i'),
            'local_started_at' => $nowLocal->format('Y-m-d H:i'),
            'started_at_utc' => $nowUtc->format('Y-m-d H:i:s'),
        ];

        if ($focusNote !== null) {
            $meta['focus_note'] = $focusNote;
        }

        $session = $this->user()->pomodoroSessions()->create([
            'type' => $type,
            'status' => PomodoroSession::STATUS_RUNNING,
            'started_at' => $nowUtc,
            'duration_seconds' => $durationSeconds,
            'meta' => $meta,
        ]);

        if ($type === PomodoroSession::TYPE_FOCUS) {
            $this->focusNote = $focusNote;
        }

        $this->currentSession = $session;
        $this->remainingSeconds = $durationSeconds;
        $this->refreshRecentSessions();
        $this->updateCurrentDurationReference();
    }

    protected function handleFinishedSession(PomodoroSession $session): void
    {
        if ($session->type === PomodoroSession::TYPE_FOCUS) {
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

            return;
        }

        if (in_array($session->type, [PomodoroSession::TYPE_SHORT, PomodoroSession::TYPE_LONG], true)) {
            $this->startSession(PomodoroSession::TYPE_FOCUS, $this->focusMinutes);
        }
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
            if ($this->currentSession->type === PomodoroSession::TYPE_FOCUS && $this->focusNote === null) {
                $this->focusNote = $this->extractFocusNote($this->currentSession->meta ?? []);
            }
        } else {
            $this->remainingSeconds = null;
        }

        $this->updateCurrentDurationReference();
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

        $this->refreshMetrics();
    }

    protected function resetEditing(): void
    {
        $this->editingSessionId = null;
        $this->editingType = PomodoroSession::TYPE_FOCUS;
        $this->editingDurationMinutes = 25;
    }

    protected function updateCurrentDurationReference(): void
    {
        if ($this->currentSession) {
            $durationSeconds = $this->currentSession->duration_seconds ?? ($this->focusMinutes * 60);
            $this->currentDurationSeconds = max(60, (int) $durationSeconds);

            return;
        }

        $label = $this->currentLabel();
        $minutes = $this->defaultDurationMinutes($label);
        $this->currentDurationSeconds = max(60, $minutes * 60);
    }

    protected function defaultDurationMinutes(string $label): int
    {
        return match ($label) {
            'Short Break' => $this->shortBreakMinutes,
            'Long Break' => $this->longBreakMinutes,
            default => $this->focusMinutes,
        };
    }

    protected function progressRatio(): float
    {
        $duration = $this->currentDurationSeconds ?? ($this->focusMinutes * 60);

        if ($duration <= 0) {
            return 0.0;
        }

        if (! $this->currentSession || $this->remainingSeconds === null) {
            return 0.0;
        }

        $remaining = max(0, $this->remainingSeconds);

        return max(0, min(1, 1 - ($remaining / $duration)));
    }

    protected function refreshMetrics(): void
    {
        $activeTimezone = $this->timezone !== ''
            ? $this->timezone
            : config('app.timezone', 'UTC');

        $now = Carbon::now($activeTimezone);
        $startOfDayUtc = $now->copy()->startOfDay()->setTimezone('UTC');
        $endOfDayUtc = $now->copy()->endOfDay()->setTimezone('UTC');

        $todayFocusQuery = $this->user()
            ->pomodoroSessions()
            ->where('type', PomodoroSession::TYPE_FOCUS)
            ->where('status', PomodoroSession::STATUS_FINISHED)
            ->whereBetween('started_at', [$startOfDayUtc, $endOfDayUtc]);

        $todayFocusSeconds = (clone $todayFocusQuery)->sum('duration_seconds');
        $todayPomodoros = (clone $todayFocusQuery)->count();

        $totalFocusQuery = $this->user()
            ->pomodoroSessions()
            ->where('type', PomodoroSession::TYPE_FOCUS)
            ->where('status', PomodoroSession::STATUS_FINISHED);

        $totalFocusSeconds = (clone $totalFocusQuery)->sum('duration_seconds');
        $totalPomodoros = (clone $totalFocusQuery)->count();

        $this->overview = [
            'today_pomodoros' => $todayPomodoros,
            'today_focus_seconds' => (int) $todayFocusSeconds,
            'total_pomodoros' => $totalPomodoros,
            'total_focus_seconds' => (int) $totalFocusSeconds,
        ];

        $sessionsToday = $this->user()
            ->pomodoroSessions()
            ->whereBetween('started_at', [$startOfDayUtc, $endOfDayUtc])
            ->latest('started_at')
            ->limit(12)
            ->get();

        $this->todayRecords = $sessionsToday
            ->map(function (PomodoroSession $session) use ($activeTimezone) {
                $startedAt = optional($session->started_at)?->setTimezone($activeTimezone);
                $endedAt = optional($session->ended_at)?->setTimezone($activeTimezone);

                if (! $endedAt && $startedAt && $session->duration_seconds) {
                    $endedAt = $startedAt->copy()->addSeconds($session->duration_seconds);
                }

                $startMinutes = $startedAt
                    ? ($startedAt->hour * 60) + $startedAt->minute
                    : null;

                $endMinutes = $endedAt
                    ? ($endedAt->hour * 60) + $endedAt->minute
                    : ($startMinutes !== null && $session->duration_seconds
                        ? $startMinutes + (int) ceil($session->duration_seconds / 60)
                        : null);

                return [
                    'id' => $session->id,
                    'type' => $session->type,
                    'status' => $session->status,
                    'duration_seconds' => $session->duration_seconds,
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt,
                    'label' => $this->formatSessionLabel($startedAt, $endedAt),
                    'duration_label' => $this->formatDurationLabel($session->duration_seconds),
                    'start_minutes' => $startMinutes,
                    'end_minutes' => $endMinutes,
                    'focus_note' => $this->extractFocusNote($session->meta ?? []),
                ];
            })
            ->all();
    }

    protected function formatSessionLabel(?DateTimeInterface $start, ?DateTimeInterface $end): string
    {
        if (! $start) {
            return '—';
        }

        $startLabel = $start->format('H:i');
        $endLabel = $end ? $end->format('H:i') : '—';

        return $startLabel . ' - ' . $endLabel;
    }

    protected function formatDurationLabel(?int $seconds): string
    {
        $seconds = (int) max(0, $seconds ?? 0);

        if ($seconds === 0) {
            return '0m';
        }

        $minutes = (int) round($seconds / 60);

        if ($minutes < 60) {
            return $minutes . 'm';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours . 'h';
        }

        return sprintf('%dh %02dm', $hours, $remainingMinutes);
    }

    protected function normalizedFocusNote(): ?string
    {
        if ($this->focusNote === null) {
            return null;
        }

        $note = trim($this->focusNote);

        return $note === '' ? null : $note;
    }

    protected function extractFocusNote(array $meta): ?string
    {
        $note = $meta['focus_note'] ?? null;

        if (! is_string($note)) {
            return null;
        }

        $note = trim($note);

        return $note === '' ? null : $note;
    }

    protected function user(): Authenticatable
    {
        /** @var Authenticatable $user */
        $user = Auth::user();

        return $user;
    }
}
