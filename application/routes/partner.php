<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api_key:partner')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Partner API']);
    });
});
