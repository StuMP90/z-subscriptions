<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ResolveShop
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost();

        $shopDomain = DB::table('shop_domains')
            ->where('domain', $domain)
            ->where('is_active', true)
            ->first();

        $request->attributes->set('current_domain', $domain);
        $request->attributes->set('current_shop_id', $shopDomain?->shop_id);

        return $next($request);
    }
}
