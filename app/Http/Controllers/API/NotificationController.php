<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'channel' => ['required', Rule::in(['web', 'email', 'push'])],
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'payload_json' => 'nullable|array',
            'theme_code' => ['nullable', Rule::in(['default', 'gamer', 'forest'])],
            'sent_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $notification = Notification::create($data);

        return response()->json($notification, 201);
    }

    public function show(Request $request, Notification $notification)
    {
        $this->guardUserModel($notification, $request);

        return response()->json($notification);
    }

    public function update(Request $request, Notification $notification)
    {
        $this->guardUserModel($notification, $request);

        $data = $request->validate([
            'channel' => ['sometimes', Rule::in(['web', 'email', 'push'])],
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'payload_json' => 'nullable|array',
            'theme_code' => ['nullable', Rule::in(['default', 'gamer', 'forest'])],
            'sent_at' => 'nullable|date',
        ]);

        $notification->update($data);

        return response()->json($notification);
    }

    public function destroy(Request $request, Notification $notification)
    {
        $this->guardUserModel($notification, $request);

        $notification->delete();

        return response()->noContent();
    }
}
