<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateExperienceRequest;
use App\Http\Requests\Admin\Profile\UpdateInfoBasicRequest;
use App\Http\Requests\Admin\Profile\UpdateInforNomalRequest;
use App\Http\Requests\Admin\Profile\UpdatePasswordRequest;
use App\Models\AdminReview;
use App\Models\Course;
use App\Models\Education;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Google\Service\CloudRedis\UpdateInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function index()
    {
        $listNewPosts = Post::select(
            'posts.id',
            'posts.title',
            'posts.thumbnail',
            'posts.created_at',
        )
            ->orderBy('posts.created_at', 'desc')
            ->paginate(5);

        $courses = AdminReview::select(
            'admin_reviews.created_at',
            'courses.id',
            'courses.name as course_name',
            'courses.status',
            'users.name as author_name',
            'users.avatar',
        )->join('courses', 'courses.id', '=', 'admin_reviews.reviewable_id')
            ->join('users', 'users.id', '=', 'courses.id_user')
            ->where('admin_reviews.reviewable_type', 'App\Models\Course')
            ->where('admin_reviews.user_id', auth()->id())
            ->where('admin_reviews.action', 'approved')
            ->orderBy('admin_reviews.created_at', 'desc')
            ->get();

        $teachers = AdminReview::select(
            'admin_reviews.created_at',
            'users.id',
            'users.name',
            'users.status',
            'users.avatar',
            'profiles.experience'
        )->join('users', 'users.id', '=', 'admin_reviews.reviewable_id')
            ->join('profiles', 'profiles.id_user', '=', 'users.id')
            ->where('admin_reviews.reviewable_type', 'App\Models\User')
            ->where('admin_reviews.user_id', auth()->id())
            ->where('admin_reviews.action', 'approved')
            ->orderBy('admin_reviews.created_at', 'desc')
            ->get();

        return view("admin.profiles.index", compact("listNewPosts", "courses", "teachers"));
    }
    public function edit()
    {
        $user = User::findOrFail(auth()->user()->id);
        $profile = $user->profile;
        return view("admin.profiles.edit", compact("profile"));
    }
    public function updateInforBasic(UpdateInfoBasicRequest $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $data['name'] = $request->name;
        try {
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $newNameImage = 'user_' . time() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('users', $image, $newNameImage);
                if ($pathImage) {
                    if ($user->avatar) {
                        $fileExists = Storage::disk('public')->exists($user->avatar);
                        if ($fileExists) {
                            Storage::disk('public')->delete($user->avatar);
                        }
                    }
                    $data['avatar'] = $pathImage;
                }
            } else {
                $data['avatar'] = $user->avatar;
            }
            $user->update($data);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->back()->with("success", "Cập nhật thành công")->with("tab", "personalDetails");
    }
    public function updateInforNormal(UpdateInforNomalRequest $request)
    {
        try {
            $data = $request->only(['address', 'phone', 'bio', 'experience']);
            $user = User::findOrFail(auth()->user()->id);
            $user->profile()->updateOrCreate(
                ['id_user' => $user->id],
                $data
            );
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->back()->with("success", "Cập nhật thành công");
    }
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $user = User::findOrFail(auth()->user()->id);
            //Kiểm tra mật khẩu cũ xem có trùng hay không
            if (!Hash::check($request->oldPassword, $user->password)) {
                return redirect()->back()->with("error", "Mật này cũ không chính xác vui lòng mời bạn nhập lại")->with('tab', 'changePassword');
            } else {
                //Trường hơp mật khảu đúng
                $user->update(['password' => Hash::make($request->newPassword)]);
                return redirect()->back()->with("success", "Động thay đổi mật khẩu")->with('tab', 'changePassword');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Đã có lỗi xảy ra. Vui lòng thử lại.')->with('tab', 'changePassword');
        }
    }
    public function updateExperience(UpdateExperienceRequest $request)
    {
        try {
            $data = $request->all();
            $user = User::findOrFail(auth()->user()->id);
            $profile = $user->profile;
            $profile->education()->updateOrCreate(
                [
                    "id_profile" => $profile->id,
                ],
                [
                    "institution_name" => $data['institution_name'],
                    "major" => $data['major'],
                    "degree" => $data['degree'],
                    "start_date" => $data['start_date'],
                    "end_date" => $data['end_date'],
                ]
            );
            return redirect()->back()->with("success", "Cập nhật kinh nghiệm thành công")->with('tab', 'experience');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Đã có lỗi xảy ra. Vui lòng thử lại.')->with('tab', 'experience');
        }
    }


}
