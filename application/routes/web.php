<?php

use Illuminate\Support\Facades\Route;

Route::domain(env('ADMIN_DOMAIN', 'admin.localhost'))->group(base_path('routes/admin.php'));
Route::domain('{shopDomain}')->middleware('resolve.shop')->group(base_path('routes/shop.php'));
