<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $mailable;

    /**
     * Create a new job instance.
     */
    public function __construct($user, Mailable $mailable)
    {
        $this->user = $user;
        $this->mailable = $mailable;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::to($this->user->email)->send($this->mailable);

            Log::info("Email sent successfully to " . $this->user->email);
        } catch (\Exception $e) {
            Log::error("Failed to send email to " . $this->user->email . ": " . $e->getMessage());
        }
    }

}
