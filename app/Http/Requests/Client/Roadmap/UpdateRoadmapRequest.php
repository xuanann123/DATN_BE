<?php

namespace App\Http\Requests\Client\Roadmap;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoadmapRequest extends FormRequest
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
            'sort_description' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên bắt buộc',
            'name.max' => 'Tên không quá 255 ký tự',
            'description.max' => 'Mô tả không quá 5000 ký tự',
            'sort_description.max' => 'Mô tả không quá 255 ký tự',
            'thumbnail.max' => 'Link ảnh không quá 255 ký tự',
        ];
    }
}
