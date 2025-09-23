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
     * Show the form for creating a new task list within a specific folder.
     */
    public function create(Request $request)
    {
        $folder = Folder::findOrFail($request->query('folder_id'));
        $this->authorize('update', $folder);

        return view('tasklists.create', compact('folder'));
    }

    /**
     * Store a newly created task list in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::findOrFail($validated['folder_id']);
        $this->authorize('update', $folder);

        // Forma mais robusta de criar, usando o relacionamento da pasta
        $folder->taskLists()->create([
            'name' => $validated['name'],
            'user_id' => Auth::id(),
        ]);

        // CORREÇÃO: Usando o nome de rota correto
        return redirect()->route('webapp.folders.show', $folder)->with('success', 'Lista de tarefas criada com sucesso!');
    }

    /**
     * Display the specified task list.
     */
    public function show(TaskList $tasklist)
    {
        $this->authorize('view', $tasklist);
        $tasklist->load('tasks');
        
        return view('tasklists.show', compact('tasklist'));
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $tasklist)
    {
        $this->authorize('update', $tasklist);

        return view('tasklists.edit', compact('tasklist'));
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(Request $request, TaskList $tasklist)
    {
        $this->authorize('update', $tasklist);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tasklist->update($validated);

        // CORREÇÃO: Usando o nome de rota correto
        return redirect()->route('webapp.folders.show', $tasklist->folder_id)->with('success', 'Lista de tarefas atualizada com sucesso!');
    }

    /**
     * Remove the specified task list from storage.
     */
    public function destroy(TaskList $tasklist)
    {
        $this->authorize('delete', $tasklist);

        $folderId = $tasklist->folder_id;
        $tasklist->delete();

        // CORREÇÃO: Usando o nome de rota correto
        return redirect()->route('webapp.folders.show', $folderId)->with('success', 'Lista de tarefas deletada com sucesso!');
    }
}