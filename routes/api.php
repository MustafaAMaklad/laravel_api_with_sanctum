<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthStoreController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CoponController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UploadController;
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

// Public routes
Route::post('/public/upload', [UploadController::class, 'upload']);
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
        Route::get('/admin/copon/show', [CoponController::class, 'show']);
        Route::post('/admin/copon/create', [CoponController::class, 'store']);
        Route::post('/admin/copon/update', [CoponController::class, 'update']);
        Route::delete('/admin/copon/delete', [CoponController::class, 'destroy']);
        // Categories
        Route::get('/admin/category/index', [CategoryController::class, 'index']);
        Route::get('/admin/category/show', [CategoryController::class, 'show']);
        Route::post('/admin/category/create', [CategoryController::class, 'store']);
        Route::post('/admin/category/update', [CategoryController::class, 'update']);
        Route::delete('/admin/category/delete', [CategoryController::class, 'destroy']);
    });
    Route::group(['middleware' => ['store']], function () {
        Route::post('/store/auth/logout', [AuthStoreController::class, 'logout']);
        Route::post('/store/product/create', [ProductController::class, 'store']);
    });
    Route::group(['middleware' => ['client']], function () {
        Route::post('/client/auth/logout', [AuthController::class, 'logout']);
        Route::get('/home/products/show', [HomeController::class, 'showProducts']);
    });
});
