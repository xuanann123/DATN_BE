<?php

namespace App\Http\Requests\Client\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonPositionsRequest extends FormRequest
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
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.position' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'lessons.array' => 'Dữ liệu phải là một mảng.',
            'lessons.*.id.required' => 'ID bài học là bắt buộc.',
            'lessons.*.id.exists' => 'Bài học không tồn tại.',
            'lessons.*.position.required' => 'Vị trí là bắt buộc.',
            'lessons.*.position.integer' => 'Vị trí phải là một số nguyên.',
        ];
    }
}
