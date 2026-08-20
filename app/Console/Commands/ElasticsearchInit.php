<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class ElasticsearchInit extends Command
{
    protected $signature = 'elasticsearch:init';

    protected $description = "Adverts uchun Elasticsearch indeksini (mapping bilan) yaratadi";

    public function handle(Client $client): int
    {
        if ($client->indices()->exists(['index' => 'adverts'])->asBool()) {
            $client->indices()->delete(['index' => 'adverts']);
            $this->info("Eski 'adverts' indeksi o'chirildi.");
        }

        $client->indices()->create([
            'index' => 'adverts',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'title' => ['type' => 'text'],
                        'content' => ['type' => 'text'],
                        'price' => ['type' => 'integer'],
                        'status' => ['type' => 'keyword'],
                        'published_at' => ['type' => 'date'],
                        'categories' => ['type' => 'integer'],
                        'regions' => ['type' => 'integer'],
                        'values' => [
                            'type' => 'nested',
                            'properties' => [
                                'attribute' => ['type' => 'integer'],
                                'value_string' => ['type' => 'keyword'],
                                'value_int' => ['type' => 'integer'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->info("'adverts' indeksi mapping bilan yaratildi.");

        return self::SUCCESS;
    }
}
