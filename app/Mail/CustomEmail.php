<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $htmlMessage;

    public function __construct(string $subjectLine, string $htmlMessage)
    {
        $this->subjectLine = $subjectLine;
        $this->htmlMessage = $htmlMessage;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->markdown('emails.custom', [
                        'htmlMessage' => $this->htmlMessage,
                    ]);
    }
}
