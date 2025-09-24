<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectList extends Component
{
    public Collection $projects;

    public function mount(): void
    {
        $this->loadProjects();
    }

    #[On('project-created')]
    public function loadProjects(): void
    {
        $this->projects = Project::where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.project-list');
    }
}