<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyOTP extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otpCode;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->markdown('emails.auth.verify_otp')
                    ->subject('Xác thực tài khoản')
                    ->with([
                        'otpCode' => $this->otpCode,
                    ]);
    }
}
