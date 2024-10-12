<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\StoreModuleRequest;
use App\Http\Requests\Client\Courses\UpdateModuleRequest;

class ModuleController extends Controller
{
    public function storeModule(StoreModuleRequest $request, Course $course)
    {
        try {
            if (auth()->id() !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền thao tác.',
                    'data' => []
                ], 403);
            }

            $request->validated();

            $maxPosition = $course->modules()->max('position');
            $newPosition = $maxPosition ? $maxPosition + 1 : 1; // Nếu không có module nào thì vị trí đầu tiên là 1


            $module = Module::create([
                'id_course' => $course->id,
                'title' => $request->title,
                'description' => $request->description,
                'position' => $newPosition,
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Module đã được thêm thành công.',
                'data' => [
                    'module' => $module,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thêm module.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateModule(UpdateModuleRequest $request, Module $module)
    {
        try {

            $request->validated();
            $module->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Module đã được cập nhật thành công.',
                'data' => [
                    'module' => $module,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật module.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteModule(Module $module)
    {
        try {
            // Kiểm tra nếu module có lesson
            if ($module->lessons()->count() > 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Bạn phải xóa tất cả bài học trong chương này trước khi xóa.',
                    'data' => []
                ], 400);
            }

            // Nếu không có lesson -> del
            $module->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Xóa module thành công.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xóa module.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
