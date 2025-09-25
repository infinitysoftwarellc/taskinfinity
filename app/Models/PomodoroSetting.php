<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSetting extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'focus_minutes',
        'short_break_minutes',
        'long_break_minutes',
        'long_break_every',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'focus_minutes' => 'integer',
        'short_break_minutes' => 'integer',
        'long_break_minutes' => 'integer',
        'long_break_every' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
