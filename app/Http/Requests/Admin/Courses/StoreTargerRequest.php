<?php

namespace App\Http\Requests\Admin\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreTargerRequest extends FormRequest
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
            [
                'goals' => 'required|array|min:4',               // Ít nhất 4 mục tiêu
                'goals.*' => 'string|max:255',                   // Mỗi mục tiêu là chuỗi văn bản
                'requirements' => 'required|array|min:1',        // Ít nhất 1 yêu cầu
                'requirements.*' => 'string|max:255',            // Mỗi yêu cầu là chuỗi văn bản
                'audiences' => 'required|array|min:1',           // Ít nhất 1 đối tượng
                'audiences.*' => 'string|max:255'                // Mỗi đối tượng là chuỗi văn bản
            ],
            [
                'goals.required' => 'Vui lòng nhập ít nhất 4 mục tiêu.',
                'goals.array' => 'Mục tiêu phải là một mảng.',
                'goals.min' => 'Cần ít nhất 4 mục tiêu.',
                'requirements.required' => 'Vui lòng nhập ít nhất 1 yêu cầu.',
                'requirements.array' => 'Yêu cầu phải là một mảng.',
                'requirements.min' => 'Cần ít nhất 1 yêu cầu.',
                'audiences.required' => 'Vui lòng nhập ít nhất 1 đối tượng.',
                'audiences.array' => 'Đối tượng phải là một mảng.',
                'audiences.min' => 'Cần ít nhất 1 đối tượng.'
            ]
        ];
    }
}
