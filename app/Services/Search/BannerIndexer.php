<?php

namespace App\Services\Search;

use App\Models\Banner;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Support\Facades\Log;

class BannerIndexer
{
    private const INDEX = 'banners';

    public function __construct(
        private Client $client,
    ) {}

    public function ensureIndex(): void
    {
        try {
            if ($this->client->indices()->exists(index: self::INDEX)->asBool()) {
                return;
            }

            $this->client->indices()->create([
                'index' => self::INDEX,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'user_id' => ['type' => 'integer'],
                            'name' => ['type' => 'text'],
                            'url' => ['type' => 'keyword'],
                            'file' => ['type' => 'keyword'],
                            'status' => ['type' => 'keyword'],
                            'format' => ['type' => 'keyword'],
                            'views' => ['type' => 'integer'],
                            'clicks' => ['type' => 'integer'],
                            'published_at' => ['type' => 'date'],
                            'expires_at' => ['type' => 'date'],
                            'created_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ]);

            Log::info('Banners index created');
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning('Could not create banners index', ['error' => $e->getMessage()]);
        }
    }

    public function index(Banner $banner): void
    {
        try {
            $this->client->index([
                'index' => self::INDEX,
                'id' => (string) $banner->id,
                'body' => [
                    'id' => $banner->id,
                    'user_id' => $banner->user_id,
                    'name' => $banner->name,
                    'url' => $banner->url,
                    'file' => $banner->file,
                    'status' => $banner->status,
                    'format' => $banner->format,
                    'views' => $banner->views,
                    'clicks' => $banner->clicks,
                    'published_at' => $banner->published_at,
                    'expires_at' => $banner->expires_at,
                    'created_at' => $banner->created_at,
                ],
            ]);

            Log::debug("Banner {$banner->id} indexed");
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning("Could not index banner {$banner->id}", ['error' => $e->getMessage()]);
        }
    }

    public function remove(int $bannerId): void
    {
        try {
            $this->client->delete([
                'index' => self::INDEX,
                'id' => (string) $bannerId,
            ]);

            Log::debug("Banner {$bannerId} removed from index");
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning("Could not remove banner {$bannerId} from index", ['error' => $e->getMessage()]);
        }
    }

    public function reindexAll(): void
    {
        try {
            if ($this->client->indices()->exists(index: self::INDEX)->asBool()) {
                $this->client->indices()->delete(index: self::INDEX);
            }

            $this->ensureIndex();

            Banner::where('status', Banner::STATUS_ACTIVE)
                ->chunkById(100, function ($banners) {
                    foreach ($banners as $banner) {
                        $this->index($banner);
                    }
                });

            Log::info('Banners reindexed successfully');
        } catch (AuthenticationException|ClientResponseException|ServerResponseException $e) {
            Log::warning('Could not reindex banners', ['error' => $e->getMessage()]);
        }
    }
}
