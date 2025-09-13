<?php

namespace App\Jobs;

use App\Mail\CustomEmail;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Email $email;

    /** @var array<string> */
    public array $tos;
    /** @var array<string> */
    public array $cc;
    /** @var array<string> */
    public array $bcc;

    public function __construct(Email $email, array $tos, array $cc = [], array $bcc = [])
    {
        $this->email = $email;
        $this->tos   = $tos;
        $this->cc    = $cc;
        $this->bcc   = $bcc;
    }

    public function handle(): void
    {
        $mailable = new CustomEmail($this->email->subject, $this->email->message);

        foreach ($this->tos as $to) {
            try {
                $msg = Mail::to($to);
                if (!empty($this->cc))  $msg->cc($this->cc);
                if (!empty($this->bcc)) $msg->bcc($this->bcc);
                $msg->send($mailable);
            } catch (\Throwable $e) {
                $this->email->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                throw $e;
            }
        }

        $this->email->update([
            'status'  => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }
}
