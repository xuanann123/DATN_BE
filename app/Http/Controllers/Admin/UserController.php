<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    const ADMIN = 'admin';
    const TEACHER = 'teacher';
    const STUDENT = 'member';

    public function index()
    {
        $title = "Quản lí thành viên";
        $data = User::query()->paginate(10);
        return view('admin.users.index', compact('data', 'title'));
    }
    public function create()
    {
        $title = "Thêm mới thành viên";
        $roles = [
            self::ADMIN => self::ADMIN,
            self::TEACHER => self::TEACHER,
            self::STUDENT => self::STUDENT,
        ];

        return view('admin.users.create', compact('roles', 'title'));
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->except('avatar');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        if ($request->avatar && $request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $newNameImage = 'avatar_user_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('users', $image, $newNameImage);

            $data['avatar'] = $pathImage;
        }

        $newUser = User::query()->create($data);

        if (!$newUser) {
            return back()->with(['error' => 'Thêm người dùng thất bại!']);
        }

        return redirect()->route('admin.users.list')->with(['success' => 'Thêm người dùng thành công!']);
    }

    public function edit(User $user)
    {

        if (!$user) {
            return redirect()->route('admin.users.list')->with(['error' => 'Người dùng không tồn tại!']);
        }

        $title = "Cập nhật tài khoản";
        $roles = [
            self::ADMIN => self::ADMIN,
            self::TEACHER => self::TEACHER,
            self::STUDENT => self::STUDENT,
        ];

        return view('admin.users.edit', compact('title', 'user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if (!$user) {
            return redirect()->route('admin.users.list')->with(['error' => 'Người dùng không tồn tại!']);
        }

        $data = $request->except('avatar');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        if ($request->avatar && $request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $newNameImage = 'avatar_user_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('users', $image, $newNameImage);

            $data['avatar'] = $pathImage;

            if ($user->avatar) {
                $fileExists = Storage::disk('public')->exists($user->avatar);

                if ($fileExists) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }
        } else {
            $data['avatar'] = $user->avatar;
        }

        $updateUser = $user->update($data);

        if (!$updateUser) {
            return redirect()->route('admin.users.list')->with(['error' => 'Cập nhật người dùng thất bại!']);
        }

        return redirect()->route('admin.users.list')->with(['success' => 'Cập nhật người dùng thành công']);
    }

    public function changePassword(ChangePasswordRequest $request, User $user)
    {
        if (!$user) {
            return redirect()->route('admin.users.list')->with(['error' => 'Người dùng không tồn tại!']);
        }

        $data['password'] = Hash::make($request->password);

        $changePassword = $user->update($data);

        if (!$changePassword) {
            return redirect()->route('admin.users.list')->with(['error' => 'Đổi mật khẩu thất bại!']);
        }

        return redirect()->route('admin.users.list')->with(['success' => 'Đổi mật khẩu thành công']);
    }

    public function delete(User $user)
    {
        if (!$user) {
            return back()->with(['error' => 'Người dùng không tồn tại!']);
        }

        if ($user->avatar) {
            $fileExists = Storage::disk('public')->exists($user->avatar);

            if ($fileExists) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        if ($user->delete()) {
            return back()->with(['success' => 'Xóa người dùng thành công!']);
        }

        return back()->with(['error' => 'Xóa người dùng thất bại!']);
    }

    public function listTeachers(Request $request) {
        if($request->keyword) {
            $title = "Danh sách giảng viên";
            $teachers = User::with(['profile', 'courses'])
                ->withCount('courses')
                ->withSum('courses', 'total_student')
                ->where('user_type', self::TEACHER)
                ->where('name', 'LIKE', '%'. $request->keyword . '%')
                ->paginate(12);
            return view('admin.users.list_teacher', compact('title', 'teachers'));

        }
        $title = "Danh sách giảng viên";
        $teachers = User::with(['profile', 'courses'])
            ->withCount('courses')
            ->withSum('courses', 'total_student')
            ->where('user_type', self::TEACHER)
            ->paginate(12);

        return view('admin.users.list_teacher', compact('title', 'teachers'));
    }
}
