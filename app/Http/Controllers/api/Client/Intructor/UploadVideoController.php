<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Video;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Jobs\Client\UploadVideo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Lessons\StoreLessonVideoRequest;

class UploadVideoController extends Controller
{
    public function uploadVideo(StoreLessonVideoRequest $request, Module $module)
    {
        $data = [
            'title' => $request['title'],
            'type' => $request['check'],
        ];

        $moduleId = $module->id;
        $description = $request['description'];

        if ($data['type'] && $data['type'] == 'upload') {
            $file = $request['video'];
            $stream = fopen($file->getRealPath(), 'r+');
            $fileName = 'videos/' . time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->put($fileName, $stream);
            fclose($stream);
            $data['url'] = $fileName;
            $data['duration'] = $request['duration'];
        } else {
            $data['video_youtube_id'] = $request['video_youtube_id'];
            $data['duration'] = $this->getVideoDuration($request['video_youtube_id']);
        }

        $newLessonVideo = $this->addLessonVideo($data, $moduleId, $description);

        if (!$newLessonVideo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thêm bài học không thành công!',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm bài học thành công!',
            'data' => $newLessonVideo,
        ], 201);
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
