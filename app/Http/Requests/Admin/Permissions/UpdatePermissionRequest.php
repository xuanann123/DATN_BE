<?php

namespace App\Http\Requests\Admin\Permissions;

use App\Configs\PermissionConfig;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
        $permissionId = $this->route('permission') ? $this->route('permission')->id : null;

        return [
            'name' => 'required',
            'slug' => [
                'required',
                'unique:permissions,slug,' . $permissionId . ',id',
                function ($attr, $value, $fail) {
                    if (!PermissionConfig::isValid($value)) {
                        $fail('Slug quyền không hợp lệ, vui lòng nhập đúng slug quyền.');
                    };
                }
            ],
            'description' => 'nullable|max:255',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên quyền',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'description.max' => 'Mô tả không quá 255 ký tự',
        ];
    }
}
