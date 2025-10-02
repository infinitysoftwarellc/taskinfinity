<?php

namespace App\Http\Livewire\Spotlight;

use App\Support\Spotlight\SearchResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommonRouteCommand
{
    public function searchDestination(string $query): array
    {
        $query = trim($query);

        return collect($this->destinations())
            ->filter(function (array $destination) use ($query) {
                if ($destination['auth'] && ! Auth::check()) {
                    return false;
                }

                if ($query === '') {
                    return true;
                }

                $haystack = Str::lower($destination['name'] . ' ' . ($destination['description'] ?? ''));

                return Str::contains($haystack, Str::lower($query));
            })
            ->take(8)
            ->map(fn (array $destination) => new SearchResult(
                $destination['id'],
                $destination['name'],
                $destination['description']
            ))
            ->values()
            ->all();
    }

    public function execute(?string $destination = null): ?array
    {
        if ($destination === null) {
            return null;
        }

        $target = collect($this->destinations())
            ->firstWhere('id', $destination);

        if (! $target) {
            return null;
        }

        if ($target['auth'] && ! Auth::check()) {
            return null;
        }

        $params = Arr::get($target, 'params', []);

        if (isset($target['route'])) {
            return [
                'route' => $target['route'],
                'parameters' => $params,
            ];
        }

        if (isset($target['url'])) {
            return [
                'url' => $target['url'],
                'parameters' => $params,
            ];
        }

        return null;
    }

    protected function destinations(): array
    {
        return [
            [
                'id' => 'dashboard',
                'name' => 'Dashboard',
                'description' => 'Visão geral do seu progresso.',
                'route' => 'dashboard',
                'auth' => true,
            ],
            [
                'id' => 'tasks.index',
                'name' => 'Tarefas',
                'description' => 'Board principal de tarefas.',
                'route' => 'tasks.index',
                'auth' => true,
            ],
            [
                'id' => 'tasks.completed',
                'name' => 'Tarefas concluídas',
                'description' => 'Histórico de tarefas finalizadas.',
                'route' => 'tasks.completed',
                'auth' => true,
            ],
            [
                'id' => 'app.pomodoro',
                'name' => 'Pomodoro',
                'description' => 'Timer de foco e ciclos.',
                'route' => 'app.pomodoro',
                'auth' => true,
            ],
            [
                'id' => 'app.pomodoro.stats',
                'name' => 'Pomodoro • Estatísticas',
                'description' => 'Relatórios de sessões e ritmo.',
                'route' => 'app.pomodoro.stats',
                'auth' => true,
            ],
            [
                'id' => 'app.settings',
                'name' => 'Configurações',
                'description' => 'Preferências gerais da conta.',
                'route' => 'app.settings',
                'auth' => true,
            ],
            [
                'id' => 'profile.edit',
                'name' => 'Perfil',
                'description' => 'Dados pessoais e avatar.',
                'route' => 'profile.edit',
                'auth' => true,
            ],
            [
                'id' => 'pulse',
                'name' => 'Pulse',
                'description' => 'Observabilidade e métricas do sistema.',
                'route' => 'pulse',
                'auth' => true,
            ],
        ];
    }
}
