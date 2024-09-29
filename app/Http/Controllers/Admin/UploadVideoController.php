<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lessons\StoreLessonVideoRequest;
use App\Jobs\UploadVideoToYoutube;
use Google\Client as GoogleClient;
//use Google\Service\YouTube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UploadVideoController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope([
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.force-ssl'
        ]);
    }

    public function redirectToGoogle()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->input('code');

        if ($code) {
            $this->client->fetchAccessTokenWithAuthCode($code);
            $accessToken = $this->client->getAccessToken();

            session([
                'youtube_access_token' => $accessToken,
            ]);

            return redirect()->route('admin.courses.detail', ['id' => Session::get('course_id')]);
        }

        return redirect()->route('admin.lessons.youtube.auth')->with('error', 'Unable to authenticate');
    }

    public function storeLessonVideo(StoreLessonVideoRequest $request)
    {

        if (!session()->has('youtube_access_token')) {
            return redirect()->route('admin.lessons.youtube.auth')->with('error', 'Authentication required');
        }

        $videoPath = $request->file('video')->store('videos', 'public');

        $videoData = [
            'id_module' => $request->input('id_module'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'path' => storage_path('app/public/' . $videoPath),
        ];


        UploadVideoToYoutube::dispatch($videoData, session('youtube_access_token'));

        return back()->with('success', 'Chúng tôi sẽ thông báo khi video đã sẵn sàng!');
    }

}
