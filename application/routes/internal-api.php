<?php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CountyStateController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\GlobalRegionController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\PublicationFrequencyController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\UserController;
use App\Models\Currency;
use App\Models\GlobalRegion;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Internal API']);
});

Route::middleware(['web', 'auth'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::apiResource('users', UserController::class)->except('destroy');
        Route::apiResource('customers', CustomerController::class)->except('destroy');
        Route::apiResource('products', ProductController::class)->except('destroy');
        Route::apiResource('shops', ShopController::class)->except('destroy');
        Route::apiResource('publications', PublicationController::class)->except('destroy');
        Route::apiResource('publication-frequencies', PublicationFrequencyController::class)->except('destroy');
        Route::apiResource('issues', IssueController::class)->except('destroy');
        Route::apiResource('settings', SettingController::class)->except('destroy');
        Route::apiResource('global-regions', GlobalRegionController::class)->except('destroy');
        Route::apiResource('countries', CountryController::class)->except('destroy');
        Route::apiResource('county-states', CountyStateController::class)->except('destroy');

        Route::get('/currencies', function () {
            return Currency::all();
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

            $dateFormat = DB::table('settings')
                ->where('shop_id', null)
                ->where('group', 'general')
                ->where('key', 'Default Date Display Format')
                ->value('value') ?? 'd/m/Y';

            return response()->json([
                'product_type_id' => $productTypeId,
                'global_region_ids' => $regionId ? [$regionId] : [],
                'currency_id' => $currencyId,
                'default_currency_id' => $currencyId,
                'date_format' => $dateFormat,
            ]);
        });

        Route::apiResource('offers', OfferController::class)->except('destroy');
        Route::apiResource('orders', OrderController::class)->except('destroy');
    });
