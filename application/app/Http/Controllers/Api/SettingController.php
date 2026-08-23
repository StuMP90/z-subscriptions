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
            'cache_seconds' => 'nullable|integer|min:0',
            'type' => 'required|string|max:20|in:string,integer,boolean',
        ]);

        $setting = Setting::create($validated);
        Setting::clearCache($setting->key, $setting->shop_id, $setting->group);

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
            'cache_seconds' => 'nullable|integer|min:0',
            'type' => 'required|string|max:20|in:string,integer,boolean',
        ]);

        $originalKey = $setting->getOriginal('key');
        $originalShopId = $setting->getOriginal('shop_id');
        $originalGroup = $setting->getOriginal('group');

        $setting->fill($validated);
        $setting->save();

        if ($originalKey !== $setting->key || $originalShopId != $setting->shop_id || $originalGroup !== $setting->group) {
            Setting::clearCache($originalKey, (int) $originalShopId, $originalGroup);
        }

        Setting::clearCache($setting->key, $setting->shop_id, $setting->group);
        Setting::getValue($setting->key, null, $setting->shop_id, $setting->group);

        if ($setting->key === 'Default Setting Cache Time') {
            Setting::flushDefaultCacheTtl();
            Setting::defaultCacheTtl();
        }

        return response()->json($setting);
    }

}
