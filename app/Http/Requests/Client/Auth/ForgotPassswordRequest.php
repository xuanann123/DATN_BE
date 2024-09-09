<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPassswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
