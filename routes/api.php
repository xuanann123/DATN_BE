<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\api\Client\AuthController;
use App\Http\Controllers\api\Client\PostController;
use App\Http\Controllers\api\Client\UserController;
use App\Http\Controllers\api\Client\BannerController;
use App\Http\Controllers\api\Client\Intructor\CourseController;
use App\Http\Controllers\api\Client\TeacherController;
use App\Http\Controllers\api\Client\CategoryController;
use App\Http\Controllers\api\Client\Intructor\CurriculumController;
use App\Http\Controllers\api\Client\Intructor\TextLessonController;
use App\Http\Controllers\api\Client\Intructor\ModuleController;
use App\Http\Controllers\api\Client\Intructor\UploadVideoController;

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


Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-otp-resetpassword', [AuthController::class, 'verifyOtpForResetPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

//Xác thực cần đăng nhập để thao tác
Route::middleware('auth:sanctum')->group(function () {

# ===================== ROUTE FOR AUTH ===========================

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

# ===================== ROUTE FOR USERS ===========================

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::get('/posts', [PostController::class, 'myListPost']);
        Route::get('/posts/{id}', [PostController::class, 'getListPostByUser']);

    });

    Route::prefix('teacher')->group(function () {
        Route::post('/course', [CourseController::class, 'storeNewCourse']);
        Route::get('/course/{course}', [CourseController::class, 'showCourseTeacher']);
        Route::prefix('manage')->group(function () {
            Route::get('/{course}/target-student', [CourseController::class, 'getCourseGoals']);
            Route::put('/{course}/target-student', [CourseController::class, 'updateTargetStudent']);
            Route::get('/{course}/overview', [CourseController::class, 'getCourseOverview']);
            Route::put('/{course}/overview', [CourseController::class, 'updateCourseOverview']);
            Route::get('/{course}/curriculum', [CurriculumController::class, 'index']);
            Route::prefix('/module')->group(function () {
                Route::post('{course}/add', [ModuleController::class, 'storeModule']);
                Route::put('{module}/update', [ModuleController::class, 'updateModule']);
                Route::delete('{module}/delete', [ModuleController::class, 'deleteModule']);
            });
            Route::prefix('/lesson')->group(function () {
                Route::post('{module}/add-text-lesson', [TextLessonController::class, 'storeTextLesson']);
                Route::put('{lesson}/update-text-lesson', [TextLessonController::class, 'updateTextLesson']);
                Route::delete('{lesson}/delete-text-lesson', [TextLessonController::class, 'destroyTextLesson']);
            });
        });
    });

    // Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // post
# ===================== ROUTE FOR POSTS ===========================
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store']);
        Route::put('/{post}', [PostController::class, 'update']);
        Route::delete('/{post}', [PostController::class, 'destroy']);
    });
});

//Không cần xác thực => vào trang web có thể xem được luôn
# ===================== ROUTE FOR BANNERS ===========================

Route::get('/banners', [BannerController::class, 'getBanners']);

# ===================== ROUTE FOR POSTS ===========================
Route::prefix('categories')->group(function () {
    Route::get('/name', [CategoryController::class, 'getNameCategories']);
});

# ===================== ROUTE FOR POSTS ===========================

Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'getPosts']);
    Route::get('/{id}', [PostController::class, 'show']);
});

# ===================== ROUTE FOR TEACHER ===========================

Route::prefix('teachers')->group(function () {
    Route::get('/', [TeacherController::class, 'getTeachers']);

    // Danh sách khóa học của một teacher cụ thể
    Route::get('/list-courses/{id}', [TeacherController::class, 'getCoursesTeacher']);

    // Tìm kiếm giảng viên;
    Route::get('/search-teacher', [TeacherController::class, 'searchTeachers']);
});
# ===================== ROUTE FOR COURSE ===========================

Route::prefix('course')->group(function () {
    //
});

# ===================== ROUTE FOR LESSON ===========================
Route::prefix('lessons')->group(function () {
    Route::post('/upload-video', [UploadVideoController::class, 'uploadVideo']);
});
