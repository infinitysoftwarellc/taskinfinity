<?php

namespace App\Http\Controllers\Habits;

use App\Http\Controllers\Controller;
use App\Livewire\Habits\HabitTracker;

class HabitTrackerPageController extends Controller
{
    public function __invoke()
    {
        return app(HabitTracker::class)->render();
    }
}
