<?php

namespace App\Http\Requests\Admin\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonVideoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'video' => 'required|mimes:mp4,mov,avi,flv',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'video.required' => 'Vui lòng tải lên một video.',
            'video.mimes' => 'Video phải có định dạng: mp4, mov, avi, hoặc flv.',
        ];
    }
}
