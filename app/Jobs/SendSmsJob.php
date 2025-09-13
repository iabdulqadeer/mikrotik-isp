<?php

namespace App\Jobs;

use App\Models\SmsMessage;
use App\Services\TwilioSmsService;
use App\Support\SmsTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public SmsMessage $sms) {}

    public function handle(TwilioSmsService $twilio): void
    {
        $user = $this->sms->user;

        $body = SmsTemplate::render($this->sms->message, $user);

        try {
            $res = $twilio->send($this->sms->phone, $body);

            $this->sms->update([
                'twilio_sid' => $res['sid'] ?? null,
                'status'     => 'sent',
                'sent_at'    => now(),
                'meta'       => ['twilio' => $res],
            ]);
        } catch (Throwable $e) {
            $this->sms->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
