<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Modules\StoreModuleRequest;
use App\Models\Quiz;

class ModuleController extends Controller
{
    public function store(StoreModuleRequest $request)
    {
        $request->validated();

        Module::create([
            'id_course' => $request->id_course,
            'title' => $request->title,
            'description' => $request->description,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', 'Thêm module thành công !');
    }
    public function storeQuiz(Request $request, string $id) {
        
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
            'total_points' => $request->input('total_points'),
        ]);

        return redirect()->back()->with('success', 'Quiz added successfully.');
    }

}
