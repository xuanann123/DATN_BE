<?php

namespace App\Mail\Approvals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $course;
    public $conditions;

    public function __construct($course, $conditions)
    {
        $this->course = $course;
        $this->conditions = $conditions;
    }

    public function build()
    {
        return $this->subject('Khóa học bị từ chối: ' . $this->course->title)
            ->view('emails.approvals.reject')
            ->with([
                'course' => $this->course,
                'conditions' => $this->conditions,
            ]);
    }
}
