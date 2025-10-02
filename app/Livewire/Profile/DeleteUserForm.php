<?php

namespace App\Livewire\Profile;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public string $password = '';

    public bool $confirmingDeletion = false;

    public function render()
    {
        return view('livewire.profile.delete-user-form');
    }

    public function confirmDeletion(): void
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->confirmingDeletion = true;
    }

    public function cancelDeletion(): void
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->confirmingDeletion = false;
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}
