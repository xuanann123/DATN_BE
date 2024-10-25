<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $courses = Course::with([
            'category',
            'user',
            'tags'
        ])->where('is_active', 1)->where('status', 'approved')->orderByDesc('price_sale')->limit(6)->get();
        $courses->each(function ($course) {
            // Tính tổng số lượng bài học trong khóa học
            $total_lessons = $course->modules->flatMap->lessons->count();
            // Set thời gian cho từng bài học (cần có hàm setLessonDurations)
            $this->setLessonDurations($course);
            $total_duration = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');
            //Cập nhật tổng số lượng bài học
            $course->total_lessons = $total_lessons;
            //Tổng thời gian của khoá học đó
            $course->total_duration = $total_duration;

        });
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
    private function setLessonDurations($course)
    {
        $course->modules->flatMap->lessons->map(function ($lesson) {
            if ($lesson->lessonable_type === Video::class) {
                $video = Video::find($lesson->lessonable_id);
                $lesson->duration = $video ? $video->duration : null;
            } else {
                $lesson->duration = null;
            }
        });
    }
}
