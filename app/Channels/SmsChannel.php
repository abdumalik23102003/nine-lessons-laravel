<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        // In production, integrate with actual SMS provider (e.g., SmsRu, Twilio)
        if (config('app.env') === 'testing') {
            \Illuminate\Support\Facades\Log::channel('testing')->info('SMS sent', [
                'phone' => $notifiable->phone,
                'message' => $message,
            ]);
        } else {
            // TODO: Implement real SMS sending
            \Illuminate\Support\Facades\Log::info('SMS would be sent', [
                'phone' => $notifiable->phone,
                'message' => $message,
            ]);
        }
    }
}
