<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

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
    // public function build()
    // {
    //     $time = $this->data['time'] ?? now()->format('H:i:s');

    //     return $this->subject("New Login Alert [{$time}]")
    //                 ->markdown('mail::message') // This will use the default Laravel email layout
    //                 ->with('data', $this->data);
    // }


    public function build()
    {
        $time = $this->data['time'] ?? now()->format('H:i:s');

        return $this->subject("New Login Alert [{$time}]")
                    ->markdown('emails.login_alert') // Use markdown view in emails folder
                    ->with('data', $this->data);
    }



}
