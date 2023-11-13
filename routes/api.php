<?php

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);

// Route::get('/user/{id}', [UserController::class, 'show']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/logout', [AuthController::class, 'logout'])->middleware('restrict:admin');
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
    Route::get('/admin/users', [UserController::class, 'index'])->middleware('restrict:admin');
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
