<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lessons\StoreLessonVideoRequest;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UploadVideoController extends Controller
{
    public function storeLessonVideo(StoreLessonVideoRequest $request)
    {
        $data = [
            'title' => $request->title,
            'type' => $request->check,
        ];

        $moduleId = $request->id_module;
        $description = $request->description;

        if ($request->check && $request->check == 'upload') {
            $file = $request->file('video');
            $stream = fopen($file->getRealPath(), 'r+');
            $fileName = 'videos/' . time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->put($fileName, $stream);
            fclose($stream);
            $data['url'] = $fileName;
            $data['duration'] = $request->duration;
        } else {
            $data['video_youtube_id'] = $request->video_youtube_id;
            $data['duration'] = $this->getVideoDuration($request->video_youtube_id);
        }

        $newLessonVideo = $this->addLessonVideo($data, $moduleId, $description);
        if (!$newLessonVideo) {
            return back()->with('error', 'Thêm bài học không thành công!');
        }

        return back()->with('success', 'Thêm bài học thành công!');
    }

    public function addLessonVideo($data, $moduleId, $description)
    {
        $newLessonVideo = Video::query()->create($data);
        $newLessonVideo->lessons()->create([
            'id_module' => $moduleId,
            'title' => $data['title'],
            'description' => $description,
            'content_type' => 'video',
            'position' => $newLessonVideo->id,
        ]);
        return $newLessonVideo;
    }

    public function getVideoDuration($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');

        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&part=contentDetails&key={$apiKey}";

        $response = Http::get($apiUrl);

        $data = $response->json();

        if (!empty($data['items'])) {
            $duration = $data['items'][0]['contentDetails']['duration'];

            $seconds = $this->convertDurationToSeconds($duration);

            return $seconds;
        }
    }

    private function convertDurationToSeconds($duration)
    {
        $interval = new \DateInterval($duration);
        return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }
}
