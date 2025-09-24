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

    // 1. ADICIONE UMA PROPRIEDADE PARA ARMAZENAR O PROJETO ATUAL
    public ?Project $currentProject = null;

    public string $newTaskDescription = '';

    public function mount($identifier): void
    {
        $this->identifier = $identifier;

        // 2. CARREGUE O PROJETO CORRETAMENTE NO MOUNT
        if ($this->identifier instanceof Project) {
            $this->currentProject = $this->identifier;
        }

        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        // 3. USE A NOVA PROPRIEDADE `currentProject` PARA A VERIFICAÇÃO
        if ($this->currentProject) {
            $this->pageTitle = $this->currentProject->name;
            $this->tasks = Task::where('project_id', $this->currentProject->id)
                               ->where('user_id', Auth::id())
                               ->where('organization_id', auth()->user()->organization_id)
                               ->orderBy('created_at', 'desc')
                               ->get();
        } else {
            // A lógica para filtros ('inbox', etc.) permanece a mesma
            $this->loadTasksByFilter($this->identifier);
        }
    }

    public function loadTasksByFilter(string $filter): void
    {
        $this->pageTitle = ucfirst($filter);

        $query = Task::where('user_id', Auth::id())
                     ->where('organization_id', auth()->user()->organization_id);

        $this->tasks = match ($filter) {
            'today' => $query->whereDate('due_date', today())->get(),
            'upcoming' => $query->where('due_date', '>', today())->get(),
            'inbox' => $query->whereNull('project_id')->get(),
            default => collect(),
        };
    }

    public function saveTask(): void
    {
        $this->validate([
            'newTaskDescription' => 'required|string|max:1000',
        ]);

        Task::create([
            'description' => $this->newTaskDescription,
            'user_id' => Auth::id(),
            'organization_id' => auth()->user()->organization_id,
            // 4. USE A PROPRIEDADE `currentProject` PARA SALVAR O ID CORRETO
            'project_id' => $this->currentProject?->id, // O '?->' garante que não haverá erro se o projeto for nulo
        ]);

        $this->reset('newTaskDescription');
        $this->loadTasks();
    }

    public function render()
    {
        // Lembre-se de remover o dd() se ele ainda estiver aqui
        return view('livewire.tasks-page');
    }
}