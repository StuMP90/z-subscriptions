<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'shop_id' => 'integer',
        'cache_seconds' => 'integer',
    ];

    protected const DEFAULT_CACHE_TTL_KEY = 'settings:default-cache-ttl';

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public static function cacheKey(string $key, ?int $shopId = null, string $group = 'general'): string
    {
        return "setting:{$group}:".($shopId ?? 'null').":{$key}";
    }

    public static function getValue(string $key, $default = null, ?int $shopId = null, string $group = 'general')
    {
        $cacheKey = static::cacheKey($key, $shopId, $group);

        if (Cache::store('redis')->has($cacheKey)) {
            return Cache::store('redis')->get($cacheKey);
        }

        $setting = static::where('shop_id', $shopId)
            ->where('group', $group)
            ->where('key', $key)
            ->first(['value', 'cache_seconds']);

        if (! $setting) {
            return $default;
        }

        $ttl = $setting->cache_seconds ?? static::defaultCacheTtl();

        if ($ttl > 0) {
            Cache::store('redis')->put($cacheKey, $setting->value, $ttl);
        }

        return $setting->value;
    }

    public static function defaultCacheTtl(): int
    {
        return (int) Cache::store('redis')->remember(static::DEFAULT_CACHE_TTL_KEY, 60, function () {
            return static::where('shop_id', null)
                ->where('group', 'general')
                ->where('key', 'Default Setting Cache Time')
                ->value('value') ?? 300;
        });
    }

    public static function clearCache(string $key, ?int $shopId = null, string $group = 'general'): void
    {
        Cache::store('redis')->forget(static::cacheKey($key, $shopId, $group));
    }

    public static function flushDefaultCacheTtl(): void
    {
        Cache::store('redis')->forget(static::DEFAULT_CACHE_TTL_KEY);
    }
}
