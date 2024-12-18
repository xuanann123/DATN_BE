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
use App\Http\Controllers\Admin\ApprovalTeacherController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ChartController;

use App\Http\Controllers\Admin\UploadVideoController;
use App\Http\Controllers\Admin\FollowController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QnAController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\api\Client\PaymentController;
use App\Models\Permission;

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
        #============================== CERTIFICATE DONE =============================
        Route::prefix("certificates")
            ->as('certificates.')
            ->group(function () {
            Route::get("/", [CertificateController::class, 'index'])->name('index')->can('certificate.read');
            Route::get("/preview/{template}", [CertificateController::class, 'show'])->name('show')->can('certificate.read');
            Route::post('/select', [CertificateController::class, 'select'])->name('select')->can('certificate.update');
        });
        #============================== BANNERS DONE =============================
        Route::prefix("banners")
            ->as('banners.')
            ->group(function () {
            Route::get("/", [BannerController::class, 'index'])->name('index')->can('banner.read');
            Route::get("show", [BannerController::class, 'show'])->name('show')->can('banner.read');
            Route::get("/create", [BannerController::class, 'create'])->name('create')->can('banner.create');
            Route::post("/store", [BannerController::class, 'store'])->name('store')->can('banner.create');
            Route::get("/edit/{banner}", [BannerController::class, 'edit'])->name('edit')->can('banner.update');
            Route::put("/update/{banner}", [BannerController::class, 'update'])->name('update')->can('banner.update');
            Route::get("/destroy/{banner}", [BannerController::class, 'destroy'])->name('destroy')->can('banner.delete');
            Route::get('action', [BannerController::class, 'action'])->name('action')->can('banner.update');
            Route::get('restore/{id}', [BannerController::class, 'restore'])->name('restore')->can('banner.update');
            Route::get('forceDelete/{id}', [BannerController::class, 'forceDelete'])->name('forceDelete')->can('banner.update');
        });


        Route::prefix("categories")
            ->as('categories.')
            ->group(function () {
                Route::get("/", [CategoryController::class, 'index'])->name('index')->can('category.read');
                Route::get("/create", [CategoryController::class, 'create'])->name('create')->can('category.create');
                Route::post("/store", [CategoryController::class, 'store'])->name('store')->can('category.create');
                Route::get("/edit/{category}", [CategoryController::class, 'edit'])->name('edit')->can('category.update');
                Route::put("/update/{category}", [CategoryController::class, 'update'])->name('update')->can('category.update');
                Route::get("/destroy/{category}", [CategoryController::class, 'destroy'])->name('destroy')->can('category.delete');
                Route::get('action', [CategoryController::class, 'action'])->name('action')->can('category.update');
                Route::get('restore/{id}', [CategoryController::class, 'restore'])->name('restore')->can('category.update');
                Route::get('forceDelete/{id}', [CategoryController::class, 'forceDelete'])->name('forceDelete')->can('category.update');
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

        // Route::resource('vouchers', VoucherController::class)->except('show');
        Route::prefix("vouchers")
            ->as('vouchers.')
            ->group(function () {
            Route::get('', [VoucherController::class, 'index'])->name('index')->can('voucher.read');
            Route::get('/create', [VoucherController::class, 'create'])->name('create')->can('voucher.create');
            Route::post('', [VoucherController::class, 'store'])->name('store')->can('voucher.create');
            Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit')->can('voucher.update');
            Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update')->can('voucher.update');
            Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy')->can('voucher.delete');
        });
        // User
        Route::prefix('users')
            ->as('users.')
            ->group(function () {
            Route::get("/", [UserController::class, 'index'])->name('list')->can('user.read');
            Route::get("/create", [UserController::class, 'create'])->name('create')->can('user.create');
            Route::post("/store", [UserController::class, 'store'])->name('store')->can('user.create');
            Route::get("/destroy/{user}", [UserController::class, 'destroy'])->name('destroy')->can('user.delete');
            Route::get("/action", [UserController::class, 'action'])->name('action')->can('user.update');
            Route::get("/edit/{user}", [UserController::class, 'edit'])->name('edit')->can('user.update');
            Route::put("/update/{user}", [UserController::class, 'update'])->name('update')->can('user.update');
            Route::put("/change-password/{user}", [UserController::class, 'changePassword'])->name('change-password')->can('user.update');
            Route::get("/detail/{user}", [UserController::class, 'detail'])->name('detail')->can('user.read');
            Route::get("/restore/{id}", [UserController::class, 'restore'])->name('restore')->can('user.update');
            Route::get("/forceDelete/{id}", [UserController::class, 'forceDelete'])->name('forceDelete')->can('user.update');
            Route::get("/list-teachers", [UserController::class, 'listTeachers'])->name('list-teachers')->can('user.read');
            Route::get("/list-admin", [UserController::class, 'listAdmin'])->name('list-admin')->can('user.read');

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
                //Hiển thị dữ liệu khi bấm edit
                Route::get('/edit/{id}', [ModuleController::class, 'edit'])->name('edit');
                //Cập nhât dữ liệu chương học
                Route::post('/update/{id}', [ModuleController::class, 'update'])->name('update');
                //Xoá chương học
                Route::get('/delete/{id}', [ModuleController::class, 'delete'])->name('delete');
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
                        Route::get("/", [ApprovalCourseController::class, 'index'])->name('list')->can('course.approve');
                        Route::get("/course-outstanding", [CourseController::class, 'courseOutstanding'])->name('course-outstanding');
                        Route::put("/outstanding/{id_course}", [CourseController::class, 'outstanding'])->name('outstanding');
                        Route::put("/add-and-remove-outstanding", [CourseController::class, 'handleRemoveAndAddCourseOutstanding'])->name('add-and-remove-outstanding');
                        Route::get("/action", [ApprovalCourseController::class, 'action'])->name('action')->can('course.approve');
                        Route::get("/{id}", [ApprovalCourseController::class, 'show'])->name('detail')->can('course.approve');
                        Route::post("/{id}/approve", [ApprovalCourseController::class, 'approve'])->name('approve')->can('course.approve');
                    });
                Route::prefix('teachers')
                    ->as('teachers.')
                    ->group(function () {
                        Route::get("/", [ApprovalTeacherController::class, 'index'])->name('list')->can('teacher.approve');
                        Route::get("/{id}", [ApprovalTeacherController::class, 'show'])->name('detail')->can('teacher.approve');
                        Route::get("/{id}/approve", [ApprovalTeacherController::class, 'approve'])->name('approve')->can('teacher.approve');
                    });
            });

        // Route transactions;
        Route::prefix('transactions')
            ->as('transactions.')
            ->group(function () {
            Route::get('/history-buy-course', [TransactionController::class, 'historyBuyCourse'])->name('history-buy-course')->can('transaction.read');
            Route::get('/detail-bill-course/{bill}', [TransactionController::class, 'detailBillCourse'])->name('detail-bill-course')->can('transaction.read');
            Route::get('/history-deposit', [TransactionController::class, 'historyDeposit'])->name('history-deposit')->can('transaction.read');
            Route::get('/history-withdraw', [TransactionController::class, 'historyWithdraw'])->name('history-withdraw')->can('transaction.read');
            Route::get('/withdraw-money', [TransactionController::class, 'withdrawMoneys'])->name('withdraw-money')->can('transaction.read');
            Route::get('/get-status-request-money/{id}', [TransactionController::class, 'getStatusRequestMoney'])->name('status-request-money')->can('transaction.read');
            Route::put('/update-status-request-money', [TransactionController::class, 'updateStatusRequest'])->name('update-status-request-money')->can('transaction.update');
        });
        #========================================== ROUTE FOR POSTS =========================================
        Route::prefix('posts')
            ->as('posts.')
            ->group(function () {
            // Routes từ resource
            Route::get('/', [PostController::class, 'index'])->name('index')->can('post.read');
            Route::get('/create', [PostController::class, 'create'])->name('create')->can('post.create');
            Route::post('/', [PostController::class, 'store'])->name('store')->can('post.create');
            Route::get('/{post}', [PostController::class, 'show'])->name('show')->can('post.read');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit')->can('post.update');
            Route::put('/{post}', [PostController::class, 'update'])->name('update')->can('post.update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy')->can('post.delete');

            // Routes bổ sung
            Route::post('/{id}/disable', [PostController::class, 'disable'])->name('disable')->can('post.update');
            Route::post('/{id}/enable', [PostController::class, 'enable'])->name('enable')->can('post.update');
            Route::get('/trash', [PostController::class, 'trash'])->name('trash')->can('post.update');
            Route::post('/{id}/restore', [PostController::class, 'restore'])->name('restore')->can('post.update');
            Route::delete('/{id}/force-delete', [PostController::class, 'forceDelete'])->name('forceDelete')->can('post.update');
        });

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

        Route::prefix('charts')
            ->as('charts.')
            ->group(function () {
                Route::get('/chart-revenue', [ChartController::class, 'chartRevenue'])->name('revenue');
                Route::get('/chart-top-courses', [ChartController::class, 'chartCourses'])->name('top-courses');
            });
        Route::prefix('qna')
            ->group(function () {
                Route::get('', [QnAController::class, 'index'])->name('qna');
                Route::post('/ask', [QnAController::class, 'askQuestion']);
                Route::get('/search', [QnAController::class, 'search']);
                Route::get('/delete-all', [QnAController::class, 'deleteAll'])->name('qna.delete.all');
            });
        # ===================== ROUTE FOR PERMISSION ===========================

        Route::prefix('permissions')
            ->as('permissions.')
            ->group(function () {
            //Danh sách quyền
            Route::get('/', [PermissionController::class, 'index'])->name('index')->can('permission.read');
            //Thêm quyền
                Route::post('/store', [PermissionController::class, 'store'])->name('store')->can('permission.create');
            //Sửa quyền
                Route::get('/edit/{permission}', [PermissionController::class, 'edit'])->name('edit')->can('permission.update');
            //Cập nhật quyền
                Route::post('/update/{permission}', [PermissionController::class, 'update'])->name('update')->can('permission.update');
            //Xoá quyền
                Route::get('/destroy/{permission}', [PermissionController::class, 'destroy'])->name('destroy')->can('permission.delete');
        });
        # ===================== ROUTE FOR ROLE ===========================
        Route::prefix('roles')
            ->as('roles.')
            ->group(function () {
            //Danh sách vai trò
            Route::get('/', [RoleController::class, 'index'])->name('index')->can('role.read');
            //Thêm role
            Route::get('/create', [RoleController::class, 'create'])->name('create')->can('role.create');
            //Lưu trữ
            Route::post('/', [RoleController::class, 'store'])->name('store')->can('role.create');
            //Chỉnh sửa roles
            Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('edit')->can('role.update');
            //Cập nhật
            Route::post('/update/{role}', [RoleController::class, 'update'])->name('update')->can('role.update');
            //Xoá
            Route::get('/destroy/{role}', [RoleController::class, 'destroy'])->name('destroy')->can('role.delete');
        });
    });
