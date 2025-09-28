<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PomodoroSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'mission_id',
        'type',
        'started_at_client',
        'ended_at_client',
        'client_timezone',
        'client_utc_offset_minutes',
        'started_at_server',
        'ended_at_server',
        'duration_seconds',
        'pause_count',
        'pause_total_seconds',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'started_at_client' => 'datetime',
        'ended_at_client' => 'datetime',
        'started_at_server' => 'datetime',
        'ended_at_server' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function pauses()
    {
        return $this->hasMany(PomodoroPause::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
