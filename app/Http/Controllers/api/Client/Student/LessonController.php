<?php

namespace App\Http\Controllers\api\Client\Student;

use App\Models\Video;
use App\Models\Lesson;
use App\Models\Document;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\LessonProgress;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Lessons\LessonProgressRequest;

class LessonController extends Controller
{
    //Xử lý authentication => mí xem được chi tiết bài học
    public function lessonDetail(Lesson $lesson)
    {
        try {
            //Lấy người dùng hiện tại
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();
            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }
            //Lấy bài học đó ra
            $lesson = Lesson::with(['lessonable'])
                ->where('id', $lesson->id)
                ->firstOrFail();
            //Nếu tồn tại bài học đó thì trả về dữ liệu như bên
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin chi tiết bài học.",
                'data' => $lesson,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server báo lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // API cập nhật tiến độ bài học
    public function updateLessonProgress(LessonProgressRequest $request, Lesson $lesson)
    {
        try {
            // Lấy người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();
            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Xác định loại bài học
            $data = [
                'id_user' => $user->id,
                'id_lesson' => $lesson->id,
                'is_completed' => $request->is_completed,
            ];

            // Nếu bài học là vid, cập nhật last_time_video
            if ($lesson->lessonable_type === Video::class && $request->has('last_time_video')) {
                $data['last_time_video'] = $request->last_time_video;
            }

            // Cập nhật hoặc tạo mới tiến độ bài học
            $lessonProgress = LessonProgress::updateOrCreate(
                [
                    'id_user' => $user->id,
                    'id_lesson' => $lesson->id,
                ],
                $data
            );

            // response cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Tiến độ bài học đã được cập nhật.',
                'data' => $lessonProgress,
            ], 200);

        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật tiến độ bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}