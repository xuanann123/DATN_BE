<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\StoreTextLessonRequest;
use App\Http\Requests\Client\Courses\UpdateTextLessonRequest;

class TextLessonController extends Controller
{

    /// Text Lesson
    public function storeTextLesson(StoreTextLessonRequest $request, Module $module)
    {
        DB::beginTransaction();
        try {
            $request->validated();

            $maxPosition = Lesson::where('id_module', $module->id)->max('position');

            $document = Document::create([
                'content' => $request->content,
            ]);

            $lesson = Lesson::create([
                'title' => $request->title,
                'id_module' => $module->id,
                'content_type' => 'document',
                'lessonable_type' => Document::class,
                'lessonable_id' => $document->id,
                'position' => $maxPosition + 1
            ]);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Bài học đã được thêm thành công.',
                'data' => [
                    'lesson' => $lesson->load('lessonable'),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tạo bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateTextLesson(UpdateTextLessonRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            if ($lesson->lessonable_type == Document::class) {
                $lesson->lessonable->update([
                    'content' => $request->content
                ]);
            }

            $lesson->update([
                'title' => $request->title,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật bài học thành công!',
                'data' => [
                    'lesson' => $lesson,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyTextLesson(Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            if ($lesson->lessonable_type == Document::class) {
                $lesson->lessonable->delete();
            }

            $lesson->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Xóa bài học thành công!',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xóa bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /// Video Lesson
}
