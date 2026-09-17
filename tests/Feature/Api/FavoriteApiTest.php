<?php

use App\Models\Advert;
use App\Models\User;
use App\Services\Search\AdvertIndexer;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('guests cannot toggle a favorite', function () {
    $advert = Advert::factory()->create();

    $this->postJson(route('api.favorites.toggle', $advert))
        ->assertUnauthorized();
});

test('a user can add and then remove an advert from favorites', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.favorites.toggle', $advert))
        ->assertOk()
        ->assertJsonPath('data.favorited', true);

    expect($user->hasFavorited($advert))->toBeTrue();

    $this->postJson(route('api.favorites.toggle', $advert))
        ->assertOk()
        ->assertJsonPath('data.favorited', false);

    expect($user->fresh()->hasFavorited($advert))->toBeFalse();
});

test('a user only sees their own favorites', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $mine = Advert::factory()->create(['title' => 'Mening sevimlim']);
    $strangers = Advert::factory()->create(['title' => 'Boshqaning sevimlisi']);

    $user->favoriteAdverts()->attach($mine);
    $stranger->favoriteAdverts()->attach($strangers);

    Sanctum::actingAs($user);

    $this->getJson(route('api.favorites.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Mening sevimlim');
});
