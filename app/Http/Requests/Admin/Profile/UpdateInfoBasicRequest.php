<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoBasicRequest extends FormRequest
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
            'name' => 'required|string|max:255', // Tên bắt buộc, kiểu chuỗi, tối đa 255 ký tự
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Avatar phải là hình ảnh và tối đa 2MB
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên không được để trống.',
            'name.string' => 'Tên phải là một chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'avatar.image' => 'Avatar phải là một file hình ảnh.',
            'avatar.mimes' => 'Avatar phải có định dạng jpeg, png, jpg, gif hoặc svg.',
            'avatar.max' => 'Avatar không được vượt quá 2MB.',
        ];
    }
}
