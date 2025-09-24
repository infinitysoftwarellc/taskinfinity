<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TasksPage extends Component
{
    public string $pageTitle = '';
    public Collection $tasks;
    public $identifier;
    public ?Project $currentProject = null;
    
    // RENOMEADO PARA MAIOR CLAREZA
    public string $newTaskName = '';

    public function mount($identifier): void
    {
        $this->identifier = $identifier;
        if ($identifier instanceof Project) {
            $this->currentProject = $identifier;
        } elseif (is_numeric($identifier)) {
            $this->currentProject = Project::where('id', $identifier)
                ->where('organization_id', auth()->user()->organization_id)
                ->first();
        }
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        // ... (código existente sem alterações)
        if ($this->currentProject) {
            $this->pageTitle = $this->currentProject->name;
            $this->tasks = Task::where('project_id', $this->currentProject->id)
                               ->where('parent_id', null)
                               ->where('user_id', Auth::id())
                               ->where('organization_id', auth()->user()->organization_id)
                               ->orderBy('created_at', 'desc')
                               ->get();
        } else {
            $this->loadTasksByFilter(strval($this->identifier));
        }
    }

    public function loadTasksByFilter(string $filter): void
    {
        // ... (código existente sem alterações)
        $this->pageTitle = ucfirst($filter);
        $query = Task::where('user_id', Auth::id())
                     ->where('organization_id', auth()->user()->organization_id)
                     ->where('parent_id', null);
        $this->tasks = match ($filter) {
            'today' => $query->whereDate('due_date', today())->get(),
            'upcoming' => $query->where('due_date', '>', today())->get(),
            'inbox' => $query->whereNull('project_id')->get(),
            default => collect(),
        };
    }

    public function saveTask(): void
    {
        // ATUALIZADO PARA VALIDAR a nova propriedade
        $this->validate([
            'newTaskName' => 'required|string|max:255',
        ]);

        Task::create([
            // ATUALIZADO PARA SALVAR EM 'name'
            'name' => $this->newTaskName,
            'user_id' => Auth::id(),
            'organization_id' => auth()->user()->organization_id,
            'project_id' => $this->currentProject?->id,
        ]);

        // ATUALIZADO PARA LIMPAR a nova propriedade
        $this->reset('newTaskName');
        $this->loadTasks();
    }

    public function render()
    {
        return view('livewire.tasks-page');
    }
}