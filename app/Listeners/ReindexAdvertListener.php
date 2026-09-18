<?php

namespace App\Listeners;

use App\Events\AdvertWasChanged;
use App\Jobs\ReindexAdvertJob;
use App\Models\Advert;

class ReindexAdvertListener
{
    public function handle(AdvertWasChanged $event): void
    {
        // Only reindex if status is active (active adverts are searchable)
        if ($event->advert->status === Advert::STATUS_ACTIVE || $event->action === 'deleted') {
            ReindexAdvertJob::dispatch($event->advert->id, $event->action);
        }
    }
}
