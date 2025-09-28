<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Details extends Component
{
    public array $details = [];

    public function mount(array $details = []): void
    {
        $this->details = $details ?: [
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
        return view('livewire.tasks.details');
    }
}
