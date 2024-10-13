<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lessons\StoreLessonVideoRequest;
use App\Jobs\Client\UploadVideo;
use Illuminate\Http\Request;

class UploadVideoController extends Controller
{
    public function uploadVideo(StoreLessonVideoRequest $request) {
        $data = $request->all();
        UploadVideo::dispatch($data);
    }
}
