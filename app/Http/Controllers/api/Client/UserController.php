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
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Video;
use Carbon\Carbon;
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
        $myCourseBought = User::with([
            'userCourses.tags',
            'userCourses.category',
        ])->findOrFail($authUser->id);
        //Duyệt qua toàn bộ khoá học đó
        $myCourseBought->userCourses->each(function ($course) {

            // Tính tổng số lượng bài học trong khóa học
            $total_lessons = $course->modules->flatMap->lessons->count();
            //Kiểm tra quiz có hay không và truy vấn vào quiz lấy ra số lượng
            $total_quizzes = $course->modules->whereNotNull('quiz')->count();

            // Set thời gian cho từng bài học (cần có hàm setLessonDurations)
            $this->setLessonDurations($course);
            $total_duration_video = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');
            // $course->submited_at = $course->created_at;
            $course->total_lessons = $total_lessons + $total_quizzes;
            //Tổng thời gian của khoá học đó
            $course->total_duration_video = $total_duration_video;

        });
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

    public function follow(Request $request, )
    {
        //id người sẽ được theo dõi
        $following_id = $request->following_id;
        //Lấy id người dùng đang online hiện tại
        $follower = Auth::user();
        $following = User::find($following_id);
        if (!$following) {
            return response()->json([
                "status" => "error",
                'message' => "Không tồn tại người mà bạn muốn theo dõi",
                "data" => [],
            ], 404);
        }
        //Kiểm tra xem người dùng này đã follow chưa
        if (!$follower->following()->where("following_id", $following_id)->exists()) {
            $follower->following()->attach($following_id);
            return response()->json([
                "status" => "success",
                "message" => "Theo dõi thành công",
                "data" => [],
            ], 200);
        }
        //Đã theo dõi từ trước sẽ báo lỗi 400
        return response()->json([
            "status" => "error",
            "message" => "Bạn đã theo dõi user này",
            "data" => [],
        ], 400);
    }
    public function unfollow(Request $request)
    {
        $follower = Auth::user();
        $following_id = $request->following_id;
        //Kiểm tra xem tài khoản trên đã được follow chưa và tồn tại không
        $following = User::find($following_id);
        if (!$following) {
            return response()->json([
                "status" => "error",
                'message' => "Không tồn tại người mà có theo dõi",
                "data" => [],
            ], 404);
        }
        //Kiểm tra xem nó có follow không
        if ($follower->following()->where("following_id", $following_id)->exists()) {
            $follower->following()->detach($following_id);
            return response()->json([
                "status" => "success",
                "message" => "Huỷ theo dõi tài khoản thành công",
                "data" => [],
            ], 200);
        }
        return response()->json([
            "status" => "error",
            "message" => "Tài khoản này chưa được theo dõi",
            "data" => [],
        ], 400);
    }
    public function listTeacherMonth(Request $request)
    {
        // Lấy danh sách giảng viên trong 1 tháng gần nhất (hoặc theo yêu cầu của bạn)
        $oneMonthAgo = Carbon::now()->subMonth();
        $teachers = User::where('user_type', 'teacher')
            ->where('created_at', '>=', $oneMonthAgo)->get();
        //Dùng phương thức map lấy ra số lượng khoá học, số lượng comment, số lượng rating
        $teachers->map(function ($teacher) {
            $courses = $teacher->userCourses;

            // Tính tổng số comment của tất cả các khóa học của giảng viên
            $total_comments = $courses->flatMap(function ($course) {
                return $course->comments;
            })->count();

            // Tính tổng số rating của tất cả các khóa học của giảng viên
            $total_ratings = $courses->flatMap(function ($course) {
                return $course->ratings;
            })->count();

            // Tính tổng số khóa học của giảng viên
            $teacher->total_courses = $courses->count();
            $teacher->total_comments = $total_comments;
            $teacher->total_ratings = $total_ratings;
            $teacher->makeHidden(['userCourses']);
            return $teacher;
        });


        // Sắp xếp giảng viên theo tổng số khóa học, bình luận, và rating cao nhất
        $teachers = $teachers->sortByDesc(function ($teacher) {
            return $teacher->total_courses + $teacher->total_comments + $teacher->total_ratings;
        });

        // Lấy ra 5 giảng viên đầu tiên (có tổng số khóa học, bình luận và rating cao nhất)
        $topTeachers = $teachers->take(5);

        return response()->json([
            "status" => "success",
            "message" => "Danh sách top 5 giảng viên theo tháng",
            "data" => $topTeachers,
        ], 200);
    }
}
