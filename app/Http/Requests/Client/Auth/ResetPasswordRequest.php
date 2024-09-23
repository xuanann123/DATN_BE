<?php

namespace App\Http\Requests\Client\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'otp_code' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'otp_code.required' => 'Mã OTP không để trống.',
            'new_password.required' => 'Mật khẩu mới không để trống.',
            'new_password.string' => 'Mật khẩu mới phải là chuỗi.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }

}
