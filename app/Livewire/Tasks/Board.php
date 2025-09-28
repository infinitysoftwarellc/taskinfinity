<?php

namespace App\Livewire\Tasks;

use App\Support\MissionShortcutFilter;
use Livewire\Component;

class Board extends Component
{
    public array $rail = [];

    public ?int $listId = null;

    public bool $isListView = false;

    public ?string $shortcut = null;

    public function mount(?int $listId = null, ?string $shortcut = null): void
    {
        $this->listId = $listId;
        $this->isListView = $listId !== null;

        if ($shortcut && in_array($shortcut, MissionShortcutFilter::supported(), true)) {
            $this->shortcut = $shortcut;
        }

        if ($this->isListView) {
            $this->shortcut = null;
        }

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
