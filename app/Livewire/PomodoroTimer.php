<?php

namespace App\Livewire;

use App\Models\PomodoroSession;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class PomodoroTimer extends Component
{
    // Configurações (serão carregadas do usuário)
    public int $workMinutes;
    public int $shortBreakMinutes;
    public int $longBreakMinutes;
    public int $cyclesUntilLongBreak;

    // Estado
    public ?int $remainingSeconds = null;
    public bool $timerRunning = false;
    public bool $isPaused = false;
    public string $sessionType = 'work';

    public function mount()
    {
        // Carrega as configurações salvas do usuário logado
        $user = Auth::user();
        $this->workMinutes = $user->pomodoro_work_minutes;
        $this->shortBreakMinutes = $user->pomodoro_short_break_minutes;
        $this->longBreakMinutes = $user->pomodoro_long_break_minutes;
        $this->cyclesUntilLongBreak = $user->pomodoro_cycles;

        $this->loadStateFromServer(false);
    }

    #[Computed]
    public function state(): array
    {
        return $this->getStateAsArray();
    }

    public function loadStateFromServer(bool $dispatchEvent = true): void
    {
        $user = Auth::user()->fresh();
        $this->sessionType = $user->pomodoro_session_type ?? $this->sessionType;

        if ($user->pomodoro_paused_at) {
            $this->timerRunning = true;
            $this->isPaused = true;
            $this->remainingSeconds = $user->pomodoro_ends_at->diffInSeconds($user->pomodoro_paused_at);
        } elseif ($user->pomodoro_ends_at && $user->pomodoro_ends_at->isFuture()) {
            $this->timerRunning = true;
            $this->isPaused = false;
            $this->remainingSeconds = now()->diffInSeconds($user->pomodoro_ends_at);
        } else {
            $this->timerRunning = false;
            $this->isPaused = false;
            $this->remainingSeconds = $this->getMinutesForType($this->sessionType) * 60;
        }

        if ($dispatchEvent) {
            $this->dispatch('state-loaded', state: $this->getStateAsArray());
        }
    }

    // O restante do arquivo continua igual...
    
    public function startTimer(): void
    {
        $user = Auth::user();

        if ($user->pomodoro_paused_at) {
            $pausedDuration = now()->diffInSeconds($user->pomodoro_paused_at);
            $newEndsAt = $user->pomodoro_ends_at->addSeconds($pausedDuration);
            
            $user->update([
                'pomodoro_ends_at' => $newEndsAt,
                'pomodoro_paused_at' => null,
            ]);
        } elseif (!$user->pomodoro_ends_at || $user->pomodoro_ends_at->isPast()) {
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
                'actual_duration' => 0,
                'started_at' => now(),
                'status' => 'running',
            ]);
        }
        
        $this->loadStateFromServer();
    }

    public function pauseTimer(): void
    {
        $user = Auth::user();
        if (!$user->pomodoro_ends_at || $user->pomodoro_paused_at) {
            return;
        }

        $user->update(['pomodoro_paused_at' => now()]);
        $this->loadStateFromServer();
    }

    public function stopTimer()
    {
        $user = Auth::user();
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
            'pomodoro_session_type' => $this->sessionType,
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

    public function updated($property, $value)
    {
        // Mapeia a propriedade do componente para a coluna do banco de dados
        $settingsMap = [
            'workMinutes' => 'pomodoro_work_minutes',
            'shortBreakMinutes' => 'pomodoro_short_break_minutes',
            'longBreakMinutes' => 'pomodoro_long_break_minutes',
            'cyclesUntilLongBreak' => 'pomodoro_cycles',
        ];
        
        // Se a propriedade alterada for uma das configurações, salva no banco
        if (array_key_exists($property, $settingsMap)) {
            Auth::user()->update([
                $settingsMap[$property] => $value
            ]);

            // Se o timer não estiver rodando, atualiza o display
            if (!$this->timerRunning) {
                $this->loadStateFromServer();
            }
        }
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