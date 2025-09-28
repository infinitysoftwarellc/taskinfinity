<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = InventoryItem::where('user_id', $request->user()->id)
            ->with('item')
            ->orderByDesc('updated_at');

        if ($request->filled('is_equipped')) {
            $query->where('is_equipped', filter_var($request->input('is_equipped'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_item_id' => ['required', 'integer', Rule::exists('store_items', 'id')],
            'is_equipped' => 'nullable|boolean',
            'equipped_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $inventoryItem = InventoryItem::create($data);

        return response()->json($inventoryItem, 201);
    }

    public function show(Request $request, InventoryItem $inventory)
    {
        $this->guardUserModel($inventory, $request);

        return response()->json($inventory->load('item'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $this->guardUserModel($inventory, $request);

        $data = $request->validate([
            'is_equipped' => 'nullable|boolean',
            'equipped_at' => 'nullable|date',
        ]);

        $inventory->update($data);

        return response()->json($inventory);
    }

    public function destroy(Request $request, InventoryItem $inventory)
    {
        $this->guardUserModel($inventory, $request);

        $inventory->delete();

        return response()->noContent();
    }
}
