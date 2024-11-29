<?php

namespace App\Http\Requests\Client\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class StoreCodingLessonRequest extends FormRequest
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
            'description' => 'nullable|string',
            'language' => 'required|string',
            // 'statement' => 'required|string',
            // 'hints' => 'nullable|string',
            // 'sample_code' => 'nullable|string',
            // 'output' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'language.required' => 'Ngôn ngữ là bắt buộc.',
            'language.string' => 'Ngôn ngữ phải là chuỗi ký tự.',
        ];
    }
}
