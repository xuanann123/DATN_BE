<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(string $id)
    {
        $module = Module::with(['quiz','course.user'])->findOrFail($id);
        // dd($module->quiz);
        $title = "Bài tập cuối chương";

        //Lấy toàn bộ câu hỏi đổ sang bên phải như sau
        $quizzes = Quiz::where('id_module', $id)->get();
        return view('admin.quizzes.index', compact('title', "module", 'quizzes'));
    }
    public function store(Request $request)
    {
        dd($request->all());
    }
}
