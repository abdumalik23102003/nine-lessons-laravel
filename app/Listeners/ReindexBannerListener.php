<?php

namespace App\Listeners;

use App\Events\BannerWasChanged;
use App\Jobs\ReindexBannerJob;
use App\Models\Banner;

class ReindexBannerListener
{
    public function handle(BannerWasChanged $event): void
    {
        // Only reindex if status is active (active banners are searchable)
        if ($event->banner->status === Banner::STATUS_ACTIVE || $event->action === 'deleted') {
            ReindexBannerJob::dispatch($event->banner->id, $event->action);
        }
    }
}
