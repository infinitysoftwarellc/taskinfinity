<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

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

    // Propriedades para Tags
    public array $selectedTags = [];
    public Collection $availableTags;
    public string $newTag = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'selectedTags' => ['sometimes', 'array'],
            'selectedTags.*' => ['exists:tags,id'],
            'newTag' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function mount(TaskList $taskList)
    {
        $this->taskList = $taskList;
        $this->loadAvailableTags();
    }

    public function loadAvailableTags(): void
    {
        $this->availableTags = auth()->user()->tags()->orderBy('name')->get();
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
        $this->selectedTags = $task->tags->pluck('id')->toArray();

        $this->dispatch('open-modal', name: 'task-modal');
    }

    public function addNewTag(): void
    {
        $this->validate(['newTag' => 'required|string|max:50|unique:tags,name,NULL,id,user_id,' . auth()->id()]);

        $tag = auth()->user()->tags()->create(['name' => $this->newTag]);

        // Recarrega as tags disponíveis e seleciona a nova
        $this->loadAvailableTags();
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
            $task = $this->taskList->tasks()->make($data);
            $task->user()->associate(auth()->user());
            $task->save();
        }

        $task->tags()->sync($this->selectedTags);

        $this->closeModal();
    }

    public function toggleCompleted(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);
        $task->update(['completed_at' => $task->completed_at ? null : now()]);
    }

    public function confirmDelete(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('delete', $task);
        $this->taskIdToDelete = $taskId;
        $this->dispatch('open-modal', name: 'confirm-task-deletion');
    }

    public function delete(): void
    {
        if ($this->taskIdToDelete === null) {
            return;
        }

        $task = Task::findOrFail($this->taskIdToDelete);
        $this->authorize('delete', $task);
        $task->delete();

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'task-modal');
        $this->dispatch('close-modal', name: 'confirm-task-deletion');
        $this->reset(['editingTask', 'title', 'description', 'due_date', 'taskIdToDelete', 'selectedTags', 'newTag']);
    }

    public function render()
    {
        $tasks = $this->taskList->tasks()->with('tags')->latest('created_at')->paginate(10);
        return view('livewire.task-manager', ['tasks' => $tasks]);
    }
}