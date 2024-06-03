<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
    // the build method is called to make a body content of the mail to send to the user.
    public function build() {
        return $this->view('emails.confirmation')->with([
            'user' => $this->user,
            'token' => $this->token,
            'url' => route('confirm', ['token' => $this->token]),
        ]);
    }
}
