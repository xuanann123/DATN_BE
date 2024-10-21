<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInforNomalRequest extends FormRequest
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
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ];
    }
    public function messages() {
        return [
            'phone.regex' => 'Số điện thoại không đúng định dạng. Vui lòng nhập số điện thoại hợp lệ.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'experience.max' => 'Kinh nghiệm không được vượt quá 255 ký tự.',
            'bio.max' => 'Giới thiệu không được vượt quá 1000 ký tự.',
        ];
    }
}
