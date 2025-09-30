<?php

// This controller orchestrates HTTP requests for the gamification area related to ability.
namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Ability;
use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function index()
    {
        return response()->json(Ability::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:abilities,code',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'effect_json' => 'required|array',
        ]);

        $ability = Ability::create($data);

        return response()->json($ability, 201);
    }

    public function show(Ability $ability)
    {
        return response()->json($ability);
    }

    public function update(Request $request, Ability $ability)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'effect_json' => 'sometimes|array',
        ]);

        $ability->update($data);

        return response()->json($ability);
    }

    public function destroy(Ability $ability)
    {
        $ability->delete();

        return response()->noContent();
    }
}
