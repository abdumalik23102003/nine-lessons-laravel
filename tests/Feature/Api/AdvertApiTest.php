<?php

use App\Models\Advert;
use App\Models\Photo;
use App\Services\Search\AdvertIndexer;
use App\Services\Search\AdvertSearchService;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function () {
    // Advert::factory()->create() fires AdvertObserver -> IndexAdvertJob,
    // which would try to reach real Elasticsearch under QUEUE_CONNECTION=sync.
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('index returns adverts produced by the search service, shaped by the resource', function () {
    $advert = Advert::factory()->active()->create(['title' => 'iPhone 13']);

    $paginator = new LengthAwarePaginator(
        items: collect([$advert]),
        total: 1,
        perPage: 20,
        currentPage: 1,
    );

    $this->mock(AdvertSearchService::class)
        ->shouldReceive('search')
        ->once()
        ->andReturn($paginator);

    $response = $this->getJson(route('api.adverts.index'));

    $response
        ->assertOk()
        ->assertJsonPath('data.0.title', 'iPhone 13')
        ->assertJsonPath('data.0.category.id', $advert->category_id)
        ->assertJsonPath('meta.total', 1);
});

test('index rejects an invalid category_id before reaching the search service', function () {
    // AdvertSearchService is intentionally NOT mocked here: if validation
    // is working, the controller body (and the service) is never reached.
    $response = $this->getJson(route('api.adverts.index', ['category_id' => 999999]));

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('category_id');
});

test('show returns full advert data for an active advert', function () {
    $advert = Advert::factory()->active()->create(['title' => 'MacBook Pro']);
    Photo::factory()->for($advert)->create();

    $response = $this->getJson(route('api.adverts.show', $advert));

    $response
        ->assertOk()
        ->assertJsonPath('data.title', 'MacBook Pro')
        ->assertJsonCount(1, 'data.photos');
});

test('show returns 403 for a draft advert', function () {
    $advert = Advert::factory()->create(); // default status: draft

    $this->getJson(route('api.adverts.show', $advert))
        ->assertForbidden();
});

test('show returns 404 for a non-existent advert', function () {
    $this->getJson('/api/adverts/999999')
        ->assertNotFound();
});
