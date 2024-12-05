<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Approvals\RegisterAppoveEmail;
use App\Models\User;
use App\Notifications\Client\Student\RegisterApproveTeacherNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApprovalTeacherController extends Controller
{
    public function index()
    {
        $title = "Kiểm duyệt giảng viên";
        $listStudent = User::with('profile.education')->whereNotNull('status')->get();
        // dd($listStudent);
        return view('admin.teachers.index', compact('title', 'listStudent'));
    }
    public function show($id)
    {
        $user = User::with('profile.education')->findOrFail($id);
        return view('admin.teachers.detail', compact('user'));
    }
    public function approve(Request $request, $id)
    {
        try {
            //Đi cập nhật thằng user sang trạng thái 
            $user = User::findOrFail($id);
            $user->update([
                'status' => User::STATUS_APPROVED,
                'user_type' => User::TYPE_TEACHER
            ]);
            //Thông báo đến người sử dụng mail này
            Mail::to($user->email)->queue(new RegisterAppoveEmail($user));
            // Gửi thông báo cho giảng viên khi chấp thuận
            $user->notify(new RegisterApproveTeacherNotification($user));
            return redirect()->route('admin.approval.teachers.list');
        } catch (\Exception $e) {
            return redirect()->route('admin.teachers.index')->with('error', "Không thể thêm được dữ liệu");
        }

    }
}
