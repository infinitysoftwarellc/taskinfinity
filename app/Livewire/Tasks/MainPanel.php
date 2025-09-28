<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class MainPanel extends Component
{
    public array $panel = [];

    public function mount(array $panel = []): void
    {
        $this->panel = $panel ?: [
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
    }

    public function render()
    {
        return view('livewire.tasks.main-panel');
    }
}
