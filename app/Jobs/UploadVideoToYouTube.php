<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Video;
use App\Notifications\VideoUploadedNotification;
use Google\Client as GoogleClient;
use Google\Service\YouTube;
use Google\Http\MediaFileUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadVideoToYoutube implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $videoData;
    protected $accessToken;

    public function __construct($videoData, $accessToken)
    {
        $this->videoData = $videoData;
        $this->accessToken = $accessToken;
    }

    public function handle()
    {
        $client = new GoogleClient();
        $client->setAccessToken($this->accessToken);

        $youtube = new YouTube($client);

        $video = new YouTube\Video();
        $video->setSnippet(new YouTube\VideoSnippet());
        $video->getSnippet()->setTitle($this->videoData['title']);
        $video->setStatus(new YouTube\VideoStatus());
        $video->getStatus()->setPrivacyStatus('public');

        $videoPath = $this->videoData['path'];


        $chunkSize = 1 * 1024 * 1024;
        $client->setDefer(true);

        $insertRequest = $youtube->videos->insert('status,snippet', $video);

        $media = new MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSize
        );

        $media->setFileSize(filesize($videoPath));

        $status = false;
        $handle = fopen($videoPath, "rb");

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $client->setDefer(false);



        $data = [
            'title' => $this->videoData['title'],
            'url' => 'https://www.youtube.com/watch?v=' . $status['id'],
            'duration' => 10000,
        ];

        $moduleId = $this->videoData['id_module'];
        $description = $this->videoData['description'];

        $this->addLessonVideo($data, $moduleId, $description);

        Storage::disk('public')->delete('videos/' . basename($videoPath));
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

        $user = User::find(auth()->user()->id);
        $user->notify(new VideoUploadedNotification($newLessonVideo));
    }
}
