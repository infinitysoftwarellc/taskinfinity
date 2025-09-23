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

    public bool $showModal = false;
    public ?Folder $editingFolder = null;
    public string $name = '';
    public ?int $folderIdToDelete = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function showCreateModal()
    {
        $this->reset(['editingFolder', 'name']);
        $this->showModal = true;
    }

    public function showEditModal(Folder $folder)
    {
        $this->authorize('update', $folder);
        $this->editingFolder = $folder;
        $this->name = $folder->name;
        $this->showModal = true;
    }

    public function save()
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
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingFolder', 'name']);
    }

    public function confirmDelete(int $folderId)
    {
        $this->folderIdToDelete = $folderId;
    }

    public function delete()
    {
        $folder = Folder::findOrFail($this->folderIdToDelete);
        $this->authorize('delete', $folder);
        $folder->delete();
        $this->folderIdToDelete = null;
    }

    public function render()
    {
        $folders = auth()->user()->folders()->latest()->paginate(10);
        return view('livewire.folder-manager', [
            'folders' => $folders,
        ]);
    }
}