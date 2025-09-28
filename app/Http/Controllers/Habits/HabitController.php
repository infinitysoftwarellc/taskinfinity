<?php

namespace App\Http\Controllers\Habits;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = Habit::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'status' => ['nullable', Rule::in(['active', 'archived'])],
        ]);

        $data['user_id'] = $request->user()->id;

        $habit = Habit::create($data);

        return response()->json($habit, 201);
    }

    public function show(Request $request, Habit $habit)
    {
        $this->guardUserModel($habit, $request);

        return response()->json($habit->load(['checkins', 'monthlyStats', 'streakCache']));
    }

    public function update(Request $request, Habit $habit)
    {
        $this->guardUserModel($habit, $request);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'status' => ['nullable', Rule::in(['active', 'archived'])],
        ]);

        $habit->update($data);

        return response()->json($habit);
    }

    public function destroy(Request $request, Habit $habit)
    {
        $this->guardUserModel($habit, $request);

        $habit->delete();

        return response()->noContent();
    }
}
