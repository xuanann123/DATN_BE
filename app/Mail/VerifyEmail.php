<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationToken;

    public function __construct($verificationToken)
    {
        $this->verificationToken = $verificationToken;
    }

    public function build()
    {
        $verifyUrl = config('app.fe_url') . 'verify-email/' . $this->verificationToken;

        return $this->markdown('emails.verify_email')
                    ->subject('Xác Thực Email')
                    ->with([
                        'verifyUrl' => $verifyUrl,
                    ]);
    }
}
