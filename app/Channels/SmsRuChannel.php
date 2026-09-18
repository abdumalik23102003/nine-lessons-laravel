<?php

namespace App\Channels;

use App\Services\Sms\SmsRuClient;
use Illuminate\Notifications\Notification;

class SmsRuChannel
{
    public function __construct(
        private SmsRuClient $client,
    ) {}

    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        if (!$notifiable->phone) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $this->client->send($notifiable->phone, $message);
    }
}
