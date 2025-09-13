<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LogSmsChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toLogSms')) {
            $payload = $notification->toLogSms($notifiable);

            // Accept string or ['message' => '...']
            $message = is_array($payload) ? ($payload['message'] ?? '') : (string) $payload;
            if ($message !== '') {
                Log::info($message);
            } else {
                Log::info('SMS log channel: empty message.');
            }
        } else {
            Log::info('SMS log channel: notification missing toLogSms().');
        }
    }
}
