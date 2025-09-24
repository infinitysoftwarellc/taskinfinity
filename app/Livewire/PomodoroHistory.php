<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class PomodoroHistory extends Component
{
    // Apenas para ouvir o evento e recarregar
    protected $listeners = ['pomodoroStopped' => '$refresh'];

    #[Computed]
    public function sessions()
    {
        // Busca apenas as sessões de 'foco' que foram completadas ou paradas,
        // ordenadas da mais recente para a mais antiga.
        return PomodoroSession::where('user_id', Auth::id())
            ->where('session_type', 'work')
            ->whereIn('status', ['completed', 'stopped'])
            ->latest('started_at')
            ->get();
    }

    #[Computed]
    public function stats()
    {
        $todaySessions = $this->sessions()->filter(function ($session) {
            return Carbon::parse($session->started_at)->isToday();
        });

        $totalSecondsToday = $todaySessions->sum('actual_duration');
        $totalHoursToday = floor($totalSecondsToday / 3600);
        $totalMinutesToday = floor(($totalSecondsToday % 3600) / 60);

        return [
            'pomodoros_today' => $todaySessions->count(),
            'total_time_today_formatted' => sprintf('%dh %02dm', $totalHoursToday, $totalMinutesToday),
        ];
    }

    public function render()
    {
        return view('livewire.pomodoro-history');
    }
}
