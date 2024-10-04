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
        if ($this->input('check') == 'upload') {
            $upload = 'required';
            $url = 'nullable';
        } else {
            $upload = 'nullable';
            $url = 'required';
        }
        return [
            'title' => 'required|string|max:255',
            'video' => $upload . '|mimes:mp4,mov,avi,flv',
            'url' => [
                $url,
                'regex:/^(https?\:\/\/)?(www\.youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+$/',
            ],
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
            'url.required' => 'Vui lòng nhập url video',
            'url.regex' => 'Vui lòng nhập đúng url video của youtube'
        ];
    }
}
