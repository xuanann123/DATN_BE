<?php

namespace App\Notifications\Client\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RegisterTeacherNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $education;
    protected $user;


    public function __construct(array $education, $user)
    {
        $this->education = $education;
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
            'id' => $this->education['id'],
            'student_name' => $this->education['name_student'],
            'message' => "Sinh viên {$this->education['name_student']} muốn đăng ký trở thành giảng viên.",
            'url' => route('admin.approval.teachers.detail', ['id' => $this->user->id]),
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => 'register_teacher',
            'id' => $this->education['id'],
            'name_student' => $this->education['name_student'],
            'message' => "Sinh viên {$this->education['name_student']} muốn đăng ký trở thành giảng viên.",
            'url' => route('admin.approval.teachers.detail', ['id' => $this->user->id]),
        ];
    }
}
