<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ApiKeyBasicAuth
{
    public function handle(Request $request, Closure $next, string $type = 'shop')
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Basic ')) {
            return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
        }

        $decoded = base64_decode(substr($header, 6), true);

        if ($decoded === false || ! str_contains($decoded, ':')) {
            return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
        }

        [$username, $password] = explode(':', $decoded, 2);

        $apiKey = ApiKey::where('username', $username)
            ->where('password', $password)
            ->where('active', true)
            ->first();

        if (! $apiKey) {
            return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
        }

        if ($type === 'shop' && ! $apiKey->is_shop) {
            return response('Forbidden', 403);
        }

        if ($type === 'partner' && ! $apiKey->is_partner) {
            return response('Forbidden', 403);
        }

        return $next($request);
    }
}
