<?php

namespace App\Jobs;

use App\Models\Banner;
use App\Services\Search\BannerIndexer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReindexBannerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bannerId,
        public string $action = 'updated',
    ) {
        $this->onQueue('default');
    }

    public function handle(BannerIndexer $indexer): void
    {
        if ($this->action === 'deleted') {
            $indexer->remove($this->bannerId);
        } else {
            $banner = Banner::find($this->bannerId);
            if ($banner) {
                $indexer->index($banner);
            }
        }
    }
}
