<?php

namespace App\Http\Requests\Admin\Modules;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Hoặc kiểm tra quyền nếu cần thiết
    }

    public function rules(): array
    {

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute vui lòng điền vào.',
            'title.required' => 'Tiêu đề chương học là bắt buộc.',
            'description.required' => 'Mô tả chương học là bắt buộc.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Tiêu đề chương học',
            'description' => 'Mô tả chương học',
        ];
    }
}
