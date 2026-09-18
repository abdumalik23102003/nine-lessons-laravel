<?php

namespace App\Jobs;

use App\Models\Advert;
use App\Services\Search\AdvertIndexer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReindexAdvertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $advertId,
        public string $action = 'updated', // updated, deleted
    ) {
        $this->onQueue('default');
    }

    public function handle(AdvertIndexer $indexer): void
    {
        if ($this->action === 'deleted') {
            $indexer->remove($this->advertId);
        } else {
            $advert = Advert::find($this->advertId);
            if ($advert) {
                $indexer->index($advert);
            }
        }
    }
}
