<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
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
    ->as("admin.")
    ->group(function () {
        Route::get("/", [DashboardController::class, "index"])->name("dashboard");
        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
        Route::resource('banners', BannerController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('vouchers', VoucherController::class)->except('show');
        //Về phần user thì sao nhỉ
        Route::prefix('users')
            ->as('users.')
            ->group(function () {
            Route::get("/", [UserController::class, 'index'])->name('list');
            Route::get("/create", [UserController::class, 'create'])->name('create');
            Route::post("/store", [UserController::class, 'store'])->name('store');
            Route::get("/destroy/{user}", [UserController::class, 'destroy'])->name('destroy');
            Route::get("/action", [UserController::class, 'action'])->name('action');
            Route::get("/edit/{user}", [UserController::class, 'edit'])->name('edit');
            Route::put("/update/{user}", [UserController::class, 'update'])->name('update');
            Route::get("/detail/{user}", [UserController::class, 'detail'])->name('detail');
            Route::get("/restore/{id}", [UserController::class, 'restore'])->name('restore');
            Route::get("/forceDelete/{id}", [UserController::class, 'forceDelete'])->name('forceDelete');
        });
        Route::prefix('courses')
            ->as('courses.')
            ->group(function () {
            Route::get("/", [CourseController::class, 'index'])->name('list');
            Route::get("/create", [CourseController::class, 'create'])->name('create');
            Route::get("/detail", [CourseController::class, 'detail'])->name('detail'); // cái này ae sửa lại cho ok
        });
    });
