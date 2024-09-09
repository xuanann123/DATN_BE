<?php

namespace App\Http\Requests\Admin\Banners;

use Illuminate\Foundation\Http\FormRequest;

class CreateBannerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'redirect_url' => 'nullable|url',
            'image' => 'required|image|max:5120',
            'position' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề phải dưới 255 ký tự.',
            'redirect_url' => 'URL không hợp lệ.',
            'image.required' => 'Vui lòng tải lên một hình ảnh.',
            'image.image' => 'Đây không phải là một hình ảnh.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB.',
            'position.integer' => 'Vị trí phải là một số nguyên.',
            'position.min' => 'Vị trí phải là một số dương.',
            'start_time.date_format' => 'Thời gian phải bao gồm ngày, giờ, tháng và năm.',
            'end_time.date_format' => 'Thời gian phải bao gồm ngày, giờ, tháng và năm.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ];
    }
}
