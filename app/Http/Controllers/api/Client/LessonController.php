<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Video;
use App\Models\Lesson;
use App\Models\Document;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LessonController extends Controller
{
    //Xử lý authentication => mí xem được chi tiết bài học
    public function lessonDetail(Lesson $lesson)
    {
        try {
            //Lấy người dùng hiện tại
            $user = auth()->user();
            //Không tồn tại người dùng báo về lỗi 403
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn cần đăng nhập để xem thông tin bài học.',
                    'data' => []
                ], 403);
            }

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
}
