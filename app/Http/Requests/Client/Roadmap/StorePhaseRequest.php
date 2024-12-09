<?php

namespace App\Http\Requests\Client\Roadmap;

use Illuminate\Foundation\Http\FormRequest;

class StorePhaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'roadmap_id' => 'required|exists:roadmaps,id', // Kiểm tra roadmap tồn tại
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'order' => 'required|integer|min:1', // Thứ tự giai đoạn
            'course_ids' => 'required|array', // Mảng chứa các course_id
            'course_ids.*' => 'exists:courses,id', // Kiểm tra các course_id có tồn tại trong bảng courses
        ];
    }

    public function messages()
    {
        return [
            'roadmap_id.required' => 'Giai đoạn phải thuộc về một lộ trình.',
            'roadmap_id.exists' => 'Lộ trình không tồn tại.',
            'name.required' => 'Tên giai đoạn là bắt buộc.',
            'name.string' => 'Tên giai đoạn phải là chuỗi.',
            'name.max' => 'Tên giai đoạn không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 5000 ký tự.',
            'order.required' => 'Thứ tự giai đoạn là bắt buộc.',
            'order.integer' => 'Thứ tự giai đoạn phải là số nguyên.',
            'order.min' => 'Thứ tự giai đoạn phải lớn hơn hoặc bằng 1.',
            'course_ids.required' => 'Bạn phải chọn ít nhất một khóa học.',
            'course_ids.array' => 'Danh sách khóa học phải là mảng.',
            'course_ids.*.exists' => 'Một hoặc nhiều khóa học không tồn tại.',
        ];
    }
}
