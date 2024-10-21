<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LessonController extends Controller
{
    public function lessonDetailTeacher(Lesson $lesson)
    {
        try {
            // NGười dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $lesson->module->course->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập vào thông tin bài học này.',
                    'data' => []
                ], 403);
            }

            // get bài học quiz, doc, or vid
            $lesson = Lesson::with(['lessonable'])
                ->where('id', $lesson->id)
                ->firstOrFail();

            // Dữ liệu thành công trả về
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin chi tiết bài học.",
                'data' => $lesson,
            ], 200);
        } catch (\Exception $e) {
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
