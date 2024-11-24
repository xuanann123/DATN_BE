<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Bill::sum('total_coin_after_discount') * 1000;
        $countTeachers = User::where('user_type', 'teacher')->count();
        $countCourses = Course::where('status', 'approved')->count();
        $countStudents = User::where('user_type', 'member')->count();


        $year = 2024;

        $countOrders2024 = Bill::whereYear('created_at', $year)->count(); // Đếm số lượng orders trong năm 2024
        $totalRevenue2024 = Bill::whereYear('created_at', $year)->sum('total_coin_after_discount') * 1000; // Tính tổng tiền trong năm 2024

        // Top 5 giảng viên nổi bật;
        $topInstructors = DB::table('users')
            ->join('courses', 'users.id', '=', 'courses.id_user')
            ->join('bills', 'courses.id', '=', 'bills.id_course')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                DB::raw('SUM(bills.id) as total_sales'),
                DB::raw('SUM(bills.total_coin_after_discount) as total_revenue')
            )
            ->whereIn('users.user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN])
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // Top 5 khóa học nổi bật
        $topCourses = DB::table('courses')
            ->join('bills', 'courses.id', '=', 'bills.id_course')
            ->join('users', 'courses.id_user', '=', 'users.id')
            ->leftJoin('ratings', 'courses.id', '=', 'ratings.id_course')
            ->select(
                'courses.id as course_id',
                'courses.name as course_name',
                'courses.thumbnail as course_thumbnail',
                'users.name as author_name',
                DB::raw('COUNT(bills.id) as total_sales'), // Tổng số lượt bán
                DB::raw('SUM(bills.total_coin_after_discount) as total_revenue'), // Tổng doanh thu
                DB::raw('COUNT(ratings.id) as total_ratings'), // Số lượng đánh giá
                DB::raw('AVG(ratings.rate) as average_rating') // Điểm đánh giá trung bình
            )
            ->groupBy('courses.id', 'courses.name', 'courses.thumbnail', 'users.name')
            ->orderByDesc('total_sales') // Sắp xếp theo tổng số bán
            ->take(5) // Lấy 5 khóa học bán chạy nhất
            ->get();

        // Doanh thu trong biểu đồ;
        $revenueData = DB::table('bills')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_coin_after_discount * 1000) as total_revenue')
            )
            ->whereYear('created_at', 2024)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month') // Sắp xếp theo tháng
            ->get();
        //Tổng số lượng người đăng kí khoá học theo tháng thông qua bảng user_courses
        $countOrders = DB::table('user_courses')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->whereYear('created_at', 2024)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // Định dạng dữ liệu để phù hợp với biểu đồ
        $months = [];
        $revenues = [];

        foreach ($revenueData as $data) {
            $months[] = $data->month;
            $revenues[] = $data->total_revenue;

        }

        // Chuyển dữ liệu sang dạng JSON để sử dụng trong JavaScript
        $monthsJson = json_encode($months);
        $revenuesJson = json_encode($revenues);
        return view('admin.dashboard', data: compact('totalRevenue', 'countTeachers', 'countCourses', 'countStudents', 'countOrders2024', 'totalRevenue2024', 'topInstructors', 'topCourses',  'monthsJson', 'revenuesJson'));
    }
}
