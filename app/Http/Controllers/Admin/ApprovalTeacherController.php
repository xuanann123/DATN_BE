<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApprovalTeacherController extends Controller
{
    public function index()
    {
        $title = "Kiểm duyệt giảng viên";
        $listStudent = User::with('profile.education')->where('status', User::STATUS_PENDING)->get();
        // dd($listStudent);
        return view('admin.teachers.index', compact('title', 'listStudent'));
    }
    public function show($id)
    {
        $user = User::find($id);
        return view('admin.teachers.detail', compact('user'));
    }
}
