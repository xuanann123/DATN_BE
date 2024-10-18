<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Quiz\StoreQuizRequest;
use App\Http\Requests\Client\Quiz\UpdateQuizRequest;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;

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
    public function deleteQuiz(Quiz $quiz) {
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
    //Thêm question cho một quiz 
    
}
