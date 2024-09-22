<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function storeWithOptions(string $id, Request $request)
    {
        // Validate input cho cả câu hỏi và tùy chọn
        // $request->validate([
        //     'question' => 'required|string',
        //     'points' => 'nullable|integer',
        //     'options.*' => 'required|string',
        //     'is_correct.*' => 'nullable|boolean',
        // ]);

        // Thêm dữ liệu vào bảng Question
        try {
            DB::beginTransaction();
            $question = Question::create([
                'id_quiz' => $id,
                'question' => $request->input('question'),
                'type' => "text",
                'points' => $request->input('points', 0),  // Điểm mặc định là 0 nếu không nhập
            ]);
            foreach ($request->input('options') as $key => $optionText) {
                Option::create([
                    'id_question' => $question->id,
                    'option' => $optionText,
                    'is_correct' => isset($request->is_correct[$key]) ? true : false,
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return redirect()->back()->with('success', 'Thêm câu hỏi thành công.');
    }
}
