<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class PomodoroHistory extends Component
{
    protected $listeners = ['pomodoroStopped' => '$refresh'];

    public string $startDate;
    public string $endDate;

    public function mount()
    {
        // Define o período padrão para a última semana
        $this->endDate = now()->toDateString();
        $this->startDate = now()->subDays(6)->toDateString();
    }

    #[Computed]
    public function sessions()
    {
        // Busca todas as sessões concluídas ou paradas no período selecionado
        return PomodoroSession::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'stopped'])
            ->whereBetween('started_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()])
            ->latest('started_at')
            ->get();
    }

    #[Computed]
    public function stats()
    {
        $sessionsInPeriod = $this->sessions();

        $pomodorosToday = $sessionsInPeriod->filter(function ($session) {
            return Carbon::parse($session->started_at)->isToday() && $session->session_type === 'work';
        })->count();

        $totalSecondsToday = $sessionsInPeriod->filter(function ($session) {
            return Carbon::parse($session->started_at)->isToday() && $session->session_type === 'work';
        })->sum('actual_duration');

        return [
            'pomodoros_today' => $pomodorosToday,
            'total_time_today_formatted' => $this->formatDuration($totalSecondsToday),
            'total_work_time' => $this->formatDuration($sessionsInPeriod->where('session_type', 'work')->sum('actual_duration')),
            'total_short_break_time' => $this->formatDuration($sessionsInPeriod->where('session_type', 'short_break')->sum('actual_duration')),
            'total_long_break_time' => $this->formatDuration($sessionsInPeriod->where('session_type', 'long_break')->sum('actual_duration')),
        ];
    }
    
    // Função auxiliar para formatar a duração de segundos para "Xh XXm"
    private function formatDuration($totalSeconds): string
    {
        if ($totalSeconds < 60) {
            return "0m";
        }
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        return sprintf('%dh %02dm', $hours, $minutes);
    }

    public function render()
    {
        return view('livewire.pomodoro-history');
    }
}