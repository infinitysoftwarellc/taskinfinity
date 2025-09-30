<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente que exibe a visão de tarefas concluídas.
 */
class Completed extends Component
{
    protected $listeners = ['tasks-updated' => '$refresh'];

    /**
     * Configuração da barra lateral exibida nessa visão.
     */
    public array $rail = [];

    /**
     * Define a estrutura do rail com os atalhos usados na página.
     */
    public function mount(): void
    {
        $this->rail = [
            'avatarLabel' => 'Você',
            'primary' => [
                ['icon' => 'list-checks', 'title' => 'All'],
                ['icon' => 'sun', 'title' => 'Today'],
                ['icon' => 'calendar-days', 'title' => '7 Days'],
                ['icon' => 'inbox', 'title' => 'Inbox'],
                ['icon' => 'pie-chart', 'title' => 'Summary'],
            ],
            'secondary' => [
                ['icon' => 'settings', 'title' => 'Settings'],
            ],
        ];
    }

    /**
     * Monta os dados necessários para renderizar as tarefas concluídas.
     */
    public function render()
    {
        $user = Auth::user();

        if (! $user) {
            return view('livewire.tasks.completed', [
                'rail' => $this->rail,
                'groups' => collect(),
                'totalCount' => 0,
            ]);
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $missions = Mission::query()
            ->with('list')
            ->where('user_id', $user->id)
            ->where('status', 'done')
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->get();

        $groups = $this->groupMissionsByCompletionDate($missions, $timezone);

        return view('livewire.tasks.completed', [
            'rail' => $this->rail,
            'groups' => $groups,
            'totalCount' => $missions->count(),
        ]);
    }

    /**
     * Agrupa missões por data de conclusão para montar as seções da página.
     */
    private function groupMissionsByCompletionDate(Collection $missions, string $timezone): Collection
    {
        $dated = [];
        $undated = [];

        foreach ($missions as $mission) {
            $completedAt = $mission->completed_at ?? $mission->updated_at ?? $mission->created_at;
            $localized = $completedAt?->copy()->setTimezone($timezone);
            $key = $localized ? $localized->toDateString() : 'sem-data';

            if ($localized) {
                $target = &$dated;
            } else {
                $target = &$undated;
            }

            if (! array_key_exists($key, $target)) {
                $target[$key] = [
                    'date' => $localized,
                    'label' => $this->formatDateHeading($localized),
                    'missions' => [],
                ];
            }

            $target[$key]['missions'][] = [
                'id' => $mission->id,
                'title' => $mission->title ?: 'Sem título',
                'list' => $mission->list?->name,
                'completed_at' => $localized,
                'completed_time' => $localized?->format('H:i'),
            ];
        }

        $datedValues = array_values($dated);
        usort($datedValues, function (array $a, array $b) {
            $timestampA = $a['date'] instanceof CarbonInterface ? $a['date']->timestamp : 0;
            $timestampB = $b['date'] instanceof CarbonInterface ? $b['date']->timestamp : 0;

            return $timestampB <=> $timestampA;
        });

        $undatedValues = array_values($undated);

        return collect(array_merge($datedValues, $undatedValues));
    }

    /**
     * Formata o título exibido para cada grupo de tarefas concluídas.
     */
    private function formatDateHeading(?CarbonInterface $date): string
    {
        if (! $date) {
            return 'Sem data de conclusão';
        }

        if ($date->isToday()) {
            return 'Hoje';
        }

        if ($date->isYesterday()) {
            return 'Ontem';
        }

        return ucfirst($date->copy()->locale(config('app.locale', 'pt_BR'))->translatedFormat('d \d\e F Y'));
    }
}
