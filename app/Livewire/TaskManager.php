<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function mount(TaskList $taskList)
    {
        $this->taskList = $taskList;
    }

    public function showCreateModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['editingTask', 'title', 'description', 'due_date']);
        $this->dispatch('open-modal', name: 'task-modal');
    }

    public function showEditModal(int $taskId): void
    {
        $this->resetErrorBag();
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);

        $this->editingTask = $task;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : null;

        $this->dispatch('open-modal', name: 'task-modal');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'user_id' => auth()->id(),
        ];

        if ($this->editingTask) {
            $this->authorize('update', $this->editingTask);
            $this->editingTask->update($data);
        } else {
            $this->taskList->tasks()->create($data);
        }

        $this->closeModal();
    }

    public function toggleCompleted(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);
        $task->update(['completed' => !$task->completed]);
    }

    public function confirmDelete(int $taskId): void
    {
        $this->taskIdToDelete = $taskId;
        $this->dispatch('open-modal', name: 'confirm-task-deletion');
    }

    public function delete(): void
    {
        $task = Task::findOrFail($this->taskIdToDelete);
        $this->authorize('delete', $task);
        $task->delete();

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'task-modal');
        $this->dispatch('close-modal', name: 'confirm-task-deletion');
        $this->reset(['editingTask', 'title', 'description', 'due_date', 'taskIdToDelete']);
    }

    public function render()
    {
        $tasks = $this->taskList->tasks()->latest()->paginate(10);
        return view('livewire.task-manager', ['tasks' => $tasks]);
    }
}