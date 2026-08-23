<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GlobalRegion;
use Illuminate\Http\Request;

class GlobalRegionController extends Controller
{
    public function index()
    {
        return GlobalRegion::orderBy('name')->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:global_regions,code',
            'is_active' => 'boolean',
        ]);

        $globalRegion = GlobalRegion::create($validated);

        return response()->json($globalRegion, 201);
    }

    public function update(Request $request, GlobalRegion $globalRegion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:global_regions,code,'.$globalRegion->id,
            'is_active' => 'boolean',
        ]);

        $globalRegion->fill($validated);
        $globalRegion->save();

        return response()->json($globalRegion);
    }

    public function destroy(GlobalRegion $globalRegion)
    {
        $globalRegion->delete();

        return response()->noContent();
    }
}
