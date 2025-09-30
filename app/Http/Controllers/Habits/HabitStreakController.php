<?php

// This controller orchestrates HTTP requests for the habits area related to habit streak.
namespace App\Http\Controllers\Habits;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\HabitStreakCache;
use Illuminate\Http\Request;

class HabitStreakController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = HabitStreakCache::query()
            ->whereHas('habit', fn ($q) => $q->where('user_id', $request->user()->id));

        if ($request->filled('habit_id')) {
            $query->where('habit_id', $request->integer('habit_id'));
        }

        return response()->json($query->get());
    }

    public function show(Request $request, HabitStreakCache $habitStreakCache)
    {
        $this->guardUserModel($habitStreakCache->habit, $request);

        return response()->json($habitStreakCache);
    }

    public function update(Request $request, HabitStreakCache $habitStreakCache)
    {
        $this->guardUserModel($habitStreakCache->habit, $request);

        $data = $request->validate([
            'current_streak' => 'nullable|integer',
            'longest_streak' => 'nullable|integer',
            'last_checkin_local' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $habitStreakCache->update($data);

        return response()->json($habitStreakCache);
    }
}
