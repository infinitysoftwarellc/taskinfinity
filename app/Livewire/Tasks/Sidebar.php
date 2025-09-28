<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Sidebar extends Component
{
    public array $shortcuts = [];

    public array $workspace = [];

    public string $filtersTip = '';

    public array $tags = [];

    public string $completedLabel = 'Completed';

    public function mount(
        array $shortcuts = [],
        array $workspace = [],
        string $filtersTip = '',
        array $tags = [],
        string $completedLabel = 'Completed'
    ): void {
        $this->shortcuts = $shortcuts ?: [
            ['icon' => 'infinity', 'label' => 'All', 'count' => 38],
            ['icon' => 'sun', 'label' => 'Today'],
            ['icon' => 'calendar-days', 'label' => 'Next 7 Days'],
        ];

        $this->workspace = $workspace ?: [
            'title' => 'SOFTWAREINFINITY',
            'badge' => 36,
            'expanded' => true,
            'items' => [
                ['icon' => 'list-todo', 'label' => 'Tasks'],
                ['icon' => 'flame', 'label' => 'Habits'],
                ['icon' => 'clock', 'label' => 'Pomodoro'],
            ],
        ];

        $this->filtersTip = $filtersTip ?: 'Display tasks filtered by list, date, priority, tag, and more';

        $this->tags = $tags ?: [
            ['label' => 'Bugs', 'color' => '#f87171', 'count' => null],
            ['label' => 'Melhorias', 'color' => '#22d3ee', 'count' => null],
        ];

        $this->completedLabel = $completedLabel;
    }

    public function render()
    {
        return view('livewire.tasks.sidebar');
    }
}
