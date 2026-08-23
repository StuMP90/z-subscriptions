<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicationFrequency;
use Illuminate\Http\Request;

class PublicationFrequencyController extends Controller
{
    public function index(Request $request)
    {
        return PublicationFrequency::where('is_active', true)
            ->orderBy('name')
            ->when($request->input('search'), fn($q, $s) => $q->where('name', 'ilike', "%{$s}%")->orWhere('slug', 'ilike', "%{$s}%"))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publication_frequencies,name',
            'slug' => 'required|string|max:255|unique:publication_frequencies,slug',
            'days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $frequency = PublicationFrequency::create($validated);

        return response()->json($frequency, 201);
    }

    public function update(Request $request, PublicationFrequency $publicationFrequency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publication_frequencies,name,'.$publicationFrequency->id,
            'slug' => 'required|string|max:255|unique:publication_frequencies,slug,'.$publicationFrequency->id,
            'days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $publicationFrequency->fill($validated);
        $publicationFrequency->save();

        return response()->json($publicationFrequency);
    }

}
