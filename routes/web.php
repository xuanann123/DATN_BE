<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VoucherController;
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

Route::get('admin/login', [AdminController::class, 'index'])->name('admin.login.index');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::prefix("admin")
    ->middleware('admin')
    ->as(".admin")
    ->group(function () {
        Route::get("/", [DashboardController::class, "index"])->name("dashboard");
        Route::get('logout', [AdminController::class, 'logout'])->name('.logout');
        Route::resource('banners', BannerController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('vouchers', VoucherController::class)->except('show');
    });
