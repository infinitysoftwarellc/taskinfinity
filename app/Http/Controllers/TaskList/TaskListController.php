<?php

namespace App\Http\Controllers\TaskList;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource. 
     * NOTE: Usually, task lists are shown within the context of a folder.
     * This method can be used for an "all lists" view if needed.
     */
    public function index()
    {
        $taskLists = Auth::user()->taskLists()->with('folder')->latest()->get();

        // View: resources/views/tasklists/index.blade.php
        return view('tasklists.index', compact('taskLists'));
    }

    /**
     * Show the form for creating a new task list within a specific folder.
     */
    public function create(Request $request)
    {
        // We need to know which folder this list will belong to.
        $folder = Folder::findOrFail($request->query('folder_id'));

        // Authorize that the user can update the folder (which implies they can add lists to it)
        $this->authorize('update', $folder);

        // View: resources/views/tasklists/create.blade.php
        return view('tasklists.create', compact('folder'));
    }

    /**
     * Store a newly created task list in storage.
     */
     public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'task_list_id' => 'required|exists:task_lists,id',
        ]);

        // 1. Encontra a lista de tarefas pai
        $taskList = TaskList::findOrFail($validated['task_list_id']);
        
        // 2. Autoriza se o usuário pode adicionar tarefas a esta lista
        $this->authorize('update', $taskList);

        // 3. CORREÇÃO: Cria a tarefa através do relacionamento com a TaskList
        // Isso garante que o 'task_list_id' seja preenchido corretamente.
        $taskList->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'user_id' => auth()->id(), // Garante que o dono da tarefa seja o usuário logado
        ]);

        // Redireciona de volta para a página da lista de tarefas
        return redirect()->route('tasklists.show', $taskList)->with('success', 'Tarefa criada com sucesso!');
    }
    /**
     * Display the specified task list.
     */
     public function show(TaskList $tasklist)
    {
        $this->authorize('view', $tasklist);

        // Carrega as tarefas associadas para evitar múltiplas queries
        $tasklist->load('tasks');
        
        // CORREÇÃO: Apontando para a view correta
        return view('tasklists.show', compact('tasklist'));
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('update', $tasklist); // <-- CORRIGIDO AQUI

        // View: resources/views/tasklists/edit.blade.php
        return view('tasklists.edit', compact('tasklist')); // <-- CORRIGIDO AQUI
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(Request $request, TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('update', $tasklist); // <-- CORRIGIDO AQUI

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tasklist->update($validated); // <-- CORRIGIDO AQUI

        return redirect()->route('webapp.folders.show', $tasklist->folder_id)->with('success', 'Lista de tarefas atualizada com sucesso!'); // <-- CORRIGIDO AQUI
    }

    /**
     * Remove the specified task list from storage.
     */
    public function destroy(TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('delete', $tasklist); // <-- CORRIGIDO AQUI

        $folderId = $tasklist->folder_id; // <-- CORRIGIDO AQUI
        $tasklist->delete(); // <-- CORRIGIDO AQUI

        return redirect()->route('webapp.folders.show', $folderId)->with('success', 'Lista de tarefas deletada com sucesso!');
    }
}