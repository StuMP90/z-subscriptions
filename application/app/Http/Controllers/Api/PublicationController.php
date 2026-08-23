<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index()
    {
        return Publication::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:publications,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $publication = Publication::create($validated);

        return response()->json($publication, 201);
    }

    public function update(Request $request, Publication $publication)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:publications,slug,'.$publication->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $publication->fill($validated);
        $publication->save();

        return response()->json($publication);
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();

        return response()->noContent();
    }
}
