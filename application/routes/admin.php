<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IssueController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');

    Route::resource('users', UserController::class)->only(['index']);
    Route::resource('shops', ShopController::class)->only(['index']);
    Route::resource('products', ProductController::class)->only(['index']);
    Route::resource('offers', OfferController::class)->only(['index']);
    Route::resource('publications', PublicationController::class)->only(['index']);
    Route::resource('issues', IssueController::class)->only(['index']);
    Route::resource('customers', CustomerController::class)->only(['index']);
    Route::resource('orders', OrderController::class)->only(['index']);
    Route::resource('settings', SettingController::class)->only(['index']);
});
