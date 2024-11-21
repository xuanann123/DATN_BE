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
    public function myCourseBought()
    {
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
            ])->get();

        //Duyệt qua toàn bộ khoá học đó
        foreach ($myCourseBought as $course) {
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
    public function registerTeacher()
    {
        $user = Auth::user();
        //Kiểm tra phải là user mí cậo nhật lên thành giảng viên
        if ($user->user_type != User::TYPE_MEMBER) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng kiểm tra lại bạn có phải thành viên không?',
                'data' => []
            ], 403);
        }
        //Đi cập nhật dữ liệu và thông báo
        $user->update([
            'user_type' => User::TYPE_TEACHER
        ]);
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

}
