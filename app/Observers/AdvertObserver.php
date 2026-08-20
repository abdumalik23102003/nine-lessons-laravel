<?php

namespace App\Observers;

use App\Jobs\IndexAdvertJob;
use App\Jobs\RemoveAdvertFromIndexJob;
use App\Models\Advert;

class AdvertObserver
{
    public function saved(Advert $advert): void
    {
        IndexAdvertJob::dispatch($advert->id)->afterCommit();
    }

    public function deleted(Advert $advert): void
    {
        RemoveAdvertFromIndexJob::dispatch($advert->id)->afterCommit();
    }
}
