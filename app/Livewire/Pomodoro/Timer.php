<?php

namespace App\Livewire\Pomodoro;

use App\Models\PomodoroDailyStat;
use App\Models\PomodoroPause;
use App\Models\PomodoroSession;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Timer extends Component
{
    private const STOP_SAVE_THRESHOLD = 300; // 5 minutes

    public int $focusDuration = 25 * 60;
    public int $shortBreakDuration = 5 * 60;
    public int $longBreakDuration = 15 * 60;

    public int $remainingSeconds = 0;
    public bool $running = false;
    public string $phase = 'focus';

    public ?int $sessionId = null;
    public ?int $activePauseId = null;

    public int $todaysPomo = 0;
    public int $todaysFocusSeconds = 0;
    public int $totalPomo = 0;
    public int $totalFocusSeconds = 0;
    public array $records = [];

    public bool $showStopConfirmation = false;
    public bool $allowSaveOnStop = false;
    public bool $showRecordModal = false;
    public array $recordModal = [];

    protected ?PomodoroSession $sessionCache = null;
    protected ?PomodoroPause $pauseCache = null;
    protected string $timezone;
    protected int $focusSessionsSinceLongBreak = 0;

    public function mount(): void
    {
        $this->timezone = $this->userTimezone();
        $this->remainingSeconds = $this->focusDuration;
        $this->loadActiveSession();
        $this->refreshStatistics();
        $this->refreshRecords();
        $this->updateFocusStreak();
    }

    public function hydrate(): void
    {
        $this->timezone = $this->userTimezone();
    }

    public function selectPhase(string $phase): void
    {
        if ($this->sessionId) {
            return;
        }

        if (! in_array($phase, ['focus', 'short_break', 'long_break'], true)) {
            return;
        }

        $this->phase = $phase;
        $this->remainingSeconds = $this->phaseDuration($phase);
        $this->running = false;
    }

    public function render(): View
    {
        return view('livewire.pomodoro.timer');
    }

    public function toggleTimer(): void
    {
        if ($this->sessionId) {
            $session = $this->currentSession();
            if (! $session) {
                $this->resetToIdle();
                return;
            }

            if ($this->running) {
                $this->pauseActiveSession($session);
            } else {
                $this->resumeActiveSession($session);
            }

            return;
        }

        $this->startPhase($this->phase);
    }

    public function confirmStop(): void
    {
        $session = $this->currentSession();

        if (! $session) {
            $this->showStopConfirmation = true;
            $this->allowSaveOnStop = false;
            return;
        }

        $elapsed = $this->elapsedSeconds($session);
        $this->allowSaveOnStop = $elapsed >= self::STOP_SAVE_THRESHOLD;
        $this->showStopConfirmation = true;
    }

    public function cancelStop(): void
    {
        $this->showStopConfirmation = false;
    }

    public function stopWithoutSaving(): void
    {
        $session = $this->currentSession();
        if ($session) {
            DB::transaction(function () use ($session) {
                $session->pauses()->delete();
                $session->delete();
            });
        }

        $this->resetToIdle();
        $this->refreshStatistics();
        $this->refreshRecords();
        $this->updateFocusStreak();
        $this->showStopConfirmation = false;
    }

    public function stopAndSave(): void
    {
        $session = $this->currentSession();
        if (! $session) {
            $this->stopWithoutSaving();
            return;
        }

        $this->finalizeSession($session, false);
        $this->prepareNextPhaseAfter($session->fresh());
        $this->refreshStatistics();
        $this->refreshRecords();
        $this->updateFocusStreak();
        $this->showStopConfirmation = false;
    }

    public function tick(): void
    {
        $session = $this->currentSession();

        if (! $session) {
            $this->allowSaveOnStop = false;
            if (! $this->running) {
                $this->remainingSeconds = $this->phaseDuration($this->phase);
            }
            return;
        }

        $this->remainingSeconds = $this->computeRemainingSeconds($session);
        $elapsed = $this->elapsedSeconds($session);
        $this->allowSaveOnStop = $elapsed >= self::STOP_SAVE_THRESHOLD;

        if ($this->running && $this->remainingSeconds <= 0) {
            $this->finalizeSession($session, true);
            $this->prepareNextPhaseAfter($session->fresh(), true);
            $this->refreshStatistics();
            $this->refreshRecords();
            $this->updateFocusStreak();
        }
    }

    public function getClockLabelProperty(): string
    {
        $seconds = max(0, $this->remainingSeconds);

        return gmdate('i:s', $seconds);
    }

    public function getProgressDegreesProperty(): float
    {
        $duration = $this->currentTargetDuration();
        if ($duration <= 0) {
            return 0.0f;
        }

        $ratio = max(0, min(1, 1 - ($this->remainingSeconds / $duration)));

        return $ratio * 360;
    }

    public function getTodayFocusHoursProperty(): int
    {
        return intdiv($this->todaysFocusSeconds, 3600);
    }

    public function getTodayFocusMinutesRemainderProperty(): int
    {
        return intdiv($this->todaysFocusSeconds % 3600, 60);
    }

    public function getTotalFocusHoursProperty(): int
    {
        return intdiv($this->totalFocusSeconds, 3600);
    }

    public function getTotalFocusMinutesRemainderProperty(): int
    {
        return intdiv($this->totalFocusSeconds % 3600, 60);
    }

    public function getDateLabelProperty(): string
    {
        return Carbon::now($this->timezone)->translatedFormat('M d');
    }

    public function getRecordItemsProperty(): array
    {
        return $this->records;
    }

    public function getPhaseLabelProperty(): string
    {
        return match ($this->phase) {
            'short_break' => __('Pausa'),
            'long_break' => __('Pausa longa'),
            default => __('Pomodoro'),
        };
    }

    public function openRecord(int $recordId): void
    {
        $record = collect($this->records)->firstWhere('id', $recordId);

        if (! $record) {
            $record = $this->loadRecordFromDatabase($recordId);
        }

        if (! $record) {
            return;
        }

        $this->recordModal = $record;
        $this->showRecordModal = true;
    }

    public function closeRecordModal(): void
    {
        $this->showRecordModal = false;
        $this->recordModal = [];
    }

    public function deleteRecord(int $recordId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $session = PomodoroSession::where('user_id', $user->id)
            ->where('id', $recordId)
            ->first();

        if (! $session) {
            return;
        }

        DB::transaction(function () use ($session) {
            $session->pauses()->delete();
            $session->delete();
        });

        $this->closeRecordModal();
        $this->refreshStatistics();
        $this->refreshRecords();
        $this->updateFocusStreak();
    }

    protected function loadActiveSession(): void
    {
        $userId = Auth::id();
        if (! $userId) {
            $this->resetToIdle();
            return;
        }

        $session = PomodoroSession::with(['pauses' => function ($query) {
            $query->orderByDesc('paused_at_server');
        }])
            ->where('user_id', $userId)
            ->whereNull('ended_at_server')
            ->latest('started_at_server')
            ->first();

        if (! $session) {
            $this->resetToIdle();
            return;
        }

        $this->sessionCache = $session;
        $this->sessionId = $session->id;
        $this->phase = $this->determinePhase($session);
        $this->remainingSeconds = $this->computeRemainingSeconds($session);

        if ($this->remainingSeconds <= 0 && ! $session->ended_at_server) {
            $this->finalizeSession($session, true);
            $this->prepareNextPhaseAfter($session->fresh(), true);
            $this->refreshStatistics();
            $this->refreshRecords();
            $this->updateFocusStreak();
            $this->loadActiveSession();

            return;
        }

        $activePause = $session->pauses->firstWhere('resumed_at_server', null);
        if ($activePause) {
            $this->activePauseId = $activePause->id;
            $this->pauseCache = $activePause;
            $this->running = false;
        } else {
            $this->running = true;
        }

        $this->allowSaveOnStop = $this->elapsedSeconds($session) >= self::STOP_SAVE_THRESHOLD;
    }

    protected function startPhase(string $phase): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $duration = $this->phaseDuration($phase);
        $clientNow = Carbon::now($this->timezone);
        $serverNow = Carbon::now(config('app.timezone'));

        $session = PomodoroSession::create([
            'user_id' => $user->id,
            'type' => $phase === 'focus' ? 'work' : 'break',
            'started_at_client' => $clientNow->format('Y-m-d H:i:s'),
            'client_timezone' => $this->timezone,
            'client_utc_offset_minutes' => $clientNow->utcOffset(),
            'started_at_server' => $serverNow,
            'duration_seconds' => $duration,
            'pause_count' => 0,
            'pause_total_seconds' => 0,
            'notes' => $phase === 'focus' ? null : $phase,
            'created_at' => $serverNow,
        ]);

        $this->sessionCache = $session;
        $this->sessionId = $session->id;
        $this->activePauseId = null;
        $this->pauseCache = null;
        $this->phase = $phase;
        $this->remainingSeconds = $duration;
        $this->running = true;
        $this->allowSaveOnStop = false;
    }

    protected function finalizeSession(PomodoroSession $session, bool $completedByTimer): void
    {
        $session->refresh();
        $elapsed = $this->elapsedSeconds($session);
        $duration = $completedByTimer ? $session->duration_seconds : min($session->duration_seconds, $elapsed);

        $serverEnd = Carbon::now(config('app.timezone'));
        $clientStart = $this->clientDate($session, 'started_at_client');
        $clientEnd = $clientStart->copy()->addSeconds($duration);

        DB::transaction(function () use ($session, $duration, $serverEnd, $clientEnd) {
            $session->update([
                'ended_at_client' => $clientEnd->format('Y-m-d H:i:s'),
                'ended_at_server' => $serverEnd,
                'duration_seconds' => $duration,
                'pause_total_seconds' => $session->pause_total_seconds,
            ]);

            $this->recordDailyStats($session->fresh(), $duration);
        });

        $this->sessionCache = $session->fresh();

        if ($completedByTimer) {
            $this->dispatch('pomodoro-completed', ['type' => $session->type]);
        }
    }

    protected function prepareNextPhaseAfter(PomodoroSession $session, bool $autoStartBreak = false): void
    {
        $phase = $this->determinePhase($session);

        if ($phase === 'focus') {
            $this->focusSessionsSinceLongBreak++;
            if ($autoStartBreak) {
                $nextPhase = $this->focusSessionsSinceLongBreak % 4 === 0 ? 'long_break' : 'short_break';
                $this->startPhase($nextPhase);
                return;
            }

            $this->resetToIdle();

            return;
        }

        if ($session->notes === 'long_break') {
            $this->focusSessionsSinceLongBreak = 0;
        }

        $this->resetToIdle();
    }

    protected function pauseActiveSession(PomodoroSession $session): void
    {
        if ($this->activePauseId) {
            return;
        }

        $serverNow = Carbon::now(config('app.timezone'));
        $clientNow = Carbon::now($session->client_timezone);

        $pause = PomodoroPause::create([
            'session_id' => $session->id,
            'paused_at_client' => $clientNow->format('Y-m-d H:i:s'),
            'resumed_at_client' => null,
            'duration_seconds' => 0,
            'paused_at_server' => $serverNow,
            'resumed_at_server' => null,
        ]);

        $this->activePauseId = $pause->id;
        $this->pauseCache = $pause;
        $this->running = false;
    }

    protected function resumeActiveSession(PomodoroSession $session): void
    {
        if (! $this->activePauseId) {
            $this->running = true;
            return;
        }

        $pause = PomodoroPause::find($this->activePauseId);
        if (! $pause) {
            $this->activePauseId = null;
            $this->pauseCache = null;
            $this->running = true;
            return;
        }

        $serverNow = Carbon::now(config('app.timezone'));
        $clientNow = Carbon::now($session->client_timezone);

        $pausedAtServer = $pause->paused_at_server ?? $serverNow;
        $duration = max(0, $pausedAtServer->diffInSeconds($serverNow));

        DB::transaction(function () use ($pause, $clientNow, $serverNow, $duration, $session) {
            $pause->update([
                'resumed_at_client' => $clientNow->format('Y-m-d H:i:s'),
                'duration_seconds' => $duration,
                'resumed_at_server' => $serverNow,
            ]);

            $session->update([
                'pause_count' => ($session->pause_count ?? 0) + 1,
                'pause_total_seconds' => ($session->pause_total_seconds ?? 0) + $duration,
            ]);
        });

        $this->activePauseId = null;
        $this->pauseCache = null;
        $this->sessionCache = $session->fresh('pauses');
        $this->running = true;
    }

    protected function computeRemainingSeconds(PomodoroSession $session): int
    {
        $duration = $session->duration_seconds;
        $elapsed = $this->elapsedSeconds($session);

        return max(0, $duration - $elapsed);
    }

    protected function elapsedSeconds(PomodoroSession $session): int
    {
        $session->refresh();
        $nowServer = Carbon::now(config('app.timezone'));
        $startServer = $session->started_at_server ?? $nowServer;
        $pauseSeconds = $session->pause_total_seconds ?? 0;

        $activePause = $this->activePause();
        if ($activePause) {
            $pausedAtServer = $activePause->paused_at_server ?? $nowServer;
            $pauseSeconds += max(0, $pausedAtServer->diffInSeconds($nowServer));
        }

        return max(0, $startServer->diffInSeconds($nowServer) - $pauseSeconds);
    }

    protected function determinePhase(PomodoroSession $session): string
    {
        if ($session->type === 'work') {
            return 'focus';
        }

        return $session->notes === 'long_break' ? 'long_break' : 'short_break';
    }

    protected function phaseDuration(string $phase): int
    {
        return match ($phase) {
            'short_break' => $this->shortBreakDuration,
            'long_break' => $this->longBreakDuration,
            default => $this->focusDuration,
        };
    }

    protected function currentTargetDuration(): int
    {
        if (! $this->sessionId) {
            return $this->phaseDuration($this->phase);
        }

        $session = $this->currentSession();
        if (! $session) {
            return $this->phaseDuration($this->phase);
        }

        return $session->duration_seconds;
    }

    protected function recordDailyStats(PomodoroSession $session, int $duration): void
    {
        $dateLocal = $this->clientDate($session, 'started_at_client')->toImmutable()->toDateString();

        $stat = PomodoroDailyStat::firstOrNew([
            'user_id' => $session->user_id,
            'date_local' => $dateLocal,
        ]);

        $stat->sessions_count = ($stat->sessions_count ?? 0) + 1;
        if ($session->type === 'work') {
            $stat->work_seconds = ($stat->work_seconds ?? 0) + $duration;
        } else {
            $stat->break_seconds = ($stat->break_seconds ?? 0) + $duration;
        }

        $stat->save();
    }

    protected function refreshStatistics(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->todaysPomo = 0;
            $this->todaysFocusSeconds = 0;
            $this->totalPomo = 0;
            $this->totalFocusSeconds = 0;
            return;
        }

        $todayLocal = Carbon::now($this->timezone)->toDateString();

        $todaySessions = PomodoroSession::where('user_id', $user->id)
            ->whereNotNull('ended_at_server')
            ->whereDate('started_at_client', $todayLocal)
            ->get(['type', 'duration_seconds']);

        $this->todaysPomo = $todaySessions->where('type', 'work')->count();
        $this->todaysFocusSeconds = (int) $todaySessions
            ->where('type', 'work')
            ->sum('duration_seconds');

        $allSessions = PomodoroSession::where('user_id', $user->id)
            ->whereNotNull('ended_at_server')
            ->get(['type', 'duration_seconds']);

        $this->totalPomo = $allSessions->where('type', 'work')->count();
        $this->totalFocusSeconds = (int) $allSessions
            ->where('type', 'work')
            ->sum('duration_seconds');
    }

    protected function refreshRecords(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->records = [];
            return;
        }

        $sessions = PomodoroSession::where('user_id', $user->id)
            ->whereNotNull('ended_at_server')
            ->orderByDesc('started_at_server')
            ->limit(5)
            ->get();

        $this->records = $sessions->map(function (PomodoroSession $session) {
            return $this->formatRecord($session);
        })->all();
    }

    protected function resetToIdle(): void
    {
        $this->sessionId = null;
        $this->sessionCache = null;
        $this->activePauseId = null;
        $this->pauseCache = null;
        $this->running = false;
        $this->phase = 'focus';
        $this->remainingSeconds = $this->focusDuration;
        $this->allowSaveOnStop = false;
    }

    protected function currentSession(): ?PomodoroSession
    {
        if (! $this->sessionId) {
            return null;
        }

        if ($this->sessionCache && $this->sessionCache->id === $this->sessionId) {
            return $this->sessionCache->refresh();
        }

        $this->sessionCache = PomodoroSession::with('pauses')->find($this->sessionId);

        return $this->sessionCache;
    }

    protected function activePause(): ?PomodoroPause
    {
        if ($this->pauseCache && $this->pauseCache->id === $this->activePauseId) {
            return $this->pauseCache->refresh();
        }

        if (! $this->sessionId || ! $this->activePauseId) {
            return null;
        }

        $this->pauseCache = PomodoroPause::find($this->activePauseId);

        return $this->pauseCache;
    }

    protected function updateFocusStreak(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->focusSessionsSinceLongBreak = 0;
            return;
        }

        $lastLongBreak = PomodoroSession::where('user_id', $user->id)
            ->where('type', 'break')
            ->where('notes', 'long_break')
            ->whereNotNull('ended_at_server')
            ->latest('started_at_server')
            ->first();

        $query = PomodoroSession::where('user_id', $user->id)
            ->where('type', 'work')
            ->whereNotNull('ended_at_server');

        if ($lastLongBreak) {
            $query->where('started_at_server', '>', $lastLongBreak->started_at_server);
        }

        $this->focusSessionsSinceLongBreak = $query->count();
    }

    protected function userTimezone(): string
    {
        $user = Auth::user();

        return $user?->timezone ?? config('app.timezone');
    }

    protected function clientDate(PomodoroSession $session, string $column): Carbon
    {
        $raw = $session->getRawOriginal($column);
        if (! $raw) {
            return Carbon::now($session->client_timezone ?? $this->timezone);
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $raw, $session->client_timezone ?? $this->timezone);
    }

    protected function formatRecord(PomodoroSession $session): array
    {
        $start = $this->clientDate($session, 'started_at_client');
        $endRaw = $session->getRawOriginal('ended_at_client');
        $end = $endRaw
            ? Carbon::createFromFormat('Y-m-d H:i:s', $endRaw, $session->client_timezone ?? $this->timezone)
            : $start->copy()->addSeconds($session->duration_seconds);

        $phase = $this->determinePhase($session);

        return [
            'id' => $session->id,
            'phase' => $phase,
            'type_label' => $this->phaseDisplayName($phase),
            'time_label' => sprintf('%s – %s', $start->format('H:i'), $end->format('H:i')),
            'start_label' => $start->format('H:i'),
            'end_label' => $end->format('H:i'),
            'date_label' => $start->translatedFormat('d MMM'),
            'date_full_label' => $start->translatedFormat('d MMMM Y'),
            'duration_minutes' => intdiv($session->duration_seconds, 60),
            'duration_label' => $this->formatDurationLabel($session->duration_seconds),
            'duration_full_label' => $this->formatDurationLabel($session->duration_seconds),
            'notes' => $session->notes,
        ];
    }

    protected function loadRecordFromDatabase(int $recordId): ?array
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $session = PomodoroSession::where('user_id', $user->id)
            ->whereNotNull('ended_at_server')
            ->find($recordId);

        if (! $session) {
            return null;
        }

        return $this->formatRecord($session);
    }

    protected function phaseDisplayName(string $phase): string
    {
        return match ($phase) {
            'short_break' => __('Pausa'),
            'long_break' => __('Pausa longa'),
            default => __('Pomodoro'),
        };
    }

    protected function formatDurationLabel(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes > 0 && $remainingSeconds > 0) {
            return sprintf('%dm %ds', $minutes, $remainingSeconds);
        }

        if ($minutes > 0) {
            return sprintf('%dm', $minutes);
        }

        return sprintf('%ds', $remainingSeconds);
    }
}
