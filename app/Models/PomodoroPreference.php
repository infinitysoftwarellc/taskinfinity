<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'focus_minutes',
        'short_break_minutes',
        'long_break_minutes',
        'cycles_before_long_break',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
