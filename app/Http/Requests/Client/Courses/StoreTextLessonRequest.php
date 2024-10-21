<?php

namespace App\Http\Requests\Client\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreTextLessonRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'id_module' => '',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Trường tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Trường nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là một chuỗi.',
        ];
    }
}
