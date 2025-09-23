<?php

namespace App\Livewire;

use App\Models\PomodoroSession;
use Livewire\Component;

class PomodoroTimer extends Component
{
    // Configurações
    public int $workMinutes = 25;
    public int $shortBreakMinutes = 5;
    public int $longBreakMinutes = 15;
    public int $cyclesUntilLongBreak = 4;

    // Estado
    public ?int $remainingSeconds = null;
    public bool $timerRunning = false;
    public bool $isPaused = false;
    public string $sessionType = 'work';

    public function mount()
    {
        $this->loadStateFromServer();
    }

    public function loadStateFromServer()
    {
        $user = auth()->user()->fresh(); // Pega os dados mais recentes do DB

        if ($user->pomodoro_paused_at) { // Se está pausado
            $this->timerRunning = true;
            $this->isPaused = true;
            $this->sessionType = $user->pomodoro_session_type;
            // Calcula o tempo restante com base na data de término e a data de pausa
            $this->remainingSeconds = $user->pomodoro_ends_at->diffInSeconds($user->pomodoro_paused_at);

        } elseif ($user->pomodoro_ends_at && $user->pomodoro_ends_at->isFuture()) { // Se está rodando
            $this->timerRunning = true;
            $this->isPaused = false;
            $this->sessionType = $user->pomodoro_session_type;
            $this->remainingSeconds = now()->diffInSeconds($user->pomodoro_ends_at);

        } else { // Se está parado
            $this->timerRunning = false;
            $this->isPaused = false;
            // sessionType pode ter sido alterado pelo usuário, então o mantemos
            $this->remainingSeconds = $this->getMinutesForType($this->sessionType) * 60;
        }

        // Envia o estado atualizado para o Alpine.js
        $this->dispatch('state-loaded', state: $this->getStateAsArray());
    }

    public function startTimer()
    {
        $user = auth()->user();

        // Se o timer estava pausado, apenas retoma
        if ($this->isPaused && $user->pomodoro_paused_at) {
            $pausedDuration = now()->diffInSeconds($user->pomodoro_paused_at);
            $newEndsAt = $user->pomodoro_ends_at->addSeconds($pausedDuration);
            
            $user->update([
                'pomodoro_ends_at' => $newEndsAt,
                'pomodoro_paused_at' => null,
            ]);

        } else { // Inicia um novo timer
            $durationMinutes = $this->getMinutesForType($this->sessionType);
            
            $user->update([
                'pomodoro_ends_at' => now()->addMinutes($durationMinutes),
                'pomodoro_session_type' => $this->sessionType,
                'pomodoro_paused_at' => null,
            ]);

            PomodoroSession::create([
                'user_id' => $user->id,
                'session_type' => $this->sessionType,
                'configured_duration' => $durationMinutes,
                'actual_duration' => 0, // <-- A CORREÇÃO ESTÁ AQUI
                'started_at' => now(),
                'status' => 'running',
            ]);
        }
        
        $this->loadStateFromServer();
    }

    public function pauseTimer()
    {
        if (!$this->timerRunning || $this->isPaused) return;

        auth()->user()->update(['pomodoro_paused_at' => now()]);
        $this->loadStateFromServer();
    }

    public function stopTimer()
    {
        $user = auth()->user();
        $session = PomodoroSession::where('user_id', $user->id)->where('status', 'running')->latest()->first();

        if ($session) {
            $session->update([
                'status' => 'stopped',
                'stopped_at' => now(),
                'actual_duration' => now()->diffInSeconds($session->started_at),
            ]);
        }

        $user->update([
            'pomodoro_ends_at' => null,
            'pomodoro_session_type' => $this->sessionType, // Mantém o tipo para o próximo início
            'pomodoro_paused_at' => null,
        ]);
        
        $this->loadStateFromServer();
    }

    public function setTimer($type)
    {
        if ($this->timerRunning) return;

        $this->sessionType = $type;
        $this->loadStateFromServer();
    }

    private function getMinutesForType(string $type): int
    {
        return match ($type) {
            'short_break' => $this->shortBreakMinutes,
            'long_break' => $this->longBreakMinutes,
            default => $this->workMinutes,
        };
    }

    private function getStateAsArray(): array
    {
        return [
            'remainingSeconds' => $this->remainingSeconds,
            'timerRunning' => $this->timerRunning,
            'isPaused' => $this->isPaused,
            'sessionType' => $this->sessionType,
        ];
    }
    
    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}
