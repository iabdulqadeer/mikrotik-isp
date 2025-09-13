<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericUserNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public ?string $message = null,
        public ?string $actionUrl = null,
        public ?string $icon = null // e.g. 'check', 'alert', etc. (purely cosmetic)
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'      => $this->title,
            'message'    => $this->message,
            'action_url' => $this->actionUrl,
            'icon'       => $this->icon,
        ];
    }
}
