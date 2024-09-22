<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VideoUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lessonVideo;

    public function __construct($lessonVideo)
    {
        $this->lessonVideo = $lessonVideo;
    }


    public function via(object $notifiable)
    {
        return ['mail'];
    }


    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject('Video Upload Successful')
            ->greeting('Hello,')
            ->line('Your video titled "' . $this->lessonVideo->title . '" has been uploaded successfully.')
            ->action('View Video', $this->lessonVideo->url)
            ->line('Thank you for uploading the video!');
    }
}
