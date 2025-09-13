<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use RuntimeException;
use Twilio\Rest\Client;
use Twilio\Http\CurlClient;

class TwilioSmsChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTwilioSms')) {
            return;
        }

        $message = $notification->toTwilioSms($notifiable);

        $sid   = config('services.twilio.sid', env('TWILIO_ACCOUNT_SID'));
        $token = config('services.twilio.token', env('TWILIO_AUTH_TOKEN'));
        $from = config('services.twilio.from', env('TWILIO_FROM'));

        if (! $sid || ! $token || ! $from) {
            throw new RuntimeException('Twilio credentials missing.');
        }

        if (! $notifiable->phone) {
            throw new RuntimeException('Notifiable phone is empty.');
        }

        // only disable SSL checks in local/dev
        $httpClient = null;
        if (app()->environment('local')) {
            $curlOptions = [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false];
            $httpClient = new CurlClient($curlOptions);
        }

        $client = new Client($sid, $token, null, null, $httpClient);

        $client->messages->create(
            $notifiable->phone,
            [
                'from' => $from,
                'body' => $message,
            ]
        );
    }
}
