<?php
// app/Models/PomodoroSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PomodoroSession extends Model
{
    protected $guarded = []; // Permite todos os campos por enquanto

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'stopped_at' => 'datetime', // caso exista
        'paused_at' => 'datetime',  // caso exista
    ];

    // Método genérico que funciona com qualquer estrutura
    public function getCurrentRemainingSeconds()
    {
        // Se tem remaining_seconds, usa ele
        if (isset($this->attributes['remaining_seconds'])) {
            if ($this->status !== 'running') {
                return $this->remaining_seconds;
            }
        }

        // Senão, calcula baseado na duração
        $duration = $this->duration_minutes ?? $this->planned_duration ?? 25;
        if (is_string($duration)) {
            $duration = (int) $duration;
        }
        
        $elapsed = now()->diffInSeconds($this->started_at);
        $totalSeconds = $duration * 60;
        
        return max(0, $totalSeconds - $elapsed);
    }

    // Verifica se expirou
    public function isExpired()
    {
        return $this->getCurrentRemainingSeconds() <= 0;
    }

    // Accessor para stopped_at se não existir
    public function getStoppedAtAttribute()
    {
        return $this->ended_at ?? $this->attributes['stopped_at'] ?? null;
    }

    // Accessor para actual_duration se não existir  
    public function getActualDurationAttribute()
    {
        if (isset($this->attributes['actual_duration'])) {
            return $this->attributes['actual_duration'];
        }
        
        // Calcula baseado no tempo decorrido
        if ($this->started_at && $this->ended_at) {
            return $this->started_at->diffInSeconds($this->ended_at);
        }
        
        return 0;
    }
}