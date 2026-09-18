<?php

namespace App\Console\Commands;

use App\Services\Search\BannerIndexer;
use Illuminate\Console\Command;

class BannerSearchReindexCommand extends Command
{
    protected $signature = 'search:reindex-banners';
    protected $description = 'Reindex all active banners in Elasticsearch';

    public function handle(BannerIndexer $indexer): int
    {
        $this->info('Reindexing banners...');
        $indexer->reindexAll();
        $this->info('Banners reindexed successfully!');

        return Command::SUCCESS;
    }
}
