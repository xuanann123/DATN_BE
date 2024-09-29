<?php

use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApprovalCourseController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuestionController;

use App\Http\Controllers\Admin\UploadVideoController;


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
        Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
        #============================== BANNERS DONE =============================
        Route::prefix("banners")
            ->as('banners.')
            ->group(function () {
                Route::get("/", [BannerController::class, 'index'])->name('index');
                Route::get("/create", [BannerController::class, 'create'])->name('create');
                Route::post("/store", [BannerController::class, 'store'])->name('store');
                Route::get("/edit/{banner}", [BannerController::class, 'edit'])->name('edit');
                Route::put("/update/{banner}", [BannerController::class, 'update'])->name('update');
                Route::get("/destroy/{banner}", [BannerController::class, 'destroy'])->name('destroy');
                Route::get('action', [BannerController::class, 'action'])->name('action');
                Route::get('restore/{id}', [BannerController::class, 'restore'])->name('restore');
                Route::get('forceDelete/{id}', [BannerController::class, 'forceDelete'])->name('forceDelete');
            });


        Route::prefix("categories")
            ->as('categories.')
            ->group(function () {
                Route::get("/", [CategoryController::class, 'index'])->name('index');
                Route::get("/create", [CategoryController::class, 'create'])->name('create');
                Route::post("/store", [CategoryController::class, 'store'])->name('store');
                Route::get("/edit/{category}", [CategoryController::class, 'edit'])->name('edit');
                Route::put("/update/{category}", [CategoryController::class, 'update'])->name('update');
                Route::get("/destroy/{category}", [CategoryController::class, 'destroy'])->name('destroy');
                Route::get('action', [CategoryController::class, 'action'])->name('action');
                Route::get('restore/{id}', [CategoryController::class, 'restore'])->name('restore');
                Route::get('forceDelete/{id}', [CategoryController::class, 'forceDelete'])->name('forceDelete');
            });
        #============================== TAGS DONE =============================
        Route::prefix("tags")
            ->as('tags.')
            ->group(function () {
                Route::get("/", [TagController::class, 'index'])->name('index');
                Route::get("/create", [TagController::class, 'create'])->name('create');
                Route::post("/store", [TagController::class, 'store'])->name('store');
                Route::get("/edit/{tag}", [TagController::class, 'edit'])->name('edit');
                Route::put("/update/{tag}", [TagController::class, 'update'])->name('update');
                Route::get("/destroy/{tag}", [TagController::class, 'destroy'])->name('destroy');
                Route::get('action', [TagController::class, 'action'])->name('action');
                Route::get('restore/{id}', [TagController::class, 'restore'])->name('restore');
                Route::get('forceDelete/{id}', [TagController::class, 'forceDelete'])->name('forceDelete');
            });

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
            Route::prefix('profile')
                ->as('profile.')
                ->group(function () {
                    Route::get("/", [ProfileController::class, 'index'])->name('index');
                    Route::get("/edit", [ProfileController::class, 'edit'])->name('edit');
                    Route::post("/update", [ProfileController::class, 'updateInforBasic'])->name('update.basic');
                    Route::post("/update/infor", [ProfileController::class, 'updateInforNormal'])->name('update.normal');
                    Route::post("/update/experience", [ProfileController::class, 'updateExperience'])->name('update.experience');
                    Route::post("/update/password", [ProfileController::class, 'updatePassword'])->name('update.password');
                });
        });
        Route::prefix('courses')
            ->as('courses.')
            ->group(function () {
                Route::get("/", [CourseController::class, 'index'])->name('list');
                Route::get("/create", [CourseController::class, 'create'])->name('create');
                Route::post('/store', [CourseController::class, 'store'])->name('store');
                Route::get("/detail/{id}", [CourseController::class, 'detail'])->name('detail');
                Route::delete('/delete/{id}', [CourseController::class, 'delete'])->name('delete');
                Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [CourseController::class, 'update'])->name('update');
            });
        Route::prefix('modules')
            ->as('modules.')
            ->group(function () {
                Route::post('/store', [ModuleController::class, 'store'])->name('store');
                Route::post('/{id}/add/quiz', [ModuleController::class, 'storeQuiz'])->name('add');
            });
        //Route with quizzes
        Route::prefix('quizzes')
            ->as('quizzes.')
            ->group(function () {

            Route::get('{id}/', [QuizController::class, 'index'])->name('index');
            //Lưu chữ chung dữ liệu vừa là question và cho option của question đó luôn.
            Route::post('/{id}/questions-with-options', [QuestionController::class, 'storeWithOptions'])->name('store');
        });

        Route::prefix('lessons')
            ->as('lessons.')
            ->group(function () {
                Route::post('/store-lesson-text', [LessonController::class, 'store'])->name('store-lesson-text');
                Route::get('/get-lesson-details/{id}', [LessonController::class, 'show'])->name('get-lesson-details');


                Route::get('/auth/youtube', [UploadVideoController::class, 'redirectToGoogle'])->name('youtube.auth');
                Route::get('/callback', [UploadVideoController::class, 'handleGoogleCallback'])->name('youtube.callback');
                Route::post('/store-lesson-video', [UploadVideoController::class, 'storeLessonVideo'])->name('store-lesson-video');
            });
        Route::prefix('approval')
            ->as('approval.')
            ->group(function () {
                Route::get('ratings/list', [RatingController::class, 'index'])->name('ratings.list');
                Route::prefix('courses')
                    ->as('courses.')
                    ->group(function () {
                        Route::get("/", [ApprovalCourseController::class, 'index'])->name('list');
                        Route::get("/detail", [ApprovalCourseController::class, 'show'])->name('detail');
                    });
            });
        // route post
        Route::prefix('posts')
            ->as('posts.')
            ->group(function () {
                Route::get('/trash', [PostController::class, 'trash'])->name('trash');
                Route::post('/{id}/restore', [PostController::class, 'restore'])->name('restore');
                Route::delete('/{id}/force-delete', [PostController::class, 'forceDelete'])->name('forceDelete');
            });
            Route::resource('posts', PostController::class);
    });


Route::get('/callback', [UploadVideoController::class, 'handleGoogleCallback'])->name('youtube.callback');
