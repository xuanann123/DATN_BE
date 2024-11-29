<?php

namespace App\Http\Requests\Client\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCodingContentRequest extends FormRequest
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
            'statement' => 'required|string',
            'hints' => 'nullable|string',
            'sample_code' => 'required|string',
            'output' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'statement.required' => 'Đề bài không được để trống.',
            'sample_code.required' => 'Code mẫu không được để trống.',
            'output.required' => 'Output không được để trống.',
        ];
    }
}
