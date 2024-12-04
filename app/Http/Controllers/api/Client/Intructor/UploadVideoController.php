<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Lesson;
use App\Models\Video;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Lessons\StoreLessonVideoRequest;
use App\Http\Requests\Client\Lessons\UpdateLessonVideoRequest;

class UploadVideoController extends Controller
{
    public function uploadVideo(StoreLessonVideoRequest $request, Module $module)
    {
        $data = [
            'title' => $request['title'],
            'is_preview' => $request['is_preview'],
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
        $maxPosition = Lesson::where('id_module', $moduleId)->max('position');
        $newLessonVideo = Video::query()->create($data);
        $newLessonVideo->lessons()->create([
            'id_module' => $moduleId,
            'title' => $data['title'],
            'description' => $description,
            'is_preview' => $data['is_preview'],
            'content_type' => 'video',
            'position' => $maxPosition + 1,
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

    public function deleteLessonVideo(Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            if ($lesson->lessonable_type == Video::class) {
                $video = $lesson->lessonable;
                if ($video->url && Storage::exists($video->url)) {
                    Storage::delete($video->url);
                }
                $video->delete();
            }

            // Cập nhật lại position của các bài học còn lại sau khi xóa bài học hiện tại
            $positionToDelete = $lesson->position;
            Lesson::where('id_module', $lesson->id_module)
                ->where('position', '>', $positionToDelete)
                ->decrement('position');
            $lesson->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa bài học thành công!',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi xóa bài học.',
                'error' => $e->getLine(),
            ], 500);
        }
    }

    public function updateLessonVideo(UpdateLessonVideoRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            if ($lesson->lessonable_type == Video::class) {
                $data = [
                    'title' => $request['title'],
                    'is_preview' => $request['is_preview'],
                ];

                if ($request['check'] && $request['check'] == 'upload' && !empty($request['video'])) {
                    $file = $request['video'];
                    $stream = fopen($file->getRealPath(), 'r+');
                    $fileName = 'videos/' . time() . '_' . $file->getClientOriginalName();
                    Storage::disk('public')->put($fileName, $stream);
                    fclose($stream);
                    $data['url'] = $fileName;
                    $data['duration'] = $request['duration'];
                    $data['type'] = $request['check'];
                    $data['video_youtube_id'] = '';
                    $video = $lesson->lessonable;
                    if ($video->url && Storage::exists($video->url)) {
                        Storage::delete($video->url);
                    }
                } else if (!empty($request['video_youtube_id'])) {
                    $data['url'] = '';
                    $data['type'] = 'url';
                    $data['video_youtube_id'] = $request['video_youtube_id'];
                    $data['duration'] = $this->getVideoDuration($request['video_youtube_id']);
                }

                $lesson->lessonable()->update($data);

            }

            $lesson->update([
                'title' => $request['title'],
                'description' => $request['description'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật bài học thành công!',
                'data' => [
                    'lesson' => $lesson,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
