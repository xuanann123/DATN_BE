<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function listNewCourse()
    {
        $courses = DB::table('courses as c')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                c.id as course_id,
                c.name as course_name,
                c.thumbnail as course_thumbnail,
                c.price,
                c.price_sale,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as course_duration,
                c.created_at as course_created_at,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->join('users as u', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->where('c.is_active', 1)
            ->where('c.status', 'approved')
            ->where('u.is_active', 1)
            ->where('u.user_type', 'teacher')
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail', 'c.price', 'c.price_sale', 'c.total_student', 'c.duration')
            ->orderByDesc('c.created_at')
            ->limit(3)
            ->get();

        if (count($courses) == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có khóa học'
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách khóa học mới nhất',
            'data' => $courses,
        ], 200);
    }

    public function listCourseSale()
    {
        $courses = DB::table('courses as c')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                c.id as course_id,
                c.name as course_name,
                c.thumbnail as course_thumbnail,
                c.price,
                c.price_sale,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as course_duration,
                c.created_at as course_created_at,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->join('users as u', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->where('c.is_active', 1)
            ->where('c.status', 'approved')
            ->where('u.is_active', 1)
            ->where('u.user_type', 'teacher')
            ->where('c.price_sale', '>', 0)
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail', 'c.price', 'c.price_sale', 'c.total_student', 'c.duration')
            ->orderByDesc('c.price_sale')
            ->limit(3)
            ->get();

        if (count($courses) == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có khóa học'
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách khóa học giảm giá',
            'data' => $courses,
        ], 200);
    }

    // Lấy khóa học nổi bật
    public function listCoursePopular(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            // Lấy các khóa học nổi bật dựa vào số lượt mua và đánh giá trung bình
            $courses = Course::with(['user:id,name,avatar'])
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->orderByDesc('total_student')
                ->orderByDesc('ratings_avg_rate')
                ->limit($limit)
                ->get();

            // Kiểm tra nếu không có khóa học nào
            if ($courses->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có khóa học nổi bật'
                ], 204);
            }

            // Trả về danh sách khóa học nổi bật
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách khóa học nổi bật',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình lấy danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Lấy khoá học theo học theo tất cả danh mục
    public function getAllCourseByCategory()
    {
        //Danh sách category
        try {
            $categories = Category::with([
                'courses' => function ($query) {
                    $query->where('is_active', 1)->where('status', 'approved')->limit(4);
                }
            ])->where('is_active', 1)->get();
            if (count($categories) < 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chưa có danh mục nào',
                    "data" => []
                ], 204);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy được danh sách khoá học theo danh mục',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình lấy danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
