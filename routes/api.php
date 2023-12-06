<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthStoreController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CoponController;
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
Route::post('/client/register', [AuthController::class, 'register']);
Route::post('/store/register', [AuthController::class, 'register']);
Route::post('/store/register', [AuthStoreController::class, 'register']);
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/client/login', [AuthController::class, 'login']);
Route::post('/store/login', [AuthController::class, 'login']);
Route::post('/public/upload', [UploadController::class, 'upload']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['middleware' => ['admin']], function () {
        // Authentication
        Route::post('/admin/logout', [AuthController::class, 'logout']);
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
        Route::post('/store/logout', [AuthController::class, 'logout']);
    });
    Route::group(['middleware' => ['client']], function () {
        Route::post('/client/logout', [AuthController::class, 'logout']);
    });
});
