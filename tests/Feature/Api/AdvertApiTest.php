<?php

use App\Models\Advert;
use App\Models\Category;
use App\Models\Photo;
use App\Models\User;
use App\Services\Search\AdvertIndexer;
use App\Services\Search\AdvertSearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Sanctum\Sanctum;

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

test('guests cannot access their own adverts list', function () {
    $this->getJson(route('api.cabinet.adverts.index'))
        ->assertUnauthorized();
});

test('my adverts only returns the authenticated users adverts', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();

    Advert::factory()->for($user)->create(['title' => 'Mening e\'lonim']);
    Advert::factory()->for($stranger)->create(['title' => 'Boshqaning e\'loni']);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.cabinet.adverts.index'));

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Mening e\'lonim');
});

test('an authenticated user can create an advert', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.adverts.store'), [
        'category_id' => $category->id,
        'title' => 'Yangi e\'lon',
        'price' => 150000,
        'address' => 'Toshkent',
        'content' => 'Tavsif',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.title', 'Yangi e\'lon');

    expect(Advert::query()->where('user_id', $user->id)->where('title', 'Yangi e\'lon')->exists())->toBeTrue();
});

test('creating an advert without a title fails validation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.adverts.store'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['category_id', 'title', 'price', 'address', 'content']);
});

test('an owner can update their own advert', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['title' => 'Eski nom']);
    $category = Category::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->patchJson(route('api.adverts.update', $advert), [
        'category_id' => $category->id,
        'title' => 'Yangilangan nom',
        'price' => 200000,
        'address' => 'Samarqand',
        'content' => 'Yangi tavsif',
    ]);

    $response->assertOk()->assertJsonPath('data.title', 'Yangilangan nom');
});

test('a stranger cannot update someone elses advert', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($stranger);

    $this->patchJson(route('api.adverts.update', $advert), [
        'category_id' => $category->id,
        'title' => 'Hack',
        'price' => 1,
        'address' => 'x',
        'content' => 'x',
    ])->assertForbidden();
});

test('an owner can delete their own advert', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $this->deleteJson(route('api.adverts.destroy', $advert))
        ->assertStatus(204);

    expect(Advert::query()->find($advert->id))->toBeNull();
});
