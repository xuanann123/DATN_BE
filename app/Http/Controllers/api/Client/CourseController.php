<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Course;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function listNewCourse()
    {
        try {
            $courses = Course::with([
                'category',
                'user',
                'tags'
                // Đếm số lượng khi lấy dữ liệu
            ])->withCount([
                        'modules as lessons_count' => function ($query) {
                            $query->whereHas('lessons');
                        },
                        'modules as quiz_count' => function ($query) {
                            $query->whereHas('quiz');
                        }
                    ])
                ->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->latest('created_at')
                ->orderByDesc('id')->limit(6)->get();

            $courses->each(function ($course) {
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

                $course->makeHidden('modules');
            });
            if (count($courses) < 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có khóa học mới nào'
                ], 204);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách khóa học mới',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lọc khóa học mới.' . $e,
                'data' => []
            ]);
        }
    }

    public function listCourseSale()
    {
        try {
            $courses = Course::with([
                'category',
                'user',
                'tags'
                // Đếm số lượng khi lấy dữ liệu
            ])->withCount([
                        'modules as lessons_count' => function ($query) {
                            $query->whereHas('lessons');
                        },
                        'modules as quiz_count' => function ($query) {
                            $query->whereHas('quiz');
                        }
                    ])
                ->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->where('price_sale', '!=', null)
                ->orderByDesc('price_sale')->limit(6)->get();

            $courses->each(function ($course) {
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

                $course->makeHidden('modules');
            });
            if (count($courses) < 0) {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lọc khóa học giảm giá.' . $e,
                'data' => []
            ]);
        }
    }

    // Lấy khóa học nổi bật
    public function listCoursePopular(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);
            $courses = Course::with(['user:id,name,avatar'])
                ->where('is_active', 1)
                ->where('status', 'approved')
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

            // Kiểm tra nếu không có khóa học nào
            if ($courses->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có khóa học nổi bật'
                ], 204);
            }

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

                $course->makeHidden('modules');
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
            $categories = Category::where('is_active', 1)
            //Check xem có khoá học thì mí cho hiển thị danh mục đó
                ->whereHas('courses', function ($query) {
                    $query->where('is_active', 1)
                        ->where('status', 'approved');
                })
                ->with([
                    'courses' => function ($query) {
                        $query->where('is_active', 1)
                            ->where('status', 'approved')
                            ->withCount([
                                'modules as lessons_count' => function ($query) {
                                    $query->whereHas('lessons');
                                },
                                'modules as quiz_count' => function ($query) {
                                    $query->whereHas('quiz');
                                }
                            ])
                            ->with(['user:id,name,avatar'])
                            ->withCount('ratings')
                            ->withAvg('ratings', 'rate')
                            ->limit(4);
                    }
                ])->get();

            if (count($categories) < 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chưa có danh mục nào',
                    "data" => []
                ], 204);
            }

            // Tính tổng số lesson, quiz và duration
            foreach ($categories as $category) {
                foreach ($category->courses as $course) {
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
                    $course->makeHidden('modules');
                }
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
    public function listFavoriteCourse()
    {
        $user = Auth::user();
        //phân trang 6 bản ghi
        try {
            $courses = $user->wishlists()->with('modules', 'user')->withAvg('ratings', 'rate')
                ->withCount([
                    'modules as lessons_count' => function ($query) {
                        $query->whereHas('lessons');
                    },
                    'modules as quiz_count' => function ($query) {
                        $query->whereHas('quiz');
                    }
                ])->paginate(6);

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
                //Lấy tiến độ của của khoá này 
                $progress = DB::table('user_courses')
                    ->where('id_course', $course->id)
                    ->where('id_user', $user->id)
                    ->first();
                $course['progress_percent'] = $progress->progress_percent ?? 0;
                $course->makeHidden('modules');
            }


            return response()->json([
                "status" => "success",
                "message" => "Danh sách yêu thích",
                "data" => $courses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Lỗi trong quá trình lấy danh sách yêu thích",
                "error" => $e->getMessage()
            ], 500);
        }

    }
    public function favoriteCourse($id_course)
    {
        $course = Course::findOrFail($id_course);
        //Đi yêu thích 1 khoá học

        $user = Auth::user();
        //Kiểm tra khoá học này đã được yêu thích từ trước chauw
        if ($user->wishlists()->where('id_course', $course->id)->exists()) {
            return response()->json([
                "status" => "error",
                "message" => "Khoá học này đã được yêu thích từ trước.",
                "data" => []
            ], status: 404);
        }
        $user->wishlists()->attach($course->id);

        return response()->json([
            "status" => "success",
            "message" => "Yêu thích khoá học thành công.",
            "data" => []
        ], 201);

    }
    public function checkFavoriteCourse($id_course)
    {
        $course = Course::findOrFail($id_course);
        //Đi yêu thích 1 khoá học

        $user = Auth::user();
        //Kiểm tra khoá học này đã được yêu thích từ trước chauw
        if ($user->wishlists()->where('id_course', $course->id)->exists()) {
            return response()->json([
                'message' => 'Đã yêu thích khoá học',
                'data' => [
                    'action' => 'unfavorite'
                ]
            ]);
        } else {
            return response()->json([
                'message' => 'Chưa yêu thích khoá học',
                'data' => [
                    'action' => 'favorite'
                ]
            ]);
        }
    }


    public function unfavoriteCourse($id_course)
    {
        $course = Course::findOrFail($id_course);
        //Đi yêu thích 1 khoá học
        $user = Auth::user();
        $user->wishlists()->detach($course->id);
        return response()->json([
            "status" => "success",
            "message" => "Bỏ yêu thích khoá học thành công.",
            "data" => []
        ], 200);
    }

}
