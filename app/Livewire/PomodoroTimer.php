<?php

namespace App\Livewire;

use Livewire\Component;

class PomodoroTimer extends Component
{
    public int $workMinutes = 25;
    public int $shortBreakMinutes = 5;
    public int $longBreakMinutes = 15;
    public int $pomodorosUntilLongBreak = 4;

    public string $sessionType = 'work'; // work | short_break | long_break
    public int $timeRemaining = 0;
    public string $status = 'stopped'; // running | stopped
    public int $completedPomodoros = 0;

    public bool $showSettings = false;

    // ✅ Propriedade pública em vez de computed
    public bool $timerIsRunning = false;

    public function mount()
    {
        $this->resetTimer();
        $this->updateTimerStatus();
    }

    // ✅ Método para atualizar o status do timer
    private function updateTimerStatus(): void
    {
        $this->timerIsRunning = $this->status === 'running';
    }

    public function startTimer(): void
    {
        if ($this->timeRemaining <= 0) {
            $this->resetTimer();
        }

        $this->status = 'running';
        $this->updateTimerStatus(); // ✅ Atualiza a propriedade
    }

    public function stopTimer(): void
    {
        $this->status = 'stopped';
        $this->updateTimerStatus(); // ✅ Atualiza a propriedade
    }

    public function skipTimer(): void
    {
        $this->status = 'stopped';

        if ($this->sessionType === 'work') {
            $this->completedPomodoros++;

            if ($this->completedPomodoros % $this->pomodorosUntilLongBreak === 0) {
                $this->sessionType = 'long_break';
            } else {
                $this->sessionType = 'short_break';
            }
        } else {
            $this->sessionType = 'work';
        }

        $this->resetTimer();
        $this->updateTimerStatus(); // ✅ Atualiza a propriedade
    }

    public function resetTimer(): void
    {
        $this->timeRemaining = $this->getMinutesForType($this->sessionType) * 60;
    }

    public function setSessionType(string $type): void
    {
        $this->sessionType = $type;
        $this->status = 'stopped';
        $this->resetTimer();
        $this->updateTimerStatus(); // ✅ Atualiza a propriedade
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

    public function saveSettings(): void
    {
        $this->resetTimer();
        $this->showSettings = false;
        $this->updateTimerStatus(); // ✅ Atualiza a propriedade
    }

    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}