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
        //Số lượng giới hạn trên 1 trạng
        $limit = $request->input('limit', 12);
        //Lấy toàn bộ danh sách giảng viên

        $teachers = User::select('id', 'name', 'avatar')
            ->whereHas('courses' , function ($query) {
                $query->where('status', 'approved');
                $query->where("is_active", 1);
            })
            ->withCount([
                'courses as total_courses' => function ($query) {
                    $query->where('status', 'approved');
                    $query->where("is_active", 1);
                }
            ])
            ->whereIn('user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN])
            ->latest('id')
            ->orderBy('total_courses', 'desc')
            ->paginate($limit);


        //Duyệt qua từng giảng viên thêm những thuộc tính total_courses, total_ratings, average_rating
        foreach ($teachers as $teacher) {
            $total_rating = 0;
            $ratings_avg_rate = 0;

            //Lấy total của tất cả khoá học của giảng viên
            foreach ($teacher->courses as $course) {
                $total_rating += DB::table('ratings')->where('id_course', $course->id)->count();
                $ratings_avg_rate += DB::table('ratings')->where('id_course', $course->id)->avg('rate');
            }
            //Tổng số lượng sinh viên 

            $teacher->total_ratings = $total_rating;
            $teacher->ratings_avg_rate = number_format(round($ratings_avg_rate, 1), 1);
            $teacher->total_student = DB::table('user_courses')->where('id_user', $teacher->id)->count();
            $teacher->ratings_avg_rate = number_format($ratings_avg_rate, 1);
        }
        if ($teachers->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thấy dữ liệu',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Lấy danh sách giảng viên thành công",
            'data' => $teachers
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
            ->whereIn('u.user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN])
            ->where('u.is_active', 1)
            ->where('u.id', $id)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->first();

        return $teacher;
    }

    // Api chi tiet giang vien va danh sach khoa hoc cua giang vien do
    public function getCoursesTeacher(Request $request, $id)
    {

        $teacher = $this->teacherId($id);
        $totalStudent = 0;
        $totalFollower = 0;

        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tồn tại giảng viên',
            ], 204);
        }
        $limit = $request->input('limit', 5);
        // Lấy các khóa học nổi bật dựa vào số lượt mua và đánh giá trung bình
        $courses = Course::select('id', 'slug', 'name', 'thumbnail', 'price', 'price_sale', 'total_student', 'id_user')
            ->with('user')
            ->where('is_active', 1)
            ->whereHas('user', function ($query) {
                $query->whereIn('user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN]);
            })
            ->where('status', 'approved')
            ->where('id_user', $id)
            ->withCount('ratings')
            ->withAvg('ratings', 'rate')
            ->withCount([
                'modules as lessons_count' => function ($query) {
                    $query->whereHas('lessons');
                },
                'modules as quiz_count' => function ($query) {
                    $query->whereHas('quiz');
                }
            ])
            ->orderByDesc('total_student')
            ->orderByDesc('ratings_avg_rate')
            ->limit($limit)
            ->get();



        if ($courses->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có dữ liệu',
            ], 204);
        }
        //Lấy số lượng sinh viên đang tham gia khoá học này
        foreach ($courses as $course) {
            $totalStudent += DB::table('user_courses')->where('id_course', $course->id)->count();
        }


        //Lấy số lượng follow của giảng viên
        $follow = DB::table('follows')
            ->where('following_id', $id)
            ->count();
        $totalFollower = $follow;

        // Tính tổng số lesson, quiz và duration
        foreach ($courses as $course) {
            // Tính tổng lessons và quiz
            $total_lessons = $course->modules->flatMap->lessons->count();
            $total_quiz = $course->modules->whereNotNull('quiz')->count();
            $course->total_lessons = $total_lessons + $total_quiz;
            // Tính tổng duration của các lesson vid
            $course->total_duration_video = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                    return $lesson->lessonable->duration ?? 0;
                });
            })->sum();
            $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);
            $course->total_student = DB::table('user_courses')->where('id_course', $course->id)->count();

            $course->makeHidden('ratings');
            $course->makeHidden('modules');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'dataCourses' => $courses,
                'dataTeacher' => $teacher,
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
                u.id as id,
                u.name as name,
                u.avatar as avatar,
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
        // Danh sách teachers
        $teachers = User::select('id', 'name', 'avatar')
            ->whereHas('courses', function ($query) {
                // Thêm điều kiện để lấy các khoá học có status là 'course'
                $query->where('status', 'approved');
                $query->where("is_active", 1);

            })
            ->withCount([
                'courses as total_courses' => function ($query) {
                    $query->where('status', 'approved');
                    $query->where("is_active", 1);
                }
            ])

            //Lấy số lượng rating ra
            ->whereIn('user_type', [User::TYPE_TEACHER, User::TYPE_ADMIN])
            ->where('created_at', '>=', $oneMonthAgo)
            ->orderBy('total_courses', 'desc')
            ->limit(6)
            ->get();


        //Duyệt qua từng giảng viên thêm những thuộc tính total_courses, total_ratings, average_rating
        foreach ($teachers as $teacher) {
            $total_rating = 0;
            $ratings_avg_rate = 0;

            //Lấy total của tất cả khoá học của giảng viên
            foreach ($teacher->courses as $course) {
                $total_rating += DB::table('ratings')->where('id_course', $course->id)->count();
                $ratings_avg_rate += DB::table('ratings')->where('id_course', $course->id)->avg('rate');
            }

            $teacher->total_ratings = $total_rating;
            // $teacher->total_student = DB::table('user_courses')->where('id_user', $teacher->id)->count();
            $teacher->ratings_avg_rate = number_format($ratings_avg_rate, 1);
            $teacher->makeHidden('courses');
        }
        if ($teachers->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách giảng viên trong 1 tháng gần nhất',
            'data' => $teachers,
        ], 200);

    }

}
