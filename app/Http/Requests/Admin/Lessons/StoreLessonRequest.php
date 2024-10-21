<?php

namespace App\Http\Requests\Admin\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            'id_module' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'content' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id_module.required' => 'Không có id_module ?',
            'id_module.exists' => 'id_module không tồn tại',
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề không được quá 255 kí tự.',
            'content.required' => 'Vui lòng nhập nội dung.',
        ];
    }
}
