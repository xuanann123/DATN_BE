<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    // Api danh sach teacher;
    public function getTeachers(Request $request)
    {
        // Số thứ tự trang;
        $page = $request->page ??  1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 12;

        $teachers = DB::table('users as u')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                COUNT(DISTINCT c.id) as total_courses,
                COUNT(DISTINCT r.id) as total_ratings,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('courses as c', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->where('u.user_type', 'teacher')
            ->where('u.is_active', 1)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if($teachers->count() <= 0){
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ],204);
        }

        return response()->json([
            'status' => 'success',
            'data' => $teachers->items(),
            'current_page' => $teachers->currentPage(),
            'total_pages' => $teachers->lastPage(),
            'total_count' => $teachers->total(),
        ], 200);
    }

    public function teacherId($id)
    {
        $teacher = DB::table('users as u')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                COUNT(c.id) as total_courses,
                COUNT(r.id) as total_ratings,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('courses as c', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->where('u.user_type', 'teacher')
            ->where('u.is_active', 1)
            ->where('u.id', $id)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->first();

        return $teacher;
    }

    // Api chi tiet giang vien va danh sach khoa hoc cua giang vien do
    public function getCoursesTeacher(Request $request) {
        $id = $request->id;

        $teacher = $this->teacherId($id);

        if(!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher not found',
            ], 204);
        }

        $courses = DB::table('courses as c')
            ->selectRaw('
                c.id as course_id,
                c.name as course_name,
                c.thumbnail as course_thumbnail,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as course_duration,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->leftJoin('ratings as r', 'r.id_course', '=', 'c.id')
            ->leftJoin('users as u', 'u.id', '=', 'c.id_user')
            ->where('c.id_user', $id)
            ->where('c.is_active', 1)
            ->groupBy('c.id', 'c.name', 'c.thumbnail')
            ->get();

        if($courses->count() <= 0){
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ],204);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'dataCourses' => $courses,
                'dataTeacher' => $teacher,
            ]
        ], 200);
    }

    public function searchTeachers(Request $request)
    {
        // Số thứ tự trang;
        $page = $request->page ??  1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 12;

        // Lấy keyword ở url;
        $searchTerm = $request->key;

        $teachers = DB::table('users as u')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                COUNT(c.id) as total_courses,
                COUNT(r.id) as total_ratings,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('courses as c', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->where('u.user_type', 'teacher')
//            ->where('u.is_active', 1)
            ->where(function($query) use ($searchTerm) {
                $query->where('u.name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('c.name', 'LIKE', "%{$searchTerm}%");
            })
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if($teachers->count() <= 0){
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'data' => $teachers->items(),
            'current_page' => $teachers->currentPage(),
            'total_pages' => $teachers->lastPage(),
            'total_count' => $teachers->total(),
        ], 200);
    }
}
