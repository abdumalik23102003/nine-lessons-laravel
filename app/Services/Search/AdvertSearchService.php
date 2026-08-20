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
            ],
        ])->asArray();

        $ids = array_column($response['hits']['hits'], '_id');
        $total = $response['hits']['total']['value'];

        if (! $ids) {
            return new Paginator([], $total, $perPage, $page);
        }

        $items = Advert::with(['category', 'region', 'photos'])
            ->whereIn('id', $ids)
            ->orderByRaw('FIELD(id,' . implode(',', $ids) . ')')
            ->get();

        return new Paginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
