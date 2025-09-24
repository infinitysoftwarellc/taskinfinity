<?php

namespace App\Livewire;

use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PomodoroTimer extends Component
{
    public string $sessionType = 'work';
    public int $pomodorosCompleted = 0;
    public bool $showSettings = false;

    // Nomes das propriedades corrigidos para corresponder à view
    public int $workMinutes = 25;
    public int $shortBreakMinutes = 5;
    public int $longBreakMinutes = 15;
    public int $pomodorosUntilLongBreak = 4;

    public function mount(): void
    {
        $this->loadStateFromServer();
        $this->loadSettings();
    }

    public function loadStateFromServer(): void
    {
        $user = Auth::user()->fresh();
        $this->sessionType = $user->pomodoro_session_type ?? 'work';
        $this->pomodorosCompleted = $user->pomodoros_completed ?? 0;
    }

    public function loadSettings(): void
    {
        $user = Auth::user();
        $this->workMinutes = $user->pomodoro_work_duration ?? 25;
        $this->shortBreakMinutes = $user->pomodoro_short_break_duration ?? 5;
        $this->longBreakMinutes = $user->pomodoro_long_break_duration ?? 15;
        $this->pomodorosUntilLongBreak = $user->pomodoros_until_long_break ?? 4;
    }

    public function saveSettings(): void
    {
        Auth::user()->update([
            'pomodoro_work_duration' => $this->workMinutes,
            'pomodoro_short_break_duration' => $this->shortBreakMinutes,
            'pomodoro_long_break_duration' => $this->longBreakMinutes,
            'pomodoros_until_long_break' => $this->pomodorosUntilLongBreak,
        ]);

        $this->showSettings = false;
        $this->dispatch('settings-saved');
    }

    #[Computed]
    public function timerEndsAt()
    {
        return Auth::user()->pomodoro_ends_at;
    }

    #[Computed]
    public function timerIsPaused(): bool
    {
        return Auth::user()->pomodoro_paused_at !== null;
    }

    #[Computed]
    public function timerIsRunning(): bool
    {
        // O timer está "rodando" se ele tem uma data de término e não está pausado
        return $this->timerEndsAt && !$this->timerIsPaused;
    }

    public function setSessionType(string $type): void
    {
        if ($this->timerIsRunning) {
            $this->stopTimer();
        }
        $this->sessionType = $type;
    }

    public function startTimer(): void
    {
        $user = Auth::user();

        if ($this->timerIsPaused) {
            $pausedDuration = now()->diffInSeconds($user->pomodoro_paused_at);
            $newEndsAt = $user->pomodoro_ends_at->addSeconds($pausedDuration);
            $user->update(['pomodoro_ends_at' => $newEndsAt, 'pomodoro_paused_at' => null]);
        } elseif (!$this->timerEndsAt || $this->timerEndsAt->isPast()) {
            $durationMinutes = $this->getMinutesForType($this->sessionType);
            $user->update([
                'pomodoro_ends_at' => now()->addMinutes($durationMinutes),
                'pomodoro_session_type' => $this->sessionType,
                'pomodoro_paused_at' => null,
            ]);

            PomodoroSession::create([
                'user_id' => $user->id,
                'session_type' => $this->sessionType,
                'configured_duration' => $durationMinutes * 60,
                'actual_duration' => 0,
                'started_at' => now(),
                'status' => 'running',
            ]);
        }
        $this->loadStateFromServer();
    }

    public function pauseTimer(): void
    {
        Auth::user()->update(['pomodoro_paused_at' => now()]);
        $this->loadStateFromServer();
    }

    public function stopTimer(): void
    {
        $user = Auth::user();
        $session = PomodoroSession::where('user_id', $user->id)
            ->where('status', 'running')
            ->latest('started_at')
            ->first();

        if ($session) {
            $session->update([
                'status' => 'stopped',
                'stopped_at' => now(),
                'actual_duration' => now()->diffInSeconds($session->started_at),
            ]);
        }

        $user->update(['pomodoro_ends_at' => null, 'pomodoro_paused_at' => null]);
        $this->loadStateFromServer();
        $this->dispatch('pomodoroStopped');
    }

    public function skipTimer(): void
    {
        // Esta lógica finaliza a sessão atual e inicia a próxima
        $this->stopTimer(); // Para e salva a sessão atual

        $user = Auth::user();
        if ($this->sessionType === 'work') {
            $this->pomodorosCompleted = ($this->pomodorosCompleted + 1);
            if ($this->pomodorosCompleted >= $this->pomodorosUntilLongBreak) {
                $this->sessionType = 'long_break';
                $this->pomodorosCompleted = 0; // Reseta o contador
            } else {
                $this->sessionType = 'short_break';
            }
        } else {
            $this->sessionType = 'work';
        }
        $user->update([
            'pomodoros_completed' => $this->pomodorosCompleted,
            'pomodoro_session_type' => $this->sessionType
        ]);

        $this->startTimer(); // Inicia a nova sessão
    }

    #[Computed]
    public function timeRemaining(): int
    {
        if ($this->timerIsPaused) {
            return Auth::user()->pomodoro_ends_at->diffInSeconds(Auth::user()->pomodoro_paused_at);
        }

        if (!$this->timerEndsAt || $this->timerEndsAt->isPast()) {
            return $this->getMinutesForType($this->sessionType) * 60;
        }

        return max(0, now()->diffInSeconds($this->timerEndsAt, false));
    }

    private function getMinutesForType(string $type): int
    {
        return match ($type) {
            'work' => $this->workMinutes,
            'short_break' => $this->shortBreakMinutes,
            'long_break' => $this->longBreakMinutes,
            default => 25,
        };
    }

    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}