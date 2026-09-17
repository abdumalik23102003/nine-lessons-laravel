<?php

namespace App\Notifications;

use App\Models\Advert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdvertModerationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Advert $advert,
    ) {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Sizning e\'lon moderatsiyadan o\'tdi')
            ->greeting('Assalomu alaikum ' . $notifiable->name . '!')
            ->line('Sizning e\'lon "' . $this->advert->title . '" moderatsiyadan o\'tdi va faol holatiga o\'tkazildi.')
            ->line('E\'lon qo\'yilish vaqti: ' . $this->advert->expires_at?->format('d.m.Y H:i'))
            ->action('E\'loningni ko\'rish', url('/adverts/' . $this->advert->id))
            ->line('Rahmat!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'advert_id' => $this->advert->id,
            'advert_title' => $this->advert->title,
            'message' => 'E\'loningiz moderatsiyadan o\'tdi',
        ];
    }
}
