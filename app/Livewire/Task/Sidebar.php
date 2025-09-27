<?php

namespace App\Livewire\Task;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Sidebar extends Component
{
    public array $views = [];

    public array $lists = [];

    public array $tags = [];

    public string $filtersDescription = '';

    public string $activeView = 'all';

    public ?int $activeListId = null;

    public function render(): View
    {
        return view('livewire.task.sidebar');
    }
}
