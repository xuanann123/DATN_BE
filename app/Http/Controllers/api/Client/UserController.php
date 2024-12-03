<?php

namespace App\Http\Controllers\api\Client;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\User\UpdateProfileRequest;
use App\Http\Requests\Client\User\ChangePasswordRequest;
use App\Models\Course;
use App\Models\Education;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizProgress;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Video;
use Carbon\Carbon;
use Dompdf\FrameDecorator\Table;
use Flasher\Prime\EventDispatcher\Event\ResponseEvent;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // get pro5
        $profile = $user->profile;

        return response()->json([
            'message' => 'Thông tin người dùng.',
            'data' => [
                'user' => $user->makeHidden('profile'),
                'profile' => $profile,
            ],
            'status' => 200,
        ], 200);
    }

    public function showUser(User $user)
    {
        try {
            // get pro5
            $profile = $user->profile;

            return response()->json([
                'message' => 'Thông tin người dùng.',
                'data' => $user->load('profile'),
                'status' => 'success',
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình lấy thông tin người dùng.',
                'error' => $e->getMessage() . $e->getLine(),
                'data' => [],
                'status' => 'error',
            ], 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $data = $request->except(['avatar']);

            // update avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $newNameAvatar = 'avatar_' . time() . '.' . $avatar->getClientOriginalExtension();
                $pathAvatar = Storage::putFileAs('avatars', $avatar, $newNameAvatar, 'public');

                $data['avatar'] = $pathAvatar;

                $this->deleteOldAvatar($user->avatar);

                // update avt
                $user->update(['avatar' => $pathAvatar]);
            } elseif ($request->input('remove_avatar')) {
                $this->deleteOldAvatar($user->avatar);

                $user->update(['avatar' => null]);
            }

            // update name
            if ($request->has('name')) {
                $user->update([
                    'name' => $request->name
                ]);
            }

            // Update profile
            $user->profile()->updateOrCreate(
                ['id_user' => $user->id],
                $data
            );

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thông tin cá nhân thành công.',
                'data' => [
                    'user' => $user->makeHidden('profile'),
                    'profile' => $user->profile
                ],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình cập nhật thông tin.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Mật khẩu hiện tại không chính xác.',
                    'data' => [],
                    'status' => 400,
                ], 400);
            }

            // update new password
            $user->update(['password' => Hash::make($request->new_password)]);

            return response()->json([
                'message' => 'Mật khẩu đã được thay đổi thành công.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình thay đổi mật khẩu.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    // xoa anh cu
    private function deleteOldAvatar($oldAvatarPath)
    {
        if ($oldAvatarPath) {
            $fileExists = Storage::disk('public')->exists($oldAvatarPath);
            if ($fileExists) {
                Storage::disk('public')->delete($oldAvatarPath);
            }
        }
    }
    //Danh sách khoá học của tôi đã mua
    public function myCourseBought(Request $request)
    {
        $limit = $request->input('limit', 6);
        $authUser = Auth::user();
        $myCourseBought = $authUser->usercourses()->with('modules', 'user')
            ->withAvg('ratings', 'rate')
            ->withCount([
                'modules as lessons_count' => function ($query) {
                    $query->whereHas('lessons');
                },
                'modules as quiz_count' => function ($query) {
                    $query->whereHas('quiz');
                }
            ]);
        // Lọc theo level (nếu có)
        if ($request->filled('search')) {
            $myCourseBought->search($request->input('search'));
        }
        // Lọc theo level (nếu có)
        if ($request->filled('level')) {
            $myCourseBought->where('level', $request->input('level'));
        }

        // Lọc theo category (nếu có)
        if ($request->filled('category')) {
            $myCourseBought->where('id_category', $request->input('category'));
        }

        // Sắp xếp theo A-Z hoặc Z-A
        if ($request->filled('arrange')) {
            if ($request->input('arrange') === 'A-Z') {
                $myCourseBought->orderBy('name', 'asc');
            } elseif ($request->input('arrange') === 'Z-A') {
                $myCourseBought->orderBy('name', 'desc');
            }
        }
        // Phân trang kết quả
        $myCourseBought = $myCourseBought->paginate($limit);



        //Duyệt qua toàn bộ khoá học đó
        foreach ($myCourseBought as $course) {
            // Tính tổng lessons và quiz
            $total_lessons = $course->modules->flatMap->lessons->count();
            $total_quiz = $course->modules->whereNotNull('quiz')->count();
            $course->total_lessons = $total_lessons + $total_quiz;
            $totalDurationVideo = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                    return $lesson->lessonable->duration ?? 0;
                });
            })->sum();
            //Tính thời gian độc docs
            $totalDurationDocs = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                    $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                    return ceil(($wordCount / 200) * 60);
                });
            })->sum();
            //Tính tổng thời gian của video và docs
            $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;
            //Lấy tiến độ của của khoá này
            $progress = DB::table('user_courses')
                ->where('id_course', $course->id)
                ->where('id_user', $authUser->id)
                ->first();
            $course['progress_percent'] = $progress->progress_percent ?? 0;

            $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);
            $course->total_student = DB::table('user_courses')->where('id_course', $course->id)->count();


            $course->makeHidden('ratings');
            $course->makeHidden('modules');

        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Danh sách khoa học đã mua.',
                'data' => $myCourseBought,
            ]
            ,
            200
        );
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
    public function registerTeacher(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'certificates' => 'required|array',
            'certificates.*' => 'string', // Nếu lưu tên file, dùng string. Nếu file thực, dùng file validation.
            'qa_pairs' => 'required|array',
        ]);
        // Lưu dữ liệu vào database
        $education = Education::create([
            'id_profile' => $user->profile->id,
            'certificates' => $validatedData['certificates'], // Lưu mảng JSON
            'qa_pairs' => $validatedData['qa_pairs'], // Lưu key-value JSON
        ]);
        //Tiếp đó gửi dữ liệu này lên bên phía Admin thông báo để kiểm duyệt
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng kí trở thành giảng viên thành công.',
            'data' => []
        ], 200);
    }
    public function checkLearning(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 5);
        $data = [];
        $history = [];

        // Tính tổng số lượng bài học mà người dùng đã học

        // Tổng số lượng bài học đã học check thông qua thằng id_user và is_completed
        $completed_lessons = LessonProgress::where('id_user', $user->id)
            ->where('is_completed', 1)
            ->count();

        // Số lượng quiz đã hoàn thành check thông qua thằng id_user và is_completed
        $completed_quizzes = QuizProgress::where('user_id', $user->id)
            ->where('is_completed', 1)
            ->count();

        $totalLessons = $completed_lessons + $completed_quizzes;

        // Lấy danh sách bài học gần nhất dựa vào bảng lesson_progress có giới hạn
        $lessonProgress = DB::table('lesson_progress')->where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        //Lấy danh sách bài học quiz gần nhất dựa vào bảng quiz_progress
        $quizProgress = DB::table('quiz_progress')->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        // Duyệt qua để lấy những bài học này
        foreach ($lessonProgress as $key => $item) {
            $history = Lesson::find($item->id_lesson);
            $history['slug'] = $history->module->course->slug;
            $history['created_at'] = LessonProgress::find($item->id)->created_at;
            $history->makeHidden('module');
            $data[] = $history;
        }

        foreach ($quizProgress as $key => $item) {
            $history = Quiz::find($item->quiz_id);
            $history['slug'] = $history->module->course->slug;
            $history['created_at'] = QuizProgress::find($item->id)->created_at;
            $history->makeHidden('module');
            $data[] = $history;
        }
        //Duyệt qua data và sắp xếp cái thằng created_at
        $data = collect($data)
            ->sortByDesc('created_at')
            ->values()
            //Lấy số lượng bản ghi = $limit
            ->take($limit)
            ->toArray();

        // Check xem dữ liệu data rỗng thì trả về 204
        if (empty($data)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Bạn chưa có bài học gần nhất',
                'data' => [
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách bài học gần nhất',
            'data' => [
                'course' => $data,
                'total_lessons' => $totalLessons
            ],
        ], 200);
    }
    public function getUserByEmail(string $email)
    {
        try {

            //Lấy thằng user đó ra
            $user = User::with('profile')->where('email', $email)->first();


            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Không tồn tại user này trên hệ thống",
                    'data' => []
                ], 204);
            }
            //Lấy những khoá học của user đó đăng lên
            $coursesByUser = $user->courses()->select('id', 'name', 'slug', 'description', 'thumbnail', 'sort_description','price', 'price_sale','level')
            ->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->withCount([
                    'modules as lessons_count' => function ($query) {
                        $query->whereHas('lessons');
                    },
                    'modules as quiz_count' => function ($query) {
                        $query->whereHas('lessons', function ($query) {
                            $query->where('content_type', 'quiz');
                        });
                    }
                ])
                ->where('is_active', 1)
                ->where('status', Course::COURSE_STATUS_APPROVED)
                ->get();
                //Duyệt vòng lập những khóa học thằng này
                foreach ($coursesByUser as $course) {
                    $total_lessons = $course->modules->flatMap->lessons->count();
                    $total_quiz = $course->modules->flatMap->lessons->where('content_type', 'quiz')->count();
                    $course->total_lessons = $total_lessons + $total_quiz;

                    //Tính thời gian có sẵn lưu vào datatable
                    $totalDurationVideo = $course->modules->flatMap(function ($module) {
                        return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                            return $lesson->lessonable->duration ?? 0;
                        });
                    })->sum();
                    //Tính thời gian độc docs
                    $totalDurationDocs = $course->modules->flatMap(function ($module) {
                        return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                            $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                            return ceil(($wordCount / 200) * 60);
                        });
                    })->sum();
                    //Tính tổng thời gian của video và docs
                    $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;

                    $course->ratings_avg_rate = number_format(round($course->ratings_avg_rate ?? 0, 1), 1);
                    $course->total_student = DB::table('user_courses')->where('id_course', $course->id)->count();

                    $course->makeHidden(['modules', 'ratings']);
                }


            $coursesUserBought = [];
            $listCoursesUserBought = UserCourse::where('id_user', $user->id)->get();
            foreach ($listCoursesUserBought as $item) {
                $coursesUserBought[] = $course = Course::select('id', 'name', 'slug', 'description', 'thumbnail', 'sort_description','price', 'price_sale','level')->withCount('ratings')
                ->withAvg('ratings', 'rate')
                ->withCount([
                    'modules as lessons_count' => function ($query) {
                        $query->whereHas('lessons');
                    },
                    'modules as quiz_count' => function ($query) {
                        $query->whereHas('lessons', function ($query) {
                            $query->where('content_type', 'quiz');
                        });
                    }
                ])->find($item->id_course);
                $total_lessons = $course->modules->flatMap->lessons->count();
                $total_quiz = $course->modules->flatMap->lessons->where('content_type', 'quiz')->count();
                $course->total_lessons = $total_lessons + $total_quiz;

                //Tính thời gian có sẵn lưu vào datatable
                $totalDurationVideo = $course->modules->flatMap(function ($module) {
                    return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                        return $lesson->lessonable->duration ?? 0;
                    });
                })->sum();
                //Tính thời gian độc docs
                $totalDurationDocs = $course->modules->flatMap(function ($module) {
                    return $module->lessons->where('content_type', 'document')->map(function ($lesson) {
                        $wordCount = $lesson->lessonable->word_count ?? str_word_count(strip_tags($lesson->lessonable->content));
                        return ceil(($wordCount / 200) * 60);
                    });
                })->sum();
                //Tính tổng thời gian của video và docs
                $course->total_duration_video = $totalDurationVideo + $totalDurationDocs;

                $course->ratings_avg_rate = number_format(round($course->ratings_avg_rate ?? 0, 1), 1);
                $course->total_student = DB::table('user_courses')->where('id_course', $course->id)->count();
                $progress = DB::table('user_courses')
                ->where('id_course', $course->id)
                ->where('id_user', $user->id)
                ->first();
            $course['progress_percent'] = $progress->progress_percent ?? 0;
            if (DB::table('user_courses')->where('id_user', auth()->id())->where('id_course', $course->id)->exists()) {
                $course->is_course_bought = true;
            } else {
                $course->is_course_bought = false;
            }
            //Ẩn đi module
            $course->makeHidden('ratings');
            $course->makeHidden('modules');
            }


            // $postsByUser = $user->posts()->select()->get();
            return response()->json([
                'status' => 'success',
                'message' => "Thống tin người dùng",
                'data' => [
                    'user' => $user,
                    'courses_by_user' => $coursesByUser,
                    // 'posts_by_user' => $postsByUser,
                    'courses_user_bought' => $coursesUserBought,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Đã xảy ra lỗi trong quá trình lấy thống tin người dùng" . $e->getMessage(),
                'data' => []
            ]);
        }

    }

}
