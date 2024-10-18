<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Video;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\LessonProgress;
use App\Http\Controllers\Controller;


class CourseDetailController extends Controller // di ve sinh
{
    //Phần chi tiết bài học
    public function courseDetail($slug)
    {
        try {
            //Chi tiết bài học lấy theo slug
            $course = Course::with(['category', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons'])
                ->where('slug', $slug)
                ->firstOrFail();

            // Số lượng bài học trong khóa học, sử dụng flatmap để đến từng số lượng bài học trong từng chương học
            $total_lessons = $course->modules->flatMap->lessons->count();


           

            // set duration cho tung bai hoc
            $this->setLessonDurations($course);

             // Sẽ lấy tổng số lượng tất cả các bài học là video trong khoá học đó
            $total_duration_vid = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');

            //Set giá trị trong khoá học là tổng bài học và tổng số lượng bài học
            $course->total_lessons = $total_lessons;
            $course->total_duration_vid = $total_duration_vid;
            // Trả về dữ liệu bên phía client khi lấy được thành công
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin khóa học.",
                'data' => [$course],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra thông tin khóa hoc.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //Phần chi tiết khoá học đôi với người dùng đã đăng nhập vào hệ thống
    public function courseDetailForAuthUser($slug)
    {

        try {
            //Lấy bài học với các mục liên quan tránh n+1 egger loading
            $course = Course::with(['category', 'tags', 'goals', 'requirements', 'audiences', 'modules.lessons'])
                ->where('slug', $slug)
                ->firstOrFail();
            //Lấy người dùng hiện tại 
            $user = auth()->user();
            // Kiểm tra xem ng dùng đã mua khóa học chưa thông qua Usercourse check xem người dùng này đã đăng kí khoá học chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $course->id)
                ->first();
            // Tống số lượng bài học trong khoá học
            $total_lessons = $course->modules->flatMap->lessons->count();

            // Nếu người dùng chưa mua khoá học đó thì
            if (!$userCourse) {
                //Tổng số lượng tất cả các bài học
                $total_duration = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                    ->sum('duration');
                //Cập nhật tổng số lượng bài học
                $course->total_lessons = $total_lessons;
                //Tổng thời gian của khoá học đó 
                $course->total_duration = $total_duration;
                //Trả dữ liệu về phía client
                return response()->json([
                    'status' => 'success',
                    'message' => "Thông tin khóa học.",
                    'data' => [$course],
                ], 200);
            }
            //tiếp tục với luồng dữ liệu đăng kí khoá học rồi

            // Tổng số lượng bài học đã học
            $completed_lessons = LessonProgress::where('id_user', $user->id)
                ->where('is_completed', 1)
                ->whereIn('id_lesson', $course->modules->flatMap->lessons->pluck('id'))
                ->count();

            // Tính tiến độ khoá học người dùng đã đăng kí khoá học sẽ là (tổng bài đã hoàn thiện)/ (tổng bài học) * 100 = tiến độ (%)
            $progress_percent = $total_lessons > 0 ? ($completed_lessons / $total_lessons) * 100 : 0;

            // Biến check khoá học đã hoàn thành => bài học cuối cùng.
            $last_completed_lesson = NULL;
            // Lấy ra bài học cuối cùng đã hoàn thành => đánh dấu khi người dùng bấm vào khoá học đó sẽ hiển thị bài đã học
            foreach ($course->modules->sortBy('posittion') as $module) {
                //Lấy danh sách bài học được sort theo posittion
                foreach ($module->lessons->sortBy('posittion') as $lesson) {
                    //Tiến độ học tập
                    $lessonProgress = LessonProgress::where('id_user', $user->id)
                        ->where('id_lesson', $lesson->id)
                        ->first();

                    //Đến bài nào
                    $lesson->is_completed = $lessonProgress ? $lessonProgress->is_completed : 0;
                    //Thời gian dừng đang ở đâu
                    $lesson->last_time_video = $lessonProgress ? $lessonProgress->last_time_video : 0;



                    // bài học cuối cùng hoàn thành
                    if ($lesson->is_completed) {
                        $last_completed_lesson = $lesson->makeHidden('module');

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
                //Trả về dữ liệu phía client
                return response()->json([
                    'status' => 'success',
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
        } catch (\Exception $e) {
            //Lỗi Auth nếu chưa đăng nhập
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy ra bài học.',
                'error' => $e->getMessage(),
            ]);
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
