<?php

namespace App\Http\Requests\Client\Notes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
    public function rules()
    {
        return [
            'content' => 'required|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Nội dung ghi chú không được để trống.',
            'content.string' => 'Nội dung ghi chú phải là chuỗi ký tự.',
            'content.max' => 'Nội dung ghi chú không được vượt quá 500 ký tự.',
        ];
    }
}
