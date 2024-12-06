<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Approvals\RegisterAppoveEmail;
use App\Mail\Approvals\RegisterApproveFailEmail;
use App\Models\AdminReview;
use App\Models\User;
use App\Notifications\Client\Student\RegisterApproveTeacherNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            DB::beginTransaction();
            //Đi cập nhật thằng user sang trạng thái 
            $user = User::findOrFail($id);
            $reject = $request->input('reject');
            $admin_comments = $request->input('admin_comments') ? $request->input('admin_comments') : NULL;
            if ($reject) {

                AdminReview::updateOrCreate(
                    [
                        'reviewable_id' => $user->id,
                        'reviewable_type' => User::class,
                    ],
                    [
                        'user_id' => auth()->id(), // admin hiện tại đăng nhập
                        'action' => 'reject',
                        'admin_comments' => $request->admin_comments ?? 'Từ chối phê duyệt giảng viên',
                    ]
                );
                //Xử lý huỷ thằng này đi
                $user->update([
                    'status' => User::STATUS_REJECTED
                ]);
                Mail::to($user->email)->queue(new RegisterApproveFailEmail($user, $admin_comments));

                //Cái này cũng phải đi lưu thông báo cho bên phía client

                //Gửi cả gmail thông báo về việc bị từ chối
                return redirect()->route('admin.approval.teachers.list')->with('success', "Từ chối giảng viên thành công");
            }
            AdminReview::updateOrCreate(
                [
                    'reviewable_id' => $user->id,
                    'reviewable_type' => User::class,
                ],
                [
                    'user_id' => auth()->id(), // admin hiện tại đăng nhập
                    'action' => 'approve',
                    'admin_comments' => $request->admin_comments ?? 'Phê duyệt giảng viên thành công',
                ]
            );
            $user->update([
                'status' => User::STATUS_APPROVED,
                'user_type' => User::TYPE_TEACHER
            ]);
            //Thông báo đến người sử dụng mail này
            Mail::to($user->email)->queue(new RegisterAppoveEmail($user));
            // Gửi thông báo cho giảng viên khi chấp thuận
            $user->notify(new RegisterApproveTeacherNotification($user));
            DB::commit();
            return redirect()->route('admin.approval.teachers.list')->with('success', "Đã phê duyệt giảng viên");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.approval.teachers.index')->with('error', "Đã xảy ra lỗi trong quá trình thêm");
        }

    }
}
