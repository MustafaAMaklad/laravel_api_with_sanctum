<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthStoreController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['language']] , function () {
    // Public routes
    Route::post('/public/upload', [UploadController::class, 'upload']);
    Route::post('/products/show', [ProductController::class, 'show']);
    Route::post('/product/show', [ProductController::class, 'showDetails']);
    Route::post('/coupon/check', [CouponController::class, 'check']);
    // Auth
    Route::post('/store/auth/register', [AuthStoreController::class, 'register']);
    Route::post('/store/auth/login', [AuthStoreController::class, 'login']);
    Route::post('/client/auth/register', [AuthController::class, 'register']);
    Route::post('/client/auth/login', [AuthController::class, 'login']);
    Route::post('/admin/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::group(['middleware' => ['admin']], function () {
            // Authentication
            Route::post('/admin/auth/logout', [AuthController::class, 'logout']);
            // User Account
            Route::put('/admin/account/status', [AdminController::class, 'updateAccountStatus']);
            Route::get('/admin/show/users', [AdminController::class, 'showUsers']);
            // Coupons
            Route::get('/admin/coupon/show', [CouponController::class, 'show']);
            Route::post('/admin/coupon/create', [CouponController::class, 'store']);
            Route::post('/admin/coupon/update', [CouponController::class, 'update']);
            Route::delete('/admin/coupon/delete', [CouponController::class, 'destroy']);
            // Categories
            Route::get('/admin/category/index', [CategoryController::class, 'index']);
            Route::get('/admin/category/show', [CategoryController::class, 'show']);
            Route::post('/admin/category/create', [CategoryController::class, 'store']);
            Route::post('/admin/category/update', [CategoryController::class, 'update']);
            Route::delete('/admin/category/delete', [CategoryController::class, 'destroy']);
            // Products
            Route::get('/admin/products/show', [ProductController::class, 'index']);
        });
        Route::group(['middleware' => ['store']], function () {
            // Authentication
            Route::post('/store/auth/logout', [AuthStoreController::class, 'logout']);
            // Product

                Route::post('/store/product/create', [ProductController::class, 'store']);
                Route::get('/store/products/show', [ProductController::class, 'showForStore']);
                Route::post('/store/product/update', [ProductController::class, 'update']);
                Route::delete('/store/product/delete', [ProductController::class, 'destroy']);

            // Order
            Route::post('/order/manage', [OrderController::class, 'manage']);
        });
        Route::group(['middleware' => ['client']], function () {
            // Authentication
            Route::post('/client/auth/logout', [AuthController::class, 'logout']);
            // Cart
            Route::post('/cart/add', [CartController::class, 'addToCart']);
            Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
            Route::post('/cart/remove/all', [CartController::class, 'removeAllFromCart']);
            Route::post('/cart/update', [CartController::class, 'updateInCart']);
            Route::get('/cart/show', [CartController::class, 'showCart']);
            // Wishlist
            Route::get('/wishlist/show', [WishlistController::class, 'show']);
            Route::post('/wishlist/update', [WishlistController::class, 'wish']);
            // Order
            Route::post('/order/make', [OrderController::class, 'make']);
            Route::post('/order/finish', [OrderController::class, 'finish']);
        });
    });
});
