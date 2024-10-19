<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Quiz;
use App\Models\Module;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\Quiz\StoreQuizRequest;
use App\Http\Requests\Client\Quiz\UpdateQuizRequest;

class ModuleQuizController extends Controller
{
    # ============================== Controller with Quiz ============================== #
    //Chỉ thêm được 1 quiz cho 1 chương học
    public function addQuiz(StoreQuizRequest $request, Module $module)
    {
        if (!$module) {
            return response()->json([
                'status' => 'error',
                'message' => 'Module not found',
                'data' => []
            ], 404);
        }
        try {
            $quiz = Quiz::create([
                'id_module' => $module->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'total_points' => 0,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Add quiz successfully',
                'data' => $quiz
            ], 201);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
    //Sửa quiz
    public function updateQuiz(UpdateQuizRequest $request, Quiz $quiz)
    {

        try {
            //Nếu không tồn tại quiz
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quiz not found',
                    'data' => []
                ], 404);
            }
            //Update quiz khi lấy được dữ liệu từ ô input
            $quiz->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ]);
            //Trả về dữ liệu khi update thành công
            return response()->json([
                'status' => 'success',
                'message' => 'Update quiz successfully',
                'data' => $quiz
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
    //Xoá quiz
    public function deleteQuiz(Quiz $quiz)
    {
        try {
            //Kiểm tra quiz tồn tại trước khi xoá không
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quiz not found',
                    'data' => []
                ], 404);
            }
            //Xoá quiz
            $quiz->delete();
            //Trả dữ liệu delete quiz success
            return response()->json([
                'status' => 'success',
                'message' => 'Delete quiz successfully',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    #=================================== Controller with Questions ===============================
    //Danh sách quiz theo module
    public function showQuiz(string $id)
    {
        //Dữ liệu theo tên chương tên khoá học + người tạo khoá học
        $module = Module::with(['quiz', 'course.user'])->where('id', $id)->first();
        //Tiếp theo lấy quiz của nó để truy vấn sâu để đổ dữ liệu ra
        $quiz = Quiz::with(['questions.options', 'module.course'])->where('id_module', $id)->first();
        if (!$module) {
            return response()->json([
                'status' => 'error',
                'message' => 'Module not found',
                'data' => []
            ]);
        }
        //Đổ câu hỏi ra
        return response()->json([
            'status' => 'success',
            'message' => 'Get quiz successfully',
            'data' => [
                // 'module' => $module,
                'quiz' => $quiz
            ]
        ]);
    }
    //Thêm dữ liệu question và options cho Quiz
    public function addQuestionAndOption(Request $request, Quiz $quiz)
    {
        try {
            //Nếu không tồn tại quiz
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quiz not found',
                    'data' => []
                ], 404);
            }
            $questionData = $request->question;
            // // Thêm dữ liệu bằng cách như sau
            $quizQuestion = Question::create([
                'id_quiz' => $quiz->id,  // Thêm quiz_id nếu cần
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'points' => $questionData['points'],
                'image_url' => $this->uploadImage($questionData['image'] ?? NULL, 'questions')
            ]);
            // Tạo 1 vòng duyệt qua từng option rồi thêm dữ liệu vào database
            foreach ($request->input('options') as $optionIndex => $optionData) {
                $optionText = is_array($optionData) ? $optionData['text'] : $optionData;
                $optionImage = $request->file("options.{$optionIndex}.image") ?? null;
                $option = Option::create([
                    'id_question' => $quizQuestion->id,
                    'option' => $optionText,
                    'image_url' => $this->uploadImage($optionImage, 'options'),
                    'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex)
                ]);
            }

            // Lấy câu hỏi vừa tạo cùng với các options
            $questionWithOptions = Question::with('options')->find($quizQuestion->id);

            // Trả về dữ liệu khi update này
            return response()->json([
                'status' => 'success',
                'message' => 'Update quiz successfully',
                'data' => [
                    'quiz' => $quiz,
                    'question' => $questionWithOptions
                ],
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    private function isCorrectAnswer($question, $optionIndex)
    {
        if ($question['type'] === 'one_choice') {
            // Với one_choice, correct_answer là một giá trị số (index)
            return $optionIndex == $question['correct_answer'];
        } elseif ($question['type'] === 'multiple_choice') {
            // Với multiple_choice, correct_answer là một mảng chứa các index
            return in_array($optionIndex, $question['correct_answer']);
        }
        return false;
    }

    private function uploadImage($image, $type)
    {
        if ($image && $image->isValid()) {
            $newNameImage = $type . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            return Storage::putFileAs('images/' . $type, $image, $newNameImage);
            // return $newNameImage;
        }
        return null;
    }

}
