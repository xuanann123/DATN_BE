<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $title = 'Danh sách khóa học';
        return view('admin.courses.index', compact('title'));
    }

    public function create()
    {
        $title = 'Thêm mới khóa học';
        return view('admin.courses.create', compact('title'));
    }

    public function detail()
    {
        $title = 'Chi tiết khóa học';
        return view('admin.courses.detail', compact('title'));
    }
}
