<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\HabitMonthlyStat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitMonthlyStatController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = HabitMonthlyStat::query()
            ->whereHas('habit', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderByDesc('year')
            ->orderByDesc('month');

        if ($request->filled('habit_id')) {
            $query->where('habit_id', $request->integer('habit_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'habit_id' => ['required', 'integer', Rule::exists('habits', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'days_done_count' => 'nullable|integer',
            'total_checkins' => 'nullable|integer',
            'best_streak_in_month' => 'nullable|integer',
            'updated_at' => 'nullable|date',
        ]);

        $stat = HabitMonthlyStat::create($data);

        return response()->json($stat, 201);
    }

    public function show(Request $request, HabitMonthlyStat $habitMonthlyStat)
    {
        $this->guardUserModel($habitMonthlyStat->habit, $request);

        return response()->json($habitMonthlyStat);
    }

    public function update(Request $request, HabitMonthlyStat $habitMonthlyStat)
    {
        $this->guardUserModel($habitMonthlyStat->habit, $request);

        $data = $request->validate([
            'days_done_count' => 'nullable|integer',
            'total_checkins' => 'nullable|integer',
            'best_streak_in_month' => 'nullable|integer',
            'updated_at' => 'nullable|date',
        ]);

        $habitMonthlyStat->update($data);

        return response()->json($habitMonthlyStat);
    }

    public function destroy(Request $request, HabitMonthlyStat $habitMonthlyStat)
    {
        $this->guardUserModel($habitMonthlyStat->habit, $request);

        $habitMonthlyStat->delete();

        return response()->noContent();
    }
}
