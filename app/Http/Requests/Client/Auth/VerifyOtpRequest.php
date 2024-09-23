<?php

namespace App\Http\Requests\Client\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends BaseFormRequest
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
            'otp_code' => 'required|digits:6',
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'otp_code.required' => 'Mã OTP không để trống.',
            'otp_code.digits' => 'Mã OTP phải có 6 chữ số.',
            'email.required' => 'Email không để trống.',
            'email.email' => 'Email không đúng định dạng.',
        ];
    }

}
