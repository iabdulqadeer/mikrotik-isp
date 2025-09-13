<?php

namespace App\Notifications;

use App\Notifications\Channels\TwilioSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $code, public string $phone) {}

    public function via($notifiable): array
    {
        // Use Twilio in prod; log fallback in local if you want
        return [TwilioSmsChannel::class];
    }
    
    public function toTwilioSms($notifiable): string
    {
        return "Your verification code is {$this->code}. It expires in 10 minutes.";
    }
}