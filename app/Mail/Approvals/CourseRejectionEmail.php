<?php

namespace App\Mail\Approvals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRejectionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $course;
    public $conditions;
    public $admin_comments;

    public function __construct($course, $conditions, $admin_comments = null)
    {
        $this->course = $course;
        $this->conditions = $conditions;
        $this->admin_comments = $admin_comments;
    }

    public function build()
    {
        return $this->subject('Khóa học bị từ chối: ' . $this->course->name)
            ->view('emails.approvals.reject')
            ->with([
                'course' => $this->course,
                'conditions' => $this->conditions,
                'admin_comments' => $this->admin_comments
            ]);
    }
}
