<?php

namespace App\Models;

use Carbon\CarbonImmutable;
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

        $startedAt = $this->started_at instanceof CarbonImmutable
            ? $this->started_at->setTimezone($timezone)
            : CarbonImmutable::now($timezone);

        $now = CarbonImmutable::now($timezone);
        $elapsed = $startedAt->diffInSeconds($now, false);

        $remaining = $this->duration_seconds - max(0, $elapsed);

        return (int) max(0, $remaining);
    }

    public function markFinished(string $timezone): void
    {
        $endedAt = CarbonImmutable::now($timezone);
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_finished_at'] = $endedAt->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_FINISHED,
            'ended_at' => $endedAt,
            'remaining_seconds' => 0,
            'meta' => $meta,
        ])->save();
    }

    public function cancel(string $timezone): void
    {
        $endedAt = CarbonImmutable::now($timezone);
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_canceled_at'] = $endedAt->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_CANCELED,
            'ended_at' => $endedAt,
            'meta' => $meta,
        ])->save();
    }

    public function pause(int $remainingSeconds, string $timezone): void
    {
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_paused_at'] = CarbonImmutable::now($timezone)->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_PAUSED,
            'remaining_seconds' => max(0, $remainingSeconds),
            'meta' => $meta,
        ])->save();
    }

    public function resume(string $timezone): void
    {
        $meta = $this->ensureMetaTimezone($timezone);
        $meta['local_resumed_at'] = CarbonImmutable::now($timezone)->format('Y-m-d H:i');

        $remaining = $this->remaining_seconds ?? $this->duration_seconds;

        $this->forceFill([
            'status' => self::STATUS_RUNNING,
            'started_at' => CarbonImmutable::now($timezone),
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
