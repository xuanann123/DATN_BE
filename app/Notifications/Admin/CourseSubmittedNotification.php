<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct($course)
    {
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'message' => 'Có khóa học mới cần được kiểm duyệt.',
            'url' => route('admin.approval.courses.detail', ['id' => $this->course->id]),
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'message' => 'Có khóa học mới cần được kiểm duyệt.',
            'url' => route('admin.approval.courses.detail', ['id' => $this->course->id]),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'message' => 'Có khóa học mới cần được kiểm duyệt.',
            'url' => route('admin.approval.courses.detail', ['id' => $this->course->id]),
        ];
    }
}
