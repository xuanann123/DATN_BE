<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Bill;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Rating;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeFilter = $request->get('time', 'all');

            $statistics = $this->getStatistic($userId, $timeFilter);

            return response()->json([
                'status' => 'success',
                'message' => 'Thống kê chung cho giảng viên.',
                'data' => $statistics,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thống kê.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStudents(Request $request)
    {
        try {
            $userId = auth()->id(); // id giang vien dang login
            // số bản ghi muốn hiển thị trên 1 trang, nếu không có thì mặc định là 8
            $limit = $request->get('limit', 8);
            $courseId = $request->get('course');

            $query = UserCourse::query();
            $this->filterByCourse($query, $userId, $courseId);

            $students = $query->with(['user:id,name,avatar', 'course:id,name,thumbnail'])
                ->latest('created_at')
                ->paginate($limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách học viên.',
                'data' => [
                    'total_students' => $students->count(),
                    'students' => $students
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách học viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRatings(Request $request)
    {
        try {
            $userId = auth()->id(); // id giang vien dang login
            // số bản ghi muốn hiển thị trên 1 trang, nếu không có thì mặc định là 8
            $limit = $request->get('limit', 8);
            $courseId = $request->get('course');

            $query = Rating::query();
            $this->filterByCourse($query, $userId, $courseId);


            $ratings = $query->with(['user:id,name,avatar', 'course:id,name,thumbnail'])
                ->latest('created_at')
                ->paginate($limit);
            ;

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách đánh giá.',
                'data' => [
                    'total_ratings' => $ratings->count(),
                    'ratings' => $ratings
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách học viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getStatistic($userId, $timeFilter)
    {
        // Lấy danh sách các id khóa học của giảng viên đang đăng nhập
        $instructorCourseIds = Course::where('id_user', $userId)->pluck('id');
        // Thống kê tổng doanh thu
        $totalRevenue = Bill::query()
            ->whereIn('id_course', $instructorCourseIds)
            ->when($timeFilter, fn($query) => $this->applyTimeFilter($query, $timeFilter))
            ->sum('total_coin_after_discount');
        ;

        // Doanh thu theo từng tháng (12 tháng gần nhất)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyRevenueData = Bill::query()
            ->whereIn('id_course', $instructorCourseIds)
            ->whereBetween('created_at', [
                now()->subMonths(11)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_coin_after_discount) as revenue')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC')
            ->get()
            ->keyBy(fn($item) => $item->month);

        $monthlyRevenue = collect(range(0, 11))->mapWithKeys(function ($offset) use ($currentMonth, $currentYear, $monthlyRevenueData) {
            $date = now()->subMonths($offset);
            $yearMonthKey = $date->month;

            return [
                $date->format('m') => $monthlyRevenueData->get($yearMonthKey)?->revenue ?? 0
            ];
        })->reverse();

        // Thống kê tổng số học viên
        $totalStudents = UserCourse::query()
            ->whereHas('course', fn($query) => $query->where('id_user', $userId))
            ->when($timeFilter, fn($query) => $this->applyTimeFilter($query, $timeFilter))
            ->distinct('id_user')
            ->count();
        ;

        // Thống kê tổng số khóa học
        $totalCourses = Course::where('id_user', $userId)
            ->when($timeFilter, fn($query) => $this->applyTimeFilter($query, $timeFilter))
            ->count();

        // Top khóa học bán chạy nhất
        $topCourses = Course::with('category')
            ->withCount('ratings')
            ->where('id_user', $userId)
            ->where('status', 'approved')
            ->withCount(['bills' => fn($query) => $this->applyTimeFilter($query, $timeFilter)])
            ->orderBy('bills_count', 'desc')
            ->limit(4)
            ->get();

        foreach ($topCourses as $topCourse) {
            $topCourse->ratings_avg_rate = round($topCourse->ratings->avg('rate'), 1);
        }

        $topCourses->makeHidden('ratings');

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'total_students' => $totalStudents,
            'total_courses' => $totalCourses,
            'top_courses' => $topCourses,
        ];
    }

    private function applyTimeFilter($query, $timeFilter)
    {
        $query->when($timeFilter && $timeFilter !== 'all', function ($query) use ($timeFilter) {
            $date = now();
            match ($timeFilter) {
                'today' => $query->whereDate('created_at', $date),
                'yesterday' => $query->whereDate('created_at', $date->subDay()),
                'this_week' => $query->whereBetween('created_at', [
                    $date->startOfDay()->toDateTimeString(),
                    $date->endOfDay()->toDateTimeString()
                ]),
                'this_month' => $query->whereMonth('created_at', $date->month),
                'this_year' => $query->whereYear('created_at', $date->year),
                default => null,
            };
        });
    }

    private function filterByCourse($query, $userId, $courseId = null)
    {
        $query->whereHas('course', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        });

        if ($courseId) {
            $query->where('id_course', $courseId);
        }

        return $query;
    }
}
