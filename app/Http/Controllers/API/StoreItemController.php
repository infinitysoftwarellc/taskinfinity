<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreItemController extends Controller
{
    public function index(Request $request)
    {
        $query = StoreItem::query()->orderBy('name');

        if ($request->filled('theme_code')) {
            $query->where('theme_code', $request->input('theme_code'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'theme_code' => ['required', Rule::in(['default', 'gamer', 'forest'])],
            'type' => ['required', Rule::in(['sound', 'icon', 'background', 'skin'])],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'asset_path' => 'required|string',
            'cost_xp' => 'nullable|integer',
            'cost_coins' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item = StoreItem::create($data);

        return response()->json($item, 201);
    }

    public function show(StoreItem $storeItem)
    {
        return response()->json($storeItem);
    }

    public function update(Request $request, StoreItem $storeItem)
    {
        $data = $request->validate([
            'theme_code' => ['sometimes', Rule::in(['default', 'gamer', 'forest'])],
            'type' => ['sometimes', Rule::in(['sound', 'icon', 'background', 'skin'])],
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'asset_path' => 'sometimes|string',
            'cost_xp' => 'nullable|integer',
            'cost_coins' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $storeItem->update($data);

        return response()->json($storeItem);
    }

    public function destroy(StoreItem $storeItem)
    {
        $storeItem->delete();

        return response()->noContent();
    }
}
