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
use App\Http\Controllers\api\Client\Intructor\LessonController as LessonTeacherController;
use App\Http\Controllers\api\Client\TeacherController;
use App\Http\Controllers\api\Client\CategoryController;
use App\Http\Controllers\api\Client\Intructor\CurriculumController;
use App\Http\Controllers\api\Client\Intructor\TextLessonController;
use App\Http\Controllers\api\Client\Intructor\ModuleController;
use App\Http\Controllers\api\Client\Intructor\UploadVideoController;
use App\Http\Controllers\api\Client\CourseDetailController;
use App\Http\Controllers\api\Client\PaymentController;
use App\Http\Controllers\api\Client\CourseController as CourseHomePageController;

use App\Http\Controllers\api\Client\Student\LessonController;
use App\Http\Controllers\api\Client\RatingController;
use App\Http\Controllers\api\Client\CommentController;
use App\Http\Controllers\api\Client\Intructor\ModuleQuizController;
use App\Http\Controllers\api\Client\Intructor\TargetController;
use App\Http\Controllers\api\Client\Student\NoteController;

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

    # ===================== ROUTE FOR TRANSACTIONS ===========================

    Route::prefix('transactions')->group(function () {
        Route::post('/payment/{user}', [PaymentController::class, 'paymentController']);
        Route::post('buy-course/{id_user}/{id_course}', [PaymentController::class, 'buyCourse']);
    });

    # ===================== ROUTE FOR CHECKOUT ===========================

    Route::prefix('payment')->group(function () {
        Route::get('/course/{slug}', [CourseController::class, 'courseCheckout']);
    });


    # ===================== ROUTE FOR COURSE ===========================

    Route::prefix('courses')->group(function () {
        // Route::get('/{course}', [CourseDetailController::class, 'courseDetail']);
        // Check xem đã mua khóa học hay chưa;
        Route::get('check-buy-course/{id_user}/{id_course}', [PaymentController::class, 'checkBuyCourse']);
        //Chi tiết bài học khi đăng kí khoá học
        Route::get('detail/check/{slug}', [CourseDetailController::class, 'courseDetailForAuthUser']);
    });

    # ===================== ROUTE FOR LESSON ===========================
    Route::prefix('lessons')->group(function () {
        Route::get('/lesson-detail/{lesson}', [LessonController::class, 'lessonDetail']);
        Route::get('/quiz-detail/{quiz}', [LessonController::class, 'quizDetail']);
        Route::put('/lesson-progress/{lesson}', [LessonController::class, 'updateLessonProgress']);
    });

    # ===================== ROUTE FOR NOTE ===========================
    Route::prefix('notes')->group(function () {
        Route::get('{course}', [NoteController::class, 'getNotes']);
        Route::get('{note}/get-lesson', [NoteController::class, 'getLessonByNote']);
        Route::post('/add-note/{lesson}', [NoteController::class, 'addNote']);
        Route::put('/update-note/{note}', [NoteController::class, 'updateNote']);
        Route::delete('/delete-note/{note}', [NoteController::class, 'deleteNote']);
    });

    # ===================== ROUTE FOR COMMENT ===========================

    Route::prefix('comments')->group(function () {
        Route::post('/add-comment-post', [CommentController::class, 'addCommentPost']);
        Route::get('/comment-lesson/{id_lesson}', [CommentController::class, 'getCommentsLesson']);
        Route::post('/add-comment-lesson', [CommentController::class, 'addCommentLesson']);
    });

    # ===================== ROUTE FOR RATING ===========================
    Route::prefix('ratings')->group(function () {
        Route::get('/add-rating-course', [RatingController::class, 'addRating']);
    });


    # ===================== ROUTE FOR USERS ===========================

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::get('/posts', [PostController::class, 'myListPost']);
        Route::get('/posts/{id}', [PostController::class, 'getListPostByUser']);

        Route::get('/balance/{user}', [PaymentController::class, 'balancePurchaseWallet']);

        //Danh sách khoá học của tôi đã mua
        Route::get('/my-course-bought', [UserController::class, 'myCourseBought']);

        Route::get('/history-buy-course/{id_user}', [PaymentController::class, 'historyBuyCourse']);
        Route::get('/history-transactions/{id_user}', [PaymentController::class, 'historyTransactionsPurchase']);

    });

    Route::prefix('teacher')->middleware('teacher')->group(function () {
        // Ví rút của giảng viên
        Route::get('/balance/{user}', [PaymentController::class, 'balanceWithdrawalWallets']);
        // Danh sách khóa học
        Route::get('/course', [CourseController::class, 'index']);
        //Thêm khoá học mới
        Route::post('/course', [CourseController::class, 'storeNewCourse']);

        // Route::get('/course/{course}', [CourseController::class, 'showCourseTeacher']);

        Route::prefix('manage')->group(function () {
            // Check điều kiện để gửi khóa học đi xem xét
            Route::get('/{course}/manage-menu', [TargetController::class, 'checkCourseCompletion']);
            //Quản lý mục tiêu khóa học
            Route::get('/{course}/target-student', [TargetController::class, 'getCourseGoals']);
            Route::put('/{course}/target-student', [TargetController::class, 'updateTargetStudent']);

            //Quản lý tổng quan khoá học
            Route::get('/{course}/overview', [CourseController::class, 'getCourseOverview']);
            Route::put('/{course}/overview', [CourseController::class, 'updateCourseOverview']);

            //Quản lý chương trình giảng dạy
            Route::get('/{course}/curriculum', [CurriculumController::class, 'index']);
            //Quản lý chương học
            Route::prefix('/module')->group(function () {
                Route::post('{course}/add', [ModuleController::class, 'storeModule']);
                Route::put('{module}/update', [ModuleController::class, 'updateModule']);
                Route::delete('{module}/delete', [ModuleController::class, 'deleteModule']);

            });
            //Quản lý bài học
            Route::prefix('/lesson')->group(function () {
                Route::get('{lesson}/detail', [LessonTeacherController::class, 'lessonDetailTeacher']);
                Route::put('{module}/update-lesson-position', [LessonTeacherController::class, 'updateLessonPosition']);
                Route::post('{module}/add-text-lesson', [TextLessonController::class, 'storeTextLesson']);
                Route::put('{lesson}/update-text-lesson', [TextLessonController::class, 'updateTextLesson']);
                Route::delete('{lesson}/delete-text-lesson', [TextLessonController::class, 'destroyTextLesson']);
                Route::post('/upload-video/{module}', [UploadVideoController::class, 'uploadVideo']);
                Route::delete('/delete-lesson-video/{lesson}', [UploadVideoController::class, 'deleteLessonVideo']);
                Route::put('/update-lesson-video/{lesson}', [UploadVideoController::class, 'updateLessonVideo']);
                # ========================== Route for quiz ===========================
                //ADD QUIZ => Lấy thằng id của module để thêm
                Route::post('{module}/add-quiz', [ModuleQuizController::class, 'addQuiz']);
                //Update QUIZ => Lấy id của quiz
                Route::put('{quiz}/update-quiz', [ModuleQuizController::class, 'updateQuiz']);
                //Xoá QUIZ
                Route::delete('{quiz}/delete-quiz', [ModuleQuizController::class, 'deleteQuiz']);
                # ========================== Route for question and option ===========================
                //Hiển thị danh sách toàn bộ câu hỏi id của module
                Route::get('{id}/show-quiz', [ModuleQuizController::class, 'showQuiz']);
                //Thêm câu hỏi cho quiz
                Route::post('quiz/{quiz}/add-question-and-option', [ModuleQuizController::class, 'addQuestionAndOption']);
                Route::get('quiz/{question}/show-question-and-option', [ModuleQuizController::class, 'showQuestionAndOption']);
                Route::put('quiz/{question}/update-question-and-option', [ModuleQuizController::class, 'updateQuestionAndOption']);
                Route::delete('quiz/{question}/delete-question-and-option', [ModuleQuizController::class, 'deleteQuestionAndOption']);
            });
            // submit cho admin de xem xet khoa hoc
            Route::post('{course}/submit', [CourseController::class, 'submit']);
        });
    });


    // Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // post
# ===================== ROUTE FOR POSTS ===========================
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store']);
        Route::put('/{slug}', [PostController::class, 'update']);
        Route::delete('/{slug}', [PostController::class, 'destroy']);
    });
});

// Redirect vnpay
Route::prefix('transactions')->group(function () {
    Route::get('/deposit', [PaymentController::class, 'depositController']);
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
    Route::get('/{slug}', [PostController::class, 'show']);
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

Route::prefix('courses')->group(function () {
    // Tìm kiếm khoá học
    Route::get('/search-course', [CourseController::class, 'searchCourses']);
    //Chi tiết khoá học đối với người chưa đăng nhập vào hệ thống
    Route::get('detail/{slug}', [CourseDetailController::class, 'courseDetail']);
    Route::get('detail/quiz/{slug}', [CourseDetailController::class, 'courseQuizDetail']);

    // Khóa học mới nhất
    Route::get('new-course', [CourseHomePageController::class, 'listNewCourse']);
    // Khóa học giảm giá
    Route::get('sale-course', [CourseHomePageController::class, 'listCourseSale']);
    // Khóa học nổi bật
    Route::get('popular-course', [CourseHomePageController::class, 'listCoursePopular']);
    Route::get('category-course', [CourseHomePageController::class, 'getAllCourseByCategory']);

});


# ===================== ROUTE FOR LESSON ===========================
Route::prefix('lessons')->group(function () {
    // Route::get('/lesson-detail/{id}', [LessonController::class, 'lessonDetail']);
});

# ===================== ROUTE FOR COMMENT ===========================
Route::prefix('comments')->group(function () {
    Route::get('/comment-post/{slug}', [CommentController::class, 'getCommentsPost']);
});

# ===================== ROUTE FOR RATING ===========================
Route::prefix('ratings')->group(function () {
    Route::get('/rating-course/{course_id}', [RatingController::class, 'getRating']);
    Route::get('/rating-home-page', [RatingController::class, 'getRatingHomePage']);
});








