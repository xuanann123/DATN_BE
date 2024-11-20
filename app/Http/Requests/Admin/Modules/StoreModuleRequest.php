<?php

namespace App\Http\Requests\Admin\Modules;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'id_course' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'position' => 'required|integer',
        ];
    }
    public function messages(): array
    {
        return [
            'required' => ':attribute vui lòng điền vào.',
            'title.required' => 'Vui lòng điền tiêu đề chương học.',
            'description.required' => 'Bui lòng điền mô tả chương học.',
        ];
    }




}
