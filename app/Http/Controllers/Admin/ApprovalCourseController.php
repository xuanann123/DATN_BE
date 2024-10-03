<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApprovalCourseController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Phê duyệt khóa học';

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

        $courses = Course::query()
            ->whereNotNull('submited_at')
            ->when($status != 'all', function ($query) use ($status) {
                return match ($status) {
                    'pending' => $query->where('status', 'pending'),
                    'approved' => $query->where('status', 'approved'),
                    'rejected' => $query->where('status', 'rejected'),
                    default => $query
                };
            })
            ->get();

        $count = [
            'all' => Course::whereNotNull('submited_at')->count(),
            'approved' => Course::where('status', 'approved')->count(),
            'pending' => Course::where('status', 'pending')->count(),
            'rejected' => Course::where('status', 'rejected')->count(),
        ];

        return view('admin.course_censors.index', compact('title', 'listAct', 'courses', 'count'));
    }

    public function show($id)
    {
        $title = 'Phê duyệt khóa học';
        $course = Course::with(
            'category',
            'user',
            'modules.lessons'
        )->findOrFail($id);

        $maxModulePosition = Module::where('id_course', $course->id)->max('position');

        $lecturesCount = $course->modules->sum(function ($module) {
            return $module->lessons->whereIn('content_type', ['document', 'video'])->count();
        });

        $quizzesCount = $course->modules->sum(function ($module) {
            return $module->quiz ? 1 : 0;
        });

        return view('admin.course_censors.detail', compact('title', 'course', 'lecturesCount', 'quizzesCount', 'maxModulePosition'));
    }

    public function approve(Request $request)
    {
        try {
            $course = Course::findOrFail($request->id);

            $course->status = match (true) {
                $request->has('approval') => 'approved',
                $request->has('reject') => 'rejected',
                $request->has('disable') => 'rejected',
                $request->has('enable') => 'approved',
                default => $course->status,
            };

            $message = match (true) {
                $request->has('approval') => 'Đã chấp thuận khóa học',
                $request->has('reject') => 'Đã từ chối khóa học',
                $request->has('disable') => 'Đã vô hiệu hóa khóa học',
                $request->has('enable') => 'Đã kích hoạt lại khóa học',
                default => NULL,
            };

            $course->save();

            return redirect()->route('admin.approval.courses.list')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Lỗi phê duyệt khóa học: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function action(Request $request)
    {
        $listCheck = $request->listCheck;

        // dd($listCheck);

        // khong co ban ghi duoc chon
        if (!$listCheck) {
            return redirect()->route('admin.approval.courses.list')->with('error', 'Vui lòng chọn khóa học để thao tác');
        }

        $act = $request->act;
        if (!$act) {
            return redirect()->route('admin.approval.courses.list')->with('error', 'Vui lòng chọn hành động để thao tác');
        }

        $message = match ($act) {
            'approve' => function () use ($listCheck) {
                    Course::whereIn('id', $listCheck)->update(['status' => 'approved']);
                    return 'Phê duyệt thành công';
                },
            'reject' => function () use ($listCheck) {
                    Course::whereIn('id', $listCheck)->update(['status' => 'rejected']);
                    return 'Từ chối thành công';
                },
            'disable' => function () use ($listCheck) {
                    Course::whereIn('id', $listCheck)->update(['status' => 'rejected']);
                    return 'Vô hiệu hóa thành công';
                },
            'enable' => function () use ($listCheck) {
                    Course::whereIn('id', $listCheck)->update(['status' => 'approved']);
                    return 'Kích hoạt thành công';
                },
            default => fn() => 'Hành động không hợp lệ'
        };

        return redirect()->route('admin.approval.courses.list')->with('success', $message());
    }
}
