<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'entry_date',
        'completed',
        'value',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'completed' => 'bool',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function isCompleted(): bool
    {
        return (bool) $this->completed;
    }

    public function date(): Carbon
    {
        return $this->entry_date instanceof Carbon
            ? $this->entry_date
            : Carbon::parse($this->entry_date);
    }
}
