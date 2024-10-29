<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\StoreModuleRequest;
use App\Http\Requests\Client\Courses\UpdateModuleRequest;
use App\Http\Requests\Client\Modules\UpdateModulePositionsRequest;

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

    public function updateModulePosition(UpdateModulePositionsRequest $request, Course $course)
    {
        DB::beginTransaction();
        try {
            // Người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập vào khóa học này.',
                    'data' => []
                ], 403);
            }

            // update vị trí của các module
            foreach ($request->modules as $modulePosition) {
                $module = Module::find($modulePosition['id']);
                if ($module && $module->id_course === $course->id) {
                    $module->position = $modulePosition['position'];
                    $module->save();
                }
            }

            DB::commit();

            // Lấy danh sách module và sắp xếp theo position
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
                'status' => 'success',
                'message' => 'Vị trí các module đã được cập nhật thành công.',
                'data' => [
                    'modules' => $modules
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật vị trí module.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
