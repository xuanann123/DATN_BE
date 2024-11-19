<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Bill;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeFilter = $request->get('time', 'all');

            $statistics = $this->getStatistic($userId, $timeFilter);

            return response()->json([
                'status' => 200,
                'message' => 'Thống kê chung cho giảng viên.',
                'data' => $statistics,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy thống kê.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getStatistic($userId, $timeFilter)
    {
        // Thống kê tổng doanh thu
        $totalRevenue = Bill::query()
            ->where('id_user', $userId)
            ->when($timeFilter, fn($query) => $this->applyTimeFilter($query, $timeFilter))
            ->sum('total_coin_after_discount');
        ;

        // Doanh thu theo từng tháng
        $monthlyRevenueData = Bill::where('id_user', $userId)
            ->selectRaw('MONTH(created_at) as month, SUM(total_coin_after_discount) as revenue')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->keyBy('month');

        $monthlyRevenue = collect(range(1, 12))->mapWithKeys(function ($month) use ($monthlyRevenueData) {
            return [$month => $monthlyRevenueData->get($month)->revenue ?? 0];
        });

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
        $topCourses = Course::where('id_user', $userId)
            ->withCount(['bills' => fn($query) => $this->applyTimeFilter($query, $timeFilter)])
            ->orderBy('bills_count', 'desc')
            ->limit(5)
            ->get();

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
}
