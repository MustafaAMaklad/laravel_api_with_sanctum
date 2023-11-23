<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/client/login', [AuthController::class, 'login']);
Route::post('/store/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['middleware' => ['admin']], function () {
        Route::post('/admin/logout', [AuthController::class, 'logout']);
        Route::put('/admin/account/status', [AdminController::class, 'updateAccountStatus']);
        Route::get('/admin/show/users', [UserController::class, 'index']);
        Route::get('/admin/show/user/{id}', [UserController::class, 'show']);
    });
    Route::group(['middleware' => ['store']], function () {
        Route::post('/store/logout', [AuthController::class, 'logout']);
    });
    Route::group(['middleware' => ['client']], function () {
        Route::post('/client/logout', [AuthController::class, 'logout']);
    });
});
