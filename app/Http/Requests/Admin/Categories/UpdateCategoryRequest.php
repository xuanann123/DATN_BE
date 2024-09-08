<?php

namespace App\Http\Requests\Admin\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        $id = $this->route('category');
        return [
            'name' => 'required|unique:categories,name,' . $id . '|max:255',
            'slug' => 'required|unique:categories,slug,' . $id . '|max:255',
            'image' => 'nullable|image|max:5120',
            'description' => 'nullable|min:6|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục phải dưới 255 ký tự.',
            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn thân thiện đã tồn tại.',
            'slug.max' => 'Đường dẫn thân thiện không được vượt quá 255 ký tự.',
            'image.image' => 'Đây không phải là một hình ảnh.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB.',
            'description.min' => 'Mô tả phải có ít nhất 6 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
        ];
    }
}
