<?php

namespace App\Events;

use App\Models\Banner;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BannerWasChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Banner $banner,
        public string $action = 'updated', // created, updated, deleted
    ) {}
}
