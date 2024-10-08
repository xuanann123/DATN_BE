<?php

namespace App\Http\Controllers\Admin;
use App\Models\Quiz;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function storeWithOptions(string $id, Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $optionText = [];
            $optionImage = [];
            //Duyệt qua vòng lập cấu hỏi sẽ có những bảng ghi những câu hỏi đó là gì
            foreach ($request->questions as $questionIndex => $questionData) {
                // Tạo câu hỏi trong bảng Question thêm nội dung câu hỏi của bài quiz nào
                $quizQuestion = Question::create([
                    'id_quiz' => $id,  // Thêm quiz_id nếu cần
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => 1,
                    'image_url' => $this->uploadImage($questionData['image'] ?? NULL, 'questions')
                ]);
                // dd($questionData['options']);
                // 1 vòng lập nữa đi duyệt quảng option ra tiếp chúng ta được danh sách lựa chọn
                // Lưu các tùy chọn (options) cho câu hỏi
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    $optionText = is_array($optionData) ? $optionData['text'] : $optionData;
                    $optionImage = $request->file("questions.{$questionIndex}.options.{$optionIndex}.image") ?? null;

                    $options = Option::create([
                        'id_question' => $quizQuestion->id,
                        'option' => $optionText,
                        'image_url' => $this->uploadImage($optionImage, 'options'),
                        // Duyệt qua index và check theo value của correct_answer để biết cái nào đúng or sai
                        'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex),  // Đánh dấu đáp án đúng
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return redirect()->back()->with('success', 'Thêm câu hỏi thành công.');
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
    public function show($id)
    {

        try {
            $quiz = Quiz::with(['questions.options'])->findOrFail($id);

            return response()->json([
                'title' => $quiz->title,
                'description' => $quiz->description,
                'questions' => $quiz->questions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }

    }

    private function uploadImage($image, $type)
    {
        if ($image && $image->isValid()) {
            $newNameImage = $type . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            return Storage::putFileAs('images/' . $type, $image, $newNameImage);
        }
        return null;
    }
}
