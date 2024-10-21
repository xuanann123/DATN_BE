<?php

namespace App\Http\Requests\Client\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class LessonProgressRequest extends FormRequest
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
            'is_completed' => 'required|boolean',
            'last_time_video' => 'nullable|numeric|min:0',
        ];
    }
}
