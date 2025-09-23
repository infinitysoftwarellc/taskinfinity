<?php

namespace App\Livewire;

use App\Models\Folder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class FolderManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?Folder $editingFolder = null;
    public string $name = '';
    public ?int $folderIdToDelete = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function showCreateModal(): void
    {
        $this->closeModal(); // evita modal duplicado
        $this->reset(['editingFolder', 'name']);
        $this->dispatch('open-modal', name: 'folder-modal');
    }

    public function showEditModal(int $folderId): void
    {
        $this->closeModal(); // evita modal duplicado
        $folder = Folder::findOrFail($folderId);
        $this->authorize('update', $folder);

        $this->editingFolder = $folder;
        $this->name = $folder->name;

        $this->dispatch('open-modal', name: 'folder-modal');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingFolder) {
            $this->authorize('update', $this->editingFolder);
            $this->editingFolder->update(['name' => $this->name]);
        } else {
            auth()->user()->folders()->create(['name' => $this->name]);
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'folder-modal');
        $this->dispatch('close-modal', name: 'confirm-folder-deletion');
        $this->reset(['editingFolder', 'name', 'folderIdToDelete']);
    }

    public function confirmDelete(int $folderId): void
    {
        $this->folderIdToDelete = $folderId;
        $this->dispatch('open-modal', name: 'confirm-folder-deletion');
    }

    public function delete(): void
    {
        $folder = Folder::findOrFail($this->folderIdToDelete);
        $this->authorize('delete', $folder);
        $folder->delete();

        $this->closeModal();
    }

    public function render()
    {
        $folders = auth()->user()->folders()->latest()->paginate(10);

        return view('livewire.folder-manager', [
            'folders' => $folders,
        ]);
    }
}
