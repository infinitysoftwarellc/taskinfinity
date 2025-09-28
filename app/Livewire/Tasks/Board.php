<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Board extends Component
{
    public array $rail = [];

    public ?int $listId = null;

    public bool $isListView = false;

    public function mount(?int $listId = null): void
    {
        $this->listId = $listId;
        $this->isListView = $listId !== null;

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
