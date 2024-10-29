<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\StoreModuleRequest;

class CurriculumController extends Controller
{
    public function index(Course $course)
    {
        try {
            //Quyền truy cập
            if (auth()->id() !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }
            //Lấy danh sách khoá tổng quan
            $modules = $course->modules()->with([
                'lessons' => function ($query) {
                    $query->orderBy('position');
                },
                'lessons.lessonable',
                'quiz'
            ])
            ->orderBy('position')
            ->get();
            return response()->json([
                'status' => 200,
                'message' => "Danh sách chương trình giảng dạy.",
                'data' => [
                    'modules' => $modules
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy ra chương trình giảng dạy.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
