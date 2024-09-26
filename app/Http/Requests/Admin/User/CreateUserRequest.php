<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:255|min:8',
            'confirm_password' => 'required|max:255|min:8|same:password',
            'user_type' => 'required',
            'email_verified_at' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Tên không được để trống",
            'name.max' => "Tên không quá 255 kí tự",
            'name.min' => "Tên ít nhất 2 kí tự",
            'email.required' => "Email không được để trống",
            'email.email' => "Email không hợp lệ",
            'email.unique' => "Email đã tồn tại",
            'password.required' => "Mật khẩu không được trống",
            'password.max' => "Mật khẩu không quá 255 kí tự",
            'password.min' => "Mật khẩu tối thiểu 8 kí tự",
            'confirm_password.required' => "Vui lòng nhập lại mật khẩu",
            'confirm_password.max' => "Mật khẩu không quá 255 kí tự",
            'confirm_password.min' => "Mật khẩu tối thiểu 8 kí tự",
            'confirm_password.same' => "Mật khẩu không khớp",
            'user_type' => 'Vui lòng chọn loại người dùng',
            'email_verified_at' => 'Vui lòng nhập thời gian xác thực',
            'avatar.image' => 'Vui lòng chọn ảnh',
            'avatar.mimes' => 'Ảnh không đúng định dạng',
            'avatar.max' => 'Ảnh không được quá 5mb',
        ];
    }
}
