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

    // code cũ
    // public function lessonDetail(Request $request) {
    //     $lessonId = $request->id;
    //     $lesson = Lesson::where([
    //         ['id', $lessonId],
    //     ])->first();

    //     if (!$lesson) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Bài học không tồn tại'
    //         ], 204);
    //     }

    //     if($lesson->content_type == 'video') {
    //         $lessonDetail = Video::find($lesson->lessonable_id);
    //     }else {
    //         $lessonDetail = Document::find($lesson->lessonable_id);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Chi tiết bài học.',
    //         'data' => [
    //             'type_lesson' => $lesson->content_type,
    //             'lesson_title' => $lesson->title,
    //             'lesson_thumbnail' => $lesson->thumbnail,
    //             'lesson_description' => $lesson->description,
    //             'lesson_detail' => $lessonDetail,
    //         ]
    //     ], 200);
    // }

    public function lessonDetail(Lesson $lesson)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn cần đăng nhập để xem thông tin bài học.',
                    'data' => []
                ], 403);
            }

            // check mua khoa hoc chua
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();

            if (!$userCourse) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            $lesson = Lesson::with(['lessonable'])
                ->where('id', $lesson->id)
                ->firstOrFail();

            return response()->json([
                'status' => 200,
                'message' => "Thông tin chi tiết bài học.",
                'data' => $lesson,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
