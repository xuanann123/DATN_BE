<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    // Api danh sach teacher;
    public function getTeachers(Request $request)
    {
        // Số thứ tự trang;
        $page = $request->page ?? 1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 12;

        $idUserLogin = $request->user()->id;

        $teachers = DB::table('users as u')
            ->selectRaw('
                u.id,
                u.name,
                u.avatar,
                COUNT(DISTINCT c.id) as total_courses,
                COUNT(DISTINCT r.id) as total_ratings,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('courses as c', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->where('u.user_type', 'teacher')
            ->where('u.is_active', 1)
            ->where('u.id', '!=', $idUserLogin)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($teachers->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thấy dữ liệu',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'teachers' => $teachers->items(),
                'current_page' => $teachers->currentPage(),
                'total_pages' => $teachers->lastPage(),
                'total_count' => $teachers->total(),
            ]
        ], 200);
    }

    public function teacherId($id)
    {
        $teacher = DB::table('users as u')
            ->selectRaw('
                u.id,
                u.name,
                u.avatar,
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
    public function getCoursesTeacher(Request $request)
    {
        $id = $request->id;

        $teacher = $this->teacherId($id);
        $totalStudent = 0;
        $totalFollower = 0;

        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tồn tại giảng viên',
            ], 204);
        }
        $courses = DB::table('courses as c')
            ->selectRaw('
                c.id,
                c.name,
                c.thumbnail,
                c.slug,
                c.level,
                c.price,
                c.price_sale,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as total_duration_video,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->leftJoin('ratings as r', 'r.id_course', '=', 'c.id')
            ->leftJoin('users as u', 'u.id', '=', 'c.id_user')
            ->where('c.id_user', $id)
            ->where('c.is_active', 1)
            ->where('c.status', 'approved')
            ->groupBy('c.id', 'c.name', 'c.thumbnail')
            ->get();

        if ($courses->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
        }
        //Lấy số lượng sinh viên đang tham gia khoá học này
        foreach ($courses as $course) {
            $totalStudent += $course->total_student;
        }
        //Lấy số lượng follow của giảng viên
        $follow = DB::table('follows')
            ->where('following_id', $id)
            ->count();
        $totalFollower = $follow;

        return response()->json([
            'status' => 'success',
            'data' => [
                'dataCourses' => $courses,
                'dataTeacher' => $teacher,
                'rating' => $this->ratingTeacher($id),
                'totalStudent' => $totalStudent,
                'totalFollower' => $totalFollower
            ]
        ], 200);
    }

    public function ratingTeacher($id)
    {
        $teacher = User::find($id);
        $totalRatings = $teacher->ratings()->count();
        $averageRating = $teacher->ratings()->avg('rate');
        $rating = [
            'totalRatings' => $totalRatings,
            'averageRating' => $averageRating,
        ];
        return $rating;
    }

    public function searchTeachers(Request $request)
    {
        // Số thứ tự trang;
        $page = $request->page ?? 1;
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
            ->where('u.is_active', 1)
            ->where(function ($query) use ($searchTerm) {
                $query->where('u.name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('c.name', 'LIKE', "%{$searchTerm}%");
            })
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($teachers->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'teachers' => $teachers->items(),
                'current_page' => $teachers->currentPage(),
                'total_pages' => $teachers->lastPage(),
                'total_count' => $teachers->total(),
            ]
        ], 200);
    }

    public function listTeacherMonth(Request $request)
    {
        // Lấy danh sách giảng viên trong 1 tháng gần nhất
        $oneMonthAgo = Carbon::now()->subMonth();
        // Map qua từng giảng viên và xử lý các thông tin cần thiết

        $page = $request->page ?? 1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 12;

        $idUserLogin = $request->user()->id;

        $teachers = DB::table('users as u')
            ->selectRaw('
                u.id,
                u.name,
                u.avatar,
                COUNT(DISTINCT c.id) as total_courses,
                COUNT(DISTINCT r.id) as total_ratings,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->leftJoin('courses as c', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->where('u.user_type', 'teacher')
            ->where('u.is_active', 1)
            ->where('u.id', '!=', $idUserLogin)
            ->where('c.created_at', '>=', $oneMonthAgo)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($teachers->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thấy dữ liệu',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'teachers' => $teachers->items(),
                'current_page' => $teachers->currentPage(),
                'total_pages' => $teachers->lastPage(),
                'total_count' => $teachers->total(),
            ]
        ], 200);
        
    }

}
