<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\api\Client\CourseController;
use App\Http\Controllers\api\Client\AuthController;
use App\Http\Controllers\api\Client\PostController;
use App\Http\Controllers\api\Client\UserController;
use App\Http\Controllers\api\Client\BannerController;
use App\Http\Controllers\api\Client\TeacherController;

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
        Route::prefix('manage')->group(function () {
            Route::put('/{course}/target-student', [CourseController::class, 'updateTargetStudent']);
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

Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'getPosts']);
    Route::get('/{id}', [PostController::class, 'show']);
});

# ===================== ROUTE FOR TEACHER ===========================

Route::prefix('teachers')->group(function () {
    Route::get('/', [TeacherController::class, 'getTeachers']);

    Route::get('/list-courses/{id}', [TeacherController::class, 'getCoursesIsTeacher']);
});


