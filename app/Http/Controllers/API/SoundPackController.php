<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SoundPack;
use Illuminate\Http\Request;

class SoundPackController extends Controller
{
    public function index()
    {
        return response()->json(SoundPack::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:sound_packs,code',
            'name' => 'required|string|max:255',
            'files_json' => 'required|array',
        ]);

        $soundPack = SoundPack::create($data);

        return response()->json($soundPack, 201);
    }

    public function show(SoundPack $soundPack)
    {
        return response()->json($soundPack);
    }

    public function update(Request $request, SoundPack $soundPack)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'files_json' => 'sometimes|array',
        ]);

        $soundPack->update($data);

        return response()->json($soundPack);
    }

    public function destroy(SoundPack $soundPack)
    {
        $soundPack->delete();

        return response()->noContent();
    }
}
