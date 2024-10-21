<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'required|max:255|min:2',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
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
            'user_type' => 'Vui lòng chọn loại người dùng',
            'email_verified_at' => 'Vui lòng nhập thời gian xác thực',
            'avatar.image' => 'Vui lòng chọn ảnh',
            'avatar.mimes' => 'Ảnh không đúng định dạng',
            'avatar.max' => 'Ảnh không được quá 5mb',
        ];
    }
}
