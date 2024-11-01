<?php

namespace App\Mail\Approvals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseApproveEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $course;
    public $conditions;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function build()
    {
        return $this->subject('Khóa học đã được chấp thuận: ' . $this->course->title)
            ->view('emails.approvals.approve')
            ->with([
                'course' => $this->course,
            ]);
    }
}
