<?php

namespace App\Livewire;

use App\Models\Tag; // Importe o model Tag
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection; // Importe a Collection

class TaskManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public TaskList $taskList;
    public ?Task $editingTask = null;
    public ?int $taskIdToDelete = null;

    // Campos do formulário
    public string $title = '';
    public string $description = '';
    public ?string $due_date = null;

    // --- NOVAS PROPRIEDADES PARA TAGS ---
    public array $selectedTags = [];
    public Collection $availableTags;
    public string $newTag = '';
    // ------------------------------------

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            // --- REGRAS PARA TAGS ---
            'selectedTags' => ['sometimes', 'array'],
            'selectedTags.*' => ['exists:tags,id'],
            'newTag' => ['nullable', 'string', 'max:50'],
            // -------------------------
        ];
    }

    public function mount(TaskList $taskList)
    {
        $this->taskList = $taskList;
        // Carrega todas as tags do usuário para o modal
        $this->loadAvailableTags();
    }

    // --- NOVO MÉTODO ---
    // Carrega as tags disponíveis do usuário
    public function loadAvailableTags()
    {
        $this->availableTags = auth()->user()->tags()->get();
    }

    public function showCreateModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['editingTask', 'title', 'description', 'due_date', 'selectedTags', 'newTag']);
        $this->dispatch('open-modal', name: 'task-modal');
    }

    public function showEditModal(int $taskId): void
    {
        $this->resetErrorBag();
        $task = Task::with('tags')->findOrFail($taskId);
        $this->authorize('update', $task);

        $this->editingTask = $task;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : null;

        // Popula as tags que já estão selecionadas para esta tarefa
        $this->selectedTags = $task->tags->pluck('id')->toArray();

        $this->dispatch('open-modal', name: 'task-modal');
    }

    // --- NOVO MÉTODO ---
    // Adiciona uma nova tag criada pelo usuário
    public function addNewTag()
    {
        $this->validate(['newTag' => 'required|string|max:50|unique:tags,name,NULL,id,user_id,'.auth()->id()]);

        $tag = auth()->user()->tags()->create([
            'name' => $this->newTag,
            // 'color' => '#ffffff' // Você pode adicionar uma cor padrão ou um seletor de cor no futuro
        ]);

        // Adiciona a nova tag à lista de disponíveis e já a seleciona
        $this->availableTags->push($tag);
        $this->selectedTags[] = $tag->id;
        $this->reset('newTag');
    }


    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
        ];
        
        $task = $this->editingTask;

        if ($task) {
            $this->authorize('update', $task);
            $task->update($data);
        } else {
            // Garante que a tarefa seja criada pelo usuário logado e associada à lista de tarefas
            $task = $this->taskList->tasks()->make($data);
            $task->user()->associate(auth()->user());
            $task->save();
        }
        
        // Sincroniza as tags selecionadas com a tarefa
        $task->tags()->sync($this->selectedTags);


        $this->closeModal();
    }

    // ... (restante do código: toggleCompleted, confirmDelete, delete) ...

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'task-modal');
        $this->dispatch('close-modal', name: 'confirm-task-deletion');
        $this->reset(['editingTask', 'title', 'description', 'due_date', 'taskIdToDelete', 'selectedTags', 'newTag']);
    }

    public function render()
    {
        // Carrega as tarefas já com suas tags para evitar N+1 queries
        $tasks = $this->taskList->tasks()->with('tags')->latest()->paginate(10);
        return view('livewire.task-manager', ['tasks' => $tasks]);
    }
}