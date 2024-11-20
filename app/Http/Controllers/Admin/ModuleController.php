<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Modules\StoreModuleRequest;
use App\Http\Requests\Admin\Modules\UpdateModuleRequest;
use App\Models\Quiz;

class ModuleController extends Controller
{
    //Lưu module ở đây
    public function store(StoreModuleRequest $request)
    {
        $request->validated();

        Module::create([
            'id_course' => $request->id_course,
            'title' => $request->title,
            'description' => $request->description,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', 'Thêm chương học thành công !');
    }
    //Edit
    public function edit($id)
    {
        $module = Module::findOrFail($id);
        if ($module) {
            return response()->json($module);
        }
        return response()->json([
            'status' => 'error',
            "message" => "Không tìm thấy chương học",
            "data" => []
        ]);

    }
    //Sửa module
    public function update(UpdateModuleRequest $request, $id)
    {
        $module = Module::findOrFail($id);

        // Cập nhật module với dữ liệu đã được validate
        $module->update($request->validated());
        return redirect()->back()->with('success','Sửa chương học thành công');
    }
    public function storeQuiz(Request $request, string $id)
    {

        $idModule = Module::findOrFail($id);
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        // dd($idModule);

        // Tạo quiz
        $quiz = Quiz::create([
            'id_module' => $idModule->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('success', 'Thêm quiz thành công');
    }
    public function delete($id) {
        $module = Module::findOrFail($id);
        $module->delete();
        return redirect()->back()->with('success', 'Xoá chương học thành công');
    }

}
