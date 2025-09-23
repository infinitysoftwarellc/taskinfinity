<?php

namespace App\Livewire;

use Livewire\Component;

class PomodoroTimer extends Component
{
    public $minutes = 25;
    public $seconds = 0;
    public $timerRunning = false;
    public $sessionType = 'work'; // Pode ser 'work', 'short_break', ou 'long_break'

    // Inicia o cronômetro
    public function startTimer()
    {
        $this->timerRunning = true;
    }

    // Pausa o cronômetro
    public function pauseTimer()
    {
        $this->timerRunning = false;
    }

    // Reseta o cronômetro para o tempo da sessão atual
    public function resetTimer()
    {
        $this->timerRunning = false;
        $this->setTimer($this->sessionType);
    }

    // Define o tempo com base no tipo de sessão (trabalho, pausa curta, etc.)
    public function setTimer($type)
    {
        $this->sessionType = $type;
        $this->timerRunning = false;

        switch ($type) {
            case 'work':
                $this->minutes = 25;
                $this->seconds = 0;
                break;
            case 'short_break':
                $this->minutes = 5;
                $this->seconds = 0;
                break;
            case 'long_break':
                $this->minutes = 15;
                $this->seconds = 0;
                break;
        }
    }

    // Lógica para decrementar o tempo (chamada a cada segundo pela view)
    public function decrementTime()
    {
        if ($this->timerRunning) {
            if ($this->seconds > 0) {
                $this->seconds--;
            } elseif ($this->minutes > 0) {
                $this->minutes--;
                $this->seconds = 59;
            } else {
                // O tempo acabou, para o timer e pode adicionar um som ou notificação aqui
                $this->timerRunning = false;
                $this->dispatch('timer-finished'); // Dispara um evento
            }
        }
    }

    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}