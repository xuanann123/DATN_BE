<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $resetPasswordUrl = url('/api/reset-password/?token=' . $this->token);

        return $this->view('emails.forgot_password')
                    ->subject('Yêu cầu đặt lại mật khẩu')
                    ->with([
                        'resetPasswordUrl' => $resetPasswordUrl,
                    ]);
    }
}
