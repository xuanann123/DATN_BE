<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
        ];
    }
}
