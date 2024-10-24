<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function listNewCourse() {
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
            ->where('u.is_active', 1)
            ->where('u.user_type', 'teacher')
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail',  'c.price','c.price_sale', 'c.total_student', 'c.duration')
            ->orderByDesc('c.created_at')
            ->limit(3)
            ->get();

        if (count($courses) == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có khóa học'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách khóa học mới nhất',
            'data' => $courses,
        ], 200);
    }

    public function listCourseSale() {
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
            ->where('u.is_active', 1)
            ->where('u.user_type', 'teacher')
            ->where('c.price_sale', '>' , 0)
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail',  'c.price','c.price_sale', 'c.total_student', 'c.duration')
            ->orderByDesc('c.price_sale')
            ->limit(3)
            ->get();

        if (count($courses) == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có khóa học'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách khóa học giảm giá',
            'data' => $courses,
        ], 200);
    }
}
