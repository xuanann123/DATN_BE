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
            ->groupBy('c.id', 'c.name', 'c.thumbnail')
            ->get();

        if ($courses->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
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
        $teachers = User::where('user_type', 'teacher')
            ->where('created_at', '>=', $oneMonthAgo)->get();
        $data = [];
        // Map qua từng giảng viên và xử lý các thông tin cần thiết
        $teachers = $teachers->map(function ($teacher) {
            $user = Auth::user();
            $courses = $teacher->userCourses;

            // Kiểm tra xem user hiện tại đã follow giáo viên này chưa
            $follow = $user->following()->where("following_id", $teacher->id)->exists();

            // Tính tổng số comment và rating của tất cả các khóa học của giảng viên
            $total_comments = $courses->flatMap(function ($course) {
                return $course->comments;
            })->count();

            $total_ratings = $courses->flatMap(function ($course) {
                return $course->ratings;
            })->count();

            $data = [
                "id" => $teacher->id,
                "name" => $teacher->name,
                "email" => $teacher->email,
                "avatar" => $teacher->avatar,
                "is_active" => $teacher->is_active,
                "email_verified_at" => $teacher->email_verified_at,
                "verification_token" => $teacher->verification_token,
                "user_type" => $teacher->user_type,
                "created_at" => $teacher->created_at,
                "updated_at" => $teacher->updated_at,
                "total_courses" => $courses->count(),
                "total_comments" => $total_comments,
                "total_ratings" => $total_ratings,
            ];
            if ($user->id != $teacher->id) {
                $data["follow"] = $follow;
            }

            // Trả về dữ liệu của giảng viên dưới dạng mảng
            return $data;
        });

        // Sắp xếp giảng viên theo tổng số khóa học, bình luận, và rating cao nhất
        $sortedTeachers = $teachers->sortByDesc(function ($teacher) {
            return $teacher['total_courses'] + $teacher['total_comments'] + $teacher['total_ratings'];
        })->values();

        // Chuyển top 5 giảng viên thành đối tượng với key là chỉ số
        $topTeachers = $sortedTeachers->take(5)->mapWithKeys(function ($teacher, $index) {
            return [$index => $teacher];
        });

        // Trả về response dạng JSON với cấu trúc mong muốn
        return response()->json([
            "status" => "success",
            "message" => "Danh sách top 5 giảng viên theo tháng",
            "data" => $topTeachers
        ], 200);
    }

}
