<?php

namespace App\Http\Controllers\TaskList;

use App\Http\Controllers\Controller;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lógica para exibir as listas de tarefas (geralmente dentro de uma pasta)
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lógica para exibir o formulário de criação de lista de tarefas
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lógica para salvar a nova lista de tarefas
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskList $taskList)
    {
        // Lógica para exibir uma lista de tarefas e suas tarefas
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskList $taskList)
    {
        // Lógica para exibir o formulário de edição da lista
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskList $taskList)
    {
        // Lógica para atualizar a lista
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskList $taskList)
    {
        // Lógica para deletar a lista
    }
}