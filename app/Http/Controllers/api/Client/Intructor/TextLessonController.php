<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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

            $resourse_path = $this->uploadFile($request->file('resourse_path'), 'resourse');

            $document = Document::create([
                'content' => $request->content,
                'resourse_path' => $resourse_path,
            ]);

            $lesson = Lesson::create([
                'title' => $request->title,
                'is_preview' => $request->input('is_preview'),
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
            $new_file = $request->file('resourse_path') ?? NULL;
            if ($new_file) {
                // Nếu có file mới upload thì xử lí upload
                $lesson->lessonable->resourse_path = $this->uploadFile($request->file('resourse_path'), 'resourse', $lesson->lessonable->resourse_path);
            }

            if ($lesson->lessonable_type == Document::class) {
                $lesson->lessonable->update([
                    'content' => $request->content,
                    'resourse_path' => $lesson->lessonable->resourse_path, // không có file upload thì lấy dữ liệu ở db
                ]);
            }

            $lesson->update([
                'title' => $request->title,
                'is_preview' => $request->input('is_preview'),
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
                $document = $lesson->lessonable;

                // Del file resource
                if ($document->resourse_path) {
                    Storage::disk('public')->delete($document->resourse_path);
                }

                $document->delete();
            }

            // Cập nhật lại position của các bài học còn lại sau khi xóa bài học hiện tại
            $positionToDelete = $lesson->position;
            Lesson::where('id_module', $lesson->id_module)
                ->where('position', '>', $positionToDelete)
                ->decrement('position');
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

    private function uploadFile($file, $type, $currentFile = null)
    {
        if ($file && $file->isValid()) {
            // Xóa ảnh cũ nếu tồn tại
            if ($currentFile) {
                Storage::delete($currentFile);
            }
            $newNameFile = $type . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            return Storage::putFileAs('files/' . $type, $file, $newNameFile);
            // return $newNameImage;
        }
        return null;
    }
}
