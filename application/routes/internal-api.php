<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
        Route::apiResource('offers', OfferController::class);
        Route::apiResource('orders', OrderController::class);
    });
