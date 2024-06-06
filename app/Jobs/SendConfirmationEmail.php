<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\UserConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $signedURL;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $signedURL)
    {
        $this->user = $user;
        $this->signedURL = $signedURL;
    }

    /**
     * Execute the job.
     *
     * @return void
     * this will handle the incoming request for the email sending to the user
     */

    public function handle()
    {
        Mail::to($this->user->email)->send(new UserConfirmationMail($this->user, $this->signedURL));
    }
}
