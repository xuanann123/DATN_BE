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
    public function index(Request $request)
    {
        $totalRevenue = Bill::sum('total_coin') * 1000;
        $totalProfits = Bill::sum('total_coin') * 1000 * 0.3;
        $countTeachers = User::where('user_type', 'teacher')->count();
        $countCourses = Course::where('status', 'approved')->count();
        $countStudents = User::where('user_type', 'member')->count();

        // Top 5 giảng viên nổi bật;
        $topInstructors = DB::table('users')
            ->join('courses', 'users.id', '=', 'courses.id_user')
            ->join('bills', 'courses.id', '=', 'bills.id_course')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                DB::raw('SUM(bills.id) as total_sales'),
                DB::raw('SUM(bills.total_coin) as total_revenue')
            )
            ->whereIn('users.user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN])
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('total_sales')
            ->take(10)
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
                DB::raw('SUM(bills.total_coin) as total_revenue'), // Tổng doanh thu
                DB::raw('COUNT(ratings.id) as total_ratings'), // Số lượng đánh giá
                DB::raw('AVG(ratings.rate) as average_rating') // Điểm đánh giá trung bình
            )
            ->groupBy('courses.id', 'courses.name', 'courses.thumbnail', 'users.name')
            ->orderByDesc('total_sales') // Sắp xếp theo tổng số bán
            ->take(10) // Lấy 5 khóa học bán chạy nhất
            ->get();

        // Doanh thu trong biểu đồ;
        $sortCriteria = $request->criteria;

        $query = DB::table('bills')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_coin * 1000) as total_revenue'),
                DB::raw('(SUM(total_coin * 1000) * 0.3) as profit')
            )
            ->whereYear('created_at', 2024)
            ->groupBy(DB::raw('MONTH(created_at)'));

        switch ($sortCriteria) {
            case 'revenue_asc': // Sắp xếp doanh thu tăng dần
                $query->orderBy('total_revenue', 'asc');
                break;

            case 'revenue_desc': // Sắp xếp doanh thu giảm dần
                $query->orderBy('total_revenue', 'desc');
                break;

            default: // Mặc định sắp xếp theo doanh thu giảm dần
                $query->orderBy('total_revenue', 'desc');
                break;
        }
        $revenueData = $query->get();

        // Định dạng dữ liệu để phù hợp với biểu đồ
        $months = [];
        $revenues = [];
        $profits = [];

        foreach ($revenueData as $data) {
            $months[] = $data->month;
            $revenues[] = $data->total_revenue;
            $profits[] = $data->profit;
        }

        // Chuyển dữ liệu sang dạng JSON để sử dụng trong JavaScript
        $monthsJson = json_encode($months);
        $revenuesJson = json_encode($revenues);
        $profitsJson = json_encode($profits);
        return view('admin.dashboard', data: compact('totalRevenue', 'totalProfits', 'countTeachers', 'countCourses', 'countStudents', 'topInstructors', 'topCourses',  'monthsJson', 'revenuesJson', 'profitsJson'));
    }
}
