<?php

namespace App\Livewire\Task;

use Livewire\Component;

class Page extends Component
{
    public function render()
    {
        return view('livewire.task.page')
            ->layout('layouts.app');
    }
}
