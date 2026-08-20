<?php

namespace App\Services\Search;

use App\Models\Advert;
use App\Models\Value;
use Elastic\Elasticsearch\Client;

class AdvertIndexer
{
    public function __construct(
        private readonly Client $client,
    ) {}

    public function clear(): void
    {
        $this->client->deleteByQuery([
            'index' => 'adverts',
            'body' => [
                'query' => ['match_all' => new \stdClass()],
            ],
        ]);
    }

    public function index(Advert $advert): void
    {
        $regions = [];
        $region = $advert->region;
        while ($region) {
            $regions[] = $region->id;
            $region = $region->parent;
        }

        $this->client->index([
            'index' => 'adverts',
            'id' => (string) $advert->id,
            'body' => [
                'id' => $advert->id,
                'published_at' => $advert->published_at?->format(DATE_ATOM),
                'title' => $advert->title,
                'content' => $advert->content,
                'price' => $advert->price,
                'status' => $advert->status,
                'categories' => [$advert->category_id, ...$advert->category->ancestorIds()],
                'regions' => $regions ?: [0],
                'values' => $advert->values->map(fn (Value $value) => [
                    'attribute' => $value->attribute_id,
                    'value_string' => (string) $value->value,
                    'value_int' => (int) $value->value,
                ])->all(),
            ],
        ]);
    }

    public function remove(int $advertId): void
    {
        $this->client->delete([
            'index' => 'adverts',
            'id' => (string) $advertId,
        ]);
    }
}
