<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PlanLimit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanLimitController extends Controller
{
    public function index()
    {
        return response()->json(PlanLimit::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_code' => ['required', Rule::in(['free', 'pro'])],
            'limits_json' => 'required|array',
        ]);

        $planLimit = PlanLimit::create($data);

        return response()->json($planLimit, 201);
    }

    public function show(PlanLimit $planLimit)
    {
        return response()->json($planLimit);
    }

    public function update(Request $request, PlanLimit $planLimit)
    {
        $data = $request->validate([
            'limits_json' => 'required|array',
        ]);

        $planLimit->update($data);

        return response()->json($planLimit);
    }

    public function destroy(PlanLimit $planLimit)
    {
        $planLimit->delete();

        return response()->noContent();
    }
}
