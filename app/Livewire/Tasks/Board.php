<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Board extends Component
{
    public array $rail = [];

    public array $sidebar = [];

    public array $panel = [];

    public array $details = [];

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

        $this->sidebar = [
            'shortcuts' => [
                ['icon' => 'infinity', 'label' => 'All', 'count' => 38],
                ['icon' => 'sun', 'label' => 'Today'],
                ['icon' => 'calendar-days', 'label' => 'Next 7 Days'],
            ],
            'workspace' => [
                'title' => 'SOFTWAREINFINITY',
                'badge' => 36,
                'expanded' => true,
                'items' => [
                    ['icon' => 'list-todo', 'label' => 'Tasks'],
                    ['icon' => 'flame', 'label' => 'Habits'],
                    ['icon' => 'clock', 'label' => 'Pomodoro'],
                ],
            ],
            'filtersTip' => 'Display tasks filtered by list, date, priority, tag, and more',
            'tags' => [
                ['label' => 'Bugs', 'color' => '#f87171', 'count' => null],
                ['label' => 'Melhorias', 'color' => '#22d3ee', 'count' => null],
            ],
            'completedLabel' => 'Completed',
        ];

        $this->panel = [
            'title' => 'All',
            'count' => 38,
            'inputPlaceholder' => "Add task to 'Inbox'",
            'groups' => [
                [
                    'title' => 'No Date',
                    'count' => 38,
                    'expanded' => true,
                    'subgroups' => [
                        [
                            'name' => 'aa',
                            'meta' => 'Inbox',
                            'expanded' => true,
                            'ghost' => ['title' => 'No Title', 'meta' => 'Inbox'],
                            'tasks' => [
                                [
                                    'title' => 'COLOCAR METAS',
                                    'meta' => 'Task Infinity',
                                    'expanded' => true,
                                    'subtasks' => [
                                        ['title' => 'Definir metas trimestrais', 'meta' => 'Inbox'],
                                        ['title' => 'Mapear KPIs por lista', 'meta' => 'Inbox'],
                                    ],
                                ],
                                ['title' => 'COLOCAR IA', 'meta' => 'Task Infinity'],
                                ['title' => 'THEMA FLORESTAL', 'meta' => 'Task Infinity'],
                                ['title' => 'THEMA GAMER', 'meta' => 'Task Infinity'],
                                ['title' => 'ADICIONAR', 'meta' => 'Task Infinity'],
                                ['title' => 'HABITOS', 'meta' => 'Task Infinity'],
                                ['title' => 'POMODORO', 'meta' => 'Task Infinity'],
                                ['title' => 'TUDO QUE FALTA', 'meta' => 'Task Infinity'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->details = [
            'owner' => 'aa',
            'actions' => [
                ['icon' => 'flag', 'title' => 'Classificar por data'],
                ['icon' => 'more-horizontal', 'title' => 'Opções'],
            ],
            'emptyTitle' => 'What would you like to do?',
            'emptyDescription' => 'Selecione uma tarefa para ver os detalhes, adicionar notas, e muito mais.',
        ];
    }

    public function render()
    {
        return view('livewire.tasks.board');
    }
}
