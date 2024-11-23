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
        $totalRevenue = Bill::sum('total_coin_after_discount') * 1000;
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
                    DB::raw('SUM(bills.total_coin_after_discount * 1000) as total_revenue')
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

            // Khởi tạo mảng doanh thu theo khóa học
            $coursesRevenues = [];
            foreach ($revenueData as $data) {
                $coursesRevenues[$data->name][$data->day] = $data->total_revenue;
            }

            $datasets = [];
            foreach ($coursesRevenues as $courseName => $monthlyData) {
                $revenue = [];
                foreach ($times as $date) {
                    $revenue[] = $monthlyData[$date] ?? 0; // Gán giá trị 0 nếu ngày không có doanh thu
                }
                $datasets[] = [
                    'label' => $courseName,
                    'data' => $revenue,
                    'borderColor' => sprintf('rgba(%d, %d, %d, 1)', rand(50, 255), rand(50, 255), rand(50, 255)),
                    'backgroundColor' => 'rgba(0, 0, 0, 0)',
                    'borderWidth' => 1
                ];
            }

            return view('admin.charts.revenue', compact('title', 'totalRevenue', 'countCourses', 'countOrders', 'totalRevenues', 'revenueData', 'times', 'datasets', 'start_date', 'end_date'));

        }

        $year = 2024;
        $countOrders2024 = Bill::whereYear('created_at', $year)->count(); // Đếm số lượng orders trong năm 2024
        $totalRevenue2024 = Bill::whereYear('created_at', $year)->sum('total_coin_after_discount') * 1000; // Tính tổng tiền trong năm 2024


        // Doanh thu trong biểu đồ;
        $revenueData = DB::table('bills')
            ->join('courses', 'bills.id_course', '=', 'courses.id')
            ->select(
                DB::raw('MONTH(bills.created_at) as month'),
                'courses.name',
                DB::raw('SUM(bills.total_coin_after_discount * 1000) as total_revenue') // Tính doanh thu
            )
            ->whereYear('bills.created_at', 2024)
            ->groupBy(DB::raw('MONTH(bills.created_at)'), 'courses.name')
            ->orderBy('month') // Sắp xếp theo tháng
            ->get();


        $times = range(1, 12); // Tạo danh sách tháng từ 1 đến 12
        $coursesRevenues = []; // Khởi tạo mảng doanh thu theo khóa học

        foreach ($revenueData as $data) {
            $coursesRevenues[$data->name][$data->month] = $data->total_revenue;
        }

        // Chuẩn bị dữ liệu cho từng khóa học
        $datasets = [];
        foreach ($coursesRevenues as $courseName => $monthlyData) {
            $revenue = [];
            foreach ($times as $month) {
                $revenue[] = $monthlyData[$month] ?? 0; // Gán giá trị 0 nếu tháng không có doanh thu
            }
            $datasets[] = [
                'label' => $courseName,
                'data' => $revenue,
                'borderColor' => sprintf('rgba(%d, %d, %d, 1)', rand(50, 255), rand(50, 255), rand(50, 255)),
                'backgroundColor' => 'rgba(0, 0, 0, 0)',
                'borderWidth' => 1
            ];
        }

        return view('admin.charts.revenue', compact('title', 'totalRevenue', 'countCourses', 'countOrders2024', 'totalRevenue2024', 'revenueData', 'times', 'datasets'));
    }
}
