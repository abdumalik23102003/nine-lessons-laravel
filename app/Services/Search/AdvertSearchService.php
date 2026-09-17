<?php

namespace App\Services\Search;

use App\Http\Requests\Adverts\SearchRequest;
use App\Models\Advert;
use Elastic\Elasticsearch\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class AdvertSearchService
{
    public function __construct(
        private readonly Client $client,
    ) {}

    public function search(SearchRequest $request, int $perPage = 20): LengthAwarePaginator
    {
        $page = (int) $request->get('page', 1);

        $must = [
            ['term' => ['status' => Advert::STATUS_ACTIVE]],
        ];

        if ($categoryId = $request->integer('category_id')) {
            $must[] = ['term' => ['categories' => $categoryId]];
        }

        if ($regionId = $request->integer('region_id')) {
            $must[] = ['term' => ['regions' => $regionId]];
        }

        if ($text = $request->string('text')->toString()) {
            $must[] = [
                'multi_match' => [
                    'query' => $text,
                    'fields' => ['title^3', 'content'],
                ],
            ];
        }

        if ($priceFrom = $request->integer('price_from')) {
            $must[] = ['range' => ['price' => ['gte' => $priceFrom]]];
        }

        if ($priceTo = $request->integer('price_to')) {
            $must[] = ['range' => ['price' => ['lte' => $priceTo]]];
        }

        // Attributes filter
        if ($attributes = $request->array('attributes')) {
            foreach ($attributes as $attr) {
                $must[] = [
                    'nested' => [
                        'path' => 'values',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['values.attribute' => $attr['id']]],
                                    ['match' => ['values.value_string' => $attr['value']]],
                                ],
                            ],
                        ],
                    ],
                ];
            }
        }

        $sort = $request->string('sort')->toString();

        $sortClause = match ($sort) {
            'price_asc' => [['price' => ['order' => 'asc']], ['id' => ['order' => 'asc']]],
            'price_desc' => [['price' => ['order' => 'desc']], ['id' => ['order' => 'asc']]],
            'newest' => [['published_at' => ['order' => 'desc']], ['id' => ['order' => 'asc']]],
            default => $text ? [] : [['published_at' => ['order' => 'desc']], ['id' => ['order' => 'asc']]],
        };

        $response = $this->client->search([
            'index' => 'adverts',
            'body' => [
                '_source' => false,
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'sort' => $sortClause,
                'query' => [
                    'bool' => ['must' => $must],
                ],
                'aggs' => [
                    'categories' => [
                        'terms' => [
                            'field' => 'categories',
                            'size' => 100,
                        ],
                    ],
                    'regions' => [
                        'terms' => [
                            'field' => 'regions',
                            'size' => 100,
                        ],
                    ],
                ],
            ],
        ])->asArray();

        $ids = array_column($response['hits']['hits'], '_id');
        $total = $response['hits']['total']['value'];

        if (! $ids) {
            return new Paginator([], $total, $perPage, $page);
        }

        $items = Advert::with(['category', 'region', 'photos', 'values.attribute'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($item) => array_search($item->id, $ids));

        return new Paginator($items->values(), $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
