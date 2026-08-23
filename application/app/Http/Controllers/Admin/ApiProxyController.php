<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ApiProxyController extends Controller
{
    public function handle(Request $request)
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'regex:/^[a-zA-Z0-9_\-\/]+$/'],
            'method' => ['required', 'string', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
        ]);

        $path = ltrim($validated['path'], '/');
        $method = strtolower($validated['method']);
        $apiHost = env('SHOP_API_DOMAIN', 'api.localhost');
        $url = 'https://'.rtrim($apiHost, '/').'/'.ltrim($path, '/');

        $username = env('SHOP_API_USER');
        $password = env('SHOP_API_PASS');

        if (! $username || ! $password) {
            return response()->json(['message' => 'No shop API key configured'], 500);
        }

        $auth = base64_encode("{$username}:{$password}");

        $headers = [
            'Authorization' => 'Basic '.$auth,
            'Accept' => 'application/json',
        ];

        $contentType = $request->header('Content-Type');
        if ($contentType) {
            $headers['Content-Type'] = $contentType;
        }

        $body = $request->getContent();
        $http = Http::withHeaders($headers)->withOptions(['verify' => false]);

        if ($body !== '' && in_array($method, ['post', 'put', 'patch'])) {
            $http = $http->withBody($body, $contentType ?? 'application/json');
        }

        $response = $http->{$method}($url);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?? 'application/json');
    }
}
