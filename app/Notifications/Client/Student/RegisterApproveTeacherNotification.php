<?php

namespace App\Notifications\Client\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegisterApproveTeacherNotification extends Notification implements ShouldBroadcast
{
    use Queueable;
    public $user;
    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'register_teacher',
            'id' => $this->user['id'],
            'student_name' => $this->user['name'],
            'message' => "Chúc mừng bạn đã trở thành giảng viên.",
            'url' => env('FE_URL') . 'instructor/dashboard/'
        ];
    }
    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => 'register_teacher',
            'id' => $this->user['id'],
            'student_name' => $this->user['name'],
            'message' => "Chúc mừng bạn đã trở thành giảng viên.",
            'url' => env('FE_URL') . 'instructor/dashboard/'
        ];
    }
}
