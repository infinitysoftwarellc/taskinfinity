<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\BigGoal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BigGoalController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = BigGoal::where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active', 'done', 'archived'])],
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'created_by_ai' => 'nullable|boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        $goal = BigGoal::create($data);

        return response()->json($goal, 201);
    }

    public function show(Request $request, BigGoal $bigGoal)
    {
        $this->guardUserModel($bigGoal, $request);

        return response()->json($bigGoal->load('steps'));
    }

    public function update(Request $request, BigGoal $bigGoal)
    {
        $this->guardUserModel($bigGoal, $request);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active', 'done', 'archived'])],
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'created_by_ai' => 'nullable|boolean',
        ]);

        $bigGoal->update($data);

        return response()->json($bigGoal);
    }

    public function destroy(Request $request, BigGoal $bigGoal)
    {
        $this->guardUserModel($bigGoal, $request);

        $bigGoal->delete();

        return response()->noContent();
    }
}
