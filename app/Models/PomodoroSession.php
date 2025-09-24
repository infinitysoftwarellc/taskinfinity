<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'session_type',
        'started_at',
        'stopped_at',
        'configured_duration',
        'actual_duration',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime', // <-- ADICIONE ESTA LINHA
        'stopped_at' => 'datetime', // <-- E ESTA TAMBÉM
    ];

    /**
     * Get the user that owns the pomodoro session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}