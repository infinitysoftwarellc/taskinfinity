<?php

// This controller orchestrates HTTP requests for the goals area related to goal step.
namespace App\Http\Controllers\Goals;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\GoalStep;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GoalStepController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = GoalStep::query()
            ->whereHas('goal', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderBy('position');

        if ($request->filled('goal_id')) {
            $query->where('goal_id', $request->integer('goal_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'goal_id' => ['required', 'integer', Rule::exists('big_goals', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'title' => 'required|string|max:255',
            'is_done' => 'nullable|boolean',
            'position' => 'nullable|integer',
            'note' => 'nullable|string',
        ]);

        $step = GoalStep::create($data);

        return response()->json($step, 201);
    }

    public function show(Request $request, GoalStep $goalStep)
    {
        $this->guardUserModel($goalStep->goal, $request);

        return response()->json($goalStep);
    }

    public function update(Request $request, GoalStep $goalStep)
    {
        $this->guardUserModel($goalStep->goal, $request);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'is_done' => 'nullable|boolean',
            'position' => 'nullable|integer',
            'note' => 'nullable|string',
        ]);

        $goalStep->update($data);

        return response()->json($goalStep);
    }

    public function destroy(Request $request, GoalStep $goalStep)
    {
        $this->guardUserModel($goalStep->goal, $request);

        $goalStep->delete();

        return response()->noContent();
    }
}
