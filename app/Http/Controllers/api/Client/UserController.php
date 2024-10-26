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
            $total_duration = Video::whereIn('id', $course->modules->flatMap->lessons->pluck('lessonable_id'))
                ->sum('duration');
            //Cập nhật tổng số lượng bài học
            // $course->submited_at = $course->created_at;
            $course->total_lessons = $total_lessons + $total_quizzes;
            //Tổng thời gian của khoá học đó
            $course->total_duration = $total_duration;

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
}
