<?php

// This support class offers helper logic for mission shortcut filter.
namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;

class MissionShortcutFilter
{
    public const TODAY = 'today';
    public const TOMORROW = 'tomorrow';
    public const NEXT_SEVEN_DAYS = 'next-seven-days';

    /**
     * @var array<string, bool>
     */
    private static array $dueAtColumnCache = [];

    /**
     * @return array<int, string>
     */
    public static function supported(): array
    {
        return [
            self::TODAY,
            self::TOMORROW,
            self::NEXT_SEVEN_DAYS,
        ];
    }

    /**
     * @param  Builder|Relation  $query
     */
    public static function apply(Builder|Relation $query, string $shortcut, string $timezone): Builder|Relation
    {
        $builder = $query instanceof Relation ? $query->getQuery() : $query;

        if (! self::shouldApplyDueDateFilters($builder)) {
            return $query;
        }

        $range = self::dateRangeFor($shortcut, $timezone);

        if (! $range) {
            return $query;
        }

        [$startUtc, $endUtc] = $range;

        $builder
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [$startUtc, $endUtc]);

        return $query;
    }

    public static function dateRangeFor(string $shortcut, string $timezone): ?array
    {
        $timezone = $timezone ?: config('app.timezone');
        $now = CarbonImmutable::now($timezone);

        return match ($shortcut) {
            self::TODAY => self::rangeForDay($now, 0),
            self::TOMORROW => self::rangeForDay($now, 1),
            self::NEXT_SEVEN_DAYS => self::rangeForSpan($now, 7),
            default => null,
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function rangeForDay(CarbonImmutable $now, int $offset): array
    {
        $day = $now->addDays($offset);
        $start = $day->startOfDay();
        $end = $day->endOfDay();

        return [
            $start->setTimezone('UTC')->toDateTimeString(),
            $end->setTimezone('UTC')->toDateTimeString(),
        ];
    }

    /**
     * Builds a range starting today and covering the given number of days.
     *
     * @return array{0: string, 1: string}
     */
    private static function rangeForSpan(CarbonImmutable $now, int $days): array
    {
        $days = max(1, $days);
        $start = $now->startOfDay();
        $end = $now->addDays($days - 1)->endOfDay();

        return [
            $start->setTimezone('UTC')->toDateTimeString(),
            $end->setTimezone('UTC')->toDateTimeString(),
        ];
    }

    private static function shouldApplyDueDateFilters(Builder $query): bool
    {
        $table = $query->getModel()->getTable();

        if (array_key_exists($table, self::$dueAtColumnCache)) {
            return self::$dueAtColumnCache[$table];
        }

        return self::$dueAtColumnCache[$table] = Schema::hasColumn($table, 'due_at');
    }
}
