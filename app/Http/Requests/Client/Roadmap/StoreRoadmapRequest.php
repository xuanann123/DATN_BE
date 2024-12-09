<?php

namespace App\Http\Requests\Client\Roadmap;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoadmapRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên lộ trình là bắt buộc.',
            'name.string' => 'Tên lộ trình phải là chuỗi.',
            'name.max' => 'Tên lộ trình không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 5000 ký tự.',
        ];
    }
}
