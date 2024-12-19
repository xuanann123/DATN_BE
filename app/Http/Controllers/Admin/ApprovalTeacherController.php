<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Approvals\RegisterAppoveEmail;
use App\Mail\Approvals\RegisterApproveFailEmail;
use App\Models\AdminReview;
use App\Models\User;
use App\Notifications\Client\Student\RegisterApproveFailNotification;
use App\Notifications\Client\Student\RegisterApproveTeacherNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApprovalTeacherController extends Controller
{
    public function index(Request $request)
    {
        $title = "Kiểm duyệt giảng viên";
        $status = $request->query('status', 'all');
        // Khởi tạo listAct ban đầu
        $listAct = match ($status) {
            'pending' => [
                'approve' => 'Phê duyệt',
                'reject' => 'Từ chối'
            ], // chờ phê duyệt
            'approved' => ['disable' => 'Vô hiệu hóa'], // đã phê duyệt
            'rejected' => ['enable' => 'Kích hoạt'], // vô hiệu hóa
            default => [], // default null (k co act)
        };


        $listStudent = User::with('profile.education')
            ->whereIn('user_type', [User::TYPE_MEMBER, User::TYPE_TEACHER])
            ->whereNotNull('status')
            ->when($status != 'all', function ($query) use ($status) {
                return match ($status) {
                    'pending' => $query->where('status', 'pending'),
                    'approved' => $query->where('status', 'approved'),
                    'rejected' => $query->where('status', 'rejected'),
                    default => $query
                };
            })->get();
        // dd($listStudent);
        $count = [
            "all" => User::whereNotNull('status')->count(),
            "pending" => User::where('status', 'pending')->count(),
            "approved" => User::where('status', 'approved')->count(),
            "rejected" => User::where('status', 'rejected')->count(),
        ];
        return view('admin.teachers.index', compact('title', 'listStudent', 'listAct', 'count'));
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
                //Không xác nhận
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
                //Thông báo đăng kí thất bại
                $user->notify(new RegisterApproveFailNotification($user));
                DB::commit();

                return redirect()->route('admin.approval.teachers.list')->with('success', "Từ chối giảng viên thành công");
            }
            //Xác nhận
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
