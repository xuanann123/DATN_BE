<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'password' => 'required|max:255|min:8',
            'confirm_password' => 'required|max:255|min:8|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => "Mật khẩu không được trống",
            'password.max' => "Mật khẩu không quá 255 kí tự",
            'password.min' => "Mật khẩu tối thiểu 8 kí tự",
            'confirm_password.required' => "Vui lòng nhập lại mật khẩu",
            'confirm_password.max' => "Mật khẩu không quá 255 kí tự",
            'confirm_password.min' => "Mật khẩu tối thiểu 8 kí tự",
            'confirm_password.same' => "Mật khẩu không khớp",
        ];
    }
}
