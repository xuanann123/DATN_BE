<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Lesson;
use App\Models\Video;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function lessonDetail(Request $request) {
        $lessonId = $request->id;
        $lesson = Lesson::where([
            ['id', $lessonId],
            ['is_active', 1]
        ])->first();

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bài học không tồn tại'
            ], 204);
        }

        if($lesson->content_type == 'video') {
            $lessonDetail = Video::find($lesson->lessonable_id);
        }else {
            $lessonDetail = Document::find($lesson->lessonable_id);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết bài học.',
            'data' => [
                'type_lesson' => $lesson->content_type,
                'lesson_title' => $lesson->title,
                'lesson_thumbnail' => $lesson->thumbnail,
                'lesson_description' => $lesson->description,
                'lesson_detail' => $lessonDetail,
            ]
        ], 200);
    }
}
