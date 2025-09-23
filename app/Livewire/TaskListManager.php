<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\TaskList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class TaskListManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public Folder $folder;
    public ?TaskList $editingTaskList = null;
    public string $name = '';
    public ?int $taskListIdToDelete = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function mount(Folder $folder)
    {
        $this->folder = $folder;
    }

    public function showCreateModal(): void
    {
        $this->closeModal();
        $this->reset(['editingTaskList', 'name']);
        $this->dispatch('open-modal', name: 'tasklist-modal');
    }

    public function showEditModal(int $taskListId): void
    {
        $this->closeModal();
        $taskList = TaskList::findOrFail($taskListId);
        $this->authorize('update', $taskList);

        $this->editingTaskList = $taskList;
        $this->name = $taskList->name;

        $this->dispatch('open-modal', name: 'tasklist-modal');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingTaskList) {
            $this->authorize('update', $this->editingTaskList);
            $this->editingTaskList->update(['name' => $this->name]);
        } else {
            $this->folder->taskLists()->create([
                'name' => $this->name,
                'user_id' => auth()->id(),
            ]);
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'tasklist-modal');
        $this->dispatch('close-modal', name: 'confirm-tasklist-deletion');
        $this->reset(['editingTaskList', 'name', 'taskListIdToDelete']);
    }

    public function confirmDelete(int $taskListId): void
    {
        $this->taskListIdToDelete = $taskListId;
        $this->dispatch('open-modal', name: 'confirm-tasklist-deletion');
    }

    public function delete(): void
    {
        $taskList = TaskList::findOrFail($this->taskListIdToDelete);
        $this->authorize('delete', $taskList);
        $taskList->delete();

        $this->closeModal();
    }

    public function render()
    {
        $taskLists = $this->folder->taskLists()->latest()->paginate(10);

        return view('livewire.task-list-manager', [
            'taskLists' => $taskLists,
        ]);
    }
}
