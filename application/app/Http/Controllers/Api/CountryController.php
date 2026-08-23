<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        return Country::with('globalRegion')->orderBy('name')->when($request->input('search'), fn($q, $s) => $q->where('name', 'ilike', "%{$s}%"))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'global_region_id' => 'required|integer|exists:global_regions,id',
            'name' => 'required|string|max:255',
            'iso2' => 'required|string|size:2|unique:countries,iso2',
            'iso3' => 'nullable|string|size:3|unique:countries,iso3',
            'dial_code' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country = Country::create($validated);

        return response()->json($country, 201);
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'global_region_id' => 'required|integer|exists:global_regions,id',
            'name' => 'required|string|max:255',
            'iso2' => 'required|string|size:2|unique:countries,iso2,'.$country->id,
            'iso3' => 'nullable|string|size:3|unique:countries,iso3,'.$country->id,
            'dial_code' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country->fill($validated);
        $country->save();

        return response()->json($country);
    }

}
