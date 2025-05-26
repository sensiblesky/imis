<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Send2faOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $firstname;

    public function __construct($otp, $firstname)
    {
        $this->otp = $otp;
        $this->firstname = $firstname;
    }

    public function build()
    {
        return $this->subject('Two-Factor Authentication OTP')
                    ->markdown('emails.two_fa_otp')
                    ->with([
                        'otp' => $this->otp,
                        'firstname' => $this->firstname,
                    ]);
    }
}