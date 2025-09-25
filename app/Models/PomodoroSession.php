<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    use HasFactory;

    public const TYPE_FOCUS = 'focus';
    public const TYPE_SHORT = 'short';
    public const TYPE_LONG = 'long';

    public const STATUS_RUNNING = 'running';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PAUSED = 'paused';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'task_id',
        'type',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
        'remaining_seconds',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'immutable_datetime',
        'ended_at' => 'immutable_datetime',
        'duration_seconds' => 'integer',
        'remaining_seconds' => 'integer',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_RUNNING, self::STATUS_PAUSED]);
    }

    public function secondsRemaining(string $timezone): int
    {
        if ($this->status === self::STATUS_PAUSED) {
            return max(0, (int) ($this->remaining_seconds ?? 0));
        }

        if ($this->status !== self::STATUS_RUNNING) {
            return 0;
        }

        $metaTimezone = $this->meta['timezone'] ?? null;
        if (! is_string($metaTimezone) || $metaTimezone === '') {
            $metaTimezone = $timezone !== '' ? $timezone : null;
        }

        $timezoneForCalculation = $this->started_at instanceof CarbonImmutable
            ? $this->started_at->getTimezone()->getName()
            : ($metaTimezone ?? config('app.timezone', 'UTC'));

        $now = Carbon::now($timezoneForCalculation)->toImmutable();

        $startedAt = $this->started_at instanceof CarbonImmutable
            ? $this->started_at
            : $now;

        $elapsed = $now->getTimestamp() - $startedAt->getTimestamp();

        $remaining = $this->duration_seconds - max(0, $elapsed);

        return (int) max(0, $remaining);
    }

    public function markFinished(string $timezone): void
    {
        $endedAtLocal = Carbon::now($timezone)->toImmutable();
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_finished_at'] = $endedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_FINISHED,
            'ended_at' => $endedAtLocal,
            'remaining_seconds' => 0,
            'meta' => $meta,
        ])->save();
    }

    public function cancel(string $timezone): void
    {
        $endedAtLocal = Carbon::now($timezone)->toImmutable();
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_canceled_at'] = $endedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_CANCELED,
            'ended_at' => $endedAtLocal,
            'meta' => $meta,
        ])->save();
    }

    public function pause(int $remainingSeconds, string $timezone): void
    {
        $pausedAtLocal = Carbon::now($timezone)->toImmutable();
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_paused_at'] = $pausedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_PAUSED,
            'remaining_seconds' => max(0, $remainingSeconds),
            'meta' => $meta,
        ])->save();
    }

    public function resume(string $timezone): void
    {
        $remaining = $this->remaining_seconds ?? $this->duration_seconds;
        $resumedAtLocal = Carbon::now($timezone)->toImmutable();
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_resumed_at'] = $resumedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_RUNNING,
            'started_at' => $resumedAtLocal,
            'duration_seconds' => max(0, $remaining),
            'remaining_seconds' => null,
            'meta' => $meta,
        ])->save();
    }

    protected function ensureMetaTimezone(string $timezone): array
    {
        $meta = $this->meta ?? [];

        if (! isset($meta['timezone']) && $timezone !== '') {
            $meta['timezone'] = $timezone;
        }

        if (isset($meta['timezone']) && $meta['timezone'] === '' && $timezone !== '') {
            $meta['timezone'] = $timezone;
        }

        return $meta;
    }

}
