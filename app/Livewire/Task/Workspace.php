<?php

namespace App\Livewire\Task;

use Livewire\Component;

class Workspace extends Component
{
    public array $sections = [
        [
            'title' => 'PRÓXIMAS TASKS',
            'tag' => 'Task Infinity',
            'tasks' => [
                ['title' => '3 - Interatividade e funcionalidades', 'subtitle' => 'task infinity', 'status' => 'Task Infinity'],
                ['title' => '3 - Construção de interface', 'subtitle' => 'task infinity', 'status' => 'Task Infinity'],
                ['title' => 'PORTFOLIO GUILHERMEINFINITY', 'subtitle' => 'portfolio', 'status' => 'Portfolio'],
                ['title' => 'PORTFOLIO SOFTWAREINFINITY', 'subtitle' => 'portfolio', 'status' => 'Portfolio'],
            ],
        ],
        [
            'title' => 'Completed',
            'tag' => '5 tarefas',
            'tasks' => [
                ['title' => '2 - Estrutura de rotas e componentes', 'subtitle' => 'task infinity', 'status' => 'Task Infinity', 'completed' => true],
                ['title' => '2 - Integração com banco de dados', 'subtitle' => 'task infinity', 'status' => 'Task Infinity', 'completed' => true],
            ],
        ],
    ];

    public function render()
    {
        return view('livewire.task.workspace');
    }
}
