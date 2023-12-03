<?php

use App\Http\Controllers\Web\AdminController;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use App\Models\Store;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/dashboard/clients', [AdminController::class, 'clientDashboard']);
Route::get('/admin/dashboard/stores', [AdminController::class, 'storeDashboard']);
