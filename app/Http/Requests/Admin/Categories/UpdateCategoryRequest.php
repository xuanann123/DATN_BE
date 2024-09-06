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
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories,slug,' . $id . '|max:255',
            'image' => 'nullable|image|max:5120',
            'description' => 'nullable|min:6|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter the category name.',
            'name.max' => 'The category name must be under 255 characters.',
            'slug.required' => 'Please enter the slug.',
            'slug.unique' => 'The slug already exists.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'image.image' => 'This is not an image.',
            'image.max' => 'The image size must not exceed 5MB.',
            'description.min' => 'The description must be at least 6 characters.',
            'description.max' => 'The description must not exceed 255 characters.',
        ];
    }
}
