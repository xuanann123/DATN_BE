<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseDetailController extends Controller
{
    public function courseDetail(Request $request) {

        $slugCourse = $request->course;

        $course = Course::where([
            ['slug', $slugCourse],
            ['is_active', 1]
        ])->first();

        try {
            $modules = $course->modules()->with(['lessons.lessonable'])->get();
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
