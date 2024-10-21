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
        $prefixeChoice = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
        ];
        //Lấy quiz của module
        $quizzesModule = Quiz::where('id_module', $id)->get();
        return view('admin.quizzes.index', compact('title', "module", 'quizzesModule', 'prefixeChoice'));
    }
    public function store(Request $request)
    {
        dd($request->all());
    }
}
