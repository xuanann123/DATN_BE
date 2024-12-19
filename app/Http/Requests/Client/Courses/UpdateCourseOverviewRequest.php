<?php

namespace App\Http\Requests\Client\Courses;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseOverviewRequest extends BaseFormRequest
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
            'name' => 'nullable|string|max:255',
            'sort_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|string|max:50',
            'code' => 'nullable|unique:courses,code,' . $this->course->id,
            'id_category' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048', // max 2mb
            'trailer' => 'nullable|file|max:51200', // max 50mb
            'price' => 'nullable|numeric|min:0',
            'price_sale' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $price = $this->input('price', 0);
                    $priceSale = $value;
                    if ($price > 0 && $priceSale < $price * 0.3) {
                        $fail("Giá giảm không được nhỏ hơn 30% giá gốc.");
                    }
                },
            ],
            'is_active' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Tên khóa học phải là một chuỗi.',
            'name.max' => 'Tên khóa học không được vượt quá 255 ký tự.',
            'sort_description.string' => 'Mô tả ngắn phải là một chuỗi.',
            'sort_description.max' => 'Mô tả ngắn không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'level.string' => 'Cấp độ phải là một chuỗi.',
            'level.max' => 'Cấp độ không được vượt quá 50 ký tự.',
            'id_category.exists' => 'Danh mục không hợp lệ.',
            'thumbnail.image' => 'Thumbnail phải là một hình ảnh.',
            'thumbnail.max' => 'Thumbnail không được vượt quá 2MB.',
            'trailer.file' => 'Trailer phải là một tệp video.',
            'trailer.max' => 'Trailer không được vượt quá 10MB.',
            'price.numeric' => 'Giá phải là một số.',
            'price.min' => 'Giá không được nhỏ hơn 0.',
            'price_sale.numeric' => 'Giá giảm phải là một số.',
            'price_sale.min' => 'Giá giảm không được nhỏ hơn 0.',
            'is_active.boolean' => 'Trạng thái hoạt động phải là đúng hoặc sai.',
            'tags.array' => 'Tags phải là một mảng.',
            'tags.*.string' => 'Mỗi tag phải là một chuỗi.',
            'tags.*.max' => 'Mỗi tag không được vượt quá 50 ký tự.',
        ];
    }
}
