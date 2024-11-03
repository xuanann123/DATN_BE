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
            // Quy tắc validation cho các trường
            'goals' => 'required|array|min:4|distinct',              // Mảng có ít nhất 4 mục tiêu, không trùng nhau
            'goals.*' => 'string|max:255',                           // Mỗi mục tiêu là chuỗi văn bản
            'requirements' => 'required|array|min:1|distinct',       // Mảng có ít nhất 1 yêu cầu, không trùng nhau
            'requirements.*' => 'string|max:255',                    // Mỗi yêu cầu là chuỗi văn bản
            'audiences' => 'required|array|min:1|distinct',          // Mảng có ít nhất 1 đối tượng, không trùng nhau
            'audiences.*' => 'string|max:255',                       // Mỗi đối tượng là chuỗi văn bản
        ];
    }

    public function messages(): array
    {
        return [
            'goals.required' => 'Vui lòng nhập ít nhất 4 mục tiêu.',
            'goals.array' => 'Mục tiêu phải là một mảng.',
            'goals.min' => 'Cần ít nhất 4 mục tiêu.',
            'goals.distinct' => 'Các mục tiêu không được trùng lặp.',  // Thông báo khi mục tiêu trùng nhau

            'requirements.required' => 'Vui lòng nhập ít nhất 1 yêu cầu.',
            'requirements.array' => 'Yêu cầu phải là một mảng.',
            'requirements.min' => 'Cần ít nhất 1 yêu cầu.',
            'requirements.distinct' => 'Các yêu cầu không được trùng lặp.',  // Thông báo khi yêu cầu trùng nhau

            'audiences.required' => 'Vui lòng nhập ít nhất 1 đối tượng.',
            'audiences.array' => 'Đối tượng phải là một mảng.',
            'audiences.min' => 'Cần ít nhất 1 đối tượng.',
            'audiences.distinct' => 'Các đối tượng không được trùng lặp.'  // Thông báo khi đối tượng trùng nhau
        ];
    }
}
