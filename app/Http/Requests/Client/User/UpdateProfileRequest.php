<?php

namespace App\Http\Requests\Client\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'nullable|string|max:15',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:500',
            'experience' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'name.required' => 'Tên không được bỏ trống.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'avatar.max' => 'Ảnh đại diện không được lớn hơn 2MB.',
            'bio.max' => 'Tiểu sử không được vượt quá 500 ký tự.',
            'experience.max' => 'Kinh nghiệm không được vượt quá 500 ký tự.',
        ];
    }
}
