<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Rating;
use App\Models\Video;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\LessonProgress;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\QuizProgress;
use App\Models\User;
use App\Models\WishList;
use Illuminate\Support\Facades\DB;

class CourseDetailController extends Controller // di ve sinh
{
    //Phần chi tiết bài học chưa đăng nhập
    public function courseDetailNoLogin($slug)
    {
        try {
            //Chi tiết bài học lấy theo slug
            $course = Course::select('id', 'slug', 'name', 'thumbnail', 'price', 'price_sale', 'total_student', 'id_user', 'description', 'level', 'sort_description', 'id_category', 'trailer')
                ->with(['category:id,name', 'user:id,name,avatar', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons', 'modules.quiz'])
                ->withCount('ratings')
                ->where('slug', $slug)
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->first();
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Khóa học không tồn tại.',
                    'data' => []
                ], 204);
            }
            // tên thằng tạo ra khóa học
            $course->author = $course->user->name;

            // Số lượng bài học trong khóa học, sử dụng flatmap để đến từng số lượng bài học trong từng chương học
            $total_lessons = $course->modules->flatMap->lessons->count();
            $total_quiz = $course->modules->whereNotNull('quiz')->count();

            $totalDurationVideo = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                    return $lesson->lessonable->duration ?? 0;
                });
            })->sum();
            //Tính thời gian độc docs
            $totalDurationDocs = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                    $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                    return ceil(($wordCount / 200) * 60);
                });
            })->sum();
            //Tính tổng thời gian của video và docs
            $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;

            //Set giá trị trong khoá học là tổng bài học và tổng số lượng bài học
            $course->total_lessons = $total_lessons + $total_quiz;

            $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);
            $course->total_student = DB::table('user_courses')->where('id_course', operator: $course->id)->count();


            // Trả về dữ liệu bên phía client khi lấy được thành công
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin khóa học.",
                'data' => $course
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra thông tin khóa hoc.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //Phần chi tiết bài học đã đăng nhập
    public function courseDetailLogin($slug)
    {
        try {
            $user = auth()->user();
            //Chi tiết bài học lấy theo slug
            $course = Course::select('id', 'slug', 'name', 'thumbnail', 'price', 'price_sale', 'total_student', 'id_user', 'description', 'level', 'sort_description', 'id_category', 'trailer')
                ->with(['category:id,name', 'user:id,name,avatar', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons', 'modules.quiz'])
                ->withCount('ratings')
                ->where('slug', $slug)
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->first();
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Khóa học không tồn tại.',
                    'data' => []
                ], 204);
            }
            $courseId = $course->id;
            // tên thằng tạo ra khóa học
            $course->author = $course->user->name;

            // Số lượng bài học trong khóa học, sử dụng flatmap để đến từng số lượng bài học trong từng chương học
            $total_lessons = $course->modules->flatMap->lessons->count();
            $total_quiz = $course->modules->whereNotNull('quiz')->count();

            $totalDurationVideo = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                    return $lesson->lessonable->duration ?? 0;
                });
            })->sum();
            //Tính thời gian độc docs
            $totalDurationDocs = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                    $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                    return ceil(($wordCount / 200) * 60);
                });
            })->sum();
            

            //Kiểm tra tiến độ
            $course->progress_percent = UserCourse::where('id_user', $user->id)->where('id_course', $course->id)->first()->progress_percent ?? 0;
            //Tổng sinh viên join khoá học
            $course->total_student = DB::table('user_courses')->where('id_course', operator: $course->id)->count();


            //Kiểm tra xem người dùng đã mua khoá học hay chưa
            $check_buy = UserCourse::where('id_user', $user->id)->where('id_course', $courseId)->exists();
            if ($check_buy) {
                $course->is_course_bought = true;
            } else {
                $course->is_course_bought = false;
            }
            //Tiếp đến check follow giảng viên chưa
            $check_follow = Follow::where('follower_id', $user->id)->where('following_id', $course->user->id)->exists();
            if ($check_follow) {
                $course->is_follow = true;

            } else {
                $course->is_follow = false;
            }
            //Kiểm tra rating chưa

            if ($check_rating = DB::table('ratings')->where('id_user', $user->id)->where('id_course', $courseId)->exists()) {
                $course->is_rating = true;
            } else {
                $course->is_rating = false;
            }
            //Kiểm tra yêu thích khoá học chưa
            if (DB::table('wish_lists')->where('id_user', $user->id)->where('id_course', $course->id)->exists()) {
                $course->is_favorite = true;
            } else {
                $course->is_favorite = false;
            }

            //Set giá trị trong khoá học là tổng bài học và tổng số lượng bài học
            $course->total_lessons = $total_lessons + $total_quiz;
            //Tính tổng thời gian của video và docs
            $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;
      
            $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);

            // Trả về dữ liệu bên phía client khi lấy được thành công
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin khóa học.",
                'data' => $course
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra thông tin khóa hoc.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function courseQuizDetail($slug)
    {
        $course = Course::with(['modules.quiz'])->where('slug', $slug)
            ->where('is_active', 1)
            ->where('status', 'approved')->firstOrFail();
        //Lưu chữ mảng dữ liệu quiz
        $listQuiz = [];
        //Danh sách module
        $listModules = $course->modules;
        //Duyệt dữ liệu từng module rồi chuyển dữ liệu vào một mảng
        foreach ($listModules as $module) {
            if ($module->quiz) {
                $listQuiz[] = $module->quiz;
            }
        }
        //Trả về dữ liệu bên phải client khi lấy được này
        return response()->json([
            'status' => 'success',
            'message' => "Thông tin khóa học.",
            'data' => $listQuiz,
        ], 200);
    }
    //Phần chi tiết khoá học đôi với người dùng đã đăng nhập vào hệ thống
    public function courseDetailForAuthUser($slug)
    {

        try {
            //Lấy bài học với các mục liên quan tránh n+1 egger loading
            $course = Course::with([
                'category',
                'tags',
                'goals',
                'requirements',
                'audiences',
                'modules' => function ($query) {
                    $query->orderBy('position'); // sort theo position
                },
                'modules.lessons' => function ($query) {
                    $query->orderBy('position'); // sort theo position
                },
                'modules.quiz'
            ])
                ->where('slug', $slug)
                ->firstOrFail();
            ;

            //Lấy người dùng hiện tại
            $user = auth()->user();

            // tên thằng tạo ra khóa học
            $course->author = $course->user->name;

            // Kiểm tra xem ng dùng đã mua khóa học chưa thông qua Usercourse check xem người dùng này đã đăng kí khoá học chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $course->id)
                ->first();
            // Tống số lượng bài học trong khoá học
            $total_lessons = $course->modules->flatMap->lessons->count();
            // Tổng số lượng quiz trong khóa học
            $total_quizzes = $course->modules->whereNotNull('quiz')->count();
            // Tổng bài học + quiz
            $total_items = $total_lessons + $total_quizzes;

            // set duration cho tung bai hoc
            $this->setLessonDurations($course);

            // Nếu người dùng chưa mua khoá học đó thì
            if (!$userCourse) {
                //Tính thời gian có sẵn lưu vào datatable
                $totalDurationVideo = $course->modules->flatMap(function ($module) {
                    return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                        return $lesson->lessonable->duration ?? 0;
                    });
                })->sum();
                //Tính thời gian độc docs
                $totalDurationDocs = $course->modules->flatMap(function ($module) {
                    return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                        $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                        return ceil(($wordCount / 200) * 60);
                    });
                })->sum();
                //Tính tổng thời gian của video và docs
                $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;
                //Cập nhật tổng số lượng bài học
                $course->total_lessons = $total_lessons;
                //Tổng thời gian của khoá học đó

                return response()->json([
                    'status' => 'success',
                    'message' => "Thông tin khóa học.",
                    'data' => $course,
                ], 200);
            }
            //tiếp tục với luồng dữ liệu đăng kí khoá học rồi

            // Tổng số lượng bài học đã học
            $completed_lessons = LessonProgress::where('id_user', $user->id)
                ->where('is_completed', 1)
                ->whereIn('id_lesson', $course->modules->flatMap->lessons->pluck('id'))
                ->count();

            // Số lượng quiz đã hoàn thành
            $completed_quizzes = QuizProgress::where('user_id', $user->id)
                ->where('is_completed', 1)
                ->whereIn('quiz_id', $course->modules->pluck('quiz.id'))
                ->count();

            // Tổng số lượng bài học và quiz đã hoàn thành
            $total_completed_items = $completed_lessons + $completed_quizzes;

            // Tính tiến độ khoá học người dùng đã đăng kí khoá học sẽ là (tổng bài đã hoàn thiện)/ (tổng bài học) * 100 = tiến độ (%)
            $progress_percent = $total_items > 0 ? ($total_completed_items / $total_items) * 100 : 0;

            // Biến check khoá học đã hoàn thành => bài học cuối cùng.
            $last_completed_lesson = NULL;
            // Lấy ra bài học cuối cùng đã hoàn thành => đánh dấu khi người dùng bấm vào khoá học đó sẽ hiển thị bài đã học
            foreach ($course->modules->sortBy('position') as $module) {
                //Lấy danh sách bài học được sort theo posittion
                foreach ($module->lessons->sortBy('position') as $lesson) {
                    //Tiến độ học tập
                    $lessonProgress = LessonProgress::where('id_user', $user->id)
                        ->where('id_lesson', $lesson->id)
                        ->first();

                    //Hoàn thành bài học hay chưa (1: có - 0: chưa)
                    $lesson->is_completed = $lessonProgress ? $lessonProgress->is_completed : 0;
                    //Thời gian dừng đang ở đâu
                    $lesson->last_time_video = $lessonProgress ? $lessonProgress->last_time_video : 0;

                    // bài học cuối cùng hoàn thành
                    if ($lesson->is_completed) {
                        $last_completed_lesson = $lesson->makeHidden('module');
                    }
                }

                // Gán giá trị is_completed cho quiz trong khóa học
                if ($module->quiz) {
                    $quizProgress = QuizProgress::where('user_id', $user->id)
                        ->where('quiz_id', $module->quiz->id)
                        ->first();

                    // Gán giá trị is_completed cho quiz
                    $module->quiz->is_completed = $quizProgress ? $quizProgress->is_completed : 0;

                    // Gán giá trị is_last_quiz cho quiz
                    $module->quiz->is_last_quiz = $module === $course->modules->sortByDesc('position')->first() ? 1 : 0;
                }
            }
            //
            // $next_lesson = NULL;
            if (!$last_completed_lesson) {
                $next_lesson = $course->modules->sortBy('position')->first()->lessons->sortBy('position')->first();
            } else {
                // check lesson tiep theo dua vao bai hoc hoan thanh cuoi cung trong 1 chuong
                $current_module = $last_completed_lesson->module;
                $next_lesson_in_module = $current_module->lessons
                    ->where('position', '>', $last_completed_lesson->position)
                    ->sortBy('position')
                    ->first();

                if ($next_lesson_in_module) {
                    // bài học tiếp theo trong cùng chương
                    $next_lesson = $next_lesson_in_module;
                } else {
                    // nếu chưa làm quiz chương đó thì "next_lesson" sẽ là quiz của chương
                    if (isset($current_module->quiz) && isset($current_module->quiz->id)) {
                        $quizProgress = QuizProgress::where('user_id', $user->id)
                            ->where('quiz_id', $current_module->quiz->id)
                            ->first();
                    } else {
                        $quizProgress = null;
                    }

                    if (!$quizProgress || !$quizProgress->is_completed) {
                        $next_lesson = $current_module->quiz;
                    } else {
                        //  neu la bai hoc cuoi cung trong chuong va quiz da hoan thanh thi chuyen sang chuong sau
                        $next_module = $course->modules
                            ->where('position', '>', $current_module->position)
                            ->sortBy('position')
                            ->first();
                        if ($next_module) {
                            $next_lesson = $next_module->lessons->sortBy('position')->first();
                        } else {
                            $next_lesson = null;
                        }
                    }
                }
            }

            // neu khong con lesson hoac quiz nao -> hoan thanh khoa hoc
            if (!$next_lesson) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Bạn đã hoàn thành khóa học.",
                    'data' => [
                        'course_name' => $course->name,
                        'progress_percent' => $progress_percent,
                        'total_lessons' => $total_items,
                        'completed_lessons' => $total_completed_items,
                        'modules' => $course->modules,
                        'next_lesson' => null,
                    ],
                ], 200);
            }

            //Trả về dữ liệu phía client
            return response()->json([
                'status' => 'success',
                'message' => "Bài học của bạn.",
                'data' => [
                    // 'course' => $course,
                    'course_name' => $course->name,
                    'progress_percent' => $progress_percent,
                    'total_lessons' => $total_items,
                    'completed_lessons' => $total_completed_items,
                    'modules' => $course->modules,
                    'next_lesson' => $next_lesson,
                ],
            ], 200);
        } catch (\Exception $e) {
            //Lỗi Auth nếu chưa đăng nhập
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra bài học.',
                'error' => $e->getMessage() . '|' . $e->getLine(),
            ], 500);
        }

    }

    private function setLessonDurations($course)
    {
        $course->modules->flatMap->lessons->map(function ($lesson) {
            if ($lesson->lessonable_type === Video::class) {
                $video = Video::find($lesson->lessonable_id);
                $lesson->duration = $video ? $video->duration : null;
            } else {
                $lesson->duration = null;
            }
        });
    }
    public function listCourseRelated($slug)
    {
        //Lấy ra khoá học đó
        try {
            //Danh sách khoá học nằm cùng danh mục


            $course = Course::where('slug', $slug)->firstOrFail();
            $category = $course->category;
            //Lấy danh sách khoá học liên quan ra
            //Danh sách khoá học nằm trong category này

            $listCoursesRelated = Course::select('id', 'slug', 'level', 'name', 'thumbnail', 'price', 'price_sale', 'id_user', 'id_category')
                ->whereNot('slug', $course->slug)
                ->with('user:id,name,avatar')
                ->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->where('id_category', $category->id)
                //Lấy những khoá khác khoá học này
                ->where('is_active', 1)
                //Khoá học được kiểm duyệt
                ->where('status', 'approved')
                ->get();
            foreach ($listCoursesRelated as $course) {
                $total_lessons = $course->modules->flatMap->lessons->count();
                // Tổng số lượng quiz trong khóa học
                $total_quizzes = $course->modules->whereNotNull('quiz')->count();
                // Tổng bài học + quiz
                $course->total_lessons = $total_lessons + $total_quizzes;
                // Tính tổng duration của các lesson vid
                $course->total_duration_video = $course->modules->flatMap(function ($module) {
                    return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                        return $lesson->lessonable->duration ?? 0;
                    });
                })->sum();
                $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);
                $course->total_student = DB::table('user_courses')->where('id_course', operator: $course->id)->count();


                $course->makeHidden('modules');
                $course->makeHidden('ratings');
            }
            return response()->json([
                'status' => 'success',
                'message' => "Lấy danh sách khoá học liên quan.",
                'data' => $listCoursesRelated,
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra listring khi lết ra khóa học.',
                'data' => []
            ], 500);
        }
    }
}
