<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lesson;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lessons\StoreLessonRequest;
use App\Models\Video;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request)
    {
        $request->validated();

        // dd($request->all());
        $maxPosition = Lesson::where('id_module', $request->id_module)->max('position');

        $document = Document::create([
            'content' => $request->content,
        ]);

        $lesson = Lesson::create([
            'title' => $request->title,
            'id_module' => $request->id_module,
            'content_type' => 'document',
            'lessonable_type' => Document::class,
            'lessonable_id' => $document->id,
            'position' => $maxPosition + 1
        ]);

        return response()->json([
            'message' => 'Thêm bài học thành công!',
            'code' => 0,
            'data' => [
                'id_module' => $lesson->id_module,
                'id' => $lesson->id,
                'title' => $lesson->title,
            ],
            'status' => 200,
        ], 200);
    }

    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->lessonable_type == Document::class) {
            return response()->json([
                'title' => $lesson->title,
                'lesson_type' => 'document',
                'content' => $lesson->lessonable->content
            ]);
        }

        if ($lesson->lessonable_type == Video::class) {
            return response()->json([
                'title' => $lesson->title,
                'lesson_type' => 'video',
                'video_youtube_id' => $lesson->lessonable->video_youtube_id
            ]);
        }
    }
}
