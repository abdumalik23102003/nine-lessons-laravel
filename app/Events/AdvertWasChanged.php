<?php

namespace App\Events;

use App\Models\Advert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdvertWasChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Advert $advert,
        public string $action = 'updated', // created, updated, deleted, moderated
    ) {}
}
