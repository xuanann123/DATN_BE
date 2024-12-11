<?php

namespace App\Http\Requests\Admin\Permissions;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required',
            'slug' => 'required|unique:permissions,slug',
            'description' => 'nullable|max:255',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên quyền không được để trống',
            'slug.required' => 'Slug quyền không được để trống',
            'slug.unique' => 'Slug quyền đã tồn tại',
        ];
    }
}
