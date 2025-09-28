<?php

namespace App\Http\Controllers\Preferences;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        return response()->json(Theme::with('soundPack')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:themes,code',
            'name' => 'required|string|max:255',
            'palette_json' => 'required|array',
            'background_asset' => 'required|string',
            'sound_pack_id' => 'nullable|exists:sound_packs,id',
        ]);

        $theme = Theme::create($data);

        return response()->json($theme, 201);
    }

    public function show(Theme $theme)
    {
        return response()->json($theme->load('soundPack'));
    }

    public function update(Request $request, Theme $theme)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'palette_json' => 'sometimes|array',
            'background_asset' => 'sometimes|string',
            'sound_pack_id' => 'nullable|exists:sound_packs,id',
        ]);

        $theme->update($data);

        return response()->json($theme);
    }

    public function destroy(Theme $theme)
    {
        $theme->delete();

        return response()->noContent();
    }
}
