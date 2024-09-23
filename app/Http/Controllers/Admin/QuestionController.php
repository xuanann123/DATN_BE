<?php

namespace App\Http\Controllers\Admin;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function storeWithOptions(string $id, Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            //Duyệt qua vòng lập cấu hỏi sẽ có những bảng ghi những câu hỏi đó là gì
            foreach ($request->questions as $questionData) {
                // Tạo câu hỏi trong bảng Question thêm nội dung câu hỏi của bài quiz nào
                $quizQuestion = Question::create([
                    'id_quiz' => $id,  // Thêm quiz_id nếu cần
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => 1,
                ]);
                // 1 vòng lập nữa đi duyệt quảng option ra tiếp chúng ta được danh sách lựa chọn
                // Lưu các tùy chọn (options) cho câu hỏi
                foreach ($questionData['options'] as $index => $optionText) {
                    Option::create([
                        'id_question' => $quizQuestion->id,
                        'option' => $optionText,
                        // Duyệt qua index và check theo value của correct_answer để biết cái nào đúng or sai
                        'is_correct' => $this->isCorrectAnswer($questionData, $index),  // Đánh dấu đáp án đúng
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
}
