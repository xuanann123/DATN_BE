<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Coding;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Lessons\StoreCodingLessonRequest;
use App\Http\Requests\Client\Lessons\UpdateCodingContentRequest;
use App\Http\Requests\Client\Lessons\UpdateCodingLessonRequest;

class CodingLesson extends Controller
{
    public function store(StoreCodingLessonRequest $request, Module $module)
    {
        try {
            DB::beginTransaction();
            $language = strtolower($request->input("language"));
            $sampleCode = $this->getSampleCode($language);

            // vị trí khóa học cuối cùng trong module hiện tại
            $maxPosition = Lesson::where('id_module', $module->id)->max('position');

            // Tạo bài học coding
            $codingLesson = Coding::create([
                'language' => $language,
                'sample_code' => $sampleCode,
            ]);

            $lesson = $codingLesson->lesson()->create([
                'id_module' => $module->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'is_preview' => $request->input('is_preview'),
                'content_type' => 'coding',
                'position' => $maxPosition + 1
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Thêm bài học coding thành công.',
                'data' => $lesson->load('lessonable'),
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi thêm bài học coding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateCodingLessonRequest $request, Lesson $lesson)
    {
        try {
            DB::beginTransaction();

            // bai coding
            $codingLesson = $lesson->lessonable;

            $language = strtolower($request->input("language"));

            // check ngôn ngữ thay đổi
            if ($codingLesson->language !== $language) {
                // Nếu ngôn ngữ thay đổi, xóa hết nội dung cũ
                $codingLesson->update([
                    'statement' => null,
                    'hints' => null,
                    'sample_code' => null,
                    'result_code' => null,
                    'output' => null,
                ]);

                $codingLesson->sample_code = $sampleCode = $this->getSampleCode($language);
            }

            // update coding
            $codingLesson->update([
                'language' => $language,
                'sample_code' => $codingLesson->sample_code,
            ]);

            // update lesson
            $lesson->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'is_preview' => $request->input('is_preview'),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bài học coding đã được cập nhật thành công.',
                'data' => $lesson->load('lessonable'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật bài học coding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateContent(UpdateCodingContentRequest $request, Lesson $lesson)
    {
        try {
            DB::beginTransaction();

            // Cập nhật thông tin nội dung của bài học coding
            $lesson->lessonable()->update([
                'statement' => $request->input('statement'),
                'hints' => $request->input('hints'),
                'sample_code' => $request->input('sample_code'),
                'result_code' => $request->input('result_code'),
                'output' => $request->input('output'),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Nội dung bài học coding đã được cập nhật thành công.',
                'data' => $lesson->load('lessonable'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật nội dung bài học coding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Lesson $lesson)
    {
        DB::beginTransaction();
        try {

            $coding = $lesson->lessonable;

            $coding->delete();

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

    private function getSampleCode($language)
    {
        $sampleCodes = [
            'javascript' => "console.log('Hello, world!');",
            'php' => "<?php\n\n// In ra màn hình\necho 'Hello, world!';",
            'python' => "print('Hello, world!')",
            'csharp' => "using System;\n\nclass Program {\n    static void Main() {\n        Console.WriteLine(\"Hello, world!\");\n    }\n}",
            'typescript' => "console.log('Hello, world!');",
            'java' => "public class Main {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, world!\");\n    }\n}"
        ];

        return $sampleCodes[$language] ?? '';
    }
}
