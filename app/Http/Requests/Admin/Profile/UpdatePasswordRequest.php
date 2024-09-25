<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|min:8|same:newPassword',
        ];
    }
    public function messages()
    {
        return [
            'oldPassword.required' => 'Vui lòng nhập mật khẩu cũ của bạn',
            'newPassword.required' => 'Vui lòng nhập mật khẩu mới của bạn',
            'newPassword.min' => 'Mật khẩu phải lớn hơn 8 kí tự',
            'confirmPassword.required' => 'Vui lòng nhập lại mật khẩu mới',
            'confirmPassword.min' => 'Mật khẩu phải là 8 kí tự',
            'confirmPassword.same' => 'Mật khẩu nhập lại không xác thực',
        ];
    }
}
