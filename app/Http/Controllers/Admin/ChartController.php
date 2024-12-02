<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function chartRevenue(Request $request)
    {
        $title = "Thống kê doanh thu";
        $totalRevenue = Bill::sum('total_coin') * 1000;
        $profit = Bill::sum('total_coin') * 1000 * 0.3;
        $countCourses = Course::where('status', 'approved')->count();

        // Nếu có request từ form chọn ngày thống kê
        if($request->start_date && $request->end_date && $request->end_date >= $request->start_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $countOrders = Bill::whereDate('bills.created_at', '>=', $start_date)
                ->whereDate('bills.created_at', '<=', $end_date)->count(); // Đếm số lượng orders trong khoảng thời gian
            $totalRevenues = Bill::whereDate('bills.created_at', '>=', $start_date)
                    ->whereDate('bills.created_at', '<=', $end_date)->sum('total_coin_after_discount') * 1000; // Tính tổng tiền trong khoảng thời gian

            // Lấy dữ liệu thống kê
            $revenueData = DB::table('bills')
                ->join('courses', 'bills.id_course', '=', 'courses.id')
                ->select(
                    DB::raw('DATE(bills.created_at) as day'),
                    'courses.name',
                    DB::raw('SUM(bills.total_coin * 1000) as total_revenue'),
                    DB::raw('(SUM(total_coin * 1000) * 0.3) as profit')
                )
                ->where('bills.created_at', '>=', $start_date)
                ->where('bills.created_at', '<=', $end_date)
                ->groupBy(DB::raw('DATE(bills.created_at)'), 'courses.name')
                ->orderBy('day')
                ->get();

            $times = [];
            $currentDate = \Carbon\Carbon::parse($start_date);
            $endDate = \Carbon\Carbon::parse($end_date);

            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            $revenues = [];
            $profits = [];

            foreach ($revenueData as $data) {
                $revenues[] = $data->total_revenue;
                $profits[] = $data->profit;
            }

            // Chuyển dữ liệu sang dạng JSON để sử dụng trong JavaScript
            $timesJson = json_encode($times);
            $revenuesJson = json_encode($revenues);
            $profitsJson = json_encode($profits);

            return view('admin.charts.revenue', compact('title', 'totalRevenue', 'profit', 'countCourses', 'countOrders', 'totalRevenues', 'revenueData', 'timesJson', 'revenuesJson', 'profitsJson', 'start_date', 'end_date'));
        }

        $year = 2024;
        $countOrders2024 = Bill::whereYear('created_at', $year)->count(); // Đếm số lượng orders trong năm 2024
        $totalRevenue2024 = Bill::whereYear('created_at', $year)->sum('total_coin') * 1000; // Tính tổng tiền trong năm 2024


        // Doanh thu trong biểu đồ;
        $revenueData = DB::table('bills')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_coin * 1000) as total_revenue'),
                DB::raw('(SUM(total_coin * 1000) * 0.3) as profit')
            )
            ->whereYear('created_at', 2024)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();


        // Định dạng dữ liệu để phù hợp với biểu đồ
        $times = [];
        $revenues = [];
        $profits = [];

        foreach ($revenueData as $data) {
            $times[] = $data->month;
            $revenues[] = $data->total_revenue;
            $profits[] = $data->profit;
        }

        // Chuyển dữ liệu sang dạng JSON để sử dụng trong JavaScript
        $timesJson = json_encode($times);
        $revenuesJson = json_encode($revenues);
        $profitsJson = json_encode($profits);

        return view('admin.charts.revenue', compact('title', 'totalRevenue', 'profit', 'countCourses', 'countOrders2024', 'totalRevenue2024', 'revenueData', 'timesJson', 'revenuesJson', 'profitsJson'));
    }

    // Top khóa học có doanh thu cao;
    public function  chartCourses(Request $request)
    {
        $title = "Top khóa học có doanh thu cao";
        $totalRevenue = Bill::sum('total_coin') * 1000;
        $profit = Bill::sum('total_coin') * 1000 * 0.3;
        // Lấy của 2024 trước;

        $year = 2024;
        $countOrders2024 = Bill::whereYear('created_at', $year)->count(); // Đếm số lượng orders trong năm 2024
        $totalRevenue2024 = Bill::whereYear('created_at', $year)->sum('total_coin') * 1000; // Tính tổng tiền trong năm 2024


        // Lấy dữ liệu thống kê doanh thu và lợi nhuận theo khóa học
        $revenueData = DB::table('bills')
            ->join('courses', 'bills.id_course', '=', 'courses.id')
            ->select(
                'courses.id as course_id',
                'courses.name as course_name',
                DB::raw('SUM(bills.total_coin * 1000) as total_revenue'),
                DB::raw('(SUM(bills.total_coin * 1000) * 0.3) as profit')
            )
            ->whereYear('bills.created_at', 2024)
            ->groupBy('courses.id', 'courses.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Chuẩn bị dữ liệu cho biểu đồ
        $courseNames = [];
        $revenues = [];
        $profits = [];

        foreach ($revenueData as $data) {
            $courseNames[] = $data->course_name; // Tên khóa học
            $revenues[] = $data->total_revenue; // Doanh thu
            $profits[] = $data->profit;         // Lợi nhuận
        }

        // Chuyển dữ liệu sang dạng JSON để dùng trong biểu đồ
        $courseNamesJson = json_encode($courseNames);
        $revenuesJson = json_encode($revenues);
        $profitsJson = json_encode($profits);
        return view('admin.charts.top_courses', compact('title', 'totalRevenue', 'profit', 'countOrders2024', 'totalRevenue2024', 'courseNamesJson', 'revenuesJson', 'profitsJson'));
    }
}
