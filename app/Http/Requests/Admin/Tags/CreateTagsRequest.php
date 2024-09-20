<?php

namespace App\Http\Requests\Admin\Tags;

use Illuminate\Foundation\Http\FormRequest;

class CreateTagsRequest extends FormRequest
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
            "name" => "required|unique:tags"
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên thẻ.',
            'name.unique' => 'Tên thẻ đã tồn tại.',
        ];
    }
}
