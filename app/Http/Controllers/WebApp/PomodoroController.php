<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PomodoroController extends Controller
{
    public function index(): View
    {
        return view('webapp.pomodoro.index');
    }
}
