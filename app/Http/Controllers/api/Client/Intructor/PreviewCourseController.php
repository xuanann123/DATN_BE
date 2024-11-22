<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PreviewCourseController extends Controller
{
    public function index(Course $course)
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
            ])->findOrFail( $course->id );

            //Lấy người dùng hiện tại
            $user = auth()->user();

            // tên thằng tạo ra khóa học
            $course->author = $course->user->name;

            // Tống số lượng bài học trong khoá học
            $total_lessons = $course->modules->flatMap->lessons->count();
            // Tổng số lượng quiz trong khóa học
            $total_quizzes = $course->modules->whereNotNull('quiz')->count();
            // Tổng bài học + quiz
            $total_items = $total_lessons + $total_quizzes;

            // set duration cho tung bai hoc
            $this->setLessonDurations($course);

            //Trả về dữ liệu phía client
            return response()->json([
                'status' => 'success',
                'message' => "Khóa học của bạn.",
                'data' => [
                    'course' => $course,
                    'total_lessons' => $total_items,
                ],
            ], 200);
        } catch (\Exception $e) {
            //Lỗi Auth nếu chưa đăng nhập
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra khóa học.',
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
}
