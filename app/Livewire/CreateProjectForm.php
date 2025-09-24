<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class CreateProjectForm extends Component
{
    public string $name = '';
    public string $color = '#4f46e5';

    public function saveProject(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7', // Hex color
        ]);

        $project = Project::create([
            'name' => $this->name,
            'color' => $this->color,
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()->organization_id,
        ]);

        $this->reset('name', 'color');

        // Dispatch the event to notify other components
        $this->dispatch('project-created');

        // Redirect to the newly created project page
        $this->redirectRoute('tasks.project', ['project' => $project]);
    }

    public function render()
    {
        return view('livewire.create-project-form');
    }
}