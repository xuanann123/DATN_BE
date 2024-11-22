<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem request có chứa khóa học, module, bài học hay quiz
        $course = $request->route('course');
        $module = $request->route('module');
        $lesson = $request->route('lesson');
        $quiz = $request->route('quiz');

        // Nếu có khóa học
        if ($course && $course instanceof Course) {

            // Kiểm tra trạng thái của khóa học
            if ($course && ($course->status !== 'draft' && $course->status !== 'rejected')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Hành động không hợp lệ, vui lòng xác nhận lại trạng thái khóa học.',
                    'data' => []
                ], 400);
            }
        }

        if ($module && $module instanceof Module) {
            $course = $module->course;

            if ($course && $course->status !== 'draft' && $course->status !== 'rejected') {
                return response()->json([
                    'status' => 400,
                    'message' => 'Hành động không hợp lệ, vui lòng xác nhận lại trạng thái khóa học.',
                    'data' => []
                ], 400);
            }
        }

        if ($lesson && $lesson instanceof Lesson) {
            $module = $lesson ? $lesson->module : null;
            $course = $module ? $module->course : null; // Lấy khóa học từ module

            if ($course && ($course->status !== 'draft' && $course->status !== 'rejected')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Hành động không hợp lệ, vui lòng xác nhận lại trạng thái khóa học.',
                    'data' => []
                ], 400);
            }
        }

        if ($quiz && $quiz instanceof Quiz) {
            $module = $quiz ? $quiz->module : null;
            $course = $module ? $module->course : null; // Lấy khóa học từ module

            if ($course && ($course->status !== 'draft' && $course->status !== 'rejected')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Hành động không hợp lệ, vui lòng xác nhận lại trạng thái khóa học.',
                    'data' => []
                ], 400);
            }
        }

        return $next($request);
    }
}
