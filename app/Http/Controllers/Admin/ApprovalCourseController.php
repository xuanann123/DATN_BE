<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\AdminReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Approvals\CourseApproveEmail;
use App\Mail\Approvals\CourseRejectionEmail;
use App\Notifications\Client\Instructor\CourseApprovalStatusNotification;

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
            ->with('admin_review')
            ->whereNotNull('submited_at')
            ->where('status', '!=', 'draft')
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
            'all' => Course::whereNotNull('submited_at')->where('status', '!=', 'draft')->count(),
            'approved' => Course::where('status', 'approved')->count(),
            'pending' => Course::where('status', 'pending')->count(),
            'rejected' => Course::where('status', 'rejected')->count(),
        ];

        return view('admin.course_censors.index', compact('title', 'listAct', 'courses', 'count'));
    }

    public function show($id)
    {
        //Hiển thị những thông tin như bên khoá detail
        $title = 'Phê duyệt khóa học';
        $course = Course::with(
            'category',
            'user',
            'modules.lessons'
        )
            ->where('status', '!=', 'draft')
            ->findOrFail($id);

        $maxModulePosition = Module::where('id_course', $course->id)->max('position');

        // Tổng thời gian video của tất cả bài học vid trong khóa học
        $totalDurationVideo = $course->total_duration_video = $course->modules->flatMap(function ($module) {
            return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                return $lesson->lessonable->duration ?? 0;
            });
        })->sum();
        // Chuyển đổi sang định dạng X giờ, Y phút
        $hours = floor($totalDurationVideo / 3600);
        $minutes = ceil(($totalDurationVideo % 3600) / 60);
        $totalDurationVideo = trim(($hours ? $hours . ' Giờ ' : '') . ($minutes ? $minutes . ' Phút' : ''));

        $lecturesCount = $course->modules->sum(function ($module) {
            return $module->lessons->whereIn('content_type', ['document', 'video'])->count();
        });

        $quizzesCount = $course->modules->sum(function ($module) {
            return $module->quiz ? 1 : 0;
        });

        $conditions = $this->getCourseConditions($course);
        //Điểm nổi bật
        $goals = $course->goals;
        $requirements = $course->requirements;
        $audiences = $course->audiences;
        //Lấy ra phần danh sách đánh giá của khoá học này
        $ratings = $course->ratings;

        return view('admin.course_censors.detail', compact('title', 'course', 'totalDurationVideo', 'lecturesCount', 'quizzesCount', 'maxModulePosition', 'conditions', 'goals', 'requirements', 'audiences', 'ratings'));
    }

    public function approve(Request $request)
    {
        DB::beginTransaction();
        try {
            $course = Course::findOrFail($request->id);

            $user = User::find($course->id_user);

            // Lấy điều kiện khóa học
            $conditions = $this->getCourseConditions($course);
            // Kiểm tra xem có điều kiện nào không đạt
            $hasFailedConditions = collect($conditions)->contains(fn($condition) => !$condition['status']);

            // Nếu có điều kiện không đạt và người dùng cố gắng phê duyệt, trả về lỗi
            if ($request->has('approval') && $hasFailedConditions) {
                return redirect()->back()->with('error', 'Không thể chấp thuận khóa học vì có điều kiện chưa đạt yêu cầu.');
            }

            // Chấp thuận khóa học
            if ($request->has('approval')) {
                $course->status = 'approved';
                $message = 'Đã chấp thuận khóa học.';
                AdminReview::updateOrCreate(
                    [
                        'reviewable_id' => $course->id,
                        'reviewable_type' => Course::class,
                    ],
                    [
                        'user_id' => auth()->id(), // admin hiện tại đăng nhập
                        'action' => 'approved',
                        'admin_comments' => $request->admin_comments ?? $message,
                    ]
                );
                // Gửi email cho giảng viên khi chấp thuận
                Mail::to($user->email)->queue(new CourseApproveEmail($course));
                // Gửi thông báo cho giảng viên khi chấp thuận
                $user->notify(new CourseApprovalStatusNotification($course, $course->status));
            }

            // Xử lý từ chối
            if ($request->has('reject')) {
                $course->status = 'rejected';
                $message = 'Đã từ chối khóa học.';
                AdminReview::updateOrCreate(
                    [
                        'reviewable_id' => $course->id,
                        'reviewable_type' => Course::class,
                    ],
                    [
                        'user_id' => auth()->id(), // admin hiện tại đăng nhập
                        'action' => 'rejected',
                        'admin_comments' => $request->admin_comments ?? 'Khóa học chưa đạt yêu cầu.',
                    ]
                );
                // lấy những điều kiện không đạt
                $failedConditions = collect($conditions)->filter(fn($condition) => !$condition['status'])->values()->all();

                // Gửi email cho giảng viên khi từ chối
                Mail::to($user->email)->queue(new CourseRejectionEmail($course, $conditions, $course->admin_review->admin_comments));
                // Gửi thông báo cho giảng viên khi từ chối
                $user->notify(new CourseApprovalStatusNotification($course, $course->status, $failedConditions, $course->admin_review->admin_comments));
            }

            $course->save();

            DB::commit();

            return redirect()->route('admin.approval.courses.list')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
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

    private function getCourseConditions($course)
    {
        $conditions = [
            [
                'label' => 'Có ít nhất 4 mục tiêu cho học viên sau khi hoàn thành khóa học.',
                'value' => $course->goals->count(),
                'required' => 4
            ],
            [
                'label' => 'Có ít nhất 1 yêu cầu hoặc điều kiện tiên quyết cho học viên khi tham gia khóa học.',
                'value' => $course->requirements->count(),
                'required' => 1
            ],
            [
                'label' => 'Có ít nhất 1 thông tin về học viên mục tiêu của khóa học.',
                'value' => $course->audiences->count(),
                'required' => 1
            ],
            [
                'label' => 'Có ít nhất 2 chương học trong chương trình giảng dạy.',
                'value' => $course->modules->count(),
                'required' => 2
            ],
            [
                'label' => 'Có ít nhất 2 bài học trong chương trình giảng dạy.',
                'value' => $course->modules->sum(fn($module) => $module->lessons->count()),
                'required' => 2
            ],
            [
                'label' => 'Tất cả các chương đều có bài tập.',
                'value' => $course->modules->filter(fn($module) => $module->quiz)->count(),
                'required' => $course->modules->count()
            ],
            [
                'label' => 'Tổng thời gian của tất cả bài học video có ít nhất 30 phút.',
                'value' => $totalDurationVideo = ceil($course->modules->flatMap(
                    fn($module) =>
                    $module->lessons->where('content_type', 'video')->map(fn($lesson) => $lesson->lessonable->duration ?? 0)
                )->sum() / 60),
                'required' => 30
            ],
            [
                'label' => 'Xác định trình độ của khóa học.',
                'value' => $course->level ? 1 : 0,
                'required' => 1
            ],
            [
                'label' => 'Mô tả khóa học dài ít nhất 200 kí tự.',
                'value' => strlen($course->description),
                'required' => 200
            ],
            [
                'label' => 'Có ảnh bìa của khóa học.',
                'value' => $course->thumbnail ? 1 : 0,
                'required' => 1
            ],
            // Kiểm tra tất cả các chương đều có ít nhất 1 bài học
            [
                'label' => 'Tất cả các chương đều có ít nhất 1 bài học.',
                'value' => $course->modules->filter(fn($module) => $module->lessons->isNotEmpty())->count(),
                'required' => $course->modules->count()
            ],
            // Kiểm tra tất cả các quiz đều có ít nhất 1 câu hỏi
            [
                'label' => 'Tất cả bài tập đều có ít nhất 1 câu hỏi.',
                'value' => $course->modules->filter(function ($module) {
                    return $module->quiz && $module->quiz->questions->count() > 0;
                })->count(),
                'required' => $course->modules->filter(fn($module) => $module->quiz)->count(),
            ]
        ];

        foreach ($conditions as &$condition) {
            $condition['status'] = $condition['value'] >= $condition['required'];
        }

        return $conditions;
    }
}
