<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Goal;
use App\Models\Course;
use App\Models\Audience;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TargetController extends Controller
{
    //Lấy mục tiêu của khoá học
    public function getCourseGoals(Course $course)
    {
        //Kiểm tra quyền truy cập
        if (auth()->id() !== $course->id_user) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền truy cập.',
                'data' => []
            ], 403);
        }

        //Trả về dữ liệu nếu pass quyền truy cập
        return response()->json([
            'message' => 'Danh sách mục tiêu khóa học.',
            'data' => [
                'goals' => $course->goals,
                'requirements' => $course->requirements,
                'audiences' => $course->audiences,
            ],
            'status' => 200,
        ], 200);
    }

    //Cập nhật mục tiêu của khoá học
    public function updateTargetStudent(Request $request, Course $course)
    {
        //Check quyền truy cập
        if ($course->id_user !== auth()->id()) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền sửa khóa học này!',
                'data' => []
            ], 403);
        }

        $goals = $request->goals ?? [];
        $requirements = $request->requirements ?? [];
        $audiences = $request->audiences ?? [];

        
        try {
            DB::beginTransaction();
            // goals
            $this->updateOrCreateRecord($course, Goal::class, $goals, 'goal');
            // requirements
            $this->updateOrCreateRecord($course, Requirement::class, $requirements, 'requirement');
            // audiences
            $this->updateOrCreateRecord($course, Audience::class, $audiences, 'audience');

            DB::commit();
            //Trả về dữ liệu
            return response()->json([
                'message' => 'Đã lưu thành công các thay đổi của bạn.',
                'data' => [
                    'goals' => $course->goals,
                    'requirements' => $course->requirements,
                    'audiences' => $course->audiences,
                ],
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            //Lưu log + rollback + trả dữ liệu nếu lỗi
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Cập nhật không thành công! ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function checkCourseCompletion(Course $course)
    {
        try {
            // Kiểm tra quyền
            if (auth()->id() !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }

            // Kiểm tra điều kiện từng phần
            // check điều kiện mục tiêu khóa học
            $course_target = $this->checkCourseTarget($course);
            // check điều kiện chương trong khóa học
            $course_curriculum = $this->checkCourseCurriculum($course);
            // check điều kiện tổng quan khóa học
            $course_overview = $this->checkCourseOverview($course);

            return response()->json([
                'status' => 'success',
                'message' => 'Điều kiện hoàn thành khóa học.',
                'data' => [
                    'course_target' => $course_target,
                    'course_curriculum' => $course_curriculum,
                    'course_overview' => $course_overview,
                ],
            ], 200);
        } catch (\Exception $e) {
            // lỗi server
            return response()->json([
                'status' => 500,
                'message' => 'Có lỗi xảy ra khi kiểm tra điều kiện hoàn thành khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    // Hàm update or create
    private function updateOrCreateRecord(Course $course, $model, $data, $field)
    {
        // Lấy tất cả các vị trí từ data
        $newPositions = array_column($data, 'position');

        // Xóa các bản ghi cũ không có trong request
        $model::where('course_id', $course->id)
            ->whereNotIn('position', $newPositions)
            ->delete();
        // Create hoặc update
        foreach ($data as $item) {
            $model::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'position' => $item['position']
                ],
                [$field => $item[$field]]
            );
        }
    }

    // hàm check mục tiêu khóa học
    private function checkCourseTarget(Course $course)
    {
        return count($course->goals ?? []) >= 4
            && count($course->requirements ?? []) >= 1
            && count($course->audiences ?? []) >= 1;
    }

    // hàm check chương trong khóa học (đủ 5 chương trở lên)
    private function checkCourseCurriculum(Course $course)
    {
        return $course->modules()->count() >= 5;
    }

    // hàm check tổng quan khóa học (đủ các trường cần thiết)
    private function checkCourseOverview(Course $course)
    {
        $required_fields = ['name', 'description', 'level', 'id_category', 'price', 'thumbnail', 'trailer'];

        foreach ($required_fields as $field) {
            if (empty($course->$field)) {
                return false; // trống 1 trường bất kì ở mảng $required_fields thì return false luôn
            }
        }

        return true; // đủ hết thì return true
    }
}
