<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\UserController;
use App\Models\Currency;
use App\Models\GlobalRegion;
use App\Models\ProductType;
use App\Models\ProductVariant;
use App\Models\SubscriptionFrequency;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Internal API']);
});

Route::middleware(['web', 'auth'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('shops', ShopController::class);
        Route::apiResource('publications', PublicationController::class);
        Route::apiResource('issues', IssueController::class);
        Route::apiResource('settings', SettingController::class);

        Route::get('/currencies', function () {
            return Currency::all();
        });
        Route::get('/global-regions', function () {
            return GlobalRegion::all();
        });
        Route::get('/subscription-frequencies', function () {
            return SubscriptionFrequency::all();
        });
        Route::get('/product-variants', function () {
            return ProductVariant::all();
        });
        Route::get('/product-types', function () {
            return ProductType::all();
        });
        Route::get('/defaults', function () {
            $productTypeId = (int) (DB::table('settings')
                ->where('shop_id', null)
                ->where('group', 'general')
                ->where('key', 'Default Product Type')
                ->value('value') ?? 1);

            $regionId = (int) (DB::table('settings')
                ->where('shop_id', null)
                ->where('group', 'general')
                ->where('key', 'Default Availability Region')
                ->value('value') ?? 5);

            $currency = Currency::where('is_base_currency', true)->first() ?? Currency::first();
            $currencyId = $currency?->id;

            return response()->json([
                'product_type_id' => $productTypeId,
                'global_region_ids' => $regionId ? [$regionId] : [],
                'currency_id' => $currencyId,
                'default_currency_id' => $currencyId,
            ]);
        });

        Route::apiResource('offers', OfferController::class);
        Route::apiResource('orders', OrderController::class);
    });
