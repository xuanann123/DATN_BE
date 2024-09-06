<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

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
Route::prefix("admin")
    ->as(".admin")
    ->group(function () {
        Route::get("/", [DashboardController::class, "index"])->name("dashboard");
        Route::resource('banners', BannerController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
    });
