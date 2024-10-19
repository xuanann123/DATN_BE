<?php

namespace App\Http\Requests\Client\Notes;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends BaseFormRequest
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
            'content' => 'required|string|max:500',
            'duration' => 'nullable|integer|min:0',
        ];
    }


    public function messages()
    {
        return [
            'content.required' => 'Nội dung ghi chú là bắt buộc.',
            'content.string' => 'Nội dung ghi chú phải là một chuỗi.',
            'content.max' => 'Nội dung ghi chú không được vượt quá 500 ký tự.',
            'duration.integer' => 'Thời gian phải là một số nguyên.',
            'duration.min' => 'Thời gian không được âm.',
        ];
    }
}
