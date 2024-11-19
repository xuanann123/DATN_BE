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

        // Doanh thu theo từng tháng (12 tháng gần nhất)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyRevenueData = Bill::where('id_user', $userId)
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
