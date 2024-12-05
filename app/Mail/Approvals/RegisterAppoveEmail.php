<?php

namespace App\Mail\Approvals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisterAppoveEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject("Chúc mừng bạn đã trở thành giảng viên của hệ thống Coursea")
            ->view('emails.approvals.register_teacher')
            ->with([
                'user' => $this->user,
            ]);
    }
}
