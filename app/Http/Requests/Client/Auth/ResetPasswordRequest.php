<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
}
