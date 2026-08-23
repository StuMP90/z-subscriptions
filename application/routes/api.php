<?php

use Illuminate\Support\Facades\Route;

Route::domain(env('SHOP_API_DOMAIN', 'api.localhost'))->group(base_path('routes/internal-api.php'));
Route::domain(env('PARTNER_API_DOMAIN', 'partner.localhost'))->group(base_path('routes/partner.php'));
