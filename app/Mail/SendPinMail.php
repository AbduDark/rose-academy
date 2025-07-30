<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPinMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pin;

    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    public function build()
    {
        return $this->subject('Your Verification PIN')
                    ->view('emails.pin');
    }
}
