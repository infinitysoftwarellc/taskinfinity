<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'schedule',
        'custom_days',
        'goal_per_period',
        'color',
    ];

    protected $casts = [
        'custom_days' => 'array',
        'goal_per_period' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(HabitEntry::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function isDueOn(Carbon $date): bool
    {
        return match ($this->schedule) {
            'daily' => true,
            'weekly' => in_array(
                $date->dayOfWeek,
                $this->custom_days ?? range(0, 6)
            ),
            'custom' => in_array($date->dayOfWeek, $this->custom_days ?? []),
            default => true,
        };
    }

    public function customDaysCollection(): Collection
    {
        return collect($this->custom_days ?? []);
    }
}
