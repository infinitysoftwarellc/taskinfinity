<?php
// app/Livewire/PomodoroTimer.php

namespace App\Livewire;

use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PomodoroTimer extends Component
{
    public int $workMinutes = 25;
    public int $shortBreakMinutes = 5;
    public int $longBreakMinutes = 15;
    public int $pomodorosUntilLongBreak = 4;

    public string $sessionType = 'work';
    public int $timeRemaining = 0;
    public bool $timerIsRunning = false;
    public int $completedPomodoros = 0;
    public bool $showSettings = false;
    public string $notes = '';
    public bool $showSaveDialog = false;

    public ?PomodoroSession $currentSession = null;

    public function mount()
    {
        $this->loadCurrentSession();
    }

    // Carrega sessão ativa do banco
    private function loadCurrentSession()
    {
        $this->currentSession = PomodoroSession::where('user_id', Auth::id())
            ->whereIn('status', ['running', 'paused'])
            ->latest()
            ->first();

        if ($this->currentSession) {
            $this->sessionType = $this->currentSession->session_type;
            $this->timeRemaining = $this->currentSession->getCurrentRemainingSeconds();
            $this->timerIsRunning = $this->currentSession->status === 'running';
            $this->notes = $this->currentSession->notes ?? '';

            // Se expirou, completa automaticamente
            if ($this->currentSession->isExpired()) {
                $this->completeCurrentSession();
            }
        } else {
            $this->resetTimer();
        }

        $this->loadStats();
    }

    private function loadStats()
    {
        $this->completedPomodoros = PomodoroSession::where('user_id', Auth::id())
            ->where('session_type', 'work')
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();
    }

    public function startTimer()
    {
        if (!$this->currentSession) {
            $this->createNewSession();
        } else {
            // Resume sessão pausada
            $this->currentSession->update([
                'status' => 'running',
                'started_at' => now() // Reinicia o contador
            ]);
        }

        $this->timerIsRunning = true;
    }

    public function pauseTimer()
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'status' => 'paused',
                'remaining_seconds' => $this->timeRemaining
            ]);
        }
        $this->timerIsRunning = false;
    }

    public function skipTimer()
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'status' => 'abandoned',
                'ended_at' => now(),
                'remaining_seconds' => $this->timeRemaining
            ]);
        }

        $this->nextSessionType();
        $this->resetForNewSession();
    }

    public function saveCurrentSession()
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'status' => 'abandoned',
                'ended_at' => now(),
                'remaining_seconds' => $this->timeRemaining,
                'notes' => $this->notes
            ]);
        }

        $this->showSaveDialog = false;
        $this->resetForNewSession();
    }

    public function openSaveDialog()
    {
        $this->showSaveDialog = true;
    }

    public function closeSaveDialog()
    {
        $this->showSaveDialog = false;
    }

    private function createNewSession()
    {
        $duration = $this->getMinutesForType($this->sessionType);
        
        $this->currentSession = PomodoroSession::create([
            'user_id' => Auth::id(),
            'session_type' => $this->sessionType,
            'duration_minutes' => $duration,
            'remaining_seconds' => $duration * 60,
            'status' => 'running',
            'started_at' => now()
        ]);

        $this->timeRemaining = $duration * 60;
    }

    private function completeCurrentSession()
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'status' => 'completed',
                'ended_at' => now(),
                'remaining_seconds' => 0
            ]);

            if ($this->currentSession->session_type === 'work') {
                $this->completedPomodoros++;
            }

            $this->nextSessionType();
        }
        
        $this->resetForNewSession();
    }

    private function nextSessionType()
    {
        if ($this->sessionType === 'work') {
            if ($this->completedPomodoros % $this->pomodorosUntilLongBreak === 0) {
                $this->sessionType = 'long_break';
            } else {
                $this->sessionType = 'short_break';
            }
        } else {
            $this->sessionType = 'work';
        }
    }

    private function resetForNewSession()
    {
        $this->currentSession = null;
        $this->timerIsRunning = false;
        $this->resetTimer();
        $this->notes = '';
        $this->loadStats();
    }

    public function resetTimer()
    {
        $this->timeRemaining = $this->getMinutesForType($this->sessionType) * 60;
    }

    public function setSessionType(string $type)
    {
        if ($this->currentSession && $this->currentSession->status !== 'completed') {
            $this->openSaveDialog();
            return;
        }

        $this->sessionType = $type;
        $this->resetTimer();
        $this->timerIsRunning = false;
    }

    public function getMinutesForType(string $type): int
    {
        return match ($type) {
            'work' => $this->workMinutes,
            'short_break' => $this->shortBreakMinutes,
            'long_break' => $this->longBreakMinutes,
            default => 25,
        };
    }

    public function saveSettings()
    {
        $this->resetTimer();
        $this->showSettings = false;
    }

    // Sincroniza com o banco a cada 5 segundos
    public function syncWithDatabase()
    {
        if ($this->currentSession) {
            $this->currentSession->refresh();
            $this->timeRemaining = $this->currentSession->getCurrentRemainingSeconds();
            
            if ($this->currentSession->isExpired() && $this->timerIsRunning) {
                $this->completeCurrentSession();
            }
        }
    }

    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}