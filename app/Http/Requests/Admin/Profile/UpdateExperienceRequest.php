<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExperienceRequest extends FormRequest
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
            'institution_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'major' => 'nullable|string|max:255', // Optional
            'start_date' => 'required|date|before:end_date', // Phải trước ngày kết thúc
            'end_date' => 'required|date|after:start_date', // Phải sau ngày bắt đầu
        ];
    }
    public function messages()
    {
        return [
            'institution_name.required' => 'Tên trường học/tổ chức là bắt buộc.',
            'institution_name.string' => 'Tên trường học/tổ chức phải là một chuỗi.',
            'institution_name.max' => 'Tên trường học/tổ chức không được vượt quá 255 ký tự.',
            'degree.required' => 'Bằng cấp là bắt buộc.',
            'degree.string' => 'Bằng cấp phải là một chuỗi.',
            'degree.max' => 'Bằng cấp không được vượt quá 255 ký tự.',
            'major.string' => 'Chuyên ngành phải là một chuỗi.',
            'major.max' => 'Chuyên ngành không được vượt quá 255 ký tự.',
            'start_date.required' => 'Thời gian bắt đầu là bắt buộc.',
            'start_date.date' => 'Thời gian bắt đầu không hợp lệ.',
            'start_date.before' => 'Thời gian bắt đầu phải trước thời gian kết thúc.',
            'end_date.required' => 'Thời gian kết thúc là bắt buộc.',
            'end_date.date' => 'Thời gian kết thúc không hợp lệ.',
            'end_date.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ];
    }
}
