<?php

// This controller orchestrates HTTP requests for the ai area related to ai request.
namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\AiRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AiRequestController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = AiRequest::where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['generate_checkpoints', 'summarize', 'suggest_plan'])],
            'input_chars' => 'nullable|integer',
            'output_chars' => 'nullable|integer',
            'model' => 'required|string|max:255',
            'cost_tokens' => 'nullable|integer',
            'status' => ['required', Rule::in(['ok', 'error'])],
            'error' => 'nullable|string',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'created_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $aiRequest = AiRequest::create($data);

        return response()->json($aiRequest, 201);
    }

    public function show(Request $request, AiRequest $aiRequest)
    {
        $this->guardUserModel($aiRequest, $request);

        return response()->json($aiRequest);
    }

    public function update(Request $request, AiRequest $aiRequest)
    {
        $this->guardUserModel($aiRequest, $request);

        $data = $request->validate([
            'input_chars' => 'nullable|integer',
            'output_chars' => 'nullable|integer',
            'model' => 'sometimes|string|max:255',
            'cost_tokens' => 'nullable|integer',
            'status' => ['sometimes', Rule::in(['ok', 'error'])],
            'error' => 'nullable|string',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'created_at' => 'nullable|date',
        ]);

        $aiRequest->update($data);

        return response()->json($aiRequest);
    }

    public function destroy(Request $request, AiRequest $aiRequest)
    {
        $this->guardUserModel($aiRequest, $request);

        $aiRequest->delete();

        return response()->noContent();
    }
}
