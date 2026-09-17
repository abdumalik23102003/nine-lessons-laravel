<?php

namespace App\Console\Commands;

use App\Models\Advert;
use App\Services\Search\AdvertIndexer;
use Illuminate\Console\Command;

class AdvertSearchReindexCommand extends Command
{
    protected $signature = 'search:reindex {--clear : Clear index before reindexing}';

    protected $description = 'Reindex all adverts to Elasticsearch';

    public function handle(AdvertIndexer $indexer): int
    {
        if ($this->option('clear')) {
            $this->info('Clearing Elasticsearch index...');
            $indexer->clear();
        }

        $this->info('Reindexing adverts...');

        Advert::chunk(100, function ($adverts) use ($indexer) {
            foreach ($adverts as $advert) {
                $indexer->index($advert);
            }
            $this->output->write('.');
        });

        $this->newLine();
        $this->info('Reindexing completed!');

        return self::SUCCESS;
    }
}
