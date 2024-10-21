<?php

namespace App\Http\Requests\Admin\Lessons;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

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
            'video_youtube_id' => [
                $url,
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('check') !== 'upload' && $this->input('video_youtube_id')) {
                // Lấy video ID từ URL
                $videoId = $this->input('video_youtube_id');

                // Kiểm tra video có tồn tại không
                if (!$this->isVideoExist($videoId)) {
                    $validator->errors()->add('video_youtube_id', 'Video không tồn tại trên YouTube.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'video.required' => 'Vui lòng tải lên một video.',
            'video.mimes' => 'Video phải có định dạng: mp4, mov, avi, hoặc flv.',
            'video_youtube_id.required' => 'Vui lòng nhập id video',
        ];
    }


    protected function isVideoExist($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$apiKey}";

        $response = Http::get($apiUrl);
        $data = $response->json();

        return !empty($data['items']);
    }
}
