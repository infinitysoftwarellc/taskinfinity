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

        $startedAtUtc = null;

        if (isset($this->meta['started_at_utc'])) {
            try {
                $startedAtUtc = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $this->meta['started_at_utc'], 'UTC');
            } catch (\Exception) {
                $startedAtUtc = null;
            }
        }

        if (! $startedAtUtc && $this->started_at instanceof CarbonImmutable) {
            $startedAtUtc = $this->started_at->setTimezone('UTC');
        }

        if (! $startedAtUtc && isset($this->meta['initial_started_at']) && $metaTimezone) {
            try {
                $startedAtUtc = CarbonImmutable::createFromFormat('Y-m-d H:i', $this->meta['initial_started_at'], $metaTimezone)
                    ->setTimezone('UTC');
            } catch (\Exception) {
                $startedAtUtc = null;
            }
        }

        $startedAtUtc ??= Carbon::now('UTC')->toImmutable();

        $nowUtc = Carbon::now('UTC')->toImmutable();

        $elapsed = $nowUtc->getTimestamp() - $startedAtUtc->getTimestamp();

        $remaining = $this->duration_seconds - max(0, $elapsed);

        return (int) max(0, $remaining);
    }

    public function markFinished(string $timezone): void
    {
        $effectiveTimezone = $this->resolveTimezone($timezone);
        $endedAtUtc = Carbon::now('UTC')->toImmutable();
        $endedAtLocal = $endedAtUtc->setTimezone($effectiveTimezone);
        $meta = $this->ensureMetaTimezone($effectiveTimezone);
        $meta['local_finished_at'] = $endedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_FINISHED,
            'ended_at' => $endedAtUtc,
            'remaining_seconds' => 0,
            'meta' => $meta,
        ])->save();
    }

    public function cancel(string $timezone): void
    {
        $effectiveTimezone = $this->resolveTimezone($timezone);
        $endedAtUtc = Carbon::now('UTC')->toImmutable();
        $endedAtLocal = $endedAtUtc->setTimezone($effectiveTimezone);
        $meta = $this->ensureMetaTimezone($effectiveTimezone);
        $meta['local_canceled_at'] = $endedAtLocal->format('Y-m-d H:i');

        $this->forceFill([
            'status' => self::STATUS_CANCELED,
            'ended_at' => $endedAtUtc,
            'meta' => $meta,
        ])->save();
    }

    public function pause(int $remainingSeconds, string $timezone): void
    {
        $effectiveTimezone = $this->resolveTimezone($timezone);
        $pausedAtLocal = Carbon::now('UTC')->toImmutable()->setTimezone($effectiveTimezone);
        $meta = $this->ensureMetaTimezone($effectiveTimezone);
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
        $effectiveTimezone = $this->resolveTimezone($timezone);
        $resumedAtUtc = Carbon::now('UTC')->toImmutable();
        $resumedAtLocal = $resumedAtUtc->setTimezone($effectiveTimezone);
        $meta = $this->ensureMetaTimezone($effectiveTimezone);
        $meta['local_resumed_at'] = $resumedAtLocal->format('Y-m-d H:i');
        $meta['started_at_utc'] = $resumedAtUtc->format('Y-m-d H:i:s');

        $this->forceFill([
            'status' => self::STATUS_RUNNING,
            'started_at' => $resumedAtUtc,
            'duration_seconds' => max(0, $remaining),
            'remaining_seconds' => null,
            'meta' => $meta,
        ])->save();
    }

    protected function resolveTimezone(string $timezone): string
    {
        return $timezone !== ''
            ? $timezone
            : config('app.timezone', 'UTC');
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
