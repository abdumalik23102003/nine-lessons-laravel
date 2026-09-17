<?php

namespace App\Notifications;

use App\Models\Advert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdvertRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Advert $advert,
        public string $reason,
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
            ->subject('Sizning e\'lon rad etildi')
            ->greeting('Assalomu alaikum ' . $notifiable->name . '!')
            ->line('Sizning e\'lon "' . $this->advert->title . '" rad etildi.')
            ->line('Sababi: ' . $this->reason)
            ->action('E\'loningni qayta tahrirlab yuborish', url('/adverts/' . $this->advert->id . '/edit'))
            ->line('Rahmat!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'advert_id' => $this->advert->id,
            'advert_title' => $this->advert->title,
            'message' => 'E\'loningiz rad etildi: ' . $this->reason,
        ];
    }
}
