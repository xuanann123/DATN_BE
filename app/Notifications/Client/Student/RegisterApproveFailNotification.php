<?php

namespace App\Notifications\Client\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegisterApproveFailNotification extends Notification implements ShouldBroadcast
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
            'user_role' => 'member',
            'id' => $this->user['id'],
            'student_name' => $this->user['name'],
            'message' => "Hệ thống từ chối ứng tuyển vị trí giảng viên, cám ơn bạn!.",
            'url' => env('FE_URL') . 'instructor/register'
        ];
    }
    public function toBroadcast(object $notifiable): array
    {
        return [
            'user_type' => 'register_teacher',
            'id' => $this->user['id'],
            'status'=> false,
            'student_name' => $this->user['name'],
            'message' => "Hệ thống từ chối ứng tuyển vị trí giảng viên, cám ơn bạn!.",
            'url' => env('FE_URL') . 'instructor/register'
        ];
    }
}
