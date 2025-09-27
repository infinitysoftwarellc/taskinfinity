<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Overview extends Component
{
    public function render()
    {
        $user = Auth::user();

        return view('livewire.dashboard.overview', [
            'userName' => $user?->name,
            'loginAt' => Date::now(),
        ]);
    }
}
