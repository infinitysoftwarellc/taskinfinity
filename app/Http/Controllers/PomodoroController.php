<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PomodoroController extends Controller
{
    /**
     * Exibe a página do timer Pomodoro.
     */
    public function index(): View
    {
        // Altere para a nova view
        return view('pomodoro');
    }
}