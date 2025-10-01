<?php

namespace App\Http\Controllers\Pomodoro;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PomodoroController extends Controller
{
    /**
     * Display the Pomodoro workspace page.
     */
    public function __invoke(): View
    {
        return view('app.pomodoro.index');
    }
}
