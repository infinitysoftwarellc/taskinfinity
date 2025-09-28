<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        return response()->json(Achievement::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:achievements,code',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'criteria_json' => 'required|array',
            'reward_xp' => 'required|integer',
        ]);

        $achievement = Achievement::create($data);

        return response()->json($achievement, 201);
    }

    public function show(Achievement $achievement)
    {
        return response()->json($achievement);
    }

    public function update(Request $request, Achievement $achievement)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'criteria_json' => 'sometimes|array',
            'reward_xp' => 'sometimes|integer',
        ]);

        $achievement->update($data);

        return response()->json($achievement);
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return response()->noContent();
    }
}
