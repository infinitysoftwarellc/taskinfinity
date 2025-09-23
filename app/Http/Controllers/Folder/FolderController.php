<?php

namespace App\Http\Controllers\Folder;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class FolderController extends Controller
{
    /**
     * Display a listing of the user's folders.
     */
    public function index()
    {
        $folders = Auth::user()->folders()->latest()->get();

        // Você precisará criar a view: resources/views/folders/index.blade.php
        return view('folders.index', compact('folders'));
    }

    /**
     * Show the form for creating a new folder.
     */
    public function create()
    {
        // Você precisará criar a view: resources/views/folders/create.blade.php
        return view('folders.create');
    }

    /**
     * Store a newly created folder in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $request->user()->folders()->create($validated);

        return redirect()->route('webapp.folders.index')->with('success', 'Pasta criada com sucesso!');
    }

    /**
     * Display the specified folder and its task lists.
     */
    public function show(Folder $folder)
    {
        // Garante que o usuário só pode ver suas próprias pastas
        $this->authorize('view', $folder);

        // Você precisará criar a view: resources/views/folders/show.blade.php
        return view('folders.show', compact('folder'));
    }

    /**
     * Show the form for editing the specified folder.
     */
    public function edit(Folder $folder)
    {
        // Garante que o usuário só pode editar suas próprias pastas
        $this->authorize('update', $folder);

        // Você precisará criar a view: resources/views/folders/edit.blade.php
        return view('folders.edit', compact('folder'));
    }

    /**
     * Update the specified folder in storage.
     */
    public function update(Request $request, Folder $folder)
    {
        // Garante que o usuário só pode atualizar suas próprias pastas
        $this->authorize('update', $folder);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $folder->update($validated);

        return redirect()->route('webapp.folders.index')->with('success', 'Pasta atualizada com sucesso!');
    }

    /**
     * Remove the specified folder from storage.
     */
    public function destroy(Folder $folder)
    {
        // Garante que o usuário só pode deletar suas próprias pastas
        $this->authorize('delete', $folder);

        $folder->delete();

        return redirect()->route('webapp.folders.index')->with('success', 'Pasta deletada com sucesso!');
    }
}