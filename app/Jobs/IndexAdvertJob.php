<?php

namespace App\Jobs;

use App\Models\Advert;
use App\Services\Search\AdvertIndexer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexAdvertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $advertId){}

    public function handle(AdvertIndexer $indexer): void
    {
        $advert = Advert::query()->find($this->advertId);

        if ($advert === null){
            return;
        }
        $indexer->index($advert);
    }
}
