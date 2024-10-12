<?php

namespace App\Http\Requests\Client\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'id_course' => '',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => '',
        ];
    }


    public function messages()
    {
        return [
            'id_course.required' => 'Trường ID khóa học là bắt buộc.',
            'id_course.exists' => 'Khóa học không tồn tại.',
            'title.required' => 'Trường tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'position.integer' => 'Vị trí phải là một số nguyên.',
        ];
    }
}
