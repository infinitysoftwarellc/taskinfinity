<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Board extends Component
{
    public array $rail = [];

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

    public function render()
    {
        return view('livewire.tasks.board');
    }
}
