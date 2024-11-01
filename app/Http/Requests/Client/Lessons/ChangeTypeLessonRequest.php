<?php

namespace App\Http\Requests\Client\Lessons;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class ChangeTypeLessonRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $newType = $this->input('new_type');
        $currentType = $this->route('lesson')->content_type;

        $rules = [
            'new_type' => [
                'required',
                'string',
                'in:video,document',
                function ($attribute, $value, $fail) use ($currentType) {
                    if ($value === $currentType) {
                        $fail('Loại nội dung mới phải khác với loại nội dung hiện tại.');
                    }
                },
            ],
        ];

        if ($newType === 'video') {
            $rules['check'] = 'required|in:upload,url';

            if ($this->input('check') === 'upload') {
                $rules['video'] = 'required';
            } else {
                $rules['video_youtube_id'] = 'required|string';
            }
        } elseif ($newType === 'document') {
            $rules['content'] = 'required|string';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('new_type') === 'video' && $this->input('check') === 'url') {
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
            'new_type.required' => 'Vui lòng chọn loại nội dung mới.',
            'new_type.in' => 'Loại nội dung phải là video hoặc document.',
            'check.required' => 'Vui lòng chọn kiểu video.',
            'check.in' => 'Kiểu video không hợp lệ.',
            'video.required' => 'Vui lòng tải lên một video.',
            // 'duration.required' => 'Vui lòng cung cấp thời lượng video.',
            // 'duration.integer' => 'Thời lượng phải là một số nguyên.',
            // 'duration.min' => 'Thời lượng phải lớn hơn 0.',
            'video_youtube_id.required' => 'Vui lòng nhập ID video YouTube.',
            'content.required' => 'Vui lòng nhập nội dung tài liệu.',
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
