<?php

namespace App\Livewire\Pomodoro;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Timer extends Component
{
    private const STATE_KEY = 'pomodoro.state';
    private const MAX_RECORDS = 5;

    public int $totalSeconds = 20 * 60;
    public int $remainingSeconds = 0;
    public bool $running = false;
    public ?int $lastTick = null;
    public string $todaysKey = '';
    public int $todaysPomo = 0;
    public int $todaysFocusMinutes = 0;
    public int $totalPomo = 0;
    public int $totalFocusMinutes = 0;
    public array $records = [];
    public ?string $currentSessionStartedAt = null;

    public function mount(): void
    {
        $this->loadState();
        $this->syncWithCurrentDay();
    }

    public function hydrate(): void
    {
        $this->syncWithCurrentDay();
    }

    public function render(): View
    {
        return view('livewire.pomodoro.timer');
    }

    public function toggleTimer(): void
    {
        if ($this->running) {
            $this->pauseTimer();
        } else {
            $this->startTimer();
        }
    }

    public function resetTimer(): void
    {
        $this->running = false;
        $this->remainingSeconds = $this->totalSeconds;
        $this->lastTick = now()->timestamp;
        $this->currentSessionStartedAt = null;
        $this->persistState();
    }

    public function tick(): void
    {
        if (! $this->running) {
            return;
        }

        $this->applyElapsed();
        $this->persistState();
    }

    public function getClockLabelProperty(): string
    {
        $seconds = max(0, $this->remainingSeconds);

        return gmdate('i:s', $seconds);
    }

    public function getProgressDegreesProperty(): float
    {
        if ($this->totalSeconds <= 0) {
            return 0.0;
        }

        $ratio = max(0, min(1, 1 - ($this->remainingSeconds / $this->totalSeconds)));

        return $ratio * 360;
    }

    public function getTodayFocusHoursProperty(): int
    {
        return intdiv($this->todaysFocusMinutes, 60);
    }

    public function getTodayFocusMinutesRemainderProperty(): int
    {
        return $this->todaysFocusMinutes % 60;
    }

    public function getTotalFocusHoursProperty(): int
    {
        return intdiv($this->totalFocusMinutes, 60);
    }

    public function getTotalFocusMinutesRemainderProperty(): int
    {
        return $this->totalFocusMinutes % 60;
    }

    public function getDateLabelProperty(): string
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $this->todaysKey)->translatedFormat('M d');
        } catch (\Throwable $exception) {
            return Carbon::now()->translatedFormat('M d');
        }
    }

    public function getRecordItemsProperty(): array
    {
        return collect($this->records)
            ->take(self::MAX_RECORDS)
            ->map(function ($record) {
                $startedAt = $this->parseIsoDate($record['started_at'] ?? null);
                $endedAt = $this->parseIsoDate($record['ended_at'] ?? null);
                $durationMinutes = (int) ($record['duration_minutes'] ?? 0);

                return [
                    'time_label' => sprintf('%s – %s',
                        $startedAt ? $startedAt->format('H:i') : '--:--',
                        $endedAt ? $endedAt->format('H:i') : '--:--'
                    ),
                    'duration_label' => sprintf('%dm', max(0, $durationMinutes)),
                ];
            })
            ->toArray();
    }

    protected function loadState(): void
    {
        $stored = session()->get(self::STATE_KEY);
        $state = is_array($stored) ? array_merge($this->defaultState(), $stored) : $this->defaultState();

        $this->remainingSeconds = $this->sanitizeSeconds($state['remaining_seconds'] ?? $this->totalSeconds);
        $this->running = (bool) ($state['running'] ?? false);
        $this->lastTick = $this->sanitizeTimestamp($state['last_tick'] ?? null);
        $this->todaysKey = (string) ($state['todays_key'] ?? now()->toDateString());
        $this->todaysPomo = $this->sanitizeCounter($state['todays_pomo'] ?? 0);
        $this->todaysFocusMinutes = $this->sanitizeCounter($state['todays_focus_minutes'] ?? 0);
        $this->totalPomo = $this->sanitizeCounter($state['total_pomo'] ?? 0);
        $this->totalFocusMinutes = $this->sanitizeCounter($state['total_focus_minutes'] ?? 0);
        $this->records = $this->sanitizeRecords($state['records'] ?? []);
        $this->currentSessionStartedAt = $state['current_session_started_at'] ?? null;

        if ($this->remainingSeconds <= 0) {
            $this->remainingSeconds = $this->totalSeconds;
            $this->running = false;
        }
    }

    protected function persistState(): void
    {
        session()->put(self::STATE_KEY, [
            'remaining_seconds' => $this->remainingSeconds,
            'running' => $this->running,
            'last_tick' => $this->lastTick,
            'todays_key' => $this->todaysKey,
            'todays_pomo' => $this->todaysPomo,
            'todays_focus_minutes' => $this->todaysFocusMinutes,
            'total_pomo' => $this->totalPomo,
            'total_focus_minutes' => $this->totalFocusMinutes,
            'records' => array_slice($this->records, 0, self::MAX_RECORDS),
            'current_session_started_at' => $this->currentSessionStartedAt,
        ]);
    }

    protected function startTimer(): void
    {
        if ($this->running) {
            return;
        }

        $now = Carbon::now();
        $this->running = true;
        $this->lastTick = $now->timestamp;

        if (! $this->currentSessionStartedAt) {
            $elapsed = $this->totalSeconds - $this->remainingSeconds;
            $start = $elapsed > 0 ? $now->copy()->subSeconds($elapsed) : $now;
            $this->currentSessionStartedAt = $start->toIso8601String();
        }

        $this->persistState();
    }

    protected function pauseTimer(): void
    {
        if (! $this->running) {
            return;
        }

        $this->applyElapsed();
        $this->running = false;
        $this->persistState();
    }

    protected function applyElapsed(): void
    {
        if (! $this->lastTick) {
            $this->lastTick = now()->timestamp;
            return;
        }

        $now = Carbon::now();
        $elapsed = max(0, $now->timestamp - $this->lastTick);
        $this->lastTick = $now->timestamp;

        if (! $this->running || $elapsed === 0) {
            return;
        }

        if ($elapsed >= $this->remainingSeconds) {
            $this->finalizeSession($now);
            return;
        }

        $this->remainingSeconds -= $elapsed;
    }

    protected function finalizeSession(Carbon $endMoment): void
    {
        $this->updateDailyKey($endMoment);

        $this->running = false;
        $this->remainingSeconds = $this->totalSeconds;
        $this->lastTick = $endMoment->timestamp;

        $startMoment = $this->currentSessionStartedAt
            ? $this->parseIsoDate($this->currentSessionStartedAt) ?? $endMoment->copy()->subSeconds($this->totalSeconds)
            : $endMoment->copy()->subSeconds($this->totalSeconds);

        $this->currentSessionStartedAt = null;

        $this->todaysPomo += 1;
        $this->totalPomo += 1;
        $incrementMinutes = intdiv($this->totalSeconds, 60);
        $this->todaysFocusMinutes += $incrementMinutes;
        $this->totalFocusMinutes += $incrementMinutes;

        array_unshift($this->records, [
            'started_at' => $startMoment->toIso8601String(),
            'ended_at' => $endMoment->toIso8601String(),
            'duration_minutes' => $incrementMinutes,
        ]);
        $this->records = array_slice($this->records, 0, self::MAX_RECORDS);

        $this->dispatch('pomodoro-completed');
        $this->persistState();
    }

    protected function updateDailyKey(Carbon $moment): void
    {
        $currentKey = $moment->toDateString();
        if ($this->todaysKey !== $currentKey) {
            $this->todaysKey = $currentKey;
            $this->todaysPomo = 0;
            $this->todaysFocusMinutes = 0;
        }
    }

    protected function syncWithCurrentDay(): void
    {
        $today = Carbon::today()->toDateString();
        if ($this->todaysKey !== $today) {
            $this->todaysKey = $today;
            $this->todaysPomo = 0;
            $this->todaysFocusMinutes = 0;
            $this->persistState();
        }
    }

    protected function sanitizeSeconds($value): int
    {
        $seconds = (int) ($value ?? $this->totalSeconds);

        return max(0, min($this->totalSeconds, $seconds));
    }

    protected function sanitizeTimestamp($value): int
    {
        $timestamp = is_numeric($value) ? (int) $value : now()->timestamp;

        return $timestamp > 0 ? $timestamp : now()->timestamp;
    }

    protected function sanitizeCounter($value): int
    {
        return max(0, (int) ($value ?? 0));
    }

    protected function sanitizeRecords($records): array
    {
        if (! is_array($records)) {
            return $this->defaultRecords();
        }

        return collect($records)
            ->filter(fn ($record) => is_array($record))
            ->map(function ($record) {
                return [
                    'started_at' => $record['started_at'] ?? null,
                    'ended_at' => $record['ended_at'] ?? null,
                    'duration_minutes' => (int) ($record['duration_minutes'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    protected function parseIsoDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function defaultState(): array
    {
        return [
            'remaining_seconds' => $this->totalSeconds,
            'running' => false,
            'last_tick' => now()->timestamp,
            'todays_key' => Carbon::today()->toDateString(),
            'todays_pomo' => 36,
            'todays_focus_minutes' => 11 * 60 + 34,
            'total_pomo' => 1606,
            'total_focus_minutes' => 528 * 60 + 29,
            'records' => $this->defaultRecords(),
            'current_session_started_at' => null,
        ];
    }

    protected function defaultRecords(): array
    {
        $today = Carbon::today();

        $baseTimes = [
            ['start' => ['h' => 22, 'm' => 23], 'end' => ['h' => 22, 'm' => 39], 'duration' => 15],
            ['start' => ['h' => 22, 'm' => 3], 'end' => ['h' => 22, 'm' => 23], 'duration' => 20],
            ['start' => ['h' => 21, 'm' => 15], 'end' => ['h' => 21, 'm' => 35], 'duration' => 20],
            ['start' => ['h' => 20, 'm' => 54], 'end' => ['h' => 21, 'm' => 14], 'duration' => 20],
            ['start' => ['h' => 20, 'm' => 33], 'end' => ['h' => 20, 'm' => 53], 'duration' => 20],
        ];

        return collect($baseTimes)
            ->map(function ($slot) use ($today) {
                $start = $today->copy()->setTime($slot['start']['h'], $slot['start']['m']);
                $end = $today->copy()->setTime($slot['end']['h'], $slot['end']['m']);

                return [
                    'started_at' => $start->toIso8601String(),
                    'ended_at' => $end->toIso8601String(),
                    'duration_minutes' => $slot['duration'],
                ];
            })
            ->all();
    }
}
