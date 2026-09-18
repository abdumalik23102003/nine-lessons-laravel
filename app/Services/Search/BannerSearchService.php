<?php

namespace App\Services\Search;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BannerSearchService
{
    private const INDEX = 'banners';
    private const PER_PAGE = 20;

    public function __construct(
        private Client $client,
    ) {}

    public function search(
        ?string $query = null,
        int $page = 1,
        int $perPage = self::PER_PAGE,
    ): Paginator {
        try {
            $from = ($page - 1) * $perPage;

            $body = [
                'query' => $this->buildQuery($query),
                'from' => $from,
                'size' => $perPage,
                'sort' => [
                    ['created_at' => ['order' => 'desc']],
                ],
            ];

            $response = $this->client->search([
                'index' => self::INDEX,
                'body' => $body,
            ]);

            $total = $response['hits']['total']['value'] ?? 0;
            $items = $this->formatResults($response['hits']['hits'] ?? []);

            return new Paginator(
                $items,
                $perPage,
                $page,
                [
                    'path' => 'api/banners/search',
                    'query' => request()->query(),
                ]
            );
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning('Banner search error', ['error' => $e->getMessage()]);
            return new Paginator([], $perPage, $page);
        }
    }

    public function aggregations(): array
    {
        try {
            $response = $this->client->search([
                'index' => self::INDEX,
                'body' => [
                    'size' => 0,
                    'aggs' => [
                        'statuses' => [
                            'terms' => [
                                'field' => 'status',
                                'size' => 10,
                            ],
                        ],
                        'formats' => [
                            'terms' => [
                                'field' => 'format',
                                'size' => 10,
                            ],
                        ],
                    ],
                ],
            ]);

            return $response['aggregations'] ?? [];
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning('Banner aggregations error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function buildQuery(?string $query): array
    {
        if (!$query) {
            return ['match_all' => new \stdClass()];
        }

        return [
            'match' => [
                'name' => [
                    'query' => $query,
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];
    }

    private function formatResults(array $hits): Collection
    {
        return collect($hits)->map(fn (array $hit) => [
            'id' => $hit['_source']['id'] ?? null,
            'user_id' => $hit['_source']['user_id'] ?? null,
            'name' => $hit['_source']['name'] ?? null,
            'url' => $hit['_source']['url'] ?? null,
            'file' => $hit['_source']['file'] ?? null,
            'status' => $hit['_source']['status'] ?? null,
            'format' => $hit['_source']['format'] ?? null,
            'views' => $hit['_source']['views'] ?? null,
            'clicks' => $hit['_source']['clicks'] ?? null,
            'published_at' => $hit['_source']['published_at'] ?? null,
            'expires_at' => $hit['_source']['expires_at'] ?? null,
        ]);
    }
}
