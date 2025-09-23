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

    // 1. Remova a propriedade $showModal, não a usaremos para controlar a visibilidade.
    // public bool $showModal = false;

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
        // 2. Despache o evento para o Alpine.js abrir o modal
        $this->dispatch('open-modal', name: 'folder-modal');
    }

    public function showEditModal(Folder $folder)
    {
        $this->authorize('update', $folder);
        $this->editingFolder = $folder;
        $this->name = $folder->name;
        // 3. Despache o evento para o Alpine.js abrir o modal
        $this->dispatch('open-modal', name: 'folder-modal');
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

        // 4. Chame o closeModal, que agora despachará um evento
        $this->closeModal();
    }
    
    public function closeModal()
    {
        // 5. Despache o evento para o Alpine.js fechar o modal
        $this->dispatch('close-modal', name: 'folder-modal');
        $this->reset(['editingFolder', 'name']);
    }

    public function confirmDelete(int $folderId)
    {
        $this->folderIdToDelete = $folderId;
        // 6. Despache o evento para abrir o modal de confirmação
        $this->dispatch('open-modal', name: 'confirm-folder-deletion');
    }

    public function delete()
    {
        $folder = Folder::findOrFail($this->folderIdToDelete);
        $this->authorize('delete', $folder);
        $folder->delete();
        
        // 7. Despache o evento para fechar o modal e resete a propriedade
        $this->dispatch('close-modal', name: 'confirm-folder-deletion');
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