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
}
