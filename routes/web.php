<?php

use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TransactionController;
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
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuestionController;

use App\Http\Controllers\Admin\UploadVideoController;
use App\Http\Controllers\Admin\FollowController;

use App\Http\Controllers\api\Client\PaymentController;


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
    //    return view('welcome');
    if (!auth()->check()) {
        return redirect()->route('admin.login');
    }
    return redirect()->route('admin.dashboard');
});

Route::get('admin/login', [AdminController::class, 'index'])->name('admin.login.index');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::prefix("admin")
    ->middleware('admin')
    ->as("admin.")
    ->group(function () {
        Route::get("/", [DashboardController::class, "index"])->name("dashboard");
        Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

        #============================== NOTIFICATIONS DONE =============================
        Route::prefix("notifications")
            ->as('notifications.')
            ->group(function () {
                Route::get("/", [NotificationController::class, 'index'])->name('index');
                Route::get("/unread-count", [NotificationController::class, 'getUnreadCount'])->name('getUnreadCount');
                Route::post("/{id}/mark-as-read", [NotificationController::class, 'markAsRead'])->name('markAsRead');
            });
        #============================== BANNERS DONE =============================
        Route::prefix("banners")
            ->as('banners.')
            ->group(function () {

                Route::get("/", [BannerController::class, 'index'])->name('index');
                Route::get("show", [BannerController::class, 'show'])->name('show');
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
                Route::put("/change-password/{user}", [UserController::class, 'changePassword'])->name('change-password');
//                Route::get("/detail/{user}", [UserController::class, 'detail'])->name('detail');
                Route::get("/restore/{id}", [UserController::class, 'restore'])->name('restore');
                Route::get("/forceDelete/{id}", [UserController::class, 'forceDelete'])->name('forceDelete');
                Route::get("/list-teachers", [UserController::class, 'listTeachers'])->name('list-teachers');
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
                Route::get("/user-rating/{id}", [CourseController::class, 'getUserDetails']);


                Route::get("/create", [CourseController::class, 'create'])->name('create');
                Route::post('/store', [CourseController::class, 'store'])->name('store');
                //Đẩy sang tabs thứ 2 view target
                Route::get('/add-target/{id}', [CourseController::class, 'addTargetCourse'])->name('new');
                //Lưu trữ mục tiêu target
                Route::post('/store-target/{id}', [CourseController::class, 'storeTargetCourse'])->name('target.store');


                Route::get("/detail/{id}", [CourseController::class, 'detail'])->name('detail');
                Route::delete('/delete/{id}', [CourseController::class, 'delete'])->name('delete');
                Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');
                //Cập nhật dữ liệu đang có của khoá học đó....
                Route::put('/update/{id}', [CourseController::class, 'update'])->name('update');
                Route::post('/submit/{id}', [CourseController::class, 'submit'])->name('submit');
            });
        Route::prefix('modules')
            ->as('modules.')
            ->group(function () {
                Route::post('/store', [ModuleController::class, 'store'])->name('store');
                //Lưu trữ quiz trong module
                Route::post('/{id}/add/quiz', [ModuleController::class, 'storeQuiz'])->name('add');
            });
        //Route with quizzes
        Route::prefix('quizzes')
            ->as('quizzes.')
            ->group(function () {
                Route::get('{id}/', [QuizController::class, 'index'])->name('index');
                //Lưu chữ chung dữ liệu vừa là question và cho option của question đó luôn.
                Route::post('/{id}/questions-with-options', [QuestionController::class, 'storeWithOptions'])->name('store');
                Route::get('/get-quiz/{id}', [QuestionController::class, 'show'])->name('get-quiz');
            });

        Route::prefix('lessons')
            ->as('lessons.')
            ->group(function () {
                Route::post('/store-lesson-text', [LessonController::class, 'store'])->name('store-lesson-text');
                Route::get('/get-lesson-details/{id}', [LessonController::class, 'show'])->name('get-lesson-details');

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
                        Route::get("/action", [ApprovalCourseController::class, 'action'])->name('action');
                        Route::get("/{id}", [ApprovalCourseController::class, 'show'])->name('detail');
                        Route::post("/{id}/approve", [ApprovalCourseController::class, 'approve'])->name('approve');
                    });
            });

        // Route transactions;
        Route::prefix('transactions')
            ->as('transactions.')
            ->group(function () {
                Route::get('/history-buy-course', [TransactionController::class, 'historyBuyCourse'])->name('history-buy-course');
                Route::get('/detail-bill-course/{bill}', [TransactionController::class, 'detailBillCourse'])->name('detail-bill-course');
                Route::get('/history-deposit', [TransactionController::class, 'historyDeposit'])->name('history-deposit');
                Route::get('/history-withdraw', [TransactionController::class, 'historyWithdraw'])->name('history-withdraw');
                Route::get('/withdraw-money', [TransactionController::class, 'withdrawMoneys'])->name('withdraw-money');
                Route::get('/get-status-request-money/{id}', [TransactionController::class, 'getStatusRequestMoney'])->name('status-request-money');
                Route::put('/update-status-request-money', [TransactionController::class, 'updateStatusRequest'])->name('update-status-request-money');
            });
        // route post
        Route::prefix('posts')
            ->as('posts.')
            ->group(function () {
                Route::post('/{id}/disable', [PostController::class, 'disable'])->name('disable');
                Route::post('/{id}/enable', [PostController::class, 'enable'])->name('enable');
                Route::get('/trash', [PostController::class, 'trash'])->name('trash');
                Route::post('/{id}/restore', [PostController::class, 'restore'])->name('restore');
                Route::delete('/{id}/force-delete', [PostController::class, 'forceDelete'])->name('forceDelete');
            });
        Route::resource('posts', PostController::class);
        Route::prefix('chat')
            ->as('chat.')
            ->group(function () {
                Route::get('/', [ChatController::class, 'index'])->name('index');
                Route::post('/message', [ChatController::class, 'messageReceived'])->name('message');
                //auto load tin nhắn
                Route::get('/api', [ChatController::class, 'fetchMessages'])->name('api');
                Route::post('/greet/{receiver}', [ChatController::class, 'greetReceived'])->name('greet');
            });

        Route::prefix('follow')
            ->as('follow.')
            ->group(function () {
                Route::post('/add-follow', [FollowController::class, 'follow'])->name('add-follow');
                Route::delete('/un-follow', [FollowController::class, 'unFollow'])->name('un-follow');
            });
    });
