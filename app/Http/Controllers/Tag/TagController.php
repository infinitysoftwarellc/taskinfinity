<?php

namespace App\Http\Controllers\Tag;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lógica para exibir todas as tags do usuário
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lógica para exibir o formulário de criação de tag
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lógica para salvar a nova tag
    }

    // Geralmente não se usa show/edit para tags, mas está aqui por padrão
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        // Lógica para exibir o formulário de edição de tag
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        // Lógica para atualizar a tag
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        // Lógica para deletar a tag
    }
}