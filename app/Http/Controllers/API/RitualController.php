<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Ritual;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RitualController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $rituals = Ritual::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rituals);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'frequency' => ['required', Rule::in(['weekly', 'monthly'])],
        ]);

        $data['user_id'] = $request->user()->id;

        $ritual = Ritual::create($data);

        return response()->json($ritual, 201);
    }

    public function show(Request $request, Ritual $ritual)
    {
        $this->guardUserModel($ritual, $request);

        return response()->json($ritual->load('entries'));
    }

    public function update(Request $request, Ritual $ritual)
    {
        $this->guardUserModel($ritual, $request);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'frequency' => ['sometimes', Rule::in(['weekly', 'monthly'])],
        ]);

        $ritual->update($data);

        return response()->json($ritual);
    }

    public function destroy(Request $request, Ritual $ritual)
    {
        $this->guardUserModel($ritual, $request);

        $ritual->delete();

        return response()->noContent();
    }
}
