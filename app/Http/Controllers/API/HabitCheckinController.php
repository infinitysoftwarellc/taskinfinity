<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitCheckin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitCheckinController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = HabitCheckin::query()
            ->whereHas('habit', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderByDesc('checked_on_local');

        if ($request->filled('habit_id')) {
            $query->where('habit_id', $request->integer('habit_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'habit_id' => ['required', 'integer', Rule::exists('habits', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'checked_on_local' => 'required|date',
            'created_at' => 'nullable|date',
        ]);

        $checkin = HabitCheckin::create($data);

        return response()->json($checkin, 201);
    }

    public function show(Request $request, HabitCheckin $habitCheckin)
    {
        $this->guardUserModel($habitCheckin->habit, $request);

        return response()->json($habitCheckin);
    }

    public function update(Request $request, HabitCheckin $habitCheckin)
    {
        $this->guardUserModel($habitCheckin->habit, $request);

        $data = $request->validate([
            'checked_on_local' => 'sometimes|date',
            'created_at' => 'nullable|date',
        ]);

        $habitCheckin->update($data);

        return response()->json($habitCheckin);
    }

    public function destroy(Request $request, HabitCheckin $habitCheckin)
    {
        $this->guardUserModel($habitCheckin->habit, $request);

        $habitCheckin->delete();

        return response()->noContent();
    }
}
