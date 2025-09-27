<?php

namespace App\Livewire\Task;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Sidebar extends Component
{
    public function render(): View
    {
        return view('livewire.task.sidebar');
    }
}
