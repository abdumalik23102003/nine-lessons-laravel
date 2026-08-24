<?php

use App\Models\Advert;
use App\Models\User;
use App\Services\Search\AdvertIndexer;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('guests are redirected to login when toggling a favorite', function () {
    $advert = Advert::factory()->create();

    $this->post(route('cabinet.favorites.toggle', $advert))
        ->assertRedirect(route('login'));
});

test('a user can add an advert to favorites', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->create();

    $this->actingAs($user)->post(route('cabinet.favorites.toggle', $advert));

    expect($user->hasFavorited($advert))->toBeTrue();
});

test('toggling twice removes the advert from favorites', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->create();

    $this->actingAs($user)->post(route('cabinet.favorites.toggle', $advert));
    $this->actingAs($user)->post(route('cabinet.favorites.toggle', $advert));

    expect($user->hasFavorited($advert))->toBeFalse();
});

test('a user only sees their own favorites', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $myFavorite = Advert::factory()->create(['title' => 'Mening sevimlim']);
    $strangersFavorite = Advert::factory()->create(['title' => 'Boshqaning sevimlisi']);

    $user->favoriteAdverts()->attach($myFavorite);
    $stranger->favoriteAdverts()->attach($strangersFavorite);

    $this->actingAs($user)
        ->get(route('cabinet.favorites.index'))
        ->assertSee('Mening sevimlim')
        ->assertDontSee('Boshqaning sevimlisi');
});

test('deleting an advert removes it from favorites', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->create();
    $user->favoriteAdverts()->attach($advert);

    $advert->delete();

    expect($user->favoriteAdverts()->count())->toBe(0);
});
