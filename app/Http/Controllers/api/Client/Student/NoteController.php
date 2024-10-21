<?php

namespace App\Http\Controllers\api\Client\Student;

use App\Models\Note;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Notes\StoreNoteRequest;
use App\Http\Requests\Client\Notes\UpdateNoteRequest;

class NoteController extends Controller
{
    public function getNotes(Course $course)
    {
        try {
            // check nguoi dung dang dang nhap
            $user = auth()->user();

            $courseId = $course->id;

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $courseId)
                ->first();

            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Lấy tất cả các module thuộc khóa học
            $course = Course::with('modules.lessons.notes')
                ->where('id', $courseId)
                ->firstOrFail();

            // Duyệt qua các module và lesson để lấy tất cả các ghi chú
            $notes = $course->modules->flatMap(function ($module) use ($user) {
                return $module->lessons->flatMap(function ($lesson) use ($user) {
                    return $lesson->notes->where('id_user', $user->id)->map(function ($note) use ($lesson) {
                        $note->lesson_title = $lesson->title;
                        return $note;
                    });
                });
            });

            // Phản hồi cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách ghi chú.',
                'data' => $notes,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách ghi chú.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addNote(StoreNoteRequest $request, Lesson $lesson)
    {
        try {
            // check nguoi dung dang dang nhap
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();

            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // insert db
            $note = Note::create([
                'id_user' => $user->id,
                'id_lesson' => $lesson->id,
                'content' => $request->content,
                'duration' => $request->duration,
            ]);

            // response cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Tạo ghi chú thành công.',
                'data' => $note,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi thêm ghi chú.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLessonByNote(Note $note)
    {
        try {
            // Check người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra ghi chú này có phải của người dùng đang đăng nhập không
            if ($note->id_user != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập ghi chú này.',
                    'data' => []
                ], 403);
            }

            // Lấy thông tin bài học liên quan đến ghi chú
            $lesson = Lesson::with(['lessonable'])
                ->where('id', $note->id_lesson)
                ->firstOrFail();

            // Response thông tin chi tiết bài học cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Thông tin chi tiết bài học bạn đã ghi chú.',
                'data' => $lesson,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học từ ghi chú.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateNote(UpdateNoteRequest $request, Note $note)
    {
        try {
            $user = auth()->user();

            // Kiểm tra ghi chú có phải của người đang đăng nhập tạo hay không
            if ($note->id_user != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền cập nhật ghi chú này.',
                    'data' => []
                ], 403);
            }

            // Update
            $note->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật ghi chú thành công.',
                'data' => $note,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật ghi chú.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteNote(Note $note)
    {
        try {
            $user = auth()->user();

            // Kiểm tra ghi chú có phải của người dùng đăng nhập hay không
            if ($note->id_user != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền xóa ghi chú này.',
                    'data' => []
                ], 403);
            }

            // Delete
            $note->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa ghi chú thành công.',
                'data' => [],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi xóa ghi chú.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
