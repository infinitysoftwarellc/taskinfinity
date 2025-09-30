<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador de anexos utilizados no fluxo da Tasks page.
 */
class AttachmentController extends Controller
{
    use InteractsWithUserModels;

    /**
     * Lista anexos pertencentes ao usuário, filtrando opcionalmente por missão.
     */
    public function index(Request $request)
    {
        $query = Attachment::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($request->filled('mission_id')) {
            $query->where('mission_id', $request->integer('mission_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Salva um novo anexo associado a uma missão.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'mission_id' => ['required', 'integer', Rule::exists('missions', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'path' => 'required|string',
            'original_name' => 'required|string|max:255',
            'size' => 'required|integer|min:0',
            'mime' => 'required|string|max:255',
            'created_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $attachment = Attachment::create($data);

        return response()->json($attachment, 201);
    }

    /**
     * Exibe detalhes de um anexo específico.
     */
    public function show(Request $request, Attachment $attachment)
    {
        $this->guardUserModel($attachment, $request);

        return response()->json($attachment);
    }

    /**
     * Atualiza metadados do anexo armazenado.
     */
    public function update(Request $request, Attachment $attachment)
    {
        $this->guardUserModel($attachment, $request);

        $data = $request->validate([
            'path' => 'sometimes|string',
            'original_name' => 'sometimes|string|max:255',
            'size' => 'sometimes|integer|min:0',
            'mime' => 'sometimes|string|max:255',
            'created_at' => 'nullable|date',
        ]);

        $attachment->update($data);

        return response()->json($attachment);
    }

    /**
     * Remove um anexo vinculado à Tasks page.
     */
    public function destroy(Request $request, Attachment $attachment)
    {
        $this->guardUserModel($attachment, $request);

        $attachment->delete();

        return response()->noContent();
    }
}
