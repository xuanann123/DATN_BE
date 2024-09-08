<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $data = User::query()->get();
        return view('admin.users.index', compact('data'));
    }
    public function create() {
        $roles = Role::query()->get();
        return view('admin.users.create', compact('roles'));
    }
    function store(Request $request)
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'required' => "Không được để trống :attribute",
                'unique' => "Đã tồn tại bản ghi :attribute từ trước",
                'confirmed' => "Mật khẩu bắt buộc phải trùng",
                'min' => ":attribute phải lớn hơn 8 kí tự",
            ],
            [
                'name' => "họ và tên",
                'email' => "địa chỉ email",
                'password' => "Mật khẩu",
            ]
        );

        $list_role = $request->list_role;
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = Storage::put("users", $request->file('image'));
        }
        $data['type'] = $request->input('type') ? User::TYPE_ADMIN : User::TYPE_MEMBER;
        DB::beginTransaction();
        try {
            //Thêm dữ liệu vào hai bảng
            $user_create = User::create($data);
            $user_create->roles()->sync($list_role);
            DB::commit();
        } catch (Exception $th) {
            //Xoá ảnh hiện tại vẫn tồn tại khi mà thực hiện câu lệnh bị lỗi
            if (Storage::exists($data['image']) && $request->hasFile('image')) {
                Storage::delete($data['image']);
            }
            DB::rollBack();
        }
        return redirect()->route("admin.users.list")->with('status', "Thêm người dùng thành công");
    }
}
