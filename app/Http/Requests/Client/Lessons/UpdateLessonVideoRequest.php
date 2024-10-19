<?php

namespace App\Http\Requests\Client\Lessons;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class UpdateLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'video' => 'nullable|mimes:mp4,mov,avi,flv',
            'video_youtube_id' => 'nullable',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('check') !== 'upload' && $this->input('video_youtube_id')) {
                $videoId = $this->input('video_youtube_id');

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
            'video.mimes' => 'Video phải có định dạng: mp4, mov, avi, hoặc flv.',
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
