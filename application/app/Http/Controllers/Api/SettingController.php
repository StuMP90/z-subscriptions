<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        return Setting::with('shop')->orderBy('group')->orderBy('key')->when($request->input('search'), fn($q, $s) => $q->where('key', 'ilike', "%{$s}%"))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'nullable|integer|exists:shops,id',
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => [
                'nullable',
                Rule::when($request->input('type') === 'integer', 'integer'),
                Rule::when($request->input('type') === 'boolean', 'boolean'),
            ],
            'type' => 'required|string|max:20|in:string,integer,boolean',
        ]);

        $setting = Setting::create($validated);

        return response()->json($setting, 201);
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'shop_id' => 'nullable|integer|exists:shops,id',
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:255|unique:settings,key,'.$setting->id,
            'value' => [
                'nullable',
                Rule::when($request->input('type') === 'integer', 'integer'),
                Rule::when($request->input('type') === 'boolean', 'boolean'),
            ],
            'type' => 'required|string|max:20|in:string,integer,boolean',
        ]);

        $setting->fill($validated);
        $setting->save();

        return response()->json($setting);
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return response()->noContent();
    }
}
