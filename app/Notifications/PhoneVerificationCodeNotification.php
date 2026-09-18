<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PhoneVerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
    ) {
        $this->onQueue('default');
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        return "Your phone verification code: {$this->code}. Valid for 10 minutes.";
    }

    public function toArray($notifiable): array
    {
        return [
            'code' => $this->code,
        ];
    }
}
