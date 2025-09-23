<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('task.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lógica para exibir o formulário de criação de tarefa
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lógica para salvar a nova tarefa
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Lógica para exibir uma tarefa específica
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Lógica para exibir o formulário de edição da tarefa
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Lógica para atualizar a tarefa
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Lógica para deletar a tarefa
    }
}