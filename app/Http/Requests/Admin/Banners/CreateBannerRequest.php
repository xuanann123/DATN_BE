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
            'title.required' => 'Please enter the title.',
            'title.max' => 'The title must be under 255 characters.',
            'redirect_url' => 'The URL is not valid.',
            'image.required' => 'Please upload an image.',
            'image.image' => 'This is not an image.',
            'image.max' => 'The image size must not exceed 5MB.',
            'position.integer' => 'The position must be an integer.',
            'position.min' => 'The position must be a positive number.',
            'start_time.date_format' => 'The time must include day, hour, month, and year.',
            'end_time.date_format' => 'The time must include day, hour, month, and year.',
            'end_time.after' => 'The end time must be after the start time.',
        ];
    }
}
