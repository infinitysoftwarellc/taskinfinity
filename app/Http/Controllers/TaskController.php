<?php

namespace App\Http\Controllers;

use App\Models\Project; // Supondo que você terá um model Project
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Mostra a página principal de tarefas, passando o identificador (filtro ou projeto).
     *
     * @param string|Project $identifier
     * @return View
     */
    public function index($identifier = 'inbox'): View
    {
        return view('tasks.index', [
            'identifier' => $identifier
        ]);
    }
}