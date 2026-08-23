<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CountyState;
use Illuminate\Http\Request;

class CountyStateController extends Controller
{
    public function index(Request $request)
    {
        return CountyState::with('country')->orderBy('name')->when($request->input('search'), fn($q, $s) => $q->where('name', 'ilike', "%{$s}%"))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $countyState = CountyState::create($validated);

        return response()->json($countyState, 201);
    }

    public function update(Request $request, CountyState $countyState)
    {
        $validated = $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $countyState->fill($validated);
        $countyState->save();

        return response()->json($countyState);
    }

}
