<?php

namespace App\Services\Search;

class SearchAggregations
{
    public function __construct(
        public array $categories = [],
        public array $regions = [],
    ) {}

    public static function fromElasticsearch(array $aggs): self
    {
        $categories = [];
        if (isset($aggs['categories']['buckets'])) {
            foreach ($aggs['categories']['buckets'] as $bucket) {
                $categories[] = [
                    'id' => (int) $bucket['key'],
                    'count' => $bucket['doc_count'],
                ];
            }
        }

        $regions = [];
        if (isset($aggs['regions']['buckets'])) {
            foreach ($aggs['regions']['buckets'] as $bucket) {
                $regions[] = [
                    'id' => (int) $bucket['key'],
                    'count' => $bucket['doc_count'],
                ];
            }
        }

        return new self($categories, $regions);
    }
}
