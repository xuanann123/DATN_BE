<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\api\Client\AuthController;
use App\Http\Controllers\api\Client\PostController;
use App\Http\Controllers\api\Client\UserController;
use App\Http\Controllers\api\Client\BannerController;
use App\Http\Controllers\api\Client\FollowController;
use App\Http\Controllers\api\Client\RatingController;
use App\Http\Controllers\api\Client\CommentController;
use App\Http\Controllers\api\Client\PaymentController;
use App\Http\Controllers\api\Client\TeacherController;
use App\Http\Controllers\api\Client\VoucherController;
use App\Http\Controllers\api\Client\CategoryController;
use App\Http\Controllers\api\Client\SocialAuthController;
use App\Http\Controllers\api\Client\CourseDetailController;
use App\Http\Controllers\api\Client\NotificationController;
use App\Http\Controllers\api\Client\Student\NoteController;
use App\Http\Controllers\api\Client\Student\LessonController;

use App\Http\Controllers\api\Client\Intructor\CourseController;
use App\Http\Controllers\api\Client\Intructor\ModuleController;
use App\Http\Controllers\api\Client\Intructor\TargetController;
use App\Http\Controllers\api\Client\Intructor\StatisticController;
use App\Http\Controllers\api\Client\Student\CertificateController;
use App\Http\Controllers\api\Client\Intructor\CurriculumController;
use App\Http\Controllers\api\Client\Intructor\ModuleQuizController;
use App\Http\Controllers\api\Client\Intructor\TextLessonController;
use App\Http\Controllers\api\Client\Intructor\UploadVideoController;
use App\Http\Controllers\api\Client\CourseController as CourseHomePageController;
use App\Http\Controllers\api\Client\GeneralSearchController;
use App\Http\Controllers\api\Client\Intructor\CodingLesson;
use App\Http\Controllers\api\Client\Student\CourseController as StudentCourseController;
use App\Http\Controllers\api\Client\Intructor\LessonController as LessonTeacherController;
use App\Http\Controllers\api\Client\Intructor\PreviewCourseController;
use App\Http\Controllers\api\Client\Intructor\RatingController as IntructorRatingController;
use App\Http\Controllers\api\Client\QnAController;

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
        Route::post('/buy-course/{id_user}/{id_course}', [PaymentController::class, 'buyCourse']);
        Route::post('/register-course/{id_user}/{id_course}', [PaymentController::class, 'registerCourse']);
    });


    # ===================== ROUTE FOR LEARNING PATH ===========================
    Route::prefix('learning-path')->group(function () {

        Route::get('/list-category', [CourseController::class, 'getListCategory']);
        Route::get('/list-course-by-learning-path/{slug}', [CourseController::class, 'getListCourseByLearningPath']);

    });


    # ===================== ROUTE FOR CHECKOUT ===========================
    Route::prefix('payment')->group(function () {
        Route::get('/course/{slug}', [CourseController::class, 'courseCheckout']);
    });

    # ===================== ROUTE FOR CHECKOUT ===========================
    Route::prefix('vouchers')->group(function () {
        Route::get('/apply-coupon/{id_user}/{voucher_code}', [VoucherController::class, 'applyCoupon']);

    });


    # ===================== ROUTE FOR COURSE ===========================
    Route::prefix('courses')->group(function () {
        Route::get('today-new', [CourseHomePageController::class, 'listNewCourseToday']);
        Route::get('favorite', [CourseHomePageController::class, 'listFavoriteCourse']);
        Route::post('favorite/{is_course}', [CourseHomePageController::class, 'favoriteCourse']);
        Route::get('check-favorite/{is_course}', [CourseHomePageController::class, 'checkFavoriteCourse']);
        Route::post('unfavorite/{is_course}', [CourseHomePageController::class, 'unfavoriteCourse']);
        Route::get('check-buy-course/{id_user}/{slug}', [PaymentController::class, 'checkBuyCourse']);
        Route::get('detail/check/{slug}', [CourseDetailController::class, 'courseDetailForAuthUser']);
        Route::post('{course}/update-progress', [StudentCourseController::class, 'updateProgress']);
        Route::get('check-done-course/{slug}', [StudentCourseController::class, 'checkDoneCourse']);
        Route::get('detail-login/{slug}', [CourseDetailController::class, 'courseDetailLogin']);
    });


    # ===================== ROUTE FOR CERTIFICATE ===========================
    Route::prefix('certificates')->group(function () {
        Route::post("{course}/certificate", [CertificateController::class, "storeCertificate"]);
        Route::get("{code}/preview-certificate", [CertificateController::class, "previewCertificate"]);
        Route::get("{code}/download-certificate", [CertificateController::class, "downloadCertificate"]);
    });


    # ===================== ROUTE FOR LESSON ===========================
    Route::prefix('lessons')->group(function () {
        Route::get('/lesson-detail/{lesson}', [LessonController::class, 'lessonDetail']);
        Route::get('/quiz-detail/{quiz}', [LessonController::class, 'quizDetail']);
        Route::put('/lesson-progress/{lesson}', [LessonController::class, 'updateLessonProgress']);
        Route::post('quiz/check-quiz', [LessonController::class, 'checkQuiz']);
        Route::put('quiz/quiz-progress/{quiz}', [LessonController::class, 'updateQuizProgress']);
        Route::get('quiz/result/{userId}/{quizId}', [LessonController::class, 'getQuizResult']);
        Route::get('{lesson}/download-resourse', [LessonController::class, 'downloadResource']);
    });

    # ===================== ROUTE FOR NOTE ===========================
    Route::prefix('notes')->group(function () {
        Route::get('/{course}', [NoteController::class, 'getNotes']);
        Route::get('/{note}/get-lesson', [NoteController::class, 'getLessonByNote']);
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
        Route::get('/check-rating/{id_user}/{id_course}', [RatingController::class, 'checkRating']);
        Route::post('/add-rating-course', [RatingController::class, 'addRating']);
    });


    # ===================== ROUTE FOR USERS ===========================
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::get('/posts', [PostController::class, 'myListPost']);
        Route::get('/posts/{id}', [PostController::class, 'getListPostByUser']);
        Route::get('/balance/{user}', [PaymentController::class, 'balancePurchaseWallet']);
        Route::get('/my-course-bought', [UserController::class, 'myCourseBought']);
        Route::get('/history-buy-course/{id_user}', [PaymentController::class, 'historyBuyCourse']);
        Route::get('/history-transactions/{id_user}', [PaymentController::class, 'historyTransactionsPurchase']);
        Route::post('/follow', [FollowController::class, 'follow']);
        Route::post('/unfollow', [FollowController::class, 'unfollow']);
        Route::get('/check-follow/{id_user}/{id_teacher}', [FollowController::class, 'checkFollow']);
        Route::post('/register-teacher', [UserController::class, 'registerTeacher']);
        Route::get('/check-history-learning', [UserController::class, 'checkLearning']);
        Route::get('vouchers', [VoucherController::class, 'getMyVouchers']);
        //Lấy thông tin user qua email
        Route::get('/by-email/{email}', [UserController::class, 'getUserByEmail']);
        //CHAT AI

        // Route::get('/qna', [QnAController::class, 'index'])->name('qna');
        Route::prefix('qna')->group(function () {
            Route::get('/', [QnAController::class, 'index'])->name('qna');
            Route::post('/ask', [QnAController::class, 'askQuestion']);
        });
        # ===================== ROUTE FOR NOTIFICATION ===========================
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/all-and-unread', [NotificationController::class, 'getUnreadCount']);
            Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
            Route::delete('/{id}/delete', [NotificationController::class, 'delete']);
        });
    });


    # ===================== ROUTE FOR TEACHERS ===========================
    Route::prefix('teacher')->middleware('teacher')->group(function () {
        Route::get('/balance/{user}', [PaymentController::class, 'balanceWithdrawalWallets']);
        Route::post('/add-request-withdraw/{id_user}', [PaymentController::class, 'createCommandWithdrawMoney']);
        Route::get('/history-withdraw/{id_user}', [PaymentController::class, 'historyWithdraw']);
        // Danh sách khóa học
        Route::get('/course', [CourseController::class, 'index']);
        Route::get('/course/approved', [CourseController::class, 'getApprovedCourses']);
        Route::post('/course', [CourseController::class, 'storeNewCourse']);
        Route::get('/course/{course}/preview', [PreviewCourseController::class, 'index']);


        # ===================== ROUTE FOR TEACHERS MANAGE ===========================
        Route::prefix('manage')->group(function () {
            Route::get('/{course}/manage-menu', [TargetController::class, 'checkCourseCompletion']);
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
                Route::put('{course}/update-module-position', [ModuleController::class, 'updateModulePosition']);
            });
            //Quản lý bài học
            Route::prefix('/lesson')->group(function () {
                Route::get('{lesson}/detail', [LessonTeacherController::class, 'lessonDetailTeacher']);
                Route::put('{module}/update-lesson-position', [LessonTeacherController::class, 'updateLessonPosition']);
                // bài học doc
                Route::post('{module}/add-text-lesson', [TextLessonController::class, 'storeTextLesson']);
                Route::put('{lesson}/update-text-lesson', [TextLessonController::class, 'updateTextLesson']);
                Route::delete('{lesson}/delete-text-lesson', [TextLessonController::class, 'destroyTextLesson']);
                // Bài học vid
                Route::post('/upload-video/{module}', [UploadVideoController::class, 'uploadVideo']);
                Route::delete('/delete-lesson-video/{lesson}', [UploadVideoController::class, 'deleteLessonVideo']);
                Route::put('/update-lesson-video/{lesson}', [UploadVideoController::class, 'updateLessonVideo']);
                // Bài học coding
                Route::post('/{module}/add-coding-lesson', [CodingLesson::class, 'store']);
                Route::put('/{lesson}/update-coding-lesson', [CodingLesson::class, 'update']);
                Route::delete('/{lesson}/delete-coding-lesson', [CodingLesson::class, 'destroy']);
                Route::put('/{lesson}/update-coding-content', [CodingLesson::class, 'updateContent']);
                // Đổi loại bài học (doc - vid)
                Route::post('/{lesson}/change-type', [LessonTeacherController::class, 'changeLessonType']);
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
                //import câu hỏi và câu trả lời cho quiz
                Route::post('quiz/{quiz}/import-questions-and-options', [ModuleQuizController::class, 'importQuestionsAndOptions']);
                //Thêm câu hỏi và câu trả lời cho quiz
                Route::post('quiz/{quiz}/add-question-and-option', [ModuleQuizController::class, 'addQuestionAndOption']);
                Route::get('quiz/{question}/show-question-and-option', [ModuleQuizController::class, 'showQuestionAndOption']);
                Route::put('quiz/{question}/update-question-and-option', [ModuleQuizController::class, 'updateQuestionAndOption']);
                Route::delete('quiz/{question}/delete-question-and-option', [ModuleQuizController::class, 'deleteQuestionAndOption']);
            });
            // xoa khoa hoc vinh vien
            Route::delete('{course}/delete-course', [CourseController::class, 'deleteCourse']);
            // an khoa hoc
            Route::put('{course}/disable-course', [CourseController::class, 'disableCourse']);
            // hien thi khoa hoc
            Route::put('{course}/enable-course', [CourseController::class, 'enableCourse']);
            // submit cho admin de xem xet khoa hoc => realtime
            Route::post('{course}/submit', [CourseController::class, 'submit']);
            // Thống kê chung
            Route::prefix('/statistic')->group(function () {
                // Thống kê chung
                Route::get('/', [StatisticController::class, 'index']);
                // Thống kê trong 1 khóa học
                Route::get('/get-students', [StatisticController::class,'getStudents']);
                Route::get('/get-ratings', [StatisticController::class,'getRatings']);
            });
            // tra loi danh gia
            Route::post('/rating/{rating}/reply', [IntructorRatingController::class, 'replyToRating']);
        });
    });

    # ===================== ROUTE FOR TEACHER ===========================
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'getTeachers']);
        Route::get('/list-teacher-month', [TeacherController::class, 'listTeacherMonth']);
    });




    # ===================== ROUTE FOR POSTS ===========================
    Route::prefix('posts')->group(function () {
        Route::post('', [PostController::class, 'store']);
        Route::put('/{slug}', [PostController::class, 'update']);
        Route::delete('/{slug}', [PostController::class, 'destroy']);
        Route::post('/save/{slug}', [PostController::class, 'savePost']);
        Route::post('/unsave/{slug}', [PostController::class, 'unsavePost']);
        Route::get('/saved', [PostController::class, 'getSavedPosts']);
        Route::get('/check-saved/{slug}', [PostController::class, 'checkSavedPost']);
        Route::post('/like/{slug}', [PostController::class, 'likePost']);
        Route::post('/unlike/{slug}', [PostController::class, 'unlikePost']);
        // Route::get('/search', [PostController::class,'search']);
        Route::get('/check-like/{slug}', [PostController::class, 'checkLikePost']);
    });

});

# ===================== ROUTE FOR NOT LOGIN ===========================


# ===================== ROUTE FOR User ===========================
Route::prefix('user')->group(function () {
    Route::get('/{user}/show', [UserController::class, 'showUser']);
});

# ===================== ROUTE FOR GENERAL SEARCH ===========================
Route::prefix('search')->group(function () {
    Route::get('/', [GeneralSearchController::class, 'index']);
    Route::get('/courses', [GeneralSearchController::class, 'searchCourses']);
    Route::get('/teachers', [GeneralSearchController::class, 'searchTeachers']);
    Route::get('/posts', [GeneralSearchController::class, 'searchPosts']);
});

# ===================== ROUTE FOR VOUCHERS ===========================
Route::prefix('vouchers')->group(function () {
    Route::get('/new-voucher', [VoucherController::class, 'newVoucher']);
});

# ===================== ROUTE FOR TRANSACTIONS ===========================
Route::prefix('transactions')->group(function () {
    Route::get('/deposit', [PaymentController::class, 'depositController']);
});


# ===================== ROUTE FOR BANNERS ===========================
Route::get('/banners', [BannerController::class, 'getBanners']);

# ===================== ROUTE FOR CATEGORIES POST ===========================
Route::prefix('categories')->group(function () {
    Route::get('/has-posts', [CategoryController::class, 'getCatHasPosts']);
    Route::get('/name', [CategoryController::class, 'getNameCategories']);
});

# ===================== ROUTE FOR POSTS ===========================
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'getPosts']);
    Route::get('/by-category-posts/{slug}', [PostController::class, 'getPostsByCategory']);
    Route::get('/detail/{slug}', [PostController::class, 'show']);
});
Route::get('/post-outstanding', [PostController::class, 'listPostOutstanding']);

# ===================== ROUTE FOR TEACHER ===========================
Route::prefix('teachers')->group(function () {
    Route::get('/list-courses/{id}', [TeacherController::class, 'getCoursesTeacher']);
    Route::get('/search-teacher', [TeacherController::class, 'searchTeachers']);
});


# ===================== ROUTE FOR COURSE ===========================
Route::prefix('courses')->group(function () {
    // Route::get('/search-course', [CourseController::class, 'searchCourses']);
    Route::get('detail-no-login/{slug}', [CourseDetailController::class, 'courseDetailNoLogin']);
    Route::get('detail/quiz/{slug}', [CourseDetailController::class, 'courseQuizDetail']);

    Route::get('new-course', [CourseHomePageController::class, 'listNewCourse']);

    Route::get('sale-course', [CourseHomePageController::class, 'listCourseSale']);
    //Khoá học nổi bật
    Route::get('popular-course', [CourseHomePageController::class, 'listCoursePopular']);
    //Khoá học miễn phí
    Route::get('free-course', [CourseHomePageController::class, 'listCourseFree']);
    Route::get('category-course', [CourseHomePageController::class, 'getAllCourseByCategory']);
    Route::get('related-course/{slug}', action: [CourseDetailController::class, 'listCourseRelated']);
    //Lấy danh sách tất cả khoá học có phân trang
    Route::get('list-course-all', [CourseHomePageController::class, 'listCourseAll']);
});


# ===================== ROUTE FOR LESSON ===========================
// Route::prefix('lessons')->group(function () {
// Route::get('/lesson-detail/{id}', [LessonController::class, 'lessonDetail']);
// });

# ===================== ROUTE FOR COMMENT ===========================
Route::prefix('comments')->group(function () {
    Route::get('/comment-post/{slug}', [CommentController::class, 'getCommentsPost']);
    Route::get('/count-comment-post/{slug}', [CommentController::class, 'countCommentPost']);
});

# ===================== ROUTE FOR RATING ===========================
Route::prefix('ratings')->group(function () {
    Route::get('/rating-course/{course_id}', [RatingController::class, 'getRating']);
    Route::get('/rating-home-page', [RatingController::class, 'getRatingHomePage']);
});



