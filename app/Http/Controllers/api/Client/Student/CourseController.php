<?php

namespace App\Http\Controllers\api\Client\Student;

use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\QuizProgress;

class CourseController extends Controller
{
    public function updateProgress(Request $request, Course $course)
    {
        try {
            // Lấy người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $course->id)
                ->first();

            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Lấy tất cả các bài học (lesson) và bài kiểm tra (quiz) trong khóa học
            $lessons = $course->modules->flatMap->lessons;
            $quizzes = $course->modules->whereNotNull('quiz')->pluck('quiz');

            // Đếm tất cả những bài học đã hoàn thành
            $completedLessons = LessonProgress::where('id_user', $user->id)
                ->whereIn('id_lesson', $lessons->pluck('id'))
                ->where('is_completed', 1)
                ->count();

            // Đếm tất cả những bài tập đã hoàn thành
            $completedQuizzes = QuizProgress::where('user_id', $user->id)
                ->whereIn('quiz_id', $quizzes->pluck('id'))
                ->where('is_completed', 1)
                ->count();

            // Tổng số lượng bài học và bài tập
            $totalItems = $lessons->count() + $quizzes->count();

            // Nếu tất cả các bài học và bài tập đã hoàn thành -> update progress
            if ($completedLessons + $completedQuizzes === $totalItems) {
                $data = [
                    'progress_percent' => '100',
                    'completed_at' => now(),
                ];

                // Cập nhật hoặc tạo mới tiến độ khóa học
                $CourseProgress = UserCourse::updateOrCreate(
                    //Check điều kiện này đã tồn tại rồi thì đi update => còn không thì đi cập nhật
                    [
                        'id_user' => $user->id,
                        'id_course' => $course->id,
                    ],
                    $data
                );

                // response cho client
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tiến độ bài học đã được cập nhật.',
                    'data' => $CourseProgress,
                ], 200);
            } else {
                // Nếu chưa hoàn thành tất cả các bài học hoặc bài tập
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn cần hoàn thành tất cả các bài học và bài tập trước.',
                    'data' => [
                        'total_items' => $totalItems,
                        'completed_items' => $completedLessons + $completedQuizzes,
                    ],
                ], 400);
            }

        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật tiến độ khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
