<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Video;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\LessonProgress;
use App\Http\Controllers\Controller;

class CourseDetailController extends Controller
{
    public function courseDetail($slug)
    {
        try {
            $course = Course::with(['category', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons.lessonable'])
                ->where('slug', $slug)
                ->firstOrFail();

            // tong so luong bai hoc
            $total_lessons = $course->modules->flatMap->lessons->count();

            // tong thoi luong video trong khoa hoc
            $total_duration_vid = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');

            $course->total_lessons = $total_lessons;
            $course->total_duration_vid = $total_duration_vid;

            return response()->json([
                'status' => 200,
                'message' => "Thông tin khóa học.",
                'data' => [$course],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy ra thông tin khóa hoc.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function courseDetailForAuthUser($slug)
    {
        $course = Course::with(['category', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons.lessonable'])
            ->where('slug', $slug)
            ->firstOrFail();

        $user = auth()->user();

        // Kiểm tra xem ng dùng đã mua khóa học chưa
        $userCourse = UserCourse::where('id_user', $user->id)
            ->where('id_course', $course->id)
            ->first();

        // tổng số bài học trong khóa học
        $total_lessons = $course->modules->flatMap->lessons->count();


        // chua mua
        if (!$userCourse) {
            $total_duration = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');

            $course->total_lessons = $total_lessons;
            $course->total_duration = $total_duration;

            return response()->json([
                'status' => 200,
                'message' => "Thông tin khóa học.",
                'data' => [$course],
            ], 200);
        }

        // bai hoc da hoan thanh
        $completed_lessons = LessonProgress::where('id_user', $user->id)
            ->where('is_completed', 1)
            ->whereIn('id_lesson', $course->modules->flatMap->lessons->pluck('id'))
            ->count();

        // tien do khoa hoc (tinh bang %)
        $progress_percent = $total_lessons > 0 ? ($completed_lessons / $total_lessons) * 100 : 0;

        // thong tin bai hoc da hoan thanh cuoi cung
        $last_completed_lesson = NULL;

        // Lấy ra bài học cuối cùng đã hoàn thành
        foreach ($course->modules->sortBy('posittion') as $module) {
            foreach ($module->lessons->sortBy('posittion') as $lesson) {
                $lessonProgress = LessonProgress::where('id_user', $user->id)
                    ->where('id_lesson', $lesson->id)
                    ->first();

                $lesson->is_completed = $lessonProgress ? $lessonProgress->is_completed : 0;
                $lesson->last_time_video = $lessonProgress ? $lessonProgress->last_time_video : 0;

                // bài học cuối cùng hoàn thành
                if ($lesson->is_completed) {
                    $last_completed_lesson = $lesson;
                }
            }
        }

        //
        // $next_lesson = NULL;
        if (!$last_completed_lesson) {
            $next_lesson = $course->modules->sortBy('posittion')->first()->lessons->sortBy('posittion')->first();
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
                // neu la bai hoc cuoi cung trong chuong thi chuyen sang chuong sau
                $next_module = $course->modules
                    ->where('position', '>', $current_module->position)
                    ->sortBy('posittion')
                    ->first();
                if ($next_module) {
                    $next_lesson = $next_module->lessons->sortBy('posittion')->first();
                }
            }
        }


        return response()->json([
            'status' => 200,
            'message' => "Bài học của bạn.",
            'data' => [
                // 'course' => $course,
                'progress_percent' => $progress_percent,
                'total_lessons' => $total_lessons,
                'completed_lessons' => $completed_lessons,
                'modules' => $course->modules,
                'next_lesson' => $next_lesson,
            ],
        ], 200);
    }
}
