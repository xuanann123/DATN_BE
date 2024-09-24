<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google\Service\ServiceControl\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function index()
    {
        return view("admin.profiles.index");
    }
    public function edit()
    {
        return view("admin.profiles.edit");
    }
    public function updateInforBasic(Request $request)
    {
        // Kiểm tra xem người dùng có đăng nhập không
        $user = User::findOrFail(auth()->user()->id);
        // Gán dữ liệu từ request
        $data['name'] = $request->name;
        // Kiểm tra xem có file avatar được tải lên hay không
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $newNameImage = 'user_' . time() . '.' . $image->getClientOriginalExtension();
            // Lưu file vào thư mục public/users
            $pathImage = Storage::putFileAs('users', $image, $newNameImage);
            if ($pathImage) {
                // Xóa avatar cũ nếu tồn tại
                if ($user->avatar) {
                    $fileExists = Storage::disk('public')->exists($user->avatar);
                    if ($fileExists) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                }
                // Gán đường dẫn ảnh mới
                $data['avatar'] = $pathImage;
            }
        } else {
            // Nếu không tải lên ảnh mới, giữ lại avatar cũ
            $data['avatar'] = $user->avatar;
        }

        // Cập nhật thông tin người dùng
        $user->update($data);

        return redirect()->back()->with("success", "Cập nhật thành công");
    }


}
