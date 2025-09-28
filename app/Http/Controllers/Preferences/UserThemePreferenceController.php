<?php

namespace App\Http\Controllers\Preferences;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\UserThemePreference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserThemePreferenceController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $prefs = UserThemePreference::where('user_id', $request->user()->id)->get();

        return response()->json($prefs);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'theme_code' => ['required', Rule::in(['default', 'gamer', 'forest'])],
            'settings_json' => 'nullable|array',
        ]);

        $data['user_id'] = $request->user()->id;

        $pref = UserThemePreference::updateOrCreate(
            ['user_id' => $data['user_id'], 'theme_code' => $data['theme_code']],
            ['settings_json' => $data['settings_json'] ?? []]
        );

        return response()->json($pref, 201);
    }

    public function show(Request $request, UserThemePreference $userThemePreference)
    {
        $this->guardUserModel($userThemePreference, $request);

        return response()->json($userThemePreference);
    }

    public function update(Request $request, UserThemePreference $userThemePreference)
    {
        $this->guardUserModel($userThemePreference, $request);

        $data = $request->validate([
            'settings_json' => 'nullable|array',
        ]);

        $userThemePreference->update($data);

        return response()->json($userThemePreference);
    }

    public function destroy(Request $request, UserThemePreference $userThemePreference)
    {
        $this->guardUserModel($userThemePreference, $request);

        $userThemePreference->delete();

        return response()->noContent();
    }
}
