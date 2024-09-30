<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalCourseController extends Controller
{
    public function index()
    {
        $title = 'Phê duyệt khóa học';
        return view('admin.course_censors.list', compact('title'));
    }

    public function show()
    {
        $title = 'Phê duyệt khóa học';
        return view('admin.course_censors.detail', compact('title'));
    }
}
