<?php

namespace App\Http\Requests\Admin\Courses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        $id = $this->route('id');
        return [
            'name' => 'required|max:255',
            'code' => 'required|unique:courses,code,' . $id . '|max:255',
            'slug' => 'required|unique:courses,slug,' . $id . '|max:255',
            'description' => 'nullable|min:6|max:255',
            'sort_description' => 'nullable|min:6|max:255',
            'learned' => 'nullable|min:6',
            'thumbnail' => 'nullable|image|max:5120',
            'trailer' => 'nullable|mimes:mp4,mov,avi,flv|max:102400',
            'id_category' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0|lt:price',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'name.max' => 'Tên khóa học không được vượt quá :max ký tự.',

            'code.required' => 'Vui lòng nhập mã khóa học.',
            'code.max' => 'Mã khóa học không được vượt quá :max ký tự.',
            'code.unique' => 'Mã khóa học đã tồn tại.',

            'slug.required' => 'Vui lòng nhập đường dẫn thân thiện.',
            'slug.unique' => 'Đường dẫn thân thiện đã tồn tại.',
            'slug.max' => 'Đường dẫn thân thiện không được vượt quá :max ký tự.',

            'description.min' => 'Mô tả khóa học phải có ít nhất :min ký tự.',
            'description.max' => 'Mô tả khóa học không được vượt quá :max ký tự.',

            'sort_description.min' => 'Mô tả ngắn phải có ít nhất :min ký tự.',
            'sort_description.max' => 'Mô tả ngắn không được vượt quá :max ký tự.',

            'learned.min' => 'Nội dung nhận được phải có ít nhất :min ký tự.',
            'learned.max' => 'Nội dung nhận được không được vượt quá :max ký tự.',

            'thumbnail.required' => 'Vui lòng chọn ảnh khóa học.',
            'thumbnail.image' => 'Đây không phải ảnh.',
            'thumbnail.max' => 'Kích thước ảnh không được vượt quá :max KB.',

            'trailer.mimes' => 'Video phải có định dạng: mp4, mov, avi, hoặc flv.',
            'trailer.max' => 'Video trailer không quá 100mb',

            'id_category.required' => 'Vui lòng chọn danh mục.',
            'id_category.integer' => 'Danh mục không hợp lệ.',

            'price.numeric' => 'Giá khóa học không hợp lệ.',
            'price.min' => 'Giá khóa học phải lớn hơn hoặc bằng 0.',


            'price_sale.numeric' => 'Giá khóa học không hợp lệ.',
            'price_sale.min' => 'Giá khóa học phải lớn hơn hoặc bằng 0.',
            'price_sale.lt' => 'Giá khoá khuyến mãi phải nhỏ hơn giá khoá học.',

            'tags.array' => 'Danh sách tags phải hợp lệ.',
        ];
    }
}
