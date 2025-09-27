<?php

namespace App\Livewire\Task;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Workspace extends Component
{
    public function render(): View
    {
        return view('livewire.task.workspace');
    }
}
