<?php

use App\Console\Commands\AdvertSearchReindexCommand;
use App\Models\Advert;
use App\Models\Category;
use App\Models\User;

test('search reindex command exists', function () {
    $this->artisan('search:reindex')
        ->assertSuccessful();
});

test('search reindex command with clear option', function () {
    $this->artisan('search:reindex --clear')
        ->assertSuccessful();
});

test('search filters by category', function () {
    $cat1 = Category::factory()->create(['name' => 'Electronics']);
    $cat2 = Category::factory()->create(['name' => 'Furniture']);
    $user = User::factory()->create();
    
    $adv1 = Advert::factory()->for($user)->for($cat1)->create(['status' => Advert::STATUS_ACTIVE]);
    $adv2 = Advert::factory()->for($user)->for($cat2)->create(['status' => Advert::STATUS_ACTIVE]);

    $response = $this->getJson(route('api.adverts.index', ['category_id' => $cat1->id]));

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

test('search filters by price', function () {
    $cat = Category::factory()->create();
    $user = User::factory()->create();
    
    Advert::factory()->for($user)->for($cat)->create(['price' => 100, 'status' => Advert::STATUS_ACTIVE]);
    Advert::factory()->for($user)->for($cat)->create(['price' => 10000, 'status' => Advert::STATUS_ACTIVE]);

    $response = $this->getJson(route('api.adverts.index', ['price_from' => 50, 'price_to' => 500]));

    $response->assertOk();
});

test('search by text', function () {
    $cat = Category::factory()->create();
    $user = User::factory()->create();
    
    Advert::factory()->for($user)->for($cat)->create(['title' => 'iPhone 15', 'status' => Advert::STATUS_ACTIVE]);
    Advert::factory()->for($user)->for($cat)->create(['title' => 'Samsung Galaxy', 'status' => Advert::STATUS_ACTIVE]);

    $response = $this->getJson(route('api.adverts.index', ['text' => 'iPhone']));

    $response->assertOk();
});

test('search sorts by price', function () {
    $cat = Category::factory()->create();
    $user = User::factory()->create();
    
    Advert::factory()->for($user)->for($cat)->create(['price' => 1000, 'status' => Advert::STATUS_ACTIVE]);
    Advert::factory()->for($user)->for($cat)->create(['price' => 100, 'status' => Advert::STATUS_ACTIVE]);

    $response = $this->getJson(route('api.adverts.index', ['sort' => 'price_asc']));

    $response->assertOk();
});

test('search only returns active adverts', function () {
    $cat = Category::factory()->create();
    $user = User::factory()->create();
    
    $active = Advert::factory()->for($user)->for($cat)->create(['status' => Advert::STATUS_ACTIVE]);
    Advert::factory()->for($user)->for($cat)->create(['status' => Advert::STATUS_DRAFT]);

    $response = $this->getJson(route('api.adverts.index'));

    $response->assertOk()
        ->assertJsonStructure(['data']);
});
