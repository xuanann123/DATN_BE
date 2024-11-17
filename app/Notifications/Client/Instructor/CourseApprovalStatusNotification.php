<?php

namespace App\Notifications\Client\Instructor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseApprovalStatusNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $course;
    protected $status; // tu choi hoac chap thuan
    protected $conditions; // dieu kien cua khoa hoc duoc chap thuan
    protected $admin_comments; // li do tu choi cua admin (neu co)

    /**
     * Create a new notification instance.
     */
    public function __construct($course, $status, $conditions = null, $admin_comments = null)
    {
        $this->course = $course;
        $this->status = $status;
        $this->conditions = $conditions;
        $this->admin_comments = $admin_comments;
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
            'type' => $this->status === 'approved' ? 'course_approved' : 'course_rejected',
            'user_role' => 'instructor',
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'status' => $this->status,
            'conditions' => $this->conditions,
            'admin_comments' => $this->admin_comments,
            'message' => $this->status === 'approved' ? 'Khóa học của bạn đã được chấp thuận' : 'Khóa học của bạn đã bị từ chối',
            'url' => env('FE_URL') . 'instructor/courses/' . $this->course->id . '/manage/goals'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => $this->status === 'approved' ? 'course_approved' : 'course_rejected',
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'status' => $this->status,
            'conditions' => $this->conditions,
            'admin_comments' => $this->admin_comments,
            'message' => $this->status === 'approved' ? 'Khóa học của bạn đã được chấp thuận' : 'Khóa học của bạn đã bị từ chối',
            'url' => env('FE_URL') . 'instructor/courses/' . $this->course->id . '/manage/goals'
        ];
    }
}
