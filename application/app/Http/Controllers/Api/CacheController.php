<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CacheController extends Controller
{
    private function cacheClient()
    {
        return Redis::connection('cache')->client();
    }

    private function cachePrefix(): string
    {
        return config('cache.prefix', 'laravel_cache_');
    }

    private function redisPrefix(): string
    {
        $client = $this->cacheClient();
        $prefix = $client->getOption(\Redis::OPT_PREFIX);

        return is_string($prefix) ? $prefix : '';
    }

    private function actualKey(string $fullKey): string
    {
        $prefix = $this->redisPrefix();

        if ($prefix !== '' && str_starts_with($fullKey, $prefix)) {
            return substr($fullKey, strlen($prefix));
        }

        return $fullKey;
    }

    public function index()
    {
        $client = $this->cacheClient();
        $cachePrefix = $this->cachePrefix();

        $pattern = "*{$cachePrefix}*";
        $keys = $client->keys($pattern);
        sort($keys);

        $items = [];
        foreach ($keys as $fullKey) {
            $actual = $this->actualKey($fullKey);
            $ttl = (int) $client->ttl($actual);
            $size = (int) $client->strlen($actual);

            $items[] = [
                'key' => $fullKey,
                'ttl' => $ttl,
                'size' => $size,
            ];
        }

        return response()->json($items);
    }

    public function destroy(Request $request)
    {
        $fullKey = $request->input('key');

        if (! $fullKey) {
            return response()->json(['deleted' => 0]);
        }

        $client = $this->cacheClient();
        $client->del($this->actualKey($fullKey));

        return response()->json(['deleted' => 1]);
    }

    public function clear()
    {
        $client = $this->cacheClient();
        $cachePrefix = $this->cachePrefix();

        $pattern = "*{$cachePrefix}*";
        $keys = $client->keys($pattern);

        $deleted = 0;
        foreach ($keys as $fullKey) {
            $client->del($this->actualKey($fullKey));
            $deleted++;
        }

        return response()->json(['deleted' => $deleted]);
    }
}
