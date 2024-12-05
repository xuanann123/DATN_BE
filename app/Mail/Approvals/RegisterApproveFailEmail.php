<?php

namespace App\Mail\Approvals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisterApproveFailEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $user;
    public $admin_comments;


    public function __construct($user, $admin_comments)
    {
        $this->user = $user;
        $this->admin_comments = $admin_comments;
    }

    public function build()
    {

        return $this->subject("Lời cám ơn hẹn bạn ứng tuyền lần sau!")
            ->view('emails.approvals.register_teacher_fail')
            ->with([
                'user' => $this->user,
                'admin_comments' => $this->admin_comments
            ]);
    }
}
