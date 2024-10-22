<?php

namespace App\Http\Requests\Client\Courses;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'id_user' => 'required|integer|exists:users,id',
            'id_course' => 'required|integer|exists:courses,id',
            'content' => 'required|string|max:255',
            'rate' => 'required|integer|between:1,5'
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Id người dùng không được trống',
            'id_user.integer' => 'Id nguời dùng phải là số nguyên',
            'id_user.exists' => 'Người dùng không tồn tại',
            'id_course.required' => 'Id khóa học không được trống',
            'id_course.integer' => 'Id khóa học phải là số nguyên',
            'id_course.exists' => 'Khóa học không tồn tại',
            'content.required' => 'Vui lòng nhập nội dung bình luận',
            'content.string' => 'Nội dung bình luận phải là chuỗi',
            'content.max' => 'Nội dung bình luận không quá 255 kí tự',
            'rate.required' => 'Vui lòng thêm điểm đánh giá',
            'rate.integer' => 'Điểm đánh giá phải là số nguyên',
            'rate.between' => 'Điểm đánh giá phải >= 1 và <= 5',
        ];
    }
}
