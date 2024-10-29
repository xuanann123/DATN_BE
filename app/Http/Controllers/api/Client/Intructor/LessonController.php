<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Lessons\UpdateLessonPositionsRequest;

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

    public function updateLessonPosition(UpdateLessonPositionsRequest $request, Module $module)
    {
        DB::beginTransaction();
        try {
            // NGười dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $module->course->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập vào chương này.',
                    'data' => []
                ], 403);
            }

            // update vị trí bài học
            foreach ($request->lessons as $lessonPosition) {
                $lesson = Lesson::find($lessonPosition['id']);
                if ($lesson && $lesson->id_module === $module->id) {
                    $lesson->position = $lessonPosition['position'];
                    $lesson->save();
                }
            }

            $module->makeHidden('course');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Vị trí các bài học đã được cập nhật thành công.',
                'data' => $module->load([
                    'lessons' => function ($query) {
                        $query->orderBy('position');
                    }
                ])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật vị trí bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
