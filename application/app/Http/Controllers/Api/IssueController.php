<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        return Issue::with('publication')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'publication_id' => 'required|integer|exists:publications,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:issues,slug',
            'issue_number' => 'required|integer',
            'publication_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $issue = Issue::create($validated);

        return response()->json($issue, 201);
    }

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'publication_id' => 'required|integer|exists:publications,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:issues,slug,'.$issue->id,
            'issue_number' => 'required|integer',
            'publication_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $issue->fill($validated);
        $issue->save();

        return response()->json($issue);
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();

        return response()->noContent();
    }
}
