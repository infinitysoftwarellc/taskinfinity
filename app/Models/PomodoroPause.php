<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PomodoroPause extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'paused_at_client',
        'resumed_at_client',
        'duration_seconds',
    ];

    protected $casts = [
        'paused_at_client' => 'datetime',
        'resumed_at_client' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(PomodoroSession::class, 'session_id');
    }
}
