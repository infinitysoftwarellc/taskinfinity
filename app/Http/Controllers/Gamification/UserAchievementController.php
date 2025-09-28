<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\UserAchievement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAchievementController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $achievements = UserAchievement::where('user_id', $request->user()->id)
            ->with('achievement')
            ->get();

        return response()->json($achievements);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'achievement_id' => ['required', 'integer', Rule::exists('achievements', 'id')],
            'unlocked_at' => 'required|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $userAchievement = UserAchievement::updateOrCreate(
            ['user_id' => $data['user_id'], 'achievement_id' => $data['achievement_id']],
            ['unlocked_at' => $data['unlocked_at']]
        );

        return response()->json($userAchievement, 201);
    }

    public function show(Request $request, UserAchievement $userAchievement)
    {
        $this->guardUserModel($userAchievement, $request);

        return response()->json($userAchievement->load('achievement'));
    }

    public function update(Request $request, UserAchievement $userAchievement)
    {
        $this->guardUserModel($userAchievement, $request);

        $data = $request->validate([
            'unlocked_at' => 'required|date',
        ]);

        $userAchievement->update($data);

        return response()->json($userAchievement);
    }

    public function destroy(Request $request, UserAchievement $userAchievement)
    {
        $this->guardUserModel($userAchievement, $request);

        $userAchievement->delete();

        return response()->noContent();
    }
}
