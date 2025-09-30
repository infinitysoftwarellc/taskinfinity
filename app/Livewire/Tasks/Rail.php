<?php

// This Livewire class manages rail behaviour for the tasks experience.
namespace App\Livewire\Tasks;

use Livewire\Component;

class Rail extends Component
{
    public array $primaryButtons = [];

    public array $secondaryButtons = [];

    public string $avatarLabel = 'Você';

    public function mount(
        array $primaryButtons = [],
        array $secondaryButtons = [],
        string $avatarLabel = 'Você'
    ): void {
        $this->primaryButtons = $primaryButtons ?: [
            ['icon' => 'list-checks', 'title' => 'All'],
            ['icon' => 'sun', 'title' => 'Today'],
            ['icon' => 'calendar-days', 'title' => '7 Days'],
            ['icon' => 'inbox', 'title' => 'Inbox'],
            ['icon' => 'pie-chart', 'title' => 'Summary'],
        ];

        $this->secondaryButtons = $secondaryButtons ?: [
            ['icon' => 'settings', 'title' => 'Settings'],
        ];

        $this->avatarLabel = $avatarLabel;
    }

    public function render()
    {
        return view('livewire.tasks.rail');
    }
}
