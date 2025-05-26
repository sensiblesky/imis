<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $time = $this->data['time'] ?? now()->format('H:i:s');

        return $this->subject("Password Reset Request [{$time}]")
                    ->markdown('emails.password_reset') // SAME as your working login email
                    ->with('data', $this->data);
    }
}
