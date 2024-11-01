<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Video;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\Lessons\ChangeTypeLessonRequest;
use App\Http\Requests\Client\Lessons\UpdateLessonPositionsRequest;

class LessonController extends Controller
{
    public function lessonDetailTeacher(Lesson $lesson)
    {
        try {
            // NGười dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $lesson->module->course->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập vào thông tin bài học này.',
                    'data' => []
                ], 403);
            }

            // get bài học quiz, doc, or vid
            $lesson = Lesson::with(['lessonable'])
                ->where('id', $lesson->id)
                ->firstOrFail();

            // Dữ liệu thành công trả về
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin chi tiết bài học.",
                'data' => $lesson,
            ], 200);
        } catch (\Exception $e) {
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateLessonPosition(UpdateLessonPositionsRequest $request, Module $module)
    {
        DB::beginTransaction();
        try {
            // NGười dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $module->course->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập vào chương này.',
                    'data' => []
                ], 403);
            }

            // update vị trí bài học
            foreach ($request->lessons as $lessonPosition) {
                $lesson = Lesson::find($lessonPosition['id']);
                if ($lesson && $lesson->id_module === $module->id) {
                    $lesson->position = $lessonPosition['position'];
                    $lesson->save();
                }
            }

            $module->makeHidden('course');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Vị trí các bài học đã được cập nhật thành công.',
                'data' => $module->load([
                    'lessons' => function ($query) {
                        $query->orderBy('position');
                    }
                ])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật vị trí bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeLessonType(ChangeTypeLessonRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();

        try {
            // NGười dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra xem người dùng có phải là người tạo ra khóa học hay không
            if ($user->id !== $lesson->module->course->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền thao tác.',
                    'data' => []
                ], 403);
            }

            $newType = $request->new_type;

            if ($newType === 'video') {

                $document = $lesson->lessonable;
                if ($lesson->lessonable_type === Document::class) {
                    // xoa bai hoc doc
                    $document->delete();
                }

                $videoData = [
                    'title' => $lesson->title,
                    'type' => $request['check'],
                ];

                if ($videoData['type'] && $videoData['type'] == 'upload') {
                    // Nếu video là tệp tải lên
                    $file = $request->file('video');
                    $stream = fopen($file->getRealPath(), 'r+');
                    $fileName = 'videos/' . time() . '_' . $file->getClientOriginalName();
                    Storage::disk('public')->put($fileName, $stream);
                    fclose($stream);
                    $videoData['url'] = $fileName;
                    $videoData['duration'] = $request->input('duration');
                } else {
                    // Nếu video là từ YouTube
                    $videoData['url'] = null;
                    $videoData['video_youtube_id'] = $request->video_youtube_id;
                    $videoData['duration'] = $this->getVideoDuration($videoData['video_youtube_id']);
                }

                // Tạo nội dung video
                $newVideo = Video::create($videoData);

                // update lesson
                $lesson->update([
                    'content_type' => 'video',
                    'lessonable_type' => Video::class,
                    'lessonable_id' => $newVideo->id
                ]);
            } elseif ($newType === 'document') {
                $video = $lesson->lessonable;
                if ($lesson->lessonable_type === Video::class) {
                    // Xoa video cu
                    if ($video->url && Storage::exists($video->url)) {
                        Storage::delete($video->url);
                    }
                    $video->delete();
                }

                // Tạo nội dung tài liệu
                $documentData = [
                    'content' => $request->input('content'),
                ];

                $newDocument = Document::create($documentData);

                // Cập nhật bài học
                $lesson->update([
                    'content_type' => 'document',
                    'lessonable_type' => Document::class,
                    'lessonable_id' => $newDocument->id
                ]);
            }

            $lesson->makeHidden(['course', 'module']);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Chuyển đổi loại bài học thành công!',
                'data' => $lesson->load(['lessonable']),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi chuyển đổi loại bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getVideoDuration($videoId)
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
